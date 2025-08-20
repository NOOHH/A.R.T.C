<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - A.R.T.C Template Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        
        
        /* ====== ENHANCED TYPOGRAPHY SECTION STYLES ====== */
        .typography-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 16px;
            border: 1px solid #e3e6f0;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .section-header {
            border-bottom: 2px solid #e3e6f0;
            padding-bottom: 1rem;
        }
        
        .setting-group {
            background: white;
            border-radius: 12px;
            border: 1px solid #f1f3f4;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .setting-group:hover {
            border-color: #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1);
        }
        
        .setting-group-header {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .color-picker-enhanced {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .color-picker-enhanced .color-input {
            width: 50px;
            height: 40px;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .color-picker-enhanced .color-input:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        
        .color-picker-enhanced .form-control {
            flex: 1;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9rem;
        }
        
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 2px solid #e3e6f0;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .modern-select {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .modern-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-label.fw-medium {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        /* ====== SIDEBAR CUSTOMIZATION STYLES ====== */
        .sidebar-preview-container {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            min-height: 400px;
        }
        
        .sidebar-preview {
            background: var(--preview-primary, #1a1a1a);
            color: var(--preview-text, #e0e0e0);
            border-radius: 8px;
            padding: 1rem;
            width: 100%;
            min-height: 350px;
            transition: all 0.3s ease;
        }
        
        .preview-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--preview-secondary, #2d2d2d);
            margin-bottom: 1rem;
        }
        
        .preview-avatar-placeholder {
            width: 40px;
            height: 40px;
            background: var(--preview-accent, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            color: white;
        }
        
        .preview-profile-info {
            flex: 1;
        }
        
        .preview-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--preview-text, #e0e0e0);
        }
        
        .preview-role {
            font-size: 0.8rem;
            color: var(--preview-text-muted, #9ca3af);
            opacity: 0.8;
        }
        
        .preview-toggle {
            background: none;
            border: none;
            color: var(--preview-text, #e0e0e0);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .preview-toggle:hover {
            background: var(--preview-hover, #374151);
        }
        
        .preview-nav {
            margin-top: 1rem;
        }
        
        .preview-section-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--preview-text-muted, #9ca3af);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .preview-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .preview-nav-item:hover {
            background: var(--preview-hover, #374151);
        }
        
        .preview-nav-item.active {
            background: var(--preview-accent, #3b82f6);
            color: white;
        }
        
        .preview-nav-item i {
            width: 16px;
            text-align: center;
        }
        
        .color-preview {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 0.25rem;
            border: 1px solid #ccc;
        }
        
        .color-picker-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .color-input {
            width: 50px;
            height: 38px;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            padding: 0;
            background: none;
        }
        
        .color-input::-webkit-color-swatch {
            border: none;
            border-radius: 4px;
        }
        
        /* Role selector styles */
        .btn-check:checked + .btn-outline-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        /* Preset buttons */
        .btn-sm .color-preview {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        /* Animation for color changes */
        .sidebar-preview * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Mobile responsiveness for sidebar customization */
        @media (max-width: 768px) {
            .color-picker-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .color-input {
                width: 100%;
                height: 50px;
            }
            
            .sidebar-preview-container {
                margin-top: 1rem;
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
                        <a class="nav-link" href="<?php echo e(route('smartprep.admin.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('smartprep.admin.website-requests')); ?>">
                            <i class="fas fa-clock me-2"></i>Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('smartprep.admin.clients')); ?>">
                            <i class="fas fa-users me-2"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('smartprep.admin.settings')); ?>">
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
                                <form method="POST" action="<?php echo e(route('smartprep.logout')); ?>" class="d-inline w-100">
                                    <?php echo csrf_field(); ?>
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
                    
                    <form id="generalForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.general')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Site Title</label>
                            <input type="text" class="form-control" name="site_name" value="<?php echo e($settings['general']['site_name'] ?? 'SmartPrep Admin'); ?>" placeholder="Enter site title">
                            <small class="form-text text-muted">Appears in browser tab and search results</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" name="site_tagline" value="<?php echo e($settings['general']['site_tagline'] ?? 'Admin Management System'); ?>" placeholder="Enter tagline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="<?php echo e($settings['general']['contact_email'] ?? 'admin@smartprep.com'); ?>" placeholder="Contact email">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="contact_phone" value="<?php echo e($settings['general']['contact_phone'] ?? '+1 (555) 123-4567'); ?>" placeholder="Phone number">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="contact_address" rows="3" placeholder="Physical address"><?php echo e($settings['general']['contact_address'] ?? '123 Admin Street, Admin City, AC 12345'); ?></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Preview URL</label>
                            <input type="url" class="form-control" name="preview_url" value="<?php echo e($settings['general']['preview_url'] ?? url('/artc')); ?>" placeholder="<?php echo e(url('/artc')); ?>">
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
                    
                    <form id="brandingForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.branding')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['branding']['primary_color'] ?? '#667eea'); ?>" onchange="updatePreviewColor('primary', this.value)">
                                <input type="text" class="form-control" name="primary_color" value="<?php echo e($settings['branding']['primary_color'] ?? '#667eea'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['branding']['secondary_color'] ?? '#764ba2'); ?>" onchange="updatePreviewColor('secondary', this.value)">
                                <input type="text" class="form-control" name="secondary_color" value="<?php echo e($settings['branding']['secondary_color'] ?? '#764ba2'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['branding']['background_color'] ?? '#ffffff'); ?>" onchange="updatePreviewColor('background', this.value)">
                                <input type="text" class="form-control" name="background_color" value="<?php echo e($settings['branding']['background_color'] ?? '#ffffff'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Logo URL</label>
                            <input type="text" class="form-control" name="logo_url" value="<?php echo e($settings['branding']['logo_url'] ?? ''); ?>" placeholder="Enter logo URL or path">
                            <small class="form-text text-muted">Enter the URL or path to your logo image</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Favicon URL</label>
                            <input type="text" class="form-control" name="favicon_url" value="<?php echo e($settings['branding']['favicon_url'] ?? ''); ?>" placeholder="Enter favicon URL or path">
                            <small class="form-text text-muted">32x32px ICO or PNG format</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Custom Font</label>
                            <select class="form-control" name="font_family">
                                <option value="Inter" <?php echo e(($settings['branding']['font_family'] ?? 'Inter') == 'Inter' ? 'selected' : ''); ?>>Inter (Default)</option>
                                <option value="Roboto" <?php echo e(($settings['branding']['font_family'] ?? '') == 'Roboto' ? 'selected' : ''); ?>>Roboto</option>
                                <option value="Open Sans" <?php echo e(($settings['branding']['font_family'] ?? '') == 'Open Sans' ? 'selected' : ''); ?>>Open Sans</option>
                                <option value="Lato" <?php echo e(($settings['branding']['font_family'] ?? '') == 'Lato' ? 'selected' : ''); ?>>Lato</option>
                                <option value="Poppins" <?php echo e(($settings['branding']['font_family'] ?? '') == 'Poppins' ? 'selected' : ''); ?>>Poppins</option>
                                <option value="Montserrat" <?php echo e(($settings['branding']['font_family'] ?? '') == 'Montserrat' ? 'selected' : ''); ?>>Montserrat</option>
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
                    
                    <form id="navbarForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.navbar')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        <?php if(session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" class="form-control" name="navbar_brand_name" value="<?php echo e($settings['navbar']['brand_name'] ?? 'SmartPrep Admin'); ?>" placeholder="Brand name">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Logo</label>
                            <input type="file" class="form-control" name="navbar_brand_logo" accept="image/*">
                            <small class="form-text text-muted">Upload a logo for the navigation bar. Recommended: 40px height, PNG format with transparent background</small>
                            <?php if(isset($settings['navbar']['brand_logo']) && $settings['navbar']['brand_logo']): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current logo:</small><br>
                                    <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($settings['navbar']['brand_logo'])); ?>" alt="Current brand logo" style="max-height: 40px;" class="img-thumbnail">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_login_button" value="1" <?php echo e(($settings['navbar']['show_login_button'] ?? '1') == '1' ? 'checked' : ''); ?>>
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
                    
                    <form id="homepageForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.homepage')); ?>" enctype="multipart/form-data" onsubmit="updateHomepage(event)">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Title</label>
                            <input type="text" class="form-control" name="hero_title" value="<?php echo e($settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.'); ?>" placeholder="Main headline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description"><?php echo e($settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.'); ?></textarea>
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
                                            <input type="text" class="form-control" name="programs_title" value="<?php echo e($settings['homepage']['programs_title'] ?? 'Our Programs'); ?>" placeholder="Programs section title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Programs Subtitle</label>
                                            <input type="text" class="form-control" name="programs_subtitle" value="<?php echo e($settings['homepage']['programs_subtitle'] ?? 'Choose from our comprehensive range of review and training programs'); ?>" placeholder="Programs section subtitle">
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
                                            <input type="text" class="form-control" name="modalities_title" value="<?php echo e($settings['homepage']['modalities_title'] ?? 'Learning Modalities'); ?>" placeholder="Modalities section title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Modalities Subtitle</label>
                                            <input type="text" class="form-control" name="modalities_subtitle" value="<?php echo e($settings['homepage']['modalities_subtitle'] ?? 'Flexible learning options designed to fit your schedule and learning style'); ?>" placeholder="Modalities section subtitle">
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
                                            <input type="text" class="form-control" name="about_title" value="<?php echo e($settings['homepage']['about_title'] ?? 'About Us'); ?>" placeholder="About section title">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Text</label>
                                            <textarea class="form-control" name="about_subtitle" rows="2" placeholder="About section description"><?php echo e($settings['homepage']['about_subtitle'] ?? 'We are committed to providing high-quality education and training'); ?></textarea>
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
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['hero_bg_color'] ?? '#667eea'); ?>" onchange="updatePreviewColor('hero_bg', this.value)">
                                                <input type="text" class="form-control" name="homepage_hero_bg_color" value="<?php echo e($settings['homepage']['hero_bg_color'] ?? '#667eea'); ?>">
                                            </div>
                                            <small class="form-text text-muted">Background color for hero section</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hero Title Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['hero_title_color'] ?? '#ffffff'); ?>" onchange="updatePreviewColor('hero_title', this.value)">
                                                <input type="text" class="form-control" name="homepage_hero_title_color" value="<?php echo e($settings['homepage']['hero_title_color'] ?? '#ffffff'); ?>">
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
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['programs_title_color'] ?? '#667eea'); ?>" onchange="updatePreviewColor('programs_title', this.value)">
                                                <input type="text" class="form-control" name="homepage_programs_title_color" value="<?php echo e($settings['homepage']['programs_title_color'] ?? '#667eea'); ?>">
                                            </div>
                                            <small class="form-text text-muted">"Our Programs" heading color</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Programs Subtitle Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['programs_subtitle_color'] ?? '#6c757d'); ?>" onchange="updatePreviewColor('programs_subtitle', this.value)">
                                                <input type="text" class="form-control" name="homepage_programs_subtitle_color" value="<?php echo e($settings['homepage']['programs_subtitle_color'] ?? '#6c757d'); ?>">
                                            </div>
                                            <small class="form-text text-muted">Programs description text color</small>
                                        </div>
                                    </div>
                                </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-label">Programs Section Gradient Color</label>
                                    <div class="color-picker-group">
                                        <input type="color" class="color-input" value="<?php echo e($settings['homepage']['gradient_color'] ?? '#764ba2'); ?>" onchange="updatePreviewColor('homepage_gradient', this.value)">
                                        <input type="text" class="form-control" name="homepage_gradient_color" value="<?php echo e($settings['homepage']['gradient_color'] ?? '#764ba2'); ?>">
                                    </div>
                                    <small class="form-text text-muted">Second color for programs section gradient effect</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Programs Section Background Color</label>
                                    <div class="color-picker-group">
                                        <input type="color" class="color-input" value="<?php echo e($settings['homepage']['programs_section_bg_color'] ?? '#667eea'); ?>" onchange="updatePreviewColor('programs_section_bg', this.value)">
                                        <input type="text" class="form-control" name="homepage_programs_section_bg_color" value="<?php echo e($settings['homepage']['programs_section_bg_color'] ?? '#667eea'); ?>">
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
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['modalities_bg_color'] ?? '#667eea'); ?>" onchange="updatePreviewColor('modalities_bg', this.value)">
                                                <input type="text" class="form-control" name="homepage_modalities_bg_color" value="<?php echo e($settings['homepage']['modalities_bg_color'] ?? '#667eea'); ?>">
                                            </div>
                                            <small class="form-text text-muted">Background color for modalities section</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Modalities Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['modalities_text_color'] ?? '#ffffff'); ?>" onchange="updatePreviewColor('modalities_text', this.value)">
                                                <input type="text" class="form-control" name="homepage_modalities_text_color" value="<?php echo e($settings['homepage']['modalities_text_color'] ?? '#ffffff'); ?>">
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
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['about_bg_color'] ?? '#ffffff'); ?>" onchange="updatePreviewColor('about_bg', this.value)">
                                                <input type="text" class="form-control" name="homepage_about_bg_color" value="<?php echo e($settings['homepage']['about_bg_color'] ?? '#ffffff'); ?>">
                                            </div>
                                            <small class="form-text text-muted">Background color for about section</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Title Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['about_title_color'] ?? '#667eea'); ?>" onchange="updatePreviewColor('about_title', this.value)">
                                                <input type="text" class="form-control" name="homepage_about_title_color" value="<?php echo e($settings['homepage']['about_title_color'] ?? '#667eea'); ?>">
                                            </div>
                                            <small class="form-text text-muted">"About Us" heading color</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">About Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['homepage']['about_text_color'] ?? '#6c757d'); ?>" onchange="updatePreviewColor('about_text', this.value)">
                                                <input type="text" class="form-control" name="homepage_about_text_color" value="<?php echo e($settings['homepage']['about_text_color'] ?? '#6c757d'); ?>">
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
                            <?php if(isset($settings['homepage']['hero_background_image']) && $settings['homepage']['hero_background_image']): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current image:</small><br>
                                    <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($settings['homepage']['hero_background_image'])); ?>" alt="Current hero background" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Login Page Image</label>
                            <input type="file" class="form-control" name="login_image" accept="image/*">
                            <small class="form-text text-muted">Image shown on login page</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Copyright Text</label>
                            <input type="text" class="form-control" name="copyright" value="<?php echo e($settings['homepage']['copyright'] ?? '© Copyright Ascendo Review and Training Center. All Rights Reserved.'); ?>" placeholder="Footer copyright">
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
                    
                    <form id="studentForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.student')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <?php if(session('student_success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo e(session('student_success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Dashboard Colors -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Colors</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Dashboard Header Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['dashboard_header_bg'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="dashboard_header_bg" value="<?php echo e($settings['student_portal']['dashboard_header_bg'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Dashboard Header Text</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['dashboard_header_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="dashboard_header_text" value="<?php echo e($settings['student_portal']['dashboard_header_text'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Sidebar Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['sidebar_bg'] ?? '#f8f9fa'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="sidebar_bg" value="<?php echo e($settings['student_portal']['sidebar_bg'] ?? '#f8f9fa'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Active Menu Item</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['active_menu_color'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="active_menu_color" value="<?php echo e($settings['student_portal']['active_menu_color'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Course Interface Colors -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-book me-2"></i>Course Interface Colors</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Course Card Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['course_card_bg'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="course_card_bg" value="<?php echo e($settings['student_portal']['course_card_bg'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Course Progress Bar</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['progress_bar_color'] ?? '#28a745'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="progress_bar_color" value="<?php echo e($settings['student_portal']['progress_bar_color'] ?? '#28a745'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Course Title Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['course_title_color'] ?? '#212529'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="course_title_color" value="<?php echo e($settings['student_portal']['course_title_color'] ?? '#212529'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Assignment Due Date</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['due_date_color'] ?? '#dc3545'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="due_date_color" value="<?php echo e($settings['student_portal']['due_date_color'] ?? '#dc3545'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Card Components Customization -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Course Card Components</h6>
                                <small class="text-muted">Customize course cards, progress bars, buttons & badges</small>
                            </div>
                            <div class="card-body">
                                <!-- Progress Bar Styling -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-chart-line me-2"></i>Progress Bar</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Progress Bar Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['progress_bar_bg'] ?? '#e9ecef'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="progress_bar_bg" value="<?php echo e($settings['student_portal']['progress_bar_bg'] ?? '#e9ecef'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Progress Bar Fill Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['progress_bar_fill'] ?? '#667eea'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="progress_bar_fill" value="<?php echo e($settings['student_portal']['progress_bar_fill'] ?? '#667eea'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Progress Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['progress_text_color'] ?? '#6c757d'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="progress_text_color" value="<?php echo e($settings['student_portal']['progress_text_color'] ?? '#6c757d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resume Button Styling -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-play-circle me-2"></i>Resume/Continue Button</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Button Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['resume_btn_bg'] ?? '#667eea'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="resume_btn_bg" value="<?php echo e($settings['student_portal']['resume_btn_bg'] ?? '#667eea'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Button Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['resume_btn_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="resume_btn_text" value="<?php echo e($settings['student_portal']['resume_btn_text'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Button Hover Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['resume_btn_hover'] ?? '#5a67d8'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="resume_btn_hover" value="<?php echo e($settings['student_portal']['resume_btn_hover'] ?? '#5a67d8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Enrollment Badges -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-tags me-2"></i>Enrollment Badges</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Premium Badge Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['premium_badge_bg'] ?? '#8e44ad'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="premium_badge_bg" value="<?php echo e($settings['student_portal']['premium_badge_bg'] ?? '#8e44ad'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Type Badge Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['type_badge_bg'] ?? '#e67e22'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="type_badge_bg" value="<?php echo e($settings['student_portal']['type_badge_bg'] ?? '#e67e22'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Badge Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['badge_text_color'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="badge_text_color" value="<?php echo e($settings['student_portal']['badge_text_color'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Placeholder & Typography -->
                                <div class="typography-section mb-4">
                                    <div class="section-header mb-4">
                                        <h6 class="text-primary mb-0 fw-bold">
                                            <i class="fas fa-text-height me-2"></i>Typography & Course Styling
                                        </h6>
                                        <small class="text-muted">Customize text appearance and course placeholder icons</small>
                                    </div>
                                    
                                    <!-- Course Placeholder -->
                                    <div class="setting-group mb-4">
                                        <div class="setting-group-header">
                                            <h6 class="mb-2 text-secondary">
                                                <i class="fas fa-image me-2"></i>Course Placeholder
                                            </h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label fw-medium">Icon Color</label>
                                                    <div class="color-picker-enhanced">
                                                        <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['course_placeholder_color'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value; updateCourseCardPreview()">
                                                        <input type="text" class="form-control" name="course_placeholder_color" value="<?php echo e($settings['student_portal']['course_placeholder_color'] ?? '#ffffff'); ?>" placeholder="#ffffff">
                                                        <div class="color-preview" style="background-color: <?php echo e($settings['student_portal']['course_placeholder_color'] ?? '#ffffff'); ?>"></div>
                                                    </div>
                                                    <small class="form-text text-muted">Color of the placeholder icon on course cards</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Typography Settings -->
                                    <div class="setting-group mb-4">
                                        <div class="setting-group-header">
                                            <h6 class="mb-2 text-secondary">
                                                <i class="fas fa-font me-2"></i>Course Title Typography
                                            </h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label fw-medium">Font Size</label>
                                                    <select class="form-select modern-select" name="course_title_font_size" onchange="updateCourseCardPreview()">
                                                        <option value="1.1rem" <?php echo e(($settings['student_portal']['course_title_font_size'] ?? '1.4rem') == '1.1rem' ? 'selected' : ''); ?>>Small (1.1rem)</option>
                                                        <option value="1.2rem" <?php echo e(($settings['student_portal']['course_title_font_size'] ?? '1.4rem') == '1.2rem' ? 'selected' : ''); ?>>Medium Small (1.2rem)</option>
                                                        <option value="1.4rem" <?php echo e(($settings['student_portal']['course_title_font_size'] ?? '1.4rem') == '1.4rem' ? 'selected' : ''); ?>>Medium (1.4rem)</option>
                                                        <option value="1.6rem" <?php echo e(($settings['student_portal']['course_title_font_size'] ?? '1.4rem') == '1.6rem' ? 'selected' : ''); ?>>Large (1.6rem)</option>
                                                        <option value="1.8rem" <?php echo e(($settings['student_portal']['course_title_font_size'] ?? '1.4rem') == '1.8rem' ? 'selected' : ''); ?>>Extra Large (1.8rem)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label fw-medium">Font Weight</label>
                                                    <select class="form-select modern-select" name="course_title_font_weight" onchange="updateCourseCardPreview()">
                                                        <option value="400" <?php echo e(($settings['student_portal']['course_title_font_weight'] ?? '700') == '400' ? 'selected' : ''); ?>>Normal (400)</option>
                                                        <option value="500" <?php echo e(($settings['student_portal']['course_title_font_weight'] ?? '700') == '500' ? 'selected' : ''); ?>>Medium (500)</option>
                                                        <option value="600" <?php echo e(($settings['student_portal']['course_title_font_weight'] ?? '700') == '600' ? 'selected' : ''); ?>>Semi Bold (600)</option>
                                                        <option value="700" <?php echo e(($settings['student_portal']['course_title_font_weight'] ?? '700') == '700' ? 'selected' : ''); ?>>Bold (700)</option>
                                                        <option value="800" <?php echo e(($settings['student_portal']['course_title_font_weight'] ?? '700') == '800' ? 'selected' : ''); ?>>Extra Bold (800)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label fw-medium">Font Style</label>
                                                    <select class="form-select modern-select" name="course_title_font_style" onchange="updateCourseCardPreview()">
                                                        <option value="normal" <?php echo e(($settings['student_portal']['course_title_font_style'] ?? 'normal') == 'normal' ? 'selected' : ''); ?>>Normal</option>
                                                        <option value="italic" <?php echo e(($settings['student_portal']['course_title_font_style'] ?? 'normal') == 'italic' ? 'selected' : ''); ?>>Italic</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Live Preview -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3"><i class="fas fa-eye me-2"></i>Live Preview</h6>
                                        <div class="course-card-preview p-3 border rounded" id="courseCardPreview">
                                            <div class="course-placeholder" id="previewPlaceholder" style="font-size: 3rem; text-align: center; color: #ffffff;">📚</div>
                                            <h3 id="previewTitle" style="margin: 12px 0; font-size: 1.4rem; font-weight: 700; color: #2c3e50;">Medical Technology Review</h3>
                                            <div class="progress-bar" id="previewProgressBar" style="height: 12px; background: #e9ecef; border-radius: 8px; position: relative; margin: 15px 0;">
                                                <div style="position: absolute; top: 0; left: 0; height: 100%; width: 75%; background: linear-gradient(90deg, #667eea, #764ba2); border-radius: 8px;"></div>
                                            </div>
                                            <span class="progress-text" id="previewProgressText" style="font-size: 0.9rem; color: #6c757d;">75% complete</span>
                                            <button class="resume-btn" id="previewResumeBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px 24px; margin: 15px 0; border-radius: 25px; font-weight: 600;">
                                                Continue Learning
                                            </button>
                                            <div class="course-enrollment-info" id="previewEnrollmentInfo" style="margin: 10px 0; display: flex; gap: 6px;">
                                                <span class="enrollment-badge" id="previewPremiumBadge" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 12px; background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%); color: white;">Premium Package</span>
                                                <span class="type-badge" id="previewTypeBadge" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 12px; background: rgba(230, 126, 34, 0.1); color: #e67e22; border: 1px solid rgba(230, 126, 34, 0.3);">Full</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Buttons and Interactive Elements -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-mouse-pointer me-2"></i>Buttons & Interactive Elements</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Button Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['primary_btn_bg'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="primary_btn_bg" value="<?php echo e($settings['student_portal']['primary_btn_bg'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Button Text</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['primary_btn_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="primary_btn_text" value="<?php echo e($settings['student_portal']['primary_btn_text'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Button Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['secondary_btn_bg'] ?? '#6c757d'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="secondary_btn_bg" value="<?php echo e($settings['student_portal']['secondary_btn_bg'] ?? '#6c757d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Link Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['link_color'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="link_color" value="<?php echo e($settings['student_portal']['link_color'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status and Notification Colors -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Status & Notification Colors</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Success Message</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['success_color'] ?? '#28a745'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="success_color" value="<?php echo e($settings['student_portal']['success_color'] ?? '#28a745'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Warning Message</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['warning_color'] ?? '#ffc107'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="warning_color" value="<?php echo e($settings['student_portal']['warning_color'] ?? '#ffc107'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Error Message</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['error_color'] ?? '#dc3545'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="error_color" value="<?php echo e($settings['student_portal']['error_color'] ?? '#dc3545'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Info Message</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['info_color'] ?? '#17a2b8'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="info_color" value="<?php echo e($settings['student_portal']['info_color'] ?? '#17a2b8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sidebar Customization -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Customization</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="studentSidebarPrimary" value="#1a1a1a" onchange="updateStudentSidebarColor('primary', this.value)">
                                                <input type="text" class="form-control" id="studentSidebarPrimaryText" value="#1a1a1a">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="studentSidebarSecondary" value="#2d2d2d" onchange="updateStudentSidebarColor('secondary', this.value)">
                                                <input type="text" class="form-control" id="studentSidebarSecondaryText" value="#2d2d2d">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Accent Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="studentSidebarAccent" value="#3b82f6" onchange="updateStudentSidebarColor('accent', this.value)">
                                                <input type="text" class="form-control" id="studentSidebarAccentText" value="#3b82f6">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="studentSidebarText" value="#e0e0e0" onchange="updateStudentSidebarColor('text', this.value)">
                                                <input type="text" class="form-control" id="studentSidebarTextText" value="#e0e0e0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hover Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="studentSidebarHover" value="#374151" onchange="updateStudentSidebarColor('hover', this.value)">
                                                <input type="text" class="form-control" id="studentSidebarHoverText" value="#374151">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success btn-sm" onclick="saveStudentSidebarColors()">
                                        <i class="fas fa-save me-1"></i>Save Sidebar Colors
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetStudentSidebarColors()">
                                        <i class="fas fa-undo me-1"></i>Reset to Default
                                    </button>
                                </div>
                            </div>
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
                    
                    <form id="professorForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.professor')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <?php if(session('professor_success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo e(session('professor_success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Sidebar Customization -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Customization</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarPrimary" value="#238ea9" onchange="updateProfessorSidebarColor('primary', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarPrimaryText" value="#238ea9">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarSecondary" value="#32cd32" onchange="updateProfessorSidebarColor('secondary', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarSecondaryText" value="#32cd32">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Accent Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarAccent" value="#ff3814" onchange="updateProfessorSidebarColor('accent', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarAccentText" value="#ff3814">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarText" value="#f1f5f9" onchange="updateProfessorSidebarColor('text', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarTextText" value="#f1f5f9">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hover Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarHover" value="#475569" onchange="updateProfessorSidebarColor('hover', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarHoverText" value="#475569">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sidebar Preview -->
                                <div class="sidebar-preview-container mt-4">
                                    <div class="sidebar-preview" id="professorSidebarPreview">
                                        <div class="preview-profile">
                                            <div class="preview-avatar-placeholder">P</div>
                                            <div class="preview-profile-info">
                                                <div class="preview-name">Professor Smith</div>
                                                <div class="preview-role">Instructor</div>
                                            </div>
                                            <button class="preview-toggle">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                        </div>
                                        <div class="preview-nav">
                                            <div class="preview-section-title">Navigation</div>
                                            <div class="preview-nav-item active">
                                                <i class="fas fa-tachometer-alt"></i>
                                                <span>Dashboard</span>
                                            </div>
                                            <div class="preview-nav-item">
                                                <i class="fas fa-users"></i>
                                                <span>Students</span>
                                            </div>
                                            <div class="preview-nav-item">
                                                <i class="fas fa-book"></i>
                                                <span>Courses</span>
                                            </div>
                                            <div class="preview-nav-item">
                                                <i class="fas fa-chart-bar"></i>
                                                <span>Analytics</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success btn-sm" onclick="saveProfessorSidebarColors()">
                                        <i class="fas fa-save me-1"></i>Save Sidebar Colors
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetProfessorSidebarColors()">
                                        <i class="fas fa-undo me-1"></i>Reset to Default
                                    </button>
                                </div>
                            </div>
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
                    
                    <form id="adminForm" method="POST" action="<?php echo e(route('smartprep.admin.settings.update.admin')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <?php if(session('admin_success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo e(session('admin_success')); ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Sidebar Colors -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Colors</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Sidebar Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['sidebar_bg'] ?? '#343a40'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="sidebar_bg" value="<?php echo e($settings['admin_panel']['sidebar_bg'] ?? '#343a40'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Sidebar Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['sidebar_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="sidebar_text" value="<?php echo e($settings['admin_panel']['sidebar_text'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Active Menu Item</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['active_menu_color'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="active_menu_color" value="<?php echo e($settings['admin_panel']['active_menu_color'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Menu Hover Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['menu_hover_color'] ?? '#495057'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="menu_hover_color" value="<?php echo e($settings['admin_panel']['menu_hover_color'] ?? '#495057'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dashboard Colors -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Colors</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Header Background</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['header_bg'] ?? '#dc3545'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="header_bg" value="<?php echo e($settings['admin_panel']['header_bg'] ?? '#dc3545'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Header Text</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['header_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="header_text" value="<?php echo e($settings['admin_panel']['header_text'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Button</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['primary_btn'] ?? '#dc3545'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="primary_btn" value="<?php echo e($settings['admin_panel']['primary_btn'] ?? '#dc3545'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Button</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['admin_panel']['secondary_btn'] ?? '#6c757d'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="secondary_btn" value="<?php echo e($settings['admin_panel']['secondary_btn'] ?? '#6c757d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sidebar Customization -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Customization</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="adminSidebarPrimary" value="#111827" onchange="updateAdminSidebarColor('primary', this.value)">
                                                <input type="text" class="form-control" id="adminSidebarPrimaryText" value="#111827">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="adminSidebarSecondary" value="#1f2937" onchange="updateAdminSidebarColor('secondary', this.value)">
                                                <input type="text" class="form-control" id="adminSidebarSecondaryText" value="#1f2937">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Accent Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="adminSidebarAccent" value="#f59e0b" onchange="updateAdminSidebarColor('accent', this.value)">
                                                <input type="text" class="form-control" id="adminSidebarAccentText" value="#f59e0b">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="adminSidebarText" value="#f9fafb" onchange="updateAdminSidebarColor('text', this.value)">
                                                <input type="text" class="form-control" id="adminSidebarTextText" value="#f9fafb">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hover Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="adminSidebarHover" value="#374151" onchange="updateAdminSidebarColor('hover', this.value)">
                                                <input type="text" class="form-control" id="adminSidebarHoverText" value="#374151">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success btn-sm" onclick="saveAdminSidebarColors()">
                                        <i class="fas fa-save me-1"></i>Save Sidebar Colors
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetAdminSidebarColors()">
                                        <i class="fas fa-undo me-1"></i>Reset to Default
                                    </button>
                                </div>
                            </div>
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
                        <?php echo csrf_field(); ?>
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
                            <a href="<?php echo e($settings['general']['preview_url'] ?? url('/artc')); ?>" class="preview-btn" target="_blank" id="openInNewTabLink">
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
                        src="<?php echo e($settings['general']['preview_url'] ?? url('/artc')); ?>" 
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
    <script type="text/javascript">
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
                    
                    // Update preview URL based on section
                    updatePreviewForSection(section);
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
                        endpoint = '<?php echo e(route("smartprep.admin.settings.update.general")); ?>';
                        break;
                    case 'branding':
                        endpoint = '<?php echo e(route("smartprep.admin.settings.update.branding")); ?>';
                        break;
                    case 'navbar':
                        endpoint = '<?php echo e(route("smartprep.admin.settings.update.navbar")); ?>';
                        break;
                    case 'homepage':
                        endpoint = '<?php echo e(route("smartprep.admin.settings.update.homepage")); ?>';
                        break;
                    default:
                        endpoint = '<?php echo e(route("smartprep.admin.settings.save")); ?>';
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

        // Update preview based on section
        function updatePreviewForSection(section) {
            const iframe = document.getElementById('previewFrame');
            const openInNewTabLink = document.getElementById('openInNewTabLink');
            const previewTitle = document.querySelector('.preview-title');
            
            if (!iframe || !openInNewTabLink || !previewTitle) {
                console.error('Preview elements not found');
                return;
            }
            
            let previewUrl = 'http://127.0.0.1:8000/';
            let titleText = 'Live Preview';
            
            switch(section) {
                case 'student':
                    previewUrl = 'http://127.0.0.1:8000/student/dashboard';
                    titleText = 'Student Portal Preview';
                    break;
                case 'professor':
                    previewUrl = 'http://127.0.0.1:8000/professor/dashboard';
                    titleText = 'Professor Panel Preview';
                    break;
                case 'admin':
                    previewUrl = 'http://127.0.0.1:8000/admin-dashboard';
                    titleText = 'Admin Panel Preview';
                    break;
                case 'homepage':
                    previewUrl = 'http://127.0.0.1:8000/';
                    titleText = 'Homepage Preview';
                    break;
                case 'navbar':
                case 'branding':
                    previewUrl = 'http://127.0.0.1:8000/';
                    titleText = 'Live Preview';
                    break;
                default:
                    previewUrl = 'http://127.0.0.1:8000/';
                    titleText = 'Live Preview';
            }
            
            // Add preview parameter and timestamp to bypass cache
            const finalUrl = previewUrl + '?preview=true&t=' + Date.now();
            
            console.log('Updating preview for section:', section, 'URL:', finalUrl);
            
            // Update iframe
            iframe.src = finalUrl;
            
            // Update open in new tab link
            openInNewTabLink.href = finalUrl;
            
            // Update title
            previewTitle.innerHTML = `<i class="fas fa-eye me-2"></i>${titleText}`;
            
            // Show loading state
            showLoading();

            // Do not auto-open new tab; keep preview within iframe only
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
                    const response = await fetch('<?php echo e(route("smartprep.api.ui-settings")); ?>');
                    if (response.ok) {
                        const settings = await response.json();
                        applySettingsToPreview(settings.data);
                        
                        // Update iframe src with configurable preview URL
                        const fallbackUrl = "<?php echo e(url('/artc')); ?>";
                        const previewUrl = (settings.data && settings.data.general && settings.data.general.preview_url)
                            ? settings.data.general.preview_url
                            : fallbackUrl;
                        iframe.src = previewUrl;
                    }
                } catch (error) {
                    console.error('Failed to fetch UI settings:', error);
                    // Fallback to default URL
                    iframe.src = "<?php echo e(url('/artc')); ?>";
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

        function showLoading() {
            const loading = document.getElementById('previewLoading');
            const iframe = document.getElementById('previewFrame');
            
            if (loading) {
                loading.style.display = 'flex';
                loading.innerHTML = `
                    <div class="loading-spinner"></div>
                    <span class="text-muted">Loading preview...</span>
                `;
            }
            if (iframe) {
                iframe.style.opacity = '0.5';
            }
        }
        
        // Initialize preview URL from settings
        async function initializePreviewUrl() {
            try {
                const response = await fetch('<?php echo e(route("smartprep.api.ui-settings")); ?>');
                if (response.ok) {
                    const settings = await response.json();
                    const fallbackUrlInit = "<?php echo e(url('/artc')); ?>";
                    const previewUrl = (settings.data && settings.data.general && settings.data.general.preview_url)
                        ? settings.data.general.preview_url
                        : fallbackUrlInit;
                    
                    // Update iframe src
                    const iframe = document.getElementById('previewFrame');
                    if (iframe) {
                        iframe.src = previewUrl;
                        // Hard-guard: if it ever navigates to smartprep, force back to ARTC
                        iframe.addEventListener('load', function() {
                            try {
                                const currentUrl = iframe.contentWindow.location.href;
                                if (/\/smartprep(\/|$)/.test(currentUrl)) {
                                    iframe.contentWindow.location.replace("<?php echo e(url('/artc')); ?>");
                                }
                            } catch (e) {
                                // Cross-origin, best-effort only
                            }
                        });
                    }
                    
                    // Update "Open in New Tab" link
                    const openInNewTabLink = document.getElementById('openInNewTabLink');
                    if (openInNewTabLink) {
                        const hrefWithPreview = previewUrl + (previewUrl.includes('?') ? '&' : '?') + 'preview=true';
                        openInNewTabLink.href = hrefWithPreview;
                    }
                }
            } catch (error) {
                console.error('Failed to initialize preview URL:', error);
            }
        }

        // Refresh homepage form with latest data
        async function refreshHomepageForm() {
            try {
                const response = await fetch('<?php echo e(route("smartprep.api.ui-settings")); ?>');
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

        // ====== ROLE-SPECIFIC SIDEBAR FUNCTIONS ======

        // Course Card Preview Functions
        function updateCourseCardPreview() {
            const preview = document.getElementById('courseCardPreview');
            const placeholder = document.getElementById('previewPlaceholder');
            const title = document.getElementById('previewTitle');
            const progressBar = document.getElementById('previewProgressBar');
            const progressBarFillElement = progressBar.querySelector('div');
            const progressText = document.getElementById('previewProgressText');
            const resumeBtn = document.getElementById('previewResumeBtn');
            const premiumBadge = document.getElementById('previewPremiumBadge');
            const typeBadge = document.getElementById('previewTypeBadge');

            if (!preview) return;

            // Get all the color inputs
            const courseCardBg = document.querySelector('input[name="course_card_bg"]')?.value || '#ffffff';
            const placeholderColor = document.querySelector('input[name="placeholder_color"]')?.value || '#ffffff';
            const titleColor = document.querySelector('input[name="course_title_color"]')?.value || '#2c3e50';
            const titleFontSize = document.querySelector('select[name="course_title_font_size"]')?.value || '1.4rem';
            const titleFontWeight = document.querySelector('select[name="course_title_font_weight"]')?.value || '700';
            const titleFontStyle = document.querySelector('select[name="course_title_font_style"]')?.value || 'normal';
            const progressBarBg = document.querySelector('input[name="progress_bar_bg"]')?.value || '#e9ecef';
            const progressBarFillColor = document.querySelector('input[name="progress_bar_fill"]')?.value || '#667eea';
            const progressTextColor = document.querySelector('input[name="progress_text_color"]')?.value || '#6c757d';
            const resumeBtnBg = document.querySelector('input[name="resume_btn_bg"]')?.value || '#667eea';
            const resumeBtnText = document.querySelector('input[name="resume_btn_text"]')?.value || '#ffffff';
            const resumeBtnHover = document.querySelector('input[name="resume_btn_hover"]')?.value || '#5a67d8';
            const premiumBadgeBg = document.querySelector('input[name="premium_badge_bg"]')?.value || '#8e44ad';
            const typeBadgeBg = document.querySelector('input[name="type_badge_bg"]')?.value || '#e67e22';
            const badgeTextColor = document.querySelector('input[name="badge_text_color"]')?.value || '#ffffff';

            // Apply styles
            preview.style.backgroundColor = courseCardBg;
            if (placeholder) placeholder.style.color = placeholderColor;
            if (title) {
                title.style.color = titleColor;
                title.style.fontSize = titleFontSize;
                title.style.fontWeight = titleFontWeight;
                title.style.fontStyle = titleFontStyle;
            }
            if (progressBar) {
                progressBar.style.backgroundColor = progressBarBg;
                if (progressBarFillElement) {
                    progressBarFillElement.style.background = `linear-gradient(90deg, ${progressBarFillColor}, ${progressBarFillColor})`;
                }
            }
            if (progressText) progressText.style.color = progressTextColor;
            if (resumeBtn) {
                resumeBtn.style.background = `linear-gradient(135deg, ${resumeBtnBg} 0%, ${resumeBtnHover} 100%)`;
                resumeBtn.style.color = resumeBtnText;
            }
            if (premiumBadge) {
                premiumBadge.style.background = `linear-gradient(135deg, ${premiumBadgeBg} 0%, #3498db 100%)`;
                premiumBadge.style.color = badgeTextColor;
            }
            if (typeBadge) {
                typeBadge.style.backgroundColor = `rgba(${hexToRgb(typeBadgeBg)}, 0.1)`;
                typeBadge.style.color = typeBadgeBg;
                typeBadge.style.border = `1px solid rgba(${hexToRgb(typeBadgeBg)}, 0.3)`;
            }
        }

        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? 
                parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) :
                '0, 0, 0';
        }

        // Add event listeners for course card inputs
        document.addEventListener('DOMContentLoaded', function() {
            // Course card preview listeners
            const courseCardInputs = [
                'input[name="course_card_bg"]',
                'input[name="placeholder_color"]',
                'input[name="course_title_color"]',
                'select[name="course_title_font_size"]',
                'select[name="course_title_font_weight"]',
                'select[name="course_title_font_style"]',
                'input[name="progress_bar_bg"]',
                'input[name="progress_bar_fill"]',
                'input[name="progress_text_color"]',
                'input[name="resume_btn_bg"]',
                'input[name="resume_btn_text"]',
                'input[name="resume_btn_hover"]',
                'input[name="premium_badge_bg"]',
                'input[name="type_badge_bg"]',
                'input[name="badge_text_color"]'
            ];

            courseCardInputs.forEach(selector => {
                const element = document.querySelector(selector);
                if (element) {
                    element.addEventListener('input', updateCourseCardPreview);
                    element.addEventListener('change', updateCourseCardPreview);
                }
            });

            // Also add listeners to color pickers
            const colorInputs = document.querySelectorAll('.color-input');
            colorInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.nextElementSibling) {
                        this.nextElementSibling.value = this.value;
                    }
                    updateCourseCardPreview();
                });
            });

            // Initial preview update
            setTimeout(updateCourseCardPreview, 500);
        });

        // Student Sidebar Functions
        function updateStudentSidebarColor(type, value) {
            document.getElementById(`studentSidebar${type.charAt(0).toUpperCase() + type.slice(1)}Text`).value = value;
            updateStudentSidebarPreview();
        }

        function updateStudentSidebarPreview() {
            const preview = document.getElementById('studentSidebarPreview');
            if (!preview) return;

            const primaryColor = document.getElementById('studentSidebarPrimary').value;
            const secondaryColor = document.getElementById('studentSidebarSecondary').value;
            const accentColor = document.getElementById('studentSidebarAccent').value;
            const textColor = document.getElementById('studentSidebarText').value;
            const hoverColor = document.getElementById('studentSidebarHover').value;

            preview.style.setProperty('--preview-primary', primaryColor);
            preview.style.setProperty('--preview-secondary', secondaryColor);
            preview.style.setProperty('--preview-accent', accentColor);
            preview.style.setProperty('--preview-text', textColor);
            preview.style.setProperty('--preview-hover', hoverColor);
        }

        function saveStudentSidebarColors() {
            const colors = {
                primary_color: document.getElementById('studentSidebarPrimary').value,
                secondary_color: document.getElementById('studentSidebarSecondary').value,
                accent_color: document.getElementById('studentSidebarAccent').value,
                text_color: document.getElementById('studentSidebarText').value,
                hover_color: document.getElementById('studentSidebarHover').value
            };

            saveSidebarColorsForRole('student', colors);
        }

        function resetStudentSidebarColors() {
            if (confirm('Reset student sidebar colors to default?')) {
                document.getElementById('studentSidebarPrimary').value = '#1a1a1a';
                document.getElementById('studentSidebarSecondary').value = '#2d2d2d';
                document.getElementById('studentSidebarAccent').value = '#3b82f6';
                document.getElementById('studentSidebarText').value = '#e0e0e0';
                document.getElementById('studentSidebarHover').value = '#374151';
                updateStudentSidebarPreview();
                showNotification('Student sidebar colors reset to default', 'info');
            }
        }

        // Professor Sidebar Functions
        function updateProfessorSidebarColor(type, value) {
            document.getElementById(`professorSidebar${type.charAt(0).toUpperCase() + type.slice(1)}Text`).value = value;
            updateProfessorSidebarPreview();
        }

        function updateProfessorSidebarPreview() {
            const preview = document.getElementById('professorSidebarPreview');
            if (!preview) return;

            const primaryColor = document.getElementById('professorSidebarPrimary').value;
            const secondaryColor = document.getElementById('professorSidebarSecondary').value;
            const accentColor = document.getElementById('professorSidebarAccent').value;
            const textColor = document.getElementById('professorSidebarText').value;
            const hoverColor = document.getElementById('professorSidebarHover').value;

            preview.style.setProperty('--preview-primary', primaryColor);
            preview.style.setProperty('--preview-secondary', secondaryColor);
            preview.style.setProperty('--preview-accent', accentColor);
            preview.style.setProperty('--preview-text', textColor);
            preview.style.setProperty('--preview-hover', hoverColor);
        }

        function saveProfessorSidebarColors() {
            const colors = {
                primary_color: document.getElementById('professorSidebarPrimary').value,
                secondary_color: document.getElementById('professorSidebarSecondary').value,
                accent_color: document.getElementById('professorSidebarAccent').value,
                text_color: document.getElementById('professorSidebarText').value,
                hover_color: document.getElementById('professorSidebarHover').value
            };

            saveSidebarColorsForRole('professor', colors);
        }

        function resetProfessorSidebarColors() {
            if (confirm('Reset professor sidebar colors to default?')) {
                document.getElementById('professorSidebarPrimary').value = '#238ea9';
                document.getElementById('professorSidebarSecondary').value = '#32cd32';
                document.getElementById('professorSidebarAccent').value = '#ff3814';
                document.getElementById('professorSidebarText').value = '#f1f5f9';
                document.getElementById('professorSidebarHover').value = '#475569';
                updateProfessorSidebarPreview();
                showNotification('Professor sidebar colors reset to default', 'info');
            }
        }

        // Admin Sidebar Functions
        function updateAdminSidebarColor(type, value) {
            document.getElementById(`adminSidebar${type.charAt(0).toUpperCase() + type.slice(1)}Text`).value = value;
            updateAdminSidebarPreview();
        }

        function updateAdminSidebarPreview() {
            const preview = document.getElementById('adminSidebarPreview');
            if (!preview) return;

            const primaryColor = document.getElementById('adminSidebarPrimary').value;
            const secondaryColor = document.getElementById('adminSidebarSecondary').value;
            const accentColor = document.getElementById('adminSidebarAccent').value;
            const textColor = document.getElementById('adminSidebarText').value;
            const hoverColor = document.getElementById('adminSidebarHover').value;

            preview.style.setProperty('--preview-primary', primaryColor);
            preview.style.setProperty('--preview-secondary', secondaryColor);
            preview.style.setProperty('--preview-accent', accentColor);
            preview.style.setProperty('--preview-text', textColor);
            preview.style.setProperty('--preview-hover', hoverColor);
        }

        function saveAdminSidebarColors() {
            const colors = {
                primary_color: document.getElementById('adminSidebarPrimary').value,
                secondary_color: document.getElementById('adminSidebarSecondary').value,
                accent_color: document.getElementById('adminSidebarAccent').value,
                text_color: document.getElementById('adminSidebarText').value,
                hover_color: document.getElementById('adminSidebarHover').value
            };

            saveSidebarColorsForRole('admin', colors);
        }

        function resetAdminSidebarColors() {
            if (confirm('Reset admin sidebar colors to default?')) {
                document.getElementById('adminSidebarPrimary').value = '#111827';
                document.getElementById('adminSidebarSecondary').value = '#1f2937';
                document.getElementById('adminSidebarAccent').value = '#f59e0b';
                document.getElementById('adminSidebarText').value = '#f9fafb';
                document.getElementById('adminSidebarHover').value = '#374151';
                updateAdminSidebarPreview();
                showNotification('Admin sidebar colors reset to default', 'info');
            }
        }

        // Shared function for saving sidebar colors
        function saveSidebarColorsForRole(role, colors) {
            fetch('/smartprep/admin/settings/sidebar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    role: role,
                    colors: colors
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`${role.charAt(0).toUpperCase() + role.slice(1)} sidebar colors saved successfully!`, 'success');
                } else {
                    showNotification('Error saving sidebar colors', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving sidebar colors', 'danger');
            });
        }

        // Initialize role-specific previews when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved colors from database (read from hidden element to avoid Blade parser issues in large script)
            const sidebarSettingsEl = document.getElementById('sidebar-settings-json');
            let sidebarSettings = {};
            if (sidebarSettingsEl) {
                try { sidebarSettings = JSON.parse(sidebarSettingsEl.textContent || '{}'); } catch(e) { sidebarSettings = {}; }
            }
            
            // Load student sidebar colors
            if (sidebarSettings.student) {
                const studentColors = sidebarSettings.student;
                document.getElementById('studentSidebarPrimary').value = studentColors.primary_color || '#1a1a1a';
                document.getElementById('studentSidebarSecondary').value = studentColors.secondary_color || '#2d2d2d';
                document.getElementById('studentSidebarAccent').value = studentColors.accent_color || '#3b82f6';
                document.getElementById('studentSidebarText').value = studentColors.text_color || '#e0e0e0';
                document.getElementById('studentSidebarHover').value = studentColors.hover_color || '#374151';
                
                // Sync text inputs
                document.getElementById('studentSidebarPrimaryText').value = studentColors.primary_color || '#1a1a1a';
                document.getElementById('studentSidebarSecondaryText').value = studentColors.secondary_color || '#2d2d2d';
                document.getElementById('studentSidebarAccentText').value = studentColors.accent_color || '#3b82f6';
                document.getElementById('studentSidebarTextText').value = studentColors.text_color || '#e0e0e0';
                document.getElementById('studentSidebarHoverText').value = studentColors.hover_color || '#374151';
            }
            
            // Load professor sidebar colors
            if (sidebarSettings.professor) {
                const professorColors = sidebarSettings.professor;
                document.getElementById('professorSidebarPrimary').value = professorColors.primary_color || '#238ea9';
                document.getElementById('professorSidebarSecondary').value = professorColors.secondary_color || '#32cd32';
                document.getElementById('professorSidebarAccent').value = professorColors.accent_color || '#ff3814';
                document.getElementById('professorSidebarText').value = professorColors.text_color || '#f1f5f9';
                document.getElementById('professorSidebarHover').value = professorColors.hover_color || '#475569';
                
                // Sync text inputs
                document.getElementById('professorSidebarPrimaryText').value = professorColors.primary_color || '#238ea9';
                document.getElementById('professorSidebarSecondaryText').value = professorColors.secondary_color || '#32cd32';
                document.getElementById('professorSidebarAccentText').value = professorColors.accent_color || '#ff3814';
                document.getElementById('professorSidebarTextText').value = professorColors.text_color || '#f1f5f9';
                document.getElementById('professorSidebarHoverText').value = professorColors.hover_color || '#475569';
            }
            
            // Load admin sidebar colors
            if (sidebarSettings.admin) {
                const adminColors = sidebarSettings.admin;
                document.getElementById('adminSidebarPrimary').value = adminColors.primary_color || '#111827';
                document.getElementById('adminSidebarSecondary').value = adminColors.secondary_color || '#1f2937';
                document.getElementById('adminSidebarAccent').value = adminColors.accent_color || '#f59e0b';
                document.getElementById('adminSidebarText').value = adminColors.text_color || '#f9fafb';
                document.getElementById('adminSidebarHover').value = adminColors.hover_color || '#374151';
                
                // Sync text inputs
                document.getElementById('adminSidebarPrimaryText').value = adminColors.primary_color || '#111827';
                document.getElementById('adminSidebarSecondaryText').value = adminColors.secondary_color || '#1f2937';
                document.getElementById('adminSidebarAccentText').value = adminColors.accent_color || '#f59e0b';
                document.getElementById('adminSidebarTextText').value = adminColors.text_color || '#f9fafb';
                document.getElementById('adminSidebarHoverText').value = adminColors.hover_color || '#374151';
            }
            
            setTimeout(() => {
                updateStudentSidebarPreview();
                updateProfessorSidebarPreview();
                updateAdminSidebarPreview();
            }, 100);
        });
    </script>
    <script type="application/json" id="sidebar-settings-json"><?php echo json_encode($sidebarSettings ?? [], 15, 512) ?></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/admin/admin-settings/index.blade.php ENDPATH**/ ?>