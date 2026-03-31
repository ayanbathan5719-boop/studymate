<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - StudyMate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
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
            text-align: center;
        }
        
        .error-card {
            background: white;
            border-radius: 24px;
            padding: 48px 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: #fef2f2;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        
        .error-icon i {
            font-size: 40px;
            color: #ef4444;
        }
        
        .error-code {
            font-size: 3rem;
            font-weight: 700;
            color: #f59e0b;
            margin-bottom: 12px;
        }
        
        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }
        
        p {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }
        
        @media (max-width: 480px) {
            .error-card {
                padding: 32px 24px;
            }
            .error-code {
                font-size: 2rem;
            }
            h1 {
                font-size: 1.4rem;
            }
            .button-group {
                flex-direction: column;
            }
            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-compass"></i>
            </div>
            <div class="error-code">404</div>
            <h1>Page Not Found</h1>
            <p>Sorry, we couldn't find the page you're looking for. It might have been moved or doesn't exist.</p>
            <div class="button-group">
                <a href="/dashboard" class="btn-primary">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
                <a href="javascript:history.back()" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>
</body>
</html>