<?php
session_start();
require 'db_connect.php';

$message = "";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['name']);
    $password   = $_POST['password'];
    $retype     = $_POST['retype_password'];
    $role       = $_POST['role']; // admin or user

    // Validate passwords
    if ($password !== $retype) {
        $message = "Passwords do not match!";
    } else {
        // Proceed if no error
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, password, role, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $name, $hashedPassword, $role);

            if ($stmt->execute()) {
                header("Location: success_register.php");
                exit();
            } else {
                $message = "Error inserting record: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register New Account</title>
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
      max-width: 600px;
      width: 100%;
      padding: 25px 30px;
      border-radius: 25px;
      box-shadow: 0px 6px 18px rgba(0,0,0,0.3);
      border-top: 8px solid #6a0dad;
      text-align: center;
    }
    .logo {
      width: 400px;
      margin-bottom: 15px;
    }
    h2 {
      color: #4b0082;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin: 10px 0 5px;
      text-align: left;
      font-weight: bold;
      color: #4b0082;
    }
    input, select {
      width: 93%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    .error {
      color: red;
      margin-bottom: 15px;
    }
    
   
    button,
    .back-btn {
      display: block;
      width: 100%;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      font-family: 'Segoe UI', sans-serif;
      border-radius: 8px;
      text-align: center;
      cursor: pointer;
      box-sizing: border-box;
      transition: background 0.3s ease;
    }

    /* Create Account button */
    button {
      background: #6a0dad;
      border: none;
      color: white;
    }

    button:hover {
      background: #4b0082;
    }

   
    .back-btn {
      background: #b5b5b5;
      color: #333;
      text-decoration: none;
      margin-top: 10px;
    }

    .back-btn:hover {
      background: #9e9e9e;
    }


    @media (max-width: 768px) {

      .overlay {
        padding: 15px;
        align-items: center;
      }

      .container {
        max-width: 100%;
        width: 100%;
        padding: 18px 16px;
        border-radius: 18px;
      }

      .logo {
        width: 90%;
        max-width: 280px;
      }

      h2 {
        font-size: 22px;
      }

      input, select {
        width: 95%;
        font-size: 14px;
        padding: 10px;
      }

      button, .back-btn {
        padding: 12px;
        font-size: 15px;
      }
    }

  </style>
</head>
<body>

<div class="overlay">
  <div class="container">   
    <h2>Register New Account</h2>

    <?php if (!empty($message)): ?>
      <p class="error"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
      <label>Name</label>
      <input type="text" name="name" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Retype Password</label>
      <input type="password" name="retype_password" required>

      <label>Account Type</label>
      <select name="role" required>
        <option value="user">Staff</option>
        <option value="admin">Admin</option>
      </select>

      <button type="submit">Create Account</button>
      <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </form>
  </div>
</div>

</body>
</html>
