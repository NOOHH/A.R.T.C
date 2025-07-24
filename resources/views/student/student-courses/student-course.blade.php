@extends('student.student-dashboard.student-dashboard-layout')

@php
  $hideSidebar = true; // Hide sidebar on course page
@endphp

@section('title', ($program->program_name ?? 'Course') . ' - A.R.T.C')

@section('head')
  <!-- Course-specific styles -->
  <link href="{{ asset('css/student/student-course.css') }}" rel="stylesheet">
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Use the same authentication context as student dashboard layout
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
            if (session('user_role') && session('user_role') !== 'student') {
                session()->flush();
                header('Location: ' . route('login') . '?error=access_denied');
                exit;
            }
            
            // No session at all
            session()->flush();
            header('Location: ' . route('login'));
            exit;
        }
    @endphp

    <!-- Course Header -->
    <div class="course-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="flex-grow-1">
@push('styles')
    <!-- Course-specific styles -->
    <link rel="stylesheet" href="{{ asset('css/student/student-course.css') }}">
    <style>
        /* Custom scrollbar styles */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush

@push('scripts')
    <!-- Global Variables for JavaScript -->
    <script>
        // Global variables accessible throughout the page
        window.myId = {{ $user ? $user->id : 'null' }};
        window.myName = @json(optional($user)->name ?? 'Guest');
        window.isAuthenticated = {{ $user ? 'true' : 'false' }};
        window.userRole = @json(optional($user)->role ?? 'guest');
        window.csrfToken = @json(csrf_token());
        
        // Make variables available without window prefix
        var myId = window.myId;
        var myName = window.myName;
        var isAuthenticated = window.isAuthenticated;
        var userRole = window.userRole;
        var csrfToken = window.csrfToken;
        
        console.log('Student Course Global variables initialized:', { myId, myName, isAuthenticated, userRole });

        // Initialize assignment form state for assignment submission UI
        window.assignmentFormState = window.assignmentFormState || {};
    </script>
@endpush

    <!-- Course Header -->
    <div class="course-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="flex-grow-1">
            <h1 class="course-title mb-1">{{ $program->program_name ?? 'Course' }}</h1>
            <p class="course-subtitle mb-0">{{ $program->description ?? 'Learn at your own pace with interactive modules and assignments.' }}</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary mt-3 mt-md-0">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
@push('styles')
    <style>
        /* Custom Colors and Variables */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;

        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        /* Layout Structure */
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        /* Sidebar Styles */
        .modern-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .sidebar-brand i {
            margin-right: 0.5rem;
            font-size: 1.5rem;
        }

        .sidebar-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            display: none;
        }

        .sidebar-content {
            flex: 1;
            padding: 1rem 0;
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .sidebar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-left: 3px solid #fff;
        }

        .sidebar-nav .nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }

        .submenu {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .submenu-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .submenu-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .submenu-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .submenu-link i {
            margin-right: 0.5rem;
            width: 1rem;
            text-align: center;
        }

        .program-info {
            display: flex;
            flex-direction: column;
        }

        .program-name {
            font-weight: 500;
        }

        .program-details {
            opacity: 0.7;
            font-size: 0.8rem;
        }

        .user-profile {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .user-details h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .user-details span {
            font-size: 0.8rem;
            opacity: 0.7;
        }


        .page-title h4 {
            color: var(--dark-color);
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            background-color: #f5f7fa;
            margin-left: -250px;
        }

        /* Course Layout Styles */
        .course-header {
            background: rgb(1, 25, 78);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .course-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .course-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .course-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            height: calc(100vh - 250px);
        }

        .modules-panel {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .modules-header {
            background: var(--light-color);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: var(--dark-color);
        }

        .modules-list {
            height: calc(100% - 60px);
            overflow-y: auto;
        }

        .module-item {
            padding: 0;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }

        .module-item:last-child {
            border-bottom: none;
        }

        .module-button {
            width: 100%;
            padding: 1rem 1.5rem;
            text-align: left;
            background: none;
            border: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .module-button:hover {
            background-color: var(--light-color);
        }

        .module-button.active {
            background-color: black;
            color: white;
        }

        .content-panel {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .content-header {
            background: var(--light-color);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .content-tabs {
            display: flex;
            gap: 1rem;
        }

        .tab-button {
            padding: 0.5rem 1rem;
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background-color: var(--primary-color);
            color: white;
        }

        .content-viewer {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Course and Content Item Styles */
        .course-item, .content-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .course-item:hover, .content-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        }

        .course-item.active, .content-item.active {
            border-color: var(--primary-color);
            background-color: #f8f9ff;
        }

        .item-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .item-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .video-icon {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        .document-icon {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
        }

        .assignment-icon {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .item-title {
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .item-description {
            color: var(--secondary-color);
            margin: 0.5rem 0 0 0;
            font-size: 0.9rem;
        }

        /* Video Player Styles */
        .video-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #000;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .video-player {
            width: 100%;
            height: 450px;
        }

        /* Assignment Styles */
        .assignment-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .assignment-header {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .assignment-actions {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }

        /* Document Viewer Styles */
        .document-container {
            max-width: 100%;
            margin: 0 auto;
        }

        .document-viewer {
            width: 100%;
            min-height: 600px;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
        }

        .pdf-viewer-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.75rem;
            border: 1px solid #dee2e6;
        }

        .file-preview {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
        }

        .content-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .content-grid {
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            }
        }

        /* Loading and Empty States */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
            color: var(--secondary-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .course-layout {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .modules-panel {
                max-height: 300px;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }

            .modern-sidebar {
                transform: translateX(-100%);
            }

            .modern-sidebar.show {
                transform: translateX(0);
            }

            .sidebar-close {
                display: block;
            }

            .main-content {
                padding: 1rem;
            }

            .course-header {
                padding: 1.5rem;
            }

            .course-title {
                font-size: 1.5rem;
            }

            .course-layout {
                height: auto;
            }

            .content-viewer {
                padding: 1rem;
            }
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Custom Dropdown Styles */
        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
        }

        .notification-dropdown {
            width: 300px;
        }

        .notification-dropdown .dropdown-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f8f9fa;
        }

        .notification-dropdown .dropdown-item:last-child {
            border-bottom: none;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush

    <!-- Course Layout -->
    <div class="course-layout">
        <!-- Modules Panel -->
        <div class="modules-panel">
            <div class="modules-header">
                <i class="bi bi-list-nested me-2"></i>
                Course Modules
            </div>
            <div class="modules-list">
                @if(!empty($course['modules']))
                    @foreach($course['modules'] as $mod)
                        <div class="module-item">
                            <button class="module-button" onclick="toggleModule('{{ $mod['id'] }}')">
                                <i class="bi bi-folder me-2"></i>
                                {{ $mod['name'] }}
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="bi bi-folder-x"></i>
                        <p>No modules available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Content Panel -->
        <div class="content-panel">
            <div class="content-header">
                <div class="content-tabs">
                    <button class="tab-button active" id="coursesTab" onclick="showCourses()">
                        <i class="bi bi-book me-1"></i>
                        Courses
                    </button>
                    <button class="tab-button" id="contentTab" onclick="showContent()" style="display: none;">
                        <i class="bi bi-file-earmark me-1"></i>
                        Content
                    </button>
                </div>
            </div>
            <div class="content-viewer" id="content-viewer">
                <!-- Default Welcome Message -->
                <div class="empty-state" id="welcome-message">
                    <i class="bi bi-mortarboard"></i>
                    <h3>Welcome to Your Course</h3>
                    <p>Select a module from the left panel to view available courses and content.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalTitle">Video Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-container">
                        <video class="video-player" id="videoPlayer" controls>
                            <source id="videoSource" src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Submission Modal -->
    <div class="modal fade" id="submissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Your Work</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="submissionForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="submissionContentId" name="content_id">
                        <div class="mb-3">
                            <label for="submissionFiles" class="form-label">Upload Files</label>
                            <input type="file" class="form-control" id="submissionFiles" name="files[]" multiple required>
                            <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, ZIP, Images, Videos (Max: 100MB each)</small>
                        </div>
                        <div class="mb-3">
                            <label for="submissionNotes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="submissionNotes" name="notes" rows="3" placeholder="Add any additional notes about your submission..."></textarea>
                        </div>
                    </form>
                    <div id="previousSubmissions" class="mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitWorkBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Course-specific JavaScript -->
    <script>
        // Global variables
        let currentModule = null;
        let currentCourse = null;
        let currentContent = null;
        let currentView = 'courses'; // 'courses' or 'content'

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Student course page initialized');
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('modernSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const sidebarClose = document.getElementById('sidebarClose');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar on mobile when clicking outside
            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }

            // Close sidebar when clicking on a link (mobile)
            const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        }

        // Toggle module and load courses
        function toggleModule(moduleId) {
            console.log('Toggling module:', moduleId);
            currentModule = moduleId;
            
            // Update active module button
            const moduleButtons = document.querySelectorAll('.module-button');
            moduleButtons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Show courses tab and hide content tab
            showCourses();
            document.getElementById('contentTab').style.display = 'none';

            // Load courses for this module
            loadCourses(moduleId);
        }

        // Load courses for a module
        function loadCourses(moduleId) {
            const viewer = document.getElementById('content-viewer');
            
            // Show loading spinner
            viewer.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading courses...</span>
                    </div>
                </div>
            `;

            // AJAX request to load courses
            fetch(`/student/module/${moduleId}/courses`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Courses loaded:', data);
                displayCourses(data.courses || []);
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                viewer.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <h4>Error Loading Courses</h4>
                        <p>Unable to load courses. Please try again later.</p>
                        <button class="btn btn-primary" onclick="loadCourses('${moduleId}')">Retry</button>
                    </div>
                `;
            });
        }

        // Display courses in the viewer
        function displayCourses(courses) {
            const viewer = document.getElementById('content-viewer');
            
            if (!courses || courses.length === 0) {
                viewer.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-book"></i>
                        <h4>No Courses Available</h4>
                        <p>There are no courses available for this module yet.</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="courses-grid">';
            courses.forEach(course => {
                const icon = getContentIcon(course.type || course.course_type || 'course');
                html += `
                    <div class="course-item d-flex justify-content-between align-items-center" onclick="selectCourse('${course.course_id}')">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="item-header">
                                <div class="item-icon ${icon.class}">
                                    <i class="bi ${icon.icon}"></i>
                                </div>
                                <h5 class="item-title">${course.course_name || course.name || 'Untitled Course'}</h5>
                            </div>
                            ${(course.course_description || course.description) ? `<p class=\"item-description\">${course.course_description || course.description}</p>` : ''}
                            <div class="mt-2">
                                <span class="badge bg-primary">${course.type || course.course_type || 'Course'}</span>
                                ${course.duration ? `<span class=\"badge bg-secondary ms-1\">${course.duration}</span>` : ''}
                            </div>
                        </div>
                        <button class="btn btn-success btn-sm ms-auto mark-complete-btn" style="min-width:120px;" onclick="event.stopPropagation(); markComplete('course', '${course.course_id}', this)">Mark Complete</button>
                    </div>
                `;
            });
            html += '</div>';
            
            viewer.innerHTML = html;
        }

        // Select a course and load its content
        function selectCourse(courseId) {
            console.log('Selecting course:', courseId);
            currentCourse = courseId;
            
            // Update active course
            const courseItems = document.querySelectorAll('.course-item');
            courseItems.forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Show content tab
            document.getElementById('contentTab').style.display = 'block';
            showContent();

            // Load course content
            loadCourseContent(currentModule, courseId);
        }

        // Load content for a course
        function loadCourseContent(moduleId, courseId) {
            const viewer = document.getElementById('content-viewer');
            
            // Show loading spinner
            viewer.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading content...</span>
                    </div>
                </div>
            `;

            // AJAX request to load course content
            fetch(`/student/module/${moduleId}/course/${courseId}/content-items`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Course content loaded:', data);
                console.log('Content items:', data.content_items || data.content || []);
                displayCourseContent(data.content_items || data.content || []);
            })
            .catch(error => {
                console.error('Error loading course content:', error);
                viewer.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <h4>Error Loading Content</h4>
                        <p>Unable to load course content. Please try again later.</p>
                        <button class="btn btn-primary" onclick="loadCourseContent('${moduleId}', '${courseId}')">Retry</button>
                    </div>
                `;
            });
        }

        // Display course content
        function displayCourseContent(content) {
            console.log('Displaying course content:', content);
            const viewer = document.getElementById('content-viewer');
            
            if (!content || content.length === 0) {
                viewer.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-file-earmark"></i>
                        <h4>No Content Available</h4>
                        <p>There is no content available for this course yet.</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="content-grid">';
            content.forEach(item => {
                console.log('Processing content item:', item);
                const icon = getContentIcon(item.content_type || item.type);
                const hasAttachment = item.attachment_path && item.attachment_path.trim() !== '';
                console.log('Item has attachment:', hasAttachment, 'Path:', item.attachment_path);

                // Determine if it's an assignment or requires submission
                const isAssignment = item.content_type === 'assignment' || item.enable_submission === true;
                const isQuiz = item.content_type === 'quiz';

                let itemHtml = `
                    <div class="content-item d-flex justify-content-between align-items-center" onclick="openContent('${item.id}', '${item.content_type || item.type}')">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="item-header">
                                <div class="item-icon ${icon.class}">
                                    <i class="bi ${icon.icon}"></i>
                                </div>
                                <h5 class="item-title">${item.content_title || item.title || 'Untitled Content'}</h5>
                            </div>
                            ${(item.content_description || item.description) ? `<p class=\"item-description\">${item.content_description || item.description}</p>` : ''}
                            <div class="mt-2">
                                <span class="badge bg-primary">${item.content_type || item.type || 'Content'}</span>
                                ${hasAttachment ? `<span class=\"badge bg-success ms-1\"><i class=\"bi bi-paperclip\"></i> Attachment</span>` : ''}
                                ${item.duration ? `<span class=\"badge bg-secondary ms-1\">${item.duration}</span>` : ''}
                            </div>
                        </div>
                        <button class="btn btn-success btn-sm ms-auto mark-complete-btn" style="min-width:120px;" onclick="event.stopPropagation(); markComplete('content', '${item.id}', this)">Mark Complete</button>
                    </div>
                `;

                if (isAssignment || isQuiz) {
                    itemHtml += `

                        </div>
                    `;
                }

                html += itemHtml;
            });
            html += '</div>';
            
            viewer.innerHTML = html;
        }

        // Get appropriate icon for content type
        function getContentIcon(type) {
            switch (type?.toLowerCase()) {
                case 'video':
                    return { icon: 'bi-play-circle-fill', class: 'video-icon' };
                case 'document':
                case 'pdf':
                    return { icon: 'bi-file-earmark-text', class: 'document-icon' };
                case 'assignment':
                    return { icon: 'bi-pencil-square', class: 'assignment-icon' };
                case 'quiz':
                    return { icon: 'bi-question-circle', class: 'assignment-icon' };
                default:
                    return { icon: 'bi-book', class: 'document-icon' };
            }
        }

        // Open content item
        function openContent(contentId, contentType) {
            console.log('Opening content:', contentId, 'Type:', contentType);
            currentContent = contentId;
            
            // Update active content
            const contentItems = document.querySelectorAll('.content-item');
            contentItems.forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Handle different content types
            switch (contentType?.toLowerCase()) {
                case 'video':
                    console.log('Opening as video content');
                    openVideoContent(contentId);
                    break;
                case 'assignment':
                    console.log('Opening as assignment content');
                    openAssignmentViewer(contentId);
                    break;
                case 'document':
                case 'pdf':
                    console.log('Opening as document content');
                    openDocumentContent(contentId);
                    break;
                default:
                    console.log('Opening as generic content');
                    openGenericContent(contentId);
                    break;
            }
        }

        // Assignment content viewer (not modal)
        function openAssignmentViewer(contentId) {
            const viewer = document.getElementById('content-viewer');
            // Show loading spinner
            viewer.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading assignment...</span>
                    </div>
                </div>
            `;
            fetch(`/student/content/${contentId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.content) {
                    let content = data.content;
                    let html = `<div class="assignment-container">
                <div class="assignment-header mb-3">
                    <h4>${content.content_title || 'Assignment'}</h4>
                    ${content.content_description ? `<p class="mb-2">${content.content_description}</p>` : ''}
                    ${content.due_date ? `<div><strong>Due:</strong> <span id='assignmentDueDateDisplay'>${content.due_date}</span></div>` : ''}
                </div>`;
                    // Show attachments if any
                    if (content.attachment_path) {
                        let files = [];
                        try { files = JSON.parse(content.attachment_path); } catch (e) { files = [content.attachment_path]; }
                        if (Array.isArray(files)) {
                            html += '<div class="mb-3"><strong>Attachments:</strong><ul>';
                            files.forEach(f => {
                                const isPdf = f.toLowerCase().endsWith('.pdf');
                                html += `<li><a href="/storage/${f}" target="_blank">${f.split('/').pop()}</a>`;
                                if (isPdf) {
                                    html += `<div class="pdf-viewer-container mt-2"><iframe src="/storage/${f}#toolbar=1&navpanes=1&scrollbar=1" class="document-viewer" frameborder="0" style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 0.5rem;"></iframe></div>`;
                                }
                                html += `</li>`;
                            });
                            html += '</ul></div>';
                        }
                    }
                    // Assignment instructions
                    if (content.content_data && content.content_data.assignment_instructions) {
                        html += `<div class="mb-3"><strong>Instructions:</strong><div>${content.content_data.assignment_instructions}</div></div>`;
                    }
                    // Deadline enforcement logic
                    let deadlinePassed = false;
                    let dueDateObj = null;
                    if (content.due_date) {
                        dueDateObj = new Date(content.due_date);
                        const now = new Date();
                        if (now > dueDateObj) {
                            deadlinePassed = true;
                        }
                    }
                    // Fetch and display previous submissions
                    fetch(`/student/content/${content.id}/submissions`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let hasSubmission = false;
                        let latest = null;
                        let isDraft = false;
                        let showSubmissionForm = !!window.assignmentFormState[contentId];
                        if (data.success && data.submissions && data.submissions.length > 0) {
                            hasSubmission = true;
                            // Find draft if exists, else latest
                            latest = data.submissions.find(s => s.status === 'draft') || data.submissions[0];
                            isDraft = latest.status === 'draft';
                            // Always show the draft/submission status view if any submission exists
                            showSubmissionForm = true;
                        } else {
                            hasSubmission = false;
                            latest = null;
                            isDraft = false;
                            showSubmissionForm = !!window.assignmentFormState[contentId];
                        }
                        // Use global state for form visibility
                        html += `<div id="assignmentSubmissionBlock">`;
                        // If no submission and not showing form, show Add Submission button
                        if (!hasSubmission && !showSubmissionForm) {
                            html += `<button class="btn btn-primary" id="addSubmissionBtn">Add submission</button>`;
                        }
                        // Show the form if a draft exists or Add Submission was clicked
                        if (isDraft || showSubmissionForm) {
                            let buttons = '<button type="button" class="btn btn-secondary" id="saveDraftBtn">Save changes</button>';
                            if (isDraft) {
                                buttons += '<button type="button" class="btn btn-primary" id="submitAssignmentBtn">Submit assignment</button>';
                                buttons += '<button type="button" class="btn btn-danger" id="removeDraftBtn">Remove submission</button>';
                            }
                            buttons += '<button type="button" class="btn btn-outline-secondary" id="cancelSubmissionBtn">Cancel</button>';
                            html += `<div class="assignment-actions">
        <form id="assignmentDraftForm" enctype="multipart/form-data">
            <input type="hidden" name="content_id" value="${content.id}">
            <input type="hidden" name="module_id" value="${content.module_id}">
            <input type="hidden" name="module_id" value="${parseInt(content.module_id || currentModule)}">
            <div class="mb-3">
                <label for="assignmentFiles" class="form-label">Upload Files</label>
                <input type="file" class="form-control" id="assignmentFiles" name="files[]" multiple ${isDraft ? '' : 'required'}>
                <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, ZIP, Images, Videos (Max: 100MB each)</small>
            </div>
            <div class="mb-3">
                <label for="assignmentNotes" class="form-label">Notes (Optional)</label>
                <textarea class="form-control" id="assignmentNotes" name="notes" rows="3" placeholder="Add any additional notes about your submission...">${isDraft && latest.comments ? latest.comments : ''}</textarea>
            </div>
            <div class="d-flex gap-2">${buttons}</div>
        </form>
        <div id="assignmentSubmissionStatus" class="mt-3"></div>
        </div>`;
                        }
                        html += `<div id="previousAssignmentSubmissions" class="mt-3"></div>`;

                        viewer.innerHTML = html;

const addSubmissionBtn = document.getElementById('addSubmissionBtn');
if (addSubmissionBtn) {
    addSubmissionBtn.onclick = function() {
        window.assignmentFormState[contentId] = true;
        openAssignmentViewer(contentId);
    };
}
                        // Fetch and display submission history
                        if (data.success && data.submissions && data.submissions.length > 0) {
                            let html2 = '<div class="mt-4"><h5><i class="bi bi-clock-history me-2"></i>Submission History</h5>';
                            data.submissions.forEach(sub => {
                                let files = sub.files || [];
                                if (typeof files === 'string') { try { files = JSON.parse(files); } catch (e) { files = []; } }
                                
                                // Determine status badge class
                                let statusClass = 'bg-secondary';
                                let statusText = 'Submitted';
                                if (sub.status === 'graded') {
                                    statusClass = 'bg-success';
                                    statusText = 'Graded';
                                } else if (sub.status === 'reviewed') {
                                    statusClass = 'bg-info';
                                    statusText = 'Reviewed';
                                }
                                
                                html2 += `<div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-1">
                                                <i class="bi bi-file-earmark-text me-2"></i>
                                                Submission ${new Date(sub.submitted_at).toLocaleDateString()}
                                            </h6>
                                            <span class="badge ${statusClass}">${statusText}</span>
                                        </div>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-calendar me-1"></i>
                                            Submitted: ${new Date(sub.submitted_at).toLocaleString()}
                                        </p>`;
                                
                                // Show grade if available
                                if (sub.grade !== null && sub.grade !== undefined && sub.status === 'graded') {
                                    let gradeClass = 'text-success';
                                    if (sub.grade < 70) gradeClass = 'text-danger';
                                    else if (sub.grade < 80) gradeClass = 'text-warning';
                                    
                                    html2 += `<div class="alert alert-light border-start border-4 border-primary mb-2">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-award me-2 text-primary"></i>
                                            <strong>Grade: <span class="${gradeClass}">${sub.grade}/100</span></strong>
                                        </div>`;
                                    
                                    // Show feedback if available
                                    if (sub.feedback) {
                                        html2 += `<div class="mt-2">
                                            <strong><i class="bi bi-chat-text me-2"></i>Instructor Feedback:</strong>
                                            <div class="bg-white rounded p-2 mt-1 border">${sub.feedback}</div>
                                        </div>`;
                                    }
                                    html2 += `</div>`;
                                } else if (sub.status === 'reviewed' && sub.feedback) {
                                    // Show feedback for reviewed submissions without grade
                                    html2 += `<div class="alert alert-info border-start border-4 border-info mb-2">
                                        <strong><i class="bi bi-chat-text me-2"></i>Instructor Feedback:</strong>
                                        <div class="bg-white rounded p-2 mt-1 border">${sub.feedback}</div>
                                        <small class="text-muted mt-1 d-block">This submission needs revision. Please review the feedback and resubmit.</small>
                                    </div>`;
                                }
                                
                                // Show files
                                if (files.length > 0) {
                                    html2 += `<div class="mb-2">
                                        <strong><i class="bi bi-paperclip me-2"></i>Files:</strong>
                                        <div class="mt-1">`;
                                    files.forEach(f => {
                                        const fileName = f.original_filename || (typeof f === 'string' ? f.split('/').pop() : 'File');
                                        const filePath = f.file_path || f.path || f;
                                        html2 += `<a href="/storage/${filePath}" target="_blank" class="btn btn-outline-primary btn-sm me-2 mb-1">
                                            <i class="bi bi-download me-1"></i>${fileName}
                                        </a>`;
                                    });
                                    html2 += `</div></div>`;
                                }
                                
                                // Show submission comments if any
                                if (sub.comments) {
                                    html2 += `<div class="mb-2">
                                        <strong><i class="bi bi-sticky me-2"></i>Your Notes:</strong>
                                        <div class="text-muted">${sub.comments}</div>
                                    </div>`;
                                }
                                
                                html2 += `</div></div>`;
                            });
                            html2 += '</div>';
                            document.getElementById('previousAssignmentSubmissions').innerHTML = html2;
                        }
                        // Add form handler if form exists
                        const formElem = document.getElementById('assignmentDraftForm');
                        if (formElem) {
                            const saveDraftBtn = document.getElementById('saveDraftBtn');
                            const submitAssignmentBtn = document.getElementById('submitAssignmentBtn');
                            const removeDraftBtn = document.getElementById('removeDraftBtn');
                            const cancelSubmissionBtn = document.getElementById('cancelSubmissionBtn');
                            const statusDiv = document.getElementById('assignmentSubmissionStatus');
                            if (saveDraftBtn) {
                                saveDraftBtn.onclick = function() {
                                    const formData = new FormData(formElem);
                                    // Append the current module ID (ensure integer)
                                    formData.append('module_id', currentModule);
                                    statusDiv.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving draft...';
                                    fetch('/student/assignment/save-draft', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        credentials: 'include',
                                        body: formData
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        statusDiv.innerHTML = data.success ? '<span class="text-success">Draft saved!</span>' : `<span class="text-danger">${data.message}</span>`;
                                        if (data.success) {
                                            setTimeout(() => openAssignmentViewer(contentId), 500); // Re-render to show draft state
                                        }
                                    })
                                    .catch(err => {
                                        statusDiv.innerHTML = '<span class="text-danger">Error saving draft.</span>';    
                                    });
                                };
                            }
                            if (submitAssignmentBtn) {
                                submitAssignmentBtn.onclick = function() {
                                    const formData = new FormData(formElem);
                                    formData.append('module_id', content.module_id);
                                    statusDiv.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
                                    fetch('/student/assignment/submit', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: formData
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        statusDiv.innerHTML = data.success ? '<span class="text-success">Assignment submitted!</span>' : `<span class="text-danger">${data.message}</span>`;
                                        if (data.success) setTimeout(() => openAssignmentViewer(contentId), 1000);
                                    });
                                };
                            }
                            if (removeDraftBtn) {
                                removeDraftBtn.onclick = function() {
                                    if (!confirm('Are you sure you want to remove this draft?')) return;
                                    statusDiv.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Removing...';
                                    fetch('/student/assignment/remove-draft', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({ module_id: content.module_id })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        statusDiv.innerHTML = data.success ? '<span class="text-success">Draft removed!</span>' : `<span class="text-danger">${data.message}</span>`;
                                        if (data.success) setTimeout(() => openAssignmentViewer(contentId), 1000);
                                    });
                                };
                            }
                            if (cancelSubmissionBtn) {
                                cancelSubmissionBtn.onclick = function() {
                                    // Hide the form and show Add Submission button again
                                    window.assignmentFormState[contentId] = false;
                                    openAssignmentViewer(contentId);
                                };
                            }
                        }
                    });
                } else {
                    viewer.innerHTML = `<div class="empty-state"><i class="bi bi-exclamation-triangle text-warning"></i><h4>Assignment Not Available</h4><p>Unable to load assignment details.</p></div>`;
                }
            });
        }

        // Open video content
        function openVideoContent(contentId) {
            // Load content details first
            fetch(`/student/content/${contentId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.content && (data.content.attachment_path || data.content.content_url)) {
                    document.getElementById('videoModalTitle').textContent = data.content.content_title || 'Video Content';
                    const videoSrc = data.content.content_url || storageUrl + '/' + data.content.attachment_path;
                    document.getElementById('videoSource').src = videoSrc;
                    document.getElementById('videoPlayer').load();
                    
                    const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
                    videoModal.show();
                } else {
                    alert('Video content not available');
                }
            })
            .catch(error => {
                console.error('Error loading video:', error);
                alert('Error loading video content');
            });
        }

        // Open document content
        function openDocumentContent(contentId) {
            // Load content details and display in viewer
            fetch(`/student/content/${contentId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.content) {
                    displayDocumentViewer(data.content);
                } else {
                    alert('Document not available');
                }
            })
            .catch(error => {
                console.error('Error loading document:', error);
                alert('Error loading document');
            });
        }

        // Display document viewer
        function displayDocumentViewer(content) {
            const viewer = document.getElementById('content-viewer');
            
            // Get the file URL - prioritize content_url, then attachment_path
            let fileUrl = '';
            if (content.content_url && content.content_url.trim() !== '') {
                fileUrl = content.content_url;
            } else if (content.attachment_path && content.attachment_path.trim() !== '') {
                fileUrl = storageUrl + '/' + content.attachment_path;
            } else {
                viewer.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <h4>No Document Available</h4>
                        <p>No document file is attached to this content item.</p>
                        <button class="btn btn-outline-primary" onclick="showContent()">
                            <i class="bi bi-arrow-left"></i> Back to Content
                        </button>
                    </div>
                `;
                return;
            }

            // Determine if it's a PDF for embedded viewing
            const isPdf = fileUrl.toLowerCase().includes('.pdf') || content.content_type === 'pdf';
            const fileName = content.attachment_path ? content.attachment_path.split('/').pop() : 'document';
            
            viewer.innerHTML = `
                <div class="document-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4>${content.content_title || 'Document'}</h4>
                            <small class="text-muted">${fileName}</small>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="showContent()">
                                <i class="bi bi-arrow-left"></i> Back
                            </button>
                            <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <button class="btn btn-success btn-sm ms-2 mark-complete-btn" onclick="markComplete('document', '${content.id}', this)">Mark Complete</button>
                        </div>
                    </div>
                    ${(content.content_description) ? `<p class="text-muted mb-3">${content.content_description}</p>` : ''}
                    
                    ${isPdf ? 
                        `<div class="pdf-viewer-container">
                            <iframe src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                                    class="document-viewer" 
                                    frameborder="0"
                                    style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 0.5rem;">
                                <p>Your browser does not support PDFs. 
                                   <a href="${fileUrl}" target="_blank">Download the PDF</a> to view it.
                                </p>
                            </iframe>
                        </div>`
                        :
                        `<div class="file-preview">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                This file type cannot be previewed in the browser. Please download to view.
                            </div>
                            <div class="text-center p-4">
                                <i class="bi bi-file-earmark text-primary" style="font-size: 4rem;"></i>
                                <h5 class="mt-3">${fileName}</h5>
                                <p class="text-muted">Click download to view this file</p>
                            </div>
                        </div>`
                    }
                </div>
            `;
        }

        // Open generic content
        function openGenericContent(contentId) {
            fetch(`/student/content/${contentId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.content) {
                    const viewer = document.getElementById('content-viewer');
                    
                    // Check if there's an attachment to display
                    let attachmentSection = '';
                    if (data.content.attachment_path && data.content.attachment_path.trim() !== '') {
                        const fileUrl = storageUrl + '/' + data.content.attachment_path;
                        const fileName = data.content.attachment_path.split('/').pop();
                        const isPdf = fileUrl.toLowerCase().includes('.pdf');
                        
                        attachmentSection = `
                            <div class="mt-4">
                                <h5><i class="bi bi-paperclip me-2"></i>Attachment</h5>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${fileName}</h6>
                                        <small class="text-muted">${isPdf ? 'PDF Document' : 'Document'}</small>
                                    </div>
                                    <div>
                                        ${isPdf ? `<button class="btn btn-outline-primary me-2" onclick="viewDocumentInline('${data.content.id}')">
                                            <i class="bi bi-eye"></i> View
                                        </button>` : ''}
                                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                                ${isPdf ? `
                                    <div id="inline-document-${data.content.id}" style="display: none;">
                                        <iframe src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                                                style="width: 100%; height: 1000px; border: 1px solid #ddd; border-radius: 0.5rem;"
                                                frameborder="0">
                                            <p>Your browser does not support PDFs. 
                                               <a href="${fileUrl}" target="_blank">Download the PDF</a> to view it.
                                            </p>
                                        </iframe>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }
                    
                    viewer.innerHTML = `
                        <div class="content-details">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>${data.content.content_title || 'Content'}</h4>
                                <button class="btn btn-outline-primary" onclick="showContent()">
                                    <i class="bi bi-arrow-left"></i> Back
                                </button>
                            </div>
                            ${data.content.content_description ? `<p class="mb-3">${data.content.content_description}</p>` : ''}
                            ${data.content.content_text ? `<div class="content-body mb-4">${data.content.content_text}</div>` : ''}
                            ${attachmentSection}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading content:', error);
                alert('Error loading content');
            });
        }

        // Helper function to toggle inline document view
        function viewDocumentInline(contentId) {
            const inlineDoc = document.getElementById(`inline-document-${contentId}`);
            if (inlineDoc) {
                if (inlineDoc.style.display === 'none') {
                    inlineDoc.style.display = 'block';
                } else {
                    inlineDoc.style.display = 'none';
                }
            }
        }

        // Submit assignment
        function submitAssignment() {
            const form = document.getElementById('assignmentForm');
            const formData = new FormData(form);
            
            // Show loading state
            const submitBtn = event.target;
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

            fetch('/student/assignment/submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Assignment submitted successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('assignmentModal')).hide();
                    form.reset();
                } else {
                    alert(data.message || 'Error submitting assignment');
                }
            })
            .catch(error => {
                console.error('Error submitting assignment:', error);
                alert('Error submitting assignment');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }

        // Tab functions
        function showCourses() {
            currentView = 'courses';
            document.getElementById('coursesTab').classList.add('active');
            document.getElementById('contentTab').classList.remove('active');
            
            if (currentModule) {
                loadCourses(currentModule);
            } else {
                document.getElementById('content-viewer').innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-mortarboard"></i>
                        <h3>Welcome to Your Course</h3>
                        <p>Select a module from the left panel to view available courses.</p>
                    </div>
                `;
            }
        }

        function showContent() {
            currentView = 'content';
            document.getElementById('contentTab').classList.add('active');
            document.getElementById('coursesTab').classList.remove('active');
            
            if (currentModule && currentCourse) {
                loadCourseContent(currentModule, currentCourse);
            } else {
                document.getElementById('content-viewer').innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-file-earmark"></i>
                        <h3>No Course Selected</h3>
                        <p>Please select a course first to view its content.</p>
                    </div>
                `;
            }
        }

        document.getElementById('submitWorkBtn').addEventListener('click', function() {
            const form = document.getElementById('submissionForm');
            const formData = new FormData(form);
            const submitBtn = this;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            fetch('/student/assignment/submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Submission successful!');
                    bootstrap.Modal.getInstance(document.getElementById('submissionModal')).hide();
                    form.reset();
                } else {
                    alert(data.message || 'Error submitting your work.');
                }
            })
            .catch(error => {
                alert('Error submitting your work.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit';
            });
        });

    </script>

    <!-- Logout Form (hidden) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap 5.3.0 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Move the markComplete function here and fix the CSRF token reference
        function markComplete(type, id, btn) {
            btn.disabled = true;
            btn.innerText = 'Marking...';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');

            let url = '';
            let payload = {};
            if (type === 'course') {
                url = '/student/complete-course';
                payload = { course_id: id };
            } else if (type === 'content') {
                url = '/student/complete-content';
                payload = { content_id: id };
            } else if (type === 'document') {
                url = '/student/complete-content';
                payload = { content_id: id };
            }

            // Always get the CSRF token from meta if not present
            let token = (typeof csrfToken !== 'undefined' && csrfToken) ? csrfToken : (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'include',
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.innerText = 'Completed';
                    btn.disabled = true;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                } else {
                    btn.disabled = false;
                    btn.innerText = 'Mark Complete';
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-success');
                    alert(data.message || 'Error marking as complete.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Mark Complete';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                alert('Error marking as complete.');
            });
        }
    </script>
@endpush
