<?php
session_start();

$conn = new mysqli("localhost", "mighcomm_ithelpdesk", "mighcomm_ithelpdesk", "mighcomm_helpdesk_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user name
$user_id = $_SESSION['user_id'] ?? null;
$user_name = '';

if ($user_id) {
    $sql = "SELECT name FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_name);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Helpdesk Ticket</title>
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
      background: rgba(75, 0, 130, 0.8);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .container {
      background: #fff;
      max-width: 750px;
      width: 100%;
      padding: 25px 30px;
      border-radius: 15px;
      box-shadow: 0px 6px 18px rgba(0,0,0,0.3);
      border-top: 8px solid #6a0dad;
      text-align: center;
    }
    .logo {
      width: 400px;
      margin-bottom: 15px;
    }
    h2 {
      text-align: center;
      color: #4b0082;
    }
    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: bold;
      color: #4b0082;
      text-align: left;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    textarea { resize: vertical; }
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

    .form-section {
      display: none;
      margin-top: 20px;
      border-top: 2px solid #eee;
      padding-top: 20px;
      text-align: left;
    }
    .topbar {
      position: fixed;
      top: 0;
      right: 0;
      background: white;
      color: #4b0082;
      padding: 10px 20px;
      border-radius: 0 0 0 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 15px;
      z-index: 1000;
    }
    .online-dot {
      width: 10px;
      height: 10px;
      background: #00cc44;
      border-radius: 50%;
      margin-right: 8px;
      box-shadow: 0 0 6px #00cc44;
    }

    @media (max-width: 480px) {

  .overlay {
    min-height: 100vh;
    padding: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
  }


  .container {
    max-width: 100%;
    padding: 18px;
    border-radius: 12px;
  }


  .logo {
    width: 100%;
    max-width: 240px;
  }

  
  h2 {
    font-size: 22px;
  }

  h3 {
    font-size: 18px;
  }


  label {
    font-size: 14px;
  }

  input, select, textarea {
    font-size: 14px;
    padding: 9px;
    width: 95%;
    max-width: 95%;
  }

  textarea {
    rows: 3;
  }

 
  button {
    font-size: 15px;
    padding: 10px;
  }


  .form-section {
    margin-top: 15px;
    padding-top: 15px;
  }


  .topbar {
    font-size: 13px;
    padding: 8px 14px;
  }
}

  </style>
</head>
<body>

  <div class="overlay">
    <div class="container">
   
      <img src="logo.png" alt="Company Logo" class="logo">

      <form action="submit_ticket.php" method="POST" enctype="multipart/form-data">

    
        <label for="formType">Select Request Type</label>
        <select id="formType" name="request_type" onchange="toggleForm()" required>
          <option value="">-- Select Type --</option>
          <option value="Hardware">Hardware</option>
          <option value="Application">Application</option>
        </select>

        <!-- Hardware Section -->
        <div id="hardwareForm" class="form-section">
          <h3 style="color:#4b0082;">Hardware Request</h3>

          <label>Name</label>
          <input type="text" name="name"required>

          <label>Specific Area (E.g.: ROOM 13)</label>
          <input type="text" name="area"required>

          <label>Contact Number (Extension Number)</label>
          <input type="text" name="contact"required>

          <div class="form-group">
            <label for="email">Email (optional):</label>
            <input type="email" id="email" name="email" >
          </div>

          <label>Select Hardware</label>
          <select name="hardware_type"required>
            <option value="">-- Select --</option>
            <option value="PC/Notebook">PC/Notebook</option>
            <option value="Printer / Fax / Scan">Printer / Fax / Scan</option>
            <option value="Telephone">Telephone</option>
            <option value="POC Terminal / Arm">POC Terminal / Arm</option>
            <option value="Door Access">Door Access</option>
            <option value="CCTV">CCTV</option>
            <option value="QMS Device">QMS Device</option>
            <option value="Nurse Call">Nurse Call</option>
            <option value="Network">Network</option>
            <option value="New Request">New Request</option>
          </select>

          <label>New Request Details | Describe the problem</label>
          <textarea name="description" rows="4"></textarea>

          <label>Upload Attachment (E.g.: PRF/Image Problem)<br>
          <small>*For New Request Please Upload PRF*</small></label>
          <input type="file" name="attachment">

          <label>Anydesk ID</label>
          <input type="text" name="anydesk">

          <input type="hidden" name="severity" value="3">

        </div>

        <!-- Application Section -->
        <div id="applicationForm" class="form-section">
          <h3 style="color:#4b0082;">Application Request</h3>

          <label>Name</label>
          <input type="text" name="name"required>

          <label>Specific Area (E.g.: ROOM 13)</label>
          <input type="text" name="area"required>

          <label>Contact Number (Extension Number)</label>
          <input type="text" name="contact"required>

          <div class="form-group">
            <label for="email">Email (optional):</label>
            <input type="email" id="email" name="email" >
          </div>

          <label>Select Application</label>
          <select name="application_name"required>
            <option value="">-- Select Application --</option>
            <option value="VESALIUS (HIS)">VESALIUS (HIS)</option>
            <option value="QMED">QMED</option>
            <option value="CHUPP">CHUPP</option>
            <option value="PACSYS (TV)">PACSYS (TV)</option>
            <option value="WINDOWS ISSUE">WINDOWS ISSUE</option>
            <option value="MICROSOFT OFFICE">MICROSOFT OFFICE (Word/Excel/PowerPoint)</option>
            <option value="LAB RESULT">LAB RESULT</option>
            <option value="RIS / PACS (RADIOLOGY)">RIS / PACS (RADIOLOGY)</option>
            <option value="RIS / PACS CV (CARDIOLOGY)">RIS / PACS CV (CARDIOLOGY)</option>
            <option value="LIS">LIS</option>
            <option value="UBS">UBS</option>
            <option value="QTMS/QPAY/SPHERE">QTMS/QPAY/SPHERE</option>
            <option value="EMAIL">EMAIL</option>
            <option value="SERVER / CLOUD">SERVER / CLOUD</option>
            <option value="SHAREHOLDERS SYSTEM">SHAREHOLDERS SYSTEM</option>
            <option value="REQUEST REPORT">REQUEST REPORT</option>
            <option value="NEW REQUEST">NEW REQUEST</option>
            <option value="Other">Other</option>
          </select>

          <label>New Request Details | Describe the problem</label>
          <textarea name="description" rows="4"></textarea>

          <label>Upload Attachment (Problem Image/New Request Document)</label>
          <input type="file" name="attachment">

          <label>Anydesk ID</label>
          <input type="text" name="anydesk">

          <input type="hidden" name="severity" value="3">

        </div>

        <button type="submit">Submit Ticket</button>

         <a href="dashboard.php" class="back-btn">Back</a>

      </form>
    </div>
  </div>

  <script>
        function toggleForm() {
      const hardwareForm = document.getElementById("hardwareForm");
      const applicationForm = document.getElementById("applicationForm");
      const type = document.getElementById("formType").value;

      
      [hardwareForm, applicationForm].forEach(section => {
        section.style.display = "none";
        section.querySelectorAll("input, select, textarea").forEach(el => el.disabled = true);
      });

     
      if (type === "Hardware") {
        hardwareForm.style.display = "block";
        hardwareForm.querySelectorAll("input, select, textarea").forEach(el => el.disabled = false);
      } else if (type === "Application") {
        applicationForm.style.display = "block";
        applicationForm.querySelectorAll("input, select, textarea").forEach(el => el.disabled = false);
      }
}

  </script>

  <div class="topbar">
  <div class="online-dot"></div>
  <?php echo htmlspecialchars($user_name ?: 'Guest'); ?>
</div>


</body>
</html>
