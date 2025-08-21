<!-- Professor Panel Settings -->
<div id="professor-settings" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-chalkboard-teacher me-2"></i>Professor Panel Settings
    </h3>
    
    <form id="professorForm" action="<?php echo e(route('smartprep.dashboard.settings.update.professor', ['website' => $selectedWebsite->id])); ?>" method="POST" onsubmit="updateProfessor(event)">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="website_id" value="<?php echo e($currentWebsite->id ?? ''); ?>">
        
        <!-- Dashboard Layout -->
        <div class="setting-group">
            <label for="professor_dashboard_layout" class="form-label">
                <i class="fas fa-layout-alt me-2"></i>Dashboard Layout
            </label>
            <select id="professor_dashboard_layout" name="professor_dashboard_layout" class="form-select">
                <option value="overview" <?php echo e(($settings['professor_dashboard_layout'] ?? 'overview') == 'overview' ? 'selected' : ''); ?>>Overview Dashboard</option>
                <option value="course-focused" <?php echo e(($settings['professor_dashboard_layout'] ?? '') == 'course-focused' ? 'selected' : ''); ?>>Course-Focused</option>
                <option value="analytics" <?php echo e(($settings['professor_dashboard_layout'] ?? '') == 'analytics' ? 'selected' : ''); ?>>Analytics-Heavy</option>
            </select>
        </div>
        
        <!-- Theme Colors -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-palette me-2"></i>Professor Panel Colors
            </label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="professor_primary_color" class="form-label small">Primary Color</label>
                    <div class="color-input-group">
                        <input type="color" id="professor_primary_color" name="professor_primary_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['professor_primary_color'] ?? '#28a745'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['professor_primary_color'] ?? '#28a745'); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="professor_secondary_color" class="form-label small">Secondary Color</label>
                    <div class="color-input-group">
                        <input type="color" id="professor_secondary_color" name="professor_secondary_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['professor_secondary_color'] ?? '#6c757d'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['professor_secondary_color'] ?? '#6c757d'); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="professor_sidebar_color" class="form-label small">Sidebar Color</label>
                    <div class="color-input-group">
                        <input type="color" id="professor_sidebar_color" name="professor_sidebar_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['professor_sidebar_color'] ?? '#f8f9fa'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['professor_sidebar_color'] ?? '#f8f9fa'); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="professor_accent_color" class="form-label small">Accent Color</label>
                    <div class="color-input-group">
                        <input type="color" id="professor_accent_color" name="professor_accent_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['professor_accent_color'] ?? '#ffc107'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['professor_accent_color'] ?? '#ffc107'); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Note: Navigation Options, Course Management Options, Grading Options, Analytics & Reports, and Communication Settings have been removed as permissions will be handled in a different section -->
        
        <!-- Save Button -->
        <div class="settings-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Professor Panel
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm('professorForm')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>

    <!-- Sidebar Customization (Professor) -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Customization</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Color</label>
                    <input type="color" id="professor_sidebar_primary_color" class="form-control form-control-color" value="<?php echo e($settings['professor_sidebar']['primary_color'] ?? '#28a745'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary Color</label>
                    <input type="color" id="professor_sidebar_secondary_color" class="form-control form-control-color" value="<?php echo e($settings['professor_sidebar']['secondary_color'] ?? '#6c757d'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Accent Color</label>
                    <input type="color" id="professor_sidebar_accent_color" class="form-control form-control-color" value="<?php echo e($settings['professor_sidebar']['accent_color'] ?? '#ffc107'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Text Color</label>
                    <input type="color" id="professor_sidebar_text_color" class="form-control form-control-color" value="<?php echo e($settings['professor_sidebar']['text_color'] ?? '#333333'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hover Color</label>
                    <input type="color" id="professor_sidebar_hover_color" class="form-control form-control-color" value="<?php echo e($settings['professor_sidebar']['hover_color'] ?? '#0056b3'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sidebar Width</label>
                    <input type="number" id="professor_sidebar_width" class="form-control" placeholder="280" min="200" max="400" value="<?php echo e($settings['professor_sidebar']['width'] ?? '280'); ?>">
                    <small class="form-text text-muted">Width in pixels (200-400px)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Collapsed Width</label>
                    <input type="number" id="professor_sidebar_collapsed_width" class="form-control" placeholder="70" min="50" max="100" value="<?php echo e($settings['professor_sidebar']['collapsed_width'] ?? '70'); ?>">
                    <small class="form-text text-muted">Width when collapsed (50-100px)</small>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSidebarColors('professor')"><i class="fas fa-save me-2"></i>Save Sidebar Colors</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetProfessorSidebarColors()"><i class="fas fa-undo me-2"></i>Reset</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/professor-panel.blade.php ENDPATH**/ ?>