<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include 'connect.php';
// Fetch all lost ID reports with status 'Pending'
$sql = "SELECT * FROM lost_ids WHERE status = 'Pending'";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    $report_id = $_POST['report_id'];

    // Update the status to 'Approved'
    $stmt = $conn->prepare("UPDATE lost_ids SET status = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();

    header("Location: police_dashboard.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch notifications
$notifications = [];
$sql = "SELECT message, created_at FROM notifications WHERE user_role = 'student' ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Check if student has reported a lost ID
$report_exists = false;
$police_report_uploaded = false;
$latest_report = null;

$check_report_sql = "SELECT * FROM lost_ids WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($check_report_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$check_report_result = $stmt->get_result();

if ($check_report_result->num_rows > 0) {
    $latest_report = $check_report_result->fetch_assoc();
    $report_exists = true;
    if (!empty($latest_report['police_report'])) {
        $police_report_uploaded = true;
    }
}

// Handle lost ID report submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_id'])) {
    $student_id = htmlspecialchars($_POST['student_id']);
    $id_number = htmlspecialchars($_POST['id_number']);
    $date_lost = $_POST['date_lost'];

    // Check if police report is uploaded (optional)
    if (!empty($_FILES['police_report']['name'])) {
        $police_report = time() . "_" . basename($_FILES['police_report']['name']); // Unique filename
        $target_dir = "uploads/";

        // Create the directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // 0777 makes it readable, writable, and executable
        }

        $target_file = $target_dir . $police_report;

        // Validate file type (optional)
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'pdf');
        $file_extension = pathinfo($police_report, PATHINFO_EXTENSION);
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('❌ Invalid file type. Only JPG, PNG, and PDF are allowed.');</script>";
            exit();
        }

        // File size validation (optional)
        if ($_FILES['police_report']['size'] > 5000000) { // 5MB limit
            echo "<script>alert('❌ File size too large. Maximum allowed size is 5MB.');</script>";
            exit();
        }

        // Move the uploaded file to target directory
        if (move_uploaded_file($_FILES['police_report']['tmp_name'], $target_file)) {
            // Check if the student already reported a lost ID
            $check_existing_sql = "SELECT id FROM lost_ids WHERE user_id = ? LIMIT 1";
            $stmt = $conn->prepare($check_existing_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // If report exists, update it
                $update_sql = "UPDATE lost_ids SET student_name=?, student_id=?, id_number=?, date_lost=?, police_report=?, reported_at=NOW() WHERE user_id=?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("sssssi", $name, $student_id, $id_number, $date_lost, $police_report, $user_id);
            } else {
                // If no report exists, insert a new one
                $insert_sql = "INSERT INTO lost_ids (user_id, student_name, student_id, id_number, date_lost, police_report, status, reported_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("isssss", $user_id, $name, $student_id, $id_number, $date_lost, $police_report);
            }

            if ($stmt->execute()) {
                echo "<script>alert('✅ Lost ID reported successfully! You can now download your waiting card.'); window.location.href='student_dashboard.php';</script>";
                exit();
            } else {
                echo "<script>alert('❌ Error reporting lost ID. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('❌ Error uploading file. Please try again.');</script>";
        }
    } else {
        // If no police report is uploaded, still allow reporting without it
        // Check if the student already reported a lost ID
        $check_existing_sql = "SELECT id FROM lost_ids WHERE user_id = ? LIMIT 1";
        $stmt = $conn->prepare($check_existing_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If report exists, update it
            $update_sql = "UPDATE lost_ids SET student_name=?, student_id=?, id_number=?, date_lost=?, reported_at=NOW() WHERE user_id=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssi", $name, $student_id, $id_number, $date_lost, $user_id);
        } else {
            // If no report exists, insert a new one
            $insert_sql = "INSERT INTO lost_ids (user_id, student_name, student_id, id_number, date_lost, status, reported_at) 
                           VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("issss", $user_id, $name, $student_id, $id_number, $date_lost);
        }

        if ($stmt->execute()) {
            echo "<script>alert('✅ Lost ID reported successfully! You can now download your waiting card.'); window.location.href='student_dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('❌ Error reporting lost ID. Please try again.');</script>";
        }
    }
}
// Fetch announcements from the database
$query = "SELECT * FROM announcements ORDER BY created_at DESC"; // Fetch latest first
$result = mysqli_query($conn, $query);

$notifications = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            background-image: url('student.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    height: 100vh;
    margin: 0;
            
        }

        .navbar {
            width: 100%;
            background: #002147;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
           
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar ul li {
            padding: 10px 15px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: color 0.3s;
        }

        .navbar ul li a:hover {
            color: #ffcc00;
        }

        .main-content {
            margin: 20px auto;
            width: 90%;
            max-width: 900px;
            bottom: 4cm;
        }

        .header {
            background: #002147;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.05);
            display: none;
        }

        .section h3 {
            color: #002147;
            border-bottom: 2px solid #002147;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .button {
            padding: 12px 24px;
            background: #002147;
            color: white;
            border: none;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            transition: background 0.3s;
            cursor: pointer;
        }

        .button:hover {
            background: #001530;
        }

        input, button {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        button {
            background: #28a745;
            color: white;
        }

        button:hover {
            background: #218838;
        }

        .notification-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .notification-card {
            background-color: #e0e0e0;
            padding: 15px;
            border-left: 5px solid #002147;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .notification-card:hover {
            background-color: #d0d0d0;
        }

        .notification-header {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #002147;
        }

        .notification-time {
            font-size: 0.85rem;
            color: #666;
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }

            .navbar ul {
                flex-direction: column;
            }

            .navbar ul li {
                padding: 5px 0;
            }
        }

    </style>
    <script>
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            section.style.display = (section.style.display === "block") ? "none" : "block";
        }
    </script>
</head>
<body>

    <div class="navbar">
        <h2>Student Panel</h2>
        <ul>
            <li><a href="caution.php">Caution</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSection('reportLost')">Report Lost ID</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSection('checkStatus')">Check ID Status</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSection('waitingCard')">Download Waiting Card</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">Welcome, <?php echo htmlspecialchars($name); ?> (Student)</div>

        <!-- Notifications -->
<button id="toggleNotifications">Show Notifications</button>

<div id="notificationsBox" style="display: none; background: #f8d7da; padding: 10px; border: 1px solid red; margin-top: 10px;">
    <h3>Notifications</h3>
    
    <?php if (!empty($notifications)) { ?>
        <?php foreach ($notifications as $notification) { ?>
            <div class="notification">
                <strong><?php echo htmlspecialchars($notification['title']); ?></strong>
                <p><?php echo htmlspecialchars($notification['message']); ?></p> 
                <small><?php echo htmlspecialchars($notification['created_at']); ?></small>
            </div>
            <hr>
        <?php } ?>
    <?php } else { ?>
        <p>No notifications at the moment.</p>
    <?php } ?>
    
    <button id="closeNotifications">Close</button>
</div>



        <!-- Report Lost ID -->
        <div class="section" id="reportLost">
            <h3>Report Lost ID</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="text" name="student_id" placeholder="Student ID" required>
                <input type="text" name="id_number" placeholder="ID Number" required>
                <input type="date" name="date_lost" required>
                <input type="file" name="police_report" required>
                <button type="submit" name="report_id">Submit Report</button>
            </form>
        </div>

        <!-- Check ID Status -->
        <div class="section" id="checkStatus">
            <h3>Check ID Status</h3>
            <a href="check_id_status.php" class="button">Check Status</a>
        </div>

        <!-- Download Waiting Card -->
        <div class="section" id="waitingCard">
            <h3>Download Waiting Card</h3>
            <?php if ($report_exists && $police_report_uploaded) { ?>
                <a href="generate_waiting_card.php" class="button">Download Waiting Card</a>
            <?php } else { ?>
                <p>⚠️ You need to report a lost ID and upload a police report first.</p>
            <?php } ?>
        </div>
    </div>
    
    <?php
// Include database connection
include 'connect.php';

// Fetch notifications from the database
$query = "SELECT message, created_at FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Store notifications in an array
$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}
?>

    <!-- Notifications Section -->


<div id="notificationsBox" style="display: none; background: #f8d7da; padding: 10px; border: 1px solid red; margin-top: 10px;">
    <h3>Admin Announcements</h3>
    
    <?php if (!empty($notifications)) { ?>
        <?php foreach ($notifications as $notification) { ?>
            <div class="notification">
                <strong>📢 Announcement:</strong> <?php echo htmlspecialchars($notification['message']); ?> 
                <br><small><?php echo htmlspecialchars($notification['created_at']); ?></small>
            </div>
            <hr>
        <?php } ?>
    <?php } else { ?>
        <p>No announcements at the moment.</p>
    <?php } ?>
    
    <button id="closeNotifications">Close</button>
</div>

<script>
    // Toggle notifications visibility
    document.getElementById("toggleNotifications").addEventListener("click", function() {
        var box = document.getElementById("notificationsBox");
        if (box.style.display === "none") {
            box.style.display = "block";
        } else {
            box.style.display = "none";
        }
    });

    // Close notifications
    document.getElementById("closeNotifications").addEventListener("click", function() {
        document.getElementById("notificationsBox").style.display = "none";
    });
</script>




</body>
</html>