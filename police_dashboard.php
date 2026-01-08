<?php
session_start();
include('connect.php');

// Check if the user is logged in and has the police role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'police') {
    header("Location: login.php");
    exit();
}

// Handle Approval Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["approve_id"])) {
    $report_id = $_POST["approve_id"];
    $stmt = $conn->prepare("UPDATE lost_ids SET status = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Report Approved Successfully!'); window.location='police_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to approve report. Please try again.');</script>";
    }
    $stmt->close();
}

// Handle Rejection Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reject_id"])) {
    $report_id = $_POST["reject_id"];
    $stmt = $conn->prepare("UPDATE lost_ids SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Report Rejected Successfully!'); window.location='police_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to reject report. Please try again.');</script>";
    }
    $stmt->close();
}

// Fetch reports that are 'Pending'
$sql = "SELECT * FROM lost_ids WHERE status = 'Pending'";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Dashboard - Lost & Found</title>
    <style>
       body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f7fc;
    color: #333;
}
.dashboard-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
}
header {
    background-color: #2b3e55;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #ffcc00;
    position: sticky;
    top: 0;
    z-index: 1000;
}
nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-weight: bold;
    transition: color 0.3s ease;
}
nav a:hover {
    color: #ffcc00;
}
main {
    padding: 30px;
    flex-grow: 1;
    overflow-y: auto;
}
.reports-section {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.report-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-left: 5px solid #2b3e55;
    transition: transform 0.3s ease;
}
.report-card:hover {
    transform: scale(1.02);
}
.report-card p {
    margin: 10px 0;
    color: #555;
}
button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    
}
.approve-btn {
    background-color: #28a745;
    color: white;
    margin-top:20px;
}
.approve-btn:hover {
    background-color: #218838;
}
.reject-btn {
    background-color: #dc3545;
    color: white;
    margin-left: 100px;
    margin-top: -35px;
    width: 100px;
}
.reject-btn:hover {
    background-color: #c82333;
}
footer {
    background-color: #2b3e55;
    color: white;
    padding: 5px;
    text-align: center;
}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <div class="logo">
                <img src="zetechlogo.jpg" alt="Zetech University Logo" height="50">
            </div>
            <nav>
                <a href="police_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h1>Police Dashboard</h1>
            <section class="reports-section">
                <h2>Pending Reports</h2>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="report-card">
                            <p><strong>Report ID:</strong> <?= $row['id'] ?></p>
                            <p><strong>Student Name:</strong> <?= $row['student_name'] ?></p>
                            <p><strong>Status:</strong> <?= $row['status'] ?></p>
                            <p><strong>Date Reported:</strong> <?= $row['date_reported'] ?></p>
                            <?php
                            if (!empty($row['police_report'])) {
                                $filePath = "uploads/" . $row['police_report'];
                                if (file_exists($filePath)) {
                                    echo "<a href='$filePath' target='_blank'>View Police Report</a>";
                                } else {
                                    echo "<p style='color: red;'>Police Report File Not Found.</p>";
                                }
                            } else {
                                echo "<p style='color: red;'>No Police Report Uploaded.</p>";
                            }
                            ?>
                            <form method="post" style="display: flex; gap: 10px;">
                                <input type="hidden" name="approve_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="approve-btn">Approve</button>
                            </form>
                            <form method="post" style="display: flex; gap: 10px;">
                                <input type="hidden" name="reject_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="reject-btn">Reject</button>
                            </form>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No pending reports found.</p>";
                }
                ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 Zetech University Lost & Found System | All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
