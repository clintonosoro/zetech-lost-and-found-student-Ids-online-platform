<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_SESSION["user"];
    $student_id = htmlspecialchars($_POST["student_id"]);
    $id_number = htmlspecialchars($_POST["id_number"]);
    $date_lost = $_POST["date_lost"];
    $today = date('Y-m-d');

    // Check that the lost date is today only
    if ($date_lost !== $today) {
        echo "<script>alert('❌ Invalid date selected. Please select today\'s date only.'); window.location='student_dashboard.php';</script>";
        exit();
    }

    $date_reported = date('Y-m-d');
    $status = 'Pending';

    // Check if the student already reported a lost ID
    $check_stmt = $conn->prepare("SELECT * FROM lost_ids WHERE student_id = ? LIMIT 1");
    $check_stmt->bind_param("s", $student_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('❌ You have already reported a lost ID.'); window.location='student_dashboard.php';</script>";
        exit();
    }
    $check_stmt->close();

    // File upload (Police report)
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_ext = strtolower(pathinfo($_FILES["police_report"]["name"], PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_extensions)) {
        echo "<script>alert('❌ Invalid file type. Only PDF, JPG, JPEG, and PNG are allowed.'); window.location='student_dashboard.php';</script>";
        exit();
    }

    $unique_name = $student_id . "_" . time() . "." . $file_ext;
    $target_file = $target_dir . $unique_name;

    if (move_uploaded_file($_FILES["police_report"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO lost_ids (student_name, student_id, id_number, date_lost, date_reported, police_report, status, reported_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("sssssss", $student_name, $student_id, $id_number, $date_lost, $date_reported, $target_file, $status);
            if ($stmt->execute()) {
                echo "<script>alert('✅ Lost ID reported successfully! You can now download your waiting card.'); window.location='student_dashboard.php';</script>";
            } else {
                echo "<script>alert('❌ Error inserting data: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('❌ Database error: Unable to prepare statement.');</script>";
        }
    } else {
        echo "<script>alert('❌ File upload failed. Please try again.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Lost ID | Zetech</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            padding: 0;
            margin: 0;
        }

        .form-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #444;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            background-color: #0275d8;
            color: #fff;
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #025aa5;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Report Lost ID</h2>
    <form action="report_lost.php" method="post" enctype="multipart/form-data">
        <label for="student_id">Student ID:</label>
        <input type="text" name="student_id" id="student_id" required>

        <label for="id_number">ID Number:</label>
        <input type="text" name="id_number" id="id_number" required>

        <label for="date_lost">Date Lost:</label>
        <input type="date" name="date_lost" id="date_lost" value="<?= date('Y-m-d') ?>" required>

        <label for="police_report">Upload Police Report:</label>
        <input type="file" name="police_report" id="police_report" accept=".pdf,.jpg,.jpeg,.png" required>

        <button type="submit">Submit Report</button>
    </form>
</div>

</body>
</html>
