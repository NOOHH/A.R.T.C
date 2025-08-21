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
            $sessionUser = (object) [
                'id' => $_SESSION['user_id'] ?? session('user_id'),
                'name' => $_SESSION['user_name'] ?? session('user_name') ?? 'Guest',
                'role' => $_SESSION['user_type'] ?? session('user_role') ?? 'guest'
            ];
            if ($sessionUser->id) {
                $user = $sessionUser;
            }
        }
        // Ensure moduleManagementEnabled is always available
        if (!isset($moduleManagementEnabled)) {
            $moduleManagementEnabled = \App\Models\AdminSetting::where('setting_key', 'professor_module_management_enabled')->value('setting_value') === '1';
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

    <!-- Professor-specific CSS (overriding admin CSS conflicts) -->
    <style>
    /* Override admin CSS conflicts */
    .admin-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
    }
    
    .main-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 68px);
        overflow: hidden;
    }
    
    .page-content {
        position: relative;
        z-index: 1;
        background: #f8f9fa;
        min-height: calc(100vh - 68px);
        margin-left: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .content-wrapper {
        position: relative;
        z-index: 1;
        background: #f8f9fa;
        padding: 1rem;
        min-height: calc(100vh - 68px);
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    /* Ensure header stays at top and doesn't interfere with content */
    .main-header {
        position: relative;
        z-index: 1000;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        height: 68px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        padding: 0 2rem;
    }
    .brand-container {
        min-width: 320px;
        display: flex;
        align-items: center;
        gap: 1rem;
        height: 68px;
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
    /* Navbar improvements */
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
    .search-container {
        width: 100%;
        max-width: 400px;
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
    
    /* Ensure content is not rendered inside header */
    .main-header .content-wrapper,
    .main-header .page-content,
    .main-header .modules-container {
        display: none !important;
    }
    
    /* Force proper content positioning */
    .content-wrapper > * {
        position: relative !important;
        z-index: 1 !important;
    }
    
    /* Professor-specific sidebar styles */
    .modern-sidebar {
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Additional professor layout styles */
    .content-below-search {
        display: flex;
        flex: 1;
        min-height: 0;
        height: calc(100vh - 68px);
    }
    
    /* Ensure proper flex layout */
    .admin-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
    }
    
    /* Sidebar positioning */
    .modern-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        z-index: 1001;
        overflow-y: auto;
        transition: all 0.3s ease;
    }
    
    .modern-sidebar.collapsed {
        width: 70px;
    }
    
    .modern-sidebar.active {
        transform: translateX(0);
    }
    
    @media (max-width: 768px) {
        .modern-sidebar {
            transform: translateX(-100%);
            width: 280px;
        }
    }
    
    /* Main content area */
    .main-content {
        margin-left: 280px;
        transition: margin-left 0.3s ease;
    }
    
    .main-content.sidebar-collapsed {
        margin-left: 70px;
    }
    
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }
    }
    
    /* Overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    </style>

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
    
         /* Sidebar Header Styles */
     .sidebar-header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 1.5rem 1.5rem 1rem;
         border-bottom: 1px solid rgba(255, 255, 255, 0.1);
         background: rgba(255, 255, 255, 0.05);
     }
     
     .sidebar-brand {
         display: flex;
         align-items: center;
         gap: 12px;
     }
     
     .sidebar-brand i {
         font-size: 1.8rem;
         color: #ffd700;
     }
     
     .brand-title {
         font-size: 1rem;
         font-weight: 700;
         color: white;
         letter-spacing: 0.5px;
     }
    
    .sidebar-toggle-btn {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    
    .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-toggle-btn i {
        transition: transform 0.3s ease;
    }
    
         /* Collapsed sidebar states */
     .modern-sidebar.collapsed .brand-title {
         opacity: 0;
         pointer-events: none;
     }
     
     .modern-sidebar.collapsed .sidebar-toggle-btn {
         transform: rotate(180deg);
         position: absolute;
         right: 4px;
         top: 35px;
         transform: translateY(-50%) rotate(180deg);
         background: rgba(255, 255, 255, 0.1);
         border-radius: 50%;
         width: 24px;
         height: 24px;
         display: flex;
         align-items: center;
         justify-content: center;
         z-index: 10;
         padding: 0;
     }
    .modern-sidebar.collapsed .sidebar-toggle-btn i {
  font-size: 1rem;       /* shrink the chevron icon */
}

/* make toggler smaller when sidebar is open */
.modern-sidebar:not(.collapsed) .sidebar-toggle-btn {
  width: 24px;             /* shrink the button container */
  height: 24px;
  padding: 0;              /* remove extra padding */
  background: transparent; /* keep it clean */
  right: 5px;              /* adjust as needed */
  top: 35px;               /* same vertical positioning */
  transform: translateY(-50%);  
}

.modern-sidebar:not(.collapsed) .sidebar-toggle-btn i {
  font-size: 1rem;         /* shrink the chevron */
}

    
    .modern-sidebar.collapsed .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
         .modern-sidebar.collapsed .sidebar-header {
         padding: 1rem 0.5rem;
         justify-content: center;
     }
     
     .modern-sidebar.collapsed .sidebar-brand {
         opacity: 0;
         pointer-events: none;
     }
    
         /* User Profile Section Styles */
     .user-profile {
         padding: 1rem 0.5rem 1rem 1.5rem;
         border-top: 1px solid rgba(255, 255, 255, 0.1);
         background: rgba(255, 255, 255, 0.05);
         margin-top: -16px;
         flex-shrink: 0;
     }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 0.5rem;
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
    
    .user-details h6 {
        margin: 0;
        color: white;
        font-size: 0.9rem;
        font-weight: 600;
        line-height: 1.2;
    }
    
    .user-details span {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.6);
        display: block;
    }
    
    /* Collapsed state for user profile */
    .modern-sidebar.collapsed .user-profile {
        padding: 1rem 0.5rem;
        text-align: center;
    }
    
    .modern-sidebar.collapsed .user-details {
        display: none;
    }
    
    .modern-sidebar.collapsed .user-avatar {
        margin: 0 auto;
    }
    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
    }
    .profile-name {
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .profile-role {
        color: #9CA3AF;
        font-size: 12px;
        font-weight: 400;
        background: none !important;
        border-radius: 0;
        padding: 0;
        display: inline-block;
        margin-top: 4px;
        text-transform: none;
    }
    </style>
    <style>
        /* Flex container for sidebar and main content */
        .content-below-search {
            display: flex;
            flex: 1;
            min-height: 0;
            height: calc(100vh - 68px); /* Subtract header height */
        }
        /* Main content area fills height below header */
        .page-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            min-height: 0;
            height: 100%;
            margin-left: 0;
        }
        /* Inner wrapper for page content padding and scrolling */
        .content-wrapper {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            min-height: 0;
            margin-left: 0;
            background: #f8f9fa;
        }
        
        /* Ensure header stays at top */
        .main-header {
            position: relative;
            z-index: 1000;
            background: white;
            border-bottom: 1px solid #dee2e6;
        }
        
        /* Ensure main wrapper doesn't overlap header */
        .main-wrapper {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 68px);
            overflow: hidden;
        }
        
        /* Override any admin dashboard CSS that might interfere */
        .admin-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Ensure content is not rendered inside header */
        .main-header .content-wrapper,
        .main-header .page-content,
        .main-header .modules-container {
            display: none !important;
        }
        
        /* Force proper content positioning */
        .content-wrapper > * {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
    <style>
/* --- Sidebar User Profile Collapsed State Fixes --- */
.user-profile {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    width: 100%;
    box-sizing: border-box;
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
}
.modern-sidebar.collapsed .user-info {
    justify-content: center;
    width: 100%;
}
.modern-sidebar.collapsed .profile-info {
    display: none !important;
}
.modern-sidebar.collapsed .user-avatar {
    margin: 0 auto;
}
.modern-sidebar {
    overflow-x: hidden;
}
</style>
<style>
/* --- Sidebar Brand Subtitle Below Title & Collapsed Header Fixes --- */
.brand-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    line-height: 1.1;
}
.brand-title {
    font-size: 1rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.5px;
    margin-bottom: 0;
}
.brand-subtitle {
    font-size: 0.8rem;
    color: #9CA3AF;
    font-weight: 500;
    letter-spacing: 0.5px;
    margin-top: 2px;
    margin-bottom: 0;
}
.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.2rem 1.2rem 0.8rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    min-height: 0;
    transition: padding 0.3s;
}
.modern-sidebar.collapsed .sidebar-header {
    padding: 0.7rem 0.5rem 0.5rem;
    justify-content: center;
    min-height: 0;
}
.modern-sidebar.collapsed .brand-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.modern-sidebar.collapsed .brand-title,
.modern-sidebar.collapsed .brand-subtitle {
    opacity: 0;
    pointer-events: none;
    height: 0;
    margin: 0;
    padding: 0;
}
.modern-sidebar.collapsed .sidebar-brand img {
    opacity: 1 !important;
    display: block !important;
    height: 32px;
    width: auto;
    margin-right: 8px;
}
</style>
</head>
<body style="overflow-x: hidden;">
<div class="admin-container">
    <!-- Top Header -->
    @php
        // Get brand name from NavbarComposer data (tenant-specific) or fallback
        $brandName = $navbar['brand_name'] ?? 
                     $settings['navbar']['brand_name'] ?? 
                     $navbarBrandName ?? 
                     'Ascendo Review & Training Center';
        
        // Get brand logo from NavbarComposer data (tenant-specific) or fallback
        $brandLogo = $navbar['brand_logo'] ?? $settings['navbar']['brand_logo'] ?? null;
        $defaultLogo = asset('images/ARTC_logo.png');
    @endphp
    
    <header class="main-header">
        <div class="header-left">
            <!-- Brand Logo and Text -->
            <div class="brand-container d-flex align-items-center gap-3" style="height:68px;">
                @if($brandLogo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($brandLogo) }}" 
                         alt="{{ $brandName }}" 
                         class="brand-logo" 
                         style="height:56px; width:auto; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); background:#fff;"
                         onerror="this.src='{{ $defaultLogo }}'">
                @else
                    <img src="{{ $defaultLogo }}" alt="{{ $brandName }}" class="brand-logo" style="height:56px; width:auto; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); background:#fff;">
                @endif
                <div class="brand-text-area d-flex flex-column justify-content-center">
                    <span class="brand-text fw-bold" style="font-size:1.25rem; color:#764ba2; letter-spacing:1px;">{{ $brandName }}</span>
                    <span class="brand-subtext text-muted" style="font-size:0.9rem;">Professor Portal</span>
                </div>
            </div>
        </div>

        <div class="header-center">
            <!-- Universal Search -->
            <div class="search-container">
                @include('components.universal-search')
            </div>
        </div>

        <div class="header-right">
 
            <!-- Chat Icon Button -->
            <button class="btn btn-link p-0 ms-2" id="chatTriggerBtn" title="Open Chat" style="font-size: 1.5rem; color: #764ba2;">
                <i class="bi bi-chat-dots"></i>
            </button>
 
            <!-- Mobile Profile Icon -->
            <div class="profile-icon">👤</div>
        </div>
    </header>
    <div class="main-wrapper">
        <div class="content-below-search">
            @include('professor.professor-layouts.professor-sidebar')
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
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.getElementById('modernSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggleBtn && sidebar && overlay) {
        sidebarToggleBtn.addEventListener('click', function() {
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
    // Chat Offcanvas trigger
    var chatBtn = document.getElementById('chatTriggerBtn');
    if (chatBtn) {
      chatBtn.addEventListener('click', function() {
        var chatOffcanvas = new bootstrap.Offcanvas(document.getElementById('chatOffcanvas'));
        chatOffcanvas.show();
      });
    }
    // Removed custom dropdown JS, Bootstrap handles collapse
});
</script>

@stack('scripts')

<!-- Include Global Chat Component -->
@include('components.global-chat')

<!-- Include Real-time Chat Component -->
@include('components.realtime-chat')
</body>
</html>
