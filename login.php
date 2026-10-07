<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "mighcomm_ithelpdesk", "mighcomm_ithelpdesk", "mighcomm_helpdesk_system");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Store session data
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid name or password.";
    }
    }
// Fetch staff departments for dropdown
$conn = new mysqli("localhost", "root", "", "helpdesk_system");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$staffQuery = "SELECT name FROM users WHERE role = 'user' ORDER BY name ASC";
$staffResult = $conn->query($staffQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body {
    max-width: 100%;
    overflow-x: hidden;
  }


  table {
    width: 100%;
    border-collapse: collapse;
  }

  .table-wrapper {
    overflow-x: auto;
  }

  img {
    max-width: 100%;
    height: auto;
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 100%;
      position: relative;
    }
    .content {
      margin-left: 0;
      padding-top: 10px;
    }
  }
</style>
</head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Helpdesk Login</title>
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
      max-width: 400px;
      width: 100%;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0px 6px 18px rgba(0,0,0,0.3);
      border-top: 8px solid #6a0dad;
      text-align: center;
    }
    .logo {
      width: 1200px;
      margin-bottom: 25px;
    }
    h2 {
      margin-bottom: 20px;
      color: #4b0082;
    }
    label {
      display: block;
      text-align: left;
      font-weight: bold;
      margin: 10px 0 5px;
      color: #4b0082;
    }
    input {
      width: 90%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    button {
      margin-top: 15px ;
      margin-bottom: 5px;
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
    .register-link {
      margin-top: 15px;
      display: block;
      color: #4b0082;
      text-decoration: none;
      font-weight: bold;
    }

    select {
      width: 93%;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }


    .popup {
      display: <?= $error ? 'flex' : 'none' ?>;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
    }
    .popup-content {
      background: white;
      padding: 20px 30px;
      border-radius: 10px;
      text-align: center;
      max-width: 300px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    }
    .popup-content h3 {
      margin: 0 0 15px;
      color: red;
    }
    .popup-content button {
      background: #6a0dad;
      padding: 10px 15px;
      border: none;
      border-radius: 8px;
      color: white;
      cursor: pointer;
    }

      .powered-by {
      margin-top: 20px;
      padding-top: 12px;
      border-top: 1px solid #eee;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: #777;
    }

    .powered-by img {
      height: 24px;
      object-fit: contain;
    }

 .admin-top-btn {
    position: fixed;
    top: 20px;
    right: 30px;
    background: #360557;  /* bright orange to stand out */
    color: white;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: 0.3s;
    z-index: 999;
}
  


  </style>
</head>
<body>
  <a href="admin_login.php" class="admin-top-btn">
    🔑 Admin Login
</a>
  <div class="overlay">
    <div class="container">
      <img src="logofixit.png" alt="Company Logo" class="logo">
 
       <form method="POST">
      <label>Department</label>
      <select name="name" required>
    <option value="">-- Select Department --</option>

    <?php while ($row = $staffResult->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['name']) ?>">
            <?= htmlspecialchars($row['name']) ?>
        </option>
    <?php endwhile; ?>

</select>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
      </form>

      
      <div class="powered-by">
        <span>Powered by</span>
        <img src="logo.png" alt="Powered By Logo">

    </div>
  </div>

  <div class="popup" id="popup">
    <div class="popup-content">
      <h3>Login Failed</h3>
      <p><?= $error ?></p>
      <button onclick="document.getElementById('popup').style.display='none'">OK</button>
    </div>
  </div>
</body>
</html>
