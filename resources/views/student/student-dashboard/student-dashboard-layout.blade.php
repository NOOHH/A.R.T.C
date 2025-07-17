<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard')</title>

    @php
        // Force student authentication context - prioritize session data
        $user = null;
        
        // Get user data from Laravel session (priority 1)
        if (session('user_id') && session('user_role') === 'student') {
            $user = (object) [
                'id' => session('user_id'),
                'name' => session('user_name') ?? session('user_firstname') . ' ' . session('user_lastname'),
                'role' => 'student',
                'email' => session('user_email')
            ];
        }
        
        // If no valid student session, redirect to login
        if (!$user) {
            // Check if we have any session but wrong role
            if (session('user_id') && session('user_role') !== 'student') {
                // Clear session and redirect
                session()->flush();
                redirect()->route('login')->send();
            } else {
                // No session at all
                redirect()->route('login')->send();
            }
        }
    @endphp

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        window.myId = {{ $user ? $user->id : 'null' }};
        window.myName = @json(optional($user)->name ?? 'Guest');
        window.isAuthenticated = {{ $user ? 'true' : 'false' }};
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
        
        console.log('Student Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student/student-dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-sidebar.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('head')
    @stack('styles')
</head>
<body>
<div class="student-container">
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <!-- Hamburger Menu Button - Always visible -->
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
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
<span class="notification-icon chat-trigger"
      data-bs-toggle="offcanvas"     {{-- ✅ correct attribute --}}
      data-bs-target="#chatOffcanvas"
      aria-label="Open chat"
      role="button">
    <i class="bi bi-chat-dots"></i>
</span>
            <span class="profile-icon">👤</span>
        </div>
    </header>

    <!-- Main Container using Bootstrap Grid -->
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <!-- Modern Sliding Sidebar -->
            <aside class="modern-sidebar col-lg-3 col-xl-2" id="modernSidebar">
                <!-- Sidebar Header -->
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <i class="bi bi-mortarboard"></i>
                        <span class="brand-title">Student Portal</span>
                    </div>
                    <button class="sidebar-close" id="sidebarClose">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <nav class="sidebar-nav">
                        {{-- Dashboard --}}
                        <div class="nav-item">
                            <a href="{{ route('student.dashboard') }}" class="nav-link @if(Route::currentRouteName() === 'student.dashboard') active @endif">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>

                        {{-- Calendar --}}
                        <div class="nav-item">
                            <a href="{{ route('student.calendar') }}" class="nav-link @if(Route::currentRouteName() === 'student.calendar') active @endif">
                                <i class="bi bi-calendar3"></i>
                                <span>Calendar</span>
                            </a>
                        </div>

                        {{-- Meetings --}}
                        <li class="@if(Route::currentRouteName() === 'student.meetings') active @endif">
                            <a href="{{ route('student.meetings') }}" class="sidebar-link">
                                <span class="icon">🎥</span> Meetings
                            </a>
                        </li>

                        {{-- My Programs --}}
                        <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'student.course')) active show @endif">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#programsMenu">
                                <i class="bi bi-journal-bookmark"></i>
                                <span>My Programs</span>
                                <i class="bi bi-chevron-down dropdown-arrow"></i>
                            </a>
                            <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'student.course')) show @endif" id="programsMenu">
                                <div class="submenu">
                                    @php
                                        // Get student's enrolled programs
                                        $studentPrograms = [];
                                        if (session('user_id')) {
                                            $student = App\Models\Student::where('user_id', session('user_id'))->first();
                                            
                                            // Get enrollments - check both by student_id and user_id for flexibility
                                            $enrollmentsQuery = App\Models\Enrollment::with(['program', 'package']);
                                            
                                            if ($student && isset($student->student_id)) {
                                                // If student record exists, search by student_id
                                                $enrollmentsQuery->where('student_id', $student->student_id);
                                            } else {
                                                // If no student record, search by user_id
                                                $enrollmentsQuery->where('user_id', session('user_id'));
                                            }
                                            
                                            $enrollments = $enrollmentsQuery->get();
                                            
                                            foreach ($enrollments as $enrollment) {
                                                if ($enrollment->program && !$enrollment->program->is_archived) {
                                                    $studentPrograms[] = [
                                                        'program_id' => $enrollment->program->program_id,
                                                        'program_name' => $enrollment->program->program_name,
                                                        'package_name' => $enrollment->package ? $enrollment->package->package_name : 'Standard Package',
                                                        'plan_name' => $enrollment->enrollment_type ?? 'Standard Plan',
                                                        'learning_mode' => $enrollment->learning_mode ?? 'Synchronous',
                                                    ];
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    @forelse($studentPrograms as $program)
                                        <a href="{{ route('student.course', ['courseId' => $program['program_id']]) }}" 
                                           class="submenu-link @if(request()->route('courseId') == $program['program_id']) active @endif">
                                            <i class="bi bi-book"></i>
                                            <span class="program-info">
                                                <div class="program-name">{{ $program['program_name'] }}</div>
                                                <small class="program-details">{{ $program['package_name'] }}</small>
                                            </span>
                                        </a>
                                    @empty
                                        <div class="submenu-link disabled">
                                            <i class="bi bi-info-circle"></i>
                                            <span>No programs available. Contact administrator.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
                
                <!-- User Profile Section -->
                <div class="user-profile">
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ strtoupper(substr(optional($user)->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <h6>{{ optional($user)->name ?? 'Student' }}</h6>
                            <span>Student</span>
                        </div>
                    </div>
                    
                    <nav class="sidebar-nav">
                        <div class="nav-item">
                            <a href="{{ route('student.settings') }}" class="nav-link @if(Route::currentRouteName() === 'student.settings') active @endif">
                                <i class="bi bi-gear"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- Main Content using Bootstrap Grid -->
            <main class="col-lg-9 col-xl-10 main-content">
                <!-- Content Area -->
                <div class="container-fluid h-100">
                    <div class="row justify-content-center h-100">
                        <div class="col-12 col-md-11 col-lg-10 col-xl-9">
                            <div class="content-wrapper p-3">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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
    // Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const modernSidebar = document.getElementById('modernSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const contentWrapper = document.querySelector('.content-wrapper');

    // Bootstrap 5 compatible toggle function
    function toggleSidebar() {
        console.log('📱 Toggle sidebar called, window width:', window.innerWidth);
        
        if (window.innerWidth >= 992) {
            console.log('🖥️ Desktop mode: toggling collapsed state');
            // Desktop: Toggle collapsed state (Bootstrap lg and up)
            modernSidebar.classList.toggle('collapsed');
            console.log('Sidebar collapsed:', modernSidebar.classList.contains('collapsed'));
        } else {
            console.log('📱 Mobile mode: toggling sidebar visibility');
            // Mobile/Tablet: Toggle sidebar visibility
            if (modernSidebar) {
                modernSidebar.classList.toggle('active');
                console.log('Sidebar active:', modernSidebar.classList.contains('active'));
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
                console.log('Overlay active:', sidebarOverlay.classList.contains('active'));
            }
            document.body.style.overflow = modernSidebar && modernSidebar.classList.contains('active') ? 'hidden' : '';
        }
    }

    // Close sidebar function (mobile only)
    function closeSidebar() {
        if (window.innerWidth < 992) {
            if (modernSidebar) {
                modernSidebar.classList.remove('active');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
    }

    // Event listeners
    if (sidebarToggle) {
        console.log('✅ Sidebar toggle button found, adding event listener');
        sidebarToggle.addEventListener('click', function(e) {
            console.log('🎯 Sidebar toggle clicked');
            toggleSidebar();
        });
    } else {
        console.error('❌ Sidebar toggle button not found');
    }

    if (sidebarClose) {
        console.log('✅ Sidebar close button found, adding event listener');
        sidebarClose.addEventListener('click', function(e) {
            console.log('🎯 Sidebar close clicked');
            closeSidebar();
        });
    } else {
        console.error('❌ Sidebar close button not found');
    }

    if (sidebarOverlay) {
        console.log('✅ Sidebar overlay found, adding event listener');
        sidebarOverlay.addEventListener('click', function(e) {
            console.log('🎯 Sidebar overlay clicked');
            closeSidebar();
        });
    } else {
        console.error('❌ Sidebar overlay not found');
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            closeSidebar();
        } else {
            // Remove collapsed class on mobile
            if (modernSidebar) {
                modernSidebar.classList.remove('collapsed');
            }
        }
    });

    // Handle dropdown animations with Bootstrap 5 integration
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            const parentNav = this.closest('.dropdown-nav');
            
            if (targetElement) {
                // Toggle Bootstrap collapse
                const bsCollapse = new bootstrap.Collapse(targetElement, {
                    toggle: true
                });
                
                // Toggle parent nav class for styling
                if (parentNav) {
                    parentNav.classList.toggle('show');
                }
            }
        });
    });

    // Auto-open dropdowns that are marked as active
    document.querySelectorAll('.dropdown-nav.active').forEach(dropdown => {
        dropdown.classList.add('show');
        const collapseElement = dropdown.querySelector('.collapse');
        if (collapseElement) {
            collapseElement.classList.add('show');
        }
    });

    // Initialize sidebar state on page load - Bootstrap compatible
    if (window.innerWidth >= 992) {
        // Desktop: Show sidebar by default (Bootstrap lg and up)
        if (modernSidebar) {
            modernSidebar.classList.add('active');
        }
    } else {
        // Mobile/Tablet: Hide sidebar by default
        if (modernSidebar) {
            modernSidebar.classList.remove('active');
        }
    }
});
</script>

<!-- Include Global Chat Component -->
@include('components.global-chat')

</body>
</html>
