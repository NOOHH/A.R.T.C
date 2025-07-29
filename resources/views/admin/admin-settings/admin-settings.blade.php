@extends('admin.admin-dashboard-layout')

@section('title', 'Settings')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/admin-settings/admin-settings.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style>
/* Styles for inactive form fields */
.requirement-inactive {
    border-left: 4px solid #ffc107 !important;
    transition: all 0.3s ease;
}

.requirement-inactive:hover {
    opacity: 0.8 !important;
}

/* Make warning alerts more compact */
.requirement-item .alert-warning {
    font-size: 0.85rem;
    border-radius: 6px;
    border: 1px solid #ffc107;
    background-color: #fff3cd;
    color: #856404;
}

/* Visual separation for inactive fields */
.requirement-inactive .form-control,
.requirement-inactive .form-select {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

/* Highlight active switch for inactive fields */
.requirement-inactive .form-check-input {
    border-color: #ffc107;
}

.requirement-inactive .form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

/* Drag and drop styling */
.dragging-placeholder {
    opacity: 0.5;
    background-color: #e9ecef;
    border: 2px dashed #adb5bd;
}

/* Unsaved changes indicator */

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}
</style>
@endpush

@section('content')
<div class="settings-container">
        <div class="settings-header text-center mb-5">
            <h1 class="display-4 fw-bold text-dark mb-0">
                <i class="fas fa-cog me-3"></i>Settings
            </h1>
        </div>
        
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Settings Tabs --}}
        <div class="settings-tabs">
            <ul class="nav nav-tabs justify-content-center" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">
                        <i class="fas fa-user-graduate me-2"></i>Student
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="professor-tab" data-bs-toggle="tab" data-bs-target="#professor" type="button" role="tab">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Professor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="director-tab" data-bs-toggle="tab" data-bs-target="#director" type="button" role="tab">
                        <i class="fas fa-user-tie me-2"></i>Director
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                        <i class="fas fa-user-shield me-2"></i>Admin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sidebar-tab" data-bs-toggle="tab" data-bs-target="#sidebar" type="button" role="tab">
                        <i class="fas fa-bars me-2"></i>Sidebar
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="plans-tab" data-bs-toggle="tab" data-bs-target="#plans" type="button" role="tab">
                        <i class="fas fa-graduation-cap me-2"></i>Plans
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="education-levels-tab" data-bs-toggle="tab" data-bs-target="#education-levels" type="button" role="tab">
                        <i class="fas fa-user-graduate me-2"></i>Education Levels
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payment-methods-tab" data-bs-toggle="tab" data-bs-target="#payment-methods" type="button" role="tab">
                        <i class="fas fa-credit-card me-2"></i>Payment Methods
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tab Content --}}
        <div class="tab-content" id="settingsTabContent">
            {{-- Student Tab --}}
            <div class="tab-pane fade show active" id="student" role="tabpanel">
                {{-- Student Sub-tabs --}}
                <ul class="nav nav-pills justify-content-center mb-4" id="studentSubTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">Home</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">Dashboard</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab">Activities</button>
                    </li>
                </ul>
                {{-- Student Sub-tab Content --}}
                <div class="tab-content" id="studentSubTabContent">
                    {{-- Login Tab --}}
                    <div class="tab-pane fade show active" id="login" role="tabpanel">

                    <div class="row g-4 mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-certificate me-2"></i>Certificate Management
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">Generate and download certificates for students based on their enrollment and completion status.</p>
                    <a href="{{ route('certificate.show') }}" class="btn btn-success">
                        <i class="fas fa-file-download me-2"></i>Generate Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">LOGIN CUSTOMIZATION</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">COLOR</label>
                                            <input type="color" class="form-control form-control-color" value="#007bff">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">COLOR</label>
                                            <input type="color" class="form-control form-control-color" value="#6c757d">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="bi bi-share me-2"></i>REFERRAL SYSTEM</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="referralSettingsForm">
                                            @csrf
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="referralEnabled" name="referral_enabled" 
                                                           {{ DB::table('admin_settings')->where('setting_key', 'referral_enabled')->value('setting_value') === '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="referralEnabled">
                                                        <strong>Enable Referral Code Field</strong>
                                                    </label>
                                                </div>
                                                <div class="form-text">Show referral code field in registration forms</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="referralRequired" name="referral_required"
                                                           {{ DB::table('admin_settings')->where('setting_key', 'referral_required')->value('setting_value') === '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="referralRequired">
                                                        <strong>Make Referral Code Required</strong>
                                                    </label>
                                                </div>
                                                <div class="form-text">Students must enter a valid referral code to register</div>
                                            </div>
                                            
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-info">
                                                    <i class="bi bi-save me-2"></i>Save Referral Settings
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <hr class="my-3">
                                        
                                        <div class="mb-3">
                                            <h6 class="text-secondary">Referral Code Format</h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Professor:</strong> PROF01JDOE (PROF + ID + Name Initials)<br>
                                                <strong>Director:</strong> DIR01JSMITH (DIR + ID + Name Initials)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Register Tab --}}
                    <div class="tab-pane fade" id="register" role="tabpanel">
                        <div class="row g-4">
                            {{-- Terms and Conditions Configuration --}}
                            <div class="col-md-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-file-contract me-2"></i>Terms and Conditions Configuration
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-4">Configure the terms and conditions text that appears in enrollment forms. You can set different texts for Full Enrollment and Modular Enrollment.</p>
                                        
                                        <form id="termsAndConditionsForm">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="fullEnrollmentTerms" class="form-label">
                                                            <strong>Full Enrollment Terms & Conditions</strong>
                                                        </label>
                                                        <textarea class="form-control" id="fullEnrollmentTerms" name="full_enrollment_terms" rows="12" 
                                                                  placeholder="Enter terms and conditions for full enrollment...">{{ \App\Models\AdminSetting::getValue('full_enrollment_terms', 'Terms and Conditions for Full Enrollment:

1. Enrollment Agreement
By enrolling in this full program, you agree to follow all institutional policies and procedures.

2. Payment Terms
All fees must be paid according to the schedule provided. Late payments may result in suspension of access to course materials.

3. Academic Integrity
Students are expected to maintain the highest standards of academic honesty and integrity.

4. Program Completion
Students must complete all required modules and assessments to receive certification.

5. Refund Policy
Refunds are available according to the institutional refund policy. Please contact administration for details.

6. Data Privacy
Your personal information will be handled according to our privacy policy and applicable data protection laws.

7. Program Duration
The program duration is as specified in your enrollment agreement. Extensions may be available upon request.

By proceeding with enrollment, you acknowledge that you have read, understood, and agree to these terms and conditions.') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="modularEnrollmentTerms" class="form-label">
                                                            <strong>Modular Enrollment Terms & Conditions</strong>
                                                        </label>
                                                        <textarea class="form-control" id="modularEnrollmentTerms" name="modular_enrollment_terms" rows="12" 
                                                                  placeholder="Enter terms and conditions for modular enrollment...">{{ \App\Models\AdminSetting::getValue('modular_enrollment_terms', 'Terms and Conditions for Modular Enrollment:

1. Module-Based Learning
You are enrolling in selected modules of our program. Each module is a standalone unit with its own requirements.

2. Flexible Schedule
Modular enrollment allows you to complete modules at your own pace within the specified timeframes.

3. Payment Terms
Payment is required per module or package selected. Payment plans may be available for multiple modules.

4. Module Completion
You must complete all activities and assessments within each enrolled module to receive certification.

5. Prerequisites
Some modules may have prerequisites. Please ensure you meet all requirements before enrolling.

6. Module Access
Access to module materials is granted upon payment confirmation and remains active for the duration specified.

7. Certification
Certificates are awarded upon successful completion of each module. Full program certification requires completion of all core modules.

8. Refund Policy
Module-specific refund policies apply. Please review the refund terms for each module before enrollment.

By proceeding with modular enrollment, you acknowledge that you have read, understood, and agree to these terms and conditions.') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="requireTermsAcceptance" name="require_terms_acceptance" 
                                                           {{ \App\Models\AdminSetting::getValue('require_terms_acceptance', '1') === '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="requireTermsAcceptance">
                                                        <strong>Require students to accept terms before enrollment</strong>
                                                    </label>
                                                </div>
                                                <button type="submit" class="btn btn-info">
                                                    <i class="fas fa-save me-2"></i>Save Terms & Conditions
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Registration Form Fields --}}
                            <div class="col-md-16">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-wpforms me-2"></i>Registration Form Fields
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <h6 class="text-primary">Manage Form Fields</h6>
                                            <p class="text-muted small">Add fields, sections, and manage what students fill out during registration</p>
                                        </div>

                                        <form id="studentRequirementsForm">
                                            @csrf
                                            <div id="requirementsContainer">
                                                <div class="text-center py-4">
                                                    <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                                    <p class="text-muted">Loading form requirements...</p>
                                                    <small class="text-muted">If this persists, please check your login status and try refreshing the page.</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary mb-3" id="addRequirement">
                                                <i class="fas fa-plus"></i> Add Field/Section
                                            </button>
                                            <div class="d-grid">
                                                <button type="submit" id="saveRequirementsBtn" class="btn btn-success">
                                                    <i class="fas fa-save"></i> Save Form Fields
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="text-secondary">Quick Actions</h6>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('enrollment.full') }}" class="btn btn-outline-info btn-sm" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i> Preview Full Form
                                                </a>
                                                <a href="{{ route('enrollment.modular') }}" class="btn btn-outline-info btn-sm" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i> Preview Modular Form
                                                </a>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="previewForm('full')">
                                                    <i class="fas fa-eye"></i> Preview Full Form
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="previewForm('modular')">
                                                    <i class="fas fa-eye"></i> Preview Modular Form
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <h6 class="text-secondary">Advanced Options</h6>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="addDynamicColumn()">
                                                    <i class="fas fa-database"></i> Add DB Column
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="showFieldManagementHelp()">
                                                    <i class="fas fa-question-circle"></i> Help
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Home Tab --}}
                    <div class="tab-pane fade" id="home" role="tabpanel">
                        <div class="row g-4">
                            {{-- Homepage Hero Section --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-home me-2"></i>Hero Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="heroSectionForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Background Color</label>
                                                <input type="color" class="form-control form-control-color" name="hero_bg_color" value="#667eea">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Text Color</label>
                                                <input type="color" class="form-control form-control-color" name="hero_text_color" value="#ffffff">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Main Title</label>
                                                <textarea class="form-control" name="hero_title" rows="3">Review Smarter. Learn Better. Succeed Faster.</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Subtitle</label>
                                                <textarea class="form-control" name="hero_subtitle" rows="3">At Ascendo Review and Training Center, we guide future licensed professionals toward exam success with expert-led reviews and flexible learning options.</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Button Text</label>
                                                <input type="text" class="form-control" name="hero_button_text" value="ENROLL NOW">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Button Color</label>
                                                <input type="color" class="form-control form-control-color" name="hero_button_color" value="#4CAF50">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Programs Section --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-graduation-cap me-2"></i>Programs Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="programsSectionForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Background Color</label>
                                                <input type="color" class="form-control form-control-color" name="programs_bg_color" value="#f8f9fa">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Text Color</label>
                                                <input type="color" class="form-control form-control-color" name="programs_text_color" value="#333333">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Title</label>
                                                <input type="text" class="form-control" name="programs_title" value="Programs Offered">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Subtitle</label>
                                                <input type="text" class="form-control" name="programs_subtitle" value="Choose from our comprehensive review programs designed for success">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Modalities Section --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-laptop me-2"></i>Modalities Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="modalitiesSectionForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Background Color</label>
                                                <input type="color" class="form-control form-control-color" name="modalities_bg_color" value="#667eea">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Text Color</label>
                                                <input type="color" class="form-control form-control-color" name="modalities_text_color" value="#ffffff">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Title</label>
                                                <input type="text" class="form-control" name="modalities_title" value="Learning Modalities">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Subtitle</label>
                                                <input type="text" class="form-control" name="modalities_subtitle" value="Choose the learning style that works best for you">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- About Section --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-info-circle me-2"></i>About Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="aboutSectionForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Background Color</label>
                                                <input type="color" class="form-control form-control-color" name="about_bg_color" value="#ffffff">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Text Color</label>
                                                <input type="color" class="form-control form-control-color" name="about_text_color" value="#333333">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Title</label>
                                                <input type="text" class="form-control" name="about_title" value="About Us">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Section Subtitle</label>
                                                <input type="text" class="form-control" name="about_subtitle" value="Learn more about our mission and values">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Save Button --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <button type="button" class="btn btn-primary btn-lg" onclick="saveHomepageSettings()">
                                            <i class="fas fa-save me-2"></i>Save Homepage Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dashboard Tab --}}
                    <div class="tab-pane fade" id="dashboard" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-graduation-cap me-2"></i>Student Portal Colors
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="studentPortalForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Primary Color</label>
                                                <input type="color" class="form-control form-control-color" name="primary_color" 
                                                       value="{{ App\Models\UiSetting::get('student_portal', 'primary_color', '#007bff') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Background Color</label>
                                                <input type="color" class="form-control form-control-color" name="background_color" 
                                                       value="{{ App\Models\UiSetting::get('student_portal', 'background_color', '#f8f9fa') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Header Logo</label>
                                                <div class="border p-2 text-center mb-2" style="min-height: 60px;">
                                                    <img id="studentPortalLogoPreview" src="" alt="Logo Preview" 
                                                         style="max-height: 50px; display: none;" class="img-fluid">
                                                    <span class="text-muted" id="studentPortalLogoPlaceholder">No logo uploaded</span>
                                                </div>
                                                <input type="file" class="form-control" name="header_logo" accept="image/*">
                                                <small class="text-muted">Upload logo for student portal header</small>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Activities Tab --}}
                    <div class="tab-pane fade" id="activities" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">Activities Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">Configure student activity settings here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Professor Tab --}}
            <div class="tab-pane fade" id="professor" role="tabpanel">
                <div class="row g-4">
                    {{-- Professor Features --}}
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Professor Features
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Control which features are available to professors in their dashboard.</p>
                                
                                <form id="professorFeaturesForm">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="aiQuizEnabled" name="ai_quiz_enabled" checked>
                                                <label class="form-check-label" for="aiQuizEnabled">
                                                    <strong>AI Quiz Generator</strong><br>
                                                    <small class="text-muted">Allow professors to generate quizzes from documents</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="gradingEnabled" name="grading_enabled" checked>
                                                <label class="form-check-label" for="gradingEnabled">
                                                    <strong>Grading System</strong><br>
                                                    <small class="text-muted">Allow professors to grade assignments and quizzes</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="videoUploadEnabled" name="upload_videos_enabled" checked>
                                                <label class="form-check-label" for="videoUploadEnabled">
                                                    <strong>Video Upload</strong><br>
                                                    <small class="text-muted">Allow professors to upload video links</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="attendanceEnabled" name="attendance_enabled" checked>
                                                <label class="form-check-label" for="attendanceEnabled">
                                                    <strong>Attendance Management</strong><br>
                                                    <small class="text-muted">Allow professors to track student attendance</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="meetingCreationEnabled" name="meeting_creation_enabled"
                                                       {{ DB::table('admin_settings')->where('setting_key', 'meeting_creation_enabled')->value('setting_value') === '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="meetingCreationEnabled">
                                                    <strong>Meeting Creation</strong><br>
                                                    <small class="text-muted">Allow professors to create and schedule meetings</small>
                                                </label>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#meetingWhitelistModal">
                                                        <i class="fas fa-users me-1"></i>View Whitelist
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="professorModuleManagementEnabled" name="professor_module_management_enabled"
                                                       {{ DB::table('admin_settings')->where('setting_key', 'professor_module_management_enabled')->value('setting_value') === '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="professorModuleManagementEnabled">
                                                    <strong>Module Management</strong><br>
                                                    <small class="text-muted">Allow professors to create and manage modules for their assigned programs</small>
                                                </label>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#moduleManagementWhitelistModal">
                                                        <i class="fas fa-users me-1"></i>View Whitelist
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        

                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i>Save Professor Settings
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="loadProfessorSettings()">
                                            <i class="fas fa-refresh me-2"></i>Reset to Defaults
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Professor Settings Info --}}
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>Feature Controls</h6>
                                <p class="text-muted small">These settings control what features are visible and accessible to professors in their dashboard.</p>
                                
                                <h6>AI Quiz Generator</h6>
                                <p class="text-muted small">When enabled, professors can upload documents and generate quizzes automatically. Quizzes sync to student deadlines.</p>
                                
                                <h6>Video Upload</h6>
                                <p class="text-muted small">When enabled, professors can add video links (Zoom, YouTube, etc.) which appear as announcements for students.</p>
                                
                                <h6>Grading System</h6>
                                <p class="text-muted small">Allows professors to grade assignments, activities, and quizzes, updating student progress.</p>
                                
                                <div class="alert alert-warning mt-3">
                                    <small><strong>Note:</strong> Disabling features will hide them from professor dashboards but won't delete existing data.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meeting Whitelist Modal -->
            <div class="modal fade" id="meetingWhitelistModal" tabindex="-1" aria-labelledby="meetingWhitelistModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="meetingWhitelistModalLabel">
                                <i class="fas fa-users me-2"></i>Meeting Creation Whitelist
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>About Whitelist:</strong> Select specific professors who can create meetings. If no professors are selected, the global "Meeting Creation" setting applies to all professors.
                            </div>
                            
                            <form id="meetingWhitelistForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Whitelisted Professors</label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        @php
                                            $professors = \App\Models\Professor::where('professor_archived', 0)->get();
                                            $whitelistedProfessors = explode(',', \App\Models\AdminSetting::getValue('meeting_whitelist_professors', ''));
                                        @endphp
                                        @forelse($professors as $professor)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="professor_{{ $professor->professor_id }}" 
                                                       name="whitelist_professors[]" 
                                                       value="{{ $professor->professor_id }}"
                                                       {{ in_array($professor->professor_id, $whitelistedProfessors) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="professor_{{ $professor->professor_id }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                {{ substr($professor->first_name ?? 'P', 0, 1) }}{{ substr($professor->last_name ?? 'P', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $professor->first_name ?? 'Unknown' }} {{ $professor->last_name ?? 'Professor' }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $professor->email ?? 'No email' }}</small>
                                                            @if($professor->programs && $professor->programs->count() > 0)
                                                                <br>
                                                                <small class="text-success">
                                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                                    {{ $professor->programs->pluck('program_name')->join(', ') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="text-center py-3">
                                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No active professors found.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <small class="text-muted">Select professors who should be allowed to create meetings, regardless of the global setting.</small>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectAllProfessors()">
                                            <i class="fas fa-check-double me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllProfessors()">
                                            <i class="fas fa-times me-1"></i>Clear All
                                        </button>
                                    </div>
                                    <div class="text-muted small">
                                        <span id="selectedCount">{{ count(array_filter($whitelistedProfessors)) }}</span> professors selected
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="saveMeetingWhitelist()">
                                <i class="fas fa-save me-2"></i>Save Whitelist
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Module Management Whitelist Modal -->
            <div class="modal fade" id="moduleManagementWhitelistModal" tabindex="-1" aria-labelledby="moduleManagementWhitelistModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="moduleManagementWhitelistModalLabel">
                                <i class="fas fa-users me-2"></i>Module Management Whitelist
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>About Whitelist:</strong> Select specific professors who can manage modules. If no professors are selected, the global "Module Management" setting applies to all professors.
                            </div>
                            
                            <form id="moduleManagementWhitelistForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Whitelisted Professors</label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        @php
                                            $professors = \App\Models\Professor::where('professor_archived', 0)->get();
                                            $whitelistedModuleProfessors = explode(',', \App\Models\AdminSetting::getValue('professor_module_management_whitelist', ''));
                                        @endphp
                                        @forelse($professors as $professor)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="module_professor_{{ $professor->professor_id }}" 
                                                       name="whitelist_module_professors[]" 
                                                       value="{{ $professor->professor_id }}"
                                                       {{ in_array($professor->professor_id, $whitelistedModuleProfessors) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="module_professor_{{ $professor->professor_id }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                {{ substr($professor->first_name ?? 'P', 0, 1) }}{{ substr($professor->last_name ?? 'P', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $professor->first_name ?? 'Unknown' }} {{ $professor->last_name ?? 'Professor' }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $professor->email ?? 'No email' }}</small>
                                                            @if($professor->programs && $professor->programs->count() > 0)
                                                                <br>
                                                                <small class="text-primary">
                                                                    Programs: {{ $professor->programs->pluck('program_name')->join(', ') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @empty
                                            <div class="text-center py-3">
                                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No active professors found.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <small class="text-muted">Select professors who should be allowed to manage modules, regardless of the global setting.</small>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectAllModuleProfessors()">
                                            <i class="fas fa-check-double me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllModuleProfessors()">
                                            <i class="fas fa-times me-1"></i>Clear All
                                        </button>
                                    </div>
                                    <div class="text-muted small">
                                        <span id="selectedModuleCount">{{ count(array_filter($whitelistedModuleProfessors)) }}</span> professors selected
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="saveModuleManagementWhitelist()">
                                <i class="fas fa-save me-2"></i>Save Whitelist
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Director Tab --}}
            <div class="tab-pane fade" id="director" role="tabpanel">
                <div class="row g-4">
                    {{-- Director Features --}}
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user-tie me-2"></i>Director Features
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Control which features are available to directors in their admin dashboard.</p>
                                
                                <form id="directorFeaturesForm">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorViewStudents" name="view_students" checked>
                                                <label class="form-check-label" for="directorViewStudents">
                                                    <strong>View Students</strong><br>
                                                    <small class="text-muted">Allow directors to view student information and lists</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorManagePrograms" name="manage_programs">
                                                <label class="form-check-label" for="directorManagePrograms">
                                                    <strong>Manage Programs</strong><br>
                                                    <small class="text-muted">Allow directors to create and edit programs</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorManageModules" name="manage_modules">
                                                <label class="form-check-label" for="directorManageModules">
                                                    <strong>Manage Modules</strong><br>
                                                    <small class="text-muted">Allow directors to create and edit modules</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorManageEnrollments" name="manage_enrollments" checked>
                                                <label class="form-check-label" for="directorManageEnrollments">
                                                    <strong>Manage Enrollments</strong><br>
                                                    <small class="text-muted">Allow directors to manage student enrollments</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorViewAnalytics" name="view_analytics" checked>
                                                <label class="form-check-label" for="directorViewAnalytics">
                                                    <strong>View Analytics</strong><br>
                                                    <small class="text-muted">Allow directors to view analytics and reports</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorManageProfessors" name="manage_professors">
                                                <label class="form-check-label" for="directorManageProfessors">
                                                    <strong>Manage Professors</strong><br>
                                                    <small class="text-muted">Allow directors to manage professor accounts</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="directorManageBatches" name="manage_batches">
                                                <label class="form-check-label" for="directorManageBatches">
                                                    <strong>Manage Batches</strong><br>
                                                    <small class="text-muted">Allow directors to manage student batches</small>
                                                </label>
                                            </div>
                                        </div>
                                        

                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Director Settings
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="loadDirectorSettings()">
                                            <i class="fas fa-refresh me-2"></i>Reset to Defaults
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Director Settings Info --}}
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>Director Access Control</h6>
                                <p class="text-muted small">These settings control what features are visible and accessible to directors in their admin dashboard.</p>
                                
                                <h6>Program Management</h6>
                                <p class="text-muted small">When enabled, directors can create, edit, and manage programs within their access scope.</p>
                                
                                <h6>Module Management</h6>
                                <p class="text-muted small">When enabled, directors can create and edit modules for their assigned programs.</p>
                                
                                <h6>Settings Access</h6>
                                <p class="text-muted small">When enabled, directors can access certain admin settings. Main admin settings are always restricted.</p>
                                
                                <div class="alert alert-warning mt-3">
                                    <small><strong>Note:</strong> Directors can only access data for programs they are assigned to, unless they have "all program access" enabled.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Admin Tab --}}
            <div class="tab-pane fade" id="admin" role="tabpanel">
                <div class="row g-4">
                    {{-- Navbar Customization --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bars me-2"></i>Navbar Customization
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="navbarCustomizationForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Background Color</label>
                                        <input type="color" class="form-control form-control-color" name="navbar_bg_color" value="#ffffff">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Text Color</label>
                                        <input type="color" class="form-control form-control-color" name="navbar_text_color" value="#333333">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Brand Name</label>
                                        <input type="text" class="form-control" name="navbar_brand_name" value="Ascendo Review and Training Center">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link Hover Color</label>
                                        <input type="color" class="form-control form-control-color" name="navbar_hover_color" value="#007bff">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Active Link Color</label>
                                        <input type="color" class="form-control form-control-color" name="navbar_active_color" value="#0056b3">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer Customization --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bars me-2"></i>Footer Customization
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="footerCustomizationForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Background Color</label>
                                        <input type="color" class="form-control form-control-color" name="footer_bg_color" value="#212529">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Text Color</label>
                                        <input type="color" class="form-control form-control-color" name="footer_text_color" value="#ffffff">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Footer Text</label>
                                        <textarea class="form-control" name="footer_text" rows="3">© Copyright Ascendo Review and Training Center. All Rights Reserved.</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link Color</label>
                                        <input type="color" class="form-control form-control-color" name="footer_link_color" value="#adb5bd">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link Hover Color</label>
                                        <input type="color" class="form-control form-control-color" name="footer_link_hover_color" value="#ffffff">
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-primary" onclick="saveFooterSettings()">
                                            <i class="fas fa-save me-2"></i>Save Footer Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Advanced Navbar Colors --}}
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-palette me-2"></i>Advanced Navbar Colors
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="advancedNavbarForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="text-secondary mb-3">Header Colors</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Header Background</label>
                                                <input type="color" class="form-control form-control-color" name="header_bg" value="#ffffff">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Header Text Color</label>
                                                <input type="color" class="form-control form-control-color" name="header_text" value="#333333">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Header Border Color</label>
                                                <input type="color" class="form-control form-control-color" name="header_border" value="#e0e0e0">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-secondary mb-3">Sidebar Colors</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Sidebar Background</label>
                                                <input type="color" class="form-control form-control-color" name="sidebar_bg" value="#343a40">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sidebar Text Color</label>
                                                <input type="color" class="form-control form-control-color" name="sidebar_text" value="#ffffff">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Active Link Background</label>
                                                <input type="color" class="form-control form-control-color" name="active_link_bg" value="#007bff">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-secondary mb-3">Actions</h6>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-outline-primary" onclick="previewNavbarColors()">
                                                    <i class="fas fa-eye"></i> Preview Colors
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="resetNavbarColors()">
                                                    <i class="fas fa-undo"></i> Reset to Default
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="saveAllNavbarSettings()">
                                                    <i class="fas fa-save"></i> Save All Changes
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Tab --}}
            <div class="tab-pane fade" id="sidebar" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bars me-2"></i>Sidebar Customization
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="sidebarCustomizationForm" method="POST" action="{{ route('admin.settings.sidebar') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Background Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_background_color" value="{{ $settings['sidebar']['background_color'] ?? '#2d1b69' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Gradient Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_gradient_color" value="{{ $settings['sidebar']['gradient_color'] ?? '#1a1340' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Text Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_text_color" value="{{ $settings['sidebar']['text_color'] ?? '#ffffff' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Hover Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_hover_color" value="{{ $settings['sidebar']['hover_color'] ?? '#a91d3a' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Active Background Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_active_bg_color" value="{{ $settings['sidebar']['active_bg_color'] ?? '#a91d3a' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Active Text Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_active_text_color" value="{{ $settings['sidebar']['active_text_color'] ?? '#ffffff' }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Sidebar Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-dark text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-align-left me-2"></i>Sidebar Footer
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="sidebarFooterCustomizationForm" method="POST" action="{{ route('admin.settings.sidebar') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Footer Background Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_footer_bg_color" value="{{ $settings['sidebar']['footer_bg_color'] ?? '#2d1b69' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Footer Text Color</label>
                                        <input type="color" class="form-control form-control-color" name="sidebar_footer_text_color" value="{{ $settings['sidebar']['footer_text_color'] ?? '#ffffff' }}">
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> The sidebar footer will automatically sync with the main sidebar colors when you save the settings.
                                    </div>
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-save me-2"></i>Save Footer Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Plans Tab --}}
            <div class="tab-pane fade" id="plans" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>Learning Mode Configuration
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4">Configure which learning modes are available for each plan type.</p>
                                
                                <div class="row" id="planSettingsContainer">
                                    <div class="col-12 text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Loading plan settings...</p>
                                        <small class="text-muted">If this persists, please check your login status and try refreshing the page.</small>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-secondary" onclick="refreshPlanSettings()">
                                            <i class="fas fa-sync-alt me-2"></i>Refresh
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="savePlanSettings()">
                                            <i class="fas fa-save me-2"></i>Save All Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Education Levels Tab --}}
            <div class="tab-pane fade" id="education-levels" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user-graduate me-2"></i>Education Level Configuration
                                </h5>
                                <button type="button" class="btn btn-light btn-sm" onclick="addEducationLevel()">
                                    <i class="fas fa-plus me-1"></i>Add Education Level
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4">Configure education levels and their associated file requirements. Each education level can have different file upload requirements that can be enabled/disabled for full plan or modular plan.</p>
                                
                                <div id="educationLevelsContainer">
                                    <div class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Loading education levels...</p>
                                        <small class="text-muted">If this persists, please check your login status and try refreshing the page.</small>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-secondary" onclick="refreshEducationLevels()">
                                            <i class="fas fa-sync-alt me-2"></i>Refresh
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="saveEducationLevels()">
                                            <i class="fas fa-save me-2"></i>Save All Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Methods Tab --}}
            <div class="tab-pane fade" id="payment-methods" role="tabpanel">
                <div class="row g-4">
                    {{-- Terms and Conditions Section --}}
                    <div class="col-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-file-contract me-2"></i>Payment Terms and Conditions
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Configure the terms and conditions that students must accept before making payments.</p>
                                
                                <form id="paymentTermsForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="paymentTermsContent" class="form-label">Payment Terms and Conditions <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="paymentTermsContent" name="payment_terms_content" rows="10" 
                                                          placeholder="Enter the terms and conditions for payments...">{{ \App\Models\AdminSetting::getValue('payment_terms_content', 'By submitting this payment, you agree to the following terms and conditions:

1. All payments are final and non-refundable once processed.
2. Payment confirmation may take 1-3 business days to process.
3. Please ensure all payment details are accurate before submission.
4. Fraudulent payment submissions will result in account suspension.
5. Contact support if you encounter any payment issues.

Please read and understand these terms before proceeding with your payment.') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="paymentAbortTermsContent" class="form-label">Payment Abort Terms <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="paymentAbortTermsContent" name="payment_abort_terms_content" rows="10" 
                                                          placeholder="Enter the terms shown when students abort payments...">{{ \App\Models\AdminSetting::getValue('payment_abort_terms_content', 'Are you sure you want to abort this payment?

By aborting this payment:
- Your enrollment will be cancelled
- All submitted information will be permanently deleted
- You will need to start the enrollment process again
- No refund will be processed for any partial payments

If you are experiencing technical difficulties, please contact our support team instead of aborting the payment.

This action cannot be undone.') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enablePaymentTerms" name="enable_payment_terms" 
                                                   {{ \App\Models\AdminSetting::getValue('enable_payment_terms', '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enablePaymentTerms">
                                                <strong>Require students to accept terms before payment</strong>
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-save me-2"></i>Save Terms and Conditions
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Payment Methods Management
                                </h5>
                                <button type="button" class="btn btn-light btn-sm" onclick="openAddPaymentMethodModal()">
                                    <i class="fas fa-plus me-1"></i>Add Payment Method
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4">Manage payment methods available to students. Enable/disable methods and upload QR codes for digital payment options.</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">Order</th>
                                                <th>Method Name</th>
                                                <th>Type</th>
                                                <th>QR Code</th>
                                                <th>Status</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paymentMethodsTableBody">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading payment methods...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Tips:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Drag rows to reorder payment methods as they appear to students</li>
                                            <li>Upload QR codes for digital payment methods (GCash, Maya, etc.)</li>
                                            <li>Only enabled payment methods will be visible to students</li>
                                            <li>Provide clear instructions for each payment method</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Modal -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentMethodModalTitle">Add Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentMethodForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="method_name" class="form-label">Method Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="method_name" name="method_name" required placeholder="e.g., GCash, Maya, Bank Transfer">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="method_type" class="form-label">Method Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="method_type" name="method_type" required>
                                        <option value="">Select Type</option>
                                        <option value="credit_card">Credit Card</option>
                                        <option value="gcash">GCash</option>
                                        <option value="maya">Maya</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cash">Cash</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Brief description of this payment method"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Payment Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="3" placeholder="Detailed instructions for students on how to use this payment method"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="qr_code" class="form-label">QR Code (Optional)</label>
                                    <input type="file" class="form-control" id="qr_code" name="qr_code" accept="image/*">
                                    <small class="text-muted">Upload a QR code image for this payment method</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" checked>
                                        <label class="form-check-label" for="is_enabled">
                                            Enable this payment method
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current QR Code Display (for editing) -->
                        <div id="currentQrCode" style="display: none;" class="mb-3">
                            <!-- QR code will be populated here -->
                        </div>
                        
                        <!-- Remove QR Code Option (for editing) -->
                        <div id="removeQrCodeSection" style="display: none;" class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remove_qr_code" name="remove_qr_code">
                                <label class="form-check-label" for="remove_qr_code">
                                    Remove current QR code
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Fields Configuration Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="bi bi-gear-fill me-2"></i>Dynamic Payment Fields Configuration
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addNewPaymentField()">
                                    <i class="bi bi-plus-circle"></i> Add Field
                                </button>
                            </div>
                            <p class="text-muted small">Configure custom fields that students need to fill when using this payment method.</p>
                            
                            <!-- Dynamic Fields Container -->
                            <div id="dynamicFieldsContainer" class="border rounded p-3 bg-light">
                                <div class="text-center text-muted py-3" id="noFieldsMessage">
                                    <i class="bi bi-info-circle"></i>
                                    No custom fields configured. Click "Add Field" to create dynamic fields for this payment method.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePaymentMethodBtn" onclick="savePaymentMethod()">
                        <i class="fas fa-save"></i> Save Payment Method
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Education Level Modal -->
    <div class="modal fade" id="educationLevelModal" tabindex="-1" aria-labelledby="educationLevelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="educationLevelModalTitle">Add Education Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="educationLevelForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level_name" class="form-label">Education Level Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="level_name" name="level_name" required placeholder="e.g., Undergraduate, Graduate, High School">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="level_order" name="level_order" min="1" placeholder="1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="level_description" class="form-label">Description</label>
                            <textarea class="form-control" id="level_description" name="level_description" rows="2" placeholder="Brief description of this education level"></textarea>
                        </div>
                        
                        <!-- Plan Availability -->
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-graduation-cap me-2"></i>Plan Availability</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="available_full_plan" name="available_full_plan" checked>
                                        <label class="form-check-label" for="available_full_plan">
                                            Available for Full Plan
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="available_modular_plan" name="available_modular_plan" checked>
                                        <label class="form-check-label" for="available_modular_plan">
                                            Available for Modular Plan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Requirements -->
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-file-upload me-2"></i>Document Requirements</h6>
                            <p class="text-muted small mb-3">Configure which documents are required for this education level. Each document can accept different file types.</p>
                            
                            <div id="fileRequirementsContainer">
                                <!-- Document requirements will be populated here -->
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDocumentRequirement()">
                                <i class="fas fa-plus me-1"></i>Add Document Requirement
                            </button>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <strong>Available Document Types:</strong> School ID, Diploma, TOR, PSA Birth Certificate, Good Moral Certificate, Course Certificate, Photo 2x2
                                    <br><strong>File Types:</strong> Images (JPG, PNG, GIF), PDF, Documents (DOC, DOCX)
                                </small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active (visible to students)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEducationLevelBtn" onclick="saveEducationLevel()">
                        <i class="fas fa-save"></i> Save Education Level
                    </button>
                </div>
            </div>
        </div>
    </div>

    
@endsection
@push('scripts')
<script>
// Global variables
let hasUnsavedChanges = false;
let educationLevels = [];
let editingEducationLevelId = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Settings page loaded');
    
    // Test if key elements exist
    const requirementsContainer = document.getElementById('requirementsContainer');
    const educationLevelsContainer = document.getElementById('educationLevelsContainer');
    const paymentMethodsContainer = document.getElementById('paymentMethodsTableBody');
    
    console.log('Requirements container found:', !!requirementsContainer);
    console.log('Education levels container found:', !!educationLevelsContainer);
    console.log('Payment methods container found:', !!paymentMethodsContainer);
    
    // Initialize main tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTabs button[data-bs-toggle="tab"]'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Initialize student sub-tabs
    var studentSubTabList = [].slice.call(document.querySelectorAll('#studentSubTabs button[data-bs-toggle="tab"]'));
    studentSubTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Load existing form requirements and settings
    loadFormRequirements();
    loadNavbarSettings();
    loadFooterSettings();
    loadStudentPortalSettings();
    loadHomepageSettings();
    loadHomepageSettings();
    loadProfessorSettings();
    loadDirectorSettings();
    
    // Load tab-specific content immediately
    loadEducationLevels();
    loadPaymentMethods();
    
    // Add event listener for plans tab
    const plansTab = document.getElementById('plans-tab');
    if (plansTab) {
        plansTab.addEventListener('shown.bs.tab', function() {
            loadPlanSettings();
        });
    }
    
    // Add new requirement functionality
    const addRequirementButton = document.getElementById('addRequirement');
    if (addRequirementButton) {
        addRequirementButton.addEventListener('click', function() {
            addRequirementField();
            showAlert('New field added. Remember to save your changes!', 'info');
        });
    }
    
    // Track changes in form fields
    document.addEventListener('change', function(e) {
        if (e.target.closest('#studentRequirementsForm')) {
            hasUnsavedChanges = true;
            updateSaveButtonState();
        }
    });
    
    // Track changes in input fields
    document.addEventListener('input', function(e) {
        if (e.target.closest('#studentRequirementsForm')) {
            hasUnsavedChanges = true;
            updateSaveButtonState();
        }
    });
    
    // Warn user about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    function updateSaveButtonState() {
        const submitButton = document.getElementById('saveRequirementsBtn');
        if (submitButton && hasUnsavedChanges) {
            submitButton.classList.add('btn-warning');
            submitButton.classList.remove('btn-success');
            submitButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Save Changes';
        }
    }
    
    function resetSaveButtonState() {
        const submitButton = document.getElementById('saveRequirementsBtn');
        if (submitButton) {
            submitButton.classList.remove('btn-warning');
            submitButton.classList.add('btn-success');
            submitButton.innerHTML = '<i class="fas fa-save"></i> Save Form Fields';
        }
        hasUnsavedChanges = false;
    }

    // Navbar color preview functionality
    const previewBtn = document.getElementById('previewColors');
    if (previewBtn) {
        previewBtn.addEventListener('click', previewNavbarColors);
    }

    // Reset colors functionality
    const resetBtn = document.getElementById('resetColors');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetNavbarColors);
    }

    // Save navbar settings
document
  .getElementById('navbarCustomizationForm')
  .addEventListener('submit', function(e) {
    e.preventDefault();
    saveNavbarSettings();
  });

    // Save student requirements
    const studentRequirementsForm = document.getElementById('studentRequirementsForm');
    if (studentRequirementsForm) {
        studentRequirementsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateSortOrder(); // Update sort order before saving
            saveFormRequirements();
        });
    }
    
    // Save student portal settings
    const studentPortalForm = document.getElementById('studentPortalForm');
    if (studentPortalForm) {
        studentPortalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveStudentPortalSettings();
        });
    }

    // Save professor features settings
    const professorFeaturesForm = document.getElementById('professorFeaturesForm');
    if (professorFeaturesForm) {
        professorFeaturesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProfessorSettings();
        });
    }

    // Save director features settings
    const directorFeaturesForm = document.getElementById('directorFeaturesForm');
    if (directorFeaturesForm) {
        directorFeaturesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveDirectorSettings();
        });
    }

    // Save referral settings
    const referralSettingsForm = document.getElementById('referralSettingsForm');
    if (referralSettingsForm) {
        referralSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveReferralSettings();
        });
    }

    // Save payment terms settings
    const paymentTermsForm = document.getElementById('paymentTermsForm');
    if (paymentTermsForm) {
        paymentTermsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            savePaymentTermsSettings();
        });
    }

    // ✅ Initialize Sortable for requirements container (drag & drop)
    const sortableRequirementsContainer = document.getElementById('requirementsContainer');
    if (sortableRequirementsContainer) {
        new Sortable(sortableRequirementsContainer, {
            animation: 150,
            ghostClass: 'dragging-placeholder',
            handle: '.requirement-handle',
            onEnd: function (evt) {
                console.log(`Item moved: ${evt.oldIndex} -> ${evt.newIndex}`);
                updateSortOrder();
                hasUnsavedChanges = true;
                updateSaveButtonState();
                showAlert('Field order changed. Remember to save your changes!', 'info');
            }
        });
    }
    
    // Add event listeners for system field toggles
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('system-field-toggle') || 
            e.target.classList.contains('system-field-program')) {
            const fieldName = e.target.getAttribute('data-field');
            const toggleType = e.target.getAttribute('data-type');
            
            if (e.target.classList.contains('system-field-toggle')) {
                const isChecked = e.target.checked;
                console.log(`System field ${fieldName} ${toggleType} changed to:`, isChecked);
                
                // You can implement API calls here to save system field settings
                // For now, just show a notification
                showAlert(`System field "${fieldName}" ${toggleType} status updated`, 'success');
            } else if (e.target.classList.contains('system-field-program')) {
                const programType = e.target.value;
                console.log(`System field ${fieldName} program type changed to:`, programType);
                showAlert(`System field "${fieldName}" program type updated to ${programType}`, 'success');
            }
        }
    });
});

function updateSaveButtonState() {
    const submitButton = document.querySelector('#studentRequirementsForm button[type="submit"]');
    if (submitButton && hasUnsavedChanges) {
        submitButton.classList.add('btn-warning');
        submitButton.classList.remove('btn-success');
        submitButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Save Changes';
    }
}

function resetSaveButtonState() {
    const submitButton = document.querySelector('#studentRequirementsForm button[type="submit"]');
    if (submitButton) {
        submitButton.classList.remove('btn-warning');
        submitButton.classList.add('btn-success');
        submitButton.innerHTML = '<i class="fas fa-save"></i> Save Form Fields';
    }
    hasUnsavedChanges = false;
}

function loadFormRequirements() {
    console.log('Loading form requirements...');
    
    const container = document.getElementById('requirementsContainer');
    if (!container) {
        console.error('Requirements container not found');
        return;
    }
    
    // Show loading state
    container.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading form requirements...</div>';
    
    fetch('/admin/settings/form-requirements')
        .then(response => {
            console.log('Load response status:', response.status);
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error('Authentication required. Please log in.');
                }
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Loaded requirements:', data);
            
            container.innerHTML = '';
            
            // Add system/hardcoded fields first
            addSystemFields();
            
            // Sort data by sort_order
            const sortedData = data.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
            
            sortedData.forEach(requirement => {
                addRequirementField(requirement);
            });
            
        })
        .catch(error => {
            console.error('Error loading requirements:', error);
            container.innerHTML = `<div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Unable to load form requirements: ${error.message}
                <button class="btn btn-sm btn-outline-warning ms-2" onclick="loadFormRequirements()">
                    <i class="fas fa-redo me-1"></i>Retry
                </button>
            </div>`;
            
            // Still show system fields even if API fails
            try {
                addSystemFields();
            } catch (e) {
                console.error('Error adding system fields:', e);
            }
        });
}
function addRequirementField(data = {}) {
    const container = document.getElementById('requirementsContainer');
    const index = container.children.length;

    const requirementDiv = document.createElement('div');
    // Add visual styling for inactive fields
    const isActive = data.is_active !== false;
    const inactiveClass = !isActive ? ' requirement-inactive' : '';
    const inactiveStyle = !isActive ? ' style="opacity: 0.6; background-color: #f8f9fa;"' : '';
    
    requirementDiv.className = `requirement-item border rounded p-3 mb-3 d-flex align-items-start${inactiveClass}`;
    if (!isActive) {
        requirementDiv.style.opacity = '0.6';
        requirementDiv.style.backgroundColor = '#f8f9fa';
    }
    
    requirementDiv.innerHTML = `
        <div class="requirement-handle me-2 d-flex align-items-center justify-content-center" 
             style="cursor: grab; width: 30px;">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="flex-grow-1">
            ${!isActive ? '<div class="alert alert-warning py-1 px-2 mb-2 small"><i class="fas fa-eye-slash"></i> This field is currently <strong>inactive</strong> and hidden from registration forms</div>' : ''}
            <div class="row gx-2">
                <!-- Section Name (3 cols) -->
                <div class="col-md-3">
                    <label class="form-label">Section Name</label>
                    <input type="text" class="form-control" 
                           name="requirements[${index}][section_name]"
                           value="${data.section_name || ''}" 
                           placeholder="e.g., Personal Information">
                </div>
                <!-- Display Label (3 cols) -->
                <div class="col-md-3">
                    <label class="form-label">Display Label</label>
                    <input type="text" class="form-control display-label-input" 
                           name="requirements[${index}][field_label]"
                           value="${data.field_label || ''}"
                           data-index="${index}"
                           placeholder="e.g., Phone Number"
                           style="${data.is_bold ? 'font-weight:bold;' : ''}">
                    <input type="hidden" class="field-name-input" 
                           name="requirements[${index}][field_name]"
                           value="${data.field_name || ''}">
                    <small class="text-muted">Field name will be auto-generated</small>
                </div>
                <!-- Field Type (2 cols) -->
                <div class="col-md-2">
                    <label class="form-label">Field Type</label>
                    <select class="form-select" 
                            name="requirements[${index}][field_type]" 
                            onchange="handleFieldTypeChange(this)">
                        <option value="text"     ${data.field_type==='text'     ? 'selected':''}>Text</option>
                        <option value="email"    ${data.field_type==='email'    ? 'selected':''}>Email</option>
                        <option value="tel"      ${data.field_type==='tel'      ? 'selected':''}>Phone</option>
                        <option value="date"     ${data.field_type==='date'     ? 'selected':''}>Date</option>
                        <option value="file"     ${data.field_type==='file'     ? 'selected':''}>File</option>
                        <option value="select"   ${data.field_type==='select'   ? 'selected':''}>Dropdown</option>
                        <option value="textarea" ${data.field_type==='textarea' ? 'selected':''}>Textarea</option>
                        <option value="section"  ${data.field_type==='section'  ? 'selected':''}>Section Header</option>
                        <option value="module_selection" ${data.field_type==='module_selection' ? 'selected':''}>Module Selection</option>
                    </select>
                </div>
                <!-- Program (1 col) -->
                <div class="col-md-1">
                    <label class="form-label">Program</label>
                    <select class="form-select" 
                            name="requirements[${index}][program_type]">
                        <option value="both"     ${data.program_type==='both'     ? 'selected':''}>Both</option>
                        <option value="full" ${data.program_type==='full' ? 'selected':''}>Full</option>
                        <option value="modular"  ${data.program_type==='modular'  ? 'selected':''}>Modular</option>
                    </select>
                </div>
                <!-- Required & Active switches (1 col) -->
                <div class="col-md-1 d-flex flex-column align-items-start">
                    <label class="form-label">Required</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="requirements[${index}][is_required]"
                               value="1"
                               ${data.is_required ? 'checked' : ''}>
                    </div>
                    <label class="form-label mt-2">Active</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="requirements[${index}][is_active]"
                               value="1"
                               onchange="toggleFieldActiveStatus(this)"
                               ${isActive ? 'checked' : ''}>
                    </div>
                </div>
                <!-- Bold & Trash buttons (1 col) -->
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm me-1"
                            onclick="toggleBoldText(this)"
                            title="Toggle Bold Text">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="removeRequirement(this)"
                            title="Remove Field">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <!-- Field Options (for select fields) -->
            <div class="field-options-container mt-2" style="display: ${data.field_type === 'select' ? 'block' : 'none'}">
                <label class="form-label">Field Options (one per line)</label>
                <textarea class="form-control" rows="3" 
                          name="requirements[${index}][field_options]"
                          placeholder="Undergraduate&#10;Graduate">${data.field_options ? data.field_options.join('\n') : ''}</textarea>
                <small class="text-muted">Enter each option on a new line</small>
            </div>
            <!-- HIDDEN flags -->
            <input type="hidden" name="requirements[${index}][id]"         value="${data.id || ''}">
            <input type="hidden" name="requirements[${index}][is_bold]"    value="${data.is_bold ? '1' : '0'}">
            <input type="hidden" name="requirements[${index}][sort_order]" value="${data.sort_order || index}">
        </div>
    `;
    container.appendChild(requirementDiv);

    // Auto-generate field_name from display label
    const displayLabelInput = requirementDiv.querySelector('.display-label-input');
    const fieldNameInput = requirementDiv.querySelector('.field-name-input');
    if (displayLabelInput && fieldNameInput) {
        displayLabelInput.addEventListener('input', function() {
            const label = this.value.trim();
            // Convert to snake_case
            let snake = label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            if (!snake.match(/^[a-z]/)) snake = 'field_' + snake;
            fieldNameInput.value = snake;
        });
        // Trigger on load
        if (!fieldNameInput.value && displayLabelInput.value) {
            const label = displayLabelInput.value.trim();
            let snake = label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
            if (!snake.match(/^[a-z]/)) snake = 'field_' + snake;
            fieldNameInput.value = snake;
        }
    }
}


function removeRequirement(button) {
    const requirementItem = button.closest('.requirement-item');
    const fieldLabel = requirementItem.querySelector('input[name*="[field_label]"]').value || 'this field';
    
    if (confirm(`Are you sure you want to remove "${fieldLabel}"?`)) {
        requirementItem.remove();
        updateSortOrder();
        hasUnsavedChanges = true;
        updateSaveButtonState();
        showAlert('Field removed. Remember to save your changes!', 'warning');
    }
}

function handleFieldTypeChange(selectElement) {
    const row = selectElement.closest('.row');
    const fieldNameInput = row.querySelector('input[name*="[field_name]"]');
    const fieldLabelInput = row.querySelector('input[name*="[field_label]"]');
    const sectionInput = row.querySelector('input[name*="[section_name]"]');
    const optionsContainer = selectElement.closest('.requirement-item').querySelector('.field-options-container');
    
    if (selectElement.value === 'section') {
        fieldNameInput.style.display = 'none';
        fieldLabelInput.style.display = 'none';
        sectionInput.placeholder = 'Section Title (e.g., Personal Information)';
        sectionInput.style.backgroundColor = '#f8f9fa';
        sectionInput.style.fontWeight = 'bold';
        optionsContainer.style.display = 'none';
    } else {
        fieldNameInput.style.display = 'block';
        fieldLabelInput.style.display = 'block';
        sectionInput.placeholder = 'e.g., Personal Information';
        sectionInput.style.backgroundColor = '';
        sectionInput.style.fontWeight = '';
        
        // Show options container only for select fields
        if (selectElement.value === 'select') {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    }
}

function updateSortOrder() {
    const updateRequirementsContainer = document.getElementById('requirementsContainer');
    const items = updateRequirementsContainer.querySelectorAll('.requirement-item');
    
    items.forEach((item, index) => {
        // Find the sort_order hidden input and update it
        const sortOrderInput = item.querySelector('input[name*="[sort_order]"]');
        if (sortOrderInput) {
            sortOrderInput.value = index;
        } else {
            // If no sort_order input exists, create one
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `requirements[${index}][sort_order]`;
            hiddenInput.value = index;
            item.appendChild(hiddenInput);
        }
        
        // Update the index in all input names for this item
        const inputs = item.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name && input.name.includes('requirements[')) {
                const newName = input.name.replace(/requirements\[\d+\]/, `requirements[${index}]`);
                input.name = newName;
            }
        });
    });
}

function saveFormRequirements() {
    const form = document.getElementById('studentRequirementsForm');
    
    // Validate form before saving
    if (!validateRequirementsForm()) {
        return;
    }
    
    // Process form data to handle checkboxes properly
    const formData = new FormData();
    const requirementItems = document.querySelectorAll('.requirement-item');
    
    requirementItems.forEach((item, index) => {
        const data = {
            id: item.querySelector('input[name*="[id]"]')?.value || '',
            section_name: item.querySelector('input[name*="[section_name]"]')?.value || '',
            field_name: item.querySelector('input[name*="[field_name]"]')?.value || '',
            field_label: item.querySelector('input[name*="[field_label]"]')?.value || '',
            field_type: item.querySelector('select[name*="[field_type]"]')?.value || '',
            program_type: item.querySelector('select[name*="[program_type]"]')?.value || '',
            is_required: item.querySelector('input[name*="[is_required]"]')?.checked ? '1' : '0',
            is_active: item.querySelector('input[name*="[is_active]"]')?.checked ? '1' : '0',
            is_bold: item.querySelector('input[name*="[is_bold]"]')?.value || '0',
            sort_order: item.querySelector('input[name*="[sort_order]"]')?.value || index,
            field_options: item.querySelector('textarea[name*="[field_options]"]')?.value || ''
        };
        
        // Add to FormData
        Object.keys(data).forEach(key => {
            formData.append(`requirements[${index}][${key}]`, data[key]);
        });
    });
    
    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    console.log('Saving form requirements...');
    
    // Log form data for debugging
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Show loading state
    const submitButton = document.getElementById('saveRequirementsBtn');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitButton.disabled = true;
    
    fetch('/admin/settings/form-requirements', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showAlert('Form fields saved successfully!', 'success');
            resetSaveButtonState();
            // Reload the requirements to show the updated state
            setTimeout(() => {
                loadFormRequirements();
            }, 1000);
        } else {
            showAlert(data.error || 'Error saving form fields', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving form fields: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

function validateRequirementsForm() {
    const requirementItems = document.querySelectorAll('.requirement-item:not(.system-field)'); // Exclude system fields
    let isValid = true;
    let errors = [];
    
    requirementItems.forEach((item, index) => {
        const fieldTypeSelect = item.querySelector('select[name*="[field_type]"]');
        const fieldNameInput = item.querySelector('input[name*="[field_name]"]');
        const fieldLabelInput = item.querySelector('input[name*="[field_label]"]');
        const sectionNameInput = item.querySelector('input[name*="[section_name]"]');
        
        // Check if elements exist before accessing their values
        if (!fieldTypeSelect) {
            console.warn(`Row ${index + 1}: Field type select not found`);
            return; // Skip this item
        }
        
        const fieldType = fieldTypeSelect.value;
        const fieldName = fieldNameInput ? fieldNameInput.value.trim() : '';
        const fieldLabel = fieldLabelInput ? fieldLabelInput.value.trim() : '';
        const sectionName = sectionNameInput ? sectionNameInput.value.trim() : '';
        
        // Validate based on field type
        if (fieldType === 'section') {
            if (!sectionName && sectionNameInput) {
                errors.push(`Row ${index + 1}: Section header must have a section name`);
                isValid = false;
            }
        } else {
            if (!fieldName && fieldNameInput) {
                errors.push(`Row ${index + 1}: Field name is required`);
                isValid = false;
            }
            if (!fieldLabel && fieldLabelInput) {
                errors.push(`Row ${index + 1}: Display label is required`);
                isValid = false;
            }
            
            // Validate field name format
            if (fieldName && !/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(fieldName)) {
                errors.push(`Row ${index + 1}: Field name must start with a letter and contain only letters, numbers, and underscores`);
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        showAlert('Please fix the following errors:<br>' + errors.join('<br>'), 'danger');
    }
    
    return isValid;
}

function loadNavbarSettings() {
    fetch('/admin/settings/navbar')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input && data[key]) {
                    input.value = data[key];
                }
            });
        })
        .catch(error => {
            console.error('Error loading navbar settings:', error);
        });
}

function loadFooterSettings() {
    fetch('/admin/settings/footer')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`input[name="${key}"], textarea[name="${key}"]`);
                if (input && data[key]) {
                    input.value = data[key];
                }
            });
        })
        .catch(error => {
            console.error('Error loading footer settings:', error);
        });
}

function loadStudentPortalSettings() {
    fetch('/admin/settings/student-portal')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`#studentPortalForm input[name="${key}"]`);
                if (input && data[key]) {
                    input.value = data[key];
                }
            });
        })
        .catch(error => {
            console.error('Error loading student portal settings:', error);
        });
}

function loadHomepageSettings() {
    console.log('Loading homepage settings...');
    
    fetch('/admin/settings/homepage')
        .then(response => response.json())
        .then(data => {
            console.log('Homepage settings loaded:', data);
            
            // Update hero section form
            const heroForm = document.getElementById('heroSectionForm');
            if (heroForm) {
                Object.keys(data).forEach(key => {
                    const input = heroForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update programs section form
            const programsForm = document.getElementById('programsSectionForm');
            if (programsForm) {
                Object.keys(data).forEach(key => {
                    const input = programsForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update modalities section form
            const modalitiesForm = document.getElementById('modalitiesSectionForm');
            if (modalitiesForm) {
                Object.keys(data).forEach(key => {
                    const input = modalitiesForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update about section form
            const aboutForm = document.getElementById('aboutSectionForm');
            if (aboutForm) {
                Object.keys(data).forEach(key => {
                    const input = aboutForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading homepage settings:', error);
        });
}

function loadNavbarCustomizationSettings() {
    console.log('Loading navbar customization settings...');
    
    fetch('/admin/settings/navbar')
        .then(response => response.json())
        .then(data => {
            console.log('Navbar settings loaded:', data);
            
            // Update navbar customization form
            const navbarForm = document.getElementById('navbarCustomizationForm');
            if (navbarForm) {
                Object.keys(data).forEach(key => {
                    const input = navbarForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
            
            // Update advanced navbar form
            const advancedForm = document.getElementById('advancedNavbarForm');
            if (advancedForm) {
                Object.keys(data).forEach(key => {
                    const input = advancedForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading navbar settings:', error);
        });
}

function loadFooterCustomizationSettings() {
    console.log('Loading footer customization settings...');
    
    fetch('/admin/settings/footer')
        .then(response => response.json())
        .then(data => {
            console.log('Footer settings loaded:', data);
            
            // Update footer form
            const footerForm = document.getElementById('footerCustomizationForm');
            if (footerForm) {
                Object.keys(data).forEach(key => {
                    const input = footerForm.querySelector(`[name="${key}"]`);
                    if (input && data[key]) {
                        input.value = data[key];
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading footer settings:', error);
        });
}

function saveAllNavbarSettings() {
    console.log('Saving all navbar settings...');
    
    // Collect all navbar form data
    const allData = {};
    
    // Get data from navbar customization form
    const navbarForm = document.getElementById('navbarCustomizationForm');
    if (navbarForm) {
        const formData = new FormData(navbarForm);
        for (let [key, value] of formData.entries()) {
            allData[key] = value;
        }
    }
    
    // Get data from advanced navbar form
    const advancedForm = document.getElementById('advancedNavbarForm');
    if (advancedForm) {
        const formData = new FormData(advancedForm);
        for (let [key, value] of formData.entries()) {
            allData[key] = value;
        }
    }
    
    // Add CSRF token
    allData._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/admin/settings/navbar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Navbar settings saved:', data);
        if (data.success) {
            showAlert('Navbar settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving navbar settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving navbar settings:', error);
        showAlert('Error saving navbar settings: ' + error.message, 'danger');
    });
}

function saveFooterSettings() {
    console.log('Saving footer settings...');
    
    const footerForm = document.getElementById('footerCustomizationForm');
    const formData = new FormData(footerForm);
    
    const allData = {};
    for (let [key, value] of formData.entries()) {
        allData[key] = value;
    }
    
    allData._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/admin/settings/footer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Footer settings saved:', data);
        if (data.success) {
            showAlert('Footer settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving footer settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving footer settings:', error);
        showAlert('Error saving footer settings: ' + error.message, 'danger');
    });
}

function previewNavbarColors() {
    showAlert('Preview functionality coming soon!', 'info');
}

function resetNavbarColors() {
    const defaultColors = {
        navbar_bg_color: '#ffffff',
        navbar_text_color: '#333333', 
        navbar_brand_name: 'Ascendo Review and Training Center',
        navbar_hover_color: '#007bff',
        navbar_active_color: '#0056b3',
        header_bg: '#ffffff',
        header_text: '#333333',
        header_border: '#e0e0e0',
        sidebar_bg: '#343a40',
        sidebar_text: '#ffffff',
        active_link_bg: '#007bff'
    };
    
    // Reset navbar customization form
    const navbarForm = document.getElementById('navbarCustomizationForm');
    if (navbarForm) {
        Object.keys(defaultColors).forEach(key => {
            const input = navbarForm.querySelector(`[name="${key}"]`);
            if (input && defaultColors[key]) {
                input.value = defaultColors[key];
            }
        });
    }
    
    // Reset advanced navbar form
    const advancedForm = document.getElementById('advancedNavbarForm');
    if (advancedForm) {
        Object.keys(defaultColors).forEach(key => {
            const input = advancedForm.querySelector(`[name="${key}"]`);
            if (input && defaultColors[key]) {
                input.value = defaultColors[key];
            }
        });
    }
    
    showAlert('Colors reset to defaults', 'info');
}

// Missing utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
    alertDiv.innerHTML = `
        <div>${message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-dismiss after 5 seconds (except for error messages)
    if (type !== 'danger') {
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }
        }, 5000);
    }
    
    // Manually dismiss on click
    alertDiv.querySelector('.btn-close').addEventListener('click', () => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    });
}

function toggleFieldActiveStatus(checkbox) {
    const requirementItem = checkbox.closest('.requirement-item');
    
    if (checkbox.checked) {
        // Field is active
        requirementItem.classList.remove('requirement-inactive');
        requirementItem.style.opacity = '1';
        requirementItem.style.backgroundColor = '';
        
        // Remove inactive warning
        const warningAlert = requirementItem.querySelector('.alert-warning');
        if (warningAlert) {
            warningAlert.remove();
        }
    } else {
        // Field is inactive
        requirementItem.classList.add('requirement-inactive');
        requirementItem.style.opacity = '0.6';
        requirementItem.style.backgroundColor = '#f8f9fa';
        
        // Add inactive warning if not already present
        const flexGrow = requirementItem.querySelector('.flex-grow-1');
        const existingWarning = flexGrow.querySelector('.alert-warning');
        if (!existingWarning) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'alert alert-warning py-1 px-2 mb-2 small';
            warningDiv.innerHTML = '<i class="fas fa-eye-slash"></i> This field is currently <strong>inactive</strong> and hidden from registration forms';
            flexGrow.insertBefore(warningDiv, flexGrow.firstChild);
        }
    }
    
    // Track changes
    hasUnsavedChanges = true;
    updateSaveButtonState();
    
    // Show immediate feedback
    showAlert('Field status updated. Remember to save your changes!', 'info');
}

function toggleBoldText(button) {
    const requirementItem = button.closest('.requirement-item');
    const labelInput = requirementItem.querySelector('input[name*="[field_label]"]');
    const hiddenBoldInput = requirementItem.querySelector('input[name*="[is_bold]"]');
    
    if (labelInput.style.fontWeight === 'bold') {
        labelInput.style.fontWeight = 'normal';
        button.classList.remove('btn-secondary');
        button.classList.add('btn-outline-secondary');
        if (hiddenBoldInput) {
            hiddenBoldInput.value = '0';
        }
    } else {
        labelInput.style.fontWeight = 'bold';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-secondary');
        if (hiddenBoldInput) {
            hiddenBoldInput.value = '1';
        }
    }
    
    // Track changes
    hasUnsavedChanges = true;
    updateSaveButtonState();
    
    showAlert('Text style updated. Remember to save your changes!', 'info');
}

function saveHomepageSettings() {
    console.log('Saving homepage settings...');
    
    const allData = {};
    
    // Collect data from all homepage forms
    const forms = ['heroSectionForm', 'programsSectionForm', 'modalitiesSectionForm', 'aboutSectionForm'];
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                allData[key] = value;
            }
        }
    });
    
    // Add CSRF token
    allData._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/admin/settings/homepage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(allData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Homepage settings saved:', data);
        if (data.success) {
            showAlert('Homepage settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving homepage settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving homepage settings:', error);
        showAlert('Error saving homepage settings: ' + error.message, 'danger');
    });
}

function saveStudentPortalSettings() {
    console.log('Saving student portal settings...');
    
    const form = document.getElementById('studentPortalForm');
    const formData = new FormData(form);
    
    fetch('/admin/settings/student-portal', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Student portal settings saved:', data);
        if (data.success) {
            showAlert('Student portal settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving student portal settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving student portal settings:', error);
        showAlert('Error saving student portal settings: ' + error.message, 'danger');
    });
}

function previewForm(type) {
    showAlert(`Preview ${type} form functionality coming soon!`, 'info');
}

// Professor Settings Functions
function loadProfessorSettings() {
    fetch('/admin/settings/professor-features', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Professor settings loaded:', data);
        if (data.success && data.settings) {
            // Set checkbox values based on loaded settings
            Object.keys(data.settings).forEach(key => {
                const checkbox = document.querySelector(`input[name="${key}"]`);
                if (checkbox) {
                    checkbox.checked = data.settings[key] === 'true' || data.settings[key] === true;
                }
            });
        }
    })
    .catch(error => {
        console.error('Error loading professor settings:', error);
        // Set default values if loading fails
        const checkboxes = document.querySelectorAll('#professorFeaturesForm input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true; // Default to enabled
        });
    });
}

function saveProfessorSettings() {
    const form = document.getElementById('professorFeaturesForm');
    
    // Collect checkbox values as string 'true'/'false' to match backend expectation
    const data = {
        ai_quiz_enabled: form.querySelector('input[name="ai_quiz_enabled"]')?.checked ? 'true' : 'false',
        grading_enabled: form.querySelector('input[name="grading_enabled"]')?.checked ? 'true' : 'false',
        upload_videos_enabled: form.querySelector('input[name="upload_videos_enabled"]')?.checked ? 'true' : 'false',
        attendance_enabled: form.querySelector('input[name="attendance_enabled"]')?.checked ? 'true' : 'false',
        view_programs_enabled: form.querySelector('input[name="view_programs_enabled"]')?.checked ? 'true' : 'false',
        meeting_creation_enabled: form.querySelector('input[name="meeting_creation_enabled"]')?.checked ? 'true' : 'false',
        professor_module_management_enabled: form.querySelector('input[name="professor_module_management_enabled"]')?.checked ? 'true' : 'false',
    };

    fetch('/admin/settings/professor-features', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Professor settings saved:', data);
        if (data.success) {
            showAlert('Professor feature settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving professor settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving professor settings:', error);
        showAlert('Error saving professor settings: ' + error.message, 'danger');
    });
}

// Meeting Whitelist Functions
function selectAllProfessors() {
    const checkboxes = document.querySelectorAll('input[name="whitelist_professors[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
}

function clearAllProfessors() {
    const checkboxes = document.querySelectorAll('input[name="whitelist_professors[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="whitelist_professors[]"]:checked');
    const count = checkboxes.length;
    document.getElementById('selectedCount').textContent = count;
}

function saveMeetingWhitelist() {
    const form = document.getElementById('meetingWhitelistForm');
    const formData = new FormData(form);
    
    fetch('/admin/settings/meeting-whitelist', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Meeting whitelist saved successfully!', 'success');
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('meetingWhitelistModal'));
            modal.hide();
        } else {
            showAlert(data.error || 'Error saving whitelist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving whitelist:', error);
        showAlert('Error saving whitelist: ' + error.message, 'danger');
    });
}

// Add event listeners for checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    // Update count when checkboxes change
    const checkboxes = document.querySelectorAll('input[name="whitelist_professors[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Update count for module management checkboxes
    const moduleCheckboxes = document.querySelectorAll('input[name="whitelist_module_professors[]"]');
    moduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedModuleCount);
    });
});

// Module Management Whitelist Functions
function selectAllModuleProfessors() {
    const checkboxes = document.querySelectorAll('input[name="whitelist_module_professors[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateSelectedModuleCount();
}

function clearAllModuleProfessors() {
    const checkboxes = document.querySelectorAll('input[name="whitelist_module_professors[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateSelectedModuleCount();
}

function updateSelectedModuleCount() {
    const checkboxes = document.querySelectorAll('input[name="whitelist_module_professors[]"]:checked');
    const count = checkboxes.length;
    document.getElementById('selectedModuleCount').textContent = count;
}

function saveModuleManagementWhitelist() {
    const form = document.getElementById('moduleManagementWhitelistForm');
    const formData = new FormData(form);
    
    fetch('/admin/settings/module-management-whitelist', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Module management whitelist saved successfully!', 'success');
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('moduleManagementWhitelistModal'));
            modal.hide();
        } else {
            showAlert(data.error || 'Error saving whitelist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving whitelist:', error);
        showAlert('Error saving whitelist: ' + error.message, 'danger');
    });
}

// Director Settings Functions
function loadDirectorSettings() {
    fetch('/admin/settings/director-features', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Director settings loaded:', data);
        if (data.success && data.settings) {
            // Set checkbox values based on loaded settings
            Object.keys(data.settings).forEach(key => {
                const checkbox = document.querySelector(`input[name="${key}"]`);
                if (checkbox) {
                    checkbox.checked = data.settings[key] === 'true' || data.settings[key] === true;
                }
            });
        }
    })
    .catch(error => {
        console.error('Error loading director settings:', error);
        // Set default values if loading fails
        const checkboxes = document.querySelectorAll('#directorFeaturesForm input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true; // Default to enabled
        });
    });
}

function saveDirectorSettings() {
    const form = document.getElementById('directorFeaturesForm');
    const formData = new FormData(form);
    
    // Add unchecked checkboxes as '0', checked as '1'
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            formData.set(checkbox.name, '0');
        } else {
            formData.set(checkbox.name, '1');
        }
    });

    fetch('/admin/settings/director-features', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Director settings saved:', data);
        if (data.success) {
            showAlert('Director feature settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving director settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving director settings:', error);
        showAlert('Error saving director settings: ' + error.message, 'danger');
    });
}

function saveReferralSettings() {
    const form = document.getElementById('referralSettingsForm');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Get checkbox values
    const referralEnabled = document.getElementById('referralEnabled').checked;
    const referralRequired = document.getElementById('referralRequired').checked;
    
    // Show loading state
    const originalContent = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    // Prepare data
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('referral_enabled', referralEnabled ? '1' : '0');
    formData.append('referral_required', referralRequired ? '1' : '0');

    fetch('/admin/settings/referral', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Referral settings saved:', data);
        if (data.success) {
            showAlert('Referral settings saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving referral settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving referral settings:', error);
        showAlert('Error saving referral settings: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalContent;
    });
}

function savePaymentTermsSettings() {
    const form = document.getElementById('paymentTermsForm');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Get form values
    const paymentTermsContent = document.getElementById('paymentTermsContent').value;
    const paymentAbortTermsContent = document.getElementById('paymentAbortTermsContent').value;
    const enablePaymentTerms = document.getElementById('enablePaymentTerms').checked;
    
    // Show loading state
    const originalContent = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    // Prepare data
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('payment_terms_content', paymentTermsContent);
    formData.append('payment_abort_terms_content', paymentAbortTermsContent);
    formData.append('enable_payment_terms', enablePaymentTerms ? '1' : '0');

    fetch('/admin/settings/payment-terms', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Payment terms saved:', data);
        if (data.success) {
            showAlert('Payment terms and conditions saved successfully!', 'success');
        } else {
            showAlert(data.error || 'Error saving payment terms', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving payment terms:', error);
        showAlert('Error saving payment terms: ' + error.message, 'danger');
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalContent;
    });
}

function addSystemFields() {
    const container = document.getElementById('requirementsContainer');
    
    // Create a header for system fields
    const systemHeader = document.createElement('div');
    systemHeader.className = 'system-fields-header border rounded p-3 mb-3';
    systemHeader.style.background = 'linear-gradient(135deg, #007bff, #0056b3)';
    systemHeader.style.color = 'white';
    systemHeader.innerHTML = `
        <h6 class="mb-0">
            <i class="fas fa-cogs me-2"></i>System/Predefined Fields
            <small class="ms-2 opacity-75">(Cannot be deleted, only toggled)</small>
        </h6>
    `;
    container.appendChild(systemHeader);
    
    // Define system fields
    const systemFields = [
        {
            field_name: 'firstname',
            field_label: 'First Name',
            field_type: 'text',
            is_required: true,
            is_active: true,
            program_type: 'both',
            field_options: [],
            is_system: true
        },
        {
            field_name: 'lastname',
            field_label: 'Last Name',
            field_type: 'text',
            is_required: true,
            is_active: true,
            program_type: 'both',
            field_options: [],
            is_system: true
        },
        {
            field_name: 'education_level',
            field_label: 'Education Level',
            field_type: 'select',
            is_required: true,
            is_active: true,
            program_type: 'both',
            field_options: ['High School', 'Undergraduate', 'Graduate'],
            is_system: true
        },
        {
            field_name: 'program_id',
            field_label: 'Program',
            field_type: 'select',
            is_required: true,
            is_active: true,
            program_type: 'both',
            field_options: ['Dynamic - Loaded from Programs'],
            is_system: true
        },
        {
            field_name: 'start_date',
            field_label: 'Start Date',
            field_type: 'date',
            is_required: false,
            is_active: true,
            program_type: 'both',
            field_options: [],
            is_system: true
        }
    ];
    
    // Add each system field
    systemFields.forEach(field => {
        addSystemField(field);
    });
}

function addSystemField(data) {
    const container = document.getElementById('requirementsContainer');
    
    const systemFieldDiv = document.createElement('div');
    systemFieldDiv.className = 'requirement-item system-field border rounded p-3 mb-3';
    systemFieldDiv.style.borderColor = '#007bff';
    systemFieldDiv.style.background = '#f8f9ff';
    
    const optionsHtml = data.field_options && data.field_options.length > 0 
        ? data.field_options.map(opt => `<span class="badge bg-light text-dark me-1">${opt}</span>`).join('')
        : '<span class="text-muted">No options</span>';
    
    systemFieldDiv.innerHTML = `
        <div class="row w-100">
            <div class="col-md-3">
                <label class="form-label fw-bold">Field Name</label>
                <div class="form-control-plaintext bg-light rounded px-3 py-2">
                    <i class="fas fa-lock me-2 text-primary"></i>${data.field_name}
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Field Label</label>
                <div class="form-control-plaintext bg-light rounded px-3 py-2">${data.field_label}</div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Type</label>
                <div class="form-control-plaintext bg-light rounded px-3 py-2">
                    <span class="badge bg-primary">${data.field_type}</span>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Options</label>
                <div class="form-control-plaintext bg-light rounded px-3 py-2" style="min-height: 38px;">
                    ${optionsHtml}
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex flex-column gap-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input system-field-toggle" type="checkbox" 
                               ${data.is_active ? 'checked' : ''} 
                               data-field="${data.field_name}" data-type="active">
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input system-field-toggle" type="checkbox" 
                               ${data.is_required ? 'checked' : ''} 
                               data-field="${data.field_name}" data-type="required">
                        <label class="form-check-label">Required</label>
                    </div>
                    <select class="form-select form-select-sm system-field-program" 
                            data-field="${data.field_name}">
                        <option value="both" ${data.program_type === 'both' ? 'selected' : ''}>Both</option>
                        <option value="full" ${data.program_type === 'full' ? 'selected' : ''}>Full Only</option>
                        <option value="modular" ${data.program_type === 'modular' ? 'selected' : ''}>Modular Only</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(systemFieldDiv);
}

// Plan Settings Functions
function loadPlanSettings() {
    fetch('/admin/settings/plan-settings')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPlanSettings(data.plans);
            } else {
                console.error('Failed to load plan settings:', data.error);
            }
        })
        .catch(error => {
            console.error('Error loading plan settings:', error);
        });
}

// Plan Settings Functions
function loadPlanSettings() {
    console.log('Loading plan settings...');
    
    fetch('/admin/settings/plan-settings')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Plan settings loaded:', data);
            renderPlanSettings(data.plans || []);
        })
        .catch(error => {
            console.error('Error loading plan settings:', error);
            showAlert('Error loading plan settings: ' + error.message, 'danger');
        });
}






function renderPlanSettings(plans) {
  const container = document.getElementById('planSettingsContainer');
  container.innerHTML = plans.map(plan => `
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-header bg-light">
          <h6 class="mb-0">${plan.plan_name}</h6>
          <small class="text-muted">${plan.description || 'No description'}</small>
        </div>
        <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="sync_${plan.plan_id}" 
                                   ${plan.enable_synchronous ? 'checked' : ''}>
                            <label class="form-check-label" for="sync_${plan.plan_id}">
                                <i class="fas fa-video me-2"></i>Enable Synchronous Learning
                            </label>
                            <small class="form-text text-muted d-block">Live classes with real-time interaction</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="async_${plan.plan_id}" 
                                   ${plan.enable_asynchronous ? 'checked' : ''}>
                            <label class="form-check-label" for="async_${plan.plan_id}">
                                <i class="fas fa-play-circle me-2"></i>Enable Asynchronous Learning
                            </label>
                            <small class="form-text text-muted d-block">Self-paced learning with recorded content</small>
                        </div>
                    </div>
      </div>
    </div>
  `).join('');
}








function refreshPlanSettings() {
    loadPlanSettings();
}

function savePlanSettings() {
    console.log('Saving plan settings...');
    
    const saveButton = document.querySelector('button[onclick="savePlanSettings()"]');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    saveButton.disabled = true;
    
    const plans = [];
    const planCards = document.querySelectorAll('#planSettingsContainer .card');
    
    if (planCards.length === 0) {
        console.error('No plan cards found');
        showAlert('No plan settings found to save. Please refresh the page.', 'warning');
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
        return;
    }
    
    planCards.forEach(card => {
        const syncCheckbox = card.querySelector('[id^="sync_"]');
        const asyncCheckbox = card.querySelector('[id^="async_"]');
        
        if (syncCheckbox && asyncCheckbox) {
            const planId = syncCheckbox.id.split('_')[1];
            
            plans.push({
                plan_id: parseInt(planId),
                enable_synchronous: syncCheckbox.checked,
                enable_asynchronous: asyncCheckbox.checked
            });
        }
    });
    
    console.log('Plans to save:', plans);
    
    fetch('/admin/settings/plan-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ plans: plans })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Save response:', data);
        if (data.success) {
            showAlert(data.message || 'Plan settings saved successfully!', 'success');
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error saving plan settings:', error);
        showAlert('Error saving plan settings: ' + error.message, 'danger');
    })
    .finally(() => {
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

// Payment Methods functionality
let paymentMethods = [];
let editingPaymentMethodId = null;

function loadPaymentMethods() {
    console.log('Loading payment methods...');
    
    const tableBody = document.getElementById('paymentMethodsTableBody');
    if (!tableBody) {
        console.error('Payment methods table body not found');
        return;
    }
    
    // Show loading state
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading payment methods...</td></tr>';
    
    fetch('/admin/settings/payment-methods/')
        .then(response => {
            console.log('Payment methods response status:', response.status);
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error('Authentication required. Please log in.');
                }
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Payment methods loaded:', data);
            if (data.success) {
                paymentMethods = data.data;
                renderPaymentMethodsTable();
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Failed to load payment methods</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading payment methods:', error);
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Unable to load payment methods: ${error.message}
                    <button class="btn btn-sm btn-outline-warning ms-2" onclick="loadPaymentMethods()">
                        <i class="fas fa-redo me-1"></i>Retry
                    </button>
                </div>
            </td></tr>`;
        });
}

function renderPaymentMethodsTable() {
    const tableBody = document.getElementById('paymentMethodsTableBody');
    if (!tableBody) return;

    if (paymentMethods.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">No payment methods configured</td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = paymentMethods.map(method => `
        <tr data-id="${method.payment_method_id}">
            <td>
                <span class="drag-handle" style="cursor: move;">⋮⋮</span>
            </td>
            <td>${method.method_name}</td>
            <td>
                <span class="badge badge-${getMethodTypeBadgeClass(method.method_type)}">
                    ${method.method_type.replace('_', ' ').toUpperCase()}
                </span>
            </td>
            <td>
                ${method.qr_code_path ? 
                    `<img src="/storage/${method.qr_code_path}" alt="QR Code" style="width: 40px; height: 40px; object-fit: cover;">` : 
                    '<span class="text-muted">No QR Code</span>'
                }
            </td>
            <td>
                <span class="badge badge-${method.is_enabled ? 'success' : 'secondary'}">
                    ${method.is_enabled ? 'Enabled' : 'Disabled'}
                </span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editPaymentMethod(${method.payment_method_id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePaymentMethod(${method.payment_method_id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');

    // Initialize sortable
    if (window.Sortable) {
        new Sortable(tableBody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                updatePaymentMethodOrder();
            }
        });
    }
}

function getMethodTypeBadgeClass(type) {
    const badgeClasses = {
        'credit_card': 'primary',
        'gcash': 'success',
        'maya': 'info',
        'bank_transfer': 'warning',
        'cash': 'secondary',
        'other': 'dark'
    };
    return badgeClasses[type] || 'secondary';
}

function openAddPaymentMethodModal() {
    editingPaymentMethodId = null;
    document.getElementById('paymentMethodModalTitle').textContent = 'Add Payment Method';
    document.getElementById('paymentMethodForm').reset();
    document.getElementById('currentQrCode').style.display = 'none';
    document.getElementById('removeQrCodeSection').style.display = 'none';
    
    // Clear dynamic fields
    clearDynamicFields();
    
    // Use Bootstrap 5 vanilla JavaScript instead of jQuery
    const modal = new bootstrap.Modal(document.getElementById('paymentMethodModal'));
    modal.show();
}

function editPaymentMethod(id) {
    const method = paymentMethods.find(m => m.payment_method_id == id);
    if (!method) return;

    editingPaymentMethodId = id;
    document.getElementById('paymentMethodModalTitle').textContent = 'Edit Payment Method';
    
    // Populate form
    document.getElementById('method_name').value = method.method_name;
    document.getElementById('method_type').value = method.method_type;
    document.getElementById('description').value = method.description || '';
    document.getElementById('instructions').value = method.instructions || '';
    document.getElementById('is_enabled').checked = method.is_enabled;

    // Handle QR code display
    const currentQrCode = document.getElementById('currentQrCode');
    const removeQrCodeSection = document.getElementById('removeQrCodeSection');
    
    if (method.qr_code_path) {
        currentQrCode.style.display = 'block';
        currentQrCode.innerHTML = `
            <label class="form-label">Current QR Code:</label>
            <div>
                <img src="/storage/${method.qr_code_path}" alt="Current QR Code" style="max-width: 150px; height: auto;">
            </div>
        `;
        removeQrCodeSection.style.display = 'block';
    } else {
        currentQrCode.style.display = 'none';
        removeQrCodeSection.style.display = 'none';
    }

    // Load dynamic fields for this payment method
    loadDynamicFields(id);

    // Use Bootstrap 5 vanilla JavaScript instead of jQuery
    const modal = new bootstrap.Modal(document.getElementById('paymentMethodModal'));
    modal.show();
}

function savePaymentMethod() {
    const form = document.getElementById('paymentMethodForm');
    const formData = new FormData(form);
    
    // Collect dynamic fields data
    const dynamicFieldsData = [];
    const fieldElements = document.querySelectorAll('.dynamic-field-item');
    
    fieldElements.forEach(fieldElement => {
        const fieldId = fieldElement.getAttribute('data-field-id');
        const fieldData = {
            label: fieldElement.querySelector(`input[name="fields[${fieldId}][label]"]`)?.value || '',
            type: fieldElement.querySelector(`select[name="fields[${fieldId}][type]"]`)?.value || '',
            placeholder: fieldElement.querySelector(`input[name="fields[${fieldId}][placeholder]"]`)?.value || '',
            order: parseInt(fieldElement.querySelector(`input[name="fields[${fieldId}][order]"]`)?.value) || 1,
            required: fieldElement.querySelector(`input[name="fields[${fieldId}][required]"]`)?.checked || false,
            validation_sensitive: fieldElement.querySelector(`input[name="fields[${fieldId}][validation_sensitive]"]`)?.checked || false
        };
        
        // Handle select options
        if (fieldData.type === 'select') {
            const optionInputs = fieldElement.querySelectorAll(`input[name="fields[${fieldId}][options][]"]`);
            fieldData.options = Array.from(optionInputs).map(input => input.value).filter(val => val.trim() !== '');
        }
        
        // Handle file upload settings
        if (fieldData.type === 'file') {
            fieldData.allowed_types = fieldElement.querySelector(`input[name="fields[${fieldId}][allowed_types]"]`)?.value || 'jpg,png,pdf';
            fieldData.max_size = parseInt(fieldElement.querySelector(`input[name="fields[${fieldId}][max_size]"]`)?.value) || 5;
        }
        
        // Only add fields that have a label and type
        if (fieldData.label && fieldData.type) {
            dynamicFieldsData.push(fieldData);
        }
    });
    
    // Add dynamic fields to form data
    formData.append('dynamic_fields', JSON.stringify(dynamicFieldsData));
    
    const url = editingPaymentMethodId ? 
        `/admin/settings/payment-methods/${editingPaymentMethodId}` : 
        '/admin/settings/payment-methods/';
    
    const method = editingPaymentMethodId ? 'PUT' : 'POST';

    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    if (editingPaymentMethodId) {
        formData.append('_method', 'PUT');
    }

    const saveButton = document.getElementById('savePaymentMethodBtn');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    saveButton.disabled = true;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(async response => {
        let data;
        try {
            const text = await response.text();
            
            // Check if response is HTML (error page) instead of JSON
            if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
                console.error('Server returned HTML instead of JSON:', text.substring(0, 200));
                showAlert('Server error occurred. Please check server logs.', 'danger');
                return;
            }
            
            data = JSON.parse(text);
        } catch (jsonError) {
            console.error('JSON parse error:', jsonError);
            console.error('Raw response:', text.substring(0, 500));
            showAlert('Server returned invalid response format.', 'danger');
            return;
        }
        
        if (response.ok && data.success) {
            showAlert(data.message || 'Payment method saved successfully', 'success');
            
            // Use Bootstrap 5 vanilla JavaScript instead of jQuery
            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentMethodModal'));
            modal.hide();
            loadPaymentMethods();
        } else {
            showAlert(data.error || data.message || 'Failed to save payment method', 'danger');
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        showAlert('Network error occurred. Please try again.', 'danger');
    })
    .finally(() => {
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

function deletePaymentMethod(id) {
    if (!confirm('Are you sure you want to delete this payment method?')) return;

    fetch(`/admin/settings/payment-methods/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            loadPaymentMethods();
        } else {
            showAlert(data.error || 'Failed to delete payment method', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to delete payment method', 'danger');
    });
}

function updatePaymentMethodOrder() {
    const rows = document.querySelectorAll('#paymentMethodsTableBody tr[data-id]');
    const paymentMethodsOrder = Array.from(rows).map((row, index) => ({
        id: parseInt(row.dataset.id),
        sort_order: index + 1
    }));

    fetch('/admin/settings/payment-methods/reorder', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            payment_methods: paymentMethodsOrder
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Payment method order updated', 'success');
            loadPaymentMethods();
        }
    })
    .catch(error => {
        console.error('Error updating order:', error);
    });
}

// Dynamic Payment Fields Management
let fieldCounter = 0;
let currentDynamicFields = [];

function addNewPaymentField() {
    fieldCounter++;
    const fieldId = `field_${fieldCounter}`;
    
    const fieldHtml = `
        <div class="dynamic-field-item border rounded p-3 mb-3 bg-white" data-field-id="${fieldId}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0 text-primary">
                    <i class="bi bi-input-cursor"></i> Payment Field #${fieldCounter}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePaymentField('${fieldId}')">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="fields[${fieldId}][label]" 
                               placeholder="e.g., Reference Number, Account Name" required>
                        <small class="text-muted">This will be shown to students</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Field Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="fields[${fieldId}][type]" onchange="handleFieldTypeChange('${fieldId}', this.value)" required>
                            <option value="">Select Type</option>
                            <option value="text">Text Input</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="tel">Phone Number</option>
                            <option value="date">Date</option>
                            <option value="textarea">Long Text (Textarea)</option>
                            <option value="select">Dropdown/Select</option>
                            <option value="file">File Upload</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Placeholder Text</label>
                        <input type="text" class="form-control" name="fields[${fieldId}][placeholder]" 
                               placeholder="Hint text for students">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Field Order</label>
                        <input type="number" class="form-control" name="fields[${fieldId}][order]" 
                               value="${fieldCounter}" min="1">
                    </div>
                </div>
            </div>
            
            <!-- Options for select fields -->
            <div class="select-options-section" id="selectOptions_${fieldId}" style="display: none;">
                <div class="mb-3">
                    <label class="form-label">Dropdown Options</label>
                    <div class="options-container" id="optionsContainer_${fieldId}">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="fields[${fieldId}][options][]" placeholder="Option 1">
                            <button type="button" class="btn btn-outline-success" onclick="addSelectOption('${fieldId}')">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- File upload settings -->
            <div class="file-settings-section" id="fileSettings_${fieldId}" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Allowed File Types</label>
                            <input type="text" class="form-control" name="fields[${fieldId}][allowed_types]" 
                                   placeholder="e.g., jpg,png,pdf" value="jpg,png,pdf">
                            <small class="text-muted">Comma-separated file extensions</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Max File Size (MB)</label>
                            <input type="number" class="form-control" name="fields[${fieldId}][max_size]" 
                                   value="5" min="1" max="50">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fields[${fieldId}][required]" 
                               id="required_${fieldId}" checked>
                        <label class="form-check-label" for="required_${fieldId}">
                            Required Field
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fields[${fieldId}][validation_sensitive]" 
                               id="validation_${fieldId}">
                        <label class="form-check-label" for="validation_${fieldId}">
                            Include in Payment Validation
                        </label>
                        <small class="text-muted d-block">If checked, this field can be marked for redo when rejecting payments</small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Hide the "no fields" message
    document.getElementById('noFieldsMessage').style.display = 'none';
    
    // Add the field to the container
    document.getElementById('dynamicFieldsContainer').insertAdjacentHTML('beforeend', fieldHtml);
    
    // Add to tracking array
    currentDynamicFields.push(fieldId);
}

function removePaymentField(fieldId) {
    if (confirm('Are you sure you want to remove this field?')) {
        const fieldElement = document.querySelector(`[data-field-id="${fieldId}"]`);
        if (fieldElement) {
            fieldElement.remove();
        }
        
        // Remove from tracking array
        currentDynamicFields = currentDynamicFields.filter(id => id !== fieldId);
        
        // Show "no fields" message if no fields left
        if (currentDynamicFields.length === 0) {
            document.getElementById('noFieldsMessage').style.display = 'block';
        }
    }
}

function handleFieldTypeChange(fieldId, fieldType) {
    const selectOptionsSection = document.getElementById(`selectOptions_${fieldId}`);
    const fileSettingsSection = document.getElementById(`fileSettings_${fieldId}`);
    
    // Hide all optional sections first
    selectOptionsSection.style.display = 'none';
    fileSettingsSection.style.display = 'none';
    
    // Show relevant section based on field type
    if (fieldType === 'select') {
        selectOptionsSection.style.display = 'block';
    } else if (fieldType === 'file') {
        fileSettingsSection.style.display = 'block';
    }
}

function addSelectOption(fieldId) {
    const container = document.getElementById(`optionsContainer_${fieldId}`);
    const optionCount = container.children.length + 1;
    
    const optionHtml = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="fields[${fieldId}][options][]" placeholder="Option ${optionCount}">
            <button type="button" class="btn btn-outline-danger" onclick="removeSelectOption(this)">
                <i class="bi bi-dash"></i>
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', optionHtml);
}

function removeSelectOption(button) {
    button.closest('.input-group').remove();
}

function loadDynamicFields(paymentMethodId) {
    if (!paymentMethodId) {
        clearDynamicFields();
        return;
    }
    
    fetch(`/admin/settings/payment-methods/${paymentMethodId}/fields`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.fields) {
                populateDynamicFields(data.fields);
            } else {
                clearDynamicFields();
            }
        })
        .catch(error => {
            console.error('Error loading dynamic fields:', error);
            clearDynamicFields();
        });
}

function populateDynamicFields(fields) {
    clearDynamicFields();
    
    if (fields && fields.length > 0) {
        fields.forEach(field => {
            addNewPaymentField();
            const currentFieldId = `field_${fieldCounter}`;
            
            // Populate field data
            const fieldContainer = document.querySelector(`[data-field-id="${currentFieldId}"]`);
            if (fieldContainer) {
                fieldContainer.querySelector(`input[name="fields[${currentFieldId}][label]"]`).value = field.label || '';
                fieldContainer.querySelector(`select[name="fields[${currentFieldId}][type]"]`).value = field.type || '';
                fieldContainer.querySelector(`input[name="fields[${currentFieldId}][placeholder]"]`).value = field.placeholder || '';
                fieldContainer.querySelector(`input[name="fields[${currentFieldId}][order]"]`).value = field.order || fieldCounter;
                fieldContainer.querySelector(`input[name="fields[${currentFieldId}][required]"]`).checked = field.required || false;
                fieldContainer.querySelector(`input[name="fields[${currentFieldId}][validation_sensitive]"]`).checked = field.validation_sensitive || false;
                
                // Handle field type specific data
                handleFieldTypeChange(currentFieldId, field.type);
                
                if (field.type === 'select' && field.options) {
                    const optionsContainer = document.getElementById(`optionsContainer_${currentFieldId}`);
                    optionsContainer.innerHTML = '';
                    field.options.forEach((option, index) => {
                        const optionHtml = `
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="fields[${currentFieldId}][options][]" 
                                       value="${option}" placeholder="Option ${index + 1}">
                                <button type="button" class="btn btn-outline-danger" onclick="removeSelectOption(this)">
                                    <i class="bi bi-dash"></i>
                                </button>
                            </div>
                        `;
                        optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
                    });
                }
                
                if (field.type === 'file') {
                    if (field.allowed_types) {
                        fieldContainer.querySelector(`input[name="fields[${currentFieldId}][allowed_types]"]`).value = field.allowed_types;
                    }
                    if (field.max_size) {
                        fieldContainer.querySelector(`input[name="fields[${currentFieldId}][max_size]"]`).value = field.max_size;
                    }
                }
            }
        });
    }
}

function clearDynamicFields() {
    document.getElementById('dynamicFieldsContainer').innerHTML = `
        <div class="text-center text-muted py-3" id="noFieldsMessage">
            <i class="bi bi-info-circle"></i>
            No custom fields configured. Click "Add Field" to create dynamic fields for this payment method.
        </div>
    `;
    currentDynamicFields = [];
    fieldCounter = 0;
}

// Education Level Management Functions
function addEducationLevel() {
    document.getElementById('educationLevelModalTitle').textContent = 'Add Education Level';
    document.getElementById('educationLevelForm').reset();
    editingEducationLevelId = null;
    
    // Clear and add default document requirements for new education level
    document.getElementById('fileRequirementsContainer').innerHTML = '';
    
    // Add default document requirements based on common education level needs
    addDocumentRequirement({
        document_type: 'school_id',
        file_type: 'image',
        is_required: true,
        available_full_plan: true,
        available_modular_plan: true
    });
    
    const modal = new bootstrap.Modal(document.getElementById('educationLevelModal'));
    modal.show();
}

function addDocumentRequirement(data = {}) {
    const container = document.getElementById('fileRequirementsContainer');
    const newRequirement = document.createElement('div');
    newRequirement.className = 'document-requirement-item mb-3 p-3 border rounded bg-light';
    
    const documentTypes = [
        { value: 'school_id', label: 'School ID' },
        { value: 'diploma', label: 'Diploma' },
        { value: 'Cert_of_Grad', label: 'Certificate of Graduation' },
        { value: 'TOR', label: 'Transcript of Records (TOR)' },
        { value: 'PSA', label: 'PSA Birth Certificate' },
        { value: 'good_moral', label: 'Good Moral Certificate' },
        { value: 'Course_Cert', label: 'Course Certificate' },
        { value: 'photo_2x2', label: 'Photo 2x2' },
        { value: 'custom', label: 'Custom Document' }
    ];
    
    const fileTypes = [
        { value: 'image', label: 'Image Only (JPG, PNG, GIF)' },
        { value: 'pdf', label: 'PDF Only' },
        { value: 'document', label: 'Document (PDF, DOC, DOCX)' },
        { value: 'any', label: 'Any File Type' }
    ];
    
    let documentTypeOptions = '';
    documentTypes.forEach(type => {
        const selected = data.document_type === type.value ? 'selected' : '';
        documentTypeOptions += `<option value="${type.value}" ${selected}>${type.label}</option>`;
    });
    
    let fileTypeOptions = '';
    fileTypes.forEach(type => {
        const selected = data.file_type === type.value || (!data.file_type && type.value === 'any') ? 'selected' : '';
        fileTypeOptions += `<option value="${type.value}" ${selected}>${type.label}</option>`;
    });
    
    newRequirement.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label small">Document Type</label>
                <select class="form-control file-req-document-type" onchange="handleDocumentTypeChange(this)">
                    ${documentTypeOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Custom Name</label>
                <input type="text" class="form-control file-req-custom-name" placeholder="Custom name" value="${data.custom_name || ''}" ${data.document_type !== 'custom' ? 'disabled' : ''}>
            </div>
            <div class="col-md-2">
                <label class="form-label small">File Type</label>
                <select class="form-control file-req-file-type">
                    ${fileTypeOptions}
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small">Required</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input file-req-required" ${data.is_required !== false ? 'checked' : ''}>
                    <label class="form-check-label">Yes</label>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label small">Full Plan</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input file-req-full-plan" ${data.available_full_plan !== false ? 'checked' : ''}>
                    <label class="form-check-label">Yes</label>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label small">Modular</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input file-req-modular-plan" ${data.available_modular_plan !== false ? 'checked' : ''}>
                    <label class="form-check-label">Yes</label>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label small">Actions</label>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeDocumentRequirement(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRequirement);
}

function handleDocumentTypeChange(selectElement) {
    const customNameInput = selectElement.closest('.row').querySelector('.file-req-custom-name');
    if (selectElement.value === 'custom') {
        customNameInput.disabled = false;
        customNameInput.focus();
    } else {
        customNameInput.disabled = true;
        customNameInput.value = '';
    }
}

function removeDocumentRequirement(button) {
    button.closest('.document-requirement-item').remove();
}

// Legacy function for backward compatibility
function addFileRequirement(data = {}) {
    return addDocumentRequirement(data);
}

function removeFileRequirement(button) {
    return removeDocumentRequirement(button);
}

function loadEducationLevels() {
    console.log('Loading education levels...');
    
    const container = document.getElementById('educationLevelsContainer');
    if (!container) {
        console.error('Education levels container not found');
        return;
    }
    
    // Show loading state
    container.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading education levels...</div>';
    
    fetch('/admin/settings/education-levels')
        .then(response => {
            console.log('Education levels response status:', response.status);
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error('Authentication required. Please log in.');
                }
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Education levels loaded:', data);
            if (data.success) {
                renderEducationLevels(data.data || []);
            } else {
                showAlert('Failed to load education levels', 'danger');
            }
        })
        .catch(error => {
            console.error('Error loading education levels:', error);
            container.innerHTML = `<div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Unable to load education levels: ${error.message}
                <button class="btn btn-sm btn-outline-warning ms-2" onclick="loadEducationLevels()">
                    <i class="fas fa-redo me-1"></i>Retry
                </button>
            </div>`;
        });
}

function renderEducationLevels(levels) {
    // Store in global variable for edit/delete functions
    educationLevels = levels || [];
    
    const container = document.getElementById('educationLevelsContainer');
    
    if (!levels || levels.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <p class="text-muted">No education levels configured. Click "Add Education Level" to get started.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    levels.forEach((level, index) => {
        const fileRequirements = level.file_requirements || [];
        let fileReqsHtml = '';
        
        if (Array.isArray(fileRequirements) && fileRequirements.length > 0) {
            fileReqsHtml = fileRequirements.map(req => {
                const displayName = req.custom_name || req.field_name || req.document_type;
                const fileTypeIcon = req.file_type === 'image' ? 'fa-image' : 
                                   req.file_type === 'pdf' ? 'fa-file-pdf' : 
                                   req.file_type === 'document' ? 'fa-file-word' : 'fa-file';
                const badgeClass = req.is_required ? 'primary' : 'secondary';
                const planInfo = [];
                if (req.available_full_plan) planInfo.push('Full');
                if (req.available_modular_plan) planInfo.push('Modular');
                
                return `
                    <span class="badge bg-${badgeClass} me-1 mb-1" title="${displayName} - ${req.file_type} files - Available for: ${planInfo.join(', ')}">
                        <i class="fas ${fileTypeIcon} me-1"></i>${displayName}
                        ${req.is_required ? '<i class="fas fa-asterisk ms-1" style="font-size: 8px;"></i>' : ''}
                    </span>
                `;
            }).join('');
        } else if (typeof fileRequirements === 'object' && Object.keys(fileRequirements).length > 0) {
            // Handle legacy format
            fileReqsHtml = Object.entries(fileRequirements).map(([fieldName, config]) => `
                <span class="badge bg-${config.required ? 'primary' : 'secondary'} me-1 mb-1">
                    ${fieldName}
                    <small class="ms-1">(${config.type || 'file'})</small>
                </span>
            `).join('');
        }
        
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h6 class="mb-1">${level.level_name}</h6>
                            <small class="text-muted">Order: ${level.level_order || 'N/A'}</small>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1">
                                <span class="badge bg-${level.available_full_plan ? 'success' : 'light text-dark'} me-1">
                                    Full Plan: ${level.available_full_plan ? 'Enabled' : 'Disabled'}
                                </span>
                            </div>
                            <div>
                                <span class="badge bg-${level.available_modular_plan ? 'success' : 'light text-dark'}">
                                    Modular Plan: ${level.available_modular_plan ? 'Enabled' : 'Disabled'}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1">
                                <small class="text-muted d-block">Document Requirements:</small>
                                ${fileReqsHtml || '<span class="text-muted">No document requirements</span>'}
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editEducationLevel(${level.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteEducationLevel(${level.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function saveEducationLevel() {
    const form = document.getElementById('educationLevelForm');
    const formData = new FormData(form);

    // Collect document requirements
    const documentRequirements = [];
    document.querySelectorAll('.document-requirement-item').forEach(item => {
        const documentType = item.querySelector('.file-req-document-type').value;
        const customName = item.querySelector('.file-req-custom-name').value.trim();
        const fileType = item.querySelector('.file-req-file-type').value;
        
        // Use custom name for custom document type, otherwise use document type
        const fieldName = documentType === 'custom' && customName ? customName : documentType;
        
        if (fieldName) {
            documentRequirements.push({
                field_name: fieldName,
                document_type: documentType,
                file_type: fileType,
                custom_name: documentType === 'custom' ? customName : null,
                is_required: item.querySelector('.file-req-required').checked,
                available_full_plan: item.querySelector('.file-req-full-plan').checked,
                available_modular_plan: item.querySelector('.file-req-modular-plan').checked
            });
        }
    });

    // Also collect legacy file requirements for backward compatibility
    document.querySelectorAll('.file-requirement-item').forEach(item => {
        const name = item.querySelector('.file-req-name')?.value.trim();
        if (name) {
            documentRequirements.push({
                field_name: name,
                field_type: item.querySelector('.file-req-type')?.value || 'file',
                file_type: 'any', // Default for legacy requirements
                is_required: item.querySelector('.file-req-required')?.checked || false,
                available_full_plan: item.querySelector('.file-req-full-plan')?.checked || true,
                available_modular_plan: item.querySelector('.file-req-modular-plan')?.checked || true
            });
        }
    });

    // Prepare data as JSON object
    const data = {
        level_name: formData.get('level_name'),
        description: formData.get('description'),
        is_active: formData.get('is_active') === '1',
        file_requirements: documentRequirements
    };

    // Add ID for updates
    if (editingEducationLevelId) {
        data.id = editingEducationLevelId;
    }
    
    const saveButton = document.getElementById('saveEducationLevelBtn');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    saveButton.disabled = true;

    // Determine method and URL
    const method = editingEducationLevelId ? 'PUT' : 'POST';
    const url = editingEducationLevelId ? 
        `/admin/settings/education-levels/${editingEducationLevelId}` : 
        '/admin/settings/education-levels';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message || 'Education level saved successfully', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('educationLevelModal'));
            modal.hide();
            loadEducationLevels();
        } else {
            showAlert(data.error || 'Failed to save education level', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving education level:', error);
        showAlert('Failed to save education level', 'danger');
    })
    .finally(() => {
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

function refreshEducationLevels() {
    loadEducationLevels();
}

function editEducationLevel(id) {
    console.log('Editing education level:', id);
    
    // Find the education level data
    const educationLevel = educationLevels.find(level => level.id == id);
    if (!educationLevel) {
        console.error('Education level not found with ID:', id);
        console.log('Available education levels:', educationLevels);
        showAlert('Education level not found', 'danger');
        return;
    }
    
    // Set modal title
    document.getElementById('educationLevelModalTitle').textContent = 'Edit Education Level';
    
    // Populate form fields
    document.getElementById('level_name').value = educationLevel.level_name || '';
    document.getElementById('description').value = educationLevel.level_description || educationLevel.description || '';
    document.getElementById('is_active').checked = educationLevel.is_active;
    
    // Populate document requirements
    const container = document.getElementById('fileRequirementsContainer');
    container.innerHTML = '';
    
    // Handle file_requirements - it might be a string (JSON) or an array
    let fileRequirements = educationLevel.file_requirements;
    if (typeof fileRequirements === 'string') {
        try {
            fileRequirements = JSON.parse(fileRequirements);
        } catch (e) {
            console.warn('Failed to parse file_requirements JSON:', e);
            fileRequirements = [];
        }
    }
    
    if (Array.isArray(fileRequirements) && fileRequirements.length > 0) {
        fileRequirements.forEach(req => {
            // Convert legacy format to new format if needed
            const documentData = {
                document_type: req.document_type || 'custom',
                custom_name: req.custom_name || (req.document_type ? null : req.field_name),
                file_type: req.file_type || 'any',
                is_required: req.is_required !== undefined ? req.is_required : req.required,
                available_full_plan: req.available_full_plan !== undefined ? req.available_full_plan : true,
                available_modular_plan: req.available_modular_plan !== undefined ? req.available_modular_plan : true
            };
            addDocumentRequirement(documentData);
        });
    } else {
        // Add default document requirement for new education levels
        addDocumentRequirement({
            document_type: 'school_id',
            file_type: 'image',
            is_required: true,
            available_full_plan: true,
            available_modular_plan: true
        });
    }
    
    // Set editing mode
    editingEducationLevelId = id;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('educationLevelModal'));
    modal.show();
}

function deleteEducationLevel(id) {
    console.log('Deleting education level:', id);
    
    // Find the education level name for confirmation
    const educationLevel = educationLevels.find(level => level.id == id);
    const levelName = educationLevel ? educationLevel.level_name : 'this education level';
    
    if (!confirm(`Are you sure you want to delete "${levelName}"? This action cannot be undone.`)) {
        return;
    }
    
    // Make delete request
    fetch(`/admin/settings/education-levels/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Education level deleted successfully', 'success');
            loadEducationLevels();
        } else {
            showAlert(data.error || 'Failed to delete education level', 'danger');
        }
    })
    .catch(error => {
        console.error('Error deleting education level:', error);
        showAlert('Failed to delete education level', 'danger');
    });
}

function saveEducationLevels() {
    // This would save any pending changes
    showAlert('Education levels configuration saved!', 'success');
}

// Load education levels when the tab is shown
document.addEventListener('DOMContentLoaded', function() {
    const educationLevelsTab = document.getElementById('education-levels-tab');
    if (educationLevelsTab) {
        educationLevelsTab.addEventListener('shown.bs.tab', function() {
            loadEducationLevels();
        });
        
        if (educationLevelsTab.classList.contains('active')) {
            loadEducationLevels();
        }
    }
});

// Load payment methods when the payment methods tab is shown
document.addEventListener('DOMContentLoaded', function() {
    // Load payment methods if we're on the payment methods tab
    const paymentMethodsTab = document.getElementById('payment-methods-tab');
    if (paymentMethodsTab) {
        paymentMethodsTab.addEventListener('shown.bs.tab', function() {
            loadPaymentMethods();
        });
        
        // Also load if payment methods tab is initially active
        if (paymentMethodsTab.classList.contains('active')) {
            loadPaymentMethods();
        }
    }
});

// Sidebar Customization Functions
function applySidebarColors() {
    const sidebarBg = document.querySelector('input[name="sidebar_background_color"]').value;
    const sidebarGradient = document.querySelector('input[name="sidebar_gradient_color"]').value;
    const sidebarText = document.querySelector('input[name="sidebar_text_color"]').value;
    const sidebarHover = document.querySelector('input[name="sidebar_hover_color"]').value;
    const sidebarActiveBg = document.querySelector('input[name="sidebar_active_bg_color"]').value;
    const sidebarActiveText = document.querySelector('input[name="sidebar_active_text_color"]').value;
    const sidebarFooterBg = document.querySelector('input[name="sidebar_footer_bg_color"]').value;
    const sidebarFooterText = document.querySelector('input[name="sidebar_footer_text_color"]').value;
    
    // Apply CSS variables
    document.documentElement.style.setProperty('--sidebar-bg', `linear-gradient(180deg, ${sidebarBg} 0%, ${sidebarGradient} 100%)`);
    document.documentElement.style.setProperty('--sidebar-text', sidebarText);
    document.documentElement.style.setProperty('--sidebar-hover', sidebarHover);
    document.documentElement.style.setProperty('--sidebar-active-bg', `linear-gradient(135deg, ${sidebarActiveBg}, ${sidebarActiveBg})`);
    document.documentElement.style.setProperty('--sidebar-active-text', sidebarActiveText);
    document.documentElement.style.setProperty('--sidebar-footer-bg', `linear-gradient(180deg, ${sidebarFooterBg} 0%, ${sidebarFooterBg} 100%)`);
    document.documentElement.style.setProperty('--sidebar-footer-text', sidebarFooterText);
}

// Add event listeners for sidebar color changes
document.addEventListener('DOMContentLoaded', function() {
    // Apply colors when sidebar tab is shown
    const sidebarTab = document.getElementById('sidebar-tab');
    if (sidebarTab) {
        sidebarTab.addEventListener('shown.bs.tab', function() {
            applySidebarColors();
        });
    }
    
    // Apply colors when color inputs change
    document.querySelectorAll('#sidebarCustomizationForm input[type="color"], #sidebarFooterCustomizationForm input[type="color"]').forEach(input => {
        input.addEventListener('change', applySidebarColors);
    });
});

// Terms and Conditions Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const termsForm = document.getElementById('termsAndConditionsForm');
    if (termsForm) {
        termsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            
            fetch('/admin/settings/terms-conditions', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success alert
                    showAlert('success', 'Terms and conditions updated successfully!');
                } else {
                    showAlert('danger', 'Failed to update terms and conditions: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Network error occurred while saving terms and conditions.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
    
    function showAlert(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert.terms-alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show terms-alert`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert before the form
        const termsCard = document.querySelector('#termsAndConditionsForm').closest('.card');
        termsCard.parentNode.insertBefore(alertDiv, termsCard);
        
        // Auto-hide success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
});
</script>
@endpush
