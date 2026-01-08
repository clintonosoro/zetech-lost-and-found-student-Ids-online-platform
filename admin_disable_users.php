<?php
session_start();
include('connect.php');

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

// Check if user ID is provided
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Update the user's status to 'disabled' in the database
    $sql = "UPDATE users SET status = 'disabled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Successfully disabled the user
        header('Location: admin_dashboard.php?status=disabled');
        exit();
    } else {
        // Error disabling the user
        header('Location: admin_dashboard.php?error=disable_failed');
        exit();
    }
} else {
    // Invalid request if no user_id is provided
    header('Location: admin_dashboard.php?error=invalid_request');
    exit();
}
?>
