<?php
ob_start(); // Start output buffering to prevent header issues
session_start();
include('connect.php'); // Ensure database connection

// Check if police is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'police') {
    header("Location: police_login.php");
    exit();
}

// Validate if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. No ID provided.");
}

$report_id = intval($_GET['id']); // Sanitize ID

// Fetch report
$query = "SELECT * FROM id_reports WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $report_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_status = 'Verified';
        $update_query = "UPDATE id_reports SET status = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $new_status, $report_id);

        if (mysqli_stmt_execute($update_stmt)) {
            // Redirect after successful verification
            header("Location: police_dashboard.php?success=verified");
            exit();
        } else {
            error_log("MySQL Error: " . mysqli_error($conn));
            die("Database update failed.");
        }
    }
} else {
    die("Report not found.");
}
ob_end_flush(); // Flush output buffer
?>
