@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person"></i> Student Details</h2>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Students
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Student Information -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-circle"></i> Student Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Student ID:</strong></td>
                                            <td>{{ $student->student_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>First Name:</strong></td>
                                            <td>{{ $student->firstname }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Middle Name:</strong></td>
                                            <td>{{ $student->middlename ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Name:</strong></td>
                                            <td>{{ $student->lastname }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $student->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>School:</strong></td>
                                            <td>{{ $student->student_school ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Contact Number:</strong></td>
                                            <td>{{ $student->contact_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Emergency Contact:</strong></td>
                                            <td>{{ $student->emergency_contact_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $student->city ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>State/Province:</strong></td>
                                            <td>{{ $student->state_province ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Zip Code:</strong></td>
                                            <td>{{ $student->zipcode ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Start Date:</strong></td>
                                            <td>{{ $student->Start_Date ? \Carbon\Carbon::parse($student->Start_Date)->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status and Program -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Status & Program</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($student->date_approved)
                                            <span class="badge bg-success">Approved</span>
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($student->date_approved)->format('M d, Y') }}</small>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Program:</strong></td>
                                    <td>{{ $student->program_name ?? 'No Program' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Package:</strong></td>
                                    <td>{{ $student->package_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Plan:</strong></td>
                                    <td>{{ $student->plan_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registration Date:</strong></td>
                                    <td>{{ $student->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>

                            <div class="mt-3">
                                @if($student->date_approved)
                                    <form method="POST" action="{{ route('admin.students.disapprove', $student) }}" 
                                          class="d-inline" onsubmit="return confirm('Remove approval for this student?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="bi bi-x-circle"></i> Revoke Approval
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.students.approve', $student) }}" 
                                          class="d-inline" onsubmit="return confirm('Approve this student?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-circle"></i> Approve Student
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            @if($student->street_address)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Address Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Street Address:</strong> {{ $student->street_address }}</p>
                            <p><strong>Full Address:</strong> 
                                {{ $student->street_address }}
                                @if($student->city), {{ $student->city }}@endif
                                @if($student->state_province), {{ $student->state_province }}@endif
                                @if($student->zipcode) {{ $student->zipcode }}@endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endpush
