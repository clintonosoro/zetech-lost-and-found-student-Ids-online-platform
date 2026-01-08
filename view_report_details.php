<?php
session_start();
include 'connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

// Fetch report ID from URL
if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    // Fetch report details from the database
    $query = "SELECT * FROM lost_ids WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $report_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();

    if (!$report) {
        die("Report not found.");
    }
} else {
    die("No report ID provided.");
}

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        // Update the report status to 'Approved'
        $update_query = "UPDATE lost_ids SET status = 'Approved' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('i', $report_id);
        $stmt->execute();
        
        // Redirect to the same page with success message
        header("Location: view_report_details.php?id=$report_id&status=approved");
        exit();
    } elseif (isset($_POST['reject'])) {
        // Update the report status to 'Rejected'
        $update_query = "UPDATE lost_ids SET status = 'Rejected' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('i', $report_id);
        $stmt->execute();
        
        // Redirect to the same page with success message
        header("Location: view_report_details.php?id=$report_id&status=rejected");
        exit();
    }
}

// Handle the status update via URL query parameters
$status_message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'approved') {
        $status_message = "<div class='alert alert-success'>Report approved successfully!</div>";
    } elseif ($_GET['status'] == 'rejected') {
        $status_message = "<div class='alert alert-danger'>Report rejected.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost ID Report Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 30px;
        }
        h3 {
            color: #002147;
        }
        .card {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .card-header {
            background-color: #002147;
            color: white;
            font-weight: bold;
        }
        .card-body {
            background-color: white;
            padding: 20px;
        }
        .btn-success, .btn-danger {
            margin-top: 15px;
        }
        .badge-success {
            background-color: #28a745;
        }
        .details p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .details strong {
            color: #002147;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Lost ID Report Details</h3>
        
        <!-- Display Status Message -->
        <?php echo $status_message; ?>

        <div class="card">
            <div class="card-header">
                Report Information
            </div>
            <div class="card-body details">
                <p><strong>Student Name:</strong> <?php echo $report['student_name']; ?></p>
                <p><strong>Student ID:</strong> <?php echo $report['student_id']; ?></p>
                <p><strong>ID Number:</strong> <?php echo $report['id_number']; ?></p>
                <p><strong>Date Reported:</strong> <?php echo $report['date_reported']; ?></p>
                <p><strong>Date Lost:</strong> <?php echo $report['date_lost']; ?></p>
                <p><strong>Status:</strong> <?php echo $report['status']; ?></p>

                <!-- Approve or Reject the report -->
                <?php if ($report['status'] != 'Approved' && $report['status'] != 'Rejected'): ?>
                    <form method="POST">
                        <button type="submit" name="approve" class="btn btn-success">Approve Report</button>
                        <button type="submit" name="reject" class="btn btn-danger">Reject Report</button>
                    </form>
                <?php else: ?>
                    <?php if ($report['status'] == 'Approved'): ?>
                        <span class="badge badge-success">Report Approved</span>
                    <?php elseif ($report['status'] == 'Rejected'): ?>
                        <span class="badge badge-danger">Report Rejected</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
