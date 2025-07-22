<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', App\Helpers\UIHelper::getSiteTitle())</title>
    
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
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getNavbarStyles() !!}
        {!! \App\Helpers\SettingsHelper::getFooterStyles() !!}
        {!! \App\Helpers\SettingsHelper::getProgramCardStyles() !!}
 
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
                <strong>{{ $navbar['navbar_brand_name'] ?? 'Ascendo Review and Training Center' }}</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Universal Search Component (accessible to all users) -->
                <div class="navbar-nav me-auto">
                    <div class="nav-item" style="min-width: 300px;">
                        @include('components.universal-search')
                    </div>
                </div>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="javascript:void(0)" id="navbarProgramsBtn" 
                           onclick="toggleProgramsModal(event)">
                            <i class="bi bi-book"></i> Review Programs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)">
                            <i class="bi bi-people"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)">
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
                                <li><a class="dropdown-item" href="{{ url('/enrollment') }}">
                                    <i class="bi bi-person-plus-fill"></i> Sign Up
                                </a></li>
                            </ul>
                        @endif
                    </li>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('/login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a></li>
                                <li><a class="dropdown-item" href="{{ url('/enrollment') }}">
                                    <i class="bi bi-person-plus-fill"></i> Sign Up
                                </a></li>
                            </ul>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
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
    <footer class="custom-footer mt-5">
        <div class="container">
            <div class="footer-top d-flex justify-content-between align-items-center flex-wrap py-4">
                <div class="footer-logo mb-3 mb-md-0">
                    <img src="{{ asset('images/ARTC_Logo.png') }}" alt="ARTC Logo" style="height: 40px;">
                    <span class="footer-title ms-2">Ascendo Review <br>and Training Center</span>
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
                    viewAllItem.href = '/programs';
                    viewAllItem.className = 'program-modal-item view-all-programs';
                    viewAllItem.innerHTML = `
                        <i class="bi bi-list-ul"></i>
                        <div class="program-modal-item-content">
                            <div class="program-modal-item-title">View All Programs</div>
                            <p class="program-modal-item-desc">Browse all available review programs</p>
                        </div>
                    `;
                    modalList.appendChild(viewAllItem);
                    
                    // Add individual programs
                    data.data.forEach(program => {
                        const programItem = document.createElement('a');
                        programItem.href = `/programs/${program.program_id}`;
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

    <!-- Global Chat Component -->
@include('components.global-chat')

</body>

</html>
