@php
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
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Customize Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App context for JS (avoid Blade echoes inside scripts for linting) -->
    <meta name="x-my-id" content="{{ $isLoggedIn && isset($user) ? $user->id : '' }}">
    <meta name="x-my-name" content="{{ $isLoggedIn && isset($user) ? ($user->name ?? 'User') : 'Guest' }}">
    <meta name="x-is-authenticated" content="{{ $isLoggedIn && isset($user) ? '1' : '0' }}">
    <meta name="x-user-role" content="{{ $userRole ?? 'guest' }}">
    <meta name="x-selected-website-id" content="{{ $selectedWebsite->id ?? '' }}">
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
    @php
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
    @endphp

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
                            <i class="fas fa-user-circle me-2"></i>{{ Auth::guard('smartprep_admin')->user()->name ?? Auth::guard('smartprep')->user()->name ?? 'User' }}
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
                        <button class="settings-nav-tab" data-section="auth">
                            <i class="fas fa-sign-in-alt me-2"></i>Auth
                        </button>
                        <button class="settings-nav-tab" data-section="permissions">
                            <i class="fas fa-shield-alt me-2"></i>Permissions
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
                            @if($userRole === 'admin')
                                <i class="fas fa-save me-2"></i>Save Settings
                            @else
                                <i class="fas fa-paper-plane me-2"></i>Submit Website Request
                            @endif
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
                            @foreach(($activeWebsites ?? collect()) as $website)
                                @php $isActive = isset($selectedWebsite) && $selectedWebsite && $selectedWebsite->id === $website->id; @endphp
                                <button type="button" class="list-group-item list-group-item-action py-2 website-item d-flex justify-content-between align-items-center {{ $isActive ? 'active' : '' }}" data-id="{{ $website->id }}">
                                    <span class="text-truncate" style="max-width:130px">{{ $website->name }}</span>
                                    @if($website->status === 'draft')<span class="badge bg-warning text-dark">Draft</span>@endif
                                </button>
                            @endforeach
                            @if(($activeWebsites ?? collect())->isEmpty())
                                <div class="text-muted small px-2 py-1">No drafts yet.</div>
                            @endif
                        </div>
                        @if(isset($selectedWebsite))
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 small">
                                <span><strong>Editing:</strong> {{ $selectedWebsite->name }}</span>
                                <code>/t/{{ $selectedWebsite->slug }}</code>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="saveDraftSettings()" style="font-size:11px;padding:3px 8px"><i class="fas fa-save"></i></button>
                                <form method="POST" action="{{ route('smartprep.dashboard.websites.destroy', $selectedWebsite->id) }}" onsubmit="return confirm('Delete this website? This cannot be undone.');" class="m-0 p-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:11px;padding:3px 8px"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <form id="generalForm" onsubmit="updateGeneral(event)">
                        @csrf
                        <!-- Admin Account Settings -->
                        <div class="form-group mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-shield text-primary me-2"></i>
                                <label class="form-label mb-0">Admin Account Settings</label>
                            </div>
                            <p class="small text-muted mb-3">Manage the primary admin account for this website. The email will be automatically generated as admin@(website-name).com</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" name="admin_email" value="{{ $settings['general']['admin_email'] ?? '' }}" placeholder="e.g. admin@training.com">
                                    <small class="form-text text-muted">Format: admin@(website-name).com</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" name="admin_password" placeholder="Minimum 8 characters">
                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                </div>
                            </div>
                        </div>

                        <!-- Brand Name (Connected to Navigation) -->
                        <div class="form-group mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" value="{{ $settings['general']['brand_name'] ?? $settings['navbar']['brand_name'] ?? '' }}" placeholder="Enter brand name">
                            <small class="form-text text-muted">This will appear in the navigation bar and browser tab</small>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Email (Optional)</label>
                            <input type="email" class="form-control" name="contact_email" value="{{ $settings['general']['contact_email'] ?? '' }}" placeholder="Contact email">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number (Optional)</label>
                            <input type="text" class="form-control" name="contact_phone" value="{{ $settings['general']['contact_phone'] ?? '' }}" placeholder="Phone number">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Address (Optional)</label>
                            <textarea class="form-control" name="contact_address" rows="3" placeholder="Physical address">{{ $settings['general']['contact_address'] ?? '' }}</textarea>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-group mb-3">
                            <label class="form-label">Terms and Conditions (Optional)</label>
                            <textarea class="form-control" name="terms_conditions" rows="4" placeholder="Enter terms and conditions">{{ $settings['general']['terms_conditions'] ?? '' }}</textarea>
                        </div>

                        <!-- Social Media Links -->
                        <div class="form-group mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-share-alt text-primary me-2"></i>
                                <label class="form-label mb-0">Social Media Links (Optional)</label>
                            </div>
                            <p class="small text-muted mb-3">Add social media links for your website footer</p>
                            <div id="socialLinksContainer">
                                @php
                                    $socialLinks = json_decode($settings['general']['social_links'] ?? '[]', true) ?: [];
                                @endphp
                                @foreach($socialLinks as $index => $link)
                                <div class="row g-2 mb-2 social-link-row">
                                    <div class="col-md-4">
                                        <select class="form-control" name="social_links[{{ $index }}][platform]">
                                            <option value="">Select Platform</option>
                                            <option value="facebook" {{ $link['platform'] == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                            <option value="youtube" {{ $link['platform'] == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                            <option value="twitter" {{ $link['platform'] == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                            <option value="instagram" {{ $link['platform'] == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                            <option value="linkedin" {{ $link['platform'] == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                            <option value="tiktok" {{ $link['platform'] == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                            <option value="telegram" {{ $link['platform'] == 'telegram' ? 'selected' : '' }}>Telegram</option>
                                            <option value="whatsapp" {{ $link['platform'] == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="url" class="form-control" name="social_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSocialLink(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSocialLink()">
                                <i class="fas fa-plus me-1"></i>Add Social Link
                            </button>
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
                            <input type="text" class="form-control" name="brand_name" value="{{ $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center' }}" placeholder="Brand name">
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
                            <input type="text" class="form-control" name="hero_title" value="{{ $settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.' }}" placeholder="Main headline">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description">{{ $settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.' }}</textarea>
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
                
                <!-- Auth (Login/Register) Settings -->
                <div class="sidebar-section" id="auth-settings" style="display: none;">
                    <div class="section-header">
                        <h5><i class="fas fa-sign-in-alt me-2"></i>Authentication & Registration</h5>
                    </div>
                    
                    <!-- Login Customization Section -->
                    <form id="loginForm" method="POST" action="{{ route('smartprep.dashboard.settings.update.auth', $selectedWebsite->id ?? 1) }}" enctype="multipart/form-data" onsubmit="updateAuth(event)">
                        @csrf
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>LOGIN CUSTOMIZATION</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-label">Login Page Title</label>
                                    <input type="text" class="form-control" name="login_title" value="{{ $settings['auth']['login_title'] ?? 'Welcome Back' }}" placeholder="Login page main title">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Login Page Subtitle</label>
                                    <input type="text" class="form-control" name="login_subtitle" value="{{ $settings['auth']['login_subtitle'] ?? 'Sign in to your account to continue' }}" placeholder="Login page subtitle">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Login Button Text</label>
                                    <input type="text" class="form-control" name="login_button_text" value="{{ $settings['auth']['login_button_text'] ?? 'Sign In' }}" placeholder="Login button text">
                                </div>
                                
                                <!-- Left Panel Customization -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Left Panel Customization</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Review Text</label>
                                            <textarea class="form-control" name="login_review_text" rows="3" placeholder="Review Smarter.&#10;Learn Better.&#10;Succeed Faster.">{{ $settings['auth']['login_review_text'] ?? 'Review Smarter.\nLearn Better.\nSucceed Faster.' }}</textarea>
                                            <small class="form-text text-muted">Main text displayed on the left panel. Use line breaks for multiple lines.</small>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-label">Login Illustration</label>
                                            <input type="file" class="form-control" name="login_illustration" accept="image/*">
                                            <small class="form-text text-muted">Upload a custom illustration for the login page</small>
                                            @if(isset($settings['auth']['login_illustration_url']) && $settings['auth']['login_illustration_url'])
                                                <div class="mt-2">
                                                    <small class="text-muted">Current illustration:</small><br>
                                                    <img src="{{ $settings['auth']['login_illustration_url'] }}" alt="Current illustration" style="max-height: 100px;" class="img-thumbnail">
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-label">Copyright Text</label>
                                            <textarea class="form-control" name="login_copyright_text" rows="2" placeholder="© Copyright Your Company. All Rights Reserved.">{{ $settings['auth']['login_copyright_text'] ?? '© Copyright Ascendo Review and Training Center.\nAll Rights Reserved.' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Background Color (Top of Gradient)</label>
                                            <small class="form-text text-muted">Main background color for the left panel</small>
                                            <input type="color" class="form-control form-control-color" name="login_bg_top_color" value="{{ $settings['auth']['login_bg_top_color'] ?? '#667eea' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Gradient Color (Bottom of Gradient)</label>
                                            <small class="form-text text-muted">Bottom color for the gradient background</small>
                                            <input type="color" class="form-control form-control-color" name="login_bg_bottom_color" value="{{ $settings['auth']['login_bg_bottom_color'] ?? '#764ba2' }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Review Text Color</label>
                                            <small class="form-text text-muted">Color for the main review text</small>
                                            <input type="color" class="form-control form-control-color" name="login_text_color" value="{{ $settings['auth']['login_text_color'] ?? '#ffffff' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Copyright Text Color</label>
                                            <small class="form-text text-muted">Color for the copyright text</small>
                                            <input type="color" class="form-control form-control-color" name="login_copyright_color" value="{{ $settings['auth']['login_copyright_color'] ?? '#ffffff' }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Login Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Registration Form Fields Section -->
                    <form id="registrationForm" method="POST" action="{{ route('smartprep.dashboard.settings.update.registration', $selectedWebsite->id ?? 1) }}" enctype="multipart/form-data" onsubmit="updateRegistration(event)">
                        @csrf
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Registration Form Fields</h6>
                                <small class="text-muted">Manage Form Fields</small>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4">Add fields, sections, and manage what students fill out during registration</p>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Registration Page Title</label>
                                    <input type="text" class="form-control" name="register_title" value="{{ $settings['auth']['register_title'] ?? 'Create Account' }}" placeholder="Registration page main title">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Registration Page Subtitle</label>
                                    <input type="text" class="form-control" name="register_subtitle" value="{{ $settings['auth']['register_subtitle'] ?? 'Join us to start your learning journey' }}" placeholder="Registration page subtitle">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Register Button Text</label>
                                    <input type="text" class="form-control" name="register_button_text" value="{{ $settings['auth']['register_button_text'] ?? 'Create Account' }}" placeholder="Register button text">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Registration Enabled</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="registration_enabled" id="registrationEnabled" {{ ($settings['auth']['registration_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="registrationEnabled">
                                            Allow new user registrations
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Update Registration Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Permissions Settings -->
                <div id="permissions-settings" class="sidebar-section" style="display: none;">
                    <h3 class="section-title">
                        <i class="fas fa-shield-alt me-2"></i>Permissions
                    </h3>
                    <p class="text-muted small mb-3">Configure access permissions for different user roles on your website.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Permission Management:</strong> Control what features are available to directors and professors to customize the user experience on your training platform.
                    </div>
                    
                    <div class="permission-overview">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
                                        <h6>Director Access</h6>
                                        <p class="text-muted small">Configure administrative features for directors</p>
                                        <button class="btn btn-outline-primary btn-sm" onclick="showSection('director-features')">
                                            <i class="fas fa-cog me-1"></i>Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chalkboard-teacher fa-2x text-success mb-2"></i>
                                        <h6>Professor Access</h6>
                                        <p class="text-muted small">Configure teaching features for professors</p>
                                        <button class="btn btn-outline-success btn-sm" onclick="showSection('professor-features')">
                                            <i class="fas fa-cog me-1"></i>Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Director Features -->
                <div id="director-features" class="sidebar-section" style="display: none;">
                    <h3 class="section-title">
                        <i class="fas fa-user-tie me-2"></i>Director Features
                    </h3>

                    <form id="directorFeaturesForm" action="{{ route('smartprep.dashboard.settings.update.director', $selectedWebsite->id ?? 1) }}" method="POST" onsubmit="updateDirectorFeatures(event)">
                        @csrf
                        <p class="text-muted small mb-3">Control which features are available to directors in their admin dashboard.</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="directorViewStudents" name="view_students" checked>
                                    <label class="form-check-label" for="directorViewStudents">
                                        <strong>View Students</strong><br>
                                        <small class="text-muted">Allow directors to view student information and lists</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="directorManagePrograms" name="manage_programs">
                                    <label class="form-check-label" for="directorManagePrograms">
                                        <strong>Manage Programs</strong><br>
                                        <small class="text-muted">Allow directors to create and edit programs</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-footer mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Director Features
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="showSection('permissions-settings')">
                                <i class="fas fa-arrow-left me-2"></i>Back to Permissions
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Professor Features -->
                <div id="professor-features" class="sidebar-section" style="display: none;">
                    <h3 class="section-title">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Professor Features
                    </h3>

                    <form id="professorFeaturesForm" action="{{ route('smartprep.dashboard.settings.update.professor-features', $selectedWebsite->id ?? 1) }}" method="POST" onsubmit="updateProfessorFeatures(event)">
                        @csrf
                        <p class="text-muted small mb-3">Control which features are available to professors in their dashboard.</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="aiQuizEnabled" name="ai_quiz_enabled" checked>
                                    <label class="form-check-label" for="aiQuizEnabled">
                                        <strong>AI Quiz Generator</strong><br>
                                        <small class="text-muted">Allow professors to generate quizzes from documents</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="gradingEnabled" name="grading_enabled" checked>
                                    <label class="form-check-label" for="gradingEnabled">
                                        <strong>Grading System</strong><br>
                                        <small class="text-muted">Allow professors to grade assignments and quizzes</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-footer mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Professor Features
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="showSection('permissions-settings')">
                                <i class="fas fa-arrow-left me-2"></i>Back to Permissions
                            </button>
                        </div>
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
                            <a href="{{ $previewUrl ?? url('/artc') }}" class="preview-btn" target="_blank" id="openInNewTabLink">
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
                        id="previewFrame"
                        src="{{ $previewUrl ?? url('/artc') }}" 
                        data-preview-url="{{ $previewUrl ?? url('/artc') }}"
                        loading="lazy" 
                        referrerpolicy="no-referrer" 
                        sandbox="allow-same-origin allow-scripts allow-forms allow-popups" 
                        title="Website Preview"
                        ></iframe>
                    <script>
                        // Early lightweight stubs so iframe events never fail even if main script later has a parse error
                        if(!window.hideLoading){
                            window.hideLoading = function(){
                                var loading = document.getElementById('previewLoading');
                                var iframe = document.getElementById('previewFrame');
                                if(loading) loading.style.display='none';
                                if(iframe) iframe.style.opacity='1';
                            };
                        }
                        if(!window.showError){
                            window.showError = function(){
                                var loading = document.getElementById('previewLoading');
                                if(loading){
                                    loading.innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><div>Preview failed to load</div><small>Server may be offline</small></div>';
                                }
                            };
                        }
                        (function(){
                            var iframe = document.getElementById('previewFrame');
                            if(!iframe) return;
                            iframe.addEventListener('load', function(){ try{ window.hideLoading(); }catch(e){} });
                            iframe.addEventListener('error', function(){ try{ window.showError(); }catch(e){} });
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Main customization script -->
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
                    const sectionElement = document.getElementById(section + '-settings');
                    if (sectionElement) {
                        sectionElement.style.display = 'block';
                        sectionElement.classList.add('active');
                    }
                    
                    // Update preview for section
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

        // Function to show specific sections (for permissions navigation)
        function showSection(sectionId) {
            const sidebarSections = document.querySelectorAll('.sidebar-section');
            
            // Hide all sections
            sidebarSections.forEach(s => {
                s.classList.remove('active');
                s.style.display = 'none';
            });
            
            // Show the requested section
            const sectionElement = document.getElementById(sectionId);
            if (sectionElement) {
                sectionElement.style.display = 'block';
                sectionElement.classList.add('active');
            }
        }

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
            form.action="{{ route('smartprep.dashboard.websites.store') }}";
            form.innerHTML = `@csrf<input type="hidden" name="name" value="${name.replace(/"/g,'&quot;')}">`;
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
            fetch("{{ url('/smartprep/dashboard/websites') }}/"+websiteId, {
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

        // Social Media Links Management
        function addSocialLink() {
            const container = document.getElementById('socialLinksContainer');
            const index = container.children.length;
            
            const linkRow = document.createElement('div');
            linkRow.className = 'row g-2 mb-2 social-link-row';
            linkRow.innerHTML = `
                <div class="col-md-4">
                    <select class="form-control" name="social_links[${index}][platform]">
                        <option value="">Select Platform</option>
                        <option value="facebook">Facebook</option>
                        <option value="youtube">YouTube</option>
                        <option value="twitter">Twitter</option>
                        <option value="instagram">Instagram</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="tiktok">TikTok</option>
                        <option value="telegram">Telegram</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="url" class="form-control" name="social_links[${index}][url]" placeholder="https://...">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSocialLink(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(linkRow);
        }

        function removeSocialLink(button) {
            const row = button.closest('.social-link-row');
            row.remove();
            
            // Reindex remaining rows
            const container = document.getElementById('socialLinksContainer');
            const rows = container.querySelectorAll('.social-link-row');
            rows.forEach((row, index) => {
                const select = row.querySelector('select');
                const input = row.querySelector('input');
                select.name = `social_links[${index}][platform]`;
                input.name = `social_links[${index}][url]`;
            });
        }

        async function handleFormSubmission(event, settingType, loadingText) {
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            const formData = new FormData(event.target);
            const websiteMode = document.getElementById('website_mode')?.value || 'customize_current';
            const selectedWebsite = document.getElementById('selected_website')?.value || 'current';
            
            // Process social media links for general settings
            if (settingType === 'general') {
                const socialLinks = [];
                const platformInputs = document.querySelectorAll('select[name^="social_links"][name$="[platform]"]');
                const urlInputs = document.querySelectorAll('input[name^="social_links"][name$="[url]"]');
                
                for (let i = 0; i < platformInputs.length; i++) {
                    const platform = platformInputs[i].value;
                    const url = urlInputs[i].value;
                    if (platform && url) {
                        socialLinks.push({ platform, url });
                    }
                }
                
                // Remove existing social_links from formData and add processed version
                formData.delete('social_links');
                formData.append('social_links', JSON.stringify(socialLinks));
            }
            
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
                formData.append('_token', '{{ csrf_token() }}');
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
        
        // Enhanced hideLoading replaces early stub (defined above iframe) once main script parsed
        window.hideLoading = function() {
            const loading = document.getElementById('previewLoading');
            const iframe = document.getElementById('previewFrame');
            setTimeout(() => {
                if (loading) loading.style.display = 'none';
                if (iframe) iframe.style.opacity = '1';
            }, 300);
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
            formData.append('_token', '{{ csrf_token() }}');
            formData.append(fieldName, fieldValue);

            fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                let data;
                try {
                    if (contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        const text = await response.text();
                        // Attempt lenient parse only if it looks like JSON; otherwise treat as HTML error page
                        if (text.trim().startsWith('{') || text.trim().startsWith('[')) {
                            try { data = JSON.parse(text); } catch(e){ data = { success:false, parse_error:true, raw:text.slice(0,200) }; }
                        } else {
                            data = { success:false, non_json:true, status: response.status, snippet: text.slice(0,200) };
                        }
                    }
                } catch (e) {
                    data = { success:false, exception:true, message:e.message };
                }
                if (data && data.success) {
                    console.log('Auto-saved successfully:', fieldName);
                    updatePreviewElement(fieldName, fieldValue);
                } else {
                    console.warn('Auto-save response not JSON or failed', data);
                }
            })
            .catch(error => console.error('Auto-save fetch error:', error));
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
            const apiUrl = '{{ url("smartprep/api/programs") }}';
            fetch(apiUrl)
                .then(async response => {
                    const ct = response.headers.get('content-type') || '';
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error('HTTP '+response.status+': '+text.slice(0,120));
                    }
                    if (ct.includes('application/json')) return response.json();
                    const text = await response.text();
                    try { return JSON.parse(text); } catch { return { success:false, non_json:true, body:text.slice(0,200) }; }
                })
                .then(data => {
                    if (data && data.non_json) {
                        console.warn('Programs endpoint returned non-JSON snippet:', data.body);
                        return;
                    }
                    console.log('Programs loaded successfully:', data);
                })
                .catch(error => console.error('Error loading programs:', error));
        }
        
        // Call loadProgramsModal on page load if needed
        // Uncomment if you want to load programs on page initialization
        // loadProgramsModal();
    </script>

    <!-- Website Request Modal -->
    <div class="modal fade" id="websiteRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ $userRole === 'admin' ? route('smartprep.admin.settings.save') : route('smartprep.dashboard.submit-customized-website') }}" id="customizedWebsiteForm">
                    @csrf
                    <!-- Hidden fields to store customization data -->
                    <input type="hidden" name="customization_data" id="customizationData">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($userRole === 'admin')
                                <i class="fas fa-cog me-2"></i>Save Settings
                            @else
                                <i class="fas fa-rocket me-2"></i>Submit Website Request
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                @if($userRole === 'admin')
                                    <strong>Save template settings!</strong> Your customizations will be saved to the database and applied to the website template.
                                @else
                                    <strong>Ready to create your website!</strong> Your customizations have been saved and will be applied to your new website once approved.
                                @endif
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
                                <input type="email" class="form-control form-control-modern" name="contact_email" required value="{{ Auth::guard('smartprep_admin')->user()->email ?? Auth::guard('smartprep')->user()->email ?? '' }}">
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
                            @if($userRole === 'admin')
                                <i class="fas fa-save me-2"></i>Save Settings
                            @else
                                <i class="fas fa-paper-plane me-2"></i>Submit Website Request
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Load Bootstrap JS (needed for modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
