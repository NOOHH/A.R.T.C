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
                    <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                        <i class="fas fa-user-shield me-2"></i>Admin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="plans-tab" data-bs-toggle="tab" data-bs-target="#plans" type="button" role="tab">
                        <i class="fas fa-graduation-cap me-2"></i>Plans
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
                        </div>
                    </div>

                    {{-- Register Tab --}}
                    <div class="tab-pane fade" id="register" role="tabpanel">
                        <div class="row g-4">
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
                                            <div id="requirementsContainer"></div>
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
                                                <input class="form-check-input" type="checkbox" id="videoUploadEnabled" name="video_upload_enabled" checked>
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
                                                <input class="form-check-input" type="checkbox" id="viewProgramsEnabled" name="view_programs_enabled" checked>
                                                <label class="form-check-label" for="viewProgramsEnabled">
                                                    <strong>View Programs</strong><br>
                                                    <small class="text-muted">Allow professors to view assigned programs</small>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="studentListEnabled" name="student_list_enabled" checked>
                                                <label class="form-check-label" for="studentListEnabled">
                                                    <strong>Student Lists</strong><br>
                                                    <small class="text-muted">Allow professors to view student lists and details</small>
                                                </label>
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
                                    <!-- Plans will be loaded here -->
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
        </div>
    </div>
@endsection
@push('scripts')
<script>
// Global variables
let hasUnsavedChanges = false;

document.addEventListener('DOMContentLoaded', function() {
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

    // ✅ Initialize Sortable for requirements container (drag & drop)
    const requirementsContainer = document.getElementById('requirementsContainer');
    if (requirementsContainer) {
        new Sortable(requirementsContainer, {
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
    
    fetch('/admin/settings/form-requirements')
        .then(response => {
            console.log('Load response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Loaded requirements:', data);
            const container = document.getElementById('requirementsContainer');
            if (!container) {
                console.error('Requirements container not found');
                return;
            }
            
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
            showAlert('Error loading form requirements: ' + error.message, 'danger');
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
                <!-- Field Name (2 cols) -->
                <div class="col-md-2">
                    <label class="form-label">Field Name</label>
                    <input type="text" class="form-control" 
                           name="requirements[${index}][field_name]"
                           value="${data.field_name || ''}" 
                           placeholder="e.g., phone_number">
                </div>
                <!-- Display Label (2 cols) -->
                <div class="col-md-2">
                    <label class="form-label">Display Label</label>
                    <input type="text" class="form-control" 
                           name="requirements[${index}][field_label]"
                           value="${data.field_label || ''}" 
                           placeholder="e.g., Phone Number"
                           style="${data.is_bold ? 'font-weight:bold;' : ''}">
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
    const requirementsContainer = document.getElementById('requirementsContainer');
    const items = requirementsContainer.querySelectorAll('.requirement-item');
    
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
    const formData = new FormData(form);
    
    // Add unchecked checkboxes as false
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            formData.set(checkbox.name, 'false');
        } else {
            formData.set(checkbox.name, 'true');
        }
    });

    fetch('/admin/settings/professor-features', {
        method: 'POST',
        body: formData,
        headers: {
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
</script>
@endpush
