@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Student Settings')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* File Viewer Modal z-index fix */
.modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

.modal-dialog {
    z-index: 10000 !important;
}

/* Ensure modal content is properly displayed */
.modal-content {
    position: relative;
    z-index: 10001 !important;
}

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
    
    .profile-photo-section {
        border: 2px dashed #28a745;
        background-color: #f8fff8;
    }
    
    .profile-photo-section:hover {
        border-color: #1e7e34;
        background-color: #e8f5e8;
    }
    
    /* Profile Upload Container */
    .profile-upload-container {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 1.5rem;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        background: #f8f9fa;
    }
    
    .profile-photo-display {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .profile-photo-display:hover {
        transform: scale(1.05);
    }
    
    .profile-photo-display:hover .profile-overlay {
        opacity: 1;
    }
    
    .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    
    .profile-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        position: relative;
    }
    
    .profile-placeholder i {
        position: absolute;
        font-size: 4rem;
        opacity: 0.3;
    }
    
    .profile-placeholder span {
        z-index: 1;
        font-size: 2rem;
    }
    
    .profile-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: white;
        font-size: 1.5rem;
    }
    
    .profile-upload-info {
        flex: 1;
    }
    
    .btn-upload {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
        display: inline-block;
    }
    
    .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    
    .current-photo-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-view, .btn-remove {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-view {
        background: #17a2b8;
        color: white;
    }
    
    .btn-view:hover {
        background: #138496;
    }
    
    .btn-remove {
        background: #dc3545;
        color: white;
    }
    
    .btn-remove:hover {
        background: #c82333;
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
    
    /* Mobile responsive profile upload */
    @media (max-width: 767px) {
        .profile-upload-container {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .profile-photo-display {
            align-self: center;
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
    
    /* ====== SIDEBAR CUSTOMIZATION STYLES ====== */
    .customization-panel, .preview-panel {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        height: fit-content;
    }
    
    .customization-panel h5, .preview-panel h5 {
        color: #6f42c1;
        font-weight: 600;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    
    .color-control-group {
        margin-bottom: 1.5rem;
    }
    
    .color-control-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .color-input-wrapper {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 0.25rem;
    }
    
    .color-picker {
        width: 50px;
        height: 40px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        background: none;
        padding: 0;
    }
    
    .color-picker::-webkit-color-swatch {
        border: none;
        border-radius: 6px;
    }
    
    .color-text-input {
        flex: 1;
        height: 40px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0 0.75rem;
        font-family: monospace;
        font-size: 0.9rem;
    }
    
    .color-text-input:focus {
        outline: none;
        border-color: #6f42c1;
        box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
    }
    
    .customization-actions {
        border-top: 2px solid #e9ecef;
        padding-top: 1rem;
    }
    
    /* SIDEBAR PREVIEW STYLES */
    .sidebar-preview {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        min-height: 400px;
    }
    
    .preview-sidebar {
        background: var(--preview-primary, #1a1a1a);
        color: var(--preview-text, #e0e0e0);
        border-radius: 8px;
        padding: 1rem;
        width: 100%;
        min-height: 350px;
    }
    
    .preview-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--preview-secondary, #2d2d2d);
        margin-bottom: 1rem;
    }
    
    .preview-avatar-placeholder {
        width: 40px;
        height: 40px;
        background: var(--preview-accent, #3b82f6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        color: white;
    }
    
    .preview-profile-info {
        flex: 1;
    }
    
    .preview-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--preview-text, #e0e0e0);
    }
    
    .preview-role {
        font-size: 0.8rem;
        color: var(--preview-text-muted, #9ca3af);
        opacity: 0.8;
    }
    
    .preview-nav {
        margin-top: 1rem;
    }
    
    .preview-section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--preview-text-muted, #9ca3af);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .preview-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    
    .preview-nav-item:hover {
        background: var(--preview-hover, #374151);
    }
    
    .preview-nav-item.active {
        background: var(--preview-accent, #3b82f6);
        color: white;
    }
    
    .preview-nav-item i {
        width: 16px;
        text-align: center;
    }
    
    /* Responsive adjustments for customization */
    @media (max-width: 768px) {
        .customization-panel, .preview-panel {
            margin-bottom: 1rem;
        }
        
        .color-input-wrapper {
            flex-direction: column;
            align-items: stretch;
        }
        
        .color-picker {
            width: 100%;
            height: 50px;
        }
        
        .customization-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .customization-actions .btn {
            width: 100%;
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

            <!-- Profile Photo Upload Section -->
            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <div class="profile-upload-container">
                    <div class="profile-photo-display">
                        @if(isset($student->profile_photo) && $student->profile_photo)
                            <img id="profilePreview" src="{{ asset('storage/profile-photos/' . $student->profile_photo) }}" alt="Profile Photo" class="profile-image">
                        @else
                            <div id="profilePreview" class="profile-placeholder">
                                <i class="bi bi-person-circle"></i>
                                <span>VD</span>
                            </div>
                        @endif
                        <div class="profile-overlay">
                            <i class="bi bi-camera"></i>
                        </div>
                    </div>
                    <input type="file" 
                           class="form-control d-none" 
                           id="profile_photo" 
                           name="profile_photo" 
                           accept=".jpg,.jpeg,.png"
                           onchange="previewProfilePhoto(this)">
                    <div class="profile-upload-info">
                        <button type="button" class="btn-upload" onclick="document.getElementById('profile_photo').click()">
                            <i class="bi bi-upload me-2"></i>Choose Photo
                        </button>
                        <small class="text-muted">Upload a JPG, JPEG, or PNG image (max 2MB)</small>
                        @if(isset($student->profile_photo) && $student->profile_photo)
                            <div class="current-photo-actions mt-2">
                                <button type="button" class="btn-view" onclick="viewProfilePhoto('{{ $student->profile_photo }}')">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                                <button type="button" class="btn-remove" onclick="removeProfilePhoto()">
                                    <i class="bi bi-trash me-1"></i>Remove
                                </button>
                            </div>
                        @endif
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

    <!-- Sidebar Customization Card -->
    <div class="settings-card">
        <h3><i class="bi bi-palette me-2"></i>Sidebar Customization</h3>
        
        <div class="row">
            <div class="col-md-6">
                <div class="customization-panel">
                    <h5>Color Settings</h5>
                    <form id="sidebarCustomizationForm">
                        @csrf
                        
                        <div class="color-control-group">
                            <label for="primaryColor">Primary Background Color</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="primaryColor" class="color-picker" value="#1a1a1a">
                                <input type="text" id="primaryColorText" class="color-text-input" value="#1a1a1a">
                            </div>
                            <small class="text-muted">Main sidebar background</small>
                        </div>

                        <div class="color-control-group">
                            <label for="secondaryColor">Secondary Background Color</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="secondaryColor" class="color-picker" value="#2d2d2d">
                                <input type="text" id="secondaryColorText" class="color-text-input" value="#2d2d2d">
                            </div>
                            <small class="text-muted">Hover and section backgrounds</small>
                        </div>

                        <div class="color-control-group">
                            <label for="accentColor">Accent Color</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="accentColor" class="color-picker" value="#3b82f6">
                                <input type="text" id="accentColorText" class="color-text-input" value="#3b82f6">
                            </div>
                            <small class="text-muted">Active item and links</small>
                        </div>

                        <div class="color-control-group">
                            <label for="textColor">Text Color</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="textColor" class="color-picker" value="#e0e0e0">
                                <input type="text" id="textColorText" class="color-text-input" value="#e0e0e0">
                            </div>
                            <small class="text-muted">Main text color</small>
                        </div>

                        <div class="color-control-group">
                            <label for="hoverColor">Hover Color</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="hoverColor" class="color-picker" value="#374151">
                                <input type="text" id="hoverColorText" class="color-text-input" value="#374151">
                            </div>
                            <small class="text-muted">Item hover background</small>
                        </div>

                        <div class="customization-actions mt-4">
                            <button type="button" class="btn btn-primary" onclick="applySidebarCustomization()">
                                <i class="bi bi-check me-1"></i>Apply Changes
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetSidebarCustomization()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset to Default
                            </button>
                            <button type="button" class="btn btn-success ms-2" onclick="saveSidebarCustomization()">
                                <i class="bi bi-floppy me-1"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="preview-panel">
                    <h5>Live Preview</h5>
                    <div class="sidebar-preview" id="sidebarPreview">
                        <div class="preview-sidebar">
                            <!-- Profile Section -->
                            <div class="preview-profile">
                                <div class="preview-avatar">
                                    <div class="preview-avatar-placeholder">JS</div>
                                </div>
                                <div class="preview-profile-info">
                                    <div class="preview-name">John Student</div>
                                    <div class="preview-role">Student</div>
                                </div>
                            </div>
                            
                            <!-- Navigation -->
                            <div class="preview-nav">
                                <div class="preview-section-title">Main</div>
                                <div class="preview-nav-item active">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard</span>
                                </div>
                                <div class="preview-nav-item">
                                    <i class="bi bi-calendar-week"></i>
                                    <span>Calendar</span>
                                </div>
                                <div class="preview-nav-item">
                                    <i class="bi bi-journal-bookmark"></i>
                                    <span>My Courses</span>
                                </div>
                                <div class="preview-nav-item">
                                    <i class="bi bi-camera-video"></i>
                                    <span>Meetings</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

function viewProfilePhoto(fileName) {
    const modal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
    document.getElementById('fileViewerTitle').textContent = 'Profile Photo';
    
    const fileUrl = '/storage/profile-photos/' + fileName;
    const content = `<img src="${fileUrl}" class="img-fluid" alt="Profile Photo" style="max-width: 100%; height: auto;">`;
    
    document.getElementById('fileViewerContent').innerHTML = content;
    
    // Ensure modal appears on top
    const modalElement = document.getElementById('fileViewerModal');
    modalElement.style.zIndex = '10000';
    
    // Remove any existing backdrops to prevent conflicts
    const existingBackdrops = document.querySelectorAll('.modal-backdrop');
    existingBackdrops.forEach(backdrop => {
        if (backdrop.style.zIndex < '9998') {
            backdrop.remove();
        }
    });
    
    modal.show();
}

function previewProfilePhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const file = input.files[0];
        
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            input.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a JPG, JPEG, or PNG image');
            input.value = '';
            return;
        }
        
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="profile-image">`;
            
            // Add overlay
            const overlay = document.createElement('div');
            overlay.className = 'profile-overlay';
            overlay.innerHTML = '<i class="bi bi-camera"></i>';
            preview.appendChild(overlay);
        };
        
        reader.readAsDataURL(file);
    }
}

function removeProfilePhoto() {
    if (confirm('Are you sure you want to remove your profile photo?')) {
        // Reset the file input
        document.getElementById('profile_photo').value = '';
        
        // Reset preview to placeholder
        const preview = document.getElementById('profilePreview');
        preview.innerHTML = `
            <div class="profile-placeholder">
                <i class="bi bi-person-circle"></i>
                <span>VD</span>
            </div>
            <div class="profile-overlay">
                <i class="bi bi-camera"></i>
            </div>
        `;
        
        // Hide action buttons
        const actions = document.querySelector('.current-photo-actions');
        if (actions) {
            actions.style.display = 'none';
        }
        
        // You can add AJAX call here to actually remove from server if needed
    }
}

// Add click handler for profile photo display
document.addEventListener('DOMContentLoaded', function() {
    const profileDisplay = document.querySelector('.profile-photo-display');
    if (profileDisplay) {
        profileDisplay.addEventListener('click', function() {
            document.getElementById('profile_photo').click();
        });
    }
});

// Add preview functionality for profile photo selection
document.addEventListener('DOMContentLoaded', function() {
    const profilePhotoInput = document.getElementById('profile_photo');
    if (profilePhotoInput) {
        profilePhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create or update preview
                    let preview = document.querySelector('.profile-photo-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'profile-photo-preview mt-2';
                        profilePhotoInput.closest('.file-upload-section').appendChild(preview);
                    }
                    preview.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" alt="Preview" style="max-width: 100px; max-height: 100px; border-radius: 50%; object-fit: cover;">
                            <div class="small text-muted mt-1">Preview of selected photo</div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// ====== SIDEBAR CUSTOMIZATION FUNCTIONALITY ======
document.addEventListener('DOMContentLoaded', function() {
    // Load current settings from the server
    loadCurrentSidebarSettings();
    
    // Setup color picker event listeners
    setupColorPickers();
    
    // Initialize preview
    updateSidebarPreview();
});

function loadCurrentSidebarSettings() {
    fetch('/api/student/sidebar-settings')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('primaryColor').value = data.settings.primary_color || '#1a1a1a';
                document.getElementById('primaryColorText').value = data.settings.primary_color || '#1a1a1a';
                document.getElementById('secondaryColor').value = data.settings.secondary_color || '#2d2d2d';
                document.getElementById('secondaryColorText').value = data.settings.secondary_color || '#2d2d2d';
                document.getElementById('accentColor').value = data.settings.accent_color || '#3b82f6';
                document.getElementById('accentColorText').value = data.settings.accent_color || '#3b82f6';
                document.getElementById('textColor').value = data.settings.text_color || '#e0e0e0';
                document.getElementById('textColorText').value = data.settings.text_color || '#e0e0e0';
                document.getElementById('hoverColor').value = data.settings.hover_color || '#374151';
                document.getElementById('hoverColorText').value = data.settings.hover_color || '#374151';
                
                updateSidebarPreview();
            }
        })
        .catch(error => console.error('Error loading sidebar settings:', error));
}

function setupColorPickers() {
    const colorInputs = [
        { picker: 'primaryColor', text: 'primaryColorText' },
        { picker: 'secondaryColor', text: 'secondaryColorText' },
        { picker: 'accentColor', text: 'accentColorText' },
        { picker: 'textColor', text: 'textColorText' },
        { picker: 'hoverColor', text: 'hoverColorText' }
    ];
    
    colorInputs.forEach(input => {
        const picker = document.getElementById(input.picker);
        const textInput = document.getElementById(input.text);
        
        // Update text input when color picker changes
        picker.addEventListener('input', function() {
            textInput.value = this.value;
            updateSidebarPreview();
        });
        
        // Update color picker when text input changes
        textInput.addEventListener('input', function() {
            if (isValidHexColor(this.value)) {
                picker.value = this.value;
                updateSidebarPreview();
            }
        });
    });
}

function isValidHexColor(color) {
    return /^#[0-9A-F]{6}$/i.test(color);
}

function updateSidebarPreview() {
    const preview = document.getElementById('sidebarPreview');
    const primaryColor = document.getElementById('primaryColor').value;
    const secondaryColor = document.getElementById('secondaryColor').value;
    const accentColor = document.getElementById('accentColor').value;
    const textColor = document.getElementById('textColor').value;
    const hoverColor = document.getElementById('hoverColor').value;
    
    preview.style.setProperty('--preview-primary', primaryColor);
    preview.style.setProperty('--preview-secondary', secondaryColor);
    preview.style.setProperty('--preview-accent', accentColor);
    preview.style.setProperty('--preview-text', textColor);
    preview.style.setProperty('--preview-hover', hoverColor);
    preview.style.setProperty('--preview-text-muted', adjustColorOpacity(textColor, 0.7));
}

function adjustColorOpacity(hex, opacity) {
    // Convert hex to rgba with opacity
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}

function applySidebarCustomization() {
    const primaryColor = document.getElementById('primaryColor').value;
    const secondaryColor = document.getElementById('secondaryColor').value;
    const accentColor = document.getElementById('accentColor').value;
    const textColor = document.getElementById('textColor').value;
    const hoverColor = document.getElementById('hoverColor').value;
    
    // Apply to actual sidebar
    const actualSidebar = document.getElementById('studentSidebar');
    if (actualSidebar) {
        actualSidebar.style.setProperty('--sidebar-bg', primaryColor);
        actualSidebar.style.setProperty('--sidebar-hover', secondaryColor);
        actualSidebar.style.setProperty('--sidebar-active', accentColor);
        actualSidebar.style.setProperty('--sidebar-text', textColor);
        actualSidebar.style.setProperty('--sidebar-border', secondaryColor);
        
        // Update CSS custom properties globally
        document.documentElement.style.setProperty('--sidebar-bg', primaryColor);
        document.documentElement.style.setProperty('--sidebar-hover', secondaryColor);
        document.documentElement.style.setProperty('--sidebar-active', accentColor);
        document.documentElement.style.setProperty('--sidebar-text', textColor);
        document.documentElement.style.setProperty('--sidebar-border', secondaryColor);
        
        showNotification('Sidebar colors applied! Changes will persist until page reload.', 'success');
    }
}

function resetSidebarCustomization() {
    if (confirm('Reset sidebar colors to default? This will reload the page.')) {
        fetch('/api/student/sidebar-settings/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification('Error resetting sidebar settings', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error resetting sidebar settings', 'error');
        });
    }
}

function saveSidebarCustomization() {
    const settings = {
        primary_color: document.getElementById('primaryColor').value,
        secondary_color: document.getElementById('secondaryColor').value,
        accent_color: document.getElementById('accentColor').value,
        text_color: document.getElementById('textColor').value,
        hover_color: document.getElementById('hoverColor').value
    };
    
    fetch('/api/student/sidebar-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Sidebar settings saved successfully!', 'success');
            // Apply the changes immediately
            applySidebarCustomization();
        } else {
            showNotification('Error saving sidebar settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving sidebar settings', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    `;
    notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
