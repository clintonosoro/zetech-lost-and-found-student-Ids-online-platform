<?php
session_start();
include 'connect.php'; // Make sure to include your database connection

// Common admin password hash (change this to your own secure hash)
$common_admin_password_hash = '$2y$10$0Yloy5ygH8MLxOhciAj0ZePCxYA1Gt5WPaGekGbZjYZjahAlGaPkC'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $admin_password = isset($_POST['admin_password']) ? trim($_POST['admin_password']) : '';

    // If role is 'admin', validate the admin password
    $admin_password = isset($_POST['admin_password']) ? trim($_POST['admin_password']) : '';

    // If role is 'admin', validate the admin password
    if ($role === 'admin') {
        // Check if admin password matches the stored hash
        if (empty($admin_password) || !password_verify($admin_password, $common_admin_password_hash)) {
            die("<p style='color:red;'>Invalid admin password. You cannot register as an admin.</p>");
        }
    }
    

    // Hash the user password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email already exists. Please use a different email.";
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        // Set the status to 'active' for the new user
        $status = 'active';
        $created_at = date("Y-m-d H:i:s");

        $stmt->bind_param("ssssss", $name, $email, $hashed_password, $role, $status, $created_at);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $name;

            // Redirect based on role
            switch ($role) {
                case 'student':
                    header("Location: student_dashboard.php");
                    break;
                case 'police':
                    header("Location: police_dashboard.php");
                    break;
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                default:
                    header("Location: login.php");
                    break;
            }
            exit();
        } else {
            $error = "Error registering user.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zetech Lost & Found</title>
    <style>
       /* General Page Styles */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    height: 100vh;
    flex-direction: row;
}

/* Left Side (Form) */
.register-container {
    width: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #fff;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
    flex-direction: column;
    padding: 20px;
}

.logo {
    width: 80px;
    margin-bottom: 20px;
}

.register-box {
    width: 100%;
    max-width: 400px;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.register-box h2 {
    color: #151B54;
    margin-bottom: 20px;
    font-size: 24px;
}

/* Input Group */
.input-group {
    width: 100%;
    margin-bottom: 15px;
}

.input-group input, .input-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    background: #fff;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.input-group input:focus, .input-group select:focus {
    border-color: #151B54;
    box-shadow: 0 0 8px rgba(21, 27, 84, 0.2);
}

/* Button */
.register-btn {
    width: 100%;
    background: #151B54;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s;
}

.register-btn:hover {
    background: #0e1442;
    transform: scale(1.03);
}

/* Links and Errors */
.login-link {
    display: block;
    margin-top: 12px;
    text-decoration: none;
    color: #151B54;
    font-size: 14px;
    text-align: center;
}

.login-link:hover {
    text-decoration: underline;
}

.error-message {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Right Side (Image) */
.background-container {
    width: 50%;
    background: url('zetech.jpg') no-repeat center center;
    background-size: cover;
}

/* Responsive Styling */
@media (max-width: 768px) {
    body {
        flex-direction: column;
        height: auto;
        background: linear-gradient(to bottom right, #e8ebf8, #f5f7fa);
    }

    .register-container, .background-container {
        width: 100%;
        height: auto;
    }

    .background-container {
        display: none; /* Optional: hide image on smaller screens */
    }

    .register-box {
        width: 90%;
        padding: 20px;
        box-shadow: none;
        background: transparent;
    }

    .register-box h2 {
        font-size: 22px;
    }

    .register-btn {
        font-size: 16px;
        padding: 10px;
    }

    .input-group input, .input-group select {
        font-size: 14px;
        padding: 10px;
    }
}

    </style>
</head>
<body>

    <!-- Left Side: Registration Form -->
    <div class="register-container">
        <img src="zetechlogo.jpg" alt="Zetech University Logo" class="logo">
        <div class="register-box">
            <h2>Register</h2>
            
            <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>

            <form action="register.php" method="POST">
    <div class="input-group">
        <input type="text" name="name" placeholder="Full Name" required maxlength="25">
    </div>
    <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
    </div>
    <div class="input-group" id="adminKeyGroup" style="display:none;">
    <input type="password" name="admin_password" placeholder="Enter Admin Password">
</div>

    <div class="input-group">
        <select name="role" id="role" required onchange="toggleAdminKey()">
            <option value="" disabled selected>Select Role</option>
            <option value="student">Student</option>
            <option value="police">Police</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <div class="input-group" id="adminKeyGroup" style="display:none;">
        <input type="password" name="admin_key" placeholder="Enter Admin Key">
    </div>
    <button type="submit" class="register-btn">Register</button>
    <a href="login.php" class="login-link">Already have an account? Login</a>
</form>

<script>
    function toggleAdminKey() {
        const roleSelect = document.getElementById('role');
        const adminKeyGroup = document.getElementById('adminKeyGroup');
        adminKeyGroup.style.display = roleSelect.value === 'admin' ? 'block' : 'none';
    }
</script>

        </div>
    </div>

    <!-- Right Side: Background Image -->
    <div class="background-container"></div>

</body>
</html>
