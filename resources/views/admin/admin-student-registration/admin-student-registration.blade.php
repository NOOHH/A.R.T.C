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

    <!-- Rejection Reason Modal -->
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

    // Global function for rejecting registration
    window.rejectRegistration = function(registrationId) {
        const rejectForm = document.getElementById('rejectReasonForm');
        rejectForm.action = `${baseUrl}/admin/registration/${registrationId}/reject-with-reason`;
        registrationModal.hide();
        rejectReasonModal.show();
    };

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
