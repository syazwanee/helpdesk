<?php
session_start();
if (isset($_SESSION['user_id'])) {

}


$registered_name = $_SESSION['registered_name'] ?? '';
unset($_SESSION['registered_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration Successful</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f8f4ff;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  .success-box {
    background: #fff;
    padding: 40px 50px;
    border-radius: 18px;
    text-align: center;
    max-width: 450px;
    box-shadow: 0 5px 25px rgba(106, 13, 173, 0.15);
  }
  .success-box h1 {
    color: #4b0082;
    font-weight: 700;
    font-size: 26px;
    margin-bottom: 15px;
  }
  .success-box p {
    color: #333;
    font-size: 15px;
    margin-bottom: 25px;
  }
  .btn {
    background: #6a0dad;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    font-weight: 600;
    border-radius: 10px;
    transition: 0.3s;
  }
  .btn:hover {
    background: #4b0082;
  }
  .checkmark {
    font-size: 60px;
    color: #0b9e2a;
    margin-bottom: 15px;
  }
</style>
</head>
<body>
  <div class="success-box">
    <div class="checkmark">✔</div>
    <h1>Registration Successful!</h1>
    <?php if (!empty($registered_name)): ?>
      <p>Welcome, <strong><?= htmlspecialchars($registered_name) ?></strong>. New account has been created successfully.</p>
    <?php else: ?>
      <p>New account has been created successfully. </p>
    <?php endif; ?>
    <a href="admin_dashboard.php" class="btn">Go to Dashboard</a>
  </div>
</body>
</html>
