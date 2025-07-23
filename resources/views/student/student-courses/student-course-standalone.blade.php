<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $program->program_name ?? 'Course' }} - A.R.T.C</title>
    
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
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
            --navbar-height: 60px;
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

        /* Navbar Styles */
        .custom-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: var(--navbar-height);
            padding: 0 1.5rem;
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
        }

        /* Course Layout Styles */
        .course-header {
            background: linear-gradient(135deg, var(--primary-color), #4c84ff);
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
            background-color: var(--primary-color);
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
            width: 6px;
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
</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Include Sidebar Component -->
        @include('components.student-sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Include Navbar Component -->
            @include('components.student-navbar', ['pageTitle' => $program->program_name ?? 'Course'])

            <!-- Main Content -->
            <main class="main-content">
                <!-- Course Header -->
                <div class="course-header">
                    <h1 class="course-title">{{ $program->program_name ?? 'Course' }}</h1>
                    <p class="course-subtitle">{{ $program->description ?? 'Learn at your own pace with interactive modules and assignments.' }}</p>
                </div>

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
            </main>
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
    <div class="modal fade" id="assignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assignmentForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="assignmentId" name="assignment_id">
                        <div class="mb-3">
                            <label for="submissionFile" class="form-label">Upload File</label>
                            <input type="file" class="form-control" id="submissionFile" name="submission_file" required>
                            <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, PPT, PPTX (Max: 10MB)</small>
                        </div>
                        <div class="mb-3">
                            <label for="submissionNotes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="submissionNotes" name="notes" rows="3" placeholder="Add any additional notes about your submission..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAssignment()">Submit Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3.0 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                const icon = getContentIcon(course.type || 'course');
                html += `
                    <div class="course-item" onclick="selectCourse('${course.id}')">
                        <div class="item-header">
                            <div class="item-icon ${icon.class}">
                                <i class="bi ${icon.icon}"></i>
                            </div>
                            <h5 class="item-title">${course.title || course.name || 'Untitled Course'}</h5>
                        </div>
                        ${course.description ? `<p class="item-description">${course.description}</p>` : ''}
                        <div class="mt-2">
                            <span class="badge bg-primary">${course.type || 'Course'}</span>
                            ${course.duration ? `<span class="badge bg-secondary ms-1">${course.duration}</span>` : ''}
                        </div>
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
            loadCourseContent(courseId);
        }

        // Load content for a course
        function loadCourseContent(courseId) {
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
            fetch(`/student/course/${courseId}/content`, {
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
                displayCourseContent(data.content || []);
            })
            .catch(error => {
                console.error('Error loading course content:', error);
                viewer.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <h4>Error Loading Content</h4>
                        <p>Unable to load course content. Please try again later.</p>
                        <button class="btn btn-primary" onclick="loadCourseContent('${courseId}')">Retry</button>
                    </div>
                `;
            });
        }

        // Display course content
        function displayCourseContent(content) {
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
                const icon = getContentIcon(item.type);
                html += `
                    <div class="content-item" onclick="openContent('${item.id}', '${item.type}')">
                        <div class="item-header">
                            <div class="item-icon ${icon.class}">
                                <i class="bi ${icon.icon}"></i>
                            </div>
                            <h5 class="item-title">${item.title || 'Untitled Content'}</h5>
                        </div>
                        ${item.description ? `<p class="item-description">${item.description}</p>` : ''}
                        <div class="mt-2">
                            <span class="badge bg-primary">${item.type || 'Content'}</span>
                            ${item.duration ? `<span class="badge bg-secondary ms-1">${item.duration}</span>` : ''}
                        </div>
                    </div>
                `;
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
            console.log('Opening content:', contentId, contentType);
            currentContent = contentId;
            
            // Update active content
            const contentItems = document.querySelectorAll('.content-item');
            contentItems.forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Handle different content types
            switch (contentType?.toLowerCase()) {
                case 'video':
                    openVideoContent(contentId);
                    break;
                case 'assignment':
                    openAssignmentContent(contentId);
                    break;
                case 'document':
                case 'pdf':
                    openDocumentContent(contentId);
                    break;
                default:
                    openGenericContent(contentId);
                    break;
            }
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
                if (data.content && data.content.file_path) {
                    document.getElementById('videoModalTitle').textContent = data.content.title || 'Video Content';
                    document.getElementById('videoSource').src = data.content.file_path;
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

        // Open assignment content
        function openAssignmentContent(contentId) {
            document.getElementById('assignmentId').value = contentId;
            const assignmentModal = new bootstrap.Modal(document.getElementById('assignmentModal'));
            assignmentModal.show();
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
            viewer.innerHTML = `
                <div class="document-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>${content.title || 'Document'}</h4>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="showContent()">
                                <i class="bi bi-arrow-left"></i> Back
                            </button>
                            <a href="${content.file_path}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                    ${content.description ? `<p class="text-muted mb-3">${content.description}</p>` : ''}
                    <iframe src="${content.file_path}" class="document-viewer" frameborder="0"></iframe>
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
                    viewer.innerHTML = `
                        <div class="content-details">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>${data.content.title || 'Content'}</h4>
                                <button class="btn btn-outline-primary" onclick="showContent()">
                                    <i class="bi bi-arrow-left"></i> Back
                                </button>
                            </div>
                            ${data.content.description ? `<p>${data.content.description}</p>` : ''}
                            ${data.content.content ? `<div class="content-body">${data.content.content}</div>` : ''}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading content:', error);
                alert('Error loading content');
            });
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
            
            if (currentCourse) {
                loadCourseContent(currentCourse);
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
    </script>
</body>
</html>
