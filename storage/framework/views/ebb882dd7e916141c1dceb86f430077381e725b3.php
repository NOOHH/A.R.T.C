<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Customize Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body {
            background: var(--gray-100);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            color: var(--gray-800);
        }
        
        /* Top Navigation */
        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand:hover {
            color: var(--secondary-color);
        }
        
        .navbar-nav .nav-link {
            color: var(--gray-600);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: 1px solid var(--gray-200);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        /* Settings Layout */
        .settings-layout {
            display: flex;
            min-height: calc(100vh - 80px);
            flex-direction: column;
        }
        
        /* Settings Navbar */
        .settings-navbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 80px;
            z-index: 999;
        }
        
        .settings-nav-brand h4 {
            color: var(--primary-color);
            margin: 0;
        }
        
        .settings-nav-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .settings-nav-tab {
            background: none;
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .settings-nav-tab:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
        }
        
        .settings-nav-tab.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .settings-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Main Layout */
        .settings-main-layout {
            display: flex;
            flex: 1;
        }
        
        /* Settings Sidebar */
        .settings-sidebar {
            width: 400px;
            background: white;
            border-right: 1px solid var(--gray-200);
            overflow-y: auto;
            height: calc(100vh - 160px);
            position: sticky;
            top: 160px;
        }
        
        .sidebar-section {
            padding: 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: none;
        }
        
        .sidebar-section.active {
            display: block;
        }
        
        .section-header {
            margin-bottom: 1.5rem;
        }
        
        .section-header h5 {
            color: var(--gray-800);
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: white;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
            outline: none;
        }
        
        .form-text {
            color: var(--gray-500);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .form-check-input {
            margin-right: 0.5rem;
        }
        
        .form-check-label {
            color: var(--gray-700);
        }
        
        /* Color Input */
        .color-input-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        input[type="color"] {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }
        
        .btn-outline-primary {
            background: none;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        /* Preview Panel */
        .preview-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--gray-50);
            position: relative;
        }
        
        .preview-header {
            background: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .preview-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
        }
        
        .preview-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .preview-btn {
            background: var(--gray-100);
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .preview-btn:hover {
            background: var(--gray-200);
            border-color: var(--gray-400);
            color: var(--gray-700);
        }
        
        .preview-iframe-container {
            position: relative;
            flex: 1;
            background: white;
            border-radius: 12px;
            margin: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 12px;
        }
        
        .preview-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: var(--gray-500);
        }
        
        .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 3px solid var(--gray-200);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .settings-main-layout {
                flex-direction: column;
            }
            
            .settings-sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            
            .settings-nav-tabs {
                flex-direction: column;
            }
            
            .preview-iframe-container {
                height: 400px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="/" class="navbar-brand me-4">
                        <i class="fas fa-graduation-cap me-2"></i>SmartPrep
                    </a>
                    <ul class="navbar-nav d-flex flex-row mb-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(Auth::guard('smartprep_admin')->check() ? route('smartprep.admin.dashboard') : route('smartprep.dashboard')); ?>">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo e(route('smartprep.dashboard.customize')); ?>">
                                <i class="fas fa-palette me-2"></i>Customize Website
                            </a>
                        </li>

                        <?php if($activeWebsites->count() > 0): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="websitesDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-globe me-2"></i>My Websites
                            </a>
                            <ul class="dropdown-menu">
                                <?php $__currentLoopData = $activeWebsites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $website): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><a class="dropdown-item" href="/t/<?php echo e($website->slug); ?>" target="_blank"><?php echo e($website->name); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <ul class="navbar-nav d-flex flex-row mb-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?php echo e(Auth::guard('smartprep')->user()->name); ?>

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>Home</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('smartprep.logout')); ?>" class="d-inline w-100">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item text-danger">
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

    <!-- Settings Layout -->
    <div class="settings-layout">
        <!-- Top Settings Navigation -->
        <nav class="settings-navbar">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <div class="settings-nav-tabs">
                        <button class="settings-nav-tab active" data-section="general">
                            <i class="fas fa-cog me-2"></i>General
                        </button>
                        <button class="settings-nav-tab" data-section="branding">
                            <i class="fas fa-palette me-2"></i>Branding
                        </button>
                        <button class="settings-nav-tab" data-section="navbar">
                            <i class="fas fa-bars me-2"></i>Navigation
                        </button>
                        <button class="settings-nav-tab" data-section="homepage">
                            <i class="fas fa-home me-2"></i>Homepage
                        </button>
                        <button class="settings-nav-tab" data-section="student">
                            <i class="fas fa-user-graduate me-2"></i>Student Portal
                        </button>
                        <button class="settings-nav-tab" data-section="professor">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Professor Panel
                        </button>
                        <button class="settings-nav-tab" data-section="admin">
                            <i class="fas fa-user-shield me-2"></i>Admin Panel
                        </button>
                        <button class="settings-nav-tab" data-section="advanced">
                            <i class="fas fa-code me-2"></i>Advanced
                        </button>
                    </div>
                    
                    <div class="settings-actions">
                        <button class="btn btn-outline-primary" onclick="saveAllSettings()">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <button class="btn btn-success" onclick="submitWebsiteRequest()">
                            <i class="fas fa-paper-plane me-2"></i>Submit Website Request
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Settings Layout -->
        <div class="settings-main-layout">
            <!-- Settings Sidebar -->
            <div class="settings-sidebar">
                <!-- General Settings -->
                <div class="sidebar-section active" id="general-settings">
                    <div class="section-header">
                        <h5><i class="fas fa-cog me-2"></i>General Settings</h5>
                    </div>
                    
                    <form id="generalForm" onsubmit="updateGeneral(event)">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Website Title</label>
                            <input type="text" class="form-control" name="site_title" value="<?php echo e($selectedWebsite->name ?? 'Your Training Center'); ?>" placeholder="Enter website title">
                            <small class="form-text text-muted">Appears in browser tab and search results</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Website Tagline</label>
                            <input type="text" class="form-control" name="tagline" value="Excellence in Education & Training" placeholder="Enter tagline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="<?php echo e(Auth::guard('smartprep')->user()->email); ?>" placeholder="Contact email">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" placeholder="+1 (555) 123-4567">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" placeholder="Physical address"></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Website Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Brief description of your training center">Professional training center dedicated to providing quality education and certification programs.</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update General Settings
                        </button>
                    </form>
                </div>
                
                <!-- Branding Settings -->
                <div class="sidebar-section" id="branding-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-palette me-2"></i>Branding & Colors</h5>
                    </div>
                    
                    <form id="brandingForm" onsubmit="updateBranding(event)" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Website Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <small class="form-text text-muted">Recommended: PNG format, 200x60px</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Favicon</label>
                            <input type="file" class="form-control" name="favicon" accept="image/*">
                            <small class="form-text text-muted">16x16 or 32x32 pixels, ICO or PNG format</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="color-input-group">
                                <input type="color" name="primary_color" value="#1e40af">
                                <input type="text" class="form-control" name="primary_color_hex" value="#1e40af" placeholder="#1e40af">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="color-input-group">
                                <input type="color" name="secondary_color" value="#3b82f6">
                                <input type="text" class="form-control" name="secondary_color_hex" value="#3b82f6" placeholder="#3b82f6">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Accent Color</label>
                            <div class="color-input-group">
                                <input type="color" name="accent_color" value="#10b981">
                                <input type="text" class="form-control" name="accent_color_hex" value="#10b981" placeholder="#10b981">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Background Style</label>
                            <select class="form-control" name="background_style">
                                <option value="solid">Solid Color</option>
                                <option value="gradient" selected>Gradient</option>
                                <option value="pattern">Pattern</option>
                                <option value="image">Background Image</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Branding
                        </button>
                    </form>
                </div>
                
                <!-- Content Settings -->
                <div class="sidebar-section" id="content-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-edit me-2"></i>Content Management</h5>
                    </div>
                    
                    <form id="contentForm" onsubmit="updateContent(event)" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Section Title</label>
                            <input type="text" class="form-control" name="hero_title" value="Welcome to Our Training Center" placeholder="Main headline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Section Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description">Transform your skills with our comprehensive training programs designed for professionals.</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Background Image</label>
                            <input type="file" class="form-control" name="hero_background" accept="image/*">
                            <small class="form-text text-muted">Recommended: 1920x1080px, JPG or PNG</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">About Section</label>
                            <textarea class="form-control" name="about_content" rows="5" placeholder="About your training center">We are a leading training center committed to providing high-quality education and professional development opportunities.</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Call-to-Action Button Text</label>
                            <input type="text" class="form-control" name="cta_text" value="Get Started Today" placeholder="Button text">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Call-to-Action Button Link</label>
                            <input type="url" class="form-control" name="cta_link" placeholder="https://example.com">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Content
                        </button>
                    </form>
                </div>
                
                <!-- Features Settings -->
                <div class="sidebar-section" id="features-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-star me-2"></i>Website Features</h5>
                    </div>
                    
                    <form id="featuresForm" onsubmit="updateFeatures(event)">
                        <?php echo csrf_field(); ?>
                        <h6 class="mt-4 mb-3">Navigation Features</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="online_enrollment" checked>
                            <label class="form-check-label">Online Enrollment System</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="student_portal" checked>
                            <label class="form-check-label">Student Portal</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="course_catalog" checked>
                            <label class="form-check-label">Course Catalog</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="instructor_profiles" checked>
                            <label class="form-check-label">Instructor Profiles</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="testimonials" checked>
                            <label class="form-check-label">Student Testimonials</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="news_announcements" checked>
                            <label class="form-check-label">News & Announcements</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="contact_forms">
                            <label class="form-check-label">Contact Forms</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="live_chat">
                            <label class="form-check-label">Live Chat Support</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="features[]" value="social_integration">
                            <label class="form-check-label">Social Media Integration</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Features
                        </button>
                    </form>
                </div>
                
                <!-- Layout Settings -->
                <div class="sidebar-section" id="layout-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-th-large me-2"></i>Layout & Design</h5>
                    </div>
                    
                    <form id="layoutForm" onsubmit="updateLayout(event)">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Header Style</label>
                            <select class="form-control" name="header_style">
                                <option value="classic">Classic Navigation</option>
                                <option value="centered" selected>Centered Logo</option>
                                <option value="minimal">Minimal Header</option>
                                <option value="split">Split Navigation</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Footer Style</label>
                            <select class="form-control" name="footer_style">
                                <option value="simple">Simple Footer</option>
                                <option value="detailed" selected>Detailed Footer</option>
                                <option value="minimal">Minimal Footer</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Page Layout</label>
                            <select class="form-control" name="page_layout">
                                <option value="full-width" selected>Full Width</option>
                                <option value="boxed">Boxed Layout</option>
                                <option value="sidebar">With Sidebar</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Typography</label>
                            <select class="form-control" name="font_family">
                                <option value="inter" selected>Inter (Modern)</option>
                                <option value="roboto">Roboto (Clean)</option>
                                <option value="open-sans">Open Sans (Friendly)</option>
                                <option value="lato">Lato (Professional)</option>
                                <option value="montserrat">Montserrat (Bold)</option>
                            </select>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="layout_options[]" value="sticky_header" checked>
                            <label class="form-check-label">Sticky Header</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="layout_options[]" value="smooth_scroll" checked>
                            <label class="form-check-label">Smooth Scrolling</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="layout_options[]" value="animations" checked>
                            <label class="form-check-label">Page Animations</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="layout_options[]" value="dark_mode">
                            <label class="form-check-label">Dark Mode Toggle</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Layout
                        </button>
                    </form>
                </div>
                
                <!-- Advanced Settings -->
                <div class="sidebar-section" id="advanced-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-code me-2"></i>Advanced Settings</h5>
                    </div>
                    
                    <form id="advancedForm" onsubmit="updateAdvanced(event)">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Custom CSS</label>
                            <textarea class="form-control" name="custom_css" rows="8" placeholder="/* Add your custom CSS here */" style="font-family: monospace; font-size: 12px;">/* Custom styles for your training center */
.custom-hero {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
}

.custom-button {
    border-radius: 25px;
    padding: 12px 30px;
}

.custom-card {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-radius: 12px;
}</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Custom JavaScript</label>
                            <textarea class="form-control" name="custom_js" rows="6" placeholder="// Add your custom JavaScript here" style="font-family: monospace; font-size: 12px;">// Custom JavaScript for enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Custom scripts loaded');
});</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Google Analytics ID</label>
                            <input type="text" class="form-control" name="analytics_id" placeholder="GA-XXXXXXXXX-X">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Meta Tags</label>
                            <textarea class="form-control" name="meta_tags" rows="4" placeholder="Additional meta tags">
<meta name="keywords" content="training, education, certification">
<meta name="author" content="Your Training Center">
<meta property="og:type" content="website"></textarea>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Performance Settings</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="performance[]" value="lazy_loading" checked>
                            <label class="form-check-label">Lazy Loading Images</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="performance[]" value="minify_css" checked>
                            <label class="form-check-label">Minify CSS</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="performance[]" value="cache_enabled" checked>
                            <label class="form-check-label">Enable Caching</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Advanced Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Live Preview Panel -->
            <div class="preview-panel">
                <div class="preview-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="preview-title">
                            <i class="fas fa-eye me-2"></i>Live Preview
                        </h5>
                        <div class="preview-controls">
                            <button class="preview-btn" onclick="refreshPreview()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <?php if(isset($selectedWebsite)): ?>
                            <a href="/t/<?php echo e($selectedWebsite->slug); ?>" class="preview-btn" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="preview-iframe-container">
                    <div class="preview-loading" id="previewLoading">
                        <div class="loading-spinner"></div>
                        <span class="text-muted">Loading website preview...</span>
                    </div>
                    
                    <!-- Show customization preview instead of requiring existing website -->
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <div class="text-center text-muted">
                            <i class="fas fa-paint-brush" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; color: #1e40af;"></i>
                            <h5 class="text-primary">Website Customization</h5>
                            <p>Customize your website settings using the options on the left.<br>Your changes will be applied when your website is created.</p>
                            <div class="mt-3">
                                <span class="badge bg-info">
                                    <i class="fas fa-info-circle me-1"></i>Live preview available after website creation
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Website Request Modal -->
    <div class="modal fade" id="websiteRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('dashboard.submit-customized-website')); ?>" id="customizedWebsiteForm">
                    <?php echo csrf_field(); ?>
                    <!-- Hidden fields to store customization data -->
                    <input type="hidden" name="customization_data" id="customizationData">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-rocket me-2"></i>Submit Website Request
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Ready to create your website!</strong> Your customizations have been saved and will be applied to your new website once approved.
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Business Name *</label>
                                <input type="text" class="form-control form-control-modern" name="business_name" required placeholder="e.g., ARTC Training Center">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Business Type *</label>
                                <select class="form-control form-control-modern" name="business_type" required>
                                    <option value="">Select business type</option>
                                    <option value="Review Center">Review Center</option>
                                    <option value="Training Institute">Training Institute</option>
                                    <option value="Educational Center">Educational Center</option>
                                    <option value="Certification Center">Certification Center</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Business Description *</label>
                                <textarea class="form-control form-control-modern" name="description" rows="3" required placeholder="Describe your training center, services offered, and target audience"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Preferred Domain/URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-gray-100 border-gray-300">/t/</span>
                                    <input type="text" class="form-control form-control-modern" name="domain_preference" placeholder="artc-training">
                                </div>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>This will be your website URL
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Email *</label>
                                <input type="email" class="form-control form-control-modern" name="contact_email" required value="<?php echo e(Auth::guard('smartprep')->user()->email); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control form-control-modern" name="contact_phone" placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6><i class="fas fa-eye me-2"></i>Customization Preview</h6>
                            <div class="customization-summary bg-light p-3 rounded">
                                <div id="customizationSummary">
                                    <p class="text-muted">Your customization settings will be displayed here...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-gray-50">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Submit Website Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Settings tab navigation with enhanced functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navTabs = document.querySelectorAll('.settings-nav-tab');
            const sidebarSections = document.querySelectorAll('.sidebar-section');
            
            navTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const section = this.getAttribute('data-section');
                    
                    // Update active tab
                    navTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update active section
                    sidebarSections.forEach(s => {
                        s.classList.remove('active');
                        s.style.display = 'none';
                    });
                    document.getElementById(section + '-settings').style.display = 'block';
                    document.getElementById(section + '-settings').classList.add('active');
                });
            });
            
            // Initialize color picker synchronization
            initializeColorPickers();
        });

        // Color picker synchronization
        function initializeColorPickers() {
            const colorInputs = document.querySelectorAll('input[type="color"]');
            
            colorInputs.forEach(colorInput => {
                const textInput = colorInput.nextElementSibling;
                
                colorInput.addEventListener('change', function() {
                    textInput.value = this.value;
                });
                
                textInput.addEventListener('change', function() {
                    if(this.value.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) {
                        colorInput.value = this.value;
                    }
                });
            });
        }

        // Form submission handlers
        async function updateGeneral(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'general', 'Updating general settings...');
        }
        
        async function updateBranding(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'branding', 'Updating branding...');
        }
        
        async function updateContent(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'content', 'Updating content...');
        }
        
        async function updateFeatures(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'features', 'Updating features...');
        }
        
        async function updateLayout(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'layout', 'Updating layout...');
        }
        
        async function updateAdvanced(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'advanced', 'Updating advanced settings...');
        }

        // Generic form submission handler
        async function handleFormSubmission(event, section, message) {
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                showLoadingMessage(message);
                
                const response = await fetch(`/dashboard/customize-website/${section}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    showSuccessMessage(`${section} settings updated successfully!`);
                    refreshPreview();
                } else {
                    showErrorMessage(`Failed to update ${section} settings.`);
                }
            } catch (error) {
                showErrorMessage('An error occurred while updating settings.');
                console.error('Error:', error);
            }
        }

        // Preview functions
        function refreshPreview() {
            const iframe = document.getElementById('previewFrame');
            if (iframe) {
                showLoading();
                iframe.src = iframe.src;
            }
        }

        function hideLoading() {
            const loading = document.getElementById('previewLoading');
            if (loading) {
                loading.style.display = 'none';
            }
        }

        function showLoading() {
            const loading = document.getElementById('previewLoading');
            if (loading) {
                loading.style.display = 'block';
            }
        }

        function showError() {
            const loading = document.getElementById('previewLoading');
            if (loading) {
                loading.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <div class="text-muted">Failed to load preview</div>
                `;
            }
        }

        // Utility functions for notifications
        function showLoadingMessage(message) {
            // You can implement toast notifications here
            console.log(message);
        }

        function showSuccessMessage(message) {
            // You can implement toast notifications here
            console.log(message);
        }

        function showErrorMessage(message) {
            // You can implement toast notifications here
            console.error(message);
        }

        // Save and publish functions
        function saveAllSettings() {
            showLoadingMessage('Saving all settings...');
            // Implement save all functionality
        }

        function submitWebsiteRequest() {
            // Gather all customization data from forms
            const customizationData = gatherCustomizationData();
            
            // Set the data in the hidden field
            document.getElementById('customizationData').value = JSON.stringify(customizationData);
            
            // Update the customization summary
            updateCustomizationSummary(customizationData);
            
            // Show the website request modal
            const modal = new bootstrap.Modal(document.getElementById('websiteRequestModal'));
            modal.show();
        }
        
        function gatherCustomizationData() {
            const data = {};
            
            // Gather data from all forms
            const forms = ['generalForm', 'brandingForm', 'contentForm', 'featuresForm', 'layoutForm', 'advancedForm'];
            
            forms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    const formData = new FormData(form);
                    data[formId] = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== '_token') { // Skip CSRF token
                            data[formId][key] = value;
                        }
                    }
                }
            });
            
            return data;
        }
        
        function updateCustomizationSummary(data) {
            let summary = '<div class="row g-3">';
            
            // General Settings
            if (data.generalForm && Object.keys(data.generalForm).length > 0) {
                summary += '<div class="col-md-6"><h6 class="text-primary">General Settings</h6><ul class="list-unstyled small">';
                if (data.generalForm.site_title) summary += `<li><strong>Site Title:</strong> ${data.generalForm.site_title}</li>`;
                if (data.generalForm.tagline) summary += `<li><strong>Tagline:</strong> ${data.generalForm.tagline}</li>`;
                summary += '</ul></div>';
            }
            
            // Branding
            if (data.brandingForm && Object.keys(data.brandingForm).length > 0) {
                summary += '<div class="col-md-6"><h6 class="text-primary">Branding</h6><ul class="list-unstyled small">';
                if (data.brandingForm.primary_color) summary += `<li><strong>Primary Color:</strong> <span class="badge" style="background-color: ${data.brandingForm.primary_color}">${data.brandingForm.primary_color}</span></li>`;
                if (data.brandingForm.secondary_color) summary += `<li><strong>Secondary Color:</strong> <span class="badge" style="background-color: ${data.brandingForm.secondary_color}">${data.brandingForm.secondary_color}</span></li>`;
                summary += '</ul></div>';
            }
            
            // Content
            if (data.contentForm && Object.keys(data.contentForm).length > 0) {
                summary += '<div class="col-md-6"><h6 class="text-primary">Content</h6><ul class="list-unstyled small">';
                if (data.contentForm.hero_title) summary += `<li><strong>Hero Title:</strong> ${data.contentForm.hero_title}</li>`;
                summary += '</ul></div>';
            }
            
            summary += '</div>';
            
            if (Object.keys(data).length === 0) {
                summary = '<p class="text-muted">No customizations have been made yet.</p>';
            }
            
            document.getElementById('customizationSummary').innerHTML = summary;
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\dashboard\customize-website-old.blade.php ENDPATH**/ ?>