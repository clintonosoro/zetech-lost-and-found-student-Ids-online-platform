<?php
include 'connect.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die("Invalid request.");
}

$id = $_GET['id'];
$action = $_GET['action'];

if ($action == 'activate') {
    $status = 'Active';
} elseif ($action == 'deactivate') {
    $status = 'Inactive';
} else {
    die("Invalid action.");
}

// Update user status
$query = "UPDATE users SET status = '$status' WHERE id = $id";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: admin_dashboard.php");
} else {
    die("Database Query Failed: " . mysqli_error($conn));
}
?>
