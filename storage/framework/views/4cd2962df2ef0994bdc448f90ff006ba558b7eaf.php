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
                <button type="button" class="settings-nav-tab" data-section="permissions">
                    <i class="fas fa-shield-alt me-2"></i>Permissions
                </button>
                <button type="button" class="settings-nav-tab" data-section="auth">
                    <i class="fas fa-sign-in-alt me-2"></i>Login/Register
                </button>
            </div>
            
            <div class="settings-actions">
                <button class="btn btn-outline-primary" onclick="saveAllSettings()">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <button class="btn btn-primary" onclick="publishChanges()">
                    <i class="fas fa-rocket me-2"></i>Request Website Publishment
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
        <?php echo $__env->make('smartprep.dashboard.partials.settings.general', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Branding Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.branding', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Navigation Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Homepage Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.homepage', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Student Portal Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.student-portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Professor Panel Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.professor-panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Admin Panel Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.admin-panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Permissions Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.permissions', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Auth (Login/Register) Settings -->
        <?php echo $__env->make('smartprep.dashboard.partials.settings.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <!-- Live Preview Panel -->
    <div class="preview-panel" data-preview-base="<?php echo e($previewUrl); ?>">
        <div class="preview-header">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="preview-title">
                    <i class="fas fa-eye me-2"></i>Live Preview
                </h5>
                <div class="preview-controls">
                    <button class="preview-btn" onclick="refreshPreview()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                    <?php
                        $initialPreview = ($settings['general']['preview_url'] ?? $previewUrl);
                        // Only append preview flag for non-draft internal routes (no /t/draft/ segment and not already flagged)
                        if (!str_contains($initialPreview, '/t/draft/') && !str_contains($initialPreview,'preview=')) {
                            $initialPreview .= (str_contains($initialPreview,'?')?'&':'?').'preview=true';
                        }
                    ?>
                    <a href="<?php echo e($initialPreview); ?>" class="preview-btn" target="_blank" id="openInNewTabLink">
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
                <?php
                    $frameBase = ($settings['general']['preview_url'] ?? $previewUrl);
                    $frameSrc = $frameBase;
                    if (!str_contains($frameBase, '/t/draft/') && !str_contains($frameBase, 'preview=')) {
                        $frameSrc .= (str_contains($frameBase,'?')?'&':'?').'preview=true';
                    }
                ?>
                src="<?php echo e($frameSrc); ?>" 
                title="Site Preview"
                id="previewFrame"
                onload="hideLoading()"
                onerror="showError()">
            </iframe>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/customize-interface.blade.php ENDPATH**/ ?>