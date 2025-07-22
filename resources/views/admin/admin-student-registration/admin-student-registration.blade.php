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
                        <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                            <i class="bi bi-credit-card"></i> Payment Pending
                        </a>
                        <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                            <i class="bi bi-clock-history"></i> Registration History
                        </a>
                    @endif
                </div>
            </div>

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
                                                    
                                                    <!-- Enhanced Rejection Dropdown -->
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="enhancedRejectRegistration('{{ $registration->registration_id }}')">
                                                            <i class="bi bi-x-circle-fill"></i> Enhanced Reject
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split" 
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="#" 
                                                                   onclick="enhancedRejectRegistration('{{ $registration->registration_id }}')">
                                                                    <i class="bi bi-x-circle-fill text-danger"></i> Enhanced Reject
                                                                    <br><small class="text-muted">Mark specific fields & provide detailed feedback</small>
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item" href="#" 
                                                                   onclick="rejectRegistration('{{ $registration->registration_id }}')">
                                                                    <i class="bi bi-x-circle text-warning"></i> Simple Reject
                                                                    <br><small class="text-muted">Basic rejection with general reason</small>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">Registration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="modal-details">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer" id="modal-actions">
                    <!-- Actions will be populated based on status -->
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Rejection Modal with Field Marking -->
    <div class="modal fade" id="enhancedRejectModal" tabindex="-1" aria-labelledby="enhancedRejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="enhancedRejectForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="enhancedRejectModalLabel">
                            <i class="bi bi-x-circle me-2"></i>Enhanced Registration Rejection
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column: Registration Overview -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Registration Overview</h6>
                                    </div>
                                    <div class="card-body" id="rejectionOverview">
                                        <!-- Will be populated with registration details -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column: Field Marking -->
                            <div class="col-md-8">
                                <div class="card h-100">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Mark Problematic Fields</h6>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success" onclick="markAllFieldsValid()">
                                                <i class="bi bi-check-all"></i> Mark All Valid
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="markAllFieldsInvalid()">
                                                <i class="bi bi-x-circle"></i> Mark All Invalid
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                        <div id="fieldsMarkingContainer">
                                            <!-- Will be dynamically populated -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Overall Rejection Reason -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Overall Rejection Reason</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="overallRejectionReason" class="form-label">
                                                General reason for rejection <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control" id="overallRejectionReason" name="rejection_reason" rows="3" 
                                                      placeholder="Provide a general explanation for why this registration is being rejected..." required></textarea>
                                            <div class="form-text">This message will be sent to the student via email along with specific field feedback.</div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="allowResubmission" name="allow_resubmission" checked>
                                            <label class="form-check-label" for="allowResubmission">
                                                Allow student to resubmit with corrections
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-warning" onclick="previewRejectionEmail()">
                            <i class="bi bi-eye"></i> Preview Email
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-send"></i> Send Rejection & Notify Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal (Original Simple Version) -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectReasonForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectReasonModalLabel">Reject Registration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectionReason" name="reason" rows="4" 
                                      placeholder="Please provide a clear reason for rejecting this registration..." required></textarea>
                            <div class="form-text">This message will be sent to the student via email.</div>
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

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
    const rejectReasonModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
    const baseUrl = window.location.origin;
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const isHistory = {{ (isset($history) && $history) ? 'true' : 'false' }};

    function na(value) {
        return (value === undefined || value === null || value === '' || value === 'null') ? 'N/A' : value;
    }

    function formatDocumentLink(filename, label) {
        if (!filename || filename === 'N/A') {
            return `<span class="text-muted">Not uploaded</span>`;
        }
        return `<a href="${baseUrl}/storage/documents/${filename}" target="_blank" class="text-primary">
                    <i class="bi bi-file-earmark-arrow-down"></i> View ${label}
                </a>`;
    }

    // Global function for viewing registration details
    window.viewRegistrationDetails = function(registrationId) {
        if (!registrationId) {
            alert('Invalid registration ID');
            return;
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
                                    <div class="col-sm-4"><strong>Plan:</strong></div>
                                    <div class="col-sm-8">${na(data.plan_name)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Plan Type:</strong></div>
                                    <div class="col-sm-8">${na(data.plan_type || data.enrollment_type)}</div>
                                </div>`;
                
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
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-danger" onclick="enhancedRejectRegistration('${registrationId}')">
                                <i class="bi bi-x-circle-fill"></i> Enhanced Reject
                            </button>
                            <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="enhancedRejectRegistration('${registrationId}')">
                                        <i class="bi bi-x-circle-fill text-danger"></i> Enhanced Reject
                                        <br><small class="text-muted">Mark specific fields & provide detailed feedback</small>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="rejectRegistration('${registrationId}')">
                                        <i class="bi bi-x-circle text-warning"></i> Simple Reject
                                        <br><small class="text-muted">Basic rejection with general reason</small>
                                    </a>
                                </li>
                            </ul>
                        </div>
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
    };

    // Global function for approving registration
    window.approveRegistration = function(registrationId) {
        // Show custom confirmation modal
        showConfirmModal(
            'Confirm Approval', 
            'Are you sure you want to approve this registration? This action cannot be undone.',
            'Yes, Approve',
            'btn-success',
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${baseUrl}/admin/registration/${registrationId}/approve`;
                
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

    // Global function for rejecting registration (original simple version)
    window.rejectRegistration = function(registrationId) {
        const rejectForm = document.getElementById('rejectReasonForm');
        rejectForm.action = `${baseUrl}/admin/registration/${registrationId}/reject-with-reason`;
        registrationModal.hide();
        rejectReasonModal.show();
    };

    // Enhanced rejection with field marking
    window.enhancedRejectRegistration = function(registrationId) {
        // First, load the registration details to populate the form
        fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
            .then(response => response.json())
            .then(data => {
                populateEnhancedRejectionModal(data);
                const enhancedRejectForm = document.getElementById('enhancedRejectForm');
                enhancedRejectForm.action = `${baseUrl}/admin/registration/${registrationId}/reject-fields`;
                registrationModal.hide();
                
                const enhancedRejectModal = new bootstrap.Modal(document.getElementById('enhancedRejectModal'));
                enhancedRejectModal.show();
            })
            .catch(error => {
                console.error('Error loading registration details:', error);
                alert('Failed to load registration details for enhanced rejection');
            });
    };

    function populateEnhancedRejectionModal(data) {
        // Populate overview section
        const overview = document.getElementById('rejectionOverview');
        overview.innerHTML = `
            <div class="mb-3">
                <strong>Student:</strong><br>
                ${data.firstname} ${data.middlename || ''} ${data.lastname}
            </div>
            <div class="mb-3">
                <strong>Email:</strong><br>
                ${data.email || 'N/A'}
            </div>
            <div class="mb-3">
                <strong>Program:</strong><br>
                ${data.program_name || 'N/A'}
            </div>
            <div class="mb-3">
                <strong>Package:</strong><br>
                ${data.package_name || 'N/A'}
            </div>
            <div class="mb-3">
                <strong>Submitted:</strong><br>
                ${new Date(data.created_at).toLocaleDateString()}
            </div>
        `;

        // Populate fields marking section
        const fieldsContainer = document.getElementById('fieldsMarkingContainer');
        fieldsContainer.innerHTML = '';

        // Define the fields that can be marked for rejection
        const markableFields = [
            { key: 'firstname', label: 'First Name', value: data.firstname },
            { key: 'lastname', label: 'Last Name', value: data.lastname },
            { key: 'middlename', label: 'Middle Name', value: data.middlename },
            { key: 'email', label: 'Email Address', value: data.email },
            { key: 'contact_number', label: 'Contact Number', value: data.contact_number },
            { key: 'street_address', label: 'Street Address', value: data.street_address },
            { key: 'city', label: 'City', value: data.city },
            { key: 'state_province', label: 'State/Province', value: data.state_province },
            { key: 'zipcode', label: 'Zip Code', value: data.zipcode },
            { key: 'emergency_contact_number', label: 'Emergency Contact', value: data.emergency_contact_number },
            { key: 'student_school', label: 'School Name', value: data.student_school },
            { key: 'good_moral', label: 'Good Moral Certificate', value: data.good_moral },
            { key: 'PSA', label: 'PSA Birth Certificate', value: data.PSA },
            { key: 'Course_Cert', label: 'Course Certificate', value: data.Course_Cert },
            { key: 'TOR', label: 'Transcript of Records', value: data.TOR },
            { key: 'Cert_of_Grad', label: 'Certificate of Graduation', value: data.Cert_of_Grad },
            { key: 'photo_2x2', label: '2x2 Photo', value: data.photo_2x2 }
        ];

        markableFields.forEach(field => {
            if (field.value) { // Only show fields that have values
                const fieldHtml = `
                    <div class="field-marking-item mb-3 p-3 border rounded" data-field="${field.key}">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <strong>${field.label}</strong>
                                <br><small class="text-muted">${field.value.length > 50 ? field.value.substring(0, 50) + '...' : field.value}</small>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input field-status" type="radio" 
                                           name="field_status_${field.key}" id="valid_${field.key}" value="valid" checked>
                                    <label class="form-check-label text-success" for="valid_${field.key}">
                                        <i class="bi bi-check-circle"></i> Valid
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input field-status" type="radio" 
                                           name="field_status_${field.key}" id="invalid_${field.key}" value="invalid">
                                    <label class="form-check-label text-danger" for="invalid_${field.key}">
                                        <i class="bi bi-x-circle"></i> Invalid
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <textarea class="form-control field-comment" 
                                          name="field_comment_${field.key}" 
                                          placeholder="Specific issue with this field (optional)"
                                          rows="2" disabled></textarea>
                            </div>
                        </div>
                    </div>
                `;
                fieldsContainer.innerHTML += fieldHtml;
            }
        });

        // Add event listeners for field status changes
        document.querySelectorAll('.field-status').forEach(radio => {
            radio.addEventListener('change', function() {
                const fieldKey = this.name.replace('field_status_', '');
                const commentField = document.querySelector(`textarea[name="field_comment_${fieldKey}"]`);
                const fieldItem = this.closest('.field-marking-item');
                
                if (this.value === 'invalid') {
                    commentField.disabled = false;
                    commentField.required = true;
                    fieldItem.classList.add('border-danger', 'bg-light');
                    fieldItem.classList.remove('border-success');
                } else {
                    commentField.disabled = true;
                    commentField.required = false;
                    commentField.value = '';
                    fieldItem.classList.add('border-success');
                    fieldItem.classList.remove('border-danger', 'bg-light');
                }
            });
        });
    }

    // Helper functions for bulk field marking
    window.markAllFieldsValid = function() {
        document.querySelectorAll('input[value="valid"]').forEach(radio => {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        });
    };

    window.markAllFieldsInvalid = function() {
        document.querySelectorAll('input[value="invalid"]').forEach(radio => {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        });
    };

    // Preview rejection email
    window.previewRejectionEmail = function() {
        const rejectedFields = {};
        const overallReason = document.getElementById('overallRejectionReason').value;
        
        // Collect rejected fields and their comments
        document.querySelectorAll('input[value="invalid"]:checked').forEach(radio => {
            const fieldKey = radio.name.replace('field_status_', '');
            const commentField = document.querySelector(`textarea[name="field_comment_${fieldKey}"]`);
            const fieldLabel = radio.closest('.field-marking-item').querySelector('strong').textContent;
            
            if (commentField.value.trim()) {
                rejectedFields[fieldLabel] = commentField.value.trim();
            }
        });

        // Show preview modal (you can implement this)
        alert(`Email Preview:\n\nOverall Reason: ${overallReason}\n\nRejected Fields: ${JSON.stringify(rejectedFields, null, 2)}`);
    };

    // Handle enhanced rejection form submission
    document.getElementById('enhancedRejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const overallReason = document.getElementById('overallRejectionReason').value.trim();
        const allowResubmission = document.getElementById('allowResubmission').checked;
        const rejectedFields = {};
        
        // Collect rejected fields and their comments
        let hasRejectedFields = false;
        document.querySelectorAll('input[value="invalid"]:checked').forEach(radio => {
            const fieldKey = radio.name.replace('field_status_', '');
            const commentField = document.querySelector(`textarea[name="field_comment_${fieldKey}"]`);
            
            if (commentField.value.trim()) {
                rejectedFields[fieldKey] = commentField.value.trim();
                hasRejectedFields = true;
            } else {
                alert(`Please provide a comment for the rejected field: ${fieldKey}`);
                commentField.focus();
                return;
            }
        });

        if (!overallReason) {
            alert('Please provide an overall rejection reason.');
            document.getElementById('overallRejectionReason').focus();
            return;
        }

        if (!hasRejectedFields) {
            if (!confirm('No specific fields were marked as invalid. Are you sure you want to proceed with a general rejection?')) {
                return;
            }
        }

        // Submit the form data
        const formData = new FormData();
        formData.append('_token', token);
        formData.append('rejection_reason', overallReason);
        formData.append('rejected_fields', JSON.stringify(rejectedFields));
        formData.append('can_resubmit', allowResubmission);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Registration rejected successfully with detailed feedback.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to reject registration. Please try again.');
        });
    });

    // Global function for undoing approval
    window.undoApproval = function(registrationId) {
        showConfirmModal(
            'Confirm Undo Approval', 
            'Are you sure you want to undo this approval? This will set the registration back to pending status.',
            'Yes, Undo',
            'btn-warning',
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${baseUrl}/admin/student/${registrationId}/undo-approval`;
                
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
    window.showConfirmModal = function(title, message, confirmText, confirmClass, onConfirm) {
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
        
        const confirmBtn = document.getElementById('confirmActionBtn');
        confirmBtn.textContent = confirmText;
        confirmBtn.className = `btn ${confirmClass}`;
        
        // Remove any existing event listeners and add new one
        confirmBtn.replaceWith(confirmBtn.cloneNode(true));
        const newConfirmBtn = document.getElementById('confirmActionBtn');
        
        newConfirmBtn.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(confirmModal);
            modal.hide();
            onConfirm();
        });

        // Show the modal
        const modal = new bootstrap.Modal(confirmModal);
        modal.show();
    };
});
</script>

<!-- Confirmation Modal Template (will be created dynamically) -->

@endsection
