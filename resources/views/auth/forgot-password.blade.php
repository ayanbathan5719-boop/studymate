<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - StudyMate</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
            opacity: 0.3;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Floating Shapes */
        .floating-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Main Container */
        .forgot-container {
            width: 100%;
            max-width: 1100px;
            background: white;
            border-radius: 32px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
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

        /* Left Section - Welcome Branding */
        .welcome-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 48px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            backdrop-filter: blur(10px);
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .welcome-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .stats-container {
            display: flex;
            gap: 30px;
            margin-bottom: 60px;
        }

        .stat-item {
            flex: 1;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        .quote-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 24px;
            margin-top: auto;
        }

        .quote-text {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
            font-style: italic;
        }

        .quote-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .author-info {
            flex: 1;
        }

        .author-name {
            font-weight: 600;
            font-size: 14px;
        }

        .author-title {
            font-size: 12px;
            opacity: 0.8;
        }

        /* Right Section - Reset Form */
        .form-section {
            flex: 1;
            padding: 48px;
            background: white;
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-title {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Alert Messages */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            color: #166534;
        }

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .alert-info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Info Box */
        .info-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 28px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            border: 1px solid #e2e8f0;
        }

        .info-box i {
            font-size: 24px;
            color: #f59e0b;
        }

        .info-box-content p {
            color: #334155;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 4px;
        }

        .info-box-content small {
            color: #64748b;
            font-size: 11px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 28px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #334155;
            font-size: 14px;
        }

        .form-label i {
            margin-right: 8px;
            color: #f59e0b;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        /* Reset Button */
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
        }

        /* Back Link */
        .back-link {
            text-align: center;
        }

        .back-link a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .back-link a:hover {
            color: #f59e0b;
        }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .forgot-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .welcome-section {
                padding: 32px;
            }
            
            .welcome-title {
                font-size: 32px;
            }
            
            .form-section {
                padding: 32px;
            }
            
            .stats-container {
                margin-bottom: 32px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .welcome-title {
                font-size: 28px;
            }
            
            .stats-container {
                flex-direction: column;
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Shapes -->
    <div class="floating-shape" style="width: 100px; height: 100px; top: 10%; left: 5%;"></div>
    <div class="floating-shape" style="width: 150px; height: 150px; bottom: 10%; right: 5%; animation-delay: -2s;"></div>
    <div class="floating-shape" style="width: 70px; height: 70px; top: 50%; left: 80%; animation-delay: -4s;"></div>

    <div class="forgot-container">
        <!-- Left Welcome Section -->
        <div class="welcome-section">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="logo-text">StudyMate</div>
            </div>
            
            <div class="welcome-content">
                <h1 class="welcome-title">Trouble Logging In?</h1>
                <p class="welcome-subtitle">Don't worry, we'll help you get back to learning</p>
                
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">Fast</div>
                        <div class="stat-label">Recovery</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">Secure</div>
                        <div class="stat-label">Process</div>
                    </div>
                </div>
            </div>
            
            <div class="quote-card">
                <div class="quote-text">
                    "Education is the most powerful weapon which you can use to change the world. Let's get you back on track!"
                </div>
                <div class="quote-author">
                    <div class="author-avatar">NM</div>
                    <div class="author-info">
                        <div class="author-name">Nelson Mandela</div>
                        <div class="author-title">Education Advocate</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Reset Form Section -->
        <div class="form-section">
            <div class="form-header">
                <h2 class="form-title">Reset Password</h2>
                <p class="form-subtitle">Enter your email and we'll send you a reset link</p>
            </div>
            
            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            
            <div class="info-box">
                <i class="fas fa-envelope"></i>
                <div class="info-box-content">
                    <p>We'll send a password reset link to your email address.</p>
                    <small>The link will expire in 60 minutes. Check your spam folder if you don't see the email.</small>
                </div>
            </div>
            
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <div class="input-wrapper">
                        <input type="email" 
                               name="email" 
                               class="form-input @error('email') is-invalid @enderror" 
                               placeholder="Enter your registered email"
                               value="{{ old('email') }}"
                               required 
                               autofocus>
                    </div>
                </div>
                
                <button type="submit" class="btn-reset">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            
            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
            
            <div class="footer-text">
                <i class="fas fa-copyright"></i> {{ date('Y') }} StudyMate. All rights reserved.
            </div>
        </div>
    </div>
    
    <script>
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
        
        // Focus on email field on page load
        document.querySelector('input[name="email"]').focus();
    </script>
</body>
</html>