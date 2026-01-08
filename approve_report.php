<?php
session_start();
include 'connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

// Fetch report ID from URL
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // Update the report status to "Approved"
    $query = "UPDATE lost_ids SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $report_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard with success message
        header("Location: admin_dashboard.php?status=report_approved");
        exit;
    } else {
        echo "Error: Unable to approve report.";
    }
} else {
    die("No report ID provided.");
}
?>
