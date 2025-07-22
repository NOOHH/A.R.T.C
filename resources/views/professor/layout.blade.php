<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Professor Dashboard') - {{ config('app.name', 'A.R.T.C') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .main-content {
            padding: 20px 0;
        }
        /* Fix dropdown button styling */
        .dropdown-item button {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 0.375rem 1rem;
            color: inherit;
        }
        .dropdown-item button:hover {
            background-color: var(--bs-dropdown-link-hover-bg);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('professor.dashboard') }}">
                <i class="fas fa-chalkboard-teacher me-2"></i>A.R.T.C Professor Portal
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}" href="{{ route('professor.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.meetings*') ? 'active' : '' }}" href="{{ route('professor.meetings') }}">
                            <i class="fas fa-calendar-alt me-1"></i>Meetings
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('professor.students*') ? 'active' : '' }}" href="#" id="studentsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users me-1"></i>Students
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('professor.students.index') }}"><i class="bi bi-person-lines-fill me-2"></i>All Students</a></li>
                            <li><a class="dropdown-item" href="{{ route('professor.students.batches') }}"><i class="bi bi-collection me-2"></i>My Batches</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.programs*') ? 'active' : '' }}" href="{{ route('professor.programs') }}">
                            <i class="fas fa-book me-1"></i>My Programs
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('professor.assignments*') ? 'active' : '' }}" href="#" id="assignmentsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tasks me-1"></i>Assignments
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('professor.grading') }}"><i class="bi bi-list-task me-2"></i>View All</a></li>
                            <li><a class="dropdown-item" href="{{ route('professor.assignments.create') }}"><i class="bi bi-plus-circle me-2"></i>Create New</a></li>
                        </ul>
                    </li>
                    @php
                        $attendanceEnabled = \App\Models\AdminSetting::where('setting_key', 'attendance_enabled')->value('setting_value') !== 'false';
                    @endphp
                    @if($attendanceEnabled)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.attendance*') ? 'active' : '' }}" href="{{ route('professor.attendance') }}">
                            <i class="fas fa-user-check me-1"></i>Attendance
                        </a>
                    </li>
                    @endif
                    {{--
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.calendar*') ? 'active' : '' }}" href="{{ route('professor.calendar') }}">
                            <i class="fas fa-calendar me-1"></i>Calendar
                        </a>
                    </li>
                    --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professor.settings*') ? 'active' : '' }}" href="{{ route('professor.settings') }}">
                            <i class="fas fa-cog me-1"></i>Settings
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ session('user_name', 'Professor') }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('professor.profile') }}"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to logout?')">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')

    <!-- Include Global Chat Component -->
    @include('components.global-chat')
</body>
</html>
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

                            <!-- Students -->
                            <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'professor.students')) active @endif">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#studentsMenu">
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

                            <!-- Assignments -->
                            <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'professor.assignments')) active @endif">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#assignmentsMenu">
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

                            @php
                                $attendanceEnabled = \App\Models\AdminSetting::where('setting_key', 'attendance_enabled')->value('setting_value') !== 'false';
                                $gradingEnabled = \App\Models\AdminSetting::where('setting_key', 'grading_enabled')->value('setting_value') !== 'false';
                            @endphp
                            @if($attendanceEnabled || $gradingEnabled)
                            <!-- Reports -->
                            <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'professor.reports')) active @endif">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#reportsMenu">
                                    <i class="bi bi-graph-up"></i>
                                    <span>Reports</span>
                                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                                </a>
                                <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'professor.reports')) show @endif" id="reportsMenu">
                                    <div class="submenu">
                                        @if($attendanceEnabled)
                                        <a href="{{ route('professor.reports.attendance') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.reports.attendance') active @endif">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>Attendance</span>
                                        </a>
                                        @endif
                                        @if($gradingEnabled)
                                        <a href="{{ route('professor.reports.grades') }}" class="submenu-link @if(Route::currentRouteName() === 'professor.reports.grades') active @endif">
                                            <i class="bi bi-award"></i>
                                            <span>Grades</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @endif

                            <!-- Chat -->
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

                <!-- Page Content -->
                <main class="page-content">
                    @yield('content')
                </main>
            </div>
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
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // Dropdown functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-bs-target');
            const dropdown = document.querySelector(target);
            if (dropdown) {
                dropdown.classList.toggle('show');
                this.classList.toggle('active');
            }
        });
    });
});
</script>

@stack('scripts')

<!-- Include Global Chat Component -->
@include('components.global-chat')
</body>
</html>
