<!-- General Settings -->
<div class="sidebar-section active" id="general-settings">
    <div class="section-header">
        <h5><i class="fas fa-cog me-2"></i>General Settings</h5>
    </div>
    
    <form id="generalForm" method="POST" action="<?php echo e(route('smartprep.dashboard.settings.update.general', ['website' => $selectedWebsite->id])); ?>">
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
            <input type="url" class="form-control" name="preview_url" value="<?php echo e($settings['general']['preview_url'] ?? $previewUrl); ?>" placeholder="<?php echo e($previewUrl); ?>">
            <small class="form-text text-muted">URL for the live preview iframe</small>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync me-2"></i>Update General Settings
        </button>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/general.blade.php ENDPATH**/ ?>