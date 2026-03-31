<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | StudyMate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            max-width: 600px;
            text-align: center;
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #ed8936;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 3px 3px 0 rgba(237, 137, 54, 0.2);
        }
        
        h1 {
            color: #2d3748;
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        p {
            color: #718096;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background: #edf2f7;
            transform: translateY(-2px);
        }
        
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        @media (max-width: 480px) {
            .error-code {
                font-size: 80px;
            }
            h1 {
                font-size: 24px;
            }
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">🔧</div>
        <div class="error-code">500</div>
        <h1>Server Error</h1>
        <p>Something went wrong on our end. Our team has been notified and we're working to fix it. Please try again later.</p>
        
        <div class="actions">
            <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
        </div>
    </div>
</body>
</html>