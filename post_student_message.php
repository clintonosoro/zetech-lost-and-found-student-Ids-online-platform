<?php
session_start();
require_once 'connect.php'; // Ensure database connection

// Only students can post messages
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Get message from request
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Message cannot be empty']);
    exit();
}

// Insert into notifications table
$sql = "INSERT INTO notifications (user_role, message, created_at) VALUES ('student', ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $message);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Message posted successfully']);
} else {
    echo json_encode(['error' => 'Failed to post message']);
}

$conn->close();
?>
