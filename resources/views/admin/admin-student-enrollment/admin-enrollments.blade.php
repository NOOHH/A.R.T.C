@extends('layouts.admin')

@section('title', 'Enrollment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="bi bi-book me-2"></i>Enrollment Management
                    </h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.student.enrollment.batch') }}" class="btn btn-primary">
                            <i class="bi bi-people-fill me-1"></i>Batch Enrollment
                        </a>
                        <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-info">
                            <i class="bi bi-clock me-1"></i>Pending Registrations
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(isset($dbError))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $dbError }}
                        </div>
                    @endif
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $totalEnrollments }}</h3>
                                            <p class="mb-0">Total Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-people fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $activeEnrollments }}</h3>
                                            <p class="mb-0">Active Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-check-circle fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $pendingEnrollments }}</h3>
                                            <p class="mb-0">Pending Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-clock fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $completedCourses }}</h3>
                                            <p class="mb-0">Completed Courses</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-trophy fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye me-2"></i>View Pending Registrations
                                        </a>
                                        <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                                            <i class="bi bi-clock-history me-2"></i>View Registration History
                                        </a>
                                        <a href="{{ route('admin.student.enrollment.batch') }}" class="btn btn-outline-success">
                                            <i class="bi bi-people-fill me-2"></i>Manage Batch Enrollments
                                        </a>
                                        <a href="{{ route('admin.batches.create') }}" class="btn btn-outline-warning">
                                            <i class="bi bi-plus-circle me-2"></i>Create New Batch
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Recent enrollment activities will be displayed here.</p>
                                    <!-- Add recent activity list here -->
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
