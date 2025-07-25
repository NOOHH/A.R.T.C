@extends('admin.admin-dashboard-layout')

@section('title', 'Director Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Director Dashboard</h1>
                <div class="text-muted">
                    Welcome, {{ $director->directors_first_name }} {{ $director->directors_last_name }}
                </div>
            </div>

            <!-- Analytics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Accessible Programs</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['accessible_programs'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Students</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['total_students'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Modules</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['total_modules'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Registrations</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['pending_registrations'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Registrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Registrations</h6>
                </div>
                <div class="card-body">
                    @if($recentRegistrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRegistrations as $registration)
                                    <tr>
                                        <td>{{ $registration->firstname }} {{ $registration->lastname }}</td>
                                        <td>Unknown</td>
                                        <td>{{ $registration->email ?? 'Not provided' }}</td>
                                        <td>{{ date('M d, Y', strtotime($registration->created_at)) }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                @if($registration->date_approved)
                                                    Approved
                                                @else
                                                    Pending
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No recent registrations</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Accessible Programs -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Your Accessible Programs</h6>
                </div>
                <div class="card-body">
                    @if($programs->count() > 0)
                        <div class="row">
                            @foreach($programs as $program)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $program->program_name }}</h5>
                                        <p class="card-text">{{ $program->program_description ?? 'No description available' }}</p>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $program->modules_count }} modules</small>
                                            <small class="text-muted">{{ $program->students_count }} students</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No programs assigned to you</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df!important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a!important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc!important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e!important;
}

.text-primary {
    color: #4e73df!important;
}

.text-success {
    color: #1cc88a!important;
}

.text-info {
    color: #36b9cc!important;
}

.text-warning {
    color: #f6c23e!important;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15)!important;
}
</style>
@endsection
