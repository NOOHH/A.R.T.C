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
            $sessionUser = (object) [
                'id' => session('user_id'),
                'name' => session('user_name') ?? 'Guest',
                'role' => session('user_role') ?? 'guest'
            ];
            if ($sessionUser->id) {
                $user = $sessionUser;
            }
        }
        
        // For professors, ensure we use the professor_id from session
        if (session('user_role') === 'professor' && session('professor_id')) {
            // Use professor_id as the primary ID for professors
            $user = (object) [
                'id' => session('professor_id'),
                'name' => session('user_name') ?? 'Professor',
                'role' => 'professor'
            ];
            
            // Ensure session variables are consistent
            session([
                'user_id' => session('professor_id'),
                'user_role' => 'professor',
                'user_type' => 'professor'
            ]);
        }
        
        // Force professor role for professor pages
        if ($user && (session('user_role') === 'professor' || session('user_type') === 'professor' || 
            (isset($user->role) && $user->role === 'professor'))) {
            $user->role = 'professor';
            session(['user_role' => 'professor', 'user_type' => 'professor']);
        }
        
        // Ensure moduleManagementEnabled is always available
        if (!isset($moduleManagementEnabled)) {
            $moduleManagementEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_module_management_enabled')->value('setting_value') === '1';
        }
        
        // Ensure announcementManagementEnabled is always available
        if (!isset($announcementManagementEnabled)) {
            $announcementManagementEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_announcement_management_enabled')->value('setting_value') === '1';
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
        
        // Debug session information
        console.log('Session Debug Info:', {
            sessionUserRole: @json(session('user_role')),
            sessionUserType: @json(session('user_type')),
            sessionUserId: @json(session('user_id')),
            sessionProfessorId: @json(session('professor_id')),
            sessionLoggedIn: @json(session('logged_in')),
            userObject: @json($user)
        });
        
        console.log('Professor Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>
    
    <!-- jQuery (required for dynamic dropdowns and AJAX in child views) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Professor-specific CSS -->
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
    .professor-container {
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
/* debug: outline all nav items so we can see if they’re in DOM */



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

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1;
    }

    .modern-sidebar.collapsed .sidebar-brand {
        opacity: 0;
        pointer-events: none;
        width: 0;
        overflow: hidden;
    }

    .brand-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        line-height: 1.1;
        min-width: 0;
        flex: 1;
    }

    .brand-title {
        font-size: 1rem;
        font-weight: 700;
        color: white;
        letter-spacing: 0.5px;
        margin-bottom: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .brand-subtitle {
        font-size: 0.8rem;
        color: var(--sidebar-text-muted);
        font-weight: 500;
        letter-spacing: 0.5px;
        margin-top: 2px;
        margin-bottom: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modern-sidebar.collapsed .brand-content {
        display: none;
    }

    .modern-sidebar.collapsed .brand-title,
    .modern-sidebar.collapsed .brand-subtitle {
        display: none;
    }

    .modern-sidebar.collapsed .sidebar-brand img {
        opacity: 1 !important;
        display: block !important;
        height: 32px;
        width: auto;
        margin: 0 auto;
    }

    /* User Profile Section */
    .user-profile {
        padding: 1rem 0.5rem 1rem 1.5rem;
        border-top: 1px solid var(--sidebar-border);
        background: rgba(255, 255, 255, 0.05);
        margin-top: -16px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .modern-sidebar.collapsed .user-profile {
        align-items: center !important;
        justify-content: center;
        width: 100%;
        padding: 1rem 0.5rem !important;
        text-align: center;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        min-width: 0;
    }

    .modern-sidebar.collapsed .user-info {
        justify-content: center;
        width: 100%;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #417d91, #5b8a9c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
        font-weight: bold;
        flex-shrink: 0;
    }

    .modern-sidebar.collapsed .user-avatar {
        margin: 0 auto;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        min-width: 0;
        flex: 1;
    }

    .modern-sidebar.collapsed .profile-info {
        display: none !important;
    }

    .profile-name {
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-role {
        color: var(--sidebar-text-muted);
        font-size: 12px;
        font-weight: 400;
        background: none !important;
        border-radius: 0;
        padding: 0;
        display: inline-block;
        margin-top: 4px;
        text-transform: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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

    .header-center {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-container {
        width: 100%;
        max-width: 400px;
    }

    .header-right {
        display: flex;
        align-items: flex-end;
        gap: 1.5rem;
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
    </style>

    {{-- Global UI Styles --}}
   

    {{-- Chat CSS + any overrides --}}
    @stack('styles')
</head>
<body>
<div class="professor-container">
    <!-- Include Sidebar Component -->
    @include('professor.professor-layouts.professor-sidebar')

    <!-- Main Content Area -->
    <div class="main-content-area" id="mainContentArea">
        <!-- Include Header Component -->
        @include('professor.professor-layouts.professor-header')

        <!-- Page Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Professor Layout JavaScript -->
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
</script>

@stack('scripts')

<!-- Include Global Chat Component -->
@include('components.global-chat')

<!-- Include Real-time Chat Component -->
@include('components.realtime-chat')
</body>
</html>
