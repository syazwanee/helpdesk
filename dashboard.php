<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-size: cover;
      background-attachment: fixed;
    }
    .overlay {
      background: rgba(75, 0, 130, 0.88);
      min-height: 110vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .container {
      background: #fff;
      max-width: 650px;
      width: 100%;
      padding: 35px;
      border-radius: 20px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.3);
      border-top: 10px solid #6a0dad;
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .logo {
      width: 500px;
      margin-bottom: 15px;
      background: #fff;
      border-radius: 10px;
      padding: 10px;
    }
    h2 {
      color: #4b0082;
      margin-bottom: 15px;
      font-size: 26px;
    }
    .welcome {
      font-size: 18px;
      margin-bottom: 25px;
      color: #333;
    }
    .btn {
      display: block;
      width: 95%;
      padding: 12px;
      background: #6a0dad;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      color: white;
      text-decoration: none;
      margin-bottom: 15px;
      transition: 0.3s;
    }
    .btn:hover {
      background: #4b0082;
    }
    .btn-logout {
      background: #B22222;
    }
    .btn-logout:hover {
      background: #800000;
    }
    .quote-box {
      margin-top: 25px;
      font-style: italic;
      color: #444;
      background: #f9f9f9;
      padding: 15px;
      border-radius: 10px;
      box-shadow: inset 0 0 8px rgba(0,0,0,0.1);
    }
    .quote-author {
      margin-top: 8px;
      font-size: 14px;
      color: #666;
    }

    .it-contact {
      margin-top: 25px;
      background: #f4f4f9;
      padding: 15px;
      border-left: 5px solid #6a0dad;
      border-radius: 10px;
      font-size: 16px;
      color: #333;
    }
    .it-contact strong {
      color: #4b0082;
    }
    @media (max-width: 480px) {

  .overlay {
    min-height: 100vh;
    padding: 10px;
  }

  .container {
    max-width: 100%;
    padding: 20px;
  }

  .logo {
    width: 100%;
    max-width: 260px;
  }

  h2 {
    font-size: 22px;
  }

  .welcome {
    font-size: 16px;
  }

  .it-contact {
    font-size: 14px;
    margin-top: 15px;
  }

  .btn {
    padding: 10px;
    font-size: 15px;
  }
}

.powered-by {
  margin-top: 25px;
  padding-top: 15px;
  border-top: 1px solid #ddd;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #666;
}

.powered-by img {
  height: 28px;
  object-fit: contain;
}


  </style>
</head>
<body>

  <div class="overlay">
    <div class="container">
      <img src="logofixit.png" alt="Company Logo" class="logo">

      <h2>User Dashboard</h2>
      <p class="welcome">Welcome, <strong><?php echo htmlspecialchars($name); ?></strong></p>


      <a href="form.php" class="btn">Submit New Ticket</a>
      <a href="my_tickets.php" class="btn">My Tickets</a>
      <a href="logout.php" class="btn btn-logout">Logout</a>

  
      <div class="it-contact">
        📞 For urgent IT support</br> 
       <br>Hardware Team : </br> 
       <strong>Fahzan - 5858(010-2972649)</strong><br>
       <strong>Nizam - 5888(019-6555433)</strong><br>
       <strong>Darwis - 5777(013-5149954)</strong><br>
       <br>Application Team : </br> 
       <strong>Elmy - 5757(013-3186538)</strong><br>
       <strong>Fajrina - 5740(012-7635190)</strong><br>
       <strong>Shafieqa - 5858(014-9210926)</strong>

      </div>

      <div class="powered-by">
      <span>Powered by</span>
      <img src="logo.png" alt="Powered By Logo">
      </div>


    </div>
  </div>
</body>
</html>
