<?php
// Assuming you have a database connection set up
include('connect.php');

// Start session to manage user roles
session_start();

// Ensure only admin can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Unauthorized access.";
    exit;
}

// Check if the report ID is provided in the URL
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // Fetch the details of the lost ID from the database
    $query = "SELECT * FROM lost_ids WHERE id = '$report_id' AND student_id IS NOT NULL";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $lost_id = mysqli_fetch_assoc($result);
    } else {
        echo "Lost ID not found or not associated with a student!";
        exit;
    }
} else {
    echo "No Lost ID report ID provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Lost ID Report Details</title>
    <!-- Add your CSS or Bootstrap links here -->
</head>
<body>
    <h1>Lost ID Report Details (Admin View)</h1>
    <table border="1">
        <tr>
            <th>Student Name</th>
            <td><?php echo $lost_id['student_name']; ?></td>
        </tr>
        <tr>
            <th>Student ID</th>
            <td><?php echo $lost_id['student_id']; ?></td>
        </tr>
        <tr>
            <th>ID Number</th>
            <td><?php echo $lost_id['id_number']; ?></td>
        </tr>
        <tr>
            <th>Date Reported</th>
            <td><?php echo $lost_id['date_reported']; ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo $lost_id['status']; ?></td>
        </tr>
        <tr>
            <th>Date Lost</th>
            <td><?php echo $lost_id['date_lost']; ?></td>
        </tr>
        <tr>
            <th>Police Report</th>
            <td><a href="<?php echo $lost_id['police_report']; ?>" target="_blank">View Police Report</a></td>
        </tr>
        <tr>
            <th>Reported At</th>
            <td><?php echo $lost_id['reported_at']; ?></td>
        </tr>
    </table>

    <a href="admin_notifications.php" class="btn btn-primary">Back to Notifications</a>
</body>
</html>
