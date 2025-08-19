<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Customize Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        .nav-link {
            color: var(--gray-600);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .nav-link:hover {
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
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
            outline: none;
        }
        
        .color-picker-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .color-input {
            width: 40px;
            height: 40px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            cursor: pointer;
        }
        
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
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
        }
        
        .preview-iframe {
            width: 100%;
            height: calc(100vh - 160px);
            border: none;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease;
        }
        
        .preview-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 8px;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-300);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .settings-sidebar {
                width: 300px;
            }
        }
        
        @media (max-width: 992px) {
            .settings-main-layout {
                flex-direction: column;
            }
            
            .settings-sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .preview-panel {
                height: 500px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>SmartPrep
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Auth::guard('smartprep_admin')->check() ? route('smartprep.admin.dashboard') : route('smartprep.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('smartprep.dashboard.customize') }}">
                            <i class="fas fa-palette me-2"></i>Customize Website
                        </a>
                    </li>
                    @if(isset($activeWebsites) && $activeWebsites->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-globe me-2"></i>My Websites
                        </a>
                    </li>
                    @endif
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>{{ Auth::guard('smartprep')->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>Home</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('smartprep.logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
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
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Site Title</label>
                            <input type="text" class="form-control" name="site_title" value="Ascendo Review and Training Center" placeholder="Enter site title">
                            <small class="form-text text-muted">Appears in browser tab and search results</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" name="tagline" value="Review Smarter. Learn Better. Succeed Faster." placeholder="Enter tagline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="admin@artc.com" placeholder="Contact email">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="+1 (555) 123-4567" placeholder="Phone number">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" placeholder="Physical address">123 Education Street, Learning City, LC 12345</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update General Settings
                        </button>
                    </form>
                </div>
                
                <!-- Branding Settings -->
                <div class="sidebar-section" id="branding-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-palette me-2"></i>Branding & Design</h5>
                    </div>
                    
                    <form id="brandingForm" onsubmit="updateBranding(event)" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#667eea" onchange="updatePreviewColor('primary', this.value)">
                                <input type="text" class="form-control" name="primary_color" value="#667eea">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#764ba2" onchange="updatePreviewColor('secondary', this.value)">
                                <input type="text" class="form-control" name="secondary_color" value="#764ba2">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#ffffff" onchange="updatePreviewColor('background', this.value)">
                                <input type="text" class="form-control" name="background_color" value="#ffffff">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Logo Upload</label>
                            <input type="file" class="form-control" name="logo" accept="image/*" onchange="previewLogo(this)">
                            <small class="form-text text-muted">Recommended: 200x60px, PNG format with transparent background</small>
                            <div class="mt-2">
                                <img id="logoPreview" src="#" alt="Logo Preview" style="max-width: 200px; display: none;">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Favicon</label>
                            <input type="file" class="form-control" name="favicon" accept="image/*">
                            <small class="form-text text-muted">32x32px ICO or PNG format</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Custom Font</label>
                            <select class="form-control" name="font_family">
                                <option value="Inter">Inter (Default)</option>
                                <option value="Roboto">Roboto</option>
                                <option value="Open Sans">Open Sans</option>
                                <option value="Lato">Lato</option>
                                <option value="Poppins">Poppins</option>
                                <option value="Montserrat">Montserrat</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Branding
                        </button>
                    </form>
                </div>
                
                <!-- Navigation Settings -->
                <div class="sidebar-section" id="navbar-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-bars me-2"></i>Navigation Bar</h5>
                    </div>
                    
                    <form id="navbarForm" onsubmit="updateNavbar(event)">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" value="Ascendo Review and Training Center" placeholder="Brand name">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Image</label>
                            <input type="file" class="form-control" name="brand_image" accept="image/*">
                            <small class="form-text text-muted">Logo for navigation bar</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Navigation Style</label>
                            <select class="form-control" name="navbar_style">
                                <option value="fixed-top">Fixed Top</option>
                                <option value="sticky-top">Sticky Top</option>
                                <option value="static">Static</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Menu Items</label>
                            <div class="nav-items-container">
                                <div class="nav-item d-flex align-items-center mb-2">
                                    <input type="text" class="form-control me-2" name="nav_items[]" value="Home" placeholder="Menu label">
                                    <input type="text" class="form-control me-2" name="nav_links[]" value="/" placeholder="Link">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeNavItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="nav-item d-flex align-items-center mb-2">
                                    <input type="text" class="form-control me-2" name="nav_items[]" value="Review Programs" placeholder="Menu label">
                                    <input type="text" class="form-control me-2" name="nav_links[]" value="/programs" placeholder="Link">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeNavItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="nav-item d-flex align-items-center mb-2">
                                    <input type="text" class="form-control me-2" name="nav_items[]" value="About Us" placeholder="Menu label">
                                    <input type="text" class="form-control me-2" name="nav_links[]" value="/about" placeholder="Link">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeNavItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addNavItem()">
                                <i class="fas fa-plus me-2"></i>Add Menu Item
                            </button>
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_login_button" checked>
                                <label class="form-check-label">Show Login Button</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Navigation
                        </button>
                    </form>
                </div>
                
                <!-- Homepage Settings -->
                <div class="sidebar-section" id="homepage-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-home me-2"></i>Homepage Content</h5>
                    </div>
                    
                    <form id="homepageForm" onsubmit="updateHomepage(event)" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Title</label>
                            <input type="text" class="form-control" name="hero_title" value="Review Smarter. Learn Better. Succeed Faster." placeholder="Main headline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description">Your premier destination for comprehensive review programs and professional training.</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Background Image</label>
                            <input type="file" class="form-control" name="hero_background" accept="image/*">
                            <small class="form-text text-muted">Recommended: 1920x1080px</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Login Page Image</label>
                            <input type="file" class="form-control" name="login_image" accept="image/*">
                            <small class="form-text text-muted">Image shown on login page</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Primary CTA Button Text</label>
                            <input type="text" class="form-control" name="cta_primary_text" value="Get Started" placeholder="Primary button text">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Primary CTA Button Link</label>
                            <input type="text" class="form-control" name="cta_primary_link" value="/programs" placeholder="Primary button link">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary CTA Button Text</label>
                            <input type="text" class="form-control" name="cta_secondary_text" value="Learn More" placeholder="Secondary button text">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary CTA Button Link</label>
                            <input type="text" class="form-control" name="cta_secondary_link" value="/about" placeholder="Secondary button link">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Features Section Title</label>
                            <input type="text" class="form-control" name="features_title" value="Why Choose Us?" placeholder="Features section title">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Copyright Text</label>
                            <input type="text" class="form-control" name="copyright" value="© Copyright Ascendo Review and Training Center. All Rights Reserved." placeholder="Footer copyright">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Homepage
                        </button>
                    </form>
                </div>
                
                <!-- Student Portal Settings -->
                <div class="sidebar-section" id="student-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-user-graduate me-2"></i>Student Portal</h5>
                    </div>
                    
                    <form id="studentForm" onsubmit="updateStudent(event)">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Student Dashboard Title</label>
                            <input type="text" class="form-control" name="student_dashboard_title" value="Student Dashboard" placeholder="Dashboard title">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Welcome Message</label>
                            <textarea class="form-control" name="student_welcome_message" rows="3" placeholder="Welcome message for students">Welcome to your learning portal. Access your courses, track progress, and achieve your goals.</textarea>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Student Features</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="course_access" checked>
                            <label class="form-check-label">Course Access</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="progress_tracking" checked>
                            <label class="form-check-label">Progress Tracking</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="assignments" checked>
                            <label class="form-check-label">Assignments</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="grades" checked>
                            <label class="form-check-label">Grades & Results</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="calendar" checked>
                            <label class="form-check-label">Calendar & Schedule</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="messages" checked>
                            <label class="form-check-label">Messages</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="resources" checked>
                            <label class="form-check-label">Learning Resources</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="student_features[]" value="certificates">
                            <label class="form-check-label">Certificates</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Student Portal
                        </button>
                    </form>
                </div>
                
                <!-- Professor Panel Settings -->
                <div class="sidebar-section" id="professor-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-chalkboard-teacher me-2"></i>Professor Panel</h5>
                    </div>
                    
                    <form id="professorForm" onsubmit="updateProfessor(event)">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Professor Dashboard Title</label>
                            <input type="text" class="form-control" name="professor_dashboard_title" value="Instructor Dashboard" placeholder="Dashboard title">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Welcome Message</label>
                            <textarea class="form-control" name="professor_welcome_message" rows="3" placeholder="Welcome message for professors">Welcome to your instructor portal. Manage your courses, students, and teaching materials.</textarea>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Professor Features</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="course_management" checked>
                            <label class="form-check-label">Course Management</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="student_management" checked>
                            <label class="form-check-label">Student Management</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="grading" checked>
                            <label class="form-check-label">Grading System</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="attendance" checked>
                            <label class="form-check-label">Attendance Tracking</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="materials">
                            <label class="form-check-label">Learning Materials Upload</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="quiz_generation">
                            <label class="form-check-label">AI Quiz Generation</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="reports" checked>
                            <label class="form-check-label">Student Reports</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="announcements" checked>
                            <label class="form-check-label">Announcements</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="video_upload">
                            <label class="form-check-label">Video Upload</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="professor_features[]" value="meeting_creation">
                            <label class="form-check-label">Virtual Meeting Creation</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Professor Panel
                        </button>
                    </form>
                </div>
                
                <!-- Admin Panel Settings -->
                <div class="sidebar-section" id="admin-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-user-shield me-2"></i>Admin Panel</h5>
                    </div>
                    
                    <form id="adminForm" onsubmit="updateAdmin(event)">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Admin Dashboard Title</label>
                            <input type="text" class="form-control" name="admin_dashboard_title" value="Administrative Dashboard" placeholder="Dashboard title">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Welcome Message</label>
                            <textarea class="form-control" name="admin_welcome_message" rows="3" placeholder="Welcome message for admins">Welcome to the administrative control panel. Manage your training center efficiently.</textarea>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Admin Features</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="user_management" checked>
                            <label class="form-check-label">User Management</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="course_oversight" checked>
                            <label class="form-check-label">Course Oversight</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="financial_reports" checked>
                            <label class="form-check-label">Financial Reports</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="analytics" checked>
                            <label class="form-check-label">Analytics & Statistics</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="system_settings" checked>
                            <label class="form-check-label">System Settings</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="backup_restore">
                            <label class="form-check-label">Backup & Restore</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="email_notifications" checked>
                            <label class="form-check-label">Email Notifications</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="admin_features[]" value="audit_logs">
                            <label class="form-check-label">Audit Logs</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Update Admin Panel
                        </button>
                    </form>
                </div>
                
                <!-- Advanced Settings -->
                <div class="sidebar-section" id="advanced-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-code me-2"></i>Advanced Settings</h5>
                    </div>
                    
                    <form id="advancedForm" onsubmit="updateAdvanced(event)">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Custom CSS</label>
                            <textarea class="form-control" name="custom_css" rows="8" placeholder="/* Add your custom CSS here */" style="font-family: monospace; font-size: 12px;">/* Custom styles for your training center */
.custom-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.custom-button {
    border-radius: 25px;
    padding: 12px 30px;
}</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Custom JavaScript</label>
                            <textarea class="form-control" name="custom_js" rows="6" placeholder="// Add your custom JavaScript here" style="font-family: monospace; font-size: 12px;">// Custom JavaScript for enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
    // Your custom code here
    console.log('A.R.T.C Custom JavaScript Loaded');
});</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Google Analytics ID</label>
                            <input type="text" class="form-control" name="google_analytics" placeholder="GA-XXXXXXXXXX">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Facebook Pixel ID</label>
                            <input type="text" class="form-control" name="facebook_pixel" placeholder="Facebook Pixel ID">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Meta Tags</label>
                            <textarea class="form-control" name="meta_tags" rows="4" placeholder="Additional meta tags">
<meta name="keywords" content="training, education, review, certification">
<meta name="author" content="A.R.T.C">
<meta property="og:type" content="website"></textarea>
                        </div>
                        
                        <h6 class="mt-4 mb-3">System Preferences</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode">
                            <label class="form-check-label">Maintenance Mode</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="debug_mode">
                            <label class="form-check-label">Debug Mode</label>
                        </div>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="cache_enabled" checked>
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
                            <a href="http://localhost/A.R.T.C/public/" class="preview-btn" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="preview-iframe-container">
                    <div class="preview-loading" id="previewLoading">
                        <div class="loading-spinner"></div>
                        <span class="text-muted">Loading A.R.T.C preview...</span>
                    </div>
                    <iframe 
                        class="preview-iframe" 
                        src="http://localhost/A.R.T.C/public/" 
                        title="A.R.T.C Site Preview"
                        id="previewFrame"
                        onload="hideLoading()"
                        onerror="showError()">
                    </iframe>
                </div>
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
            
            // Enable auto-save for important changes
            enableAutoSave();
        });

        // Form submission handlers
        async function updateGeneral(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'general', 'Updating general settings...');
        }
        
        async function updateBranding(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'branding', 'Updating branding...');
        }
        
        async function updateNavbar(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'navbar', 'Updating navigation...');
        }
        
        async function updateHomepage(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'homepage', 'Updating homepage content...');
        }
        
        async function updateStudent(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'student', 'Updating student portal...');
        }
        
        async function updateProfessor(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'professor', 'Updating professor panel...');
        }
        
        async function updateAdmin(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'admin', 'Updating admin panel...');
        }
        
        async function updateAdvanced(event) {
            event.preventDefault();
            await handleFormSubmission(event, 'advanced', 'Updating advanced settings...');
        }

        async function handleFormSubmission(event, settingType, loadingText) {
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            const formData = new FormData(event.target);
            
            // Update button state
            submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText}`;
            submitBtn.disabled = true;
            
            try {
                // Simulate API call - replace with actual endpoint
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                // Success state
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Settings Updated Successfully!';
                showNotification(`${settingType.charAt(0).toUpperCase() + settingType.slice(1)} settings have been updated successfully!`, 'success');
                
                // Refresh preview if needed
                if (['branding', 'navbar', 'homepage'].includes(settingType)) {
                    refreshPreview();
                }
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 2000);
                
            } catch (error) {
                // Error state
                submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Update Failed';
                showNotification('Failed to update settings. Please try again.', 'danger');
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 2000);
            }
        }

        // Color picker management
        function updatePreviewColor(type, color) {
            const textInput = event.target.nextElementSibling;
            if (textInput) {
                textInput.value = color;
            }
            
            // Apply color changes to preview iframe
            try {
                const iframe = document.getElementById('previewFrame');
                if (iframe && iframe.contentDocument) {
                    const iframeDoc = iframe.contentDocument;
                    const root = iframeDoc.documentElement;
                    
                    switch(type) {
                        case 'primary':
                            root.style.setProperty('--primary-color', color);
                            break;
                        case 'secondary':
                            root.style.setProperty('--secondary-color', color);
                            break;
                        case 'background':
                            root.style.setProperty('--background-color', color);
                            break;
                    }
                }
            } catch (e) {
                console.log('Cross-origin iframe access restricted - normal behavior');
            }
            
            showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} color updated to ${color}`, 'info');
        }
        
        function initializeColorPickers() {
            document.querySelectorAll('.color-picker-group').forEach(group => {
                const colorInput = group.querySelector('.color-input');
                const textInput = group.querySelector('input[type="text"]');
                
                if (colorInput && textInput) {
                    // Update color picker when text is changed
                    textInput.addEventListener('input', function() {
                        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                            colorInput.value = this.value;
                        }
                    });
                    
                    // Update text when color picker is changed
                    colorInput.addEventListener('input', function() {
                        textInput.value = this.value;
                    });
                }
            });
        }

        // Logo preview functionality
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.getElementById('logoPreview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'logoPreview';
                        preview.style.maxWidth = '200px';
                        preview.style.marginTop = '10px';
                        preview.style.border = '1px solid #ddd';
                        preview.style.borderRadius = '4px';
                        preview.style.padding = '5px';
                        preview.alt = 'Logo Preview';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    showNotification('Logo preview loaded! Save changes to apply.', 'info');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Navigation menu management
        function addNavItem() {
            const container = document.querySelector('.nav-items-container');
            const navItem = document.createElement('div');
            navItem.className = 'nav-item d-flex align-items-center mb-2';
            navItem.innerHTML = `
                <input type="text" class="form-control me-2" name="nav_items[]" placeholder="Menu label">
                <input type="text" class="form-control me-2" name="nav_links[]" placeholder="Link">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeNavItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(navItem);
            showNotification('New menu item added', 'info');
        }
        
        function removeNavItem(button) {
            button.closest('.nav-item').remove();
            showNotification('Menu item removed', 'info');
        }

        // Preview control functions
        function refreshPreview() {
            const iframe = document.getElementById('previewFrame');
            const loading = document.getElementById('previewLoading');
            
            if (iframe && loading) {
                loading.style.display = 'flex';
                iframe.style.opacity = '0.5';
                
                // Reload iframe
                iframe.src = iframe.src;
                
                showNotification('Refreshing A.R.T.C preview...', 'info');
            }
        }
        
        function hideLoading() {
            const loading = document.getElementById('previewLoading');
            const iframe = document.getElementById('previewFrame');
            
            setTimeout(() => {
                if (loading) loading.style.display = 'none';
                if (iframe) iframe.style.opacity = '1';
            }, 500);
        }
        
        function showError() {
            const loading = document.getElementById('previewLoading');
            if (loading) {
                loading.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <div>Preview failed to load</div>
                        <small>A.R.T.C server may be offline</small>
                    </div>
                `;
            }
        }

        // Enhanced notification system
        function showNotification(message, type = 'success') {
            // Remove existing notifications of the same type
            document.querySelectorAll('.settings-notification').forEach(n => n.remove());
            
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show settings-notification position-fixed`;
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.style.maxWidth = '400px';
            notification.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            
            const iconMap = {
                success: 'fas fa-check-circle',
                danger: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            
            notification.innerHTML = `
                <i class="${iconMap[type]} me-2"></i>
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Global settings management
        async function saveAllSettings() {
            showNotification('Saving all A.R.T.C settings...', 'info');
            
            // Simulate saving all forms
            const forms = document.querySelectorAll('form');
            let successCount = 0;
            
            for (let form of forms) {
                try {
                    // Simulate API call for each form
                    await new Promise(resolve => setTimeout(resolve, 200));
                    successCount++;
                } catch (error) {
                    console.error('Failed to save form:', form.id);
                }
            }
            
            showNotification(`Successfully saved ${successCount} setting sections!`, 'success');
            refreshPreview();
        }
        
        async function submitWebsiteRequest() {
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
            const forms = ['generalForm', 'brandingForm', 'navbarForm', 'homepageForm', 'studentForm', 'professorForm', 'adminForm', 'advancedForm'];
            
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
            
            // Homepage
            if (data.homepageForm && Object.keys(data.homepageForm).length > 0) {
                summary += '<div class="col-md-6"><h6 class="text-primary">Homepage</h6><ul class="list-unstyled small">';
                if (data.homepageForm.title) summary += `<li><strong>Homepage Title:</strong> ${data.homepageForm.title}</li>`;
                summary += '</ul></div>';
            }
            
            summary += '</div>';
            
            if (Object.keys(data).length === 0) {
                summary = '<p class="text-muted">No customizations have been made yet.</p>';
            }
            
            document.getElementById('customizationSummary').innerHTML = summary;
        }

        // Auto-save functionality
        function enableAutoSave() {
            let autoSaveTimeout;
            
            document.querySelectorAll('input, textarea, select').forEach(element => {
                element.addEventListener('input', function() {
                    clearTimeout(autoSaveTimeout);
                    
                    // Auto-save after 3 seconds of inactivity
                    autoSaveTimeout = setTimeout(() => {
                        if (this.name && this.value) {
                            console.log('Auto-saving:', this.name, this.value);
                            // Implement actual auto-save logic here
                        }
                    }, 3000);
                });
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S or Cmd+S to save all
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAllSettings();
            }
            
            // Ctrl+R or Cmd+R to refresh preview
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                refreshPreview();
            }
        });
    </script>

    <!-- Website Request Modal -->
    <div class="modal fade" id="websiteRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('dashboard.submit-customized-website') }}" id="customizedWebsiteForm">
                    @csrf
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
                                <input type="email" class="form-control form-control-modern" name="contact_email" required value="{{ Auth::guard('smartprep')->user()->email }}">
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
</body>
</html>
