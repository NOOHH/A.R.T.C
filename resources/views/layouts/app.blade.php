<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Ascendo Review')</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="body-background">

    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-left">
       
            <div class="brand-text">
                <strong>Ascendo Review and Training Center</strong>
            </div>
        </div>
        <ul class="navbar-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Review Courses</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Sign Up</a></li>
        </ul>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <footer class="footer">
        © Copyright Ascendo Review and Training Center.<br>
        All Rights Reserved.
    </footer>

</body>
</html>
