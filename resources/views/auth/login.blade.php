<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Blue Header - Strathmore Style */
        .header {
            background: #003087;
            color: white;
            text-align: center;
            padding: 32px 20px 28px;
        }

        .university-logo {
            width: 85px;
            height: 85px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 14.5px;
            opacity: 0.95;
        }

        .card-body {
            padding: 40px 35px;
        }

        .welcome {
            text-align: center;
            margin-bottom: 32px;
        }

        .welcome h2 {
            font-size: 24px;
            color: #1e2937;
            margin-bottom: 8px;
        }

        .welcome p {
            color: #64748b;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 13.5px;
            color: #334155;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #003087;
            box-shadow: 0 0 0 4px rgba(0, 48, 135, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        /* Password Field */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 18px;
            color: #64748b;
            cursor: pointer;
        }

        .password-toggle:hover {
            color: #003087;
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0 24px;
            font-size: 14px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .forgot-link {
            color: #003087;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: #003087;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: #002266;
            transform: translateY(-2px);
        }

        .login-btn:disabled {
            background: #64748b;
            cursor: not-allowed;
        }

        .security-note {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #64748b;
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14.5px;
            color: #64748b;
        }

        .signup-link a {
            color: #003087;
            font-weight: 600;
            text-decoration: none;
        }

        .footer {
            text-align: center;
            margin-top: 35px;
            font-size: 12.5px;
            color: #94a3b8;
        }

        /* Error Message */
        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">

            <!-- Header -->
            <div class="header">
                <img src="{{ asset('images/logo.png') }}" alt="StudyMate Logo" class="university-logo">
                <h1>StudyMate</h1>
                <p>Learning Management System</p>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="welcome">
                    <h2>Welcome Back</h2>
                    <p>Sign in to continue your learning journey</p>
                </div>

                @if(session('success'))
                    <div style="background:#f0fdf4; color:#166534; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background:#fef2f2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email">Username / Email Address</label>
                        <input type="text" 
                               name="email" 
                               id="email"
                               class="form-input" 
                               placeholder="Enter your username or email"
                               value="{{ old('email') }}"
                               required 
                               autofocus>
                        <div class="error-message" id="emailError">Please enter a valid email or username</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-input" 
                                   placeholder="Enter your password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="error-message" id="passwordError">Password is required</div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                        @endif
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        SIGN IN
                    </button>
                </form>

                <div class="security-note">
                    For security reasons, please log out and close your browser when finished.
                </div>

                <div class="signup-link">
                    Don't have an account? 
                    <a href="{{ route('register') }}">Sign Up</a>
                </div>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} StudyMate • All Rights Reserved
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('.password-toggle i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Enhanced Frontend Validation
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Email / Username validation
            if (emailInput.value.trim() === '') {
                document.getElementById('emailError').style.display = 'block';
                emailInput.classList.add('error');
                isValid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
                emailInput.classList.remove('error');
            }

            // Password validation
            if (passwordInput.value.trim() === '') {
                document.getElementById('passwordError').style.display = 'block';
                passwordInput.classList.add('error');
                isValid = false;
            } else {
                document.getElementById('passwordError').style.display = 'none';
                passwordInput.classList.remove('error');
            }

            // Disable button during submission
            if (isValid) {
                loginBtn.textContent = 'SIGNING IN...';
                loginBtn.disabled = true;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Real-time validation
        emailInput.addEventListener('input', () => {
            document.getElementById('emailError').style.display = 'none';
            emailInput.classList.remove('error');
        });

        passwordInput.addEventListener('input', () => {
            document.getElementById('passwordError').style.display = 'none';
            passwordInput.classList.remove('error');
        });
    </script>
</body>
</html>