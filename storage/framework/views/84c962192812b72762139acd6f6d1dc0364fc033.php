<!-- Auth (Login/Register) Settings -->
<div class="sidebar-section" id="auth-settings" style="display: none;">
    <div class="section-header">
        <h5><i class="fas fa-sign-in-alt me-2"></i>Authentication & Registration</h5>
    </div>
    
    <!-- Login Customization Section -->
    <form id="loginForm" method="POST" action="<?php echo e(route('smartprep.dashboard.settings.update.auth', ['website' => $selectedWebsite->id])); ?>" enctype="multipart/form-data" onsubmit="updateAuth(event)">
        <?php echo csrf_field(); ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>LOGIN CUSTOMIZATION</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">Login Page Title</label>
                    <input type="text" class="form-control" name="login_title" value="<?php echo e($settings['auth']['login_title'] ?? 'Welcome Back'); ?>" placeholder="Login page main title">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Login Page Subtitle</label>
                    <input type="text" class="form-control" name="login_subtitle" value="<?php echo e($settings['auth']['login_subtitle'] ?? 'Sign in to your account to continue'); ?>" placeholder="Login page subtitle">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Login Button Text</label>
                    <input type="text" class="form-control" name="login_button_text" value="<?php echo e($settings['auth']['login_button_text'] ?? 'Sign In'); ?>" placeholder="Login button text">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Background Color (Top of Gradient)</label>
                            <small class="form-text text-muted">Main background color for the left panel</small>
                            <input type="color" class="form-control form-control-color" name="login_bg_top_color" value="<?php echo e($settings['auth']['login_bg_top_color'] ?? '#667eea'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Gradient Color (Bottom of Gradient)</label>
                            <small class="form-text text-muted">Bottom color for the gradient background</small>
                            <input type="color" class="form-control form-control-color" name="login_bg_bottom_color" value="<?php echo e($settings['auth']['login_bg_bottom_color'] ?? '#764ba2'); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Accent Color (Button Color)</label>
                            <small class="form-text text-muted">Color for login button and accent elements</small>
                            <input type="color" class="form-control form-control-color" name="login_accent_color" value="<?php echo e($settings['auth']['login_accent_color'] ?? '#007bff'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Text Color</label>
                            <small class="form-text text-muted">Color for text in the right panel</small>
                            <input type="color" class="form-control form-control-color" name="login_text_color" value="<?php echo e($settings['auth']['login_text_color'] ?? '#495057'); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Card Background</label>
                            <small class="form-text text-muted">Background color for the login form card</small>
                            <input type="color" class="form-control form-control-color" name="login_card_bg" value="<?php echo e($settings['auth']['login_card_bg'] ?? '#ffffff'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Input Border Color</label>
                            <small class="form-text text-muted">Border color for input fields</small>
                            <input type="color" class="form-control form-control-color" name="login_input_border" value="<?php echo e($settings['auth']['login_input_border'] ?? '#ced4da'); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Input Focus Color</label>
                    <small class="form-text text-muted">Border color when input fields are focused</small>
                    <input type="color" class="form-control form-control-color" name="login_input_focus" value="<?php echo e($settings['auth']['login_input_focus'] ?? '#80bdff'); ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Login Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Registration Form Fields Section -->
    <form id="registrationForm" method="POST" action="<?php echo e(route('smartprep.dashboard.settings.update.registration', ['website' => $selectedWebsite->id])); ?>" enctype="multipart/form-data" onsubmit="updateRegistration(event)">
        <?php echo csrf_field(); ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Registration Form Fields</h6>
                <small class="text-muted">Manage Form Fields</small>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Add fields, sections, and manage what students fill out during registration</p>
                
                <!-- System/Predefined Fields -->
                <h6 class="text-primary mb-3">System/Predefined Fields (Cannot be deleted, only toggled)</h6>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Field Name</th>
                                <th>Field Label</th>
                                <th>Type</th>
                                <th>Options</th>
                                <th>Active</th>
                                <th>Required</th>
                                <th>Program</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>firstname</code></td>
                                <td>First Name</td>
                                <td><span class="badge bg-secondary">text</span></td>
                                <td><em>No options</em></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="system_fields[firstname][active]" checked disabled>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="system_fields[firstname][required]" checked disabled>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Both</span></td>
                            </tr>
                            <tr>
                                <td><code>lastname</code></td>
                                <td>Last Name</td>
                                <td><span class="badge bg-secondary">text</span></td>
                                <td><em>No options</em></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="system_fields[lastname][active]" checked disabled>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="system_fields[lastname][required]" checked disabled>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Both</span></td>
                            </tr>
                            <tr>
                                <td><code>education_level</code></td>
                                <td>Education Level</td>
                                <td><span class="badge bg-primary">select</span></td>
                                <td>High School<br>Undergraduate<br>Graduate</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="system_fields[education_level][active]" <?php echo e(($settings['auth']['system_fields']['education_level']['active'] ?? true) ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="system_fields[education_level][required]" <?php echo e(($settings['auth']['system_fields']['education_level']['required'] ?? true) ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Both</span></td>
                            </tr>
                            <tr>
                                <td><code>program_id</code></td>
                                <td>Program</td>
                                <td><span class="badge bg-primary">select</span></td>
                                <td><em>Dynamic - Loaded from Programs</em></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="system_fields[program_id][active]" <?php echo e(($settings['auth']['system_fields']['program_id']['active'] ?? true) ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="system_fields[program_id][required]" <?php echo e(($settings['auth']['system_fields']['program_id']['required'] ?? true) ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Both</span></td>
                            </tr>
                            <tr>
                                <td><code>start_date</code></td>
                                <td>Start Date</td>
                                <td><span class="badge bg-warning">date</span></td>
                                <td><em>No options</em></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="system_fields[start_date][active]" <?php echo e(($settings['auth']['system_fields']['start_date']['active'] ?? true) ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="system_fields[start_date][required]" <?php echo e(($settings['auth']['system_fields']['start_date']['required'] ?? true) ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Both</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Custom Fields Section -->
                <hr class="my-4">
                <h6 class="text-success mb-3">Custom Fields</h6>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Section Name</label>
                            <input type="text" class="form-control" name="custom_section_name" value="<?php echo e($settings['auth']['custom_section_name'] ?? 'Personal Information'); ?>" placeholder="Personal Information">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Display Label</label>
                            <input type="text" class="form-control" name="custom_field_label" value="<?php echo e($settings['auth']['custom_field_label'] ?? 'Birthday'); ?>" placeholder="Birthday">
                            <small class="text-muted">Field name will be auto-generated</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">Field Type</label>
                            <select class="form-select" name="custom_field_type">
                                <option value="text" <?php echo e(($settings['auth']['custom_field_type'] ?? 'text') == 'text' ? 'selected' : ''); ?>>Text</option>
                                <option value="email" <?php echo e(($settings['auth']['custom_field_type'] ?? '') == 'email' ? 'selected' : ''); ?>>Email</option>
                                <option value="date" <?php echo e(($settings['auth']['custom_field_type'] ?? '') == 'date' ? 'selected' : ''); ?>>Date</option>
                                <option value="select" <?php echo e(($settings['auth']['custom_field_type'] ?? '') == 'select' ? 'selected' : ''); ?>>Select</option>
                                <option value="textarea" <?php echo e(($settings['auth']['custom_field_type'] ?? '') == 'textarea' ? 'selected' : ''); ?>>Textarea</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">Program</label>
                            <select class="form-select" name="custom_field_program">
                                <option value="both" <?php echo e(($settings['auth']['custom_field_program'] ?? 'both') == 'both' ? 'selected' : ''); ?>>Both</option>
                                <option value="complete" <?php echo e(($settings['auth']['custom_field_program'] ?? '') == 'complete' ? 'selected' : ''); ?>>Complete Plan</option>
                                <option value="modular" <?php echo e(($settings['auth']['custom_field_program'] ?? '') == 'modular' ? 'selected' : ''); ?>>Modular Plan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">Field Options</label>
                            <div class="d-flex gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_field_required" id="customFieldRequired" <?php echo e(($settings['auth']['custom_field_required'] ?? false) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="customFieldRequired">Required</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="custom_field_active" id="customFieldActive" <?php echo e(($settings['auth']['custom_field_active'] ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="customFieldActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add Custom Field Button -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button type="button" class="btn btn-outline-success" onclick="addCustomField()">
                            <i class="fas fa-plus me-2"></i>Add Custom Field
                        </button>
                    </div>
                    <div>
                        <small class="text-muted">Configure custom fields for registration form</small>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Registration Page Title</label>
                    <input type="text" class="form-control" name="register_title" value="<?php echo e($settings['auth']['register_title'] ?? 'Create Account'); ?>" placeholder="Registration page main title">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Registration Page Subtitle</label>
                    <input type="text" class="form-control" name="register_subtitle" value="<?php echo e($settings['auth']['register_subtitle'] ?? 'Join us to start your learning journey'); ?>" placeholder="Registration page subtitle">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Register Button Text</label>
                    <input type="text" class="form-control" name="register_button_text" value="<?php echo e($settings['auth']['register_button_text'] ?? 'Create Account'); ?>" placeholder="Register button text">
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Registration Enabled</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="registration_enabled" id="registrationEnabled" <?php echo e(($settings['auth']['registration_enabled'] ?? true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="registrationEnabled">
                            Allow new user registrations
                        </label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Update Registration Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/auth.blade.php ENDPATH**/ ?>