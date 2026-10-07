<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id       = $_SESSION['user_id'];
    $name          = $_POST['name'];
    $area          = $_POST['area'];
    $contact       = $_POST['contact'];
    $email         = $_POST['email'];
    $request_type  = $_POST['request_type'];
    $anydesk       = $_POST['anydesk'];
    $severity      = $_POST['severity'];

  
    $sql_ticket = "INSERT INTO tickets (user_id, staff_name, area, contact, email, request_type, anydesk_id, severity, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql_ticket);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "isssssss",
        $user_id,
        $name,
        $area,
        $contact,
        $email,
        $request_type,
        $anydesk,
        $severity
    );

    if (!$stmt->execute()) {
        die("Insert failed: " . $stmt->error);
    }

    $ticket_id = $stmt->insert_id;
    $stmt->close();

   
    $uploadFile = "";
    if (!empty($_FILES['attachment']['name'])) {
        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadFile = $uploadDir . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $uploadFile);
    }

    // Hardware Ticket
    if ($request_type == "Hardware") {
        $hardwareType = $_POST['hardware_type'];
        $description  = $_POST['description'];

        $sql_hardware = "INSERT INTO hardware_issues (ticket_id, hardware_type, problem_description, attachment) 
                         VALUES (?, ?, ?, ?)";
        $stmt_h = $conn->prepare($sql_hardware);
        $stmt_h->bind_param("isss", $ticket_id, $hardwareType, $description, $uploadFile);
        $stmt_h->execute();
        $stmt_h->close();
        $conn->close();
        header("Location: success.php");
        exit();
    }

    // Application Ticket
    else if ($request_type == "Application") {
        $appName     = $_POST['application_name'];
        $description = $_POST['description'];

        $sql_app = "INSERT INTO application_issues (ticket_id, application_name, problem_description, attachment) 
                    VALUES (?, ?, ?, ?)";
        $stmt_a = $conn->prepare($sql_app);
        $stmt_a->bind_param("isss", $ticket_id, $appName, $description, $uploadFile);
        $stmt_a->execute();
        $stmt_a->close();

        $conn->close();
        header("Location: success.php");
        exit();
    }
}
?>
