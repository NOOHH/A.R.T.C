
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Ascendo Review')</title>
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    @stack('styles') {{-- ✅ to load page-specific styles --}}
</head>

<body class="body-background">
    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
            <div class="brand-text">
                <a href="{{ url('/') }}"><strong>Ascendo Review and Training Center</strong>
            </div>
        </div>
        <ul class="navbar-links">
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="#">Review Courses</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
            <li class="dropdown">
                <a href="#">Sign Up</a>
                <div class="dropdown-menu">
                    <a href="{{ url('/login') }}">Login</a>
                    <a href="{{ url('/enrollment') }}">Sign Up</a>
                </div>
            </li>
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
