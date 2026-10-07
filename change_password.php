<?php
session_start();


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "mighcomm_ithelpdesk", "mighcomm_ithelpdesk", "mighcomm_helpdesk_system");

$message = "";
$success = false;

// Get all users
$users = [];
$result = $conn->query("SELECT user_id, name, password FROM users ORDER BY user_id ASC");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($selected_user_id) || empty($new_password) || empty($confirm_password)) {
        $message = "<p style='color:red;'>Please fill in all fields.</p>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<p style='color:red;'>New password and confirm password do not match.</p>";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_hashed_password, $selected_user_id);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Password updated successfully!</p>";
            $success = true;
        } else {
            $message = "<p style='color:red;'>Error updating password. Please try again.</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Change User Password</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: url('https://www.toptal.com/designers/subtlepatterns/patterns/moroccan-flower-dark.png');
      background-size: cover;
      background-attachment: fixed;
    }
    .overlay {
      background: rgba(75, 0, 130, 0.85);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .container {
      background: #fff;
      max-width: 500px;
      width: 100%;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0px 6px 18px rgba(0,0,0,0.3);
      border-top: 8px solid #6a0dad;
      text-align: center;
    }
    h2 {
      color: #4b0082;
      margin-bottom: 20px;
    }
    input, select {
      width: 95%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #6a0dad;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      color: white;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: #4b0082;
    }
    .back-btn {
      background: #bbb;
      margin-top: 10px;
    }
    .back-btn:hover {
      background: #999;
    }
  </style>
</head>
<body>

<div class="overlay">
  <div class="container">
    <h2>Change User Password</h2>
    <?php echo $message; ?>
    <form method="POST" action="">
      <select name="user_id" required>
        <option value="">-- Select User --</option>
        <?php foreach ($users as $user): ?>
          <option value="<?php echo $user['user_id']; ?>">
            <?php echo htmlspecialchars($user['name'] ); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input type="password" name="new_password" placeholder="Enter New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
      <button type="submit">Update Password</button>
    </form>

    <form action="admin_dashboard.php" method="get">
      <button type="submit" class="back-btn">Back to Dashboard</button>
    </form>
  </div>
</div>

<?php if ($success): ?>
<script>
  alert("Password updated successfully!");
  window.location.href = "admin_dashboard.php";
</script>
<?php endif; ?>

</body>
</html>
