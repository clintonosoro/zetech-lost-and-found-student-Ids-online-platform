<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include 'connect.php';
$user_id = $_SESSION['user_id'];

// Fetch the status of the student's ID
$sql = "SELECT status FROM lost_ids WHERE user_id = ? ORDER BY reported_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $status = $result->fetch_assoc()['status'];
} else {
    $status = "No report found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check ID Status</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
        .navbar {
            width: 100%;
            background: #002147;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            margin-left: 10px;
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

        /* Main Content */
        .main-content {
            margin: 20px auto;
            width: 90%;
            max-width: 900px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #002147;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
        }

        /* Status Section */
        .status-section {
            background: #eaf3ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        .status-section h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #002147;
        }

        .status-message {
            background: #ffcc00;
            padding: 15px;
            font-size: 18px;
            color: #333;
            border-radius: 5px;
        }

        /* Button */
        .button {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            text-align: center;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .button:hover {
            background: #218838;
        }

        /* Responsive Design */
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

            .main-content {
                width: 100%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Student Panel</h2>
        <ul>
            <li><a href="student_dashboard.php">Back to Dashboard</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">Check Your ID Status</div>

        <!-- Status Section -->
        <div class="status-section">
            <h2>Your ID Status</h2>
            <div class="status-message">
                <?php echo htmlspecialchars($status); ?>
            </div>

            <a href="student_dashboard.php" class="button">Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
