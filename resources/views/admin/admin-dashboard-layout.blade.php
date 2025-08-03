<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    @php
        // Get user info for global variables
        $user = Auth::user();
        
        // Check if user is actually logged in via Laravel Auth or valid session
        $isLoggedIn = Auth::check() || session('logged_in') === true;
        
        // If Laravel Auth user is not available but session indicates logged in, fallback to session data
        if (!$user && $isLoggedIn) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Only use session data if logged_in is explicitly true
            if (session('logged_in') === true || $_SESSION['logged_in'] ?? false) {
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
        }
        
        // If not logged in, clear user data
        if (!$isLoggedIn) {
            $user = null;
        }
        use App\Models\AdminSetting;
    @endphp

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        @php
            // Get user data from session since Auth::user() is not working
            $user = null;
            $director = null;
            $role = null;
            $id = null;
            $name = null;
            
            // Check session data first
            if (session('logged_in') && session('user_id')) {
                $user = (object) [
                    'id' => session('user_id'),
                    'name' => session('user_name'),
                    'role' => session('user_role') ?? session('role'),
                    'user_type' => session('user_type')
                ];
            }
            
            // Check PHP session as fallback
            if (!$user && isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                $user = (object) [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'role' => $_SESSION['user_type'],
                    'user_type' => $_SESSION['user_type']
                ];
            }
            
            // Set role and ID based on user type
            if ($user) {
                if ($user->user_type === 'director' || $user->role === 'director') {
                    $role = 'director';
                    $id = $user->id;
                    $name = $user->name;
                } elseif ($user->user_type === 'admin' || $user->role === 'admin') {
                    $role = 'admin';
                    $id = $user->id;
                    $name = $user->name;
                } elseif ($user->user_type === 'professor' || $user->role === 'professor') {
                    $role = 'professor';
                    $id = $user->id;
                    $name = $user->name;
                } else {
                    $role = $user->role ?? 'student';
                    $id = $user->id;
                    $name = $user->name;
                }
            }
            
            // Prepare debug data
            $debugData = [
                'session_user_id' => session('user_id'),
                'session_logged_in' => session('logged_in'),
                'session_role' => session('user_role'),
                'session_type' => session('user_type'),
                'php_session_user_id' => $_SESSION['user_id'] ?? null,
                'php_session_logged_in' => $_SESSION['logged_in'] ?? null,
                'php_session_type' => $_SESSION['user_type'] ?? null
            ];
        @endphp
        window.myId = @json($id);
        window.myName = @json($name);
        window.userRole = @json($role);
        window.isAuthenticated = @json($user !== null);
        // DEBUG: show the session data for troubleshooting
        window._sessionDebug = @json($debugData);
        console.log('DEBUG Session data:', window._sessionDebug);
        console.log('DEBUG Auth::user()', @json(Auth::user()));
        console.log('DEBUG Auth::guard("director")->user()', @json(Auth::guard('director')->user()));
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
        
        console.log('Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>

    @php
        // Ensure we can reliably check admin status
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $isAdmin = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin')
                 || (session('user_type') === 'admin')
                 || ($user && isset($user->role) && $user->role === 'admin');
        $isDirector = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'director')
                 || (session('user_type') === 'director')
                 || ($user && isset($user->role) && $user->role === 'director');
        $directorFeatures = [
            'view_students' => AdminSetting::getValue('director_view_students', 'true') === 'true' || AdminSetting::getValue('director_view_students', '1') === '1',
            'manage_programs' => AdminSetting::getValue('director_manage_programs', 'false') === 'true' || AdminSetting::getValue('director_manage_programs', '0') === '1',
            'manage_modules' => AdminSetting::getValue('director_manage_modules', 'false') === 'true' || AdminSetting::getValue('director_manage_modules', '0') === '1',
            'manage_professors' => AdminSetting::getValue('director_manage_professors', 'false') === 'true' || AdminSetting::getValue('director_manage_professors', '0') === '1',
            'manage_batches' => AdminSetting::getValue('director_manage_batches', 'false') === 'true' || AdminSetting::getValue('director_manage_batches', '0') === '1',
            'view_analytics' => AdminSetting::getValue('director_view_analytics', 'false') === 'true' || AdminSetting::getValue('director_view_analytics', '0') === '1',
            'manage_enrollments' => AdminSetting::getValue('director_manage_enrollments', 'true') === 'true' || AdminSetting::getValue('director_manage_enrollments', '1') === '1',
        ];
    @endphp
    
    <!-- jQuery (required for dynamic dropdowns and AJAX in child views) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Admin-specific CSS -->
    <style>
    :root {
        --sidebar-width: 280px;
        --sidebar-collapsed-width: 70px;
        --sidebar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --sidebar-text: #ffffff;
        --sidebar-text-muted: #9ca3af;
        --sidebar-hover: rgba(255, 255, 255, 0.1);
        --sidebar-active: rgba(255, 255, 255, 0.2);
        --sidebar-border: rgba(255, 255, 255, 0.1);
        --sidebar-shadow: 0 4px 25px rgba(0, 0, 0, 0.15);
        --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --border-radius: 12px;
    }

    /* Global container */
    .admin-container {
        display: flex;
        min-height: 100vh;
        background: #f8f9fa;
    }

    /* Main content area */
    .main-content-area {
        flex: 1;
        margin-left: var(--sidebar-width);
        transition: margin-left 0.3s ease;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .main-content-area.sidebar-collapsed {
        margin-left: var(--sidebar-collapsed-width);
    }

    @media (max-width: 768px) {
        .main-content-area {
            margin-left: 0;
        }
    }

    /* Ensure consistent sidebar behavior */
    .modern-sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        box-shadow: var(--sidebar-shadow);
        transform: translateX(0);
        transition: var(--transition-smooth);
        z-index: 9999;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    /* Add subtle hover effect for sidebar background areas */
    .modern-sidebar:hover {
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }

    /* Remove cursor pointer and hover effects from navigation elements */
    .modern-sidebar .nav-link,
    .modern-sidebar .submenu-link,
    .modern-sidebar button,
    .modern-sidebar a,
    .modern-sidebar form {
        cursor: pointer;
    }

    .modern-sidebar .nav-link:hover,
    .modern-sidebar .submenu-link:hover,
    .modern-sidebar button:hover,
    .modern-sidebar a:hover {
        box-shadow: none;
    }

    .modern-sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    /* Ensure sidebar content is properly structured */
    .sidebar-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
    }

    /* Mobile: Hidden by default */
    @media (max-width: 768px) {
        .modern-sidebar {
            transform: translateX(-100%);
        }
        
        .modern-sidebar.mobile-open {
            transform: translateX(0);
        }
    }

    /* Sidebar Header */
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem 1.2rem 0.8rem;
        border-bottom: 1px solid var(--sidebar-border);
        background: rgba(255, 255, 255, 0.05);
        min-height: 0;
        transition: padding 0.3s;
        flex-shrink: 0;
    }

    /* Header Profile Styles */
    .header-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.8rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .header-profile:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .header-profile-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
        object-fit: cover;
        transition: border-color 0.3s ease;
    }

    .header-profile:hover .header-profile-avatar {
        border-color: rgba(255, 255, 255, 0.5);
    }

    .header-profile-info {
        flex: 1;
        min-width: 0;
    }

    .header-profile-name {
        font-weight: 600;
        color: #fff;
        font-size: 0.95rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .header-profile-role {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Collapsed state for header profile */
    .modern-sidebar.collapsed .header-profile {
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        justify-content: center;
    }

    .modern-sidebar.collapsed .header-profile-info {
        display: none;
    }

    .modern-sidebar.collapsed .header-profile-avatar {
        width: 32px;
        height: 32px;
    }

    .modern-sidebar.collapsed .sidebar-header {
        padding: 0.7rem 0.5rem 0.5rem;
        justify-content: center;
        min-height: 0;
    }

    /* Navigation */
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 0;
        min-width: 0;
    }

    .nav-item {
        margin: 0.25rem 1rem;
        min-width: 0;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: var(--border-radius);
        transition: var(--transition-smooth);
        gap: 12px;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        position: relative;
    }

    .nav-link:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text);
        text-decoration: none;
    }

    .nav-link.active {
        background: var(--sidebar-active);
        color: var(--sidebar-text);
    }

    .nav-link i {
        width: 1.2rem;
        text-align: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .nav-link span {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modern-sidebar.collapsed .nav-link {
        justify-content: center;
        padding: 0.75rem 0.5rem;
        gap: 0;
    }

    .modern-sidebar.collapsed .nav-link span {
        opacity: 0;
        pointer-events: none;
        width: 0;
        overflow: hidden;
        display: none;
    }

    .modern-sidebar.collapsed .nav-link i {
        margin: 0;
        font-size: 1.3rem;
    }

    /* Dropdown styles */
    .dropdown-toggle {
        position: relative;
    }

    .dropdown-arrow {
        margin-left: auto;
        transition: transform 0.3s ease;
        flex-shrink: 0;
        width: 1rem;
        text-align: center;
    }

    .dropdown-toggle[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }

    .modern-sidebar.collapsed .dropdown-arrow {
        display: none !important;
        width: 0;
        margin: 0;
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .modern-sidebar.collapsed .dropdown-toggle {
        justify-content: center;
    }

    .modern-sidebar.collapsed .nav-link.dropdown-toggle {
        padding: 0.75rem 0.5rem;
        gap: 0;
    }

    /* Ensure main icon is centered in collapsed dropdown items */
    .modern-sidebar.collapsed .dropdown-toggle i:first-child {
        margin: 0 auto;
        display: block;
    }

    .modern-sidebar.collapsed .nav-link i:first-child {
        margin: 0 auto;
    }

    .submenu {
        margin-left: 1rem;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .submenu-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: var(--border-radius);
        transition: var(--transition-smooth);
        gap: 12px;
        font-size: 0.9rem;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
    }

    .submenu-link:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text);
        text-decoration: none;
    }

    .submenu-link.active {
        background: var(--sidebar-active);
        color: var(--sidebar-text);
    }

    .submenu-link i {
        width: 1rem;
        text-align: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .submenu-link span {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modern-sidebar.collapsed .submenu {
        display: none;
    }

    /* Logout button */
    .logout-btn {
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        padding: 0.75rem 1rem;
        color: inherit;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
        gap: 12px;
        border-radius: var(--border-radius);
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
    }

    .logout-btn:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text);
    }

    .logout-btn i {
        width: 1.2rem;
        text-align: center;
        flex-shrink: 0;
    }

    .logout-btn span {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modern-sidebar.collapsed .logout-btn {
        justify-content: center;
        padding: 0.75rem 0.5rem;
    }

    .modern-sidebar.collapsed .logout-btn span {
        display: none;
    }

    .modern-sidebar.collapsed .logout-btn i {
        margin: 0;
        font-size: 1.3rem;
    }

    /* Sidebar backdrop for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    @media (min-width: 769px) {
        .sidebar-overlay {
            display: none;
        }
    }

    /* Mobile toggle button */
    .mobile-sidebar-toggle {
        position: fixed;
        top: 1rem;
        left: 1rem;
        width: 48px;
        height: 48px;
        background: var(--sidebar-bg);
        border: 2px solid var(--sidebar-border);
        border-radius: 12px;
        color: white;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-smooth);
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .mobile-sidebar-toggle:hover {
        background: var(--sidebar-hover);
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .mobile-sidebar-toggle i {
        font-size: 1.25rem;
        transition: var(--transition-smooth);
    }

    .mobile-sidebar-toggle.active i {
        transform: rotate(180deg);
    }

    @media (max-width: 768px) {
        .mobile-sidebar-toggle {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-sidebar-toggle {
            display: none !important;
        }
    }

    /* Content wrapper */
    .content-wrapper {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
        min-height: 100vh;
        background: #f8f9fa;
        height: auto;
    }

    /* Header styles */
    .main-header {
        background: white;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    .header-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .brand-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .brand-logo {
        height: 56px;
        width: auto;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .brand-text-area {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .brand-text {
        font-size: 1.25rem;
        color: #764ba2;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .brand-subtext {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
    }

    .search-container {
        width: 100%;
        max-width: 400px;
        position: relative;
    }

    .profile-icon {
        font-size: 1.5rem;
        color: #764ba2;
        background: #fff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .main-header .btn-link {
        color: #764ba2;
        font-size: 1.5rem;
        background: none;
        border: none;
        padding: 0;
        margin-left: 1rem;
        transition: color 0.2s;
    }

    .main-header .btn-link:hover {
        color: #5b3b91;
    }

    /* Search box styles */
    .search-box {
        position: relative;
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border-radius: 25px;
        border: 1px solid #dee2e6;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .search-box:focus-within {
        border-color: #764ba2;
        box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
    }

    .search-icon {
        padding: 0 15px;
        color: #6c757d;
        font-size: 1rem;
    }

    .search-input {
        border: none;
        background: transparent;
        padding: 0.75rem 0;
        font-size: 0.95rem;
        flex: 1;
        outline: none;
        box-shadow: none !important;
    }

    .search-input:focus {
        box-shadow: none !important;
        border: none !important;
        outline: none !important;
    }

    .search-btn {
        background: none;
        border: none;
        padding: 0 15px;
        color: #6c757d;
        cursor: pointer;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .search-btn:hover {
        color: #764ba2;
    }

    /* Search dropdown styles */
    .search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        margin-top: 0.5rem;
    }

    .search-dropdown-content {
        padding: 0.5rem;
    }

    .search-suggestions,
    .search-results {
        margin-bottom: 0.5rem;
    }

    .search-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        color: #6c757d;
    }
    </style>

    {{-- Global UI Styles (e.g. from your helper) --}}
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}

    {{-- Chat CSS + any overrides --}}
    @stack('styles')
</head>
<body>
<div class="admin-container">
    <!-- Include Sidebar Component -->
    @include('admin.admin-layouts.admin-sidebar')

    <!-- Main Content Area -->
    <div class="main-content-area" id="mainContentArea">
        <!-- Include Header Component -->
        @include('admin.admin-layouts.admin-header')

        <!-- Page Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Admin Layout JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('modernSidebar');
    const backdrop = document.getElementById('sidebarOverlay');
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const mainContentArea = document.getElementById('mainContentArea');

    // Toggle sidebar function
    function toggleSidebar() {
        if (window.innerWidth >= 769) {
            // Desktop: Toggle collapsed state
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                
                // Update content margin
                if (sidebar.classList.contains('collapsed')) {
                    mainContentArea.style.marginLeft = '70px';
                } else {
                    mainContentArea.style.marginLeft = '280px';
                }
            }
        } else {
            // Mobile: Toggle visibility
            if (sidebar) {
                sidebar.classList.toggle('mobile-open');
            }
            if (backdrop) {
                backdrop.classList.toggle('active');
            }
            
            // Prevent body scroll when sidebar is open
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }
    }

    // Close sidebar (mobile)
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
        }
        if (backdrop) {
            backdrop.classList.remove('active');
        }
        document.body.style.overflow = 'auto';
    }

    // Make entire sidebar clickable to toggle
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            // Only toggle if we're not clicking on navigation links or forms
            if (!e.target.closest('.nav-link') && 
                !e.target.closest('.submenu-link') && 
                !e.target.closest('form') && 
                !e.target.closest('button') &&
                !e.target.closest('a')) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            }
        });
    }

    // Mobile toggle button
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Backdrop click to close (mobile)
    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 769) {
            // Desktop mode
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
            }
            if (backdrop) {
                backdrop.classList.remove('active');
            }
            document.body.style.overflow = 'auto';
            
            // Set proper margin
            if (sidebar && sidebar.classList.contains('collapsed')) {
                mainContentArea.style.marginLeft = '70px';
            } else if (sidebar) {
                mainContentArea.style.marginLeft = '280px';
            }
        } else {
            // Mobile mode
            if (sidebar) {
                sidebar.classList.remove('collapsed');
            }
            mainContentArea.style.marginLeft = '0';
        }
    });

    // Initialize proper layout on load
    function initializeLayout() {
        if (window.innerWidth >= 769) {
            if (sidebar) {
                mainContentArea.style.marginLeft = '280px';
            }
        } else {
            mainContentArea.style.marginLeft = '0';
        }
    }

    // Initialize layout
    initializeLayout();
    
    // Ensure body can scroll on page load
    document.body.style.overflow = 'auto';
    document.documentElement.style.overflow = 'auto';

    // Chat Offcanvas trigger
    var chatBtn = document.getElementById('chatTriggerBtn');
    if (chatBtn) {
        chatBtn.addEventListener('click', function() {
            var chatOffcanvas = new bootstrap.Offcanvas(document.getElementById('chatOffcanvas'));
            chatOffcanvas.show();
        });
    }
});

// Search functionality
let searchTimeout;
let currentSearchType = 'all';

function handleSearchInput() {
    const searchInput = document.getElementById('universalSearchInput');
    const query = searchInput.value.trim();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // If query is empty, hide dropdown
    if (query.length === 0) {
        hideSearchDropdown();
        return;
    }
    
    // Show loading and perform search after delay
    showSearchLoading(true);
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
}

function performSearch(query = null) {
    const searchInput = document.getElementById('universalSearchInput');
    const searchQuery = query || searchInput.value.trim();
    
    if (searchQuery.length === 0) {
        hideSearchDropdown();
        return;
    }

    showSearchLoading(true);
    
    // Make AJAX request to search endpoint
    fetch(`/admin/search?q=${encodeURIComponent(searchQuery)}&type=${currentSearchType}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        showSearchLoading(false);
        displaySearchResults(data);
    })
    .catch(error => {
        console.error('Search error:', error);
        showSearchLoading(false);
    });
}

function showSearchDropdown() {
    const dropdown = document.getElementById('searchResultsDropdown');
    if (dropdown) {
        dropdown.style.display = 'block';
    }
}

function hideSearchDropdown() {
    const dropdown = document.getElementById('searchResultsDropdown');
    if (dropdown) {
        setTimeout(() => {
            dropdown.style.display = 'none';
        }, 200);
    }
}

function showSearchLoading(show) {
    const loading = document.getElementById('searchLoading');
    if (loading) {
        if (show) {
            loading.classList.remove('d-none');
        } else {
            loading.classList.add('d-none');
        }
    }
}

function displaySearchResults(data) {
    const resultsContainer = document.getElementById('searchResults');
    if (!resultsContainer) return;

    resultsContainer.innerHTML = '';

    if (data.results && data.results.length > 0) {
        data.results.forEach(result => {
            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item p-2 border-bottom';
            
            // Create avatar or icon element
            let avatarElement = '';
            if (result.avatar && (result.type === 'student' || result.type === 'professor')) {
                avatarElement = `<img src="${result.avatar}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="Profile">`;
            } else {
                avatarElement = `<i class="bi ${result.icon || 'bi-search'} me-2"></i>`;
            }
            
            resultItem.innerHTML = `
                <div class="d-flex align-items-center">
                    ${avatarElement}
                    <div>
                        <div class="fw-medium">${result.title}</div>
                        <small class="text-muted">${result.subtitle || ''}</small>
                    </div>
                </div>
            `;
            resultItem.addEventListener('click', () => {
                if (result.url) {
                    window.location.href = result.url;
                }
            });
            resultsContainer.appendChild(resultItem);
        });
    } else {
        resultsContainer.innerHTML = '<div class="p-3 text-muted text-center">No results found</div>';
    }
}
</script>

@stack('scripts')

<!-- Include Global Chat Component -->
@include('components.global-chat')

<!-- Include Real-time Chat Component -->
@include('components.realtime-chat')
</body>
</html>
