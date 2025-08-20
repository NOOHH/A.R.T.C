<!-- Top Settings Navigation -->
<nav class="settings-navbar">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            
            <div class="settings-nav-tabs">
                <button type="button" class="settings-nav-tab active" data-section="general">
                    <i class="fas fa-cog me-2"></i>General
                </button>
                <button type="button" class="settings-nav-tab" data-section="branding">
                    <i class="fas fa-palette me-2"></i>Branding
                </button>
                <button type="button" class="settings-nav-tab" data-section="navbar">
                    <i class="fas fa-bars me-2"></i>Navigation
                </button>
                <button type="button" class="settings-nav-tab" data-section="homepage">
                    <i class="fas fa-home me-2"></i>Homepage
                </button>
                <button type="button" class="settings-nav-tab" data-section="student">
                    <i class="fas fa-user-graduate me-2"></i>Student Portal
                </button>
                <button type="button" class="settings-nav-tab" data-section="professor">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Professor Panel
                </button>
                <button type="button" class="settings-nav-tab" data-section="admin">
                    <i class="fas fa-user-shield me-2"></i>Admin Panel
                </button>
                <button type="button" class="settings-nav-tab" data-section="advanced">
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
        @include('smartprep.dashboard.partials.settings.general')
        
        <!-- Branding Settings -->
        @include('smartprep.dashboard.partials.settings.branding')
        
        <!-- Navigation Settings -->
        @include('smartprep.dashboard.partials.settings.navbar')
        
        <!-- Homepage Settings -->
        @include('smartprep.dashboard.partials.settings.homepage')
        
        <!-- Student Portal Settings -->
        @include('smartprep.dashboard.partials.settings.student-portal')
        
        <!-- Professor Panel Settings -->
        @include('smartprep.dashboard.partials.settings.professor-panel')
        
        <!-- Admin Panel Settings -->
        @include('smartprep.dashboard.partials.settings.admin-panel')
        
        <!-- Advanced Settings -->
        @include('smartprep.dashboard.partials.settings.advanced')
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
                    <a href="{{ $settings['general']['preview_url'] ?? $previewUrl }}" class="preview-btn" target="_blank" id="openInNewTabLink">
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
                src="{{ $settings['general']['preview_url'] ?? $previewUrl }}" 
                title="Site Preview"
                id="previewFrame"
                onload="hideLoading()"
                onerror="showError()">
            </iframe>
        </div>
    </div>
</div>
