<?php
include 'connect.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = $_GET['id'];

// Delete user
$query = "DELETE FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: admin_dashboard.php");
} else {
    die("Database Query Failed: " . mysqli_error($conn));
}
?>
