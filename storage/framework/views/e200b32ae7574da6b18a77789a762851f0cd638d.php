<!-- Navigation Settings -->
<div class="sidebar-section" id="navbar-settings" style="display: none;">
    <div class="section-header">
        <h5><i class="fas fa-bars me-2"></i>Navigation Bar</h5>
    </div>
    
    <form id="navbarForm" method="POST" action="<?php echo e(route('smartprep.dashboard.settings.update.navbar', ['website' => $selectedWebsite->id])); ?>" enctype="multipart/form-data" onsubmit="updateNavbar(event)">
        <?php echo csrf_field(); ?>
        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="form-group mb-3">
            <label class="form-label">Brand Name</label>
            <input type="text" class="form-control" name="brand_name" value="<?php echo e($settings['navbar']['brand_name'] ?? 'Your Company Name'); ?>" placeholder="Brand name">
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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/navbar.blade.php ENDPATH**/ ?>