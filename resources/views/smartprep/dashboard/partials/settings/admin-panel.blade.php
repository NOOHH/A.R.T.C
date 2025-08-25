<!-- Admin Panel Settings -->
<div id="admin-settings" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-cog me-2"></i>Admin Panel Settings
    </h3>
    
    <form id="adminForm" action="{{ route('smartprep.dashboard.settings.update.admin', ['website' => $selectedWebsite->id]) }}" method="POST" onsubmit="updateAdmin(event)">
        @csrf
        <input type="hidden" name="website_id" value="{{ $currentWebsite->id ?? '' }}">
        
        <!-- Dashboard Layout -->
        <div class="setting-group">
            <label for="admin_dashboard_layout" class="form-label">
                <i class="fas fa-layout-alt me-2"></i>Dashboard Layout
            </label>
            <select id="admin_dashboard_layout" name="admin_dashboard_layout" class="form-select">
                <option value="comprehensive" {{ ($settings['admin_dashboard_layout'] ?? 'comprehensive') == 'comprehensive' ? 'selected' : '' }}>Comprehensive Overview</option>
                <option value="simplified" {{ ($settings['admin_dashboard_layout'] ?? '') == 'simplified' ? 'selected' : '' }}>Simplified View</option>
                <option value="analytics-focused" {{ ($settings['admin_dashboard_layout'] ?? '') == 'analytics-focused' ? 'selected' : '' }}>Analytics-Focused</option>
            </select>
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
                    <input type="color" id="admin_sidebar_primary_color" class="form-control form-control-color" value="{{ $settings['admin_sidebar']['primary_color'] ?? '#dc3545' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary Color</label>
                    <input type="color" id="admin_sidebar_secondary_color" class="form-control form-control-color" value="{{ $settings['admin_sidebar']['secondary_color'] ?? '#6c757d' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Accent Color</label>
                    <input type="color" id="admin_sidebar_accent_color" class="form-control form-control-color" value="{{ $settings['admin_sidebar']['accent_color'] ?? '#ffc107' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Text Color</label>
                    <input type="color" id="admin_sidebar_text_color" class="form-control form-control-color" value="{{ $settings['admin_sidebar']['text_color'] ?? '#ffffff' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hover Color</label>
                    <input type="color" id="admin_sidebar_hover_color" class="form-control form-control-color" value="{{ $settings['admin_sidebar']['hover_color'] ?? '#0056b3' }}">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSidebarColors('admin')"><i class="fas fa-save me-2"></i>Save Sidebar Colors</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetAdminSidebarColors()"><i class="fas fa-undo me-2"></i>Reset</button>
            </div>
        </div>
    </div>
</div>
