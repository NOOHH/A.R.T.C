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
            <img src="{{ asset('images/ARTC_logo.png') }}" alt="Logo">
            <div class="brand-text">Ascendo Review<br>and Training Center</div>
        </div>
        <nav>
            <ul>
                {{-- Dashboard --}}
                <li class="@if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                        <span>&#128200;</span> Dashboard
                    </a>
                </li>

                {{-- Student Registration --}}
                <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) active @endif">
                    <a href="#" class="sidebar-link flex justify-between">
                        <span>&#128100; Student Registration</span>
                        <span class="chevron">&#9662;</span>
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

                {{-- Enrollment --}}
                <li class="@if(Route::currentRouteName() === 'enrollment.full' || Route::currentRouteName() === 'enrollment.modular') active @endif">
                    <a href="{{ route(Route::currentRouteName()) }}" class="sidebar-link">
                        <span>&#128221;</span> Enrollment
                    </a>
                </li>

                {{-- Programs & Packages Dropdown --}}
                <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index') active @endif">
                    <a href="#" class="sidebar-link flex justify-between">
                        <span>&#128451; Programs</span>
                        <span class="chevron">&#9662;</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="@if(Route::currentRouteName() === 'admin.programs.index') active @endif">
                            <a href="{{ route('admin.programs.index') }}">Manage Programs</a>
                        </li>
                        <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.modules')) active @endif">
                            <a href="{{ route('admin.modules.index') }}">Manage Modules</a>
                        </li>
                        <li class="@if(Route::currentRouteName() === 'admin.packages.index') active @endif">
                            <a href="{{ route('admin.packages.index') }}">Packages</a>
                        </li>
                    </ul>
                </li>

                {{-- Professors --}}
                <li class="@if(Route::currentRouteName() === 'admin.professors.index') active @endif">
                    <a href="{{ route('admin.professors.index') }}" class="sidebar-link">
                        <span>&#128101;</span> Professors
                    </a>
                </li>
                --}}
            </ul>
        </nav>

        <div class="flex-grow"></div>

        {{-- Bottom Links --}}
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
                <span class="menu-icon">&#9776;</span>
                <input type="text" placeholder="Search">
                <span class="search-icon">&#128269;</span>
            </div>
            <div class="flex-grow"></div>
            <span class="icon">&#128172;</span>
            <span class="icon">&#128100;</span>
        </div>

        @yield('content')
    </div>
</div>

@yield('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dropdowns
    document.querySelectorAll('.dropdown-sidebar > a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            link.parentElement.classList.toggle('active');
        });
    });

    // Hover/click animations
    function addAnimatedEvents(el, hoverBg, hoverColor) {
        if (!el) return;
        el.addEventListener('mouseenter', () => {
            el.style.background = hoverBg;
            el.style.color = hoverColor;
            el.style.transform = 'scale(1.05)';
        });
        el.addEventListener('mouseleave', () => {
            el.style.background = '';
            el.style.color = '';
            el.style.transform = 'scale(1)';
        });
        el.addEventListener('mousedown', () => el.style.transform = 'scale(0.95)');
        el.addEventListener('mouseup',   () => el.style.transform = 'scale(1.05)');
    }
    addAnimatedEvents(document.querySelector('.help-link'),    '#f1c40f', '#fff');
    addAnimatedEvents(document.querySelector('.settings-link'),'#8e44ad', '#fff');
    addAnimatedEvents(document.querySelector('.logout'),       '#e74c3c', '#fff');

    // Logout
    document.querySelector('.logout')?.addEventListener('click', () => {
        window.location.href = '/';
    });
});
</script>

{{-- allow child views to push scripts here --}}
@stack('scripts')
</body>
</html>
