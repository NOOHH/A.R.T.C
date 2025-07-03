@extends('admin.admin-dashboard-layout')

@section('title', 'Settings')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/admin-settings/admin-settings.css') }}">
<style>
.settings-tabs {
    margin-bottom: 2rem;
}

.settings-tabs .nav-tabs {
    border-bottom: 2px solid #e9ecef;
}

.settings-tabs .nav-link {
    border: none;
    border-radius: 25px;
    padding: 12px 30px;
    margin-right: 10px;
    font-weight: 600;
    color: #6c757d;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.settings-tabs .nav-link:hover {
    background: #e9ecef;
    color: #495057;
}

.settings-tabs .nav-link.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.tab-content {
    padding: 2rem 0;
}

.settings-section {
    margin-bottom: 2rem;
}

.settings-placeholder {
    text-align: center;
    padding: 3rem;
    background: #f8f9fa;
    border-radius: 10px;
    color: #6c757d;
}

.nav-pills .nav-link {
    border-radius: 20px;
    margin: 0 5px;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
}
</style>
@endpush

@section('content')
<div class="main-content-wrapper">
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
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Settings Tabs --}}
        <div class="settings-tabs">
            <ul class="nav nav-tabs justify-content-center" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab" aria-controls="student" aria-selected="true">
                        <i class="fas fa-user-graduate me-2"></i>Student
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="professor-tab" data-bs-toggle="tab" data-bs-target="#professor" type="button" role="tab" aria-controls="professor" aria-selected="false">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Professor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="false">
                        <i class="fas fa-user-shield me-2"></i>Admin
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tab Content --}}
        <div class="tab-content" id="settingsTabContent">
            {{-- Student Tab --}}
            <div class="tab-pane fade show active" id="student" role="tabpanel" aria-labelledby="student-tab">
                <div class="row g-4">
                    {{-- Student Portal Settings --}}
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>Student Portal
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

                    {{-- Student Registration Settings --}}
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user-plus me-2"></i>Registration
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="studentRequirementsForm">
                                    @csrf
                                    <div class="mb-4">
                                        <h6 class="text-primary">Manage Registration Requirements</h6>
                                        <p class="text-muted small">Add or modify required fields for student registration forms</p>
                                    </div>
                                    
                                    <div id="requirementsContainer">
                                        <!-- Dynamic requirements will be loaded here -->
                                    </div>
                                    
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary" id="addRequirement">
                                            <i class="fas fa-plus"></i> Add New Requirement
                                        </button>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success">Save Requirements</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subtabs for Student Settings --}}
                <div class="settings-section">
                    <ul class="nav nav-pills nav-fill mb-4" id="studentSubTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="home-tab-sub" data-bs-toggle="tab" data-bs-target="#home-sub" type="button" role="tab">Home</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dashboard-tab-sub" data-bs-toggle="tab" data-bs-target="#dashboard-sub" type="button" role="tab">Dashboard</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab">Activities</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="studentSubTabContent">
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Login Page Background</label>
                                                    <input type="file" class="form-control" accept="image/*">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Background Overlay Color</label>
                                                    <input type="color" class="form-control form-control-color" value="#000000">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Overlay Opacity</label>
                                                    <input type="range" class="form-range" min="0" max="100" value="50">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Login Form Title</label>
                                                    <input type="text" class="form-control" value="Student Login" placeholder="Enter form title">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Welcome Text</label>
                                                    <textarea class="form-control" rows="3">Welcome back! Please sign in to access your courses.</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Show "Remember Me" option</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Login Settings</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="register" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Registration Form Layout</label>
                                                    <select class="form-select">
                                                        <option>Single Column</option>
                                                        <option selected>Two Columns</option>
                                                        <option>Stepped Form</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Form Background Color</label>
                                                    <input type="color" class="form-control form-control-color" value="#ffffff">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Success Message</label>
                                                    <textarea class="form-control" rows="2">Registration successful! Please check your email for verification.</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Require email verification</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Registration Settings</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="home-sub" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Hero Section Title</label>
                                                    <input type="text" class="form-control" value="Welcome to A.R.T.C" placeholder="Main homepage title">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Hero Section Subtitle</label>
                                                    <textarea class="form-control" rows="2">Advance your career with our comprehensive learning platform</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Hero Background Image</label>
                                                    <input type="file" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Featured Programs Section</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Show featured programs</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Programs to Display</label>
                                                    <input type="number" class="form-control" value="6" min="3" max="12">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Call-to-Action Button Text</label>
                                                    <input type="text" class="form-control" value="Start Learning Today">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Homepage Settings</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dashboard-sub" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Dashboard Layout</label>
                                                    <select class="form-select">
                                                        <option>Sidebar Left</option>
                                                        <option selected>Sidebar Right</option>
                                                        <option>Top Navigation</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Sidebar Color</label>
                                                    <input type="color" class="form-control form-control-color" value="#343a40">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Quick Actions</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Show progress overview</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Show recent activities</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Welcome Message</label>
                                                    <textarea class="form-control" rows="2">Welcome back! Continue your learning journey.</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Dashboard Widgets</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Course Progress</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Upcoming Assignments</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox">
                                                        <label class="form-check-label">Announcements</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Dashboard Settings</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="activities" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Course Card Style</label>
                                                    <select class="form-select">
                                                        <option>Minimal Cards</option>
                                                        <option selected>Detailed Cards</option>
                                                        <option>List View</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Progress Bar Color</label>
                                                    <input type="color" class="form-control form-control-color" value="#28a745">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Cards per Row</label>
                                                    <select class="form-select">
                                                        <option>2</option>
                                                        <option selected>3</option>
                                                        <option>4</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Filter Options</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Filter by status</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" checked>
                                                        <label class="form-check-label">Filter by category</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox">
                                                        <label class="form-check-label">Filter by difficulty</label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Default Sort Order</label>
                                                    <select class="form-select">
                                                        <option>Newest First</option>
                                                        <option selected>Progress</option>
                                                        <option>Alphabetical</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Activities Settings</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Professor Tab --}}
            <div class="tab-pane fade" id="professor" role="tabpanel" aria-labelledby="professor-tab">
                <div class="row g-4">
                    {{-- Professor Portal Settings --}}
                    <div class="col-md-12">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Professor Portal Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Professor Dashboard Theme</label>
                                                <select class="form-select">
                                                    <option>Light Theme</option>
                                                    <option selected>Dark Theme</option>
                                                    <option>Blue Theme</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Primary Color</label>
                                                <input type="color" class="form-control form-control-color" value="#6f42c1">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Professor Portal Logo</label>
                                                <input type="file" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Course Management Layout</label>
                                                <select class="form-select">
                                                    <option>Grid View</option>
                                                    <option selected>List View</option>
                                                    <option>Card View</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Default Video Player</label>
                                                <select class="form-select">
                                                    <option selected>HTML5 Player</option>
                                                    <option>YouTube Embed</option>
                                                    <option>Vimeo Embed</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" checked>
                                                    <label class="form-check-label">Enable video progress tracking</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Professor Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Admin Tab --}}
            <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
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
                                            <div class="mb-3">
                                                <label class="form-label">Search Box Background</label>
                                                <input type="color" class="form-control form-control-color" name="search_bg" value="#f8f9fa">
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
                                            <div class="mb-3">
                                                <label class="form-label">Active Link Text</label>
                                                <input type="color" class="form-control form-control-color" name="active_link_text" value="#ffffff">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-secondary mb-3">Hover & Focus States</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Link Hover Background</label>
                                                <input type="color" class="form-control form-control-color" name="hover_bg" value="#495057">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Link Hover Text</label>
                                                <input type="color" class="form-control form-control-color" name="hover_text" value="#ffffff">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Submenu Background</label>
                                                <input type="color" class="form-control form-control-color" name="submenu_bg" value="#2c3034">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Submenu Text</label>
                                                <input type="color" class="form-control form-control-color" name="submenu_text" value="#adb5bd">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <h6 class="text-secondary mb-3">Additional Settings</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Sidebar Footer Background</label>
                                                <input type="color" class="form-control form-control-color" name="footer_bg" value="#212529">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Icon Color</label>
                                                <input type="color" class="form-control form-control-color" name="icon_color" value="#6c757d">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-secondary mb-3">Preview & Actions</h6>
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-outline-secondary" id="previewColors">
                                                    <i class="fas fa-eye"></i> Preview Changes
                                                </button>
                                                <button type="button" class="btn btn-outline-warning ms-2" id="resetColors">
                                                    <i class="fas fa-undo"></i> Reset to Default
                                                </button>
                                            </div>
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Navbar Colors
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Global Logo Settings --}}
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-globe me-2"></i>Global Logo
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="logoSettingsForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Current Logo</label>
                                                <div class="border p-3 text-center mb-2" id="currentLogoPreview">
                                                    <img src="{{ App\Helpers\UIHelper::getGlobalLogo() }}" 
                                                         alt="Current Logo" style="max-height: 80px;" class="img-fluid">
                                                </div>
                                                <input type="file" class="form-control" name="logo" accept="image/*" id="logoInput">
                                                <small class="text-muted">Recommended size: 200x80px (JPEG, PNG, SVG, WebP)</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Logo Position</label>
                                                <select class="form-select" name="logo_position">
                                                    <option value="left" {{ App\Models\UiSetting::get('global', 'logo_position', 'left') == 'left' ? 'selected' : '' }}>Left</option>
                                                    <option value="center" {{ App\Models\UiSetting::get('global', 'logo_position') == 'center' ? 'selected' : '' }}>Center</option>
                                                    <option value="right" {{ App\Models\UiSetting::get('global', 'logo_position') == 'right' ? 'selected' : '' }}>Right</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Favicon</label>
                                                <input type="file" class="form-control" name="favicon" accept="image/x-icon,image/png" id="faviconInput">
                                                <small class="text-muted">32x32px ICO or PNG format</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Site Title</label>
                                                <input type="text" class="form-control" name="site_title" value="{{ App\Models\UiSetting::get('global', 'site_title', 'A.R.T.C Admin Portal') }}">
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="show_on_all_pages" 
                                                           {{ App\Models\UiSetting::get('global', 'show_on_all_pages', '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label">Show logo on all pages</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Save Logo Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Homepage Settings --}}
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-home me-2"></i>Homepage Customization
                                </h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Homepage Layout</label>
                                                <select class="form-select">
                                                    <option>Traditional</option>
                                                    <option selected>Modern Grid</option>
                                                    <option>Full Width</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Color Scheme</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small">Primary</label>
                                                        <input type="color" class="form-control form-control-color" value="#007bff">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small">Secondary</label>
                                                        <input type="color" class="form-control form-control-color" value="#6c757d">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Background Image</label>
                                                <input type="file" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Homepage Title</label>
                                                <input type="text" class="form-control" value="Administrative Portal">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" rows="3">Manage students, professors, and courses efficiently</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Footer Text</label>
                                                <input type="text" class="form-control" value="© 2025 A.R.T.C. All rights reserved.">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">Save Homepage Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Email Settings --}}
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-envelope me-2"></i>Email Configuration
                                </h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">SMTP Server</label>
                                                <input type="text" class="form-control" placeholder="smtp.gmail.com">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Port</label>
                                                <input type="number" class="form-control" value="587">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="email" class="form-control" placeholder="your-email@gmail.com">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <input type="password" class="form-control" placeholder="••••••••">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">From Name</label>
                                                <input type="text" class="form-control" value="A.R.T.C System">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">From Email</label>
                                                <input type="email" class="form-control" placeholder="noreply@artc.com">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email Notifications</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" checked>
                                                    <label class="form-check-label">New student registrations</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" checked>
                                                    <label class="form-check-label">Course completions</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox">
                                                    <label class="form-check-label">System alerts</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm">Send Test Email</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-warning">Save Email Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

    // Initialize sub tabs
    var subTriggerTabList = [].slice.call(document.querySelectorAll('#studentSubTabs button[data-bs-toggle="tab"]'));
    subTriggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Load existing form requirements, navbar settings, and student portal settings
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
    
    // Save logo settings
    document.getElementById('logoSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveLogoSettings();
    });
    
    // Preview logo upload
    document.getElementById('logoInput').addEventListener('change', function(e) {
        previewLogo(e.target);
    });
    
    // Preview student portal logo upload
    const studentLogoInput = document.querySelector('#studentPortalForm input[name="header_logo"]');
    if (studentLogoInput) {
        studentLogoInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('#studentPortalLogoPreview');
                    const placeholder = document.querySelector('#studentPortalLogoPlaceholder');
                    if (preview && placeholder) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
});

function saveLogoSettings() {
    const form = document.getElementById('logoSettingsForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    submitBtn.disabled = true;
    
    fetch('/admin/settings/logo', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Logo settings saved successfully!', 'success');
            if (data.logo_url) {
                // Update preview
                document.querySelector('#currentLogoPreview img').src = data.logo_url;
                // Update navbar logo if exists
                const navbarLogo = document.querySelector('.brand-link img');
                if (navbarLogo) navbarLogo.src = data.logo_url;
            }
        } else {
            showAlert(data.message || 'Error saving logo settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving logo settings', 'danger');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('#currentLogoPreview img').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewImage(input, targetSelector) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector(targetSelector);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function loadStudentPortalSettings() {
    fetch('/admin/settings/student-portal')
        .then(response => response.json())
        .then(data => {
            // Set form values from loaded settings
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

function loadNavbarSettings() {
    fetch('/admin/settings/navbar')
        .then(response => response.json())
        .then(data => {
            // Set form values from loaded settings
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

function loadFormRequirements() {
    // Load existing requirements from database
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
    requirementDiv.className = 'requirement-item border rounded p-3 mb-3';
    requirementDiv.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Field Name</label>
                <input type="text" class="form-control" name="requirements[${index}][field_name]" 
                       value="${data.field_name || ''}" placeholder="e.g., phone_number">
            </div>
            <div class="col-md-3">
                <label class="form-label">Display Label</label>
                <input type="text" class="form-control" name="requirements[${index}][field_label]" 
                       value="${data.field_label || ''}" placeholder="e.g., Phone Number">
            </div>
            <div class="col-md-2">
                <label class="form-label">Field Type</label>
                <select class="form-select" name="requirements[${index}][field_type]">
                    <option value="text" ${data.field_type === 'text' ? 'selected' : ''}>Text</option>
                    <option value="email" ${data.field_type === 'email' ? 'selected' : ''}>Email</option>
                    <option value="tel" ${data.field_type === 'tel' ? 'selected' : ''}>Phone</option>
                    <option value="date" ${data.field_type === 'date' ? 'selected' : ''}>Date</option>
                    <option value="file" ${data.field_type === 'file' ? 'selected' : ''}>File</option>
                    <option value="select" ${data.field_type === 'select' ? 'selected' : ''}>Dropdown</option>
                    <option value="textarea" ${data.field_type === 'textarea' ? 'selected' : ''}>Textarea</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Program</label>
                <select class="form-select" name="requirements[${index}][program_type]">
                    <option value="both" ${data.program_type === 'both' ? 'selected' : ''}>Both</option>
                    <option value="complete" ${data.program_type === 'complete' ? 'selected' : ''}>Complete</option>
                    <option value="modular" ${data.program_type === 'modular' ? 'selected' : ''}>Modular</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Required</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="requirements[${index}][is_required]" 
                           ${data.is_required !== false ? 'checked' : ''}>
                </div>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-outline-danger btn-sm d-block" onclick="removeRequirement(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <input type="hidden" name="requirements[${index}][id]" value="${data.id || ''}">
    `;
    
    container.appendChild(requirementDiv);
}

function removeRequirement(button) {
    button.closest('.requirement-item').remove();
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
            showAlert('Requirements saved successfully!', 'success');
            loadFormRequirements(); // Reload to get IDs for new items
        } else {
            showAlert('Error saving requirements', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving requirements', 'danger');
    });
}

function previewNavbarColors() {
    const form = document.getElementById('navbarSettingsForm');
    const formData = new FormData(form);
    
    // Apply colors to current page as preview
    const colors = {};
    for (let [key, value] of formData.entries()) {
        colors[key] = value;
    }
    
    // Apply preview styles
    applyNavbarColors(colors, true);
    
    showAlert('Preview applied! Refresh page to revert.', 'info');
}

function resetNavbarColors() {
    const defaultColors = {
        header_bg: '#ffffff',
        header_text: '#333333',
        header_border: '#e0e0e0',
        search_bg: '#f8f9fa',
        sidebar_bg: '#343a40',
        sidebar_text: '#ffffff',
        active_link_bg: '#007bff',
        active_link_text: '#ffffff',
        hover_bg: '#495057',
        hover_text: '#ffffff',
        submenu_bg: '#2c3034',
        submenu_text: '#adb5bd',
        footer_bg: '#212529',
        icon_color: '#6c757d'
    };
    
    // Set form values to defaults
    Object.keys(defaultColors).forEach(key => {
        const input = document.querySelector(`input[name="${key}"]`);
        if (input) input.value = defaultColors[key];
    });
    
    showAlert('Colors reset to defaults', 'info');
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
            // Apply the saved colors
            const colors = {};
            for (let [key, value] of formData.entries()) {
                colors[key] = value;
            }
            applyNavbarColors(colors, false);
        } else {
            showAlert('Error saving navbar settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving navbar settings', 'danger');
    });
}

function applyNavbarColors(colors, isPreview = false) {
    // Create or update style element
    let styleElement = document.getElementById('navbar-custom-styles');
    if (!styleElement) {
        styleElement = document.createElement('style');
        styleElement.id = 'navbar-custom-styles';
        document.head.appendChild(styleElement);
    }
    
    const css = `
        .main-header {
            background-color: ${colors.header_bg} !important;
            color: ${colors.header_text} !important;
            border-bottom: 1px solid ${colors.header_border} !important;
        }
        .search-box {
            background-color: ${colors.search_bg} !important;
        }
        .sidebar {
            background-color: ${colors.sidebar_bg} !important;
            color: ${colors.sidebar_text} !important;
        }
        .sidebar .sidebar-link {
            color: ${colors.sidebar_text} !important;
        }
        .sidebar .sidebar-link:hover {
            background-color: ${colors.hover_bg} !important;
            color: ${colors.hover_text} !important;
        }
        .sidebar li.active > .sidebar-link {
            background-color: ${colors.active_link_bg} !important;
            color: ${colors.active_link_text} !important;
        }
        .sidebar .sidebar-submenu {
            background-color: ${colors.submenu_bg} !important;
        }
        .sidebar .sidebar-submenu a {
            color: ${colors.submenu_text} !important;
        }
        .sidebar-footer {
            background-color: ${colors.footer_bg} !important;
        }
        .sidebar .icon {
            color: ${colors.icon_color} !important;
        }
    `;
    
    styleElement.textContent = css;
    
    if (isPreview) {
        // Add a class to indicate this is a preview
        document.body.classList.add('navbar-preview');
    }
}

function showAlert(message, type) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>
@endpush

@endsection
