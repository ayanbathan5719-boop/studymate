<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - StudyMate</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-container {
            width: 100%;
            max-width: 500px;
            animation: slideUp 0.6s ease;
        }

        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }

        .logo-img i {
            font-size: 40px;
            color: white;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .header-text {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-text h2 {
            color: #1e293b;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header-text p {
            color: #64748b;
            font-size: 14px;
        }

        .error-alert {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group label i {
            color: #667eea;
            margin-right: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .password-requirements {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }

        .password-requirements p {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-requirements p i {
            color: #f59e0b;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            color: #64748b;
            margin-bottom: 5px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-reset {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: #94a3b8;
            font-size: 12px;
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
    <div class="reset-container">
        <div class="reset-card">
            <div class="logo-section">
                <div class="logo-wrapper">
                    <div class="logo-img">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="logo-text">StudyMate</div>
                </div>
            </div>

            <div class="header-text">
                <h2>Reset Password</h2>
                <p>Enter your new password below</p>
            </div>

            @if($errors->any())
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" 
                               class="form-input @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ $email ?? old('email') }}" 
                               readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> New Password
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               class="form-input @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Enter new password"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-check-circle"></i> Confirm Password
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" 
                               class="form-input" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Confirm new password"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn-reset">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </form>

            <div class="footer-text">
                <i class="fas fa-copyright"></i> {{ date('Y') }} StudyMate. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>