<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/homepage/login.css') }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getLoginStyles() !!}
    </style>
    <style>
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
            Reset Your Password.<br>Secure Your Account.<br>Continue Learning.
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
        <h2>Reset your password.</h2>

        {{-- Display Success Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Display Info Messages --}}
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
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

        <form class="login-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <label for="email">Enter your email address</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
            <small style="color: #666; font-size: 0.9em; margin-bottom: 16px; display: block;">
                We'll send you a password reset link if this email is registered in our system.
            </small>
            
            <button type="submit">SEND RESET LINK</button>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="{{ route('login') }}" class="forgot">← Back to Login</a>
            </div>
            
            <div style="margin-top: 8px; font-size: 1em; text-align: center;">
                Don't have an account? <a href="{{ route('signup') }}" class="register-link">Register here.</a>
            </div>
        </form>
    </div>
</body>
</html>
