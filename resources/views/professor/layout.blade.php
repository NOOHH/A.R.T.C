<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Professor Dashboard')</title>
    
    @php
        // Get user info for global variables
        $user = Auth::user();
        
        // If Laravel Auth user is not available, fallback to session data
        if (!$user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Create a fake user object from session data for consistency
            $sessionUser = (object) [
                'id' => $_SESSION['user_id'] ?? session('user_id'),
                'name' => $_SESSION['user_name'] ?? session('user_name') ?? 'Guest',
                'role' => $_SESSION['user_type'] ?? session('user_role') ?? 'guest'
            ];
            
            // Only use session user if we have valid session data
            if ($sessionUser->id) {
                $user = $sessionUser;
            }
        }
    @endphp

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        window.myId = @json(optional($user)->id);
        window.myName = @json(optional($user)->name ?? 'Guest');
        window.isAuthenticated = @json((bool) $user);
        window.userRole = @json(optional($user)->role ?? 'guest');
        window.csrfToken = @json(csrf_token());
        
        // Global chat state
        window.currentChatType = null;
        window.currentChatUser = null;
        
        // Make variables available without window prefix
        var myId = window.myId;
        var myName = window.myName;
        var isAuthenticated = window.isAuthenticated;
        var userRole = window.userRole;
        var csrfToken = window.csrfToken;
        var currentChatType = window.currentChatType;
        var currentChatUser = window.currentChatUser;
        
        console.log('Professor Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>
    
    <!-- jQuery (required for dynamic dropdowns and AJAX in child views) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Admin CSS (reused for consistency) -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">

    {{-- Global UI Styles --}}
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}

    {{-- Chat CSS + any overrides --}}
    @stack('styles')
    
    <style>
    .logout-btn {
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        padding: 0.75rem 1.5rem;
        color: inherit;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .logout-btn:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .logout-btn i {
        margin-right: 0.75rem;
        width: 1.2rem;
        text-align: center;
    }
    /* Smooth transition for sidebar width */
    .modern-sidebar {
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    </style>
    <style>
        /* Flex container for sidebar and main content */
        .content-below-search {
            display: flex;
            flex: 1;
            min-height: 0;
        }
        /* Main content area fills height below header */
        .page-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            min-height: 0;
            height: calc(100vh - 68px);
        }
        /* Inner wrapper for page content padding and scrolling */
        .content-wrapper {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            min-height: 0;
        }
    </style>
</head>
<body style="overflow-x: hidden;">
<div class="admin-container">
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <!-- Hamburger Menu Button -->
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
            <!-- Brand Logo and Text -->
            <div class="brand-container">
                <img src="{{ asset('images/ARTC_logo.png') }}" alt="A.R.T.C" class="brand-logo">
                <span class="brand-text">Professor Dashboard</span>
            </div>
        </div>

        <div class="header-center">
            <!-- Global Search -->
            <div class="search-container">
                <div class="search-wrapper">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search students, meetings, programs...">
                    <div class="search-results-dropdown" id="searchResults"></div>
                </div>
            </div>
        </div>

        <div class="header-right">
            <!-- Notifications -->
            <div class="header-item notification-dropdown">
                <button class="btn notification-btn" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge" id="notificationCount">0</span>
                </button>
                <div class="dropdown-menu notification-menu">
                    <div class="notification-header">
                        <h6>Notifications</h6>
                        <button class="btn btn-link mark-all-read">Mark all as read</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <div class="notification-item">
                            <i class="bi bi-info-circle text-primary"></i>
                            <div class="notification-content">
                                <p>Welcome to the Professor Dashboard!</p>
                                <small class="text-muted">Just now</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="header-item profile-dropdown">
                <button class="btn profile-btn" data-bs-toggle="dropdown">
                    <span class="profile-name">{{ session('user_name', 'Professor') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="dropdown-menu profile-menu">
                    <a class="dropdown-item" href="{{ route('professor.profile') }}">
                        <i class="bi bi-person me-2"></i>My Profile
                    </a>
                    <a class="dropdown-item" href="{{ route('professor.settings') }}">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Mobile Profile Icon -->
        <div class="profile-icon">👤</div>
    </header>
    <div class="main-wrapper">
        <div class="content-below-search">
            <!-- Sidebar Overlay -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            <!-- Sidebar -->
            <aside class="modern-sidebar" id="modernSidebar">
                <div class="sidebar-content">
                    <nav class="sidebar-nav">
                        <!-- Dashboard -->
                        <div class="nav-item">
                            <a href="{{ route('professor.dashboard') }}" class="nav-link @if(Route::currentRouteName() === 'professor.dashboard') active @endif">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <!-- Meetings -->
                        <div class="nav-item">
                            <a href="{{ route('professor.meetings') }}" class="nav-link @if(Route::currentRouteName() === 'professor.meetings') active @endif">
                                <i class="bi bi-calendar-event"></i>
                                <span>Meetings</span>
                            </a>
                        </div>
                        <!-- Students Dropdown -->
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#studentsMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="studentsMenu">
                                <i class="bi bi-people"></i>
                                <span>Students</span>
                                <i class="bi bi-chevron-down dropdown-arrow"></i>
                            </a>
                            <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'professor.students')) show @endif" id="studentsMenu">
                                <div class="submenu">
                                    <a href="{{ route('professor.students.index') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.students.index') active @endif">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <span>All Students</span>
                                    </a>
                                    <a href="{{ route('professor.students.batches') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.students.batches') active @endif">
                                        <i class="bi bi-collection"></i>
                                        <span>My Batches</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Programs -->
                        <div class="nav-item">
                            <a href="{{ route('professor.programs') }}" class="nav-link @if(Route::currentRouteName() === 'professor.programs') active @endif">
                                <i class="bi bi-book"></i>
                                <span>My Programs</span>
                            </a>
                        </div>
                        <!-- Assignments Dropdown -->
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#assignmentsMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="assignmentsMenu">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Assignments</span>
                                <i class="bi bi-chevron-down dropdown-arrow"></i>
                            </a>
                            <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'professor.assignments')) show @endif" id="assignmentsMenu">
                                <div class="submenu">
                                    <a href="{{ route('professor.grading') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.assignments.index') active @endif">
                                        <i class="bi bi-list-task"></i>
                                        <span>View All</span>
                                    </a>
                                    <a href="{{ route('professor.assignments.create') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.assignments.create') active @endif">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Create New</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Messages -->
                        <div class="nav-item">
                            <a href="{{ route('professor.chat') }}" class="nav-link @if(Route::currentRouteName() === 'professor.chat') active @endif">
                                <i class="bi bi-chat-dots"></i>
                                <span>Messages</span>
                            </a>
                        </div>
                        <!-- Settings -->
                        <div class="nav-item">
                            <a href="{{ route('professor.settings') }}" class="nav-link @if(Route::currentRouteName() === 'professor.settings') active @endif">
                                <i class="bi bi-gear"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                        <!-- Logout -->
                        <div class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </aside>
            <!-- Main Content -->
            <main class="page-content">
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Admin Layout JavaScript (reused) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('modernSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', function() {
            // On mobile: toggle slide-in
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            } else {
                // On desktop: toggle collapsed
                sidebar.classList.toggle('collapsed');
            }
        });
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
    // Removed custom dropdown JS, Bootstrap handles collapse
});
</script>

@stack('scripts')

<!-- Include Global Chat Component -->
@include('components.global-chat')
</body>
</html>
