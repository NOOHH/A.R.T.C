<!-- Professor Features -->
<div id="professor-features" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-chalkboard-teacher me-2"></i>Professor Features
    </h3>

    @if(isset($selectedWebsite))
    <form id="professorFeaturesForm" action="{{ route('smartprep.dashboard.settings.update.professor-features', ['website' => $selectedWebsite->id]) }}" method="POST" onsubmit="updateProfessorFeatures(event)">
        @csrf
        <p class="text-muted small mb-3">Control which features are available to professors in their dashboard.</p>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="aiQuizEnabled" name="ai_quiz_enabled" {{ ($settings['professor_features']['ai_quiz_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="aiQuizEnabled">
                        <strong>AI Quiz Generator</strong><br>
                        <small class="text-muted">Allow professors to generate quizzes from documents</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="gradingEnabled" name="grading_enabled" {{ ($settings['professor_features']['grading_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="gradingEnabled">
                        <strong>Grading System</strong><br>
                        <small class="text-muted">Allow professors to grade assignments and quizzes</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="videoUploadEnabled" name="upload_videos_enabled" {{ ($settings['professor_features']['upload_videos_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="videoUploadEnabled">
                        <strong>Video Upload</strong><br>
                        <small class="text-muted">Allow professors to upload video links</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="attendanceEnabled" name="attendance_enabled" {{ ($settings['professor_features']['attendance_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="attendanceEnabled">
                        <strong>Attendance Management</strong><br>
                        <small class="text-muted">Allow professors to track student attendance</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="meetingCreationEnabled" name="meeting_creation_enabled" {{ ($settings['professor_features']['meeting_creation_enabled'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="meetingCreationEnabled">
                        <strong>Meeting Creation</strong><br>
                        <small class="text-muted">Allow professors to create and schedule meetings</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="professorModuleManagementEnabled" name="professor_module_management_enabled" {{ ($settings['professor_features']['professor_module_management_enabled'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="professorModuleManagementEnabled">
                        <strong>Module Management</strong><br>
                        <small class="text-muted">Allow professors to create and manage modules</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="professorAnnouncementManagementEnabled" name="professor_announcement_management_enabled" {{ ($settings['professor_features']['professor_announcement_management_enabled'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="professorAnnouncementManagementEnabled">
                        <strong>Announcement Management</strong><br>
                        <small class="text-muted">Allow professors to create and manage announcements</small>
                    </label>
                </div>
            </div>
            

        </div>

        <div class="settings-footer mt-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i>Save Professor Features
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm('professorFeaturesForm')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>No Website Selected:</strong> Please select a website to configure professor features.
        </div>
    @endif
</div>
