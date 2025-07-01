<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    @yield('head')
    @stack('styles')
</head>
<body>
<div class="admin-container">
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
                <input type="text" placeholder="Search">
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
                        <li class="@if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                                <span class="icon">📊</span> Dashboard
                            </a>
                        </li>

                        {{-- Student Registration --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">👥</span> Student Registration
                                <span class="chevron">▼</span>
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
                        <li class="@if(Route::currentRouteName() === '' || Route::currentRouteName() === 'enrollment.modular') active @endif">
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                                <span class="icon">📝</span> Enrollment
                            </a>
                        </li>

                        {{-- Programs & Packages Dropdown --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index') active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">🎓</span> Programs
                                <span class="chevron">▼</span>
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

                        {{-- Professors - TODO: Uncomment when AdminProfessorController is created --}}
                        {{-- 
                        <li class="@if(Route::currentRouteName() === 'admin.professors.index') active @endif">
                            <a href="{{ route('admin.professors.index') }}" class="sidebar-link">
                                <span class="icon">👨‍🏫</span> Professors
                            </a>
                        </li>
                        --}}
                    </ul>
                </nav>

                <!-- Bottom section -->
                <div class="sidebar-footer">
                    <ul class="bottom-links">
                        <li class="help-link"><span class="icon">❓</span> Help</li>
                        <li class="settings-link"><span class="icon">⚙️</span> Settings</li>
                        <li class="logout" onclick="handleAdminLogout();">
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
<form id="logout-form" action="{{ route('login') }}" method="GET" style="display: none;">
</form>

@yield('scripts')
<script>
function handleAdminLogout() {
    // Clear any session data and redirect to login/home
    if (confirm('Are you sure you want to logout?')) {
        // You can add session clearing logic here
        window.location.href = '{{ route("login") }}';
    }
}

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

@stack('scripts')
</body>
</html>
