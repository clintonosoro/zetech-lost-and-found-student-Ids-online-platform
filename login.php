<?php
session_start();
include 'connect.php';

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Verify database connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Prepare SQL Query
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if user is disabled
        if ($user['status'] == 'disabled') {
            $error = "You are disabled. Contact the admin.";
        } 
        // Verify password if user is active
        elseif (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Security: Prevent session fixation attacks
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            switch ($user['role']) {
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
                    exit();
            }
            exit();
        } else {
            $error = "Incorrect username or password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zetech Lost & Found</title>
    <style>
        /* General Page Styles */
          /* General Styles */
          * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            flex-wrap: wrap;
        }

        /* Left Side - Login Form */
        .login-container {
            width: 50%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-box {
            width: 350px;
            padding: 30px;
            text-align: center;
        }

        .logo {
            width: 120px;
            margin-bottom: 15px;
        }

        .login-box h2 {
            color: #151B54;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .input-group {
            width: 100%;
            margin-bottom: 15px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: #151B54;
            box-shadow: 0 0 5px rgba(21, 27, 84, 0.5);
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            background: #151B54;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s;
        }

        .login-btn:hover {
            background: #0e1442;
            transform: scale(1.02);
        }

        .forgot-password,
        .register-link {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #151B54;
            font-size: 14px;
        }

        .forgot-password:hover,
        .register-link:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Right Side - Background */
        .background-container {
            width: 50%;
            min-height: 100vh;
            background: url('zetech.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.6);
        }

        /* Responsive Layout */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
            }

            .login-container, .background-container {
                width: 100%;
                min-height: 50vh;
            }

            .login-box {
                width: 80%;
            }

            .login-box h2 {
                font-size: 20px;
            }

            .login-btn {
                font-size: 16px;
            }

            .input-group input {
                font-size: 14px;
                padding: 10px;
            }
        }
        
    </style>
    <script>
        function togglePassword(id) {
            var input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="zetechlogo.jpg" alt="Zetech Logo" class="logo">
            <h2>User Login</h2>
            <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required maxlength="12">
                    <span class="toggle-password" onclick="togglePassword('password')"></span>
                </div>
                <button type="submit" class="login-btn">Login</button>
                
                <a href="register.php" class="register-link"> Don't have an account? Register</a>
            </form>
        </div>
    </div>
    <div class="background-container"></div>
</body>
</html>
