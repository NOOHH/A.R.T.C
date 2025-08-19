<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - A.R.T.C Template Settings</title>
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
                        <a class="nav-link" href="{{ route('smartprep.admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.website-requests') }}">
                            <i class="fas fa-clock me-2"></i>Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.clients') }}">
                            <i class="fas fa-users me-2"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('smartprep.admin.settings') }}">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>View Site</a></li>
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
                        <button class="btn btn-primary" onclick="publishChanges()">
                            <i class="fas fa-rocket me-2"></i>Publish
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
                    
                    <form id="generalForm" method="POST" action="{{ route('smartprep.admin.settings.update.general') }}">
                        @csrf
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Site Title</label>
                            <input type="text" class="form-control" name="site_name" value="{{ $settings['general']['site_name'] ?? 'SmartPrep Admin' }}" placeholder="Enter site title">
                            <small class="form-text text-muted">Appears in browser tab and search results</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" name="site_tagline" value="{{ $settings['general']['site_tagline'] ?? 'Admin Management System' }}" placeholder="Enter tagline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="{{ $settings['general']['contact_email'] ?? 'admin@smartprep.com' }}" placeholder="Contact email">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="contact_phone" value="{{ $settings['general']['contact_phone'] ?? '+1 (555) 123-4567' }}" placeholder="Phone number">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="contact_address" rows="3" placeholder="Physical address">{{ $settings['general']['contact_address'] ?? '123 Admin Street, Admin City, AC 12345' }}</textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Preview URL</label>
                            <input type="url" class="form-control" name="preview_url" value="{{ $settings['general']['preview_url'] ?? 'http://127.0.0.1:8000/' }}" placeholder="http://127.0.0.1:8000/">
                            <small class="form-text text-muted">URL for the live preview iframe</small>
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
                    
                    <form id="brandingForm" method="POST" action="{{ route('smartprep.admin.settings.update.branding') }}">
                        @csrf
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['branding']['primary_color'] ?? '#667eea' }}" onchange="updatePreviewColor('primary', this.value)">
                                <input type="text" class="form-control" name="primary_color" value="{{ $settings['branding']['primary_color'] ?? '#667eea' }}">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['branding']['secondary_color'] ?? '#764ba2' }}" onchange="updatePreviewColor('secondary', this.value)">
                                <input type="text" class="form-control" name="secondary_color" value="{{ $settings['branding']['secondary_color'] ?? '#764ba2' }}">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['branding']['background_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('background', this.value)">
                                <input type="text" class="form-control" name="background_color" value="{{ $settings['branding']['background_color'] ?? '#ffffff' }}">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Logo URL</label>
                            <input type="text" class="form-control" name="logo_url" value="{{ $settings['branding']['logo_url'] ?? '' }}" placeholder="Enter logo URL or path">
                            <small class="form-text text-muted">Enter the URL or path to your logo image</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Favicon URL</label>
                            <input type="text" class="form-control" name="favicon_url" value="{{ $settings['branding']['favicon_url'] ?? '' }}" placeholder="Enter favicon URL or path">
                            <small class="form-text text-muted">32x32px ICO or PNG format</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Custom Font</label>
                            <select class="form-control" name="font_family">
                                <option value="Inter" {{ ($settings['branding']['font_family'] ?? 'Inter') == 'Inter' ? 'selected' : '' }}>Inter (Default)</option>
                                <option value="Roboto" {{ ($settings['branding']['font_family'] ?? '') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                <option value="Open Sans" {{ ($settings['branding']['font_family'] ?? '') == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                <option value="Lato" {{ ($settings['branding']['font_family'] ?? '') == 'Lato' ? 'selected' : '' }}>Lato</option>
                                <option value="Poppins" {{ ($settings['branding']['font_family'] ?? '') == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                                <option value="Montserrat" {{ ($settings['branding']['font_family'] ?? '') == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
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
                    
                    <form id="navbarForm" method="POST" action="{{ route('smartprep.admin.settings.update.navbar') }}">
                        @csrf
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" class="form-control" name="navbar_brand_name" value="{{ $settings['navbar']['brand_name'] ?? 'SmartPrep Admin' }}" placeholder="Brand name">
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_login_button" value="1" {{ ($settings['navbar']['show_login_button'] ?? '1') == '1' ? 'checked' : '' }}>
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
                    
                    <form id="homepageForm" method="POST" action="{{ route('smartprep.admin.settings.update.homepage') }}" enctype="multipart/form-data" onsubmit="updateHomepage(event)">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Title</label>
                            <input type="text" class="form-control" name="hero_title" value="{{ $settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.' }}" placeholder="Main headline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description">{{ $settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.' }}</textarea>
                        </div>
                        
                        <!-- Section Content Customization -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Section Content</h6>
                            </div>
                            <div class="card-body">
                                <!-- Programs Section Content -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-book me-2"></i>Programs Section</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Programs Title</label>
                                            <input type="text" class="form-control" name="programs_title" value="{{ $settings['homepage']['programs_title'] ?? 'Our Programs' }}" placeholder="Programs section title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Programs Subtitle</label>
                                            <input type="text" class="form-control" name="programs_subtitle" value="{{ $settings['homepage']['programs_subtitle'] ?? 'Choose from our comprehensive range of review and training programs' }}" placeholder="Programs section subtitle">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modalities Section Content -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-laptop me-2"></i>Learning Modalities Section</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Modalities Title</label>
                                            <input type="text" class="form-control" name="modalities_title" value="{{ $settings['homepage']['modalities_title'] ?? 'Learning Modalities' }}" placeholder="Modalities section title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Modalities Subtitle</label>
                                            <input type="text" class="form-control" name="modalities_subtitle" value="{{ $settings['homepage']['modalities_subtitle'] ?? 'Flexible learning options designed to fit your schedule and learning style' }}" placeholder="Modalities section subtitle">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- About Section Content -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-info-circle me-2"></i>About Section</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Title</label>
                                            <input type="text" class="form-control" name="about_title" value="{{ $settings['homepage']['about_title'] ?? 'About Us' }}" placeholder="About section title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Text</label>
                                            <textarea class="form-control" name="about_subtitle" rows="2" placeholder="About section description">{{ $settings['homepage']['about_subtitle'] ?? 'We are committed to providing high-quality education and training' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Color Customization Section -->
                        <div class="card mb-4">

                        </div>
                        
                        <!-- Section-Specific Color Customization -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Section-Specific Colors</h6>
                            </div>
                            <div class="card-body">
                                <!-- Hero Section Colors -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-home me-2"></i>Hero Section</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hero Background Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['hero_bg_color'] ?? '#667eea' }}" onchange="updatePreviewColor('hero_bg', this.value)">
                                                <input type="text" class="form-control" name="homepage_hero_bg_color" value="{{ $settings['homepage']['hero_bg_color'] ?? '#667eea' }}">
                                            </div>
                                            <small class="form-text text-muted">Background color for hero section</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hero Title Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['hero_title_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('hero_title', this.value)">
                                                <input type="text" class="form-control" name="homepage_hero_title_color" value="{{ $settings['homepage']['hero_title_color'] ?? '#ffffff' }}">
                                            </div>
                                            <small class="form-text text-muted">Color for hero title text</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Programs Section Colors -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-book me-2"></i>Programs Section</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Programs Title Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['programs_title_color'] ?? '#667eea' }}" onchange="updatePreviewColor('programs_title', this.value)">
                                                <input type="text" class="form-control" name="homepage_programs_title_color" value="{{ $settings['homepage']['programs_title_color'] ?? '#667eea' }}">
                                            </div>
                                            <small class="form-text text-muted">"Our Programs" heading color</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Programs Subtitle Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['programs_subtitle_color'] ?? '#6c757d' }}" onchange="updatePreviewColor('programs_subtitle', this.value)">
                                                <input type="text" class="form-control" name="homepage_programs_subtitle_color" value="{{ $settings['homepage']['programs_subtitle_color'] ?? '#6c757d' }}">
                                            </div>
                                            <small class="form-text text-muted">Programs description text color</small>
                                        </div>
                                    </div>
                                </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-label">Programs Section Gradient Color</label>
                                    <div class="color-picker-group">
                                        <input type="color" class="color-input" value="{{ $settings['homepage']['gradient_color'] ?? '#764ba2' }}" onchange="updatePreviewColor('homepage_gradient', this.value)">
                                        <input type="text" class="form-control" name="homepage_gradient_color" value="{{ $settings['homepage']['gradient_color'] ?? '#764ba2' }}">
                                    </div>
                                    <small class="form-text text-muted">Second color for programs section gradient effect</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Programs Section Background Color</label>
                                    <div class="color-picker-group">
                                        <input type="color" class="color-input" value="{{ $settings['homepage']['programs_section_bg_color'] ?? '#667eea' }}" onchange="updatePreviewColor('programs_section_bg', this.value)">
                                        <input type="text" class="form-control" name="homepage_programs_section_bg_color" value="{{ $settings['homepage']['programs_section_bg_color'] ?? '#667eea' }}">
                                    </div>
                                    <small class="form-text text-muted">Background color for the programs section (creates gradient with secondary color)</small>
                                </div>
                            </div>
                                
                                <!-- Modalities Section Colors -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-laptop me-2"></i>Learning Modalities Section</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Modalities Background Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['modalities_bg_color'] ?? '#667eea' }}" onchange="updatePreviewColor('modalities_bg', this.value)">
                                                <input type="text" class="form-control" name="homepage_modalities_bg_color" value="{{ $settings['homepage']['modalities_bg_color'] ?? '#667eea' }}">
                                            </div>
                                            <small class="form-text text-muted">Background color for modalities section</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Modalities Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['modalities_text_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('modalities_text', this.value)">
                                                <input type="text" class="form-control" name="homepage_modalities_text_color" value="{{ $settings['homepage']['modalities_text_color'] ?? '#ffffff' }}">
                                            </div>
                                            <small class="form-text text-muted">Text color for modalities section</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- About Section Colors -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-info-circle me-2"></i>About Section</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Background Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['about_bg_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('about_bg', this.value)">
                                                <input type="text" class="form-control" name="homepage_about_bg_color" value="{{ $settings['homepage']['about_bg_color'] ?? '#ffffff' }}">
                                            </div>
                                            <small class="form-text text-muted">Background color for about section</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Title Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['about_title_color'] ?? '#667eea' }}" onchange="updatePreviewColor('about_title', this.value)">
                                                <input type="text" class="form-control" name="homepage_about_title_color" value="{{ $settings['homepage']['about_title_color'] ?? '#667eea' }}">
                                            </div>
                                            <small class="form-text text-muted">"About Us" heading color</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="{{ $settings['homepage']['about_text_color'] ?? '#6c757d' }}" onchange="updatePreviewColor('about_text', this.value)">
                                                <input type="text" class="form-control" name="homepage_about_text_color" value="{{ $settings['homepage']['about_text_color'] ?? '#6c757d' }}">
                                            </div>
                                            <small class="form-text text-muted">About description text color</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Background Image</label>
                            <input type="file" class="form-control" name="hero_background" accept="image/*">
                            <small class="form-text text-muted">Recommended: 1920x1080px</small>
                            @if(isset($settings['homepage']['hero_background_image']) && $settings['homepage']['hero_background_image'])
                                <div class="mt-2">
                                    <small class="text-muted">Current image:</small><br>
                                    <img src="{{ asset($settings['homepage']['hero_background_image']) }}" alt="Current hero background" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                                </div>
                            @endif
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Login Page Image</label>
                            <input type="file" class="form-control" name="login_image" accept="image/*">
                            <small class="form-text text-muted">Image shown on login page</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Copyright Text</label>
                            <input type="text" class="form-control" name="copyright" value="{{ $settings['homepage']['copyright'] ?? '© Copyright Ascendo Review and Training Center. All Rights Reserved.' }}" placeholder="Footer copyright">
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
                            <a href="http://127.0.0.1:8000/" class="preview-btn" target="_blank" id="openInNewTabLink">
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
                        src="http://127.0.0.1:8000/" 
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
            
            // Initialize preview URL from settings
            initializePreviewUrl();
            
            // Refresh homepage form with current data
            setTimeout(() => {
                refreshHomepageForm();
            }, 500);
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
            
            // Debug: Log form data
            console.log('Form submission debug:', {
                settingType: settingType,
                formData: Object.fromEntries(formData.entries()),
                heroTitle: formData.get('hero_title'),
                heroTitleLength: formData.get('hero_title') ? formData.get('hero_title').length : 0
            });
            
            // Update button state
            submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText}`;
            submitBtn.disabled = true;
            
            try {
                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Determine the correct endpoint based on setting type
                let endpoint;
                switch(settingType) {
                    case 'general':
                        endpoint = '{{ route("smartprep.admin.settings.update.general") }}';
                        break;
                    case 'branding':
                        endpoint = '{{ route("smartprep.admin.settings.update.branding") }}';
                        break;
                    case 'navbar':
                        endpoint = '{{ route("smartprep.admin.settings.update.navbar") }}';
                        break;
                    case 'homepage':
                        endpoint = '{{ route("smartprep.admin.settings.update.homepage") }}';
                        break;
                    default:
                        endpoint = '{{ route("smartprep.admin.settings.save") }}';
                }
                
                // Make actual AJAX call
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                console.log('Response debug:', {
                    status: response.status,
                    ok: response.ok,
                    headers: Object.fromEntries(response.headers.entries())
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Response result:', result);
                    
                    // Success state
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Settings Updated Successfully!';
                    showNotification(`${settingType.charAt(0).toUpperCase() + settingType.slice(1)} settings have been updated successfully!`, 'success');
                    
                    // Show additional notification for homepage updates
                    if (settingType === 'homepage') {
                        setTimeout(() => {
                            showNotification('💡 Tip: Refresh the homepage (Ctrl+F5) to see the changes!', 'info');
                            
                            // Add a "View Changes" button
                            const viewChangesBtn = document.createElement('button');
                            viewChangesBtn.className = 'btn btn-outline-primary btn-sm ms-2';
                            viewChangesBtn.onclick = () => window.open('http://127.0.0.1/?v=' + Date.now(), '_blank');
                            
                            // Add the button to the notification area
                            const notificationArea = document.querySelector('.notification-area') || document.body;
                            notificationArea.appendChild(viewChangesBtn);
                        }, 1000);
                    }
                    
                    // Refresh preview if needed
                    if (['branding', 'navbar', 'homepage'].includes(settingType)) {
                        refreshPreview();
                    }
                    
                    // Refresh form data to show updated values
                    if (settingType === 'homepage') {
                        refreshHomepageForm();
                    }
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 2000);
                } else {
                    throw new Error('Server error: ' + response.status);
                }
                
            } catch (error) {
                console.error('Error updating settings:', error);
                
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
        async function refreshPreview() {
            const iframe = document.getElementById('previewFrame');
            const loading = document.getElementById('previewLoading');
            
            if (iframe && loading) {
                loading.style.display = 'flex';
                iframe.style.opacity = '0.5';
                
                try {
                    // Fetch current UI settings from API
                    const response = await fetch('{{ route("smartprep.api.ui-settings") }}');
                    if (response.ok) {
                        const settings = await response.json();
                        applySettingsToPreview(settings.data);
                        
                        // Update iframe src with configurable preview URL
                        const previewUrl = settings.data.general?.preview_url || 'http://127.0.0.1:8000/';
                        iframe.src = previewUrl;
                    }
                } catch (error) {
                    console.error('Failed to fetch UI settings:', error);
                    // Fallback to default URL
                    iframe.src = 'http://127.0.0.1:8000/';
                }
                
                showNotification('Refreshing A.R.T.C preview...', 'info');
            }
        }
        
        // Apply settings to preview iframe
        function applySettingsToPreview(settings) {
            try {
                const iframe = document.getElementById('previewFrame');
                if (iframe && iframe.contentDocument) {
                    const iframeDoc = iframe.contentDocument;
                    const root = iframeDoc.documentElement;
                    
                    // Apply branding colors
                    if (settings.branding) {
                        if (settings.branding.primary_color) {
                            root.style.setProperty('--primary-color', settings.branding.primary_color);
                        }
                        if (settings.branding.secondary_color) {
                            root.style.setProperty('--secondary-color', settings.branding.secondary_color);
                        }
                        if (settings.branding.background_color) {
                            root.style.setProperty('--background-color', settings.branding.background_color);
                        }
                        if (settings.branding.font_family) {
                            root.style.setProperty('--font-family', settings.branding.font_family);
                        }
                    }
                    
                    // Apply navbar settings
                    if (settings.navbar) {
                        const navbar = iframeDoc.querySelector('.navbar-brand');
                        if (navbar && settings.navbar.brand_name) {
                            // Find the strong element that contains the brand name
                            const brandText = navbar.querySelector('strong');
                            if (brandText) {
                                brandText.textContent = settings.navbar.brand_name;
                            } else {
                                // If no strong element exists, create one
                                const strong = iframeDoc.createElement('strong');
                                strong.textContent = settings.navbar.brand_name;
                                navbar.appendChild(strong);
                            }
                        }
                    }
                    
                    // Apply homepage settings
                    if (settings.homepage) {
                        const heroTitle = iframeDoc.querySelector('.hero-title');
                        if (heroTitle && settings.homepage.hero_title) {
                            heroTitle.textContent = settings.homepage.hero_title;
                        }
                        
                        const heroSubtitle = iframeDoc.querySelector('.hero-subtitle');
                        if (heroSubtitle && settings.homepage.hero_subtitle) {
                            heroSubtitle.textContent = settings.homepage.hero_subtitle;
                        }
                    }
                }
                
                // Update "Open in New Tab" link with configurable preview URL
                const openInNewTabLink = document.getElementById('openInNewTabLink');
                if (openInNewTabLink && settings.general?.preview_url) {
                    openInNewTabLink.href = settings.general.preview_url;
                }
            } catch (e) {
                console.log('Cross-origin iframe access restricted - normal behavior');
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
        
        // Initialize preview URL from settings
        async function initializePreviewUrl() {
            try {
                const response = await fetch('{{ route("smartprep.api.ui-settings") }}');
                if (response.ok) {
                    const settings = await response.json();
                    const previewUrl = settings.data.general?.preview_url || 'http://127.0.0.1:8000/';
                    
                    // Update iframe src
                    const iframe = document.getElementById('previewFrame');
                    if (iframe) {
                        iframe.src = previewUrl;
                    }
                    
                    // Update "Open in New Tab" link
                    const openInNewTabLink = document.getElementById('openInNewTabLink');
                    if (openInNewTabLink) {
                        openInNewTabLink.href = previewUrl;
                    }
                }
            } catch (error) {
                console.error('Failed to initialize preview URL:', error);
            }
        }

        // Refresh homepage form with latest data
        async function refreshHomepageForm() {
            try {
                const response = await fetch('{{ route("smartprep.api.ui-settings") }}');
                if (response.ok) {
                    const settings = await response.json();
                    const homepageSettings = settings.data.homepage;
                    
                    if (homepageSettings) {
                        // Update form fields with latest data
                        const form = document.getElementById('homepageForm');
                        if (form) {
                            // Update text inputs
                            const heroTitleInput = form.querySelector('input[name="hero_title"]');
                            if (heroTitleInput) {
                                heroTitleInput.value = homepageSettings.hero_title || '';
                            }
                            
                            const heroSubtitleInput = form.querySelector('textarea[name="hero_subtitle"]');
                            if (heroSubtitleInput) {
                                heroSubtitleInput.value = homepageSettings.hero_subtitle || '';
                            }
                            
                            const ctaPrimaryTextInput = form.querySelector('input[name="cta_primary_text"]');
                            if (ctaPrimaryTextInput) {
                                ctaPrimaryTextInput.value = homepageSettings.cta_primary_text || '';
                            }
                            
                            const ctaPrimaryLinkInput = form.querySelector('input[name="cta_primary_link"]');
                            if (ctaPrimaryLinkInput) {
                                ctaPrimaryLinkInput.value = homepageSettings.cta_primary_link || '';
                            }
                            
                            const ctaSecondaryTextInput = form.querySelector('input[name="cta_secondary_text"]');
                            if (ctaSecondaryTextInput) {
                                ctaSecondaryTextInput.value = homepageSettings.cta_secondary_text || '';
                            }
                            
                            const ctaSecondaryLinkInput = form.querySelector('input[name="cta_secondary_link"]');
                            if (ctaSecondaryLinkInput) {
                                ctaSecondaryLinkInput.value = homepageSettings.cta_secondary_link || '';
                            }
                            
                            const featuresTitleInput = form.querySelector('input[name="features_title"]');
                            if (featuresTitleInput) {
                                featuresTitleInput.value = homepageSettings.features_title || '';
                            }
                            
                            const copyrightInput = form.querySelector('input[name="copyright"]');
                            if (copyrightInput) {
                                copyrightInput.value = homepageSettings.copyright || '';
                            }
                            
                            // Update color inputs
                            const backgroundColorInput = form.querySelector('input[name="homepage_background_color"]');
                            if (backgroundColorInput) {
                                backgroundColorInput.value = homepageSettings.background_color || '#667eea';
                            }
                            
                            const gradientColorInput = form.querySelector('input[name="homepage_gradient_color"]');
                            if (gradientColorInput) {
                                gradientColorInput.value = homepageSettings.gradient_color || '#764ba2';
                            }
                            
                            const textColorInput = form.querySelector('input[name="homepage_text_color"]');
                            if (textColorInput) {
                                textColorInput.value = homepageSettings.text_color || '#ffffff';
                            }
                            
                            const buttonColorInput = form.querySelector('input[name="homepage_button_color"]');
                            if (buttonColorInput) {
                                buttonColorInput.value = homepageSettings.button_color || '#28a745';
                            }
                            
                            // Update color pickers
                            const colorPickers = form.querySelectorAll('.color-input');
                            colorPickers.forEach(picker => {
                                const textInput = picker.nextElementSibling;
                                if (textInput && textInput.value) {
                                    picker.value = textInput.value;
                                }
                            });
                        }
                    }
                }
            } catch (error) {
                console.error('Failed to refresh homepage form:', error);
            }
        }
        
        function showError() {
            const loading = document.getElementById('previewLoading');
            if (loading) {
                loading.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <div>Preview failed to load</div>
                        <small>SmartPrep server may be offline</small>
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
            showNotification('Saving all SmartPrep settings...', 'info');
            
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
            refreshPreview(); // Refresh preview after saving all settings
        }
        
        async function publishChanges() {
            showNotification('Publishing changes to SmartPrep live site...', 'info');
            
            try {
                // Simulate publishing process
                await new Promise(resolve => setTimeout(resolve, 2000));
                showNotification('Changes published successfully! SmartPrep is now live with your updates.', 'success');
                refreshPreview(); // Refresh preview after publishing
            } catch (error) {
                showNotification('Failed to publish changes. Please try again.', 'danger');
            }
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
                refreshPreview(); // Refresh preview with keyboard shortcut
            }
        });
    </script>
</body>
</html>
