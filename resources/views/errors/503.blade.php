<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Maintenance Mode | StudyMate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
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
            color: #4a5568;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 3px 3px 0 rgba(74, 85, 104, 0.2);
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
        
        .maintenance-message {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .maintenance-message h3 {
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .maintenance-message ul {
            margin-left: 30px;
            color: #718096;
        }
        
        .maintenance-message li {
            margin-bottom: 5px;
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
        
        .refresh-indicator {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 14px;
        }
        
        .refresh-indicator button {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
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
        <div class="error-code">503</div>
        <h1>Under Maintenance</h1>
        <p>We're currently performing scheduled maintenance to improve your experience.</p>
        
        <div class="maintenance-message">
            <h3>⏱️ Expected completion time</h3>
            <p style="margin-bottom: 10px;">Approximately <strong>30 minutes</strong></p>
            
            <h3>🛠️ What we're working on</h3>
            <ul>
                <li>System performance improvements</li>
                <li>Database optimization</li>
                <li>Security updates</li>
            </ul>
        </div>
        
        <div class="actions">
            <a href="/dashboard" class="btn btn-primary">Try Again</a>
            <a href="mailto:support@studymate.com" class="btn btn-secondary">Contact Support</a>
        </div>
        
        <div class="refresh-indicator">
            <span>Page will auto-refresh in <span id="countdown">30</span> seconds</span>
            <button onclick="location.reload()">Refresh now</button>
        </div>
    </div>

    <script>
        // Countdown timer for auto-refresh
        let seconds = 30;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                location.reload();
            }
        }, 1000);
    </script>
</body>
</html>