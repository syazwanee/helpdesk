<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
$name = ucfirst($_SESSION['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ticket Submitted</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: url("islamic_bg.jpg") no-repeat center center fixed;
      background-size: cover;
      text-align: center;
      padding: 50px;
      color: white;
    }
    .container {
      background: rgba(120, 75, 152, 0.9);
      padding: 40px;
      border-radius: 15px;
      width: 90%;
      max-width: 500px;
      margin: auto;
      box-shadow: 0px 6px 20px rgba(0,0,0,0.4);
      animation: fadeIn 0.8s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    img.logo {
      width: 300px;
      margin-bottom: 20px;
      background: white;
      border-radius: 10px;
      padding: 10px;
    }
    h1 {
      margin-bottom: 10px;
      font-size: 26px;
    }
    p {
      margin-bottom: 20px;
      font-size: 16px;
    }
    .btn {
      display: inline-block;
      padding: 12px 25px;
      margin: 10px;
      background: #6a0dad;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
    }
    .btn:hover {
      background: #4b0082;
    }
    .btn-dashboard {
      background: #ccc;
      color: #333;
    }
    .btn-dashboard:hover {
      background: #999;
    }
    .quote {
      margin-top: 25px;
      font-style: italic;
      font-size: 15px;
      color: #f8f8f8;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="logo.png" class="logo" alt="Logo">
    <h1>Ticket Submitted Successfully!</h1>
    <p>Thank you, <strong><?php echo htmlspecialchars($name); ?></strong>.</p>
    <p>Your request has been recorded in the Helpdesk system.<br>We’ll get back to you soon.</p>

    <a href="form.php" class="btn">Submit Another Request</a>
    <a href="dashboard.php" class="btn btn-dashboard">Back to Dashboard</a>
    
  </div>
</body>
</html>
