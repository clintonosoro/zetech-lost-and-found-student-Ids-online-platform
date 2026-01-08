<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into announcements table
    $insert_announcement = mysqli_query($conn, "INSERT INTO announcements (title, message, created_at) VALUES ('$title', '$message', NOW())");

    // Insert into notifications table (Clearly identifying the message)
    $notification_message = "New Announcement: $title. Message: $message";
    $insert_notification = mysqli_query($conn, "INSERT INTO notifications (user_role, message, created_at) VALUES ('student', '$notification_message', NOW())");

    if ($insert_announcement && $insert_notification) {
        echo "<script>alert('Announcement posted successfully!'); window.location.href='admin_dashboard.php?action=post';</script>";
    } else {
        echo "<script>alert('Error posting announcement.');</script>";
    }
}
?>

<h3>Post Announcement</h3>
<form method="post">
    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Message</label>
        <textarea name="message" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Post</button>
</form>
<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into announcements table
    $insert_announcement = mysqli_query($conn, "INSERT INTO announcements (title, message, created_at) VALUES ('$title', '$message', NOW())");

    // Insert into notifications table
    $notification_message = "New Announcement: $title. Message: $message";
    $insert_notification = mysqli_query($conn, "INSERT INTO notifications (user_role, message, created_at) VALUES ('student', '$notification_message', NOW())");

    if ($insert_announcement && $insert_notification) {
        echo "<script>alert('Announcement posted successfully!'); window.location.href='admin_dashboard.php?action=post';</script>";
    } else {
        echo "<script>alert('Error posting announcement.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h3 {
            color: #0044cc;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #0044cc;
            border: none;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0033a0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>📢 Post Announcement</h3>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter announcement title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Announcement</button>
        </form>
    </div>
</body>
</html>
