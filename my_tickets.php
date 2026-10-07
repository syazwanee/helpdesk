<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';

$filter = "WHERE t.user_id = ?";
$params = [$user_id];
$types  = "i";

if (!empty($search)) {
    $filter .= " AND (t.ticket_id LIKE ? OR t.area LIKE ? OR t.request_type LIKE ?)";
    $search_like = "%$search%";
    $params = array_merge($params, [$search_like, $search_like, $search_like]);
    $types .= "sss";
}

if (!empty($start_date) && !empty($end_date)) {
    $filter .= " AND DATE(t.created_at) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= "ss";
}

if (!empty($status)) {
    $filter .= " AND t.status = ?";
    $params[] = $status;
    $types .= "s";
}


$sql = "
    SELECT t.ticket_id, t.area, t.request_type, t.severity, t.status, t.created_at,
           tan.created_by AS admin_name, tan.note AS admin_note
    FROM tickets t
    LEFT JOIN (
        SELECT n1.ticket_id, n1.note, n1.created_by
        FROM ticket_admin_notes n1
        INNER JOIN (
            SELECT ticket_id, MAX(created_at) AS max_created
            FROM ticket_admin_notes
            GROUP BY ticket_id
        ) n2 ON n1.ticket_id = n2.ticket_id AND n1.created_at = n2.max_created
    ) tan ON t.ticket_id = tan.ticket_id
    $filter
    ORDER BY t.created_at DESC
    LIMIT 10
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Split tickets by status for tabs
$active_tickets = [];
$closed_tickets = [];

while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['status']);
    if ($status === 'closed') {
        $closed_tickets[] = $row;
    } else {
        $active_tickets[] = $row;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Tickets</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: 'Segoe UI',
      sans-serif;
      background: #f8f8ff; 
      margin: 0; 
      padding: 0;
     }

  header {
    background: #6a0dad;
    color: white; 
    padding: 15px 20px;
    display: flex;
    align-items: center; 
    justify-content: space-between; 
    flex-wrap: wrap;
  }

  header h2 { 
  margin: 0; 
  font-size: 1.2rem;
   }

  .back-btn {
    background: white; 
    color: #6a0dad; 
    border: none; 
    padding: 6px 12px;
    border-radius: 6px; 
    cursor: pointer; 
    font-weight: bold; 
    margin-bottom: 5px;
  }
  .container {
   padding: 20px;
    }

  .tab { 
    display: inline-block; 
    padding: 10px 20px; 
    cursor: pointer; 
    background: #ddd;
    border-radius: 8px 8px 0 0; 
    margin-right: 5px; 
    font-weight: bold; 
    font-size: 0.9rem;
  }

  .tab.active { 
    background: #6a0dad; 
    color: white; 
  }

  .tab-content { display: none; 
    background: white; 
    padding: 15px; 
    border-radius: 0 8px 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .tab-content.active {
    display: block; 
   }

  .filters { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 10px; 
    margin-bottom: 20px; 
  }

  .filters input, .filters select, .filters button {
    padding: 8px; 
    font-size: 0.9rem; 
    border-radius: 6px; 
    border: 1px solid #ccc;
  }

  .filters button {
    background: #6a0dad; 
    color: white; 
    border: none; 
    cursor: pointer;
  }

  table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-top: 10px; 
    font-size: 0.85rem; 
  }

  th, td { 
    border: 1px solid #ddd; 
    padding: 8px; 
    text-align: center; 
    word-wrap: break-word;
   }

  th { 
    background-color: #6a0dad; 
    color: white; 
  }

  tr:hover { 
    background: #f2e6ff; 
  }

  .status-closed { 
    color: green; 
    font-weight: bold; 
  }

  .status-active { 
    color: red; 
    font-weight: bold; 
  }

  .section-title { 
    background: #eee; 
    padding: 8px; font-weight: bold; 
    border-radius: 6px; 
    margin-top: 20px; 
  }

  .btn-link {
    background: #6a0dad; 
    color: white; 
    padding: 5px 10px; 
    border-radius: 6px; 
    text-decoration: none;
    }
    
  @media (max-width: 768px) {
    table, thead, tbody, th, td, tr { display: block; }
    thead tr { display: none; }
    tr { margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; padding: 10px; }
    td { text-align: left; padding-left: 50%; position: relative; }
    td:before {
      position: absolute; left: 10px; width: 45%; white-space: nowrap; font-weight: bold;
    }
    td:nth-of-type(1):before { content: "Ticket ID"; }
    td:nth-of-type(2):before { content: "Area"; }
    td:nth-of-type(3):before { content: "Request Type"; }
    td:nth-of-type(4):before { content: "Severity"; }
    td:nth-of-type(5):before { content: "Status"; }
    td:nth-of-type(6):before { content: "Person In Charge"; }
    td:nth-of-type(7):before { content: "Created At"; }
  }
</style>
</head>
<body>

<header>
  <button class="back-btn" onclick="window.location.href='dashboard.php'">Back</button>
  <h2>My Submitted Tickets</h2>
</header>

<div class="container">
  <form method="GET" class="filters">
    <input type="text" name="search" placeholder="Search Ticket / Area / Type" value="<?= htmlspecialchars($search) ?>">
    <select name="status">
      <option value="">All Status</option>
      <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
      <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
      <option value="Resolve" <?= $status === 'Resolve' ? 'selected' : '' ?>>Resolve</option>
      <option value="Closed" <?= $status === 'Closed' ? 'selected' : '' ?>>Closed</option>
    </select>
    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
    <button type="submit">Apply Filters</button>
  </form>

  <div class="tabs">
    <div class="tab active" data-tab="active" onclick="showTab('active')">Active Tickets</div>
    <div class="tab" data-tab="closed" onclick="showTab('closed')">Closed Tickets</div>
  </div>

  <div id="tab-active" class="tab-content active">
    <?php displayTicketSection($active_tickets); ?>
  </div>

  <div id="tab-closed" class="tab-content">
    <?php displayTicketSection($closed_tickets); ?>
  </div>
</div>

<script>
function showTab(tab) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
  document.querySelector(`.tab[data-tab="${tab}"]`).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}
function openTicketPopup(id) {
  window.open("user_ticket_desc.php?id=" + id, "_blank", "width=900,height=700,scrollbars=yes");
}
</script>

</body>
</html>

<?php
function displayTicketSection($tickets) {
    if (empty($tickets)) {
        echo "<p>No tickets found.</p>";
        return;
    }

    echo "<table>
            <thead>
              <tr>
                <th>Ticket ID</th>
                <th>Area</th>
                <th>Request Type</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Person In Charge</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>";

    foreach ($tickets as $r) {
        $statusClass = strtolower($r['status']) === 'closed' ? 'status-closed' : 'status-active';
        $adminDisplay = !empty($r['admin_name']) ? htmlspecialchars($r['admin_name']) : '-';

        echo "<tr>
                <td><a href='javascript:void(0)' onclick='openTicketPopup({$r['ticket_id']})' class='btn-link'>{$r['ticket_id']}</a></td>
                <td>" . htmlspecialchars($r['area']) . "</td>
                <td>" . htmlspecialchars($r['request_type']) . "</td>
                <td>" . htmlspecialchars($r['severity']) . "</td>
                <td class='$statusClass'>" . htmlspecialchars($r['status']) . "</td>
                <td>$adminDisplay</td>
                <td>" . date('d M Y', strtotime($r['created_at'])) . "</td>
              </tr>";
    }

    echo "</tbody></table>";
}
?>
