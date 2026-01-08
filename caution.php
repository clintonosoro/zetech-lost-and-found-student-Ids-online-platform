<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Important Notice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color:#002147;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .notice-container {
            max-width: 800px;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h3 {
            font-size: 2rem;
            font-weight: bold;
        }
        p {
            font-size: 1.2rem;
            line-height: 1.6;
        }
        .btn-custom {
            font-size: 1.2rem;
            padding: 12px 24px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="notice-container">
        <h3 class="text-danger">⚠ IMPORTANT NOTICE ⚠</h3>
        <p class="mt-4">
            Please be advised that you should <strong>ONLY</strong> download the waiting card <strong>after</strong> receiving approval from the police. 
            If you attempt to download the waiting card without approval, you will <strong>NOT</strong> be granted access to the university premises. 
            Security officers will take appropriate action, and you may be required to return home immediately.
        </p>
        <p class="text-muted mt-3"><em>Ensure compliance with this directive to avoid any inconveniences.</em></p>
        <a href="student_dashboard.php" class="btn btn-primary btn-custom mt-4">Back to Dashboard</a>
    </div>
</body>
</html>
