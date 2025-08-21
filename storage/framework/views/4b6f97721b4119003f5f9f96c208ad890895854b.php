<!-- Student Portal Settings -->
<div id="student-settings" class="sidebar-section">
    <div class="section-header">
        <h5><i class="fas fa-user-graduate me-2"></i>Student Portal</h5>
    </div>

    <form id="studentForm" method="POST" action="<?php echo e(route('smartprep.dashboard.settings.update.student', ['website' => $selectedWebsite->id])); ?>" onsubmit="updateStudent(event)">
        <?php echo csrf_field(); ?>

        <!-- Dashboard Colors -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Colors</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Dashboard Header Background</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['dashboard_header_bg'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="dashboard_header_bg" value="<?php echo e($settings['student_portal']['dashboard_header_bg'] ?? '#0d6efd'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Dashboard Header Text</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['dashboard_header_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="dashboard_header_text" value="<?php echo e($settings['student_portal']['dashboard_header_text'] ?? '#ffffff'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Sidebar Background</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['sidebar_bg'] ?? '#f8f9fa'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="sidebar_bg" value="<?php echo e($settings['student_portal']['sidebar_bg'] ?? '#f8f9fa'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Active Menu Item</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['active_menu_color'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="active_menu_color" value="<?php echo e($settings['student_portal']['active_menu_color'] ?? '#0d6efd'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Interface Colors -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-book me-2"></i>Course Interface Colors</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Course Card Background</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['course_card_bg'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="course_card_bg" value="<?php echo e($settings['student_portal']['course_card_bg'] ?? '#ffffff'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Course Progress Bar</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['progress_bar_color'] ?? '#28a745'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="progress_bar_color" value="<?php echo e($settings['student_portal']['progress_bar_color'] ?? '#28a745'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Course Title Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['course_title_color'] ?? '#212529'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="course_title_color" value="<?php echo e($settings['student_portal']['course_title_color'] ?? '#212529'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Assignment Due Date</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['due_date_color'] ?? '#dc3545'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="due_date_color" value="<?php echo e($settings['student_portal']['due_date_color'] ?? '#dc3545'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons and Interactive Elements -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-mouse-pointer me-2"></i>Buttons & Interactive Elements</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Button Background</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['primary_btn_bg'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="primary_btn_bg" value="<?php echo e($settings['student_portal']['primary_btn_bg'] ?? '#0d6efd'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Primary Button Text</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['primary_btn_text'] ?? '#ffffff'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="primary_btn_text" value="<?php echo e($settings['student_portal']['primary_btn_text'] ?? '#ffffff'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Secondary Button Background</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['secondary_btn_bg'] ?? '#6c757d'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="secondary_btn_bg" value="<?php echo e($settings['student_portal']['secondary_btn_bg'] ?? '#6c757d'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Link Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['link_color'] ?? '#0d6efd'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="link_color" value="<?php echo e($settings['student_portal']['link_color'] ?? '#0d6efd'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status and Notification Colors -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Status & Notification Colors</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Success Message</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['success_color'] ?? '#28a745'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="success_color" value="<?php echo e($settings['student_portal']['success_color'] ?? '#28a745'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Warning Message</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['warning_color'] ?? '#ffc107'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="warning_color" value="<?php echo e($settings['student_portal']['warning_color'] ?? '#ffc107'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Error Message</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['error_color'] ?? '#dc3545'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="error_color" value="<?php echo e($settings['student_portal']['error_color'] ?? '#dc3545'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Info Message</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="<?php echo e($settings['student_portal']['info_color'] ?? '#17a2b8'); ?>" onchange="this.nextElementSibling.value = this.value">
                                <input type="text" class="form-control" name="info_color" value="<?php echo e($settings['student_portal']['info_color'] ?? '#17a2b8'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Student Portal
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm('studentForm')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>

    <!-- Sidebar Customization (Student) -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-bars me-2"></i>Sidebar Customization</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Primary Color</label>
                    <div class="color-input-group">
                        <input type="color" id="student_sidebar_primary_color" class="form-control form-control-color" value="<?php echo e($settings['student_sidebar']['primary_color'] ?? '#3f4d69'); ?>" onchange="updateColorText('student_sidebar_primary_color', this.value)">
                        <input type="text" class="form-control form-control-sm" value="<?php echo e($settings['student_sidebar']['primary_color'] ?? '#3f4d69'); ?>" onchange="updateColorPicker('student_sidebar_primary_color', this.value)">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary Color</label>
                    <div class="color-input-group">
                        <input type="color" id="student_sidebar_secondary_color" class="form-control form-control-color" value="<?php echo e($settings['student_sidebar']['secondary_color'] ?? '#2d2d2d'); ?>" onchange="updateColorText('student_sidebar_secondary_color', this.value)">
                        <input type="text" class="form-control form-control-sm" value="<?php echo e($settings['student_sidebar']['secondary_color'] ?? '#2d2d2d'); ?>" onchange="updateColorPicker('student_sidebar_secondary_color', this.value)">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Accent Color</label>
                    <div class="color-input-group">
                        <input type="color" id="student_sidebar_accent_color" class="form-control form-control-color" value="<?php echo e($settings['student_sidebar']['accent_color'] ?? '#4f757d'); ?>" onchange="updateColorText('student_sidebar_accent_color', this.value)">
                        <input type="text" class="form-control form-control-sm" value="<?php echo e($settings['student_sidebar']['accent_color'] ?? '#4f757d'); ?>" onchange="updateColorPicker('student_sidebar_accent_color', this.value)">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Text Color</label>
                    <div class="color-input-group">
                        <input type="color" id="student_sidebar_text_color" class="form-control form-control-color" value="<?php echo e($settings['student_sidebar']['text_color'] ?? '#e0e0e0'); ?>" onchange="updateColorText('student_sidebar_text_color', this.value)">
                        <input type="text" class="form-control form-control-sm" value="<?php echo e($settings['student_sidebar']['text_color'] ?? '#e0e0e0'); ?>" onchange="updateColorPicker('student_sidebar_text_color', this.value)">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hover Color</label>
                    <div class="color-input-group">
                        <input type="color" id="student_sidebar_hover_color" class="form-control form-control-color" value="<?php echo e($settings['student_sidebar']['hover_color'] ?? '#374151'); ?>" onchange="updateColorText('student_sidebar_hover_color', this.value)">
                        <input type="text" class="form-control form-control-sm" value="<?php echo e($settings['student_sidebar']['hover_color'] ?? '#374151'); ?>" onchange="updateColorPicker('student_sidebar_hover_color', this.value)">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sidebar Width</label>
                    <input type="number" id="student_sidebar_width" class="form-control" placeholder="280" min="200" max="400" value="<?php echo e($settings['student_sidebar']['width'] ?? '280'); ?>">
                    <small class="form-text text-muted">Width in pixels (200-400px)</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Collapsed Width</label>
                    <input type="number" id="student_sidebar_collapsed_width" class="form-control" placeholder="70" min="50" max="100" value="<?php echo e($settings['student_sidebar']['collapsed_width'] ?? '70'); ?>">
                    <small class="form-text text-muted">Width when collapsed (50-100px)</small>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSidebarColors('student')"><i class="fas fa-save me-2"></i>Save Sidebar Colors</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetStudentSidebarColors()"><i class="fas fa-undo me-2"></i>Reset</button>
            </div>
        </div>
    </div>

    <style>
    .color-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .color-input-group .form-control-color {
        width: 50px;
        height: 38px;
        padding: 0;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    
    .color-input-group .form-control-sm {
        flex: 1;
        font-family: monospace;
        font-size: 0.875rem;
    }
    </style>

    <script>
    // Color input synchronization functions
    function updateColorText(colorId, value) {
        const textInput = document.querySelector(`input[type="text"][onchange*="${colorId}"]`);
        if (textInput) {
            textInput.value = value;
        }
    }

    function updateColorPicker(colorId, value) {
        // Validate hex color format
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            const colorInput = document.getElementById(colorId);
            if (colorInput) {
                colorInput.value = value;
            }
        } else {
            // Show error for invalid color format
            alert('Please enter a valid hex color (e.g., #ff0000)');
        }
    }
    </script>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/student-portal.blade.php ENDPATH**/ ?>