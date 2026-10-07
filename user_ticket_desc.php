<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];


$sql = "SELECT * FROM tickets WHERE ticket_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$ticket = $res->fetch_assoc();
$stmt->close();

if (!$ticket) {
    echo "Ticket not found or access denied.";
    exit();
}


$problem_description = "";
$extra_info = "-";

if ($ticket['request_type'] === 'Hardware') {
    $q = $conn->prepare("SELECT problem_description, hardware_type FROM hardware_issues WHERE ticket_id = ?");
} else {
    $q = $conn->prepare("SELECT problem_description, application_name FROM application_issues WHERE ticket_id = ?");
}
$q->bind_param("i", $ticket_id);
$q->execute();
$r = $q->get_result();
$row = $r->fetch_assoc();

$problem_description = $row['problem_description'] ?? '';
if ($ticket['request_type'] === 'Hardware' && isset($row['hardware_type'])) {
    $extra_info = $row['hardware_type'];
} elseif ($ticket['request_type'] === 'Application' && isset($row['application_name'])) {
    $extra_info = $row['application_name'];
}
$q->close();

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_description = trim($_POST['problem_description']);
    if ($ticket['request_type'] === 'Application') {
        $u = $conn->prepare("UPDATE application_issues SET problem_description = ? WHERE ticket_id = ?");
    } else {
        $u = $conn->prepare("UPDATE hardware_issues SET problem_description = ? WHERE ticket_id = ?");
    }
    $u->bind_param("si", $new_description, $ticket_id);
    $u->execute();
    $u->close();

    echo "<script>alert('Description updated successfully.'); window.location.reload();</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ticket #<?= htmlspecialchars($ticket['ticket_id']) ?></title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f8f4ff;
    margin: 0;
    padding: 0;
    color: #333;
  }

  .container {
    max-width: 800px;
    background: #fff;
    margin: 50px auto;
    padding: 35px 45px;
    border-radius: 18px;
    box-shadow: 0 5px 25px rgba(106, 13, 173, 0.15);
  }

  h2 {
    text-align: center;
    color: #4b0082;
    font-weight: 700;
    margin-bottom: 25px;
  }

  .ticket-info {
    background: #f3ebff;
    border: 2px solid #c8a2ff;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 30px;
  }

  .ticket-info p {
    margin: 6px 0;
    font-size: 15px;
  }

  .status {
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 14px;
  }

  .status-closed {
    background: #e8ffe8;
    color: #0b9e2aff;
  }

  .status-pending {
    background: #ffe5e5 ;
    color: #ff1414ff;
  }

  .prev-desc {
    background: #f9f6ff;
    border: 1px solid #cdb8ff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    white-space: pre-wrap;
  }

  label {
    font-weight: 600;
    color: #4b0082;
    display: block;
    margin-bottom: 8px;
  }

  textarea {
    width: 100%;
    height: 130px;
    border: 2px solid #c7a6f2;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 15px;
    resize: none;
    transition: 0.3s;
  }

  textarea:focus {
    border-color: #6a0dad;
    box-shadow: 0 0 10px rgba(106, 13, 173, 0.3);
    outline: none;
  }

  .btn {
    display: inline-block;
    background: #6a0dad;
    color: white;
    font-weight: 600;
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
    transition: all 0.3s;
  }

  .btn:hover {
    background: #4b0082;
    transform: translateY(-1px);
  }

  .back-btn {
    display: inline-block;
    background: #fff;
    color: #6a0dad;
    border: 2px solid #6a0dad;
    font-weight: 600;
    border-radius: 10px;
    padding: 8px 16px;
    text-decoration: none;
    transition: all 0.3s;
    margin-bottom: 25px;
  }

  .back-btn:hover {
    background: #6a0dad;
    color: white;
  }

  hr {
    border: none;
    height: 2px;
    background: #e4d2ff;
    margin: 25px 0;
  }
</style>
</head>
<body>
  <div class="container">
    <a href="#" class="back-btn" onclick="window.close()">← Back</a>
    <h2>Ticket #<?= htmlspecialchars($ticket['ticket_id']) ?></h2>

    <div class="ticket-info">
      <p><strong>Status:</strong> 
        <span class="status <?= strtolower($ticket['status']) === 'closed' ? 'status-closed' : 'status-pending' ?>">
          <?= htmlspecialchars($ticket['status']) ?>
        </span>
      </p>
      <p><strong>Reported by:</strong> <?= htmlspecialchars($ticket['staff_name']) ?></p>
      <p><strong>Area:</strong> <?= htmlspecialchars($ticket['area']) ?></p>
      <p><strong>Request Type:</strong> <?= htmlspecialchars($ticket['request_type']) ?></p>

      <?php if ($ticket['request_type'] === 'Hardware'): ?>
        <p><strong>Hardware Type:</strong> <?= htmlspecialchars($extra_info) ?></p>
      <?php else: ?>
        <p><strong>Application Name:</strong> <?= htmlspecialchars($extra_info) ?></p>
      <?php endif; ?>

      <p><strong>Severity:</strong> <?= htmlspecialchars($ticket['severity']) ?></p>
      <p><strong>Created:</strong> <?= htmlspecialchars($ticket['created_at']) ?></p>
    </div>

    <label>Description:</label>
    <div class="prev-desc">
      <?= $problem_description ? nl2br(htmlspecialchars($problem_description)) : '<em>No previous description provided.</em>' ?>
    </div>

    <form method="POST">
      <label for="problem_description">Update Your Description</label>
      <textarea name="problem_description" id="problem_description" required><?= htmlspecialchars($problem_description) ?></textarea>
      <button type="submit" class="btn">Update Description</button>
    </form>
  </div>
</body>
</html>
