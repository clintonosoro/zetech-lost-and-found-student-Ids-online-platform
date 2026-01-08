<?php
session_start();
include('connect.php');

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=lost_id_reports.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Open output buffer
$output = fopen("php://output", "w");

// Column headers
$columns = ["ID", "Name", "Student ID", "National ID", "Date Reported", "Status"];
fputcsv($output, $columns, "\t"); // Use tab separator for Excel compatibility

// Fetch data from the lost_ids table
$query = "SELECT id, student_name, student_id, id_number, date_reported, status FROM lost_ids ORDER BY date_reported DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Fix National ID issue by adding a tab before the number (Excel will treat it as text)
        $row['id_number'] = "\t" . $row['id_number'];  

        // Fix Date Format issue: Ensure date is properly formatted
        if ($row['date_reported'] == '0000-00-00') {
            $row['date_reported'] = 'N/A'; // Replace invalid dates
        } else {
            $row['date_reported'] = date("m/d/Y", strtotime($row['date_reported'])); // Convert to MM/DD/YYYY
        }

        fputcsv($output, $row, "\t"); // Write each row to the Excel file
    }
} else {
    fputcsv($output, ["Error fetching data"], "\t");
}

// Close output buffer
fclose($output);
exit();
?>
