<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>

body {
  max-width: 100%;
  overflow-x: hidden;
  font-family: 'Segoe UI', sans-serif;
  margin: 0;
  padding: 0;
}


.sidebar {
  width: 220px;
  background: linear-gradient(180deg, #4b0082, #2e004f);
  min-height: 100vh;
  color: white;
  padding: 20px 0;
  position: fixed;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: all 0.3s ease;
  z-index: 1000;
  box-shadow: 4px 0 15px rgba(0,0,0,0.25);
}


.logo-container {
  background: white;
  padding: 18px;
  border-radius: 15px;
  margin: 10px 30px 25px 30px;
  text-align: center;
}

.sidebar-logo {
  width: 130px;
  height: auto;
  display: block;
  margin: 0 auto;
}


.sidebar a {
  display: block;
  padding: 12px 25px;
  color: white;
  text-decoration: none;
  font-weight: 600;
  text-align: left;
  transition: all 0.2s ease;
  border-left: 4px solid transparent;
}

.sidebar a:hover {
  background: #6a0dad;
  border-left: 4px solid #d4ac0d;
  padding-left: 30px;
}


.toggle-btn {
  background: #6a0dad;
  color: white;
  border: none;
  padding: 10px;
  font-size: 18px;
  cursor: pointer;
  width: 80%;
  margin: 20px auto 0;
  border-radius: 8px;
  display: none;
  transition: 0.2s;
}

.toggle-btn:hover {
  background: #5b0095;
}


.content {
  margin-left: 220px;
  padding: 30px;
  flex-grow: 1;
  background: rgba(255,255,255,0.95);
  min-height: 100vh;
  transition: margin-left 0.3s ease;
}

.content.full {
  margin-left: 0;
}


.content table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.content .table-wrapper {
  overflow-x: auto;
}

.content table th,
.content table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.content table th {
  background: #eee;
}

.content .btn {
  display: inline-block;
  padding: 10px 15px;
  background: #6a0dad;
  color: white;
  text-decoration: none;
  border-radius: 5px;
}

.content .btn:hover {
  background: #4b0082;
}

.content img {
  max-width: 100%;
  height: auto;
}


@media (max-width: 768px) {
  .toggle-btn {
    display: block;
  }

  .sidebar {
    transform: translateX(-100%);
  }

  .sidebar.show {
    transform: translateX(0);
  }

  .content {
    margin-left: 0;
    padding-top: 20px;
  }
}

.powered-by-container {
  display: flex;             
  align-items: center;       
  justify-content: center;   
  gap: 8px;                 
  margin-bottom: 60px;     
  background: #fff;        
  padding: 6px 10px;
  border-radius: 10px;
  width: fit-content;        
  margin-left: auto;         
  margin-right: auto;
}


.powered-text {
  font-size: 12px; 
  font-weight: bold;
  color: #4b0082;
  white-space: nowrap;
  font-family: 'Segoe UI', sans-serif;
}
.powered-logo {
  width: 100px;
  height: auto;
  display: block;
}

  </style>
</head>

<body>

  <div class="sidebar" id="sidebar">
    <div>
      <div class="logo-container">
        <img src="logofixit.png" alt="Logo" class="sidebar-logo">
      </div>
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="Hcurrent_tickets.php">Active Hardware Tickets</a>
      <a href="Acurrent_tickets.php">Active Application Tickets</a>
      <a href="completed_tickets.php">Closed Tickets</a>
      <a href="reports.php">Reports</a>
      <a href="register.php">Create Account</a>
      <a href="change_password.php">Change Password</a>
      <a href="logout.php">Logout</a>
    </div>

    <div class="powered-by-container">
      <span class="powered-text">Powered by</span>
      <img src="logo.png" alt="Powered Logo" class="powered-logo">
    </div>

   

</div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const content = document.getElementById('content');
      sidebar.classList.toggle('show');
      content.classList.toggle('full');
    }
  </script>
</body>
