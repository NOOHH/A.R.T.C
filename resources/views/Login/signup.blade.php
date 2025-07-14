<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        {!! \App\Helpers\SettingsHelper::getLoginStyles() !!}
        {!! \App\Helpers\SettingsHelper::getButtonStyles() !!}
        
        .otp-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            transition: all 0.3s ease;
        }
        
        .otp-container.active {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .otp-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .otp-icon {
            width: 40px;
            height: 40px;
            background: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .otp-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #495057;
        }
        
        .otp-input-group {
            position: relative;
        }
        
        .otp-input {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 3px;
            padding: 12px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .otp-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-otp {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            padding: 8px 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
        }
        
        .btn-otp:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        }
        
        .btn-otp:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }
        
        .btn-otp.verified {
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
        }
        
        .status-message {
            margin-top: 12px;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-success {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
        }
        
        .status-error {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #842029;
        }
        
        .email-input-group {
            position: relative;
        }
        
        .email-input-group .btn-otp {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .email-input-group input {
            padding-right: 100px;
        }
        
        #signupBtn:disabled {
            background-color: #6c757d !important;
            cursor: not-allowed !important;
        }
        
        input[readonly] {
            background-color: #e9ecef;
        }
        
        .step-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .step-indicator.completed {
            background: #198754;
        }
    </style>
</head>
<body class="login-page">
    @php
        $settings = \App\Helpers\SettingsHelper::getSettings();
        $login = $settings['login'] ?? [];
        $footer = $settings['footer'] ?? [];
    @endphp
    <div class="left">
        <div class="review-text">
            Review Smarter.<br>Learn Better.<br>Succeed Faster.
        </div>
        <div class="copyright">
            {!! $footer['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' !!}
        </div>
    </div>
    <div class="right">
        <div class="logo-row">
            <img src="{{ \App\Helpers\SettingsHelper::getLogoUrl() }}" alt="Logo">
            @php
                $navbarSettings = $settings['navbar'] ?? [];
            @endphp
            <a href="{{ url('/') }}" class="brand-text">{{ $navbarSettings['brand_name'] ?? 'Ascendo Review and Training Center' }}</a>
        </div>
        <h2>Create your account.</h2>

        {{-- Display Success Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Display Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('user.signup') }}" id="signupForm">
            @csrf
            
            <div class="input-row">
                <div style="flex: 1; margin-right: 8px;">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
                </div>
                <div style="flex: 1; margin-left: 8px;">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <label for="email">Enter your email address</label>
            <div class="email-input-group">
                <input type="email" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                <button type="button" id="sendOtpBtn" class="btn-otp" onclick="sendOTP()">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
            </div>

            <!-- OTP Verification Section -->
            <div id="otpContainer" class="otp-container" style="display: none;">
                <div class="otp-header">
                    <div class="otp-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h6 class="otp-title">
                            <span class="step-indicator">2</span>
                            Email Verification
                        </h6>
                        <small class="text-muted">Enter the 6-digit code sent to your email</small>
                    </div>
                </div>
                
                <div class="otp-input-group">
                    <input type="text" id="otp" name="otp" class="form-control otp-input" placeholder="000000" maxlength="6" pattern="\d{6}">
                    <button type="button" id="verifyOtpBtn" class="btn btn-otp mt-2 w-100" onclick="verifyOTP()">
                        <i class="fas fa-check-circle"></i> Verify Code
                    </button>
                </div>
                
                <div id="otpStatus" class="status-message" style="display: none;"></div>
            </div>



            <label for="password">Enter your password</label>
            <div class="input-row">
                <input type="password" id="password" name="password" placeholder="at least 8 characters" required>
                <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
            </div>

            <label for="password_confirmation">Confirm your password</label>
            <div class="input-row">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="confirm password" required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation')">👁️</span>
            </div>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" style="margin: 20px 0;"></div>

            <button type="submit" id="signupBtn" disabled>SIGN UP</button>
            <button type="button" class="google-btn"><span style="font-size:1.2em;">&#128279;</span> SIGN UP WITH GOOGLE</button>
            <div style="margin-top: 8px; font-size: 1em;">Already have an account? <a href="{{ route('login') }}" class="register-link">Login here.</a></div>
        </form>
    </div>
    <script>
        let emailVerified = false;
        
        // Initialize email validation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeEmailValidation();
        });
        
        function initializeEmailValidation() {
            const emailInput = document.getElementById('email');
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            
            if (!emailInput || !sendOtpBtn) return;
            
            let emailCheckTimeout;
            
            // Disable Send OTP initially
            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                
                // Clear previous timeout
                clearTimeout(emailCheckTimeout);
                
                // Reset button state
                sendOtpBtn.disabled = true;
                
                // Check if email is valid format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email || !emailRegex.test(email)) {
                    sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                    return;
                }
                
                // Show checking animation
                sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
                
                // Debounce email checking (wait 500ms after user stops typing)
                emailCheckTimeout = setTimeout(() => {
                    checkEmailAvailability(email);
                }, 500);
            });
        }
        
        async function checkEmailAvailability(email) {
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            
            try {
                const response = await fetch('{{ route("check.email.availability") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (data.available) {
                    // Email is available, enable Send OTP
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                } else {
                    // Email already exists, disable Send OTP
                    sendOtpBtn.disabled = true;
                    sendOtpBtn.innerHTML = '<i class="fas fa-times"></i> Email Exists';
                    
                    // Show alert for existing email
                    alert('This email is already registered. Please use a different email or login to your existing account.');
                }
            } catch (error) {
                console.error('Error checking email:', error);
                // On error, enable the button (fail gracefully)
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            }
        }

        function togglePassword(fieldId) {
            const pwd = document.getElementById(fieldId);
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }

        async function sendOTP() {
            const email = document.getElementById('email').value;
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const otpContainer = document.getElementById('otpContainer');
            
            if (!email) {
                alert('Please enter your email address first.');
                return;
            }

            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                const response = await fetch('{{ route("signup.send.otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();
                
                if (data.success) {
                    otpContainer.style.display = 'block';
                    otpContainer.classList.add('active');
                    sendOtpBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
                    showMessage('OTP sent successfully to your email!', 'success');
                    
                    // Auto focus on OTP input
                    document.getElementById('otp').focus();
                } else {
                    showMessage(data.message, 'error');
                    sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
                }
            } catch (error) {
                showMessage('Failed to send OTP. Please try again.', 'error');
                sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            }
            
            sendOtpBtn.disabled = false;
        }

        async function verifyOTP() {
            const otp = document.getElementById('otp').value;
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            
            if (!otp || otp.length !== 6) {
                alert('Please enter a valid 6-digit OTP.');
                return;
            }

            verifyOtpBtn.disabled = true;
            verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

            try {
                const response = await fetch('{{ route("signup.verify.otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ otp: otp })
                });

                const data = await response.json();
                
                if (data.success) {
                    emailVerified = true;
                    document.getElementById('signupBtn').disabled = false;
                    document.getElementById('email').readOnly = true;
                    
                    verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> ✓ Verified';
                    verifyOtpBtn.classList.add('verified');
                    
                    // Update step indicator
                    const stepIndicator = document.querySelector('.step-indicator');
                    stepIndicator.classList.add('completed');
                    stepIndicator.innerHTML = '✓';
                    
                    showMessage('Email verified successfully!', 'success');
                } else {
                    showMessage(data.message, 'error');
                    verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
                }
            } catch (error) {
                showMessage('Failed to verify OTP. Please try again.', 'error');
                verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
            }
            
            verifyOtpBtn.disabled = false;
        }

        function showMessage(message, type) {
            const statusDiv = document.getElementById('otpStatus');
            statusDiv.textContent = message;
            statusDiv.className = `status-message status-${type}`;
            statusDiv.style.display = 'block';
            
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        }

        // Prevent form submission if email is not verified
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            if (!emailVerified) {
                e.preventDefault();
                alert('Please verify your email address before signing up.');
            }
        });

        // OTP input formatting
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>
</html>
