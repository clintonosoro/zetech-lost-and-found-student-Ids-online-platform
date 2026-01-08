<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php'; // Ensure database connection is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['message'])) {
        $message = trim($_POST['message']);
        $user_role = 'admin'; // Adjust role if necessary

        // Debug: Check if values are received
        echo "Message: $message | User Role: $user_role <br>";

        // Prepare and execute SQL statement
        $stmt = $conn->prepare("INSERT INTO notifications (user_role, message, created_at) VALUES (?, ?, NOW())");

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ss", $user_role, $message);

        if ($stmt->execute()) {
            echo "Notification posted successfully!";
        } else {
            die("Error posting aannouncement: " . $stmt->error);
        }

        $stmt->close();
    } else {
        die("Error: Message cannot be empty.");
    }
} else {
    die("Invalid request method.");
}
?>
