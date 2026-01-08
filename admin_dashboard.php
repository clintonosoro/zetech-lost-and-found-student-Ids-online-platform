<?php
session_start();
include('connect.php');

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetching total user count
$total_users_query = mysqli_query($conn, "SELECT COUNT(id) AS total_users FROM users");
$total_users = mysqli_fetch_assoc($total_users_query)['total_users'];

// Fetching active users count
$active_users_query = mysqli_query($conn, "SELECT COUNT(id) AS active_users FROM users WHERE status = 'active'");
$active_users = mysqli_fetch_assoc($active_users_query)['active_users'];

// Fetching disabled users count
$disabled_users_query = mysqli_query($conn, "SELECT COUNT(id) AS disabled_users FROM users WHERE status = 'disabled'");
$disabled_users = mysqli_fetch_assoc($disabled_users_query)['disabled_users'];

// Fetching notifications
$notifications_query = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");

// Fetching lost ID reports
$lost_ids_query = mysqli_query($conn, "SELECT * FROM lost_ids ORDER BY date_reported DESC");

// Handle enable/disable user request
if (isset($_GET['id']) && isset($_GET['action'])) {
    $user_id_to_update = intval($_GET['id']);
    $new_status = ($_GET['action'] === 'disable') ? 'disabled' : 'active';

    $query = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_status, $user_id_to_update);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User successfully " . ($new_status === 'disabled' ? "disabled" : "enabled") . "!";
    } else {
        $_SESSION['error'] = "Error updating user status.";
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Handle posting announcements
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Check if the same announcement already exists
    $checkQuery = "SELECT id FROM announcements WHERE title = ? AND message = ?";
    $stmt = $conn->prepare($checkQuery);
    
    if ($stmt) {
        $stmt->bind_param("ss", $title, $message);
        $stmt->execute();
        $stmt->store_result(); // Store the result to check rows

        if ($stmt->num_rows == 0) { // No duplicate found, insert new announcement
            $insertQuery = "INSERT INTO announcements (title, message, created_at) VALUES (?, ?, NOW())";
            $insertStmt = $conn->prepare($insertQuery);
            
            if ($insertStmt) {
                $insertStmt->bind_param("ss", $title, $message);
                if ($insertStmt->execute()) {
                    echo "<script>alert('Announcement posted successfully!'); window.location.href='admin_dashboard.php';</script>";
                    exit(); // Stop execution after redirect
                } else {
                    echo "<script>alert('Error posting announcement!');</script>";
                }
                $insertStmt->close();
            }
        } else {
            echo "<script>alert('Duplicate announcement detected!'); window.location.href='admin_dashboard.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error: Unable to prepare statement!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zetech University</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <head>
    <script>
        if (window.location.search.length > 0) {
            history.replaceState(null, "", window.location.pathname);
        }
    </script>
</head>

    <style>
        /* Navbar */
.navbar {
    background-color: #002147;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100%;
    background-color: #002147;
    padding: 20px 10px;
    color: white;
}
.sidebar h3 {
    color: white;
    margin-bottom: 20px;
}
.sidebar a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px 0;
}
.sidebar a:hover {
    background-color: #004080;
    color: white;
    border-radius: 4px;
}

/* Content */
.content {
    margin-left: 270px;
    padding: 20px;
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Widgets */
.widget {
    background-color: #f1f1f1;
    border: 1px solid #002147;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 20px;
}
.widget .title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #002147;
}
.widget .count {
    font-size: 2rem;
    font-weight: bold;
    color: #002147;
}

/* Table Styling */
.table th, .table td {
    text-align: center;
}

/* Footer */
footer {
    text-align: center;
    padding: 10px;
    background-color: #002147;
    color: white;
    margin-top: 20px;
}
 /* Center the form */
 .post-notification-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .post-notification-form {
        width: 40%;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        text-align: center;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .btn-primary {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Zetech University</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar">

    <h3>Admin Panel</h3>
    <a href="javascript:void(0)" onclick="toggleSection('manageUsers')">Manage Users</a>
    <a href="javascript:void(0)" onclick="toggleSection('viewReports')">View Reports</a>
    <a href="javascript:void(0)" onclick="toggleSection('notifications')">Notifications</a>
    <a href="javascript:void(0)" onclick="toggleSection('postannouncement')">Send Announcement</a>
    
</div>
    <div class="content">
        <div class="container">
            <h2>Welcome, <?php echo $name; ?> 👋</h2>
            <p>Manage users and reports .</p>

            <!-- Success/Error Messages -->
            <?php
            if (isset($_GET['status']) && $_GET['status'] == 'enabled') {
                echo "<div class='alert alert-success'>User enabled successfully!</div>";
            }
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 'enable_failed') {
                    echo "<div class='alert alert-danger'>Error enabling user. Please try again.</div>";
                } elseif ($_GET['error'] == 'invalid_request') {
                    echo "<div class='alert alert-danger'>Invalid request. No user ID provided.</div>";
                }
            }
            ?>
            <div class="toggle-section" id="viewReports">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Student ID</th>
                        <th>National ID</th>
                        <th>Date Reported</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            <tbody>
            <?php while ($report = mysqli_fetch_assoc($lost_ids_query)): ?>
                <tr>
                    <td><?php echo $report['id']; ?></td>
                    <td><?php echo $report['student_name']; ?></td>
                    <td><?php echo $report['student_id']; ?></td>
                    <td><?php echo $report['id_number']; ?></td>
                    <td><?php echo $report['date_reported']; ?></td>
                    <td><?php echo $report['status']; ?></td>
                    <td>
                        <a href="view_report_details.php?id=<?php echo $report['id']; ?>">View Details</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        </table>
            </div>
         <!-- Dashboard Widgets -->
         <div class="row">
                <div class="col-md-4">
                    <div class="widget">
                        <div class="title">Total Users</div>
                        <div class="count"><?php echo $total_users; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widget">
                        <div class="title">Active Users</div>
                        <div class="count"><?php echo $active_users; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widget">
                        <div class="title">Disabled Users</div>
                        <div class="count"><?php echo $disabled_users; ?></div>
                    </div>
                </div>
            </div>
            <div class="text-end">
    <a href="export_reports.php" class="btn btn-primary">Export Reports to Excel</a>
</div>

            <!-- Manage Users Section -->
            <div id="manageUsers" class="toggle-section">
            <h3>Manage Users</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all users
                    $users_query = mysqli_query($conn, "SELECT * FROM users");
                    while ($user = mysqli_fetch_assoc($users_query)) {
                        echo "<tr>
                                <td>{$user['id']}</td>
                                <td>{$user['name']}</td>
                                <td>{$user['email']}</td>
                                <td>{$user['role']}</td>
                                <td>{$user['status']}</td>
                                <td>";
                        if ($user['status'] == 'disabled') {
                            // Show "Enable" button if user is disabled
                            echo "<a href='enable_user.php?user_id={$user['id']}' class='btn btn-success'>Enable</a>";
                        } else {
                            // Show "Disable" button if user is active
                            echo "<a href='disable_user.php?user_id={$user['id']}' class='btn btn-danger'>Disable</a>";
                        }
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            </div>

            <!-- Dynamic Page Loading -->
            <?php
            if (isset($_GET['action'])) {
                if ($_GET['action'] == 'users') {
                    include 'admin_manage_users.php';
                } elseif ($_GET['action'] == 'disable_user') {
                    include 'admin_disable_user.php';
                } elseif ($_GET['action'] == 'reports') {
                    include 'admin_view_reports.php';
                } elseif ($_GET['action'] == 'post') {
                    include 'admin_post.php';
                }
            }
            ?>
          <?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Start the session only if it's not already started
}

include 'connect.php';  // Path to your database connection file

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized access.");
}

// Define the extractReportId function
function extractReportId($message) {
    // Assuming the message contains something like "ID number: 123" or "lost ID report: 123"
    if (preg_match('/ID number:\s*(\d+)/', $message, $matches)) {
        return $matches[1];  // Return the extracted report ID
    }
    return null;  // Return null if no ID was found
}
?>
<div id="notifications" class="content-section">
<h3>Admin Notifications</h3>

<?php
include 'connect.php'; // Ensure database connection

// Handle form submission for posting announcements
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO announcements (title, message, created_at) VALUES ('$title', '$message', NOW())";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Announcement posted successfully!');</script>";
    } else {
        echo "<script>alert('Error posting announcement: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch lost ID reports
$lost_id_reports_query = mysqli_query($conn, "SELECT * FROM lost_ids ORDER BY date_reported DESC LIMIT 5");
?>

<div class="lost-id-reports">
    <?php if (mysqli_num_rows($lost_id_reports_query) > 0): ?>
        <?php while ($report = mysqli_fetch_assoc($lost_id_reports_query)): ?>
            <div class="alert alert-info" onclick="toggleSection('report_<?php echo $report['id']; ?>')" style="cursor: pointer;">
                <strong>Lost ID Report - Click to View</strong>
                <p><strong>Student ID:</strong> <?php echo $report['student_id']; ?></p>
                <div id="report_<?php echo $report['id']; ?>" style="display: none; margin-top: 10px;">
                    <p><strong>Student Name:</strong> <?php echo $report['student_name']; ?></p>
                    <p><strong>ID Number:</strong> <?php echo $report['id_number']; ?></p>
                    <p><strong>Status:</strong> <?php echo $report['status']; ?></p>
                    <p><strong>Date Reported:</strong> <?php echo $report['date_reported']; ?></p>
                    
                    <?php if (!empty($report['police_report'])): ?>
                        <?php $filePath = "uploads/" . $report['police_report']; ?>
                        <?php if (file_exists($filePath)): ?>
                            <a href="<?php echo $filePath; ?>" target="_blank">View Police Report</a>
                        <?php else: ?>
                            <p style='color: red;'>Police Report File Not Found.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style='color: red;'>No Police Report Uploaded.</p>
                    <?php endif; ?>

                    <small class="text-muted"><?php echo $report['reported_at']; ?></small>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning">
            No lost ID reports available at the moment.
        </div>
    <?php endif; ?>
</div>

<div class="toggle-section" id="postannouncement">
    <div class="post-notification-container">
        <form method="POST" action="" class="post-notification-form">
            <h2>Post Announcement</h2>
            <input type="text" name="title" class="form-control" placeholder="Enter Title" required>
            <textarea name="message" class="form-control" rows="4" placeholder="Enter Message" required></textarea>
            <button type="submit" class="btn-primary">Send Announcement</button>
        </form>
    </div>
</div>

<script>
    let activeSection = null; // No section visible at the start

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);

        // If clicking the same section, hide it
        if (activeSection === section) {
            section.style.display = "none";
            activeSection = null;
        } else {
            // Hide previously active section
            if (activeSection) {
                activeSection.style.display = "none";
            }
            // Show the selected section
            section.style.display = "block";
            activeSection = section;
        }
    }
</script>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
