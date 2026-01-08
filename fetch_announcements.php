<?php
include 'connect.php'; // Ensure this file contains the correct database connection

$sql = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5"; // Fetch the latest 5 announcements
$result = $conn->query($sql);

$announcements = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = [
            'title' => $row['title'],
            'message' => $row['message'],
            'created_at' => $row['created_at']
        ];
    }
}

echo json_encode($announcements);

$conn->close();
?>
