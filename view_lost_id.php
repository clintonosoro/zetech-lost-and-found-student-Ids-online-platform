<?php
// Assuming you have a database connection set up
include('connect.php');

// Check if the report ID is provided in the URL
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // Fetch the details of the lost ID from the database
    $query = "SELECT * FROM lost_ids WHERE id = '$report_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $lost_id = mysqli_fetch_assoc($result);
    } else {
        echo "Lost ID not found!";
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
    <title>Lost ID Details</title>
    <!-- Add your CSS or Bootstrap links here -->
</head>
<body>
    <h1>Lost ID Details</h1>
    <table>
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
            <th>Police Report</th>
            <td><a href="<?php echo $lost_id['police_report']; ?>" target="_blank">View Police Report</a></td>
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
            <th>Reported At</th>
            <td><?php echo $lost_id['reported_at']; ?></td>
        </tr>
    </table>
</body>
</html>
