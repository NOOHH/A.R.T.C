@extends('admin.admin-dashboard-layout')

@section('title', 'Website Settings')

@push('styles')
<link href="{{ asset('css/admin/admin-settings/admin-settings-new.css') }}" rel="stylesheet">
<style>
/* Bootstrap customizations for settings page */
.settings-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-tabs {
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
    padding: 0 1rem;
}

.settings-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    border-radius: 0;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.settings-tabs .nav-link:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border-color: transparent;
}

.settings-tabs .nav-link.active {
    background: #fff;
    color: #667eea;
    border-bottom-color: #667eea;
    font-weight: 600;
}

.settings-content {
    padding: 0;
}

.settings-card {
    border: none;
    margin-bottom: 2rem;
    background: #fff;
}

.settings-card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
}

.settings-card-header h3 {
    margin: 0 0 0.5rem 0;
    color: #495057;
    font-size: 1.25rem;
    font-weight: 600;
}

.settings-card-header p {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.settings-card-body {
    padding: 1.5rem;
}

.color-input-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.form-control-color {
    width: 60px;
    height: 38px;
    border-radius: 6px;
    border: 1px solid #ced4da;
}

.color-hex {
    flex: 1;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.form-actions {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.preview-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    padding: 1.5rem;
    position: sticky;
    top: 2rem;
}

.preview-header h4 {
    color: #495057;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.preview-header p {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.website-preview {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    font-size: 0.75rem;
}

.nav-preview {
    padding: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dee2e6;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.preview-logo {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.brand-text {
    font-weight: 600;
    font-size: 0.75rem;
}

.nav-links {
    display: flex;
    gap: 1rem;
}

.nav-links span {
    font-size: 0.7rem;
    opacity: 0.8;
}

.hero-preview {
    padding: 2rem 1rem;
    text-align: center;
    color: white;
}

.hero-preview h1 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.hero-preview p {
    font-size: 0.8rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.hero-btn {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-size: 0.7rem;
}

.programs-preview {
    padding: 1rem;
    background: #f8f9fa;
}

.program-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 1rem;
    text-align: center;
}

.program-card h4 {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.program-card p {
    font-size: 0.7rem;
    opacity: 0.8;
    margin-bottom: 0.75rem;
}

.enroll-btn {
    padding: 0.375rem 0.75rem;
    border: none;
    border-radius: 4px;
    color: white;
    font-size: 0.7rem;
}

.footer-preview {
    padding: 1rem;
    text-align: center;
    font-size: 0.65rem;
    border-top: 1px solid #dee2e6;
}

.current-image-info {
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.alert {
    border: none;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 0.5rem 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6c46a0 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Website Settings</h1>
                <p class="page-subtitle">Customize your website's appearance and branding</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column: Settings Forms -->
        <div class="col-lg-8">
            <div class="settings-container">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab">
                            <i class="bi bi-palette"></i> Branding
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout" type="button" role="tab">
                            <i class="bi bi-layout-text-window"></i> Layout
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages" type="button" role="tab">
                            <i class="bi bi-file-earmark-text"></i> Pages
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="forms-tab" data-bs-toggle="tab" data-bs-target="#forms" type="button" role="tab">
                            <i class="bi bi-ui-checks"></i> Forms & Buttons
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content settings-content" id="settingsTabContent">
                    <!-- BRANDING TAB -->
                    <div class="tab-pane fade show active" id="branding" role="tabpanel">
                        <!-- Global Logo -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-image"></i> Website Logo</h3>
                                <p>This logo will appear in the navigation bar and throughout your website</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.global-logo') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label for="global_logo" class="form-label">Upload Logo</label>
                                            <input type="file" class="form-control" id="global_logo" name="global_logo" accept="image/*">
                                            <div class="form-text">Recommended: PNG or SVG format, max 5MB</div>
                                        </div>
                                        <div class="col-md-4">
                                            @if(isset($settings['global_logo']) && $settings['global_logo'])
                                                <div class="current-logo-preview">
                                                    <label class="form-label">Current Logo</label>
                                                    <div class="logo-preview-container">
                                                        <img src="{{ asset('storage/' . $settings['global_logo']) }}" alt="Current Logo" class="current-logo">
                                                        <form action="{{ route('admin.settings.remove.global-logo') }}" method="POST" class="remove-logo-form">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger remove-btn" onclick="return confirm('Remove logo?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="no-logo-preview">
                                                    <i class="bi bi-image"></i>
                                                    <p>No logo uploaded</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-cloud-upload"></i> Update Logo
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Brand Colors -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-palette-fill"></i> Brand Colors</h3>
                                <p>Define your website's primary color scheme</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.buttons') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="color-group">
                                                <label for="primary_color" class="form-label">Primary Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ $settings['buttons']['primary_color'] ?? '#667eea' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['primary_color'] ?? '#667eea' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="color-group">
                                                <label for="primary_text_color" class="form-label">Primary Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="primary_text_color" name="primary_text_color" value="{{ $settings['buttons']['primary_text_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['primary_text_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="color-group">
                                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ $settings['buttons']['secondary_color'] ?? '#6c757d' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['secondary_color'] ?? '#6c757d' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="color-group">
                                                <label for="secondary_text_color" class="form-label">Secondary Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="secondary_text_color" name="secondary_text_color" value="{{ $settings['buttons']['secondary_text_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['secondary_text_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Colors
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- LAYOUT TAB -->
                    <div class="tab-pane fade" id="layout" role="tabpanel">
                        <!-- Navigation Bar -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-navigation"></i> Navigation Bar</h3>
                                <p>Customize your website's navigation appearance</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.navbar') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="navbar_brand_name" class="form-label">Brand Name</label>
                                                <input type="text" class="form-control" id="navbar_brand_name" name="navbar_brand_name" value="{{ $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="navbar_text_color" class="form-label">Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="navbar_text_color" name="navbar_text_color" value="{{ $settings['navbar']['text_color'] ?? '#222222' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['navbar']['text_color'] ?? '#222222' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="navbar_background_color" class="form-label">Background Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="navbar_background_color" name="navbar_background_color" value="{{ $settings['navbar']['background_color'] ?? '#f1f1f1' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['navbar']['background_color'] ?? '#f1f1f1' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="navbar_gradient_color" class="form-label">Gradient Color (Optional)</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="navbar_gradient_color" name="navbar_gradient_color" value="{{ $settings['navbar']['gradient_color'] ?? '' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['navbar']['gradient_color'] ?? '' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Navigation
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-card-text"></i> Footer</h3>
                                <p>Customize your website's footer appearance</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.footer') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="footer_text" class="form-label">Footer Text</label>
                                                <textarea class="form-control" id="footer_text" name="footer_text" rows="3">{{ $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="footer_background_color" class="form-label">Background Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="footer_background_color" name="footer_background_color" value="{{ $settings['footer']['background_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['footer']['background_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="footer_text_color" class="form-label">Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="footer_text_color" name="footer_text_color" value="{{ $settings['footer']['text_color'] ?? '#444444' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['footer']['text_color'] ?? '#444444' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Footer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- PAGES TAB -->
                    <div class="tab-pane fade" id="pages" role="tabpanel">
                        <!-- Homepage -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-house-door"></i> Homepage</h3>
                                <p>Customize your homepage appearance and content</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.homepage') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="homepage_title" class="form-label">Hero Title</label>
                                                <input type="text" class="form-control" id="homepage_title" name="homepage_title" value="{{ $settings['homepage']['title'] ?? 'ENROLL NOW' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="homepage_background_image" class="form-label">Background Image</label>
                                                <input type="file" class="form-control" id="homepage_background_image" name="homepage_background_image" accept="image/*">
                                                @if(isset($settings['homepage']['background_image']) && $settings['homepage']['background_image'])
                                                    <div class="current-image-info">
                                                        <small class="text-muted">Current: {{ basename($settings['homepage']['background_image']) }}</small>
                                                        <form action="{{ route('admin.settings.remove.image') }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="type" value="homepage">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Remove image?')">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="homepage_background_color" class="form-label">Background Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="homepage_background_color" name="homepage_background_color" value="{{ $settings['homepage']['background_color'] ?? '#667eea' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['homepage']['background_color'] ?? '#667eea' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="homepage_gradient_color" class="form-label">Gradient Color (Optional)</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="homepage_gradient_color" name="homepage_gradient_color" value="{{ $settings['homepage']['gradient_color'] ?? '' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['homepage']['gradient_color'] ?? '' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="homepage_text_color" class="form-label">Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="homepage_text_color" name="homepage_text_color" value="{{ $settings['homepage']['text_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['homepage']['text_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Homepage
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Login Page -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-box-arrow-in-right"></i> Login Page</h3>
                                <p>Customize the login page appearance</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.login') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="login_background_image" class="form-label">Background Image</label>
                                                <input type="file" class="form-control" id="login_background_image" name="login_background_image" accept="image/*">
                                                @if(isset($settings['login']['background_image']) && $settings['login']['background_image'])
                                                    <div class="current-image-info">
                                                        <small class="text-muted">Current: {{ basename($settings['login']['background_image']) }}</small>
                                                        <form action="{{ route('admin.settings.remove.image') }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="type" value="login">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Remove image?')">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="login_illustration" class="form-label">Login Illustration</label>
                                                <input type="file" class="form-control" id="login_illustration" name="login_illustration" accept="image/*">
                                                @if(isset($settings['login']['login_illustration']) && $settings['login']['login_illustration'])
                                                    <div class="current-image-info">
                                                        <small class="text-muted">Current: {{ basename($settings['login']['login_illustration']) }}</small>
                                                        <form action="{{ route('admin.settings.remove.login-illustration') }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Remove illustration?')">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="login_background_color" class="form-label">Background Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="login_background_color" name="login_background_color" value="{{ $settings['login']['background_color'] ?? '#f8f9fa' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['login']['background_color'] ?? '#f8f9fa' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="login_gradient_color" class="form-label">Gradient Color (Optional)</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="login_gradient_color" name="login_gradient_color" value="{{ $settings['login']['gradient_color'] ?? '' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['login']['gradient_color'] ?? '' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="login_accent_color" class="form-label">Accent Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="login_accent_color" name="login_accent_color" value="{{ $settings['login']['accent_color'] ?? '#667eea' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['login']['accent_color'] ?? '#667eea' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Login Page
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Program Cards & Enrollment -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-card-list"></i> Program Cards & Enrollment</h3>
                                <p>Customize program display and enrollment pages</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.program-cards') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Program Cards</h5>
                                            <div class="form-group">
                                                <label for="program_card_background_color" class="form-label">Card Background</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="program_card_background_color" name="program_card_background_color" value="{{ $settings['program_cards']['background_color'] ?? '#f9f9f9' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['program_cards']['background_color'] ?? '#f9f9f9' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="program_card_text_color" class="form-label">Card Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="program_card_text_color" name="program_card_text_color" value="{{ $settings['program_cards']['text_color'] ?? '#333333' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['program_cards']['text_color'] ?? '#333333' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="program_cards_background_image" class="form-label">Section Background Image</label>
                                                <input type="file" class="form-control" id="program_cards_background_image" name="program_cards_background_image" accept="image/*">
                                                @if(isset($settings['program_cards']['background_image']) && $settings['program_cards']['background_image'])
                                                    <div class="current-image-info">
                                                        <small class="text-muted">Current: {{ basename($settings['program_cards']['background_image']) }}</small>
                                                        <form action="{{ route('admin.settings.remove.image') }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="type" value="program_cards">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Remove image?')">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Enrollment Page</h5>
                                            <div class="form-group">
                                                <label for="enrollment_page_background_color" class="form-label">Page Background</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="enrollment_page_background_color" name="enrollment_page_background_color" value="{{ $settings['program_cards']['enrollment_page_background_color'] ?? '#f8f9fa' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['program_cards']['enrollment_page_background_color'] ?? '#f8f9fa' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="enrollment_button_color" class="form-label">Enroll Button Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="enrollment_button_color" name="enrollment_button_color" value="{{ $settings['program_cards']['enrollment_button_color'] ?? '#667eea' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['program_cards']['enrollment_button_color'] ?? '#667eea' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="enrollment_form_background_color" class="form-label">Form Background</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="enrollment_form_background_color" name="enrollment_form_background_color" value="{{ $settings['program_cards']['enrollment_form_background_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['program_cards']['enrollment_form_background_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Program Cards & Enrollment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- FORMS & BUTTONS TAB -->
                    <div class="tab-pane fade" id="forms" role="tabpanel">
                        <!-- Advanced Button Colors -->
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <h3><i class="bi bi-ui-checks-grid"></i> Button Colors</h3>
                                <p>Customize button colors for different actions</p>
                            </div>
                            <div class="settings-card-body">
                                <form action="{{ route('admin.settings.update.buttons') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Success Buttons</h5>
                                            <div class="form-group">
                                                <label for="success_color" class="form-label">Success Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="success_color" name="success_color" value="{{ $settings['buttons']['success_color'] ?? '#28a745' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['success_color'] ?? '#28a745' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="success_text_color" class="form-label">Success Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="success_text_color" name="success_text_color" value="{{ $settings['buttons']['success_text_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['success_text_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Danger Buttons</h5>
                                            <div class="form-group">
                                                <label for="danger_color" class="form-label">Danger Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="danger_color" name="danger_color" value="{{ $settings['buttons']['danger_color'] ?? '#dc3545' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['danger_color'] ?? '#dc3545' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="danger_text_color" class="form-label">Danger Text Color</label>
                                                <div class="color-input-group">
                                                    <input type="color" class="form-control form-control-color" id="danger_text_color" name="danger_text_color" value="{{ $settings['buttons']['danger_text_color'] ?? '#ffffff' }}">
                                                    <input type="text" class="form-control color-hex" value="{{ $settings['buttons']['danger_text_color'] ?? '#ffffff' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check2"></i> Update Button Colors
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Live Preview -->
        <div class="col-lg-4">
            <div class="preview-container sticky-top">
                <div class="preview-header">
                    <h4><i class="bi bi-eye"></i> Live Preview</h4>
                    <p>See your changes in real-time</p>
                </div>
                
                <!-- Website Preview -->
                <div class="website-preview">
                    <!-- Navigation Preview -->
                    <div class="nav-preview" id="navPreview">
                        <div class="nav-brand">
                            @if(isset($settings['global_logo']) && $settings['global_logo'])
                                <img src="{{ asset('storage/' . $settings['global_logo']) }}" alt="Logo" class="preview-logo">
                            @endif
                            <span class="brand-text" id="brandText">{{ $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center' }}</span>
                        </div>
                        <div class="nav-links">
                            <span>Home</span>
                            <span>Programs</span>
                            <span>About</span>
                        </div>
                    </div>

                    <!-- Homepage Hero Preview -->
                    <div class="hero-preview" id="heroPreview">
                        <h1 id="heroTitle">{{ $settings['homepage']['title'] ?? 'ENROLL NOW' }}</h1>
                        <p>Your education journey starts here</p>
                        <button class="hero-btn">Get Started</button>
                    </div>

                    <!-- Program Cards Preview -->
                    <div class="programs-preview">
                        <div class="program-card" id="programCardPreview">
                            <h4>Sample Program</h4>
                            <p>Program description goes here</p>
                            <button class="enroll-btn" id="enrollBtnPreview">Enroll Now</button>
                        </div>
                    </div>

                    <!-- Footer Preview -->
                    <div class="footer-preview" id="footerPreview">
                        <p id="footerText">{!! $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time preview updates
    
    // Color inputs update hex values
    document.querySelectorAll('input[type="color"]').forEach(input => {
        input.addEventListener('input', function() {
            const hexInput = this.parentElement.querySelector('.color-hex');
            if (hexInput) {
                hexInput.value = this.value.toUpperCase();
            }
            updatePreview();
        });
    });

    // Text inputs
    document.querySelectorAll('input[type="text"], textarea').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        // Update navigation preview
        const navPreview = document.getElementById('navPreview');
        const brandText = document.getElementById('brandText');
        const navBgColor = document.getElementById('navbar_background_color')?.value || '#f1f1f1';
        const navGradientColor = document.getElementById('navbar_gradient_color')?.value;
        const navTextColor = document.getElementById('navbar_text_color')?.value || '#222222';
        const navBrandName = document.getElementById('navbar_brand_name')?.value || 'Ascendo Review and Training Center';

        if (navPreview) {
            let navBackground = navBgColor;
            if (navGradientColor && navGradientColor !== navBgColor) {
                navBackground = `linear-gradient(135deg, ${navBgColor} 0%, ${navGradientColor} 100%)`;
            }
            navPreview.style.background = navBackground;
            navPreview.style.color = navTextColor;
        }

        if (brandText) {
            brandText.textContent = navBrandName;
        }

        // Update homepage hero preview
        const heroPreview = document.getElementById('heroPreview');
        const heroTitle = document.getElementById('heroTitle');
        const homepageBgColor = document.getElementById('homepage_background_color')?.value || '#667eea';
        const homepageGradientColor = document.getElementById('homepage_gradient_color')?.value;
        const homepageTextColor = document.getElementById('homepage_text_color')?.value || '#ffffff';
        const homepageTitleText = document.getElementById('homepage_title')?.value || 'ENROLL NOW';

        if (heroPreview) {
            let heroBackground = homepageBgColor;
            if (homepageGradientColor && homepageGradientColor !== homepageBgColor) {
                heroBackground = `linear-gradient(135deg, ${homepageBgColor} 0%, ${homepageGradientColor} 100%)`;
            }
            heroPreview.style.background = heroBackground;
            heroPreview.style.color = homepageTextColor;
        }

        if (heroTitle) {
            heroTitle.textContent = homepageTitleText;
        }

        // Update program card preview
        const programCardPreview = document.getElementById('programCardPreview');
        const enrollBtnPreview = document.getElementById('enrollBtnPreview');
        const cardBgColor = document.getElementById('program_card_background_color')?.value || '#f9f9f9';
        const cardTextColor = document.getElementById('program_card_text_color')?.value || '#333333';
        const enrollBtnColor = document.getElementById('enrollment_button_color')?.value || '#667eea';
        const enrollBtnTextColor = document.getElementById('enrollment_button_text_color')?.value || '#ffffff';

        if (programCardPreview) {
            programCardPreview.style.background = cardBgColor;
            programCardPreview.style.color = cardTextColor;
        }

        if (enrollBtnPreview) {
            enrollBtnPreview.style.background = enrollBtnColor;
            enrollBtnPreview.style.color = enrollBtnTextColor;
        }

        // Update footer preview
        const footerPreview = document.getElementById('footerPreview');
        const footerText = document.getElementById('footerText');
        const footerBgColor = document.getElementById('footer_background_color')?.value || '#ffffff';
        const footerTextColor = document.getElementById('footer_text_color')?.value || '#444444';
        const footerTextContent = document.getElementById('footer_text')?.value || '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.';

        if (footerPreview) {
            footerPreview.style.background = footerBgColor;
            footerPreview.style.color = footerTextColor;
        }

        if (footerText) {
            footerText.innerHTML = footerTextContent;
        }
    }

    // Initial preview update
    updatePreview();
});
</script>
@endsection
