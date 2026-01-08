<?php
include 'connect.php';

$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p><strong>" . htmlspecialchars($row['title']) . ":</strong> " . htmlspecialchars($row['message']) . " <small>" . $row['created_at'] . "</small></p>";
    }
} else {
    echo "<p>No notifications available.</p>";
}
?>
