<?php 
session_start();
include 'connect.php';

// Your login processing logic here (if any)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zetech Lost & Found</title>
    <style>
        /* General Page Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        /* Left Side (Login Form) */
        .login-container {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
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

        .forgot-password {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #151B54;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Right Side (Background Image) */
        .background-container {
            width: 50%;
            background: url('zetech.jpg') no-repeat center center;
            background-size: cover;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .login-container, .background-container {
                width: 100%;
                height: 50vh;
            }

            .login-box {
                width: 80%;
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
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div>
                <button type="submit" class="login-btn">Login</button>
                <a href="reset_password.php" class="forgot-password">Forgot Password?</a>
            </form>
        </div>
    </div>
    <div class="background-container"></div>
</body>
</html>
