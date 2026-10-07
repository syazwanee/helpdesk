<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($ticket_id <= 0) {
    echo "Invalid ticket id.";
    exit();
}

// Get ticket info
$stmt = $conn->prepare("
    SELECT 
        t.*, 
        u.name AS department
    FROM tickets t
    LEFT JOIN users u ON t.user_id = u.user_id
    WHERE t.ticket_id = ?
");

$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$res = $stmt->get_result();
$ticket = $res->fetch_assoc();
$stmt->close();

if (!$ticket) {
    echo "Ticket not found.";
    exit();
}

$problem_description = "";
$attachment = "";
$application_name = "";
$hardware_type = "";

if (isset($ticket['request_type']) && strtolower($ticket['request_type']) === 'application') {
    $q = $conn->prepare("SELECT application_name, problem_description, attachment FROM application_issues WHERE ticket_id = ? LIMIT 1");
} else {
    $q = $conn->prepare("SELECT hardware_type, problem_description, attachment FROM hardware_issues WHERE ticket_id = ? LIMIT 1");
}
$q->bind_param("i", $ticket_id);
$q->execute();
$r = $q->get_result();
$row = $r->fetch_assoc();
if ($row) {
    $application_name = $row['application_name'] ?? '';
    $hardware_type = $row['hardware_type'] ?? '';
    $problem_description = $row['problem_description'] ?? '';
    $attachment = $row['attachment'] ?? '';
}
$q->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? $ticket['status'];
    $new_severity = intval($_POST['severity'] ?? $ticket['severity']);
    $closed_by = trim($_POST['closed_by'] ?? $ticket['closed_by']);
    $new_admin_note = trim($_POST['new_admin_note'] ?? '');
    $created_by = $_SESSION['name'] ?? $ticket['name'];


    $u = $conn->prepare("UPDATE tickets SET status = ?, severity = ?, closed_by = ?, updated_at = NOW() WHERE ticket_id = ?");
    $u->bind_param("sisi", $new_status, $new_severity, $closed_by, $ticket_id);
    $u->execute();
    $u->close();

    if (!empty($new_admin_note)) {
        $n = $conn->prepare("INSERT INTO ticket_admin_notes (ticket_id, note, created_by) VALUES (?, ?, ?)");
        $n->bind_param("iss", $ticket_id, $new_admin_note, $created_by);
        $n->execute();
        $n->close();
    }

    echo "<script>
            if (window.opener) { window.opener.location.reload(); }
            alert('Ticket updated successfully.');
            window.close();
          </script>";
    exit();
}

$notes = [];
$note_sql = $conn->prepare("SELECT * FROM ticket_admin_notes WHERE ticket_id = ? ORDER BY created_at ASC");
$note_sql->bind_param("i", $ticket_id);
$note_sql->execute();
$note_res = $note_sql->get_result();
while ($n = $note_res->fetch_assoc()) {
    $notes[] = $n;
}
$note_sql->close();
?>
<!doctype html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Dancing+Script&display=swap" rel="stylesheet">
  <meta charset="utf-8" />
  <title>Ticket #<?= htmlspecialchars($ticket['ticket_id']) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    body { font-family: Arial, Helvetica, sans-serif; margin: 20px; color: #222; }
    h2 { color: #4b0082; margin-bottom: 10px; }
    label { display:block; margin-top:10px; font-weight:600; }
    textarea, select, input[type=text] {
      width:100%; padding:8px; margin-top:6px;
      border-radius:6px; border:1px solid #ccc; box-sizing:border-box;
    }
    .btn { margin-top:10px; padding:10px 14px; background:#6a0dad; color:#fff; border:none; border-radius:6px; cursor:pointer; }
    .btn:hover { background:#4b0082; }
    .row { display:flex; gap:12px; }
    .col { flex:1; }
    .meta { background:#f9f9f9; padding:12px; border-radius:6px; border:1px solid #ddd; margin-bottom:12px; }
    .note-entry { background:#f4f0ff; padding:8px 10px; border-radius:6px; margin-top:8px; border-left:4px solid #6a0dad; }
    .note-meta { font-size:12px; color:#666; margin-bottom:5px; }

    .print-letter { display:none; }

    @media print {
      form, .btn, h2, .meta { display:none !important; }
      .print-letter { display:block !important; }

      body { font-family:"Times New Roman", serif; color:#000; }
      .letter-header { text-align:center; border-bottom:2px solid #000; padding-bottom:8px; margin-bottom:20px; }
      .letter-header h1 { font-size:22px; margin:0; text-transform:uppercase; }
      .letter-header p { margin:3px 0; font-size:13px; }
      .info-grid { display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:15px; }
      .info-box { border:1px solid #000; border-radius:5px; padding:8px 10px; font-size:14px; }
      .section-title { font-weight:bold; margin-top:15px; margin-bottom:5px; text-transform:uppercase; }
      .box { border:1px solid #000; border-radius:6px; padding:10px; min-height:60px; font-size:14px; white-space:pre-wrap; }
      .signature-section { display:flex; justify-content:space-between; margin-top:40px;}
      .signature-box { width:45%; }
      .signature-label { font-weight:bold; text-transform:uppercase; margin-bottom:5px; }
      .signature-box-inner { border:1px solid #000; border-radius:8px; padding:25px 10px; text-align:center; min-height:100px; font-size:15px;
        display:flex; flex-direction:column; justify-content:center; font-family: 'Great Vibes', cursive;
        font-size: 20px; font-weight: 400; }
      .signature-box-inner strong {
        font-family: 'Great Vibes', cursive;
        font-size: 26px;
        font-weight: 400;
        margin-bottom: 5px;
      }
      .box {
        line-height: 1.4;
        font-size: 13px;
        white-space: normal !important;
      }
    }
  </style>
</head>
<body>

<h2>Ticket #<?= htmlspecialchars($ticket['ticket_id']) ?></h2>

<div class="meta">
  <p><strong>Staff Name:</strong> <?= htmlspecialchars($ticket['staff_name']) ?></p>
  <p><strong>Department:</strong> <?= htmlspecialchars($ticket['department']) ?></p>
  <p><strong>Area:</strong> <?= htmlspecialchars($ticket['area']) ?></p>
  <p><strong>Request Type:</strong> <?= htmlspecialchars($ticket['request_type']) ?></p>
  <?php if ($application_name): ?><p><strong>Application Name:</strong> <?= htmlspecialchars($application_name) ?></p><?php endif; ?>
  <?php if ($hardware_type): ?><p><strong>Hardware Type:</strong> <?= htmlspecialchars($hardware_type) ?></p><?php endif; ?>
  <p><strong>Severity:</strong> <?= htmlspecialchars($ticket['severity']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']) ?></p>
  <p><strong>Problem Description:</strong> <?= htmlspecialchars($problem_description) ?></p>
  <?php if (!empty($attachment)): ?>
    <p><strong>Attachment:</strong> <a href="<?= htmlspecialchars($attachment) ?>" target="_blank"><?= htmlspecialchars($attachment) ?></a></p>
  <?php endif; ?>
</div>

<form method="post">
  <label for="new_admin_note">Add New Admin Note/ Resolution</label>
  <textarea name="new_admin_note" id="new_admin_note" rows="3"></textarea>

  <?php if (!empty($notes)): ?>
    <div style="margin-top:12px;">
      <strong>Previous Notes:</strong>
      <?php foreach ($notes as $n): ?>
        <div class="note-entry">
          <div class="note-meta"><?= htmlspecialchars($n['created_by']) ?> — <?= date("d/m/Y H:i", strtotime($n['created_at'])) ?></div>
          <div><?= nl2br(htmlspecialchars($n['note'])) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col">
      <label for="closed_by">Closed By</label>
      <select name="closed_by" id="closed_by">
        <option value="">-- Select --</option>
        <option value="Fahzan" <?= ($ticket['closed_by'] === 'Fahzan') ? 'selected' : '' ?>>Fahzan</option>
        <option value="Nizam" <?= ($ticket['closed_by'] === 'Nizam') ? 'selected' : '' ?>>Nizam</option>
        <option value="Darwis" <?= ($ticket['closed_by'] === 'Darwis') ? 'selected' : '' ?>>Darwis</option>
        <option value="Fajrina" <?= ($ticket['closed_by'] === 'Fajrina') ? 'selected' : '' ?>>Fajrina</option>
        <option value="Elmy" <?= ($ticket['closed_by'] === 'Elmy') ? 'selected' : '' ?>>Elmy</option>
      </select>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <label for="status">Status</label>
      <select name="status" id="status">
        <option value="Pending" <?= ($ticket['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
        <option value="In Progress" <?= ($ticket['status'] === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
        <option value="Resolve" <?= ($ticket['status'] === 'Resolve') ? 'selected' : '' ?>>Resolve</option>
        <option value="Closed" <?= ($ticket['status'] === 'Closed') ? 'selected' : '' ?>>Closed</option>
      </select>
    </div>

    <div class="col">
      <label for="severity">Severity</label>
      <select name="severity" id="severity">
        <option value="1" <?= ($ticket['severity'] == 1) ? 'selected' : '' ?>>1 - Urgent</option>
        <option value="2" <?= ($ticket['severity'] == 2) ? 'selected' : '' ?>>2 - Moderate</option>
        <option value="3" <?= ($ticket['severity'] == 3) ? 'selected' : '' ?>>3 - Normal</option>
      </select>
    </div>
  </div>

  <p>
    <button type="button" class="btn" onclick="setTimeout(() => window.print(), 200)">Print</button>
    <button type="submit" class="btn">Save changes</button>
  </p>
</form>

<!-- PRINT SECTION -->
<div class="print-letter" aria-hidden="true">
  <div class="letter-header">
    <div class="print-logo">
      <img src="logo.png" alt="Logo" style="width:350px; height:auto;">
    </div>
    <h1>IT SERVICE REQUEST REPORT</h1>
    <p>Date Printed: <?= date("d/m/Y") ?></p>
  </div>

  <div class="info-grid">
    <div class="info-box"><strong>Ticket ID:</strong> <?= htmlspecialchars($ticket['ticket_id']) ?></div>
    <div class="info-box"><strong>Staff Name:</strong> <?= htmlspecialchars($ticket['staff_name']) ?></div>
    <div class="info-box"><strong>Department:</strong> <?= htmlspecialchars($ticket['department']) ?></div>
    <div class="info-box"><strong>Area:</strong> <?= htmlspecialchars($ticket['area']) ?></div>
    <div class="info-box"><strong>Request Type:</strong> <?= htmlspecialchars($ticket['request_type']) ?></div>
    <?php if ($application_name): ?><div class="info-box"><strong>Application:</strong> <?= htmlspecialchars($application_name) ?></div><?php endif; ?>
    <?php if ($hardware_type): ?><div class="info-box"><strong>Hardware:</strong> <?= htmlspecialchars($hardware_type) ?></div><?php endif; ?>
    <div class="info-box"><strong>Contact Number:</strong> <?= htmlspecialchars($ticket['contact']) ?></div>
    <div class="info-box"><strong>Time Report:</strong> <?= htmlspecialchars($ticket['created_at']) ?></div>
  </div>

  <div class="section-title">Problem Description (User)</div>
  <div class="box"><?= nl2br(htmlspecialchars($problem_description)) ?></div>

  <div class="section-title">Remarks / Root Cause / Resolution</div>
  <div class="box">
    <?php foreach ($notes as $n): ?>
      <?= date("d/m/Y H:i", strtotime($n['created_at'])) ?> - 
      <?= htmlspecialchars($n['created_by']) ?>: 
      <?= htmlspecialchars(preg_replace('/\s+/', ' ', $n['note'])) ?><br>
    <?php endforeach; ?>
  </div>

  <div class="signature-section">
    <div class="signature-box">
      <div class="signature-label">Reported by:</div>
      <div class="signature-box-inner">
        <strong><?= htmlspecialchars($ticket['staff_name']) ?></strong><br>
        <?= !empty($ticket['created_at']) ? date("d/m/Y", strtotime($ticket['created_at'])) : '' ?>
      </div>
    </div>

    <div class="signature-box">
      <div class="signature-label">Closed by:</div>
      <div class="signature-box-inner">
        <strong><?= htmlspecialchars($ticket['closed_by'] ?? '') ?></strong><br>
        <?= !empty($ticket['updated_at']) ? date("d/m/Y H:i", strtotime($ticket['updated_at'])) : '' ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
