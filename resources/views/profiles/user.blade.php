@extends('layouts.app')

@section('title', $profile['name'] . ' - Profile')

@section('content')
<div class="container-fluid py-4">
    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="{{ $profile['avatar'] }}" 
                                 alt="{{ $profile['name'] }}" 
                                 class="rounded-circle mb-3" 
                                 width="120" height="120">
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-2">{{ $profile['name'] }}</h2>
                            <div class="mb-2">
                                <span class="badge bg-{{ $profile['role'] === 'Student' ? 'primary' : ($profile['role'] === 'Professor' ? 'success' : 'warning') }} fs-6">
                                    <i class="fas fa-{{ $profile['role'] === 'Student' ? 'user-graduate' : ($profile['role'] === 'Professor' ? 'chalkboard-teacher' : 'user-shield') }} me-2"></i>
                                    {{ $profile['role'] }}
                                </span>
                                <span class="badge bg-{{ $profile['status'] === 'Online' ? 'success' : 'secondary' }} fs-6 ms-2">
                                    <i class="fas fa-circle me-1"></i>{{ $profile['status'] }}
                                </span>
                            </div>
                            <p class="text-muted mb-1">
                                <i class="fas fa-envelope me-2"></i>{{ $profile['email'] }}
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Joined {{ $profile['created_at']->format('M d, Y') }}
                            </p>
                            @if($profile['last_seen'])
                                <p class="text-muted mb-0">
                                    <i class="fas fa-clock me-2"></i>Last seen {{ $profile['last_seen']->diffForHumans() }}
                                </p>
                            @endif
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

    <!-- Student Information -->
    @if($profile['role'] === 'Student' && isset($profile['enrollments']))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Program Enrollments
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($profile['enrollments']) > 0)
                            <div class="row">
                                @foreach($profile['enrollments'] as $enrollment)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-primary">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">
                                                    <i class="fas fa-book me-2"></i>{{ $enrollment['program'] }}
                                                </h6>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        Enrolled: {{ $enrollment['enrolled_at']->format('M d, Y') }}
                                                    </small>
                                                </p>
                                                <span class="badge bg-success">{{ $enrollment['status'] }}</span>
                                                <div class="mt-2">
                                                    <a href="{{ route('profile.program', $enrollment['program_id']) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        View Program
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted">No Program Enrollments</h6>
                                <p class="text-muted">This student is not currently enrolled in any programs.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Professor Information -->
    @if($profile['role'] === 'Professor' && isset($profile['programs']))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Teaching Programs
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($profile['programs']) > 0)
                            <div class="row">
                                @foreach($profile['programs'] as $program)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-success">
                                            <div class="card-body">
                                                <h6 class="card-title text-success">
                                                    <i class="fas fa-graduation-cap me-2"></i>{{ $program['program_name'] }}
                                                </h6>
                                                <p class="card-text">
                                                    <small class="text-muted">{{ $program['program_description'] }}</small>
                                                </p>
                                                <div class="d-flex justify-content-between text-muted small mb-2">
                                                    <span><i class="fas fa-cube me-1"></i>{{ $program['modules_count'] }} Modules</span>
                                                    <span><i class="fas fa-users me-1"></i>{{ $program['students_count'] }} Students</span>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('profile.program', $program['program_id']) }}" 
                                                       class="btn btn-sm btn-outline-success">
                                                        View Program
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted">No Teaching Assignments</h6>
                                <p class="text-muted">This professor is not currently assigned to any programs.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Contact Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $profile['email'] }}</dd>
                        
                        <dt class="col-sm-4">Role:</dt>
                        <dd class="col-sm-8">{{ $profile['role'] }}</dd>
                        
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $profile['status'] === 'Online' ? 'success' : 'secondary' }}">
                                {{ $profile['status'] }}
                            </span>
                        </dd>
                        
                        @if(isset($profile['student_id']))
                            <dt class="col-sm-4">Student ID:</dt>
                            <dd class="col-sm-8">{{ $profile['student_id'] }}</dd>
                        @endif
                        
                        @if(isset($profile['professor_id']))
                            <dt class="col-sm-4">Professor ID:</dt>
                            <dd class="col-sm-8">{{ $profile['professor_id'] }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Member Since:</dt>
                        <dd class="col-sm-7">{{ $profile['created_at']->format('M d, Y') }}</dd>
                        
                        @if($profile['last_seen'])
                            <dt class="col-sm-5">Last Activity:</dt>
                            <dd class="col-sm-7">{{ $profile['last_seen']->diffForHumans() }}</dd>
                        @endif
                        
                        <dt class="col-sm-5">Account Type:</dt>
                        <dd class="col-sm-7">{{ $profile['role'] }}</dd>
                    </dl>
                    
                    @auth
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'director')
                            <div class="mt-3">
                                <a href="#" class="btn btn-sm btn-primary me-2">
                                    <i class="fas fa-edit me-1"></i>Edit Profile
                                </a>
                                <a href="#" class="btn btn-sm btn-info">
                                    <i class="fas fa-envelope me-1"></i>Send Message
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

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-secondary:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
</style>
@endsection
