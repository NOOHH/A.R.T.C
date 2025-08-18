<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', App\Helpers\UIHelper::getSiteTitle())</title>
    
    @php
        // Get user info for global variables - check all relevant guards
        // Use try-catch to handle database connection issues in multi-tenant setup
        $user = null;
        $isLoggedIn = false;
        
        try {
            $user = Auth::user() ?: Auth::guard('smartprep')->user() ?: Auth::guard('admin')->user();
            
            // Check if user is actually logged in via Laravel Auth or valid session
            $isLoggedIn = Auth::check() || Auth::guard('smartprep')->check() || Auth::guard('admin')->check() || session('logged_in') === true;
        } catch (Exception $e) {
            // If database query fails, fallback to session data
            $isLoggedIn = session('logged_in') === true;
        }
        
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
    @endphp

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        window.myId = @json($isLoggedIn && $user ? $user->id : null);
        window.myName = @json($isLoggedIn && $user ? $user->name : 'Guest');
        window.isAuthenticated = @json($isLoggedIn && (bool) $user);
        window.userRole = @json($isLoggedIn && $user ? $user->role : 'guest');
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
        
        console.log('Navbar Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>
    
    {{-- Global UI Meta Tags and Styles --}}
    {!! App\Helpers\UIHelper::getPageHead() !!}
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
            <link rel="stylesheet" href="{{ asset('css/homepage/navbar.css') }}?v={{ time() }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getNavbarStyles() !!}
        {!! \App\Helpers\SettingsHelper::getFooterStyles() !!}
        {!! \App\Helpers\SettingsHelper::getProgramCardStyles() !!}
        
        /* Fix navbar hover issues */
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 8px 12px !important;
            border-radius: 6px;
        }
        
        .nav-link:hover {
            color: #5c2f91 !important;
            background: rgba(92, 47, 145, 0.1) !important;
            transform: translateY(-1px);
        }
        
        .nav-link:focus,
        .nav-link:active {
            color: #5c2f91 !important;
            background: rgba(92, 47, 145, 0.1) !important;
            outline: none !important;
            box-shadow: none !important;
        }
        
        /* Remove Bootstrap's default button-like appearance */
        .navbar-nav .nav-link {
            background-color: transparent !important;
            border: none !important;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus,
        .navbar-nav .nav-link:active {
            background-color: rgba(92, 47, 145, 0.1) !important;
            border: none !important;
        }
        
        /* Ensure modal styles are properly loaded */
        .programs-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .programs-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .programs-modal-content {
            background: white;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .programs-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .programs-modal-header h3 {
            margin: 0;
            font-weight: 600;
        }
        
        .close-modal {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.2s;
        }
        
        .close-modal:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .programs-modal-body {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }
        
        /* Fix dropdown item styling */
        .nav-item.dropdown .nav-link {
            cursor: pointer;
        }
        
        /* Ensure proper spacing and remove any Bootstrap button effects */
        .navbar-nav .nav-item {
            margin: 0 5px;
        }
        
        .navbar-nav .nav-item .nav-link {
            color: #222 !important;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Remove any button-like styling from Bootstrap */
        .navbar-light .navbar-nav .nav-link {
            color: #222 !important;
        }
        
        .navbar-light .navbar-nav .nav-link:hover,
        .navbar-light .navbar-nav .nav-link:focus {
            color: #5c2f91 !important;
            background: rgba(92, 47, 145, 0.1) !important;
        }
        
        /* Smooth icon transitions */
        .nav-link i {
            transition: transform 0.2s ease;
        }
        
        .nav-link:hover i {
            transform: scale(1.1);
        }
        
        /* Fix dropdown menu hover styles - More specific selectors to override external CSS */
        .navbar .dropdown-menu,
        .navbar-nav .dropdown-menu,
        .navbar .navbar-nav .dropdown-menu {
            border: none !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px !important;
            padding: 8px 0 !important;
            margin-top: 8px !important;
            background-color: white !important; /* White background */
            color: #222 !important; /* Dark text */
            max-height: none !important; /* Override external CSS max-height */
            overflow: visible !important; /* Override external CSS overflow */
            transform: none !important; /* Override external CSS transform */
            transition: none !important; /* Override external CSS transition */
            width: auto !important; /* Override external CSS width */
            min-width: 200px !important; /* Set minimum width */
        }
        
        .navbar .dropdown-menu .dropdown-item,
        .navbar-nav .dropdown-menu .dropdown-item,
        .navbar .navbar-nav .dropdown-menu .dropdown-item {
            padding: 10px 20px !important;
            transition: all 0.2s ease !important;
            background: transparent !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-weight: 500 !important;
            color: #222 !important; /* Dark text for white background */
            opacity: 1 !important; /* Override external CSS opacity */
            transform: none !important; /* Override external CSS transform */
            pointer-events: auto !important; /* Override external CSS pointer-events */
            text-align: left !important; /* Override external CSS text-align */
            white-space: normal !important; /* Override external CSS white-space */
            box-sizing: border-box !important; /* Override external CSS box-sizing */
        }
        
        .navbar .dropdown-menu .dropdown-item:hover,
        .navbar-nav .dropdown-menu .dropdown-item:hover,
        .navbar .navbar-nav .dropdown-menu .dropdown-item:hover {
            background: rgba(92, 47, 145, 0.1) !important; /* Light purple background on hover */
            color: #5c2f91 !important; /* Purple text on hover */
            transform: translateX(5px) !important;
            border-radius: 0 !important;
        }
        
        .navbar .dropdown-menu .dropdown-item:focus,
        .navbar-nav .dropdown-menu .dropdown-item:focus,
        .navbar .navbar-nav .dropdown-menu .dropdown-item:focus,
        .navbar .dropdown-menu .dropdown-item:active,
        .navbar-nav .dropdown-menu .dropdown-item:active,
        .navbar .navbar-nav .dropdown-menu .dropdown-item:active {
            background: rgba(92, 47, 145, 0.1) !important;
            color: #5c2f91 !important;
            outline: none !important;
            box-shadow: none !important;
        }
        
        /* Dropdown item icons */
        .navbar .dropdown-menu .dropdown-item i,
        .navbar-nav .dropdown-menu .dropdown-item i,
        .navbar .navbar-nav .dropdown-menu .dropdown-item i {
            transition: transform 0.2s ease;
            width: 16px;
            text-align: center;
        }
        
        .navbar .dropdown-menu .dropdown-item:hover i,
        .navbar-nav .dropdown-menu .dropdown-item:hover i,
        .navbar .navbar-nav .dropdown-menu .dropdown-item:hover i {
            transform: scale(1.1);
        }
        
        /* Special styling for logout button */
        .navbar .dropdown-menu .dropdown-item.text-danger:hover,
        .navbar-nav .dropdown-menu .dropdown-item.text-danger:hover,
        .navbar .navbar-nav .dropdown-menu .dropdown-item.text-danger:hover {
            background: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }
        
        /* Dropdown divider styling */
        .navbar .dropdown-menu .dropdown-divider,
        .navbar-nav .dropdown-menu .dropdown-divider,
        .navbar .navbar-nav .dropdown-menu .dropdown-divider {
            margin: 8px 0 !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Override external CSS dropdown hover behavior */
        .navbar .dropdown:hover .dropdown-menu,
        .navbar-nav .dropdown:hover .dropdown-menu,
        .navbar .navbar-nav .dropdown:hover .dropdown-menu {
            max-height: none !important;
            padding: 8px 0 !important;
        }
        
        /* Ensure dropdown items are always visible when dropdown is open */
        .navbar .dropdown.show .dropdown-menu .dropdown-item,
        .navbar-nav .dropdown.show .dropdown-menu .dropdown-item,
        .navbar .navbar-nav .dropdown.show .dropdown-menu .dropdown-item {
            opacity: 1 !important;
            transform: none !important;
            pointer-events: auto !important;
        }
        
        /* Additional overrides to ensure white background and proper styling */
        .navbar .dropdown-menu,
        .navbar-nav .dropdown-menu {
            background: white !important;
            background-color: white !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Force override any external CSS that might be setting dark backgrounds */
        .navbar .dropdown-menu *,
        .navbar-nav .dropdown-menu * {
            background-color: transparent !important;
        }
        
        .navbar .dropdown-menu,
        .navbar-nav .dropdown-menu {
            background: white !important;
            background-color: white !important;
        }
        
        /* Ensure proper z-index for dropdown */
        .navbar .dropdown-menu,
        .navbar-nav .dropdown-menu {
            z-index: 1050 !important;
        }
        
        /* Bootstrap dropdown specific overrides */
        .dropdown-menu.show {
            background: white !important;
            background-color: white !important;
            color: #222 !important;
        }
        
        /* Override any Bootstrap dark theme classes */
        .dropdown-menu[data-bs-popper="static"],
        .dropdown-menu[data-bs-popper="dynamic"] {
            background: white !important;
            background-color: white !important;
        }
        
        /* Force white background on all dropdown states */
        .dropdown-menu,
        .dropdown-menu.show,
        .dropdown-menu.show * {
            background: white !important;
            background-color: white !important;
        }
        
        /* Ensure text is dark on white background */
        .dropdown-menu,
        .dropdown-menu.show,
        .dropdown-menu .dropdown-item {
            color: #222 !important;
        }
        
        /* Position dropdown menu correctly when on the right side */
        .navbar-nav.ms-auto .dropdown-menu {
            right: 0 !important;
            left: auto !important;
            transform: none !important;
        }
        
        /* Ensure proper spacing for right-aligned dropdown */
        .navbar-nav.ms-auto .nav-item {
            margin-left: 15px;
        }
 
    </style>
    @stack('styles') {{-- ✅ to load page-specific styles --}}
</head>

<body class="body-background d-flex flex-column min-vh-100 @if(request()->routeIs('enrollment.*')) enrollment-page @endif">
    @php
        $settings = \App\Helpers\SettingsHelper::getSettings();
        $navbarSettings = \App\Models\UiSetting::getSection('navbar');
        $footerSettings = \App\Models\UiSetting::getSection('footer');
        
        $navbar = $navbarSettings ? $navbarSettings->toArray() : [];
        $footer = $footerSettings ? $footerSettings->toArray() : [];
    @endphp
    
    {{-- Bootstrap Navbar --}}
    @if (!View::hasSection('hide_navbar'))
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ \App\Helpers\SettingsHelper::getLogoUrl() }}" 
                     alt="Logo" class="logo me-2" style="height: 40px;"
                     onerror="this.src='{{ asset('images/ARTC_Logo.png') }}'">
                <strong>{{ $navbar['brand_name'] ?? 'Ascendo Review and Training Center' }}</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- All navigation links grouped together on the right -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="javascript:void(0)" id="navbarProgramsBtn" onclick="toggleProgramsModal(event)">
                            <i class="bi bi-book"></i> Review Programs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#about') }}">
                            <i class="bi bi-people"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#contact') }}">
                            <i class="bi bi-envelope"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        @if(session('user_id'))
                            {{-- User is logged in - show user name with dropdown --}}
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarUserDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ explode(' ', session('user_name'))[0] ?? 'User' }}
                            </a>
                            <ul class="dropdown-menu">
                                @if(session('user_role') === 'student')
                                    <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('student.settings') }}">
                                        <i class="bi bi-gear"></i> Settings
                                    </a></li>
                                @elseif(session('user_role') === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('student.logout') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        @else
                            {{-- User is not logged in - show Account dropdown --}}
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-plus"></i> Account
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('/login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a></li>
                                <li><a class="dropdown-item" href="{{ url('/signup') }}">
                                    <i class="bi bi-person-plus-fill"></i> Sign Up
                                </a></li>
                            </ul>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endif
    
    <!-- Programs Modal -->
    <div id="programsModal" class="programs-modal" style="display: none;">
        <div class="programs-modal-overlay" onclick="closeProgramsModal()"></div>
        <div class="programs-modal-content">
            <div class="programs-modal-header">
                <h3>Review Programs</h3>
                <button type="button" class="close-modal" onclick="closeProgramsModal()">&times;</button>
            </div>
            <div class="programs-modal-body">
                <div id="programsModalList">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading programs...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <main class="main-content flex-grow-1">
        @yield('content')
    </main>

    <!-- Only keep the new custom footer below, remove the old duplicate footer -->
     @unless (View::hasSection('hide_footer'))
    <footer class="custom-footer mt-5">
        <div class="container">
            <div class="footer-top d-flex justify-content-between align-items-center flex-wrap py-4">
                <div class="footer-logo mb-3 mb-md-0">
                    <img src="{{ asset('images/ARTC_Logo.png') }}" alt="ARTC Logo" style="height: 40px;">
                    <span class="footer-title ms-2">{{ $navbar['brand_name'] ?? 'Ascendo Review and Training Center' }}</span>
                </div>
                <div class="footer-social">
                    <a href="#" class="footer-social-icon"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="footer-social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="footer-social-icon"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="footer-social-icon"><i class="bi bi-x"></i></a>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-links row py-3">
                <div class="col-md-3 col-6 mb-3">
                    <ul class="list-unstyled">
                        <li><a href="#">Review Programs</a></li>
                        <li><a href="#">Learning Modalities</a></li>
                        <li><a href="#"></a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <ul class="list-unstyled">
                        <li><a href="#">Support</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#"></a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <ul class="list-unstyled">
                        <li><a href="#"></a></li>
                        <li><a href="#"></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center py-3">
                <div class="footer-copyright">
                    &copy; {{ date('Y') }} ARTC. All rights reserved.
                </div>
                <div class="footer-policies">
                    <a href="#">Terms and Conditions</a>
                    <a href="#">Privacy Statement</a>
                    <a href="#">Cookie Policy</a>
                    <a href="#">Data Protection</a>
                    <a href="#">Trademarks</a>
                    <a href="#">Accessibility</a>
                </div>
            </div>
        </div>
    </footer>
 @endunless
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Load programs for navbar modal
    document.addEventListener('DOMContentLoaded', function() {
        loadProgramsModal();
        initNavbarScroll();
    });
    
    // Navbar scroll effect
    function initNavbarScroll() {
        const navbar = document.querySelector('.navbar');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
    
    // Toggle programs modal
    function toggleProgramsModal(event) {
        event.preventDefault();
        event.stopPropagation();
        const modal = document.getElementById('programsModal');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // Load programs when modal is opened
            loadProgramsModal();
        } else {
            closeProgramsModal();
        }
    }
    
    // Close programs modal
    function closeProgramsModal() {
        const modal = document.getElementById('programsModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function loadProgramsModal() {
        fetch('/api/programs')
            .then(response => response.json())
            .then(data => {
                const modalList = document.getElementById('programsModalList');
                if (!modalList) return;
                
                modalList.innerHTML = '';
                
                if (data.success && data.data && data.data.length > 0) {
                    // Add "View All Programs" option first
                    const viewAllItem = document.createElement('a');
                    viewAllItem.href = '/review-programs';

                    modalList.appendChild(viewAllItem);
                    
                    // Add individual programs
                    data.data.forEach(program => {
                        const programItem = document.createElement('a');
                        programItem.href = `/profile/program/${program.program_id}`;
                        programItem.className = 'program-modal-item';
                        programItem.innerHTML = `
                            <i class="bi bi-book"></i>
                            <div class="program-modal-item-content">
                                <div class="program-modal-item-title">${program.program_name}</div>
                                <p class="program-modal-item-desc">${program.program_description ? 
                                    (program.program_description.length > 100 ? 
                                        program.program_description.substring(0, 100) + '...' : 
                                        program.program_description) 
                                    : 'Professional review program'}</p>
                            </div>
                        `;
                        modalList.appendChild(programItem);
                    });
                } else {
                    // No programs found
                    modalList.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">No Programs Available</h5>
                            <p class="text-muted mb-0">Check back later for new programs</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading programs:', error);
                const modalList = document.getElementById('programsModalList');
                if (modalList) {
                    modalList.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #dc3545;"></i>
                            <h5 class="mt-3 text-danger">Error Loading Programs</h5>
                            <p class="text-muted mb-0">Please try again later</p>
                        </div>
                    `;
                }
            });
    }
    
    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProgramsModal();
        }
    });
    </script>
    
    @stack('scripts') {{-- Ensure page-specific scripts are loaded before </body> --}}


    

</body>

</html>
