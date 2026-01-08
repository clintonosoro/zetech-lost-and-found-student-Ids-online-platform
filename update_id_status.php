<?php
session_start();
include('connect.php');

// Check if the user is logged in and has the police role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'police') {
    header("Location: login.php");
    exit();
}

// Get the report_id from the URL
if (isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];

    // Fetch the specific report details
    $sql = "SELECT * FROM lost_ids WHERE id = '$report_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $report = mysqli_fetch_assoc($result);
    } else {
        echo "<p>No report found with ID: $report_id</p>";
        exit();
    }
} else {
    echo "<p>Report ID is missing.</p>";
    exit();
}

// Check if the form is submitted to update the status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    // Update the status of the report
    $update_sql = "UPDATE lost_ids SET status = '$status' WHERE id = '$report_id'";
    if (mysqli_query($conn, $update_sql)) {
        echo "<p>Status updated successfully!</p>";
        // Redirect back to the police dashboard
        header("Location: police_dashboard.php");
        exit();
    } else {
        echo "<p>Error updating status: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Report Status</title>
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <h1>Update Report Status</h1>

    <p><strong>Report ID:</strong> <?php echo $report['id']; ?></p>
    <p><strong>Student Name:</strong> <?php echo $report['student_name']; ?></p>
    <p><strong>Status:</strong> <?php echo $report['status']; ?></p>
    <p><strong>Reported Date:</strong> <?php echo $report['date_reported']; ?></p>

    <!-- Update Status Form -->
    <form method="POST">
        <label for="status">New Status:</label>
        <select name="status" id="status" required>
            <option value="verified" <?php echo ($report['status'] == 'verified') ? 'selected' : ''; ?>>Verified</option>
            <option value="invalid" <?php echo ($report['status'] == 'invalid') ? 'selected' : ''; ?>>Invalid</option>
            <option value="pending" <?php echo ($report['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
        </select>
        <button type="submit">Update Status</button>
    </form>
</body>
</html>
