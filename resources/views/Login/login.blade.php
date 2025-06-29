<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="left">
        <div class="review-text">
            Review Smarter.<br>Learn Better.<br>Succeed Faster.
        </div>
        <img src="{{ asset('images/login-illustration.png') }}" alt="Study Illustration" class="login-illustration">
        <div class="copyright">
            © Copyright Ascendo Review and Training Center.<br>All Rights Reserved.
        </div>
    </div>
    <div class="right">
        <div class="logo-row">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <a href="{{ url('/') }}" class="brand-text">Ascendo Review<br>and Training Center</a>
        </div>
        <h2>Log in to your account.</h2>

       <form class="login-form" method="POST" action="{{ route('student.login') }}">
    @csrf
    <label for="email">Enter your email address</label>
    <input type="email" id="email" name="email" placeholder="name@example.com" required>

    <label for="password">Enter your password</label>
    <div class="input-row">
        <input type="password" id="password" name="password" placeholder="at least 8 characters">
        <span class="toggle-password" onclick="togglePassword()">👁️</span>
    </div>
    <a href="#" class="forgot">Forgot your password? Click here.</a>
    <button type="submit">LOG IN</button>
    <button type="button" class="google-btn"><span style="font-size:1.2em;">&#128279;</span> SIGN IN WITH GOOGLE</button>
    <div style="margin-top: 8px; font-size: 1em;">Don't have an account? <a href="#" class="register-link">Register here.</a></div>
</form>
    </div>
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }
    </script>
</body>
</html>
