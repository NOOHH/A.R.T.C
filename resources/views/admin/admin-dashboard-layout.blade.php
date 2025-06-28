<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    @yield('head')
    @stack('styles')
</head>
<body>
<div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-row">
            <img src='{{ asset('images/ARTC_logo.png') }}' alt='Logo'>
            <div class="brand-text">Ascendo Review<br>and Training Center</div>
        </div>
        <nav>
            <ul>
                <li class="@if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                    <a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;">
                        <span>&#128200;</span> Dashboard
                    </a>
                </li>
                <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) active @endif">
                    <a href="#" style="color:inherit;text-decoration:none;display:flex;align-items:center;justify-content:space-between;">
                        <span><span>&#128100;</span> Student Registration</span>
                        <span style="font-size:1.1em;margin-left:8px;">&#9662;</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="@if(Route::currentRouteName() === 'admin.student.registration.pending') active @endif">
                            <a href="{{ route('admin.student.registration.pending') }}">Pending</a>
                        </li>
                        <li class="@if(Route::currentRouteName() === 'admin.student.registration.history') active @endif">
                            <a href="{{ route('admin.student.registration.history') }}">History</a>
                        </li>
                    </ul>
                </li>
                <li class="@if(Route::currentRouteName() === 'enrollment.full' || Route::currentRouteName() === 'enrollment.modular') active @endif">
                    <span>&#128221;</span> Enrollment
                </li>
                <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.programs')) active @endif">
                    <a href="#" style="color:inherit;text-decoration:none;display:flex;align-items:center;justify-content:space-between;">
                        <span><span>&#128451;</span> Programs</span>
                        <span style="font-size:1.1em;margin-left:8px;">&#9662;</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="@if(Route::currentRouteName() === 'admin.programs.index') active @endif">
                            <a href="{{ route('admin.programs.index') }}">Programs List</a>
                        </li>
                        <li class="@if(Route::currentRouteName() === 'admin.packages.index') active @endif">
                            <a href="{{ route('admin.packages.index') }}">Packages</a>
                        </li>
                    </ul>
                </li>
                <li><span>&#128101;</span> Professors</li>
            </ul>
        </nav>
        <div style="flex: 1;"></div>
        <ul class="bottom-links">
            <li class="help-link"><span>&#10067;</span> Help</li>
            <li class="settings-link"><span>&#9881;&#65039;</span> Settings</li>
            <li class="logout"><span>&#8634;</span> Logout</li>
        </ul>
    </aside>
    <!-- Main Content -->
    <div class="main">
        <div class="topbar">
            <div class="searchbar">
                <span style="font-size: 1.3em; margin-right: 10px;">&#9776;</span>
                <input type="text" placeholder="Search">
                <span style="font-size: 1.2em; color: #888; margin-left: 8px;">&#128269;</span>
            </div>
            <div style="flex: 1;"></div>
            <!-- Fixed top-right icons -->
            <div class="fixed-top-icons">
                <span class="icon">&#128172;</span>
                <span class="icon">&#128100;</span>
            </div>
        </div>
        <div class="main-content-wrapper">
            @yield('content')
        </div>
    </div>
</div>
@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdowns = document.querySelectorAll('.dropdown-sidebar');
        dropdowns.forEach(function(dropdown) {
            var link = dropdown.querySelector('a');
            link.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            });
        });

        function addAnimatedEvents(element, hoverBg, hoverColor) {
            if (!element) return;
            element.addEventListener('mouseenter', function() {
                element.style.background = hoverBg;
                element.style.color = hoverColor;
                element.style.transform = 'scale(1.05)';
            });
            element.addEventListener('mouseleave', function() {
                element.style.background = '';
                element.style.color = '';
                element.style.transform = 'scale(1)';
            });
            element.addEventListener('mousedown', function() {
                element.style.transform = 'scale(0.95)';
            });
            element.addEventListener('mouseup', function() {
                element.style.transform = 'scale(1.05)';
            });
        }
        addAnimatedEvents(document.querySelector('.help-link'), '#f1c40f', '#fff');
        addAnimatedEvents(document.querySelector('.settings-link'), '#8e44ad', '#fff');
        addAnimatedEvents(document.querySelector('.logout'), '#e74c3c', '#fff');

        var logoutBtn = document.querySelector('.logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                window.location.href = '/';
            });
        }
    });
</script>
</body>
</html>
