<?php
session_start();
include('connect.php');

// Check if the user is logged in and has the police role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'police') {
    header("Location: login.php");
    exit();
}

// Check if a report ID is provided in the URL
if (isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];

    // Fetch the report details from the database
    $stmt = $conn->prepare("SELECT * FROM lost_ids WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();
    $stmt->close();

    // If the form is submitted to update the status
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE lost_ids SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $report_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Status updated successfully!'); window.location='police_dashboard.php';</script>";
        } else {
            echo "<script>alert('Failed to update status. Please try again.');</script>";
        }
        
        $stmt->close();
    }
} else {
    echo "<script>alert('No report ID provided.'); window.location='police_dashboard.php';</script>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2b3e55;
        }

        p {
            font-size: 1.1em;
        }

        form {
            margin-top: 20px;
        }

        label, select, button {
            font-size: 1em;
            margin-top: 10px;
        }

        button {
            background-color: #2b3e55;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #ffcc00;
            color: black;
        }

        a {
            color: #2b3e55;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($report): ?>
            <h2>Report Details</h2>
            <p><strong>Report ID:</strong> <?= $report['id']; ?></p>
            <p><strong>Student Name:</strong> <?= htmlspecialchars($report['student_name']); ?></p>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($report['student_id']); ?></p>
            <p><strong>ID Number:</strong> <?= htmlspecialchars($report['id_number']); ?></p>
            <p><strong>Date Lost:</strong> <?= htmlspecialchars($report['date_lost']); ?></p>
            <p><strong>Date Reported:</strong> <?= htmlspecialchars($report['date_reported']); ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($report['status']); ?></p>
            <p><strong>Police Report:</strong> <a href="<?= htmlspecialchars($report['police_report']); ?>" target="_blank">View Report</a></p>
            
            <form method="POST">
                <label for="status">Update Status:</label>
                <select name="status" id="status" required>
                    <option value="">Select Status</option>
                    <option value="Approved">Approve</option>
                    <option value="Rejected">Reject</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
        <?php else: ?>
            <p>Report not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
