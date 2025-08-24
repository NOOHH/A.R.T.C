<!-- Admin Panel Settings -->
<div id="admin-settings" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-cog me-2"></i>Admin Panel Settings
    </h3>
    
    <form id="adminForm" action="<?php echo e(route('smartprep.dashboard.settings.update.admin', ['website' => $selectedWebsite->id])); ?>" method="POST" onsubmit="updateAdmin(event)">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="website_id" value="<?php echo e($currentWebsite->id ?? ''); ?>">
        
        <!-- Dashboard Layout -->
        <div class="setting-group">
            <label for="admin_dashboard_layout" class="form-label">
                <i class="fas fa-layout-alt me-2"></i>Dashboard Layout
            </label>
            <select id="admin_dashboard_layout" name="admin_dashboard_layout" class="form-select">
                <option value="comprehensive" <?php echo e(($settings['admin_dashboard_layout'] ?? 'comprehensive') == 'comprehensive' ? 'selected' : ''); ?>>Comprehensive Overview</option>
                <option value="simplified" <?php echo e(($settings['admin_dashboard_layout'] ?? '') == 'simplified' ? 'selected' : ''); ?>>Simplified View</option>
                <option value="analytics-focused" <?php echo e(($settings['admin_dashboard_layout'] ?? '') == 'analytics-focused' ? 'selected' : ''); ?>>Analytics-Focused</option>
            </select>
        </div>
        
        <!-- Theme Colors -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-palette me-2"></i>Admin Panel Colors
            </label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="admin_primary_color" class="form-label small">Primary Color</label>
                    <div class="color-input-group">
                        <input type="color" id="admin_primary_color" name="admin_primary_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['admin_primary_color'] ?? '#dc3545'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['admin_primary_color'] ?? '#dc3545'); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="admin_secondary_color" class="form-label small">Secondary Color</label>
                    <div class="color-input-group">
                        <input type="color" id="admin_secondary_color" name="admin_secondary_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['admin_secondary_color'] ?? '#6c757d'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['admin_secondary_color'] ?? '#6c757d'); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="admin_sidebar_color" class="form-label small">Sidebar Color</label>
                    <div class="color-input-group">
                        <input type="color" id="admin_sidebar_color" name="admin_sidebar_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['admin_sidebar_color'] ?? '#343a40'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['admin_sidebar_color'] ?? '#343a40'); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="admin_accent_color" class="form-label small">Accent Color</label>
                    <div class="color-input-group">
                        <input type="color" id="admin_accent_color" name="admin_accent_color" 
                               class="form-control form-control-color" 
                               value="<?php echo e($settings['admin_accent_color'] ?? '#ffc107'); ?>">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php echo e($settings['admin_accent_color'] ?? '#ffc107'); ?>" readonly>
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
                <input type="checkbox" id="admin_show_sidebar" name="admin_show_sidebar" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_show_sidebar'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_show_sidebar" class="form-check-label">Show sidebar navigation</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_collapsible_sidebar" name="admin_collapsible_sidebar" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_collapsible_sidebar'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_collapsible_sidebar" class="form-check-label">Collapsible sidebar</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_show_breadcrumbs" name="admin_show_breadcrumbs" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_show_breadcrumbs'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_show_breadcrumbs" class="form-check-label">Show breadcrumbs</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_show_search" name="admin_show_search" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_show_search'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_show_search" class="form-check-label">Show global search</label>
            </div>
        </div>
        
        <!-- User Management -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-users me-2"></i>User Management Options
            </label>
            <div class="form-check">
                <input type="checkbox" id="admin_bulk_user_actions" name="admin_bulk_user_actions" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_bulk_user_actions'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_bulk_user_actions" class="form-check-label">Enable bulk user actions</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_user_impersonation" name="admin_user_impersonation" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_user_impersonation'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_user_impersonation" class="form-check-label">Allow user impersonation</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_advanced_user_filters" name="admin_advanced_user_filters" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_advanced_user_filters'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_advanced_user_filters" class="form-check-label">Advanced user filters</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_user_export" name="admin_user_export" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_user_export'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_user_export" class="form-check-label">User data export options</label>
            </div>
        </div>
        
        <!-- System Management -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-server me-2"></i>System Management
            </label>
            <div class="form-check">
                <input type="checkbox" id="admin_system_logs" name="admin_system_logs" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_system_logs'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_system_logs" class="form-check-label">Show system logs</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_performance_metrics" name="admin_performance_metrics" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_performance_metrics'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_performance_metrics" class="form-check-label">Performance metrics dashboard</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_backup_management" name="admin_backup_management" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_backup_management'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_backup_management" class="form-check-label">Backup management tools</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_maintenance_mode" name="admin_maintenance_mode" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_maintenance_mode'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_maintenance_mode" class="form-check-label">Maintenance mode toggle</label>
            </div>
        </div>
        
        <!-- Security Settings -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-shield-alt me-2"></i>Security Settings
            </label>
            <div class="form-check">
                <input type="checkbox" id="admin_two_factor" name="admin_two_factor" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_two_factor'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_two_factor" class="form-check-label">Require two-factor authentication</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_login_logs" name="admin_login_logs" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_login_logs'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_login_logs" class="form-check-label">Track login attempts</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_ip_restrictions" name="admin_ip_restrictions" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_ip_restrictions'] ?? false) ? 'checked' : ''); ?>>
                <label for="admin_ip_restrictions" class="form-check-label">Enable IP restrictions</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_session_management" name="admin_session_management" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_session_management'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_session_management" class="form-check-label">Advanced session management</label>
            </div>
        </div>
        
        <!-- Notification Preferences -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-bell me-2"></i>Notification Preferences
            </label>
            <div class="form-check">
                <input type="checkbox" id="admin_system_alerts" name="admin_system_alerts" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_system_alerts'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_system_alerts" class="form-check-label">System alerts</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_user_activity_alerts" name="admin_user_activity_alerts" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_user_activity_alerts'] ?? false) ? 'checked' : ''); ?>>
                <label for="admin_user_activity_alerts" class="form-check-label">User activity alerts</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_security_alerts" name="admin_security_alerts" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_security_alerts'] ?? true) ? 'checked' : ''); ?>>
                <label for="admin_security_alerts" class="form-check-label">Security alerts</label>
            </div>
            <div class="form-check">
                <input type="checkbox" id="admin_daily_reports" name="admin_daily_reports" class="form-check-input" 
                       value="1" <?php echo e(($settings['admin_daily_reports'] ?? false) ? 'checked' : ''); ?>>
                <label for="admin_daily_reports" class="form-check-label">Daily summary reports</label>
            </div>
        </div>
        
        <!-- Data Management -->
        <div class="setting-group">
            <label class="form-label">
                <i class="fas fa-database me-2"></i>Data Management
            </label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="admin_data_retention" class="form-label small">Data Retention Period</label>
                    <select id="admin_data_retention" name="admin_data_retention" class="form-select">
                        <option value="30" <?php echo e(($settings['admin_data_retention'] ?? '365') == '30' ? 'selected' : ''); ?>>30 days</option>
                        <option value="90" <?php echo e(($settings['admin_data_retention'] ?? '365') == '90' ? 'selected' : ''); ?>>90 days</option>
                        <option value="365" <?php echo e(($settings['admin_data_retention'] ?? '365') == '365' ? 'selected' : ''); ?>>1 year</option>
                        <option value="1095" <?php echo e(($settings['admin_data_retention'] ?? '365') == '1095' ? 'selected' : ''); ?>>3 years</option>
                        <option value="unlimited" <?php echo e(($settings['admin_data_retention'] ?? '365') == 'unlimited' ? 'selected' : ''); ?>>Unlimited</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="admin_backup_frequency" class="form-label small">Backup Frequency</label>
                    <select id="admin_backup_frequency" name="admin_backup_frequency" class="form-select">
                        <option value="daily" <?php echo e(($settings['admin_backup_frequency'] ?? 'weekly') == 'daily' ? 'selected' : ''); ?>>Daily</option>
                        <option value="weekly" <?php echo e(($settings['admin_backup_frequency'] ?? 'weekly') == 'weekly' ? 'selected' : ''); ?>>Weekly</option>
                        <option value="monthly" <?php echo e(($settings['admin_backup_frequency'] ?? 'weekly') == 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Save Button -->
        <div class="settings-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Admin Panel
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm('adminForm')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>

    <!-- Sidebar Customization (Admin) -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Customization</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Color</label>
                    <input type="color" id="admin_sidebar_primary_color" class="form-control form-control-color" value="<?php echo e($settings['admin_sidebar']['primary_color'] ?? '#dc3545'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary Color</label>
                    <input type="color" id="admin_sidebar_secondary_color" class="form-control form-control-color" value="<?php echo e($settings['admin_sidebar']['secondary_color'] ?? '#6c757d'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Accent Color</label>
                    <input type="color" id="admin_sidebar_accent_color" class="form-control form-control-color" value="<?php echo e($settings['admin_sidebar']['accent_color'] ?? '#ffc107'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Text Color</label>
                    <input type="color" id="admin_sidebar_text_color" class="form-control form-control-color" value="<?php echo e($settings['admin_sidebar']['text_color'] ?? '#ffffff'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hover Color</label>
                    <input type="color" id="admin_sidebar_hover_color" class="form-control form-control-color" value="<?php echo e($settings['admin_sidebar']['hover_color'] ?? '#0056b3'); ?>">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSidebarColors('admin')"><i class="fas fa-save me-2"></i>Save Sidebar Colors</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetAdminSidebarColors()"><i class="fas fa-undo me-2"></i>Reset</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/admin-panel.blade.php ENDPATH**/ ?>