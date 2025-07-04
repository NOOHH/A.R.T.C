<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student/student-dashboard-layout.css') }}">
    @yield('head')
    @stack('styles')
</head>
<body>
<div class="student-container">
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <a href="{{ route('home') }}" class="brand-link">
                <img src="{{ asset('images/ARTC_logo.png') }}" alt="Logo">
                <div class="brand-text">Ascendo Review<br>and Training Center</div>
            </a>
        </div>
        
        <!-- Search Bar in Header -->
        <div class="header-search">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search courses or topics">
                <button class="search-btn">🔍</button>
            </div>
        </div>
        
        <div class="header-right">
            <span class="notification-icon">💬</span>
            <span class="profile-icon">👤</span>
        </div>
    </header>

    <div class="main-wrapper">
        <div class="content-below-search">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav>
                    <ul>
                        {{-- Dashboard --}}
                        <li class="@if(Route::currentRouteName() === 'student.dashboard') active @endif">
                            <a href="{{ route('student.dashboard') }}" class="sidebar-link">
                                <span class="icon">📊</span> Dashboard
                            </a>
                        </li>

                        {{-- Calendar --}}
                        <li class="@if(Route::currentRouteName() === 'student.calendar') active @endif">
                            <a href="{{ route('student.calendar') }}" class="sidebar-link">
                                <span class="icon">📅</span> Calendar
                            </a>
                        </li>

                        {{-- My Programs --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'student.courses')) active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">📚</span> My Programs
                                <span class="chevron">▼</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li class="@if(Route::currentRouteName() === 'student.courses.calculus1') active @endif">
                                    <a href="{{ route('student.courses.calculus1') }}">Calculus 1</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'student.courses.calculus2') active @endif">
                                    <a href="{{ route('student.courses.calculus2') }}">Calculus 2</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>

                <!-- Bottom section -->
                <div class="sidebar-footer">
                    <ul class="bottom-links">
                        <li class="help-link"><span class="icon">❓</span> Help</li>
                        <li class="settings-link">
                            <a href="{{ route('student.settings') }}" style="color: inherit; text-decoration: none;">
                                <span class="icon">⚙️</span> Settings
                            </a>
                        </li>
                        <li class="logout" onclick="document.getElementById('logout-form').submit();">
                            <span class="icon">🚪</span> Logout
                        </li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Content Area -->
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@yield('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-open dropdowns that are marked as active
    document.querySelectorAll('.dropdown-sidebar.active').forEach(dropdown => {
        dropdown.classList.add('active');
    });

    // Toggle dropdowns
    document.querySelectorAll('.dropdown-sidebar > a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            link.parentElement.classList.toggle('active');
        });
    });
});
</script>
</body>
</html>
