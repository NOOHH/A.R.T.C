<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/homepage/login.css') }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getLoginStyles() !!}
    </style>
    <style>
        {!! \App\Helpers\SettingsHelper::getButtonStyles() !!}
    </style>
    
    <!-- reCAPTCHA -->
    @if(env('RECAPTCHA_SITE_KEY'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    
    <style>
        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0 16px 0;
            font-size: 0.85em;
            color: #495057;
        }
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        .password-requirements li {
            margin: 4px 0;
        }
        .g-recaptcha {
            margin: 16px 0;
            display: flex;
            justify-content: center;
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            margin-bottom: 16px;
            background: #e9ecef;
            transition: all 0.3s ease;
        }
        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #fd7e14; width: 50%; }
        .strength-good { background: #ffc107; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }
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
            Secure Your Account.<br>Create Strong Password.<br>Continue Learning.
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
        <h2>Change your password.</h2>

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

        <form class="login-form" method="POST" action="{{ route('password.update') }}" id="changePasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
            
            <label for="password">New Password</label>
            <div class="input-row">
                <input type="password" id="password" name="password" placeholder="Enter your new password" required>
                <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
            </div>
            <div class="password-strength" id="passwordStrength"></div>
            
            <div class="password-requirements">
                <strong>Password must contain:</strong>
                <ul>
                    <li>At least 8 characters</li>
                    <li>At least one uppercase letter (A-Z)</li>
                    <li>At least one lowercase letter (a-z)</li>
                    <li>At least one number (0-9)</li>
                    <li>At least one special character (!@#$%^&*)</li>
                </ul>
            </div>

            <label for="password_confirmation">Confirm New Password</label>
            <div class="input-row">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password" required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation')">👁️</span>
            </div>
            
            <!-- reCAPTCHA -->
            @if(env('RECAPTCHA_SITE_KEY'))
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            @endif
            
            <button type="submit">CHANGE PASSWORD</button>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="{{ route('login') }}" class="forgot">← Back to Login</a>
            </div>
        </form>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const pwd = document.getElementById(fieldId);
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let score = 0;
            
            // Length check
            if (password.length >= 8) score++;
            
            // Character type checks
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;
            
            // Update strength bar
            strengthBar.className = 'password-strength';
            if (score === 0) {
                strengthBar.classList.add('');
            } else if (score <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (score === 3) {
                strengthBar.classList.add('strength-fair');
            } else if (score === 4) {
                strengthBar.classList.add('strength-good');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Form validation
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            // Check if passwords match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match. Please try again.');
                return false;
            }
            
            // Check password requirements
            const requirements = [
                { test: password.length >= 8, msg: 'Password must be at least 8 characters long' },
                { test: /[a-z]/.test(password), msg: 'Password must contain at least one lowercase letter' },
                { test: /[A-Z]/.test(password), msg: 'Password must contain at least one uppercase letter' },
                { test: /[0-9]/.test(password), msg: 'Password must contain at least one number' },
                { test: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password), msg: 'Password must contain at least one special character' }
            ];
            
            for (let req of requirements) {
                if (!req.test) {
                    e.preventDefault();
                    alert(req.msg);
                    return false;
                }
            }
            
            // Check reCAPTCHA if enabled
            @if(env('RECAPTCHA_SITE_KEY'))
            const recaptchaResponse = document.querySelector('[name="g-recaptcha-response"]');
            if (!recaptchaResponse || !recaptchaResponse.value) {
                e.preventDefault();
                alert('Please complete the reCAPTCHA verification.');
                return false;
            }
            @endif
        });
    </script>
</body>
</html>
