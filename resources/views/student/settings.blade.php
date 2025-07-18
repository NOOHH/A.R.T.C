@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Settings')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* Mobile-First Responsive Design */
    .settings-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .settings-card h3 {
        color: #6f42c1;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6f42c1;
        box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
    }
    
    /* Specific sizing for different field types */
    input[type="text"], 
    input[type="email"], 
    input[type="tel"], 
    input[type="date"],
    input[type="number"] {
        height: 48px;
        max-width: 400px; /* Limit width for better UX */
    }
    
    /* Override for full-width fields */
    .full-width input,
    textarea.form-control {
        max-width: 100%;
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    
    select.form-control {
        height: 48px;
        max-width: 400px;
    }
    
    /* File input specific styling */
    input[type="file"].form-control {
        padding: 0.5rem;
        height: auto;
        min-height: 48px;
        max-width: 100%;
    }
    
    /* Email field styling */
    .email-field-wrapper {
        position: relative;
        max-width: 400px;
    }
    
    .email-field-wrapper input {
        padding-right: 80px; /* Space for button */
    }
    
    .email-change-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        padding: 4px 8px;
        font-size: 12px;
        background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
        border: none;
        border-radius: 4px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .email-change-btn:hover {
        background: linear-gradient(135deg, #5a359a 0%, #7a3a93 100%);
        transform: translateY(-50%) scale(1.05);
    }
    
    /* Mobile responsiveness for email field */
    @media (max-width: 767px) {
        .email-field-wrapper {
            max-width: 100%;
        }
        
        .email-change-btn {
            position: static;
            transform: none;
            margin-top: 8px;
            width: 100%;
            padding: 8px 12px;
        }
        
        .email-field-wrapper input {
            padding-right: 12px; /* Reset padding on mobile */
        }
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6f42c1, #8e44ad);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-danger {
        background: #dc3545;
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .readonly {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: -0.5rem;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        padding: 0.5rem;
    }
    
    .file-upload-section {
        margin: 1rem 0;
        padding: 1rem;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .file-upload-section:hover {
        border-color: #6f42c1;
        background-color: #f8f9fa;
    }
    
    .file-preview {
        margin-top: 0.5rem;
    }
    
    .file-preview a {
        color: #6f42c1;
        text-decoration: none;
        font-weight: 600;
    }
    
    /* ==== TABLET DEVICES (768px - 991px) ==== */
    @media (min-width: 768px) {
        .settings-container {
            padding: 2rem;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
        }
        
        .settings-card {
            padding: 2rem;
        }
        
        /* Better sizing for tablet */
        input[type="text"], 
        input[type="email"], 
        input[type="tel"], 
        input[type="date"],
        input[type="number"],
        select.form-control {
            max-width: 350px;
        }
        
        .email-field-wrapper {
            max-width: 350px;
        }
    }
    
    /* ==== LAPTOP DEVICES (992px - 1199px) ==== */
    @media (min-width: 992px) {
        .settings-container {
            padding: 2.5rem;
        }
        
        .form-control {
            font-size: 1.1rem;
        }
    }
    
    /* ==== PC/DESKTOP DEVICES (1200px+) ==== */
    @media (min-width: 1200px) {
        .settings-container {
            padding: 3rem;
        }
        
        .settings-card {
            padding: 2.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="settings-container">
    <div class="settings-card">
        <h3>Personal Information</h3>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form action="{{ route('student.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Core Required Fields -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_firstname">First Name *</label>
                        <input type="text" 
                               class="form-control" 
                               id="user_firstname" 
                               name="user_firstname" 
                               value="{{ old('user_firstname', $user->user_firstname ?? $student->firstname ?? '') }}" 
                               required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="middlename">Middle Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="middlename" 
                               name="middlename" 
                               value="{{ old('middlename', $student->middlename ?? '') }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_lastname">Last Name *</label>
                        <input type="text" 
                               class="form-control" 
                               id="user_lastname" 
                               name="user_lastname" 
                               value="{{ old('user_lastname', $user->user_lastname ?? $student->lastname ?? '') }}" 
                               required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_email">Email Address *</label>
                        <div class="email-field-wrapper">
                            <input type="email" 
                                   class="form-control" 
                                   id="user_email" 
                                   name="user_email" 
                                   value="{{ old('user_email', $user->email ?? $student->email ?? '') }}" 
                                   readonly>
                            <button type="button" class="email-change-btn" onclick="showChangeEmailModal()">
                                <i class="bi bi-pencil"></i> Change
                            </button>
                        </div>
                        <small class="text-muted">Click "Change" to update your email address</small>
                    </div>
                </div>
            </div>

            <!-- Dynamic Fields Based on Form Requirements -->
            @if(isset($formRequirements) && $formRequirements->count() > 0)
                @foreach($formRequirements as $requirement)
                    @php
                        // Skip core fields that are already displayed above
                        $coreFields = ['user_firstname', 'user_lastname', 'user_email', 'firstname', 'lastname', 'email'];
                        $shouldSkip = in_array($requirement->field_name, $coreFields) || 
                                     in_array(strtolower($requirement->field_name), ['firstname', 'lastname', 'email']) ||
                                     (strtolower($requirement->field_name) === 'first_name') ||
                                     (strtolower($requirement->field_name) === 'last_name');
                    @endphp
                    
                    @if(!$shouldSkip)
                        <div class="form-group">
                            <label for="{{ $requirement->field_name }}">
                                {{ $requirement->field_label }}
                                @if($requirement->is_required) * @endif
                            </label>
                            
                            @if($requirement->field_type == 'text')
                                <input type="text" 
                                       class="form-control" 
                                       id="{{ $requirement->field_name }}" 
                                       name="{{ $requirement->field_name }}" 
                                       value="{{ old($requirement->field_name, $student->{$requirement->field_name} ?? '') }}"
                                       @if($requirement->is_required) required @endif>
                            
                            @elseif($requirement->field_type == 'email')
                                <input type="email" 
                                       class="form-control" 
                                       id="{{ $requirement->field_name }}" 
                                       name="{{ $requirement->field_name }}" 
                                       value="{{ old($requirement->field_name, $student->{$requirement->field_name} ?? '') }}"
                                       @if($requirement->is_required) required @endif>
                            
                            @elseif($requirement->field_type == 'file')
                                <div class="file-upload-section">
                                    <input type="file" 
                                           class="form-control" 
                                           id="{{ $requirement->field_name }}" 
                                           name="{{ $requirement->field_name }}" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           @if($requirement->is_required && !isset($student->{$requirement->field_name})) required @endif>
                                    
                                    @if(isset($student->{$requirement->field_name}) && $student->{$requirement->field_name})
                                        <div class="file-preview">
                                            <small class="text-success">✓ File uploaded</small>
                                            <a href="#" onclick="viewFile('{{ $requirement->field_name }}', '{{ $student->{$requirement->field_name} }}')">
                                                <i class="bi bi-eye me-1"></i>View File
                                            </a>
                                        </div>
                                    @else
                                        <small class="text-muted">No file uploaded yet</small>
                                    @endif
                                </div>
                            
                            @elseif($requirement->field_type == 'select')
                                <select class="form-control" 
                                        id="{{ $requirement->field_name }}" 
                                        name="{{ $requirement->field_name }}"
                                        @if($requirement->is_required) required @endif>
                                    <option value="">Select {{ $requirement->field_label }}</option>
                                    @if($requirement->field_options)
                                        @foreach($requirement->field_options as $option)
                                            <option value="{{ $option }}" 
                                                    {{ old($requirement->field_name, $student->{$requirement->field_name} ?? '') == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            
                            @else
                                <input type="{{ $requirement->field_type }}" 
                                       class="form-control" 
                                       id="{{ $requirement->field_name }}" 
                                       name="{{ $requirement->field_name }}" 
                                       value="{{ old($requirement->field_name, $student->{$requirement->field_name} ?? '') }}"
                                       @if($requirement->is_required) required @endif>
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif
            
            <!-- Core Address Fields -->
            <div class="form-group full-width">
                <label for="street_address">Street Address</label>
                <input type="text" 
                       class="form-control" 
                       id="street_address" 
                       name="street_address" 
                       value="{{ old('street_address', $student->street_address ?? '') }}">
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="state_province">State/Province</label>
                        <input type="text" 
                               class="form-control" 
                               id="state_province" 
                               name="state_province" 
                               value="{{ old('state_province', $student->state_province ?? '') }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" 
                               class="form-control" 
                               id="city" 
                               name="city" 
                               value="{{ old('city', $student->city ?? '') }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="zipcode">Zip Code</label>
                        <input type="text" 
                               class="form-control" 
                               id="zipcode" 
                               name="zipcode" 
                               value="{{ old('zipcode', $student->zipcode ?? '') }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" 
                               class="form-control" 
                               id="contact_number" 
                               name="contact_number" 
                               value="{{ old('contact_number', $student->contact_number ?? '') }}">
                    </div>
                </div>
            </div>
            
            <!-- Read-only fields -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="student_id">Student ID (Read Only)</label>
                        <input type="text" 
                               class="form-control readonly" 
                               id="student_id" 
                               value="{{ $student->student_id ?? 'Not applicable' }}" 
                               readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="program_name">Enrolled Program</label>
                        <input type="text" 
                               class="form-control readonly" 
                               id="program_name" 
                               value="{{ $student->program_name ?? 'Not enrolled yet' }}" 
                               readonly>
                    </div>
                </div>
            </div>
            
            <div class="form-group d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check me-1"></i>Update Information
                    </button>
                </div>
                <div>
                    <a href="#" class="btn-secondary me-2" onclick="showChangePasswordModal()">
                        <i class="bi bi-key me-1"></i>Change Password
                    </a>
                    <a href="#" class="btn-danger" onclick="showForgotPasswordModal()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset Password
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Change Email Modal -->
<div class="modal fade" id="changeEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Email Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="emailStep1">
                    <p class="text-muted">Enter your new email address. We'll send a verification code to confirm the change.</p>
                    <div class="form-group">
                        <label for="new_email">New Email Address</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                        <div class="form-text">An OTP will be sent to this email address for verification.</div>
                    </div>
                </div>
                
                <div id="emailStep2" style="display: none;">
                    <p class="text-success">✓ Verification code sent!</p>
                    <p class="text-muted">Please check your new email and enter the 6-digit code below:</p>
                    <div class="form-group">
                        <label for="email_otp">Verification Code</label>
                        <input type="text" class="form-control" id="email_otp" name="email_otp" maxlength="6" placeholder="000000">
                    </div>
                    <div id="otpTimer" class="text-muted small"></div>
                </div>
                
                <div id="emailError" class="alert alert-danger" style="display: none;"></div>
                <div id="emailSuccess" class="alert alert-success" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendEmailOTP" onclick="sendEmailOTP()">Send Code</button>
                <button type="button" class="btn btn-success" id="verifyEmailOTP" onclick="verifyEmailOTP()" style="display: none;">Verify & Change</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePasswordForm" action="{{ route('student.change-password') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    
                    @if(env('RECAPTCHA_SITE_KEY'))
                    <!-- reCAPTCHA -->
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" action="{{ route('student.reset-password') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">A new random password will be generated and sent to your email address.</p>
                    <div class="form-group">
                        <label for="email_confirm">Confirm Your Email</label>
                        <input type="email" class="form-control" id="email_confirm" name="email" value="{{ $student->email ?? '' }}" readonly>
                    </div>
                    
                    <!-- reCAPTCHA -->
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- File Viewer Modal -->
<div class="modal fade" id="fileViewerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileViewerTitle">File Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="fileViewerContent"></div>
            </div>
        </div>
    </div>
</div>

@if(env('RECAPTCHA_SITE_KEY'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<script>
function showChangeEmailModal() {
    // Reset modal state
    document.getElementById('emailStep1').style.display = 'block';
    document.getElementById('emailStep2').style.display = 'none';
    document.getElementById('sendEmailOTP').style.display = 'inline-block';
    document.getElementById('verifyEmailOTP').style.display = 'none';
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('emailSuccess').style.display = 'none';
    
    // Clear form fields
    document.getElementById('new_email').value = '';
    document.getElementById('email_otp').value = '';
    
    new bootstrap.Modal(document.getElementById('changeEmailModal')).show();
}

function showChangePasswordModal() {
    new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
}

function showForgotPasswordModal() {
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

function sendEmailOTP() {
    const newEmail = document.getElementById('new_email').value;
    const errorDiv = document.getElementById('emailError');
    
    // Validate email
    if (!newEmail) {
        showEmailError('Please enter an email address.');
        return;
    }
    
    if (!isValidEmail(newEmail)) {
        showEmailError('Please enter a valid email address.');
        return;
    }
    
    // Send OTP request
    fetch('{{ route("student.send-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: newEmail })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show step 2
            document.getElementById('emailStep1').style.display = 'none';
            document.getElementById('emailStep2').style.display = 'block';
            document.getElementById('sendEmailOTP').style.display = 'none';
            document.getElementById('verifyEmailOTP').style.display = 'inline-block';
            document.getElementById('emailError').style.display = 'none';
            
            // Start countdown timer
            startOTPTimer();
        } else {
            showEmailError(data.message || 'Error sending verification code. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showEmailError('Error sending verification code. Please try again.');
    });
}

function verifyEmailOTP() {
    const newEmail = document.getElementById('new_email').value;
    const otp = document.getElementById('email_otp').value;
    
    if (!otp || otp.length !== 6) {
        showEmailError('Please enter the 6-digit verification code.');
        return;
    }
    
    fetch('{{ route("student.verify-email-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            otp: otp,
            new_email: newEmail 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.getElementById('emailSuccess').textContent = data.message;
            document.getElementById('emailSuccess').style.display = 'block';
            document.getElementById('emailError').style.display = 'none';
            
            // Update the email field in the main form
            document.getElementById('user_email').value = newEmail;
            
            // Close modal after 2 seconds
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('changeEmailModal')).hide();
                location.reload(); // Reload to update session data
            }, 2000);
        } else {
            showEmailError(data.message || 'Invalid verification code. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showEmailError('Error verifying code. Please try again.');
    });
}

function showEmailError(message) {
    const errorDiv = document.getElementById('emailError');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    document.getElementById('emailSuccess').style.display = 'none';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function startOTPTimer() {
    let timeLeft = 600; // 10 minutes in seconds
    const timerDiv = document.getElementById('otpTimer');
    
    const timer = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDiv.textContent = `Code expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        timeLeft--;
        
        if (timeLeft < 0) {
            clearInterval(timer);
            timerDiv.textContent = 'Code has expired. Please request a new one.';
        }
    }, 1000);
}

function sendOTPForEmailChange() {
    const email = document.getElementById('user_email').value;
    if (!email) {
        alert('Please enter an email address first.');
        return;
    }
    
    // This function is kept for backward compatibility but redirects to modal
    showChangeEmailModal();
    document.getElementById('new_email').value = email;
}

function viewFile(fieldName, fileName) {
    const modal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
    document.getElementById('fileViewerTitle').textContent = fieldName.replace('_', ' ').toUpperCase();
    
    // Create file preview based on file type
    const fileExtension = fileName.split('.').pop().toLowerCase();
    const fileUrl = '/storage/student-documents/' + fileName;
    
    let content = '';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        content = `<img src="${fileUrl}" class="img-fluid" alt="${fieldName}">`;
    } else if (fileExtension === 'pdf') {
        content = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="400px">`;
    } else {
        content = `<p>File type not supported for preview. <a href="${fileUrl}" target="_blank">Download file</a></p>`;
    }
    
    document.getElementById('fileViewerContent').innerHTML = content;
    modal.show();
}
</script>
@endsection
