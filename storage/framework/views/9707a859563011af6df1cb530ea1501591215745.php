<!-- Advanced Settings -->
<div id="advanced-settings" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-code me-2"></i>Advanced Settings
    </h3>

    <form id="advancedForm" action="<?php echo e(route('smartprep.dashboard.settings.update.advanced', ['website' => $selectedWebsite->id])); ?>" method="POST" onsubmit="updateAdvanced(event)">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label class="form-label">Custom CSS</label>
            <textarea class="form-control" name="custom_css" rows="8" placeholder="/* Add your custom CSS here */" style="font-family: monospace; font-size: 12px;"><?php echo e($settings['advanced']['custom_css'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Custom JavaScript</label>
            <textarea class="form-control" name="custom_js" rows="6" placeholder="// Add your custom JavaScript here" style="font-family: monospace; font-size: 12px;"><?php echo e($settings['advanced']['custom_js'] ?? ''); ?></textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Google Analytics ID</label>
                <input type="text" class="form-control" name="google_analytics" value="<?php echo e($settings['advanced']['google_analytics'] ?? ''); ?>" placeholder="GA-XXXXXXXXXX">
            </div>
            <div class="col-md-6">
                <label class="form-label">Facebook Pixel ID</label>
                <input type="text" class="form-control" name="facebook_pixel" value="<?php echo e($settings['advanced']['facebook_pixel'] ?? ''); ?>" placeholder="Facebook Pixel ID">
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label class="form-label">Meta Tags</label>
            <textarea class="form-control" name="meta_tags" rows="4" placeholder="Additional meta tags"><?php echo e($settings['advanced']['meta_tags'] ?? ''); ?></textarea>
        </div>

        <h6 class="mt-4 mb-3">System Preferences</h6>
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" <?php echo e(($settings['advanced']['maintenance_mode'] ?? false) ? 'checked' : ''); ?>>
            <label class="form-check-label">Maintenance Mode</label>
        </div>
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="debug_mode" value="1" <?php echo e(($settings['advanced']['debug_mode'] ?? false) ? 'checked' : ''); ?>>
            <label class="form-check-label">Debug Mode</label>
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="cache_enabled" value="1" <?php echo e(($settings['advanced']['cache_enabled'] ?? true) ? 'checked' : ''); ?>>
            <label class="form-check-label">Enable Caching</label>
        </div>

        <div class="settings-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Advanced
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm('advancedForm')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\dashboard\partials\settings\advanced.blade.php ENDPATH**/ ?>