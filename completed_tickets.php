<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'sidebar.php';

$conn = new mysqli("localhost", "mighcomm_ithelpdesk", "mighcomm_ithelpdesk", "mighcomm_helpdesk_system");

// Handle filters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Base SQL
$sql = "
SELECT 
    t.ticket_id,
    t.staff_name,
    u.name AS department, 
    t.request_type,
    t.severity,
    t.status,
    t.created_at,
    CONCAT(
        COALESCE(admin.name, ''), 
        CASE 
            WHEN COALESCE(admin.name,'') <> '' AND COALESCE(tan.note,'') <> '' THEN ' - ' 
            ELSE '' 
        END,
        COALESCE(tan.note, '')
    ) AS admin_in_charge
FROM tickets t
-- Latest admin note per ticket
LEFT JOIN (
    SELECT n1.ticket_id, n1.note, n1.created_by
    FROM ticket_admin_notes n1
    INNER JOIN (
        SELECT ticket_id, MAX(created_at) AS max_created
        FROM ticket_admin_notes
        GROUP BY ticket_id
    ) n2 ON n1.ticket_id = n2.ticket_id AND n1.created_at = n2.max_created
) tan ON t.ticket_id = tan.ticket_id
-- Admin who wrote the note
LEFT JOIN users admin ON tan.created_by = admin.user_id
-- Department (user who created the ticket)
LEFT JOIN users u ON t.user_id = u.user_id
WHERE t.status = 'Closed'
";

if (!empty($searchTerm)) {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $sql .= " AND (t.ticket_id LIKE '%$safeSearch%' 
              OR t.staff_name LIKE '%$safeSearch%'
              OR t.department LIKE '%$safeSearch%')";
}

if (!empty($status)) {
    $safeStatus = $conn->real_escape_string($status);
    $sql .= " AND t.status = '$safeStatus'";
}

if (!empty($startDate) && !empty($endDate)) {
    $sql .= " AND DATE(t.created_at) BETWEEN '" . $conn->real_escape_string($startDate) . "' 
              AND '" . $conn->real_escape_string($endDate) . "'";
} elseif (!empty($startDate)) {
    $sql .= " AND DATE(t.created_at) >= '" . $conn->real_escape_string($startDate) . "'";
} elseif (!empty($endDate)) {
    $sql .= " AND DATE(t.created_at) <= '" . $conn->real_escape_string($endDate) . "'";
}

$sql .= " ORDER BY t.created_at DESC";
$result = $conn->query($sql);


$tickets = [
    'recent' => [],
    'week'   => [],
    'month'  => [],
    'year'   => []
];

$currentTime = time();
$currentYear = date("Y", $currentTime);
$currentMonth = date("m", $currentTime);
$weekStart = strtotime("monday this week", $currentTime);
$weekEnd   = strtotime("sunday this week", $currentTime);

while ($row = $result->fetch_assoc()) {
    $date = strtotime($row['created_at']);
    $tickets['recent'][] = $row;

    if ($date >= $weekStart && $date <= $weekEnd) {
        $range = date("d M Y", $weekStart) . " - " . date("d M Y", $weekEnd);
        $tickets['week'][$range][] = $row;
    }

    if (date("Y", $date) == $currentYear && date("m", $date) == $currentMonth) {
        $monthKey = date("F Y", $date);
        $tickets['month'][$monthKey][] = $row;
    }

    if (date("Y", $date) == $currentYear) {
        $tickets['year'][$currentYear][] = $row;
    }
}
?>

<div class="content">
  <a href="admin_dashboard.php" class="back-btn">Back</a>
  <h2>Closed Tickets</h2>


<form method="GET" class="search-bar">
  <div style="display:flex; flex-direction:column; gap:10px;">
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <input type="text" name="search" placeholder="Search by Ticket ID, Name or Department" value="<?= htmlspecialchars($searchTerm) ?>" style="padding:8px; border-radius:6px; border:1px solid #ccc; flex:1;">
      <select name="status" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
        <option value="">All Status</option>
        <option value="Pending" <?= $status=='Pending'?'selected':''; ?>>Pending</option>
        <option value="In Progress" <?= $status=='In Progress'?'selected':''; ?>>In Progress</option>
        <option value="Resolve" <?= $status=='Resolve'?'selected':''; ?>>Resolve</option>
      </select>
    </div>
    <div class="date-inputs">
      <label><b>Date Range:</b></label>
      <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
      <span class="to-text">to</span>
      <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
      <button type="submit" class="search-btn">Filter</button>
      <a href="Acurrent_tickets.php" class="reset-btn">Reset</a>
    </div>
  </div>
</form>


<div class="tabs">
  <button class="tab-btn active" onclick="openTab(event, 'recent')">Recent</button>
  <button class="tab-btn" onclick="openTab(event, 'week')">Week</button>
  <button class="tab-btn" onclick="openTab(event, 'month')">Month</button>
  <button class="tab-btn" onclick="openTab(event, 'year')">Year</button>
</div>

<?php
function renderTicketTable($rows) {
    if (empty($rows)) {
        echo "<p>No tickets found.</p>";
        return;
    }
    echo "<table border='1' cellpadding='8' cellspacing='0' width='100%'>
            <tr>
              <th>ID</th><th>Name</th><th>Department</th>
              <th>Request Type</th><th>Severity</th><th>Status</th>
              <th>Created</th>
            </tr>";
    foreach ($rows as $row) {
        echo "<tr>
                <td><a href='ticket_desc.php?id={$row['ticket_id']}' 
                       onclick=\"window.open(this.href,'popup','width=800,height=600,scrollbars=yes'); return false;\">
                       {$row['ticket_id']}</a></td>
                <td>".htmlspecialchars($row['staff_name'])."</td>
                <td>".htmlspecialchars($row['department'])."</td>
                <td>".htmlspecialchars($row['request_type'])."</td>
                <td>".htmlspecialchars($row['severity'])."</td>
                <td>".htmlspecialchars($row['status'])."</td>
                <td>{$row['created_at']}</td>
              </tr>";
    }
    echo "</table>";
}
?>

<div id="recent" class="tab-content active">
  <h3 style="color:#4b0082; margin-top:20px;">Recent Tickets</h3>
  <?php renderTicketTable($tickets['recent']); ?>
</div>


<div id="week" class="tab-content">
  <?php foreach ($tickets['week'] as $range => $rows): ?>
    <h3 style="color:#4b0082; margin-top:20px;">Week: <?= $range ?></h3>
    <?php renderTicketTable($rows); ?>
  <?php endforeach; ?>
</div>

<div id="month" class="tab-content">
  <?php foreach ($tickets['month'] as $month => $rows): ?>
    <h3 style="color:#4b0082; margin-top:20px;">Month: <?= $month ?></h3>
    <?php renderTicketTable($rows); ?>
  <?php endforeach; ?>
</div>

<div id="year" class="tab-content">
  <?php foreach ($tickets['year'] as $year => $rows): ?>
    <h3 style="color:#4b0082; margin-top:20px;">Year: <?= $year ?></h3>
    <?php renderTicketTable($rows); ?>
  <?php endforeach; ?>
</div>
</div>

<style>

.back-btn {
  display:inline-block;background:#6a0dad;color:white;
  text-decoration:none;padding:8px 15px;border-radius:8px;
  font-weight:600;margin-bottom:10px;transition:background 0.3s;
}
.back-btn:hover { background:#4b0082; }
.tabs { margin-bottom:20px; }
.tab-btn { background:#6a0dad;color:#fff;border:none;
  padding:10px 15px;margin-right:5px;cursor:pointer;border-radius:5px; }
.tab-btn.active { background:#4b0082; }
.tab-content { display:none; }
.tab-content.active { display:block; }
.search-bar {
  background:#f8f4ff;border:2px solid #6a0dad;border-radius:10px;
  padding:15px;margin-bottom:25px;
}
.search-btn{ 
  border:2px solid #6a0dad;border-radius:8px;
  padding:6px 14px;font-weight:600;text-decoration:none;
}
.reset-btn {
  border:2px solid #6a0dad;border-radius:8px;
  padding:4px 14px;font-weight:600;text-decoration:none;
}

.search-btn { background:#fff;color:#6a0dad; }
.search-btn:hover { background:#6a0dad;color:#fff; }
.reset-btn { background:#fff;color:#6a0dad; }
.reset-btn:hover { background:#6a0dad;color:#fff; }


@media (max-width: 768px) {

  
  .content {
    width: 100%;
    overflow-x: hidden;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;  
  }

  th, td {
    word-wrap: break-word;
    word-break: break-word;
    font-size: 13px;
    padding: 6px;
  }

  th:nth-child(4),
  td:nth-child(4),
  th:nth-child(5),
  td:nth-child(5) {
    display: none; 
  }

  .search-bar form,
  .search-bar div {
    width: 100%;
  }

  .search-bar input,
  .search-bar select {
    width: 100%;
  }

}

.search-btn,
.reset-btn {
  width: auto;          
  padding: 6px 10px;   
  font-size: 13px;
}


.date-inputs {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  align-items: center;
}


</style>

<script>
function openTab(evt, tabId) {
  var tabcontent = document.getElementsByClassName("tab-content");
  for (var i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
    tabcontent[i].classList.remove("active");
  }
  var tabbtns = document.getElementsByClassName("tab-btn");
  for (var i = 0; i < tabbtns.length; i++) {
    tabbtns[i].classList.remove("active");
  }
  document.getElementById(tabId).style.display = "block";
  document.getElementById(tabId).classList.add("active");
  evt.currentTarget.classList.add("active");
}
document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("recent").style.display = "block";
});
</script>
