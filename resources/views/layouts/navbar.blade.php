<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ascendo Review')</title>
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getNavbarStyles() !!}
        {!! \App\Helpers\SettingsHelper::getFooterStyles() !!}
        {!! \App\Helpers\SettingsHelper::getProgramCardStyles() !!}
    </style>
    @stack('styles') {{-- ✅ to load page-specific styles --}}
</head>

<body class="body-background @if(request()->routeIs('enrollment.*')) enrollment-page @endif">
    @php
        $settings = \App\Helpers\SettingsHelper::getSettings();
        $navbar = $settings['navbar'] ?? [];
        $footer = $settings['footer'] ?? [];
    @endphp
    
    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-left">
            <img src="{{ \App\Helpers\SettingsHelper::getLogoUrl(asset('images/ARTC_Logo.png')) }}" alt="Logo" class="logo">
            <div class="brand-text">
                <a href="{{ url('/') }}"><strong>{{ $navbar['brand_name'] ?? 'Ascendo Review and Training Center' }}</strong></a>
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
        {!! $footer['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' !!}
    </footer>
    @stack('scripts') {{-- Ensure page-specific scripts are loaded before </body> --}}
</body>
</html>
