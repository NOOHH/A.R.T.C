@extends('layouts.app')

@section('title', $profileData['name'] . ' - Program Profile')

@section('content')
<div class="container-fluid py-4">
    <!-- Program Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="program-icon-large d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-2 text-primary">{{ $profileData['name'] }}</h2>
                            <div class="mb-2">
                                <span class="badge bg-{{ $profileData['is_active'] ? 'success' : 'warning' }} fs-6">
                                    <i class="fas fa-{{ $profileData['is_active'] ? 'check-circle' : 'pause-circle' }} me-2"></i>
                                    {{ $profileData['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                                @if($profileData['is_archived'])
                                    <span class="badge bg-secondary fs-6 ms-2">
                                        <i class="fas fa-archive me-1"></i>Archived
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted mb-1">{{ $profileData['description'] ?: 'No description available' }}</p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Created {{ $profileData['created_at']->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-cube fs-1 mb-2"></i>
                    <h4>{{ count($profileData['modules']) }}</h4>
                    <p class="mb-0">Modules</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book fs-1 mb-2"></i>
                    <h4>{{ $profileData['modules']->sum('courses_count') }}</h4>
                    <p class="mb-0">Total Courses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fs-1 mb-2"></i>
                    <h4>{{ count($profileData['professors']) }}</h4>
                    <p class="mb-0">Professors</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fs-1 mb-2"></i>
                    <h4>{{ count($profileData['students']) }}</h4>
                    <p class="mb-0">Students</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules and Courses -->
    @if(count($profileData['modules']) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cube me-2"></i>Program Modules & Courses
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="modulesAccordion">
                            @foreach($profileData['modules'] as $index => $module)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $index }}" 
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $index }}">
                                            <i class="fas fa-cube me-2"></i>
                                            <strong>{{ $module['module_name'] }}</strong>
                                            <span class="badge bg-info ms-2">{{ $module['courses_count'] }} courses</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" 
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                         aria-labelledby="heading{{ $index }}" 
                                         data-bs-parent="#modulesAccordion">
                                        <div class="accordion-body">
                                            <p class="text-muted mb-3">{{ $module['module_description'] ?: 'No description available' }}</p>
                                            
                                            @if(count($module['courses']) > 0)
                                                <h6 class="mb-3">Courses in this Module:</h6>
                                                <div class="row">
                                                    @foreach($module['courses'] as $course)
                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                            <div class="card border-info">
                                                                <div class="card-body p-3">
                                                                    <h6 class="card-title text-info">
                                                                        <i class="fas fa-book me-2"></i>{{ $course['course_title'] }}
                                                                    </h6>
                                                                    <p class="card-text small text-muted">
                                                                        {{ $course['course_description'] ?: 'No description available' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>No courses available in this module yet.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Teaching Professors -->
    @if(count($profileData['professors']) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Teaching Professors
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($profileData['professors'] as $professor)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body d-flex align-items-center">
                                            <img src="{{ $professor['avatar'] }}" 
                                                 alt="{{ $professor['name'] }}" 
                                                 class="rounded-circle me-3" 
                                                 width="50" height="50">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1 text-success">{{ $professor['name'] }}</h6>
                                                <p class="card-text small text-muted mb-2">{{ $professor['email'] }}</p>
                                                <a href="{{ route('profile.user', $professor['user_id']) }}" 
                                                   class="btn btn-sm btn-outline-success">
                                                    View Profile
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enrolled Students -->
    @if(count($profileData['students']) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Enrolled Students
                        </h5>
                    </div>
                    <div class="card-body">
                        @auth
                            @if(in_array(auth()->user()->role, ['admin', 'director', 'professor']))
                                <div class="row">
                                    @foreach($profileData['students'] as $student)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body d-flex align-items-center">
                                                    <img src="{{ $student['avatar'] }}" 
                                                         alt="{{ $student['name'] }}" 
                                                         class="rounded-circle me-3" 
                                                         width="50" height="50">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1">{{ $student['name'] }}</h6>
                                                        <p class="card-text small text-muted mb-2">{{ $student['email'] }}</p>
                                                        <a href="{{ route('profile.user', $student['user_id']) }}" 
                                                           class="btn btn-sm btn-outline-warning">
                                                            View Profile
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ count($profileData['students']) }} students are enrolled in this program.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ count($profileData['students']) }} students are enrolled in this program.
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Program Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Program Information
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Program Name:</dt>
                        <dd class="col-sm-7">{{ $profileData['name'] }}</dd>
                        
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-{{ $profileData['is_active'] ? 'success' : 'warning' }}">
                                {{ $profileData['is_active'] ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">{{ $profileData['created_at']->format('M d, Y') }}</dd>
                        
                        <dt class="col-sm-5">Modules:</dt>
                        <dd class="col-sm-7">{{ count($profileData['modules']) }}</dd>
                        
                        <dt class="col-sm-5">Total Courses:</dt>
                        <dd class="col-sm-7">{{ $profileData['modules']->sum('courses_count') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Enrollment Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Teaching Staff:</dt>
                        <dd class="col-sm-6">{{ count($profileData['professors']) }} professors</dd>
                        
                        <dt class="col-sm-6">Enrolled Students:</dt>
                        <dd class="col-sm-6">{{ count($profileData['students']) }} students</dd>
                        
                        <dt class="col-sm-6">Archive Status:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-{{ $profileData['is_archived'] ? 'secondary' : 'success' }}">
                                {{ $profileData['is_archived'] ? 'Archived' : 'Active' }}
                            </span>
                        </dd>
                    </dl>
                    
                    @auth
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'director')
                            <div class="mt-3">
                                <a href="#" class="btn btn-sm btn-primary me-2">
                                    <i class="fas fa-edit me-1"></i>Edit Program
                                </a>
                                <a href="#" class="btn btn-sm btn-info">
                                    <i class="fas fa-users me-1"></i>Manage Enrollments
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.program-icon-large {
    width: 120px;
    height: 120px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-radius: 20px;
    font-size: 48px;
    color: white;
}

.card {
    border: none;
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    border: none;
}

.badge {
    font-size: 0.8rem;
}

.accordion-button:not(.collapsed) {
    background-color: rgba(13, 110, 253, 0.1);
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-info:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
</style>
@endsection
