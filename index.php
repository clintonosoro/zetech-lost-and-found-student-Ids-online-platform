<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lost & Found Zetech</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }

    .navbar {
      background-color: #002147;
      padding: 15px 30px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      color: #fff;
      font-size: 1.2rem;
      font-weight: bold;
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
    }

    .nav-links a {
      color: #fff;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 6px;
      transition: background 0.3s ease;
    }

    .nav-links a:hover {
      background-color: #004080;
      color: #e0e0e0;
    }

    .header-image {
      background-image: url('index.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 100vh;
      width: 100%;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .content-overlay {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 40px;
      max-width: 900px;
      width: 90%;
      text-align: center;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    h2 {
      font-size: 2.5rem;
      color: #002147;
      font-weight: 700;
      margin-bottom: 20px;
    }

    p {
      font-size: 1.2rem;
      color: #333;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      .nav-links {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      .content-overlay {
        padding: 25px;
      }

      h2 {
        font-size: 2rem;
      }

      p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo">Lost and Found Student IDs Zetech</div>
    <ul class="nav-links">
      <li><a href="register.php">Get Started</a></li>
    </ul>
  </nav>

  <div class="header-image">
    <div class="content-overlay">
      <h2>Welcome to Zetech University Lost and Found Student IDs Platform</h2>
      <p id="animatedText"></p>
    </div>
  </div>

  <script>
    const text = "Efficiently report and retrieve lost student IDs with our secure platform. Whether you've lost your ID or found someone else's, we're here to help reconnect students with their valuable identification cards. Explore below to get started.";
    let index = 0;

    function typeWriter() {
      if (index < text.length) {
        document.getElementById("animatedText").innerHTML += text.charAt(index);
        index++;
        setTimeout(typeWriter, 50);
      } else {
        setTimeout(resetText, 2000);
      }
    }

    function resetText() {
      document.getElementById("animatedText").innerHTML = "";
      index = 0;
      typeWriter();
    }

    typeWriter();
  </script>
</body>
</html>
