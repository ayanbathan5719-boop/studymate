<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Create Account</title>
    
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
            padding: 15px;
        }

        .register-container {
            width: 100%;
            max-width: 420px;
        }

        .register-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Header */
        .header {
            background: #003087;
            color: white;
            text-align: center;
            padding: 24px 20px 20px;
        }

        .university-logo {
            width: 72px;
            height: 72px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .header p {
            font-size: 15px;
            opacity: 0.95;
        }

        .card-body {
            padding: 28px 30px;        /* Reduced padding */
        }

        .welcome {
            text-align: center;
            margin-bottom: 24px;
        }

        .welcome h2 {
            font-size: 26px;
            color: #1e2937;
            margin-bottom: 6px;
        }

        .welcome p {
            color: #64748b;
            font-size: 15.5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 15px;           /* Bigger */
            color: #334155;
        }

        .form-input {
            width: 100%;
            padding: 15px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 17px;           /* Larger input text */
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

        /* Password Toggle */
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
            font-size: 20px;
            color: #64748b;
            cursor: pointer;
        }

        /* Password Requirements - Made more compact */
        .password-requirements {
            background: #f8fafc;
            border-radius: 10px;
            padding: 12px 16px;
            margin-top: 10px;
            font-size: 14px;
        }

        .password-requirements p {
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
        }

        .password-requirements li {
            margin: 6px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Terms */
        .terms-group {
            margin: 22px 0 20px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14.5px;
            color: #475569;
            line-height: 1.4;
        }

        .register-btn {
            width: 100%;
            padding: 16px;
            background: #003087;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
        }

        .register-btn:hover {
            background: #002266;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            font-size: 15.5px;
            color: #64748b;
        }

        .login-link a {
            color: #003087;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            margin-top: 28px;
            font-size: 13px;
            color: #94a3b8;
        }

        .error-message {
            color: #ef4444;
            font-size: 13.5px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">

            <!-- Header -->
            <div class="header">
                <img src="{{ asset('images/logo.png') }}" alt="StudyMate Logo" class="university-logo">
                <h1>StudyMate</h1>
                <p>Learning Management System</p>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="welcome">
                    <h2>Create Account</h2>
                    <p>Join StudyMate to start your learning journey</p>
                </div>

                @if($errors->any())
                    <div style="background:#fef2f2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:18px; font-size:14.5px;">
                        <strong>Please fix the following:</strong>
                        <ul style="margin-top: 6px; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" class="form-input" 
                               placeholder="Enter your full name" value="{{ old('name') }}" required>
                        <div class="error-message" id="nameError">Full name is required</div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-input" 
                               placeholder="Enter your email address" value="{{ old('email') }}" required>
                        <div class="error-message" id="emailError">Please enter a valid email</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-input" 
                                   placeholder="Create a strong password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-requirements">
                            <p>Password must contain:</p>
                            <ul>
                                <li id="length-check"><i class="fas fa-circle"></i> At least 8 characters</li>
                                <li id="letter-check"><i class="fas fa-circle"></i> At least one letter</li>
                                <li id="number-check"><i class="fas fa-circle"></i> At least one number</li>
                            </ul>
                        </div>
                        <div class="error-message" id="passwordError">Password must meet the requirements</div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-input" placeholder="Confirm your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="error-message" id="confirmError">Passwords do not match</div>
                    </div>

                    <div class="terms-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="terms" id="terms" required>
                            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="register-btn" id="registerBtn">
                        CREATE ACCOUNT
                    </button>
                </form>

                <div class="login-link">
                    Already have an account? 
                    <a href="{{ route('login') }}">Sign In</a>
                </div>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} StudyMate • All Rights Reserved
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = input.parentElement.querySelector('.password-toggle i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password Strength
        const passwordInput = document.getElementById('password');
        function updatePasswordRequirements() {
            const pass = passwordInput.value;
            document.getElementById('length-check').classList.toggle('valid', pass.length >= 8);
            document.getElementById('letter-check').classList.toggle('valid', /[a-zA-Z]/.test(pass));
            document.getElementById('number-check').classList.toggle('valid', /\d/.test(pass));
        }
        passwordInput.addEventListener('input', updatePasswordRequirements);

        // Form Validation
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', function(e) {
            let valid = true;
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

            if (document.getElementById('name').value.trim() === '') {
                document.getElementById('nameError').style.display = 'block';
                document.getElementById('name').classList.add('error');
                valid = false;
            }

            if (!document.getElementById('email').value.includes('@')) {
                document.getElementById('emailError').style.display = 'block';
                document.getElementById('email').classList.add('error');
                valid = false;
            }

            const password = document.getElementById('password').value;
            if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/\d/.test(password)) {
                document.getElementById('passwordError').style.display = 'block';
                document.getElementById('password').classList.add('error');
                valid = false;
            }

            if (password !== document.getElementById('password_confirmation').value) {
                document.getElementById('confirmError').style.display = 'block';
                document.getElementById('password_confirmation').classList.add('error');
                valid = false;
            }

            if (valid) {
                document.getElementById('registerBtn').textContent = 'CREATING ACCOUNT...';
                document.getElementById('registerBtn').disabled = true;
            } else {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>