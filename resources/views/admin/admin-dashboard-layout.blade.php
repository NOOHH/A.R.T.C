<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">

    {{-- Global UI Styles --}}
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}
    
    @yield('head')
    @stack('styles')
</head>
<body>
<div class="admin-container">
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <a href="{{ route('home') }}" class="brand-link">
                <img src="{{ App\Helpers\UIHelper::getGlobalLogo() }}" alt="Logo">
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

            <!-- Main Content -->
            <div class="main-content">
                

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
                                <li class="@if(Route::currentRouteName() === 'admin.student.registration.payment.pending') active @endif">
                                    <a href="{{ route('admin.student.registration.payment.pending') }}">Payment Pending</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.student.registration.payment.history') active @endif">
                                    <a href="{{ route('admin.student.registration.payment.history') }}">Payment History</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Students List --}}
                        <li class="@if(Route::currentRouteName() === 'admin.students.index') active @endif">
                            <a href="{{ route('admin.students.index') }}" class="sidebar-link">
                                <span class="icon">📋</span> List of Students
                            </a>
                        </li>

                        {{-- Student Enroll Dropdown --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.batches') || Route::currentRouteName() === 'admin.enrollments.index') active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">📝</span> Student Enroll
                                <span class="chevron">▼</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.batches')) active @endif">
                                    <a href="{{ route('admin.batches.index') }}">Batch Enroll</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.enrollments.index') active @endif">
                                    <a href="{{ route('admin.enrollments.index') }}">Assign Course to Student</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Directors --}}
                        <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.directors')) active @endif">
                            <a href="{{ route('admin.directors.index') }}" class="sidebar-link">
                                <span class="icon">👨‍💼</span> Directors
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

                        {{-- Professors --}}
                        <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.professors')) active @endif">
                            <a href="{{ route('admin.professors.index') }}" class="sidebar-link">
                                <span class="icon">👨‍🏫</span> Professors
                            </a>
                        </li>

                        {{-- Analytics --}}
                        <li class="@if(Route::currentRouteName() === 'admin.analytics.index') active @endif">
                            <a href="{{ route('admin.analytics.index') }}" class="sidebar-link">
                                <span class="icon">📈</span> Analytics
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Bottom section -->
                <div class="sidebar-footer">
                    <ul class="bottom-links">
                        <li class="help-link"><span class="icon">❓</span> Help</li>
                        <li class="settings-link">
                            <a href="{{ route('admin.settings.index') }}" class="sidebar-link">
                                <span class="icon">⚙️</span> Settings
                            </a>
                        </li>
                        <li class="logout" onclick="handleAdminLogout();" style="cursor: pointer;">
                            <span class="icon">🚪</span> Logout
                        </li>
                    </ul>
                </div>
            </aside>




















                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="admin-logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@yield('scripts')
<script>
function handleAdminLogout() {
    if (confirm('Are you sure you want to logout?')) {
        // Submit the form to properly log out and clear session
        document.getElementById('admin-logout-form').submit();
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

    // Settings navigation
    document.querySelector('.settings-link')?.addEventListener('click', () => {
        window.location.href = '{{ route("admin.settings.index") }}';
    });

    // Logout is handled by handleAdminLogout() function, no additional handler needed here
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
