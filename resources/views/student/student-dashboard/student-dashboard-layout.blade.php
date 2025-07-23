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
    <style>
        .sidebar-close {
            display: none; /* Hidden by default on desktop */
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
        }
        @media (max-width: 991.98px) {
            .modern-sidebar.active .sidebar-close {
                display: block; /* Show only on mobile when sidebar is active */
            }
        }
        .modern-sidebar, .sidebar-content {
            overflow-y: hidden !important;
            overflow-x: hidden !important;
        }
        .modern-sidebar {
            width: 240px;
            transform: translateX(0);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .modern-sidebar.collapsed {
            transform: translateX(-180px);
        }
    </style>
</head>
<body>
<div class="student-container">
    <!-- Top Header -->
@include('components.student-navbar')
    <!-- Main Container using Bootstrap Grid -->
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            @php
                // Prepare student programs data for sidebar
                $studentPrograms = [];
                if ($user && $user->id) {
                    try {
                        $enrollments = \App\Models\Enrollment::where('user_id', $user->id)
                            ->with(['program', 'package'])
                            ->where('enrollment_status', 'approved')
                            ->get();
                        
                        foreach ($enrollments as $enrollmentData) {
                            if ($enrollmentData->program) {
                                $studentPrograms[] = [
                                    'program_id' => $enrollmentData->program->program_id,
                                    'program_name' => $enrollmentData->program->program_name,
                                    'package_name' => $enrollmentData->package ? $enrollmentData->package->package_name : 'No Package'
                                ];
                            }
                        }
                    } catch (Exception $e) {
                        // Fallback if there's an error
                        $studentPrograms = [];
                    }
                }
            @endphp
            
   @include('components.student-sidebar', ['studentPrograms' => $studentPrograms, 'user' => $user ?? null])

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

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const modernSidebar = document.getElementById('modernSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarReopenBtn = document.getElementById('sidebarReopenBtn');

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
            setTimeout(updateSidebarReopenBtn, 300);
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

    if (sidebarReopenBtn) {
        sidebarReopenBtn.addEventListener('click', function() {
            if (modernSidebar) {
                modernSidebar.classList.remove('collapsed');
                updateSidebarReopenBtn();
            }
        });
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
        updateSidebarReopenBtn();
    });

    // Handle dropdown animations with Bootstrap 5 integration
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                // Use Bootstrap's native collapse instance to toggle
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetElement);
                bsCollapse.toggle();
            }
        });
    });

    // Adjust parent 'show' class based on Bootstrap collapse events
    document.querySelectorAll('.collapse').forEach(collapseElement => {
        const parentNav = collapseElement.closest('.dropdown-nav');
        if (parentNav) {
            collapseElement.addEventListener('show.bs.collapse', function () {
                parentNav.classList.add('show');
            });
            collapseElement.addEventListener('hide.bs.collapse', function () {
                parentNav.classList.remove('show');
            });
        }
    });

    // Auto-open dropdowns that are marked as active
    document.querySelectorAll('.dropdown-nav.active .collapse').forEach(collapse => {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapse, {
            toggle: false
        });
        bsCollapse.show();
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

    function updateSidebarReopenBtn() {
        if (window.innerWidth >= 992 && modernSidebar && modernSidebar.classList.contains('collapsed')) {
            sidebarReopenBtn.style.display = 'block';
        } else {
            sidebarReopenBtn.style.display = 'none';
        }
    }

    // Initial state
    updateSidebarReopenBtn();
});
</script>

<!-- Include Global Chat Component -->
@include('components.global-chat')

</body>
</html>
