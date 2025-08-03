@extends('admin.admin-dashboard-layout')

@section('title', 'Student Registration')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-header.bg-primary { background-color: #0d6efd !important; }
        .card-header.bg-info { background-color: #0dcaf0 !important; }
        .card-header.bg-success { background-color: #198754 !important; }
        .card-header.bg-warning { background-color: #ffc107 !important; }
        
        .modal-xl {
            max-width: 1200px;
        }
        
        @media (max-width: 768px) {
            .modal-xl {
                max-width: 95%;
            }
        }
        
        .document-link {
            text-decoration: none;
        }
        
        .document-link:hover {
            text-decoration: underline;
        }
        
        .badge {
            font-size: 0.875em;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .btn-group .btn {
            margin-right: 2px;
        }
        
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    {{ (isset($history) && $history) ? 'Student Registration History' : 'Student Registration Pending' }}
                </h1>
                <div class="d-flex gap-2">
                    @if(isset($history) && $history)
                        <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-primary">
                            <i class="bi bi-clock"></i> Pending
                        </a>
                        <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                            <i class="bi bi-credit-card"></i> Payment Pending
                        </a>
                    @else
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-x-circle"></i> Rejected
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.student.registration.rejected') }}">
                                    <i class="bi bi-person-x me-2"></i>Registration Rejected
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.student.registration.payment.rejected') }}">
                                    <i class="bi bi-credit-card-2-front-fill me-2"></i>Payment Rejected
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                            <i class="bi bi-credit-card"></i> Payment Pending
                        </a>
                        <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                            <i class="bi bi-clock-history"></i> Registration History
                        </a>
                    @endif
                </div>
            </div>

            {{-- Registration Rejected Table --}}
            @if(!isset($history) || !$history)
                @php
                    $rejectedRegistrations = \App\Models\Registration::where('status', 'rejected')->orderBy('rejected_at', 'desc')->get();
                @endphp
                @if($rejectedRegistrations->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-danger text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-exclamation-triangle me-2"></i>Students with Rejected Registration
                        </h6>
                        <div>
                            <small>Total: {{ $rejectedRegistrations->count() }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Rejected Date</th>
                                        <th>Rejection Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedRegistrations as $registration)
                                    <tr>
                                        <td>
                                            {{ ($registration->firstname ?? '') }} 
                                            {{ ($registration->middlename ?? '') }}
                                            {{ ($registration->lastname ?? '') }}
                                        </td>
                                        <td>
                                            @if(isset($registration->user) && $registration->user)
                                                {{ $registration->user->email ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $registration->program_name ?? 'N/A' }}</td>
                                        <td>{{ $registration->package_name ?? 'N/A' }}</td>
                                        <td>{{ $registration->rejected_at ? \Carbon\Carbon::parse($registration->rejected_at)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $registration->rejection_reason }}">
                                                {{ $registration->rejection_reason ?? 'No reason provided' }}
                                            </div>
                                        </td>
                                        <td><span class="badge bg-danger">Rejected</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewRejectedRegistrationDetails('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="editRejectedFields('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-pencil"></i> Edit Rejection
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary" style="display:inline-block !important;" onclick="undoRejection('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Undo
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Registration Resubmission Table --}}
                @php
                    $resubmittedRegistrations = \App\Models\Registration::where('status', 'resubmitted')->orderBy('resubmitted_at', 'desc')->get();
                @endphp
                @if($resubmittedRegistrations->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-arrow-repeat me-2"></i>Students with Pending Registration Resubmission
                        </h6>
                        <div>
                            <small>Total: {{ $resubmittedRegistrations->count() }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Resubmitted Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resubmittedRegistrations as $registration)
                                    <tr>
                                        <td>
                                            {{ ($registration->firstname ?? '') }} 
                                            {{ ($registration->middlename ?? '') }}
                                            {{ ($registration->lastname ?? '') }}
                                        </td>
                                        <td>
                                            @if(isset($registration->user) && $registration->user)
                                                {{ $registration->user->email ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $registration->program_name ?? 'N/A' }}</td>
                                        <td>{{ $registration->package_name ?? 'N/A' }}</td>
                                        <td>{{ $registration->resubmitted_at ? \Carbon\Carbon::parse($registration->resubmitted_at)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">Previous Rejected</span>
                                            <span class="badge bg-info">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewResubmissionComparison('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="approveResubmission('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectResubmission('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            @else
                @php
                    $approvedRegistrations = $registrations->where('status', 'approved');
                @endphp
                @if($approvedRegistrations->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-person-check-fill me-2"></i>Students with Approved Registration
                        </h6>
                        <div>
                            <small class="text-muted">Total: {{ $approvedRegistrations->count() }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Course</th>
                                        <th>Plan Type</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedRegistrations as $registration)
                                    <tr>
                                        <td>
                                            {{ ($registration->firstname ?? ($registration->student->firstname ?? '')) }} 
                                            {{ ($registration->middlename ?? ($registration->student->middlename ?? '')) }}
                                            {{ ($registration->lastname ?? ($registration->student->lastname ?? '')) }}
                                        </td>
                                        <td>
                                            @if(isset($registration->user) && $registration->user)
                                                {{ $registration->user->email ?? 'N/A' }}
                                            @elseif(isset($registration->email))
                                                {{ $registration->email }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A') }}</td>
                                        <td>{{ $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A') }}</td>
                                        <td>
                                            @if($registration->enrollment_type === 'modular' || $registration->enrollment_type === 'Modular')
                                                @php
                                                    $selectedCourses = [];
                                                    if (isset($registration->selected_courses) && is_string($registration->selected_courses)) {
                                                        $selectedCourses = json_decode($registration->selected_courses, true) ?? [];
                                                    } elseif (isset($registration->selected_courses) && is_array($registration->selected_courses)) {
                                                        $selectedCourses = $registration->selected_courses;
                                                    }
                                                    
                                                    $selectedModules = [];
                                                    if (isset($registration->selected_modules) && is_string($registration->selected_modules)) {
                                                        $selectedModules = json_decode($registration->selected_modules, true) ?? [];
                                                    } elseif (isset($registration->selected_modules) && is_array($registration->selected_modules)) {
                                                        $selectedModules = $registration->selected_modules;
                                                    }
                                                @endphp
                                                
                                                @if(!empty($selectedCourses))
                                                    @php
                                                        $courseNames = [];
                                                        foreach($selectedCourses as $courseId) {
                                                            $course = \App\Models\Course::find($courseId);
                                                            if ($course) {
                                                                $courseNames[] = $course->course_name ?? $course->subject_name ?? "Course #{$courseId}";
                                                            } else {
                                                                $courseNames[] = "Course #{$courseId}";
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="small">
                                                        <strong>Selected Courses:</strong><br>
                                                        {{ implode(', ', $courseNames) }}
                                                    </div>
                                                @elseif(!empty($selectedModules))
                                                    @php
                                                        $moduleNames = [];
                                                        foreach($selectedModules as $moduleData) {
                                                            $moduleId = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
                                                            if ($moduleId) {
                                                                $module = \App\Models\Module::find($moduleId);
                                                                if ($module) {
                                                                    $moduleNames[] = $module->module_name ?? "Module #{$moduleId}";
                                                                } else {
                                                                    $moduleNames[] = "Module #{$moduleId}";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="small">
                                                        <strong>Selected Modules:</strong><br>
                                                        {{ implode(', ', $moduleNames) }}
                                                    </div>
                                                @else
                                                    <span class="text-warning">No Course/Module Selected</span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A ({{ ucfirst($registration->enrollment_type ?? 'N/A') }})</span>
                                            @endif
                                        </td>
                                        <td>{{ $registration->plan_name ?? ($registration->enrollment_type ? ucfirst($registration->enrollment_type) : 'N/A') }}</td>
                                        <td>{{ $registration->created_at ? \Carbon\Carbon::parse($registration->created_at)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ ucfirst($registration->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewRegistrationDetails('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ (isset($history) && $history) ? 'Students Registration History' : 'Students with Pending Registration' }}
                    </h6>
                    <div>
                        <small class="text-muted">Total: {{ $registrations->count() }}</small>
                    </div>
                </div>
                <div class="card-body">
                    @if($registrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Course</th>
                                        <th>Plan Type</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                    <tr>
                                        <td>
                                            {{ ($registration->firstname ?? ($registration->student->firstname ?? '')) }} 
                                            {{ ($registration->middlename ?? ($registration->student->middlename ?? '')) }}
                                            {{ ($registration->lastname ?? ($registration->student->lastname ?? '')) }}
                                        </td>
                                        <td>
                                            @if(isset($registration->user) && $registration->user)
                                                {{ $registration->user->email ?? 'N/A' }}
                                            @elseif(isset($registration->email))
                                                {{ $registration->email }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A') }}</td>
                                        <td>{{ $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A') }}</td>
                                        <td>
                                            @if($registration->enrollment_type === 'modular' || $registration->enrollment_type === 'Modular')
                                                @php
                                                    $selectedCourses = [];
                                                    if (isset($registration->selected_courses) && is_string($registration->selected_courses)) {
                                                        $selectedCourses = json_decode($registration->selected_courses, true) ?? [];
                                                    } elseif (isset($registration->selected_courses) && is_array($registration->selected_courses)) {
                                                        $selectedCourses = $registration->selected_courses;
                                                    }
                                                    
                                                    $selectedModules = [];
                                                    if (isset($registration->selected_modules) && is_string($registration->selected_modules)) {
                                                        $selectedModules = json_decode($registration->selected_modules, true) ?? [];
                                                    } elseif (isset($registration->selected_modules) && is_array($registration->selected_modules)) {
                                                        $selectedModules = $registration->selected_modules;
                                                    }
                                                @endphp
                                                
                                                @if(!empty($selectedCourses))
                                                    @php
                                                        $courseNames = [];
                                                        foreach($selectedCourses as $courseId) {
                                                            $course = \App\Models\Course::find($courseId);
                                                            if ($course) {
                                                                $courseNames[] = $course->course_name ?? $course->subject_name ?? "Course #{$courseId}";
                                                            } else {
                                                                $courseNames[] = "Course #{$courseId}";
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="small">
                                                        <strong>Selected Courses:</strong><br>
                                                        {{ implode(', ', $courseNames) }}
                                                    </div>
                                                @elseif(!empty($selectedModules))
                                                    @php
                                                        $moduleNames = [];
                                                        foreach($selectedModules as $moduleData) {
                                                            $moduleId = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
                                                            if ($moduleId) {
                                                                $module = \App\Models\Module::find($moduleId);
                                                                if ($module) {
                                                                    $moduleNames[] = $module->module_name ?? "Module #{$moduleId}";
                                                                } else {
                                                                    $moduleNames[] = "Module #{$moduleId}";
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="small">
                                                        <strong>Selected Modules:</strong><br>
                                                        {{ implode(', ', $moduleNames) }}
                                                    </div>
                                                @else
                                                    <span class="text-warning">No Course/Module Selected</span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A ({{ ucfirst($registration->enrollment_type ?? 'N/A') }})</span>
                                            @endif
                                        </td>
                                        <td>{{ $registration->plan_name ?? ($registration->enrollment_type ? ucfirst($registration->enrollment_type) : 'N/A') }}</td>
                                        <td>{{ $registration->created_at ? \Carbon\Carbon::parse($registration->created_at)->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @if($registration->status === 'approved')
                                                <span class="badge bg-success">{{ ucfirst($registration->status) }}</span>
                                            @elseif($registration->status === 'rejected')
                                                <span class="badge bg-danger">{{ ucfirst($registration->status) }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($registration->status ?? 'pending') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(isset($history) && $history)
                                                    <!-- History view - only view and undo buttons -->
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewRegistrationDetails('{{ $registration->registration_id }}')">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                    @if($registration->status === 'approved')
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                onclick="undoApproval('{{ $registration->registration_id }}')">
                                                            <i class="bi bi-arrow-counterclockwise"></i> Undo
                                                        </button>
                                                    @endif
                                                @else
                                                    <!-- Pending view - approve, view, reject buttons -->
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            onclick="approveRegistration('{{ $registration->registration_id }}')">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewRegistrationDetails('{{ $registration->registration_id }}')">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="rejectRegistration('{{ $registration->registration_id }}')">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-plus" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">
                                {{ (isset($history) && $history) ? 'No Registration History' : 'No Pending Registrations' }}
                            </h5>
                            <p class="text-muted">
                                {{ (isset($history) && $history) ? 'No student registrations have been processed yet.' : 'All student registrations have been processed.' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Enhanced Registration Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">Registration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="registration-details-content">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="rejectReasonForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectReasonModalLabel">Reject Registration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Mark the fields that need to be corrected by the student. These fields will be highlighted in red when the student views their registration.
                        </div>
                        
                        <!-- Registration details will be loaded here -->
                        <div id="rejectionFieldsContainer">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="rejectionReason" class="form-label">General Notes/Comments <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectionReason" name="reason" rows="4" 
                                      placeholder="Please provide clear instructions for what needs to be corrected..." required></textarea>
                            <div class="form-text">This message will be sent to the student along with the marked fields.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Reject Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resubmission Comparison Modal -->
    <div class="modal fade" id="resubmissionComparisonModal" tabindex="-1" aria-labelledby="resubmissionComparisonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resubmissionComparisonModalLabel">Registration Resubmission Comparison</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Previous Rejected Information</h6>
                            <div id="previousRegistrationData" class="border p-3 bg-light">
                                <!-- Previous data will be loaded here -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="bi bi-arrow-clockwise"></i> New Resubmitted Information</h6>
                            <div id="newRegistrationData" class="border p-3">
                                <!-- New data will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="approveResubmissionBtn">
                        <i class="bi bi-check-circle"></i> Approve Resubmission
                    </button>
                    <button type="button" class="btn btn-danger" id="rejectResubmissionBtn">
                        <i class="bi bi-x-circle"></i> Reject Again
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Rejected Fields Modal -->
    <div class="modal fade" id="editRejectedFieldsModal" tabindex="-1" aria-labelledby="editRejectedFieldsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editRejectedFieldsForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRejectedFieldsModalLabel">Edit Rejection Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="editRejectionFieldsContainer">
                            <!-- Fields will be loaded here -->
                        </div>
                        
                        <div class="mt-4">
                            <label for="editRejectionReason" class="form-label">Updated Notes/Comments</label>
                            <textarea class="form-control" id="editRejectionReason" name="reason" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Update Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Document Image Preview Modal -->
    <div class="modal fade" id="documentImageModal" tabindex="-1" aria-labelledby="documentImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentImageModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="documentImageModalBody">
                    <!-- Image or PDF will be injected here -->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Global variables and functions that need to be available immediately
let registrationModal;
let rejectReasonModal;
const baseUrl = window.location.origin;
const token = document.querySelector('meta[name="csrf-token"]')?.content;
const isHistory = '{{ isset($history) && $history ? "true" : "false" }}' === 'true';

// Helper functions
function na(value) {
    return (value === undefined || value === null || value === '' || value === 'null') ? 'N/A' : value;
}

function formatDocumentLink(filename, label) {
    if (!filename || filename === 'N/A' || filename === '') {
        return `<span class="badge bg-secondary">Not uploaded</span>`;
    }
    
    // Clean the filename
    const cleanFilename = filename.replace(/^storage\//, '').replace(/^\//, '');
    const ext = cleanFilename.split('.').pop().toLowerCase();
    const isImage = ['jpg','jpeg','png','gif','webp'].includes(ext);
    const isPdf = ext === 'pdf';
    
    let viewBtn = '';
    if (isImage || isPdf) {
        viewBtn = `<button type="button" class="btn btn-outline-primary btn-sm" onclick="showDocumentPreview('${cleanFilename}','${label}')">
            <i class="bi bi-eye"></i> View ${label}
        </button>`;
    } else {
        viewBtn = `<a href="${baseUrl}/storage/documents/${cleanFilename}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-download"></i> Download ${label}
        </a>`;
    }
    return `<span class="badge bg-success me-2">Uploaded</span>${viewBtn}`;
}

// Define global functions IMMEDIATELY to ensure they're available for inline handlers
window.viewRegistrationDetails = function(registrationId) {
    console.log('🔍 Opening registration details modal for:', registrationId);
    
    if (!registrationId) {
        console.error('Invalid registration ID provided');
        alert('Invalid registration ID');
        return;
    }
    
    // Show modal using the exact same pattern as payment modal
    const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
    modal.show();
    
    // Load the registration details
    loadRegistrationDetails(registrationId);
};

// Function to load registration details (matching payment modal structure exactly)
function loadRegistrationDetails(registrationId) {
    const detailsContainer = document.getElementById('registration-details-content');
    detailsContainer.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                detailsContainer.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            // Create the exact same structure as your payment modal with professional cards
            detailsContainer.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-person"></i> Student Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Name:</strong></div>
                                    <div class="col-sm-8">${data.user_info?.full_name || (data.firstname || '') + ' ' + (data.lastname || '') || 'N/A'}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">${data.user_info?.email || data.email || 'N/A'}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Registration ID:</strong></div>
                                    <div class="col-sm-8">${data.registration_id || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-mortarboard"></i> Program Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Program:</strong></div>
                                    <div class="col-sm-8">${data.program_name || 'N/A'}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Package:</strong></div>
                                    <div class="col-sm-8">${data.package_name || 'N/A'}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Type:</strong></div>
                                    <div class="col-sm-8">${data.enrollment_type || 'Full'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Registration Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Status:</strong> 
                                            <span class="badge ${data.status === 'approved' ? 'bg-success' : data.status === 'rejected' ? 'bg-danger' : 'bg-warning'}">${data.status || 'pending'}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Learning Mode:</strong> ${data.learning_mode || 'Not specified'}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Registration Date:</strong> ${data.created_at_formatted || data.created_at || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading registration details:', error);
            detailsContainer.innerHTML = `<div class="alert alert-danger">Error loading registration details</div>`;
        });
}
};

window.approveRegistration = function(registrationId) {
    console.log('approveRegistration called with ID:', registrationId);
    if (confirm('Are you sure you want to approve this registration?')) {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/registration/${registrationId}/approve`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
};

window.rejectRegistration = function(registrationId) {
    console.log('rejectRegistration called with ID:', registrationId);
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/registration/${registrationId}/reject`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content;
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        
        form.appendChild(csrfInput);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
};

// DUPLICATE REMOVED: Global function for viewing registration details
// window.viewRegistrationDetails = function(registrationId) {
    /*if (!registrationId) {
        alert('Invalid registration ID');
        return;
    }
    
    if (!registrationModal) {
        registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
    }
    
    const modalDetails = document.getElementById('modal-details');
    const modalActions = document.getElementById('modal-actions');
    
    modalDetails.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    registrationModal.show();

    fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch details');
            }
            return response.json();
        })
        .then(data => {
            // Left Column - Personal Information
            let leftColumn = `
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-person"></i> Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Full Name:</strong></div>
                                <div class="col-sm-8">${na(data.firstname)} ${na(data.middlename)} ${na(data.lastname)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">${na(data.email)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Phone:</strong></div>
                                <div class="col-sm-8">${na(data.contact_number || data.mobile_number)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Address:</strong></div>
                                <div class="col-sm-8">${na(data.street_address)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>City:</strong></div>
                                <div class="col-sm-8">${na(data.city)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Province:</strong></div>
                                <div class="col-sm-8">${na(data.province)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>ZIP Code:</strong></div>
                                <div class="col-sm-8">${na(data.zip_code)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Education:</strong></div>
                                <div class="col-sm-8">${na(data.educational_attainment)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>School:</strong></div>
                                <div class="col-sm-8">${na(data.school)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Course:</strong></div>
                                <div class="col-sm-8">${na(data.course)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Batch Date:</strong></div>
                                <div class="col-sm-8">${na(data.batch_start_date)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge ${data.status === 'approved' ? 'bg-success' : 
                                                      data.status === 'rejected' ? 'bg-danger' : 'bg-warning'}">${na(data.status)}</span>
                                </div>
                            </div>
                            ${data.rejection_reason ? `
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Rejection Reason:</strong></div>
                                <div class="col-sm-8 text-danger">${na(data.rejection_reason)}</div>
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;

            // Right Column - Documents and Additional Info
            let rightColumn = `
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="bi bi-files"></i> Documents</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>2x2 Photo:</strong></div>
                                <div class="col-sm-8">${formatDocumentLink(data.photo_2x2, '2x2 Photo')}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>PSA Birth Certificate:</strong></div>
                                <div class="col-sm-8">${formatDocumentLink(data.PSA, 'PSA Birth Certificate')}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Diploma:</strong></div>
                                <div class="col-sm-8">${formatDocumentLink(data.diploma, 'Diploma')}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>TOR:</strong></div>
                                <div class="col-sm-8">${formatDocumentLink(data.TOR, 'Transcript of Records')}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-mortarboard"></i> Program Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Program:</strong></div>
                                <div class="col-sm-8">${na(data.program_title)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Package:</strong></div>
                                <div class="col-sm-8">${na(data.package_name)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Plan:</strong></div>
                                <div class="col-sm-8">${na(data.plan_name)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>Registration Date:</strong></div>
                                <div class="col-sm-8">${na(data.created_at_formatted)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            modalDetails.innerHTML = leftColumn + rightColumn;

            // Setup action buttons based on status and view type
            let actionButtons = '';
            if (isHistory) {
                if (data.status === 'approved') {
                    actionButtons = `
                        <button type="button" class="btn btn-warning" onclick="undoApproval('${registrationId}')">
                            <i class="bi bi-arrow-counterclockwise"></i> Undo Approval
                        </button>
                    `;
                }
            } else {
                // Pending view - show approve/reject buttons
                actionButtons = `
                    <button type="button" class="btn btn-success" onclick="approveRegistration('${registrationId}')">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" onclick="rejectRegistration('${registrationId}')">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                `;
            }

            modalActions.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                ${actionButtons}
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            modalDetails.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Failed to load registration details. Please try again.
                    </div>
                </div>
            `;
            modalActions.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            `;
        });
        */ // END DUPLICATE FUNCTION COMMENT

// Note: Duplicate function definitions removed to prevent conflicts
// Global functions are defined at the top of the script section

// Helper functions that need to be available globally
// Document formatting function already defined above

// Document preview function
window.showDocumentPreview = function(filename, label) {
    const ext = filename.split('.').pop().toLowerCase();
    const modal = new bootstrap.Modal(document.getElementById('documentImageModal'));
    const modalBody = document.getElementById('documentImageModalBody');
    let content = '';
    if (["jpg","jpeg","png","gif","webp"].includes(ext)) {
        content = `<img src="${baseUrl}/storage/documents/${filename}" alt="${label}" class="img-fluid" style="max-height:70vh;">`;
    } else if (ext === 'pdf') {
        content = `<iframe src="${baseUrl}/storage/documents/${filename}" style="width:100%;height:70vh;" frameborder="0"></iframe>`;
    } else {
        content = `<a href="${baseUrl}/storage/documents/${filename}" target="_blank">Download ${label}</a>`;
    }
    modalBody.innerHTML = content;
    document.getElementById('documentImageModalLabel').textContent = label + ' Preview';
    modal.show();
};

// Load registration fields for rejection marking
function loadRegistrationFieldsForRejection(registrationId) {
    const container = document.getElementById('rejectionFieldsContainer');
    
    fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
        .then(response => response.json())
        .then(data => {
            let fieldsHtml = '<div class="row">';
            
            // Create checkboxes for marking fields that need correction
            const fields = [
                { name: 'firstname', label: 'First Name', value: data.firstname },
                { name: 'lastname', label: 'Last Name', value: data.lastname },
                { name: 'contact_number', label: 'Contact Number', value: data.contact_number },
                { name: 'street_address', label: 'Address', value: data.street_address },
                { name: 'PSA', label: 'PSA Birth Certificate', value: 'Document' },
                { name: 'diploma', label: 'Diploma', value: 'Document' },
                { name: 'TOR', label: 'Transcript of Records', value: 'Document' }
            ];
            
            fieldsHtml += '<div class="col-12"><h6>Select fields to mark as needing correction:</h6>';
            
            fields.forEach(field => {
                fieldsHtml += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="${field.name}" id="reject_${field.name}">
                        <label class="form-check-label" for="reject_${field.name}">${field.label}: ${field.value || 'N/A'}</label>
                    </div>
                `;
            });
            
            fieldsHtml += '</div>';
            container.innerHTML = fieldsHtml;
        })
        .catch(error => {
            console.error('Error loading fields:', error);
            container.innerHTML = '<div class="alert alert-danger">Failed to load registration fields.</div>';
        });
}

// Function to show confirmation modal
window.showConfirmModal = function(title, message, confirmText, confirmClass, onConfirm, options = {}) {
    // Create modal if it doesn't exist
    let confirmModal = document.getElementById('confirmActionModal');
    if (!confirmModal) {
        const modalHtml = `
            <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmActionModalTitle"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="confirmActionModalMessage"></p>
                            <div id="confirmActionModalExtra"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn" id="confirmActionBtn">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        confirmModal = document.getElementById('confirmActionModal');
    }

    // Update modal content
    document.getElementById('confirmActionModalTitle').textContent = title;
    document.getElementById('confirmActionModalMessage').textContent = message;
    const extraDiv = document.getElementById('confirmActionModalExtra');
    extraDiv.innerHTML = '';
    if (options.reason) {
        extraDiv.innerHTML = `<label for='undoReasonInput' class='form-label mt-2'>Reason <span class='text-danger'>*</span></label><textarea id='undoReasonInput' class='form-control' rows='3' required placeholder='Please provide a reason...'></textarea><div class='invalid-feedback'>Reason is required.</div>`;
    }

    const confirmBtn = document.getElementById('confirmActionBtn');
    confirmBtn.textContent = confirmText;
    confirmBtn.className = `btn ${confirmClass}`;
    
    // Remove any existing event listeners and add new one
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    const newConfirmBtn = document.getElementById('confirmActionBtn');
    
    newConfirmBtn.addEventListener('click', function() {
        if (options.reason) {
            const reasonInput = document.getElementById('undoReasonInput');
            if (!reasonInput.value.trim()) {
                reasonInput.classList.add('is-invalid');
                reasonInput.focus();
                return;
            } else {
                reasonInput.classList.remove('is-invalid');
            }
        }
        onConfirm();
        const modal = bootstrap.Modal.getInstance(confirmModal);
        modal.hide();
    });

    // Show the modal
    const modal = new bootstrap.Modal(confirmModal);
    modal.show();
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    if (!registrationModal) {
        registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
    }
    if (!rejectReasonModal) {
        rejectReasonModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
    }

    // DUPLICATE REMOVED: Global function for viewing registration details using enhanced modal
    /*window.viewRegistrationDetails = function(registrationId) {
        if (!registrationId) {
            alert('Invalid registration ID');
            return;
        }

        fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch details');
                }
                return response.json();
            })
            .then(data => {
                // Use the enhanced modal from enhanced-registration-modal.js
                if (typeof createEnhancedRegistrationModal === 'function') {
                    createEnhancedRegistrationModal(data);
                } else {
                    console.error('Enhanced modal function not found');
                    alert('Error: Enhanced modal not loaded properly');
                }
            })
            .catch(error => {
                console.error('Error fetching registration details:', error);
                alert('Error loading registration details: ' + error.message);
            });
    };
    // Global function for approving registration
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-person"></i> Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Full Name:</strong></div>
                                    <div class="col-sm-8">${na(data.firstname)} ${na(data.middlename)} ${na(data.lastname)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">${na(data.email)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Phone:</strong></div>
                                    <div class="col-sm-8">${na(data.contact_number || data.mobile_number)}</div>
                                </div>
                                ${data.telephone_number ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Telephone:</strong></div>
                                    <div class="col-sm-8">${na(data.telephone_number)}</div>
                                </div>
                                ` : ''}
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Emergency Contact:</strong></div>
                                    <div class="col-sm-8">${na(data.emergency_contact_number)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Gender:</strong></div>
                                    <div class="col-sm-8">${na(data.gender)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Birthdate:</strong></div>
                                    <div class="col-sm-8">${na(data.birthdate)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Age:</strong></div>
                                    <div class="col-sm-8">${na(data.age)}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Address Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Address:</strong></div>
                                    <div class="col-sm-8">${na(data.street_address || data.address)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>City:</strong></div>
                                    <div class="col-sm-8">${na(data.city)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>State/Province:</strong></div>
                                    <div class="col-sm-8">${na(data.state_province || data.province)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>ZIP Code:</strong></div>
                                    <div class="col-sm-8">${na(data.zipcode)}</div>
                                </div>
                                ${data.school_name || data.student_school ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>School:</strong></div>
                                    <div class="col-sm-8">${na(data.school_name || data.student_school)}</div>
                                </div>
                                ` : ''}
                                ${data.referral_code ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Referral Code:</strong></div>
                                    <div class="col-sm-8"><span class="badge bg-secondary">${na(data.referral_code)}</span></div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;

                // Right Column - Academic & Documents
                let rightColumn = `
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-mortarboard"></i> Program Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Program:</strong></div>
                                    <div class="col-sm-8">${na(data.program_name)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Package:</strong></div>
                                    <div class="col-sm-8">${na(data.package_name)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Plan Type:</strong></div>
                                    <div class="col-sm-8">${na(data.plan_type || data.enrollment_type)}</div>
                                </div>
                                ${data.learning_mode ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Learning Mode:</strong></div>
                                    <div class="col-sm-8"><span class="badge bg-info">${na(data.learning_mode)}</span></div>
                                </div>
                                ` : ''}
                                ${data.registration_mode ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Registration Mode:</strong></div>
                                    <div class="col-sm-8"><span class="badge bg-secondary">${na(data.registration_mode)}</span></div>
                                </div>
                                ` : ''}
                                ${data.education_level ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Education Level:</strong></div>
                                    <div class="col-sm-8">${na(data.education_level)}</div>
                                </div>
                                ` : ''}`;
                
                // Add course information for all enrollments
                if (data.course_info && data.course_info !== 'Full') {
                    rightColumn += `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Selected Course(s):</strong></div>
                                    <div class="col-sm-8">
                                        <div class="small text-wrap">${data.course_info}</div>
                                    </div>
                                </div>`;
                } else if (data.plan_type === 'Full' || data.enrollment_type === 'full') {
                    rightColumn += `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Course Type:</strong></div>
                                    <div class="col-sm-8"><span class="badge bg-info">Full Program</span></div>
                                </div>`;
                }

                rightColumn += `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Start Date:</strong></div>
                                    <div class="col-sm-8">${na(data.Start_Date)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Status:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge ${data.status === 'approved' ? 'bg-success' : data.status === 'rejected' ? 'bg-danger' : 'bg-warning'}">
                                            ${na(data.status)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Documents</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>PSA Birth Certificate:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.PSA, 'PSA')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Transcript of Records:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.TOR, 'TOR')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Course Certificate:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.Course_Cert, 'Course Certificate')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Good Moral:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.good_moral, 'Good Moral')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>2x2 Photo:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.photo_2x2, '2x2 Photo')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Certificate of Graduation:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.Cert_of_Grad, 'Certificate of Graduation')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Valid ID:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.valid_id, 'Valid ID')}</div>
                                </div>
                                ${data.birth_certificate ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Birth Certificate:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.birth_certificate, 'Birth Certificate')}</div>
                                </div>
                                ` : ''}
                                ${data.diploma_certificate ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Diploma:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.diploma_certificate, 'Diploma')}</div>
                                </div>
                                ` : ''}
                                ${data.Undergraduate ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Undergraduate Docs:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.Undergraduate, 'Undergraduate Documents')}</div>
                                </div>
                                ` : ''}
                                ${data.Graduate ? `
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Graduate Docs:</strong></div>
                                    <div class="col-sm-8">${formatDocumentLink(data.Graduate, 'Graduate Documents')}</div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        
                        <!-- Dynamic Fields Section -->
                        <div class="card mb-3" id="dynamicFieldsCard" style="display:none;">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="bi bi-gear"></i> Additional Information</h6>
                            </div>
                            <div class="card-body" id="dynamicFieldsContent">
                            </div>
                        </div>
                    </div>
                `;

                modalDetails.innerHTML = leftColumn + rightColumn;
                
                // Add dynamic fields that weren't explicitly handled
                const handledFields = ['registration_id', 'firstname', 'middlename', 'lastname', 'email', 
                    'contact_number', 'mobile_number', 'emergency_contact_number', 'telephone_number', 'gender', 
                    'birthdate', 'age', 'street_address', 'address', 'city', 'state_province', 'province', 
                    'zipcode', 'school_name', 'student_school', 'program_name', 'package_name', 'plan_name', 
                    'plan_type', 'course_info', 'learning_mode', 'registration_mode', 'education_level', 
                    'Start_Date', 'start_date', 'status', 'PSA', 'TOR', 'Course_Cert', 'good_moral', 
                    'photo_2x2', 'birth_certificate', 'diploma_certificate', 'Cert_of_Grad', 'valid_id', 
                    'Undergraduate', 'Graduate', 'referral_code', 'selected_modules', 'selected_courses_dynamic', 
                    'created_at'];
                    
                let dynamicFieldsHtml = '';
                let hasDynamicFields = false;
                
                for (const [key, value] of Object.entries(data)) {
                    if (!handledFields.includes(key) && value && value !== 'N/A' && value !== '' && value !== null) {
                        hasDynamicFields = true;
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        dynamicFieldsHtml += `
                            <div class="row mb-2">
                                <div class="col-sm-4"><strong>${label}:</strong></div>
                                <div class="col-sm-8">${na(value)}</div>
                            </div>
                        `;
                    }
                }
                
                if (hasDynamicFields) {
                    document.getElementById('dynamicFieldsContent').innerHTML = dynamicFieldsHtml;
                    document.getElementById('dynamicFieldsCard').style.display = 'block';
                }

                // Setup action buttons based on status and view type
                let actionButtons = '';
                if (isHistory) {
                    if (data.status === 'approved') {
                        actionButtons = `
                            <button type="button" class="btn btn-warning" onclick="undoApproval('${registrationId}')">
                                <i class="bi bi-arrow-counterclockwise"></i> Undo Approval
                            </button>
                        `;
                    }
                } else {
                    // Pending view - show approve/reject buttons
                    actionButtons = `
                        <button type="button" class="btn btn-success" onclick="approveRegistration('${registrationId}')">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectRegistration('${registrationId}')">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                    `;
                }

                modalActions.innerHTML = `
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    ${actionButtons}
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                modalDetails.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Failed to load registration details. Please try again.
                        </div>
                    </div>
                `;
                modalActions.innerHTML = `
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                `;
            });
    */  // END DUPLICATE FUNCTION COMMENT

    // Note: Duplicate function definitions removed - using global functions defined at top

    // Load registration fields for rejection marking
    function loadRegistrationFieldsForRejection(registrationId) {
        const container = document.getElementById('rejectionFieldsContainer');
        container.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
        
        fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
            .then(response => response.json())
            .then(data => {
                let fieldsHtml = '<div class="row">';
                
                // Personal Information Fields
                fieldsHtml += `
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-person"></i> Personal Information</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="firstname" id="reject_firstname">
                            <label class="form-check-label" for="reject_firstname">First Name: ${data.firstname || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="middlename" id="reject_middlename">
                            <label class="form-check-label" for="reject_middlename">Middle Name: ${data.middlename || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="lastname" id="reject_lastname">
                            <label class="form-check-label" for="reject_lastname">Last Name: ${data.lastname || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="contact_number" id="reject_contact">
                            <label class="form-check-label" for="reject_contact">Contact Number: ${data.contact_number || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="gender" id="reject_gender">
                            <label class="form-check-label" for="reject_gender">Gender: ${data.gender || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="birthdate" id="reject_birthdate">
                            <label class="form-check-label" for="reject_birthdate">Birthdate: ${data.birthdate || 'N/A'}</label>
                        </div>
                    </div>
                `;
                
                // Address and Documents
                fieldsHtml += `
                    <div class="col-md-6">
                        <h6 class="text-info"><i class="bi bi-geo-alt"></i> Address Information</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="street_address" id="reject_address">
                            <label class="form-check-label" for="reject_address">Address: ${data.street_address || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="city" id="reject_city">
                            <label class="form-check-label" for="reject_city">City: ${data.city || 'N/A'}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="zipcode" id="reject_zipcode">
                            <label class="form-check-label" for="reject_zipcode">ZIP Code: ${data.zipcode || 'N/A'}</label>
                        </div>
                        
                        <h6 class="text-warning mt-3"><i class="bi bi-file-earmark-text"></i> Documents</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="PSA" id="reject_psa">
                            <label class="form-check-label" for="reject_psa">PSA Birth Certificate</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="TOR" id="reject_tor">
                            <label class="form-check-label" for="reject_tor">Transcript of Records</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="good_moral" id="reject_good_moral">
                            <label class="form-check-label" for="reject_good_moral">Good Moral Character</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="Course_Cert" id="reject_course_cert">
                            <label class="form-check-label" for="reject_course_cert">Course Certificate</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="photo_2x2" id="reject_photo">
                            <label class="form-check-label" for="reject_photo">2x2 Photo</label>
                        </div>
                    </div>
                `;
                
                fieldsHtml += '</div>';
                container.innerHTML = fieldsHtml;
            })
            .catch(error => {
                console.error('Error loading fields:', error);
                container.innerHTML = '<div class="alert alert-danger">Failed to load registration fields.</div>';
            });
    }

    // View rejected registration details
    window.viewRejectedRegistrationDetails = function(registrationId) {
        viewRegistrationDetails(registrationId);
    };

    // View resubmission comparison
    window.viewResubmissionComparison = function(registrationId) {
        const modal = new bootstrap.Modal(document.getElementById('resubmissionComparisonModal'));
        
        // Load both original and new data
        Promise.all([
            fetch(`${baseUrl}/admin/registration/${registrationId}/original-data`),
            fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
        ])
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(([originalData, newData]) => {
            displayComparisonData(originalData, newData, registrationId);
            modal.show();
        })
        .catch(error => {
            console.error('Error loading comparison data:', error);
            alert('Failed to load comparison data');
        });
    };

    // Display comparison data
    function displayComparisonData(originalData, newData, registrationId) {
        const previousContainer = document.getElementById('previousRegistrationData');
        const newContainer = document.getElementById('newRegistrationData');
        
        // Helper function to create field display
        function createFieldDisplay(label, value, isRejected = false) {
            const className = isRejected ? 'bg-danger text-white p-2 rounded mb-2' : 'mb-2';
            return `<div class="${className}"><strong>${label}:</strong> ${value || 'N/A'}</div>`;
        }
        
        // Get rejected fields
        const rejectedFields = originalData.rejected_fields ? JSON.parse(originalData.rejected_fields) : [];
        
        // Display previous data with rejected fields highlighted
        let previousHtml = `
            <h6 class="text-danger mb-3">Rejection Reason:</h6>
            <div class="alert alert-danger">${originalData.rejection_reason}</div>
            <h6 class="mb-3">Previous Information:</h6>
        `;
        previousHtml += createFieldDisplay('Name', `${originalData.firstname} ${originalData.middlename} ${originalData.lastname}`, rejectedFields.includes('firstname') || rejectedFields.includes('lastname'));
        previousHtml += createFieldDisplay('Contact', originalData.contact_number, rejectedFields.includes('contact_number'));
        previousHtml += createFieldDisplay('Address', originalData.street_address, rejectedFields.includes('street_address'));
        previousHtml += createFieldDisplay('City', originalData.city, rejectedFields.includes('city'));
        
        // Display new data
        let newHtml = '<h6 class="mb-3">New Information:</h6>';
        newHtml += createFieldDisplay('Name', `${newData.firstname} ${newData.middlename} ${newData.lastname}`);
        newHtml += createFieldDisplay('Contact', newData.contact_number);
        newHtml += createFieldDisplay('Address', newData.street_address);
        newHtml += createFieldDisplay('City', newData.city);
        
        previousContainer.innerHTML = previousHtml;
        newContainer.innerHTML = newHtml;
        
        // Setup buttons
        document.getElementById('approveResubmissionBtn').onclick = () => approveResubmission(registrationId);
        document.getElementById('rejectResubmissionBtn').onclick = () => rejectResubmission(registrationId);
    }

    // Approve resubmission
    window.approveResubmission = function(registrationId) {
        if (confirm('Are you sure you want to approve this resubmission?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${baseUrl}/admin/registration/${registrationId}/approve-resubmission`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = token;
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    };

    // Reject resubmission
    window.rejectResubmission = function(registrationId) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('resubmissionComparisonModal'));
        modal.hide();
        rejectRegistration(registrationId);
    };

    // Edit rejected fields
    window.editRejectedFields = function(registrationId) {
        const modal = new bootstrap.Modal(document.getElementById('editRejectedFieldsModal'));
        const form = document.getElementById('editRejectedFieldsForm');
        form.action = `${baseUrl}/admin/registration/${registrationId}/update-rejection`;
        
        // Load current rejection data
        fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editRejectionReason').value = data.rejection_reason || '';
                loadEditableRejectionFields(registrationId, data.rejected_fields);
                modal.show();
            });
    };

    function loadEditableRejectionFields(registrationId, currentRejectedFields) {
        const container = document.getElementById('editRejectionFieldsContainer');
        const rejectedFields = currentRejectedFields ? JSON.parse(currentRejectedFields) : [];
        
        // Similar to loadRegistrationFieldsForRejection but with current selections
        fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
            .then(response => response.json())
            .then(data => {
                let fieldsHtml = '<div class="row">';
                
                // Create checkboxes with current selections
                const fields = [
                    { name: 'firstname', label: 'First Name', value: data.firstname },
                    { name: 'lastname', label: 'Last Name', value: data.lastname },
                    { name: 'contact_number', label: 'Contact Number', value: data.contact_number },
                    { name: 'street_address', label: 'Address', value: data.street_address },
                    { name: 'PSA', label: 'PSA Birth Certificate', value: 'Document' },
                    { name: 'TOR', label: 'Transcript of Records', value: 'Document' }
                ];
                
                fieldsHtml += '<div class="col-12"><h6>Select fields to mark as needing correction:</h6>';
                
                fields.forEach(field => {
                    const isChecked = rejectedFields.includes(field.name) ? 'checked' : '';
                    fieldsHtml += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="${field.name}" id="edit_${field.name}" ${isChecked}>
                            <label class="form-check-label" for="edit_${field.name}">${field.label}: ${field.value || 'N/A'}</label>
                        </div>
                    `;
                });
                
                fieldsHtml += '</div></div>';
                container.innerHTML = fieldsHtml;
            });
    }

    // Global function for undoing approval
    window.undoApproval = function(registrationId) {
        showConfirmModal(
            'Confirm Undo Approval',
            'Are you sure you want to undo this approval? This will set the registration back to pending status. Please provide a reason for undoing approval.',
            'Yes, Undo',
            'btn-warning',
            function(reason) {
                fetch(`/admin/registrations/${registrationId}/undo-approval`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ undo_reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Registration approval undone successfully!');
                        location.reload();
                    } else {
                        alert('Error undoing registration approval: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Error undoing registration approval: ' + (error && error.message ? error.message : 'Unknown error'));
                });
            },
            { reason: true }
        );
    };

    // Global function for undoing rejection
    window.undoRejection = function(registrationId) {
        showConfirmModal(
            'Confirm Undo Rejection',
            'Are you sure you want to undo this rejection? This will set the registration back to pending status.',
            'Yes, Undo',
            'btn-warning',
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${baseUrl}/admin/registration/${registrationId}/undo-rejection`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = token;
                
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        );
    };

    // Handle rejection form submission
    document.getElementById('rejectReasonForm').addEventListener('submit', function(e) {
        const reasonTextarea = document.getElementById('rejectionReason');
        if (!reasonTextarea.value.trim()) {
            e.preventDefault();
            alert('Please provide a reason for rejection.');
            reasonTextarea.focus();
            return;
        }
    });

    // Function to show confirmation modal
    window.showConfirmModal = function(title, message, confirmText, confirmClass, onConfirm, options = {}) {
        // Create modal if it doesn't exist
        let confirmModal = document.getElementById('confirmActionModal');
        if (!confirmModal) {
            const modalHtml = `
                <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmActionModalTitle"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p id="confirmActionModalMessage"></p>
                                <div id="confirmActionModalExtra"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn" id="confirmActionBtn">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            confirmModal = document.getElementById('confirmActionModal');
        }

        // Update modal content
        document.getElementById('confirmActionModalTitle').textContent = title;
        document.getElementById('confirmActionModalMessage').textContent = message;
        const extraDiv = document.getElementById('confirmActionModalExtra');
        extraDiv.innerHTML = '';
        if (options.reason) {
            extraDiv.innerHTML = `<label for='undoReasonInput' class='form-label mt-2'>Reason <span class='text-danger'>*</span></label><textarea id='undoReasonInput' class='form-control' rows='3' required placeholder='Please provide a reason...'></textarea><div class='invalid-feedback'>Reason is required.</div>`;
        }

        const confirmBtn = document.getElementById('confirmActionBtn');
        confirmBtn.textContent = confirmText;
        confirmBtn.className = `btn ${confirmClass}`;
        
        // Remove any existing event listeners and add new one
        confirmBtn.replaceWith(confirmBtn.cloneNode(true));
        const newConfirmBtn = document.getElementById('confirmActionBtn');
        
        newConfirmBtn.addEventListener('click', function() {
            if (options.reason) {
                const reasonInput = document.getElementById('undoReasonInput');
                if (!reasonInput.value.trim()) {
                    reasonInput.classList.add('is-invalid');
                    reasonInput.focus();
                    return;
                } else {
                    reasonInput.classList.remove('is-invalid');
                }
            }
            onConfirm();
            const modal = bootstrap.Modal.getInstance(confirmModal);
            modal.hide();
        });

        // Show the modal
        const modal = new bootstrap.Modal(confirmModal);
        modal.show();
    };
});

// Ensure all functions are globally available immediately (Fallback functions)
if (typeof window.viewRegistrationDetails === 'undefined') {
    window.viewRegistrationDetails = function(registrationId) {
        console.log('Fallback viewRegistrationDetails called with ID:', registrationId);
        if (!registrationId) {
            alert('Invalid registration ID');
            return;
        }
        
        // Initialize modal if needed
        const modal = document.getElementById('registrationModal');
        if (!modal) {
            console.error('Registration modal not found');
            return;
        }
        
        if (!registrationModal) {
            registrationModal = new bootstrap.Modal(modal);
        }
        
        const modalDetails = document.getElementById('modal-details');
        const modalActions = document.getElementById('modal-actions');
        
        if (modalDetails && modalActions) {
            modalDetails.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
            registrationModal.show();
            
            fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
                .then(response => response.json())
                .then(data => {
                    displayRegistrationDetails(data, registrationId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalDetails.innerHTML = '<div class="alert alert-danger">Failed to load details</div>';
                });
        }
    };
}

if (typeof window.approveRegistration === 'undefined') {
    window.approveRegistration = function(registrationId) {
        console.log('Fallback approveRegistration called with ID:', registrationId);
        if (!registrationId) {
            alert('Invalid registration ID');
            return;
        }
        
        if (confirm('Are you sure you want to approve this registration?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/registration/${registrationId}/approve`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = token || document.querySelector('meta[name="csrf-token"]')?.content;
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
}

if (typeof window.rejectRegistration === 'undefined') {
    window.rejectRegistration = function(registrationId) {
        console.log('Fallback rejectRegistration called with ID:', registrationId);
        if (!registrationId) {
            alert('Invalid registration ID');
            return;
        }
        
        const reason = prompt('Please provide a reason for rejection:');
        if (reason && reason.trim()) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/registration/${registrationId}/reject`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = token || document.querySelector('meta[name="csrf-token"]')?.content;
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            
            form.appendChild(csrfInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
}

// Helper function for displaying registration details
function displayRegistrationDetails(data, registrationId) {
    const modalDetails = document.getElementById('modal-details');
    const modalActions = document.getElementById('modal-actions');
    
    if (!modalDetails || !modalActions) return;
    
    let leftColumn = `
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-person-circle"></i> User Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Full Name:</strong></div>
                        <div class="col-sm-8">${(data.firstname || '') + ' ' + (data.lastname || '')}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8">${data.email || 'N/A'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Registration Date:</strong></div>
                        <div class="col-sm-8">${data.created_at || 'N/A'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge ${data.status === 'approved' ? 'bg-success' : 
                                              data.status === 'rejected' ? 'bg-danger' : 'bg-warning'}">${data.status || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    let rightColumn = `
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-mortarboard"></i> Enrollment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Program:</strong></div>
                        <div class="col-sm-8">${data.program_name || 'N/A'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Package:</strong></div>
                        <div class="col-sm-8">${data.package_name || 'N/A'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Plan Type:</strong></div>
                        <div class="col-sm-8">${data.enrollment_type || 'N/A'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Learning Mode:</strong></div>
                        <div class="col-sm-8">${data.learning_mode || 'N/A'}</div>
                    </div>
                </div>
            </div>
        </div>
    `;

    modalDetails.innerHTML = leftColumn + rightColumn;

    let actionButtons = '';
    if (data.status === 'pending') {
        actionButtons = `
            <button type="button" class="btn btn-success" onclick="approveRegistration('${registrationId}')">
                <i class="bi bi-check-circle"></i> Approve
            </button>
            <button type="button" class="btn btn-danger" onclick="rejectRegistration('${registrationId}')">
                <i class="bi bi-x-circle"></i> Reject
            </button>
        `;
    }

    modalActions.innerHTML = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        ${actionButtons}
    `;
}
</script>

<!-- Enhanced Registration Modal JavaScript -->
<script src="{{ asset('js/enhanced-registration-modal.js') }}"></script>

<!-- Confirmation Modal Template (will be created dynamically) -->

@endsection
