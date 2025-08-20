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
        
        <!-- Navigation Options -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-bars me-2"></i>Navigation Options
            </label>
            <div class="form-check">
                <input type="checkbox" id="professor_show_sidebar" name="professor_show_sidebar" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_show_sidebar'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_show_sidebar" class="form-check-label">Show sidebar navigation</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_collapsible_sidebar" name="professor_collapsible_sidebar" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_collapsible_sidebar'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_collapsible_sidebar" class="form-check-label">Collapsible sidebar</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_show_quick_actions" name="professor_show_quick_actions" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_show_quick_actions'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_show_quick_actions" class="form-check-label">Show quick actions toolbar</label>
            </div>
        </div>
        
        <!-- Course Management -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-book me-2"></i>Course Management Options
            </label>
            <div class="form-check">
                <input type="checkbox" id="professor_bulk_actions" name="professor_bulk_actions" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_bulk_actions'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_bulk_actions" class="form-check-label">Enable bulk actions</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_advanced_filters" name="professor_advanced_filters" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_advanced_filters'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_advanced_filters" class="form-check-label">Show advanced filters</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_export_options" name="professor_export_options" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_export_options'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_export_options" class="form-check-label">Enable export options</label>
            </div>
        </div>
        
        <!-- Grading Options -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-clipboard-check me-2"></i>Grading Options
            </label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="professor_grading_scale" class="form-label small">Default Grading Scale</label>
                    <select id="professor_grading_scale" name="professor_grading_scale" class="form-select">
                        <option value="percentage" <?php echo e(($settings['professor_grading_scale'] ?? 'percentage') == 'percentage' ? 'selected' : ''); ?>>Percentage (0-100)</option>
                        <option value="letter" <?php echo e(($settings['professor_grading_scale'] ?? '') == 'letter' ? 'selected' : ''); ?>>Letter Grades (A-F)</option>
                        <option value="points" <?php echo e(($settings['professor_grading_scale'] ?? '') == 'points' ? 'selected' : ''); ?>>Points Based</option>
                        <option value="pass-fail" <?php echo e(($settings['professor_grading_scale'] ?? '') == 'pass-fail' ? 'selected' : ''); ?>>Pass/Fail</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="professor_grade_display" class="form-label small">Grade Display Format</label>
                    <select id="professor_grade_display" name="professor_grade_display" class="form-select">
                        <option value="numeric" <?php echo e(($settings['professor_grade_display'] ?? 'numeric') == 'numeric' ? 'selected' : ''); ?>>Numeric Only</option>
                        <option value="letter" <?php echo e(($settings['professor_grade_display'] ?? '') == 'letter' ? 'selected' : ''); ?>>Letter Only</option>
                        <option value="both" <?php echo e(($settings['professor_grade_display'] ?? '') == 'both' ? 'selected' : ''); ?>>Both Numeric & Letter</option>
                    </select>
                </div>
            </div>
            <div class="form-check mt-2">
                <input type="checkbox" id="professor_auto_calculate" name="professor_auto_calculate" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_auto_calculate'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_auto_calculate" class="form-check-label">Auto-calculate final grades</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_grade_comments" name="professor_grade_comments" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_grade_comments'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_grade_comments" class="form-check-label">Enable grade comments</label>
            </div>
        </div>
        
        <!-- Analytics & Reports -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-chart-bar me-2"></i>Analytics & Reports
            </label>
            <div class="form-check">
                <input type="checkbox" id="professor_show_analytics" name="professor_show_analytics" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_show_analytics'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_show_analytics" class="form-check-label">Show analytics dashboard</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_student_progress" name="professor_student_progress" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_student_progress'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_student_progress" class="form-check-label">Student progress tracking</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_engagement_metrics" name="professor_engagement_metrics" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_engagement_metrics'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_engagement_metrics" class="form-check-label">Engagement metrics</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_automated_reports" name="professor_automated_reports" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_automated_reports'] ?? false) ? 'checked' : ''); ?>>
                <label for="professor_automated_reports" class="form-check-label">Automated weekly reports</label>
            </div>
        </div>
        
        <!-- Communication Settings -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-comments me-2"></i>Communication Settings
            </label>
            <div class="form-check">
                <input type="checkbox" id="professor_messaging" name="professor_messaging" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_messaging'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_messaging" class="form-check-label">Enable messaging system</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_announcements" name="professor_announcements" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_announcements'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_announcements" class="form-check-label">Course announcements</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="professor_office_hours" name="professor_office_hours" class="form-check-input" 
                       value="1" <?php echo e(($settings['professor_office_hours'] ?? true) ? 'checked' : ''); ?>>
                <label for="professor_office_hours" class="form-check-label">Office hours scheduling</label>
            </div>
        </div>
        
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
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSidebarColors('professor')"><i class="fas fa-save me-2"></i>Save Sidebar Colors</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetProfessorSidebarColors()"><i class="fas fa-undo me-2"></i>Reset</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/professor-panel.blade.php ENDPATH**/ ?>