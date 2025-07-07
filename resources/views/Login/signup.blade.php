<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getLoginStyles() !!}
        {!! \App\Helpers\SettingsHelper::getButtonStyles() !!}
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

        <form class="login-form" method="POST" action="{{ route('user.signup') }}">
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
            <input type="email" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>

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

            <button type="submit">SIGN UP</button>
            <button type="button" class="google-btn"><span style="font-size:1.2em;">&#128279;</span> SIGN UP WITH GOOGLE</button>
            <div style="margin-top: 8px; font-size: 1em;">Already have an account? <a href="{{ route('login') }}" class="register-link">Login here.</a></div>
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
    </script>
</body>
</html>
