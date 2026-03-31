<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - StudyMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.6s ease;
        }
        .error-card {
            background: white;
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .error-icon {
            font-size: 80px;
            color: #f59e0b;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 10px;
        }
        p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-hourglass-end"></i>
            </div>
            <h1>Session Expired</h1>
            <p>Your session has timed out due to inactivity.<br>Please log in again to continue.</p>
            <a href="/login" class="btn">
                <i class="fas fa-sign-in-alt"></i> Go to Login
            </a>
        </div>
    </div>
</body>
</html>