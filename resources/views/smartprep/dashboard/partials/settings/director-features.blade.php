<!-- Director Features -->
<div id="director-features" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-user-tie me-2"></i>Director Features
    </h3>

    @if(isset($selectedWebsite))
    <form id="directorFeaturesForm" action="{{ route('smartprep.dashboard.settings.update.director', ['website' => $selectedWebsite->id]) }}" method="POST" onsubmit="updateDirectorFeatures(event)">
        @csrf
        <p class="text-muted small mb-3">Control which features are available to directors in their admin dashboard.</p>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorViewStudents" name="view_students" {{ ($settings['director_features']['view_students'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorViewStudents">
                        <strong>View Students</strong><br>
                        <small class="text-muted">Allow directors to view student information and lists</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManagePrograms" name="manage_programs" {{ ($settings['director_features']['manage_programs'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorManagePrograms">
                        <strong>Manage Programs</strong><br>
                        <small class="text-muted">Allow directors to create and edit programs</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageModules" name="manage_modules" {{ ($settings['director_features']['manage_modules'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorManageModules">
                        <strong>Manage Modules</strong><br>
                        <small class="text-muted">Allow directors to create and edit modules</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageEnrollments" name="manage_enrollments" {{ ($settings['director_features']['manage_enrollments'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorManageEnrollments">
                        <strong>Manage Enrollments</strong><br>
                        <small class="text-muted">Allow directors to manage student enrollments</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorViewAnalytics" name="view_analytics" {{ ($settings['director_features']['view_analytics'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorViewAnalytics">
                        <strong>View Analytics</strong><br>
                        <small class="text-muted">Allow directors to view analytics and reports</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageProfessors" name="manage_professors" {{ ($settings['director_features']['manage_professors'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorManageProfessors">
                        <strong>Manage Professors</strong><br>
                        <small class="text-muted">Allow directors to manage professor accounts</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageAnnouncements" name="manage_announcements" {{ ($settings['director_features']['manage_announcements'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorManageAnnouncements">
                        <strong>Manage Announcements</strong><br>
                        <small class="text-muted">Allow directors to create and manage announcements</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageBatches" name="manage_batches" {{ ($settings['director_features']['manage_batches'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="directorManageBatches">
                        <strong>Manage Batches</strong><br>
                        <small class="text-muted">Allow directors to manage student batches</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-footer mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Director Features
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm('directorFeaturesForm')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>No Website Selected:</strong> Please select a website to configure director features.
        </div>
    @endif
</div>
