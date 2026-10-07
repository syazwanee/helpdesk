<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$phpSpreadsheetAvailable = file_exists(__DIR__ . '/vendor/autoload.php');
if ($phpSpreadsheetAvailable) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if (isset($_GET['download'])) {
    $month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
    $year  = isset($_GET['year'])  ? intval($_GET['year'])  : intval(date('Y'));
    $monthYear = sprintf('%04d-%02d', $year, $month);

    $conn = new mysqli("localhost", "root", "", "helpdesk_system");
    if ($conn->connect_error) {
        http_response_code(500);
        echo "DB connection error.";
        exit();
    }

   
$sql = "SELECT 
            t.ticket_id,
            t.created_at,
            t.staff_name,
            u.name AS department,
            t.area,
            t.contact,
            t.request_type,
            h.hardware_type,
            a.application_name,
            COALESCE(h.problem_description, a.problem_description) AS description,
            t.severity,
            t.status,
            t.closed_by,
            CASE 
            WHEN t.status = 'Closed' THEN t.updated_at
             ELSE NULL
           END AS closed_at,
            COALESCE(GROUP_CONCAT(CONCAT(n.note, ' (', DATE_FORMAT(n.created_at, '%Y-%m-%d %H:%i:%s'), ')') SEPARATOR '\n'), '') AS note
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.user_id
        LEFT JOIN hardware_issues h ON t.ticket_id = h.ticket_id
        LEFT JOIN application_issues a ON t.ticket_id = a.ticket_id
        LEFT JOIN ticket_admin_notes n ON t.ticket_id = n.ticket_id
        WHERE DATE_FORMAT(t.updated_at, '%Y-%m') = ?
        GROUP BY t.ticket_id
        ORDER BY t.created_at ASC";



    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $monthYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $headers = [
        'Ticket ID','Created At','Staff Name','Department','Area','Contact',
        'Request Type','Hardware/Application Name','Description','Severity','Resolution','Status','Closed By','Closed At'
    ];

    if ($phpSpreadsheetAvailable) {
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        // Fill rows
        $rowNum = 2;
        while ($row = $result->fetch_assoc()) {
            $hardwareApp = trim(($row['hardware_type'] ?? '') . ' ' . ($row['application_name'] ?? ''));

            $sheet->setCellValue("A".$rowNum, $row['ticket_id'] ?? '');
            $sheet->setCellValue("B".$rowNum, $row['created_at'] ?? '');
            $sheet->setCellValue("C".$rowNum, $row['staff_name'] ?? '');
            $sheet->setCellValue("D".$rowNum, $row['department'] ?? '');
            $sheet->setCellValue("E".$rowNum, $row['area'] ?? '');
            $sheet->setCellValue("F".$rowNum, $row['contact'] ?? '');
            $sheet->setCellValue("G".$rowNum, $row['request_type'] ?? '');
            $sheet->setCellValue("H".$rowNum, $hardwareApp);
            $sheet->setCellValue("I".$rowNum, $row['description'] ?? '');
            $sheet->setCellValue("L".$rowNum, $row['severity'] ?? '');
            $sheet->setCellValue("M".$rowNum, $row['note'] ?? '');
            $sheet->setCellValue("N".$rowNum, $row['status'] ?? '');
            $sheet->setCellValue("O".$rowNum, $row['closed_by'] ?? '');
            $sheet->setCellValue("P".$rowNum, $row['closed_at'] ?? '');
            $rowNum++;
        }

        foreach (range('A', 'P') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "ticket_report_{$monthYear}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();

    } else {
        $filename = "ticket_report_{$monthYear}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        while ($row = $result->fetch_assoc()) {
            $hardwareApp = trim(($row['hardware_type'] ?? '') . ' ' . ($row['application_name'] ?? ''));
            fputcsv($output, [
                $row['ticket_id'] ?? '',
                $row['created_at'] ?? '',
                $row['staff_name'] ?? '',
                $row['department'] ?? '',
                $row['area'] ?? '',
                $row['contact'] ?? '',
                $row['request_type'] ?? '',
                $hardwareApp,
                $row['description'] ?? '',
                $row['severity'] ?? '',
                $row['note'] ?? '',
                $row['status'] ?? '',
                $row['closed_by'] ?? '',
                $row['closed_at'] ?? ''
            ]);
        }

        fclose($output);
        exit();
    }
}

include 'sidebar.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Reports</title>
<style>
  body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background:#f4f4f9; }
  .content { margin-left:220px; padding:50px 20px; min-height:100vh; display:flex; justify-content:center; align-items:center; }
  .report-card {
    background:white;
    padding:30px 40px;
    border-radius:16px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    max-width:500px;
    width:100%;
    text-align:center;
  }
  .report-card h2
    { color:#4b0082; font-size:28px; margin-bottom:8px; }
  .report-card p 
    { color:#555; margin-bottom:25px; font-size:15px; }
  label 
    { display:block; text-align:left; color:#4b0082; font-weight:600; margin-bottom:6px; margin-top:15px; }
  select 
    { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; font-size:15px; outline:none; transition:all 0.3s ease; }
  select:focus 
    { border-color:#6a0dad; box-shadow:0 0 5px rgba(106,13,173,0.3); }
  .btn
    { margin-top:25px; width:100%; padding:12px; font-size:16px; background:#6a0dad; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; transition:all 0.3s ease; }
  .btn:hover 
    { background:#4b0082; transform:translateY(-2px); }
  .back-btn 
    { display:inline-block; margin-top:15px; width:95%; padding:12px; text-align:center; background:#6a0dad; color:white; text-decoration:none; border-radius:8px; font-weight:600; transition:all 0.3s ease; }
  .back-btn:hover 
    { background:#4b0082; transform:translateY(-2px); }

@media (max-width: 768px) {


  .content {
    margin-left: 0;
    padding: 20px 12px;
    align-items: center;
  }


  .report-card {
    padding: 20px 18px;
    border-radius: 12px;
    max-width: 100%;
    width: 100%;
  }
  .report-card h2 {
    font-size: 22px;
  }

  .report-card p {
    font-size: 14px;
  }

  label {
    font-size: 14px;
  }

  select {
    font-size: 14px;
  }


  .btn, .back-btn {
    padding: 10px;
    font-size: 14px;
  }
  .btn:hover, .back-btn:hover {
    transform: none;
  }
}



    
</style>
</head>
<body>
<div class="content">
  <div class="report-card">
    <h2>Monthly Reports</h2>
    <p>Select the month and year below to download the complete ticket report.</p>
    <form method="GET" action="">
      <label for="month">Month</label>
      <select name="month" id="month" required>
        <?php
        $currentMonth = intval(date('m'));
        for ($m = 1; $m <= 12; $m++):
            $sel = ($m === $currentMonth) ? 'selected' : '';
        ?>
          <option value="<?php echo sprintf('%02d',$m); ?>" <?php echo $sel; ?>>
            <?php echo date('F', mktime(0,0,0,$m,1)); ?>
          </option>
        <?php endfor; ?>
      </select>

      <label for="year">Year</label>
      <select name="year" id="year" required>
        <?php
        $currentYear = intval(date('Y'));
        for ($y = $currentYear; $y >= $currentYear - 5; $y--):
            $sel = ($y === $currentYear) ? 'selected' : '';
        ?>
          <option value="<?php echo $y; ?>" <?php echo $sel; ?>><?php echo $y; ?></option>
        <?php endfor; ?>
      </select>

      <button type="submit" name="download" value="1" class="btn">Download</button>
      <a href="admin_dashboard.php" class="back-btn">Back</a>
    </form>
  </div>
</div>
</body>
</html>
