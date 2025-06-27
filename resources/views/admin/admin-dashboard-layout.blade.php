<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    @yield('head')
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
                <li class="@if(Route::currentRouteName() === 'admin.dashboard') active @endif"><a href="{{ route('admin.dashboard') }}" style="color:inherit;text-decoration:none;"><span>&#128200;</span> Dashboard</a></li>
                <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) active @endif">
                    <a href="#" style="color:inherit;text-decoration:none;display:flex;align-items:center;justify-content:space-between;">
                        <span><span>&#128100;</span> Student Registration</span>
                        <span style="font-size:1.1em;margin-left:8px;">&#9662;</span>
                    </a>
                    <ul class="sidebar-submenu" @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration'))style="display:block;"@else style="display:none;"@endif>
                        <li class="@if(Route::currentRouteName() === 'admin.student.registration.pending') active @endif"><a href="{{ route('admin.student.registration.pending') }}">Pending</a></li>
                        <li class="@if(Route::currentRouteName() === 'admin.student.registration.history') active @endif"><a href="{{ route('admin.student.registration.history') }}">History</a></li>
                    </ul>
                </li>
                <li class="@if(Route::currentRouteName() === 'enrollment.full' || Route::currentRouteName() === 'enrollment.modular') active @endif"><span>&#128221;</span> Enrollment</li>
                <li><span>&#128451;</span> Programs</li>
                <li><span>&#128101;</span> Professors</li>
            </ul>
        </nav>
        <div style="flex: 1;"></div>
        <ul class="bottom-links">
            <li><span>&#10067;</span> Help</li>
            <li><span>&#9881;&#65039;</span> Settings</li>
            <li class="logout"><span>&#8634;</span> Logout</li>
        </ul>
    </aside>
    <!-- Main Content -->
    <div class="main">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="searchbar">
                <span style="font-size: 1.3em; margin-right: 10px;">&#9776;</span>
                <input type="text" placeholder="Search">
                <span style="font-size: 1.2em; color: #888; margin-left: 8px;">&#128269;</span>
            </div>
            <div style="flex: 1;"></div>
            <span class="icon">&#128172;</span>
            <span class="icon">&#128100;</span>
        </div>
        @yield('content')
    </div>
</div>
@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdown = document.querySelector('.dropdown-sidebar');
        if (dropdown) {
            var link = dropdown.querySelector('a');
            var submenu = dropdown.querySelector('.sidebar-submenu');
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var isOpen = submenu.style.display === 'block';
                submenu.style.display = isOpen ? 'none' : 'block';
                dropdown.classList.toggle('active', !isOpen);
            });
        }
    });
</script>
</body>
</html>
