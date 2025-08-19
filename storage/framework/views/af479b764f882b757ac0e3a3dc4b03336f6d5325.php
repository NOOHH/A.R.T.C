<?php
    // Ensure auth context variables exist before head is rendered
    $user = Auth::guard('smartprep_admin')->user() ?: Auth::guard('smartprep')->user() ?: Auth::user();
    $isLoggedIn = Auth::guard('smartprep_admin')->check() || Auth::guard('smartprep')->check() || Auth::check();
    $userRole = 'guest';
    if ($isLoggedIn && $user) {
        if (Auth::guard('smartprep_admin')->check()) {
            $userRole = 'admin';
        } elseif (Auth::guard('smartprep')->check()) {
            $userRole = $user->role ?? 'user';
        } else {
            $userRole = $user->role ?? 'user';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Customize Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- App context for JS (avoid Blade echoes inside scripts for linting) -->
    <meta name="x-my-id" content="<?php echo e($isLoggedIn && isset($user) ? $user->id : ''); ?>">
    <meta name="x-my-name" content="<?php echo e($isLoggedIn && isset($user) ? ($user->name ?? 'User') : 'Guest'); ?>">
    <meta name="x-is-authenticated" content="<?php echo e($isLoggedIn && isset($user) ? '1' : '0'); ?>">
    <meta name="x-user-role" content="<?php echo e($userRole ?? 'guest'); ?>">
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
    <?php
        // Get user info for global variables - check all relevant guards
        $user = Auth::guard('smartprep_admin')->user() ?: Auth::guard('smartprep')->user() ?: Auth::user();
        
        // Check if user is actually logged in
        $isLoggedIn = Auth::guard('smartprep_admin')->check() || Auth::guard('smartprep')->check() || Auth::check();
        
        // Determine user role
        $userRole = 'guest';
        if ($isLoggedIn && $user) {
            if (Auth::guard('smartprep_admin')->check()) {
                $userRole = 'admin';
            } elseif (Auth::guard('smartprep')->check()) {
                $userRole = $user->role ?? 'user';
            } else {
                $userRole = $user->role ?? 'user';
            }
        }
    ?>

    <!-- Global Variables for JavaScript -->
    <script>
        // Global variables accessible throughout the page (read from meta tags)
        (function() {
            function meta(name) {
                var el = document.querySelector('meta[name="' + name + '"]');
                return el ? el.getAttribute('content') : null;
            }
            window.myId = meta('x-my-id') || null;
            window.myName = meta('x-my-name') || 'Guest';
            window.isAuthenticated = meta('x-is-authenticated') === '1';
            window.userRole = meta('x-user-role') || 'guest';
            window.csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).getAttribute ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
            console.log('Navbar Global variables initialized:', {
                myId: window.myId,
                myName: window.myName,
                isAuthenticated: window.isAuthenticated,
                userRole: window.userRole
            });
        })();
    </script>

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
                        <a class="nav-link" href="<?php echo e(Auth::guard('smartprep_admin')->check() ? route('smartprep.admin.dashboard') : route('smartprep.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('smartprep.dashboard.customize')); ?>">
                            <i class="fas fa-palette me-2"></i>Customize Website
                        </a>
                    </li>
                    <?php if(isset($activeWebsites) && $activeWebsites->count() > 0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-globe me-2"></i>My Websites
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?php echo e(Auth::guard('smartprep_admin')->user()->name ?? Auth::guard('smartprep')->user()->name ?? 'User'); ?>

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>Home</a></li>
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
                        <button class="btn btn-success" onclick="submitWebsiteRequest()">
                            <?php if($userRole === 'admin'): ?>
                                <i class="fas fa-save me-2"></i>Save Settings
                            <?php else: ?>
                                <i class="fas fa-paper-plane me-2"></i>Submit Website Request
                            <?php endif; ?>
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
                    <div class="section-header mb-3">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>General Settings</h5>
                    </div>
                    <!-- Website Management (separate from form) -->
                    <div class="mb-4" id="website_selector_container">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Websites</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCreateWebsite()" title="Create Draft"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="list-group small mb-2 border" id="websiteList" style="max-height:140px;overflow:auto;">
                            <?php $__currentLoopData = ($activeWebsites ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $website): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $isActive = isset($selectedWebsite) && $selectedWebsite && $selectedWebsite->id === $website->id; ?>
                                <button type="button" class="list-group-item list-group-item-action py-2 website-item d-flex justify-content-between align-items-center <?php echo e($isActive ? 'active' : ''); ?>" data-id="<?php echo e($website->id); ?>">
                                    <span class="text-truncate" style="max-width:130px"><?php echo e($website->name); ?></span>
                                    <?php if($website->status === 'draft'): ?><span class="badge bg-warning text-dark">Draft</span><?php endif; ?>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(($activeWebsites ?? collect())->isEmpty()): ?>
                                <div class="text-muted small px-2 py-1">No drafts yet.</div>
                            <?php endif; ?>
                        </div>
                        <?php if(isset($selectedWebsite)): ?>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 small">
                                <span><strong>Editing:</strong> <?php echo e($selectedWebsite->name); ?></span>
                                <code>/t/<?php echo e($selectedWebsite->slug); ?></code>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="saveDraftSettings()" style="font-size:11px;padding:3px 8px"><i class="fas fa-save"></i></button>
                                <form method="POST" action="<?php echo e(route('smartprep.dashboard.websites.destroy', $selectedWebsite->id)); ?>" onsubmit="return confirm('Delete this website? This cannot be undone.');" class="m-0 p-0">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:11px;padding:3px 8px"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    <form id="generalForm" onsubmit="updateGeneral(event)">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Site Title</label>
                            <input type="text" class="form-control" name="site_title" value="<?php echo e($settings['general']['site_name'] ?? 'Ascendo Review and Training Center'); ?>" placeholder="Enter site title">
                            <small class="form-text text-muted">Appears in browser tab and search results</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" name="tagline" value="<?php echo e($settings['general']['site_tagline'] ?? 'Review Smarter. Learn Better. Succeed Faster.'); ?>" placeholder="Enter tagline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="<?php echo e($settings['general']['contact_email'] ?? 'admin@artc.com'); ?>" placeholder="Contact email">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo e($settings['general']['contact_phone'] ?? '+1 (555) 123-4567'); ?>" placeholder="Phone number">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" placeholder="Physical address"><?php echo e($settings['general']['contact_address'] ?? '123 Education Street, Learning City, LC 12345'); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-1 w-100">
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
                        <?php echo csrf_field(); ?>
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
                    
                    <form id="navbarForm" onsubmit="updateNavbar(event)" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" value="<?php echo e($settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center'); ?>" placeholder="Brand name">
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
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Title</label>
                            <input type="text" class="form-control" name="hero_title" value="<?php echo e($settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.'); ?>" placeholder="Main headline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description"><?php echo e($settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.'); ?></textarea>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Background Image</label>
                            <input type="file" class="form-control" name="hero_background" accept="image/*">
                            <small class="form-text text-muted">Recommended: 1920x1080px</small>
                        </div>
                        
                        <!-- Homepage Color Customization -->
                        <h6 class="mt-4 mb-3"><i class="fas fa-palette me-2"></i>Homepage Colors</h6>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#667eea" onchange="updatePreviewColor('homepage_primary', this.value)">
                                <input type="text" class="form-control color-text" name="homepage_primary_color" value="#667eea" placeholder="#667eea">
                            </div>
                            <small class="form-text text-muted">Main brand color for buttons and accents</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#764ba2" onchange="updatePreviewColor('homepage_secondary', this.value)">
                                <input type="text" class="form-control color-text" name="homepage_secondary_color" value="#764ba2" placeholder="#764ba2">
                            </div>
                            <small class="form-text text-muted">Secondary color for highlights and gradients</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#ffffff" onchange="updatePreviewColor('homepage_background', this.value)">
                                <input type="text" class="form-control color-text" name="homepage_background_color" value="#ffffff" placeholder="#ffffff">
                            </div>
                            <small class="form-text text-muted">Main background color</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Text Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#333333" onchange="updatePreviewColor('homepage_text', this.value)">
                                <input type="text" class="form-control color-text" name="homepage_text_color" value="#333333" placeholder="#333333">
                            </div>
                            <small class="form-text text-muted">Main text color</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Overlay Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="#000000" onchange="updatePreviewColor('homepage_overlay', this.value)">
                                <input type="text" class="form-control color-text" name="homepage_overlay_color" value="#000000" placeholder="#000000">
                            </div>
                            <small class="form-text text-muted">Dark overlay on hero background image</small>
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
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['sidebar_bg'] ?? '#f8f9fa'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="sidebar_bg" value="<?php echo e($settings['professor_panel']['sidebar_bg'] ?? '#f8f9fa'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Sidebar Text Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['sidebar_text'] ?? '#212529'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="sidebar_text" value="<?php echo e($settings['professor_panel']['sidebar_text'] ?? '#212529'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Active Menu Item</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['active_menu_color'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="active_menu_color" value="<?php echo e($settings['professor_panel']['active_menu_color'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Menu Hover Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['menu_hover_color'] ?? '#e9ecef'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="menu_hover_color" value="<?php echo e($settings['professor_panel']['menu_hover_color'] ?? '#e9ecef'); ?>">
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
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['header_bg'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="header_bg" value="<?php echo e($settings['professor_panel']['header_bg'] ?? '#0d6efd'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Header Text</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['header_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="header_text" value="<?php echo e($settings['professor_panel']['header_text'] ?? '#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Button</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['primary_btn'] ?? '#28a745'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="primary_btn" value="<?php echo e($settings['professor_panel']['primary_btn'] ?? '#28a745'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Button</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" value="<?php echo e($settings['professor_panel']['secondary_btn'] ?? '#6c757d'); ?>" onchange="this.nextElementSibling.value = this.value">
                                                <input type="text" class="form-control" name="secondary_btn" value="<?php echo e($settings['professor_panel']['secondary_btn'] ?? '#6c757d'); ?>">
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
                                                <input type="color" class="color-input" id="professorSidebarPrimary" value="#1e293b" onchange="updateProfessorSidebarColor('primary', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarPrimaryText" value="#1e293b">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarSecondary" value="#334155" onchange="updateProfessorSidebarColor('secondary', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarSecondaryText" value="#334155">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Accent Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="color-input" id="professorSidebarAccent" value="#10b981" onchange="updateProfessorSidebarColor('accent', this.value)">
                                                <input type="text" class="form-control" id="professorSidebarAccentText" value="#10b981">
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
                            <a href="<?php echo e($previewUrl ?? url('/artc')); ?>" class="preview-btn" target="_blank" id="openInNewTabLink">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="preview-iframe-container">
                    <div class="preview-loading" id="previewLoading">
                        <div class="loading-spinner"></div>
                        <span class="text-muted">Loading preview...</span>
                    </div>
                    <iframe 
                        class="preview-iframe" 
                        src="<?php echo e($previewUrl ?? url('/artc')); ?>" 
                        title="Website Preview"
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
                    
                    // Update preview URL based on section
                    updatePreviewForSection(section);
                });
            });
            
            // Initialize color picker synchronization
            initializeColorPickers();
            
            // Enable auto-save for important changes
            enableAutoSave();

            // Toggle website selector visibility based on mode
            const modeSelect = document.getElementById('website_mode');
            const selectorContainer = document.getElementById('website_selector_container');
            if (modeSelect && selectorContainer) {
                const toggleSelector = () => {
                    const mode = modeSelect.value;
                    selectorContainer.style.display = (mode === 'customize_current') ? 'block' : 'none';
                };
                modeSelect.addEventListener('change', toggleSelector);
                toggleSelector();
            }
        });

        function changeWebsite(id){
            const url = new URL(window.location.href);
            if(id){
                url.searchParams.set('website', id);
            } else {
                url.searchParams.delete('website');
            }
            window.location = url.toString();
        }

        function openCreateWebsite(){
            const name = prompt('Enter new website name');
            if(!name) return;
            const form = document.createElement('form');
            form.method='POST';
            form.action="<?php echo e(route('smartprep.dashboard.websites.store')); ?>";
            form.innerHTML = `<?php echo csrf_field(); ?><input type="hidden" name="name" value="${name.replace(/"/g,'&quot;')}">`;
            document.body.appendChild(form);form.submit();
        }

        function saveDraftSettings(){
            const websiteId = new URL(window.location.href).searchParams.get('website');
            if(!websiteId){ alert('No website selected'); return; }
            // Collect a minimal settings snapshot (general section inputs)
            const payload = { settings: { general: {} } };
            document.querySelectorAll('#generalForm input[name], #generalForm textarea[name]').forEach(el=>{
                if(el.name){ payload.settings.general[el.name]=el.value; }
            });
            fetch("<?php echo e(url('/smartprep/dashboard/websites')); ?>/"+websiteId, {
                method:'PATCH',
                headers:{'X-CSRF-TOKEN':window.csrfToken,'Accept':'application/json','Content-Type':'application/json'},
                body: JSON.stringify(payload)
            }).then(r=>r.json().catch(()=>({success:false}))).then(res=>{
                // Silent success – optionally toast
                console.log('Draft saved', res);
            }).catch(e=>console.error(e));
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.website-item').forEach(btn => {
                btn.addEventListener('click', () => changeWebsite(btn.getAttribute('data-id')));
            });
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
            const websiteMode = document.getElementById('website_mode')?.value || 'customize_current';
            const selectedWebsite = document.getElementById('selected_website')?.value || 'current';
            
            // Update button state
            submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText}`;
            submitBtn.disabled = true;
            
            try {
                // Determine endpoint based on mode and section
                let endpoint = '/smartprep/admin/settings';
                if (settingType === 'navbar') endpoint = '/smartprep/admin/settings/navbar';
                if (settingType === 'branding') endpoint = '/smartprep/admin/settings/branding';
                if (settingType === 'homepage') endpoint = '/smartprep/admin/settings/homepage';

                // Include mode to inform backend intent
                formData.append('website_mode', websiteMode);
                formData.append('_token', '<?php echo e(csrf_token()); ?>');
                formData.append('selected_website', selectedWebsite);

                await fetch(endpoint, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                
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
                        case 'homepage_primary':
                            root.style.setProperty('--homepage-primary-color', color);
                            // Also update common CSS variables
                            root.style.setProperty('--primary-color', color);
                            root.style.setProperty('--bs-primary', color);
                            break;
                        case 'homepage_secondary':
                            root.style.setProperty('--homepage-secondary-color', color);
                            root.style.setProperty('--secondary-color', color);
                            root.style.setProperty('--bs-secondary', color);
                            break;
                        case 'homepage_background':
                            root.style.setProperty('--homepage-background-color', color);
                            root.style.setProperty('--bs-body-bg', color);
                            // Update body background directly
                            const body = iframeDoc.body;
                            if (body) body.style.backgroundColor = color;
                            break;
                        case 'homepage_text':
                            root.style.setProperty('--homepage-text-color', color);
                            root.style.setProperty('--bs-body-color', color);
                            // Update body text color directly
                            const bodyText = iframeDoc.body;
                            if (bodyText) bodyText.style.color = color;
                            break;
                        case 'homepage_overlay':
                            root.style.setProperty('--homepage-overlay-color', color);
                            // Update hero overlay if present
                            const heroOverlays = iframeDoc.querySelectorAll('.hero-overlay, .overlay');
                            heroOverlays.forEach(overlay => {
                                overlay.style.backgroundColor = color;
                            });
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
                    const previewUrl = settings.data.general?.preview_url || 'http://127.0.0.1:8000/';
                    
                    // Update iframe src
                    const iframe = document.getElementById('previewFrame');
                    if (iframe) {
                        iframe.src = previewUrl;
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
                            autoSaveField(this.name, this.value);
                        }
                    }, 3000);
                });
            });
        }

        // Specific preview element updates for faster feedback
        function updatePreviewElement(fieldName, fieldValue) {
            const iframe = document.getElementById('previewFrame');
            
            try {
                if (iframe && iframe.contentDocument) {
                    const iframeDoc = iframe.contentDocument;
                    
                    // Handle navbar brand name updates
                    if (fieldName === 'brand_name') {
                        // Update all elements with navbar brand name across different pages
                        const brandElements = iframeDoc.querySelectorAll(
                            '.navbar-brand strong, ' +
                            '.footer-title, ' +
                            '.navbar-brand, ' +
                            '.brand-text, ' +
                            'a.navbar-brand, ' +
                            '.brand-text.fw-bold, ' +
                            'span.brand-text.fw-bold'
                        );
                        
                        brandElements.forEach(element => {
                            // For navbar-brand that contains both icon and text
                            if (element.classList.contains('navbar-brand') && element.querySelector('i')) {
                                // Update only the text part, preserving the icon
                                const iconElement = element.querySelector('i');
                                element.innerHTML = iconElement.outerHTML + fieldValue;
                            } else {
                                // Direct text update for other elements
                                element.textContent = fieldValue;
                            }
                        });
                        
                        console.log(`Updated navbar brand name to: ${fieldValue}`);
                        return; // Don't refresh iframe for brand name changes
                    }
                    
                    // Handle hero title updates
                    if (fieldName === 'hero_title') {
                        const heroElements = iframeDoc.querySelectorAll('.hero-title, h1.display-4');
                        heroElements.forEach(element => {
                            element.textContent = fieldValue;
                        });
                        
                        console.log(`Updated hero title to: ${fieldValue}`);
                        return; // Don't refresh iframe for hero title changes
                    }
                    
                    // Handle homepage color updates
                    if (fieldName.includes('homepage_') && fieldName.includes('_color')) {
                        const colorType = fieldName.replace('homepage_', '').replace('_color', '');
                        updatePreviewColor('homepage_' + colorType, fieldValue);
                        console.log(`Updated homepage ${colorType} color to: ${fieldValue}`);
                        return; // Don't refresh iframe for color changes
                    }
                }
            } catch (e) {
                console.log('Cross-origin iframe access restricted - falling back to iframe refresh');
            }
            
            // Fallback to full iframe refresh for other changes or if direct update fails
            refreshPreview();
        }

        // Auto-save individual field
        function autoSaveField(fieldName, fieldValue) {
            // Determine which endpoint to use based on field name
            let endpoint = '/smartprep/admin/settings';
            if (fieldName.includes('brand_name') || fieldName.includes('navbar')) {
                endpoint = '/smartprep/admin/settings/navbar';
            } else if (fieldName.includes('hero') || fieldName.includes('homepage')) {
                endpoint = '/smartprep/admin/settings/homepage';
            }

            const formData = new FormData();
            formData.append('_token', '<?php echo e(csrf_token()); ?>');
            formData.append(fieldName, fieldValue);

            fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Auto-saved successfully:', fieldName);
                    // Update specific elements in preview or refresh iframe
                    updatePreviewElement(fieldName, fieldValue);
                } else {
                    console.error('Auto-save failed:', data.message);
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
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
        
        // Add missing loadProgramsModal function to prevent errors
        function loadProgramsModal() {
            // Use the SmartPrep API endpoint with full URL to avoid resolution issues
            const apiUrl = '<?php echo e(url("smartprep/api/programs")); ?>';
            
            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Programs loaded successfully:', data);
                    // Handle programs data if needed
                })
                .catch(error => {
                    console.error('Error loading programs:', error);
                    // Don't show error to user in this context since it's not critical
                });
        }
        
        // Call loadProgramsModal on page load if needed
        // Uncomment if you want to load programs on page initialization
        // loadProgramsModal();

        // ====== ROLE-SPECIFIC SIDEBAR FUNCTIONS ======

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
                document.getElementById('professorSidebarPrimary').value = '#1e293b';
                document.getElementById('professorSidebarSecondary').value = '#334155';
                document.getElementById('professorSidebarAccent').value = '#10b981';
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
            // Load saved colors from database
            const sidebarSettings = <?php echo json_encode($sidebarSettings ?? [], 15, 512) ?>;
            
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
                document.getElementById('professorSidebarPrimary').value = professorColors.primary_color || '#1e293b';
                document.getElementById('professorSidebarSecondary').value = professorColors.secondary_color || '#334155';
                document.getElementById('professorSidebarAccent').value = professorColors.accent_color || '#10b981';
                document.getElementById('professorSidebarText').value = professorColors.text_color || '#f1f5f9';
                document.getElementById('professorSidebarHover').value = professorColors.hover_color || '#475569';
                
                // Sync text inputs
                document.getElementById('professorSidebarPrimaryText').value = professorColors.primary_color || '#1e293b';
                document.getElementById('professorSidebarSecondaryText').value = professorColors.secondary_color || '#334155';
                document.getElementById('professorSidebarAccentText').value = professorColors.accent_color || '#10b981';
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

    <!-- Website Request Modal -->
    <div class="modal fade" id="websiteRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?php echo e($userRole === 'admin' ? route('smartprep.admin.settings.save') : route('smartprep.dashboard.submit-customized-website')); ?>" id="customizedWebsiteForm">
                    <?php echo csrf_field(); ?>
                    <!-- Hidden fields to store customization data -->
                    <input type="hidden" name="customization_data" id="customizationData">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?php if($userRole === 'admin'): ?>
                                <i class="fas fa-cog me-2"></i>Save Settings
                            <?php else: ?>
                                <i class="fas fa-rocket me-2"></i>Submit Website Request
                            <?php endif; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <?php if($userRole === 'admin'): ?>
                                    <strong>Save template settings!</strong> Your customizations will be saved to the database and applied to the website template.
                                <?php else: ?>
                                    <strong>Ready to create your website!</strong> Your customizations have been saved and will be applied to your new website once approved.
                                <?php endif; ?>
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
                                <input type="email" class="form-control form-control-modern" name="contact_email" required value="<?php echo e(Auth::guard('smartprep_admin')->user()->email ?? Auth::guard('smartprep')->user()->email ?? ''); ?>">
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
                            <?php if($userRole === 'admin'): ?>
                                <i class="fas fa-save me-2"></i>Save Settings
                            <?php else: ?>
                                <i class="fas fa-paper-plane me-2"></i>Submit Website Request
                            <?php endif; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/customize-website.blade.php ENDPATH**/ ?>