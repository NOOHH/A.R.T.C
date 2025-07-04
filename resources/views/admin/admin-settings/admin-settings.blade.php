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
                    <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                        <i class="fas fa-user-shield me-2"></i>Admin
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
                                                <button type="submit" class="btn btn-success">
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
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="previewForm('complete')">
                                                    <i class="fas fa-eye"></i> Preview Complete Form
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
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">HOMEPAGE CUSTOMIZATION</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">COLOR</label>
                                            <input type="color" class="form-control form-control-color" value="#17a2b8">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">COLOR</label>
                                            <input type="color" class="form-control form-control-color" value="#28a745">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">TEXT</label>
                                            <input type="text" class="form-control" value="Welcome Text">
                                        </div>
                                        <button type="button" class="btn btn-primary">COLOR</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">Navpanel</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <input type="color" class="form-control form-control-color" value="#ffc107">
                                        </div>
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

            {{-- Admin Tab --}}
            <div class="tab-pane fade" id="admin" role="tabpanel">
                <div class="row g-4">
                    {{-- Navbar Color Customization --}}
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-palette me-2"></i>Navbar Customization
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="navbarSettingsForm">
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
                                                <button type="button" class="btn btn-outline-primary" id="previewColors">
                                                    <i class="fas fa-eye"></i> Preview Colors
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="resetColors">
                                                    <i class="fas fa-undo"></i> Reset to Default
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Changes
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
        </div>
    </div>
@endsection
@push('scripts')
<script>
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
    loadStudentPortalSettings();
    
    // Add new requirement functionality
    document.getElementById('addRequirement').addEventListener('click', function() {
        addRequirementField();
    });

    // Navbar color preview functionality
    document.getElementById('previewColors').addEventListener('click', function() {
        previewNavbarColors();
    });

    // Reset colors functionality
    document.getElementById('resetColors').addEventListener('click', function() {
        resetNavbarColors();
    });

    // Save navbar settings
    document.getElementById('navbarSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveNavbarSettings();
    });

    // Save student requirements
    document.getElementById('studentRequirementsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveFormRequirements();
    });
    
    // Save student portal settings
    document.getElementById('studentPortalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveStudentPortalSettings();
    });

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
            }
        });
    }
});

function loadFormRequirements() {
    fetch('/admin/settings/form-requirements')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('requirementsContainer');
            container.innerHTML = '';
            
            data.forEach(requirement => {
                addRequirementField(requirement);
            });
            
            if (data.length === 0) {
                // Add default requirements
                addRequirementField({
                    field_name: 'phone_number',
                    field_label: 'Phone Number', 
                    field_type: 'tel',
                    program_type: 'both',
                    is_required: true
                });
                addRequirementField({
                    field_name: 'tor_document',
                    field_label: 'Transcript of Records (TOR)',
                    field_type: 'file',
                    program_type: 'both', 
                    is_required: true
                });
            }
        })
        .catch(error => {
            console.error('Error loading requirements:', error);
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
                        <option value="complete" ${data.program_type==='complete' ? 'selected':''}>Complete</option>
                        <option value="modular"  ${data.program_type==='modular'  ? 'selected':''}>Modular</option>
                    </select>
                </div>
                <!-- Required & Active switches (1 col) -->
                <div class="col-md-1 d-flex flex-column align-items-start">
                    <label class="form-label">Required</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="requirements[${index}][is_required]"
                               ${data.is_required ? 'checked' : ''}>
                    </div>
                    <label class="form-label mt-2">Active</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="requirements[${index}][is_active]"
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
    button.closest('.requirement-item').remove();
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
    const formData = new FormData(form);
    
    fetch('/admin/settings/form-requirements', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Form fields saved successfully!', 'success');
            loadFormRequirements();
        } else {
            showAlert('Error saving form fields', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving form fields', 'danger');
    });
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

function saveNavbarSettings() {
    const form = document.getElementById('navbarSettingsForm');
    const formData = new FormData(form);
    
    fetch('/admin/settings/navbar', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Navbar settings saved successfully!', 'success');
        } else {
            showAlert('Error saving navbar settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving navbar settings', 'danger');
    });
}

function saveStudentPortalSettings() {
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
        if (data.success) {
            showAlert('Student portal settings saved successfully!', 'success');
        } else {
            showAlert('Error saving student portal settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving student portal settings', 'danger');
    });
}

function previewNavbarColors() {
    showAlert('Preview functionality would be implemented here', 'info');
}

function resetNavbarColors() {
    const defaultColors = {
        header_bg: '#ffffff',
        header_text: '#333333',
        header_border: '#e0e0e0',
        sidebar_bg: '#343a40',
        sidebar_text: '#ffffff',
        active_link_bg: '#007bff'
    };
    
    Object.keys(defaultColors).forEach(key => {
        const input = document.querySelector(`input[name="${key}"]`);
        if (input) input.value = defaultColors[key];
    });
    
    showAlert('Colors reset to defaults', 'info');
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

function toggleBoldText(btn) {
  // climb up to the .requirement-item wrapper
  const itemDiv    = btn.closest('.requirement-item');
  // find the Display Label input and the hidden is_bold within that same item
  const labelInput = itemDiv.querySelector('input[name*="[field_label]"]');
  const boldInput  = itemDiv.querySelector('input[name*="[is_bold]"]');
  if (!labelInput || !boldInput) return;

  // toggle
  const nowBold = labelInput.style.fontWeight !== 'bold';
  labelInput.style.fontWeight = nowBold ? 'bold' : '';
  boldInput.value            = nowBold ? '1' : '0';
}

function toggleFieldActiveStatus(checkbox) {
    const requirementItem = checkbox.closest('.requirement-item');
    const isActive = checkbox.checked;
    
    if (isActive) {
        // Make field active - restore normal appearance
        requirementItem.style.opacity = '1';
        requirementItem.style.backgroundColor = '';
        requirementItem.classList.remove('requirement-inactive');
        
        // Remove inactive warning if it exists
        const warning = requirementItem.querySelector('.alert-warning');
        if (warning) {
            warning.remove();
        }
    } else {
        // Make field inactive - dim appearance
        requirementItem.style.opacity = '0.6';
        requirementItem.style.backgroundColor = '#f8f9fa';
        requirementItem.classList.add('requirement-inactive');
        
        // Add inactive warning if it doesn't exist
        const flexGrow = requirementItem.querySelector('.flex-grow-1');
        const existingWarning = flexGrow.querySelector('.alert-warning');
        if (!existingWarning) {
            const warning = document.createElement('div');
            warning.className = 'alert alert-warning py-1 px-2 mb-2 small';
            warning.innerHTML = '<i class="fas fa-eye-slash"></i> This field is currently <strong>inactive</strong> and hidden from registration forms';
            
            const rowDiv = flexGrow.querySelector('.row');
            flexGrow.insertBefore(warning, rowDiv);
        }
    }
}

// Show field management help
function showFieldManagementHelp() {
    const helpModal = document.createElement('div');
    helpModal.className = 'modal fade';
    helpModal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dynamic Registration System Help</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>How the Dynamic Registration System Works:</h6>
                    <ul>
                        <li><strong>Static Columns:</strong> Each field has a corresponding column in the registrations table</li>
                        <li><strong>No Data Loss:</strong> When you disable a field, data is preserved in the database</li>
                        <li><strong>Flexible Forms:</strong> Enable/disable fields without affecting existing registrations</li>
                    </ul>
                    
                    <h6>Field Management:</h6>
                    <ul>
                        <li><strong>Add Field:</strong> Creates a new form field and optionally adds a database column</li>
                        <li><strong>Disable Field:</strong> Hides field from forms but keeps data intact</li>
                        <li><strong>Enable Field:</strong> Shows field in forms again, using existing data</li>
                    </ul>
                    
                    <h6>Best Practices:</h6>
                    <ul>
                        <li>Test new fields in preview before making them live</li>
                        <li>Consider program type when adding fields (complete vs modular)</li>
                        <li>Use meaningful field names that won't conflict with existing columns</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(helpModal);
    const modal = new bootstrap.Modal(helpModal);
    modal.show();
    
    // Remove modal from DOM after it's hidden
    helpModal.addEventListener('hidden.bs.modal', function () {
        document.body.removeChild(helpModal);
    });
}

// Add field deactivation functionality
function toggleFieldActive(fieldId, isActive) {
    fetch('/admin/settings/form-requirements/toggle-active', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            field_id: fieldId,
            is_active: isActive
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Field '${data.field_name}' ${isActive ? 'activated' : 'deactivated'} successfully!`, 'success');
            loadFormRequirements();
        } else {
            showAlert('Error updating field status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating field status', 'danger');
    });
}

// Add new column to database
function addDynamicColumn() {
    const fieldName = prompt('Enter field name (e.g., middle_name):');
    if (!fieldName) return;
    
    const fieldType = prompt('Enter field type (string, text, integer, date, boolean):', 'string');
    if (!fieldType) return;
    
    const nullable = confirm('Should this field be nullable?');
    
    fetch('/admin/settings/form-requirements/add-column', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            field_name: fieldName,
            field_type: fieldType,
            nullable: nullable
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
        } else {
            showAlert(data.error, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding column', 'danger');
    });
}

// Preview form
function previewForm(programType) {
    window.open(`/admin/settings/form-requirements/preview/${programType}`, '_blank');
}

</script>
@endpush
