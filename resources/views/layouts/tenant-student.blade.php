<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ARTC - Academic Resource and Training Center')</title>
    
    @php
        // Proper authentication logic that matches student dashboard
        $user = null;
        $isLoggedIn = false;
        
        // Check if user is logged in via Laravel Auth
        if (Auth::check()) {
            $user = Auth::user();
            $isLoggedIn = true;
        }
        // Check if user is logged in via session (for student authentication)
        elseif (session('user_id') && session('user_role')) {
            $user = (object) [
                'id' => session('user_id'),
                'name' => session('user_name') ?? session('user_firstname') . ' ' . session('user_lastname'),
                'role' => session('user_role'),
                'email' => session('user_email')
            ];
            $isLoggedIn = true;
        }
        
        // Force student role if session indicates student
        if ($isLoggedIn && session('user_role') === 'student') {
            $user->role = 'student';
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
        
        console.log('App Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/homepage/homepage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-navbar.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            min-height: 100vh;
        }
        
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
        }
        
        .footer {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        /* Override student navbar for app layout */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        /* Hide sidebar toggle button for app layout */
        .sidebar-toggle-btn {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Student Navbar -->
    <header class="main-header">
        <div class="header-left">
            <a href="{{ route('home') }}" class="brand-link">
                @php
                    $brandingSettings = \App\Helpers\UiSettingsHelper::getSection('navbar');
                    $logoUrl = $brandingSettings['brand_logo'] ?? null;
                    $brandName = $brandingSettings['brand_name'] ?? 'Ascendo Review and Training Center';
                @endphp
                
                @if($logoUrl)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logoUrl) }}" alt="Logo">
                @else
                    <img src="{{ asset('images/ARTC_logo.png') }}" alt="Logo">
                @endif
                <div class="brand-text">
                    {{ str_replace(' and ', '<br>and ', $brandName) }}
                </div>
            </a>
        </div>

        <div class="header-search">
            @include('components.student-search')
        </div>

        <div class="header-right">
            <span class="notification-icon chat-trigger"
                  data-bs-toggle="offcanvas"
                  data-bs-target="#chatOffcanvas"
                  aria-label="Open chat"
                  role="button">
                <i class="bi bi-chat-dots"></i>
            </span>
            <span class="profile-icon">
                @php
                    $student = \App\Models\Student::where('user_id', session('user_id'))->first();
                    $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
                @endphp
                
                @if($profilePhoto)
                    <img src="{{ asset('storage/profile-photos/' . $profilePhoto) }}" 
                         alt="Profile" 
                         class="navbar-profile-image">
                @else
                    <div class="navbar-profile-placeholder">
                        {{ substr(session('user_firstname', 'U'), 0, 1) }}{{ substr(session('user_lastname', 'U'), 0, 1) }}
                    </div>
                @endif
            </span>
        </div>
    </header>
    
    @include('components.global-chat')

    <!-- Main Content -->
    <main class="main-content flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-2">ARTC</h6>
                    <p class="text-muted small mb-0">Academic Resource and Training Center</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} ARTC. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>
