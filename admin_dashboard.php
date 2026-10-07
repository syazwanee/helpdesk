<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['name'];

// Database connection
include 'db_connect.php';

// --- Fetch hardware ticket counts ---
$hardwareData = [];
$hardwareQuery = "
    SELECT h.hardware_type, COUNT(*) AS count
    FROM tickets t
    JOIN hardware_issues h ON t.ticket_id = h.ticket_id
    WHERE t.status IN ('Pending', 'In Progress','Resolve')
    GROUP BY h.hardware_type
";
$hardwareResult = $conn->query($hardwareQuery);
$hardwareData = $hardwareResult->fetch_all(MYSQLI_ASSOC);



// --- Fetch application ticket counts ---
$applicationData = [];
$applicationQuery = "
    SELECT a.application_name, COUNT(*) AS count
    FROM tickets t
    JOIN application_issues a ON t.ticket_id = a.ticket_id
    WHERE t.status IN ('Pending', 'In Progress','Resolve')
    GROUP BY a.application_name
";
$applicationResult = $conn->query($applicationQuery);
$applicationData = $applicationResult->fetch_all(MYSQLI_ASSOC);


?>

<?php include 'sidebar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
 

html, body {
  margin: 0;
  padding: 0;
  width: 100%;
  overflow-x: hidden;
  font-family: 'Segoe UI', sans-serif;
}

* {
  box-sizing: border-box;
}

/* Charts */
.chart-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  margin-top: 30px;
  width: 100%;
}

.chart-box {
  background: #fff;
  padding: 18px;
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 450px;
  text-align: center;
}

.chart-box h3 {
  color: #4b0082;
  margin-bottom: 18px;
}
 
.welcome-box {
  text-align: center;
  padding: 35px 20px;
  background: linear-gradient(135deg, #6a0dad, #4b0082);
  color: white;
  border-radius: 15px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  width: 100%;
  max-width: 100%;
}

.welcome-box h1 {
  font-size: 32px;
  margin-bottom: 10px;
}

.welcome-box p {
  font-size: 18px;
  margin: 0;
  opacity: 0.9;
}

.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-top: 30px;
  width: 100%;
}

.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 20px;
  text-align: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.card h3 {
  color: #4b0082;
  margin-bottom: 12px;
  font-size: 20px;
}

.card p {
  font-size: 14px;
  margin-bottom: 15px;
  color: #333;
}

.btn {
  display: inline-block;
  background: #6a0dad;
  color: #fff;
  text-decoration: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: bold;
  transition: background 0.3s ease;
}

.btn:hover {
  background: #4b0082;
}

@media (max-width: 768px) {

  .chart-container {
    flex-direction: column;
    gap: 18px;
    margin-top: 20px;
  }

  .chart-box {
    padding: 15px;
  }

  .welcome-box {
    padding: 25px 15px;
  }

  .welcome-box h1 {
    font-size: 26px;
  }

  .welcome-box p {
    font-size: 16px;
  }

  .card {
    padding: 18px;
  }

  .card h3 {
    font-size: 18px;
  }

  .card p {
    font-size: 13px;
  }
}

  </style>
</head>
<body>

<div class="content">
  <div class="welcome-box">
    <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?> </h1>
    <p>Ready to manage tickets, close issues, and keep everything running smoothly?</p>
  </div>

  
  <div class="chart-container">
    <div class="chart-box">
      <h3>Hardware Ticket Breakdown</h3>
      <canvas id="hardwareChart"></canvas>
    </div>
    <div class="chart-box">
      <h3>Application Ticket Breakdown</h3>
      <canvas id="applicationChart"></canvas>
    </div>
  </div>

  <div class="dashboard">
    <div class="card">
      <h3>Application Tickets</h3>
      <p>View and manage all current application-related tickets.</p>
      <a href="Acurrent_tickets.php" class="btn">View Application Tickets</a>
    </div>

    <div class="card">
      <h3>Hardware Tickets</h3>
      <p>View and manage all current hardware-related tickets.</p>
      <a href="Hcurrent_tickets.php" class="btn">View Hardware Tickets</a>
    </div>

    <div class="card">
      <h3>Closed Tickets</h3>
      <p>Review all tickets that have been resolved and closed.</p>
      <a href="completed_tickets.php" class="btn">View Closed Tickets</a>
    </div>

    <div class="card">
      <h3>Reports</h3>
      <p>Generate and download monthly helpdesk activity reports.</p>
      <a href="reports.php" class="btn">Download Reports</a>
    </div>

    <div class="card">
      <h3>Create New User</h3>
      <p> New user account creation page </p>
      <a href="register.php" class="btn">New User</a>
    </div>

    <div class="card">
      <h3>Change Password</h3>
      <p>Update old password to new password.</p>
      <a href="change_password.php" class="btn">Change Password</a>
    </div>

    <div class="card">
      <h3>Log out</h3>
      <p>Clear cache and end session.</p>
      <a href="logout.php" class="btn">Log out</a>
    </div>
  </div>
</div>

<style>
.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 40px;
}

.welcome-box {
  text-align: center;
  padding: 40px 20px;
  background: linear-gradient(135deg, #6a0dad, #4b0082);
  color: white;
  border-radius: 15px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.welcome-box h1 {
  font-size: 36px;
  margin-bottom: 10px;
  font-weight: bold;
}

.welcome-box p {
  font-size: 18px;
  margin: 0;
  opacity: 0.9;
}

.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 25px;
  text-align: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.card h3 {
  color: #4b0082;
  margin-bottom: 12px;
  font-size: 20px;
}

.card p {
  font-size: 14px;
  margin-bottom: 15px;
  color: #333;
}

.btn {
  display: inline-block;
  background: #6a0dad;
  color: #fff;
  text-decoration: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: bold;
  transition: background 0.3s ease;
}

.btn:hover {
  background: #4b0082;
}
</style>
<script>
  
  // PHP to JS data
  const hardwareLabels = <?php echo json_encode(array_column($hardwareData, 'hardware_type')); ?>;
  const hardwareCounts = <?php echo json_encode(array_column($hardwareData, 'count')); ?>;

  const applicationLabels = <?php echo json_encode(array_column($applicationData, 'application_name')); ?>;
  const applicationCounts = <?php echo json_encode(array_column($applicationData, 'count')); ?>;


  // Hardware Chart
  new Chart(document.getElementById('hardwareChart'), {
    type: 'pie',
    data: {
      labels: hardwareLabels,
      datasets: [{
        data: hardwareCounts,
        backgroundColor: [
          '#5E2E91', 
          '#7D3C98', 
          '#C39BD3', 
          '#D4AC0D', 
          '#F1C40F', 
          '#F7DC6F', 
          '#F4D03F', 
          '#B7950B'  
        ]
      }
    ]
    },

    options: { 
  responsive: true,
  onClick: function(evt, elements) {
    if (elements.length > 0) {
      const index = elements[0].index;
      const selectedType = hardwareLabels[index];
      window.location.href = "Hcurrent_tickets.php?type=" + encodeURIComponent(selectedType);
    }
  }
}

  });

  // Application Chart
  new Chart(document.getElementById('applicationChart'), {
    type: 'pie',
    data: {
      labels: applicationLabels,
      datasets: [{
        data: applicationCounts,
        backgroundColor: [
          '#4A148C', '#6A1B9A', '#8E24AA', 
          '#CE93D8', 
          '#7D6608', '#9A7D0A', '#B7950B', '#D4AC0D', 
          '#F1C40F', '#F7DC6F', '#F9E79F', '#F4D03F', 
          '#BB8FCE', '#D2B4DE'  
        ]

,
      }]
    },
   options: { 
  responsive: true,
  onClick: function(evt, elements) {
    if (elements.length > 0) {
      const index = elements[0].index;
      const selectedApp = applicationLabels[index];
      window.location.href = "Acurrent_tickets.php?app=" + encodeURIComponent(selectedApp);
    }
  }
}

  });
</script>
</body>
</html>
