<?php
session_start();
include('connect.php');

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Enable user
    $sql = "UPDATE users SET status = 'active' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?status=enabled");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=enable_failed");
        exit();
    }
} else {
    header("Location: admin_dashboard.php?error=invalid_request");
    exit();
}
?>
