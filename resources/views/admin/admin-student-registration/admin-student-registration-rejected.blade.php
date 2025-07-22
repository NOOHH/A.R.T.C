@extends('admin.admin-dashboard-layout')

@section('title', 'Registration Rejected')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-header.bg-danger { background-color: #dc3545 !important; }
        .badge-rejected { background-color: #dc3545; }
        .rejected-field { 
            background-color: #fee; 
            border: 1px solid #f5c6cb; 
            border-radius: 4px; 
            padding: 2px 4px; 
            margin: 2px; 
            display: inline-block; 
        }
        .modal-comparison { 
            display: flex; 
            gap: 20px; 
        }
        .comparison-side { 
            flex: 1; 
        }
        .comparison-side h6 { 
            background: #f8f9fa; 
            padding: 10px; 
            margin-bottom: 0; 
            border-radius: 4px 4px 0 0; 
        }
        .comparison-content { 
            border: 1px solid #dee2e6; 
            border-radius: 0 0 4px 4px; 
            padding: 15px; 
            max-height: 400px; 
            overflow-y: auto; 
        }
        .field-rejected { 
            background-color: #ffe6e6 !important; 
            border-left: 4px solid #dc3545; 
            padding-left: 8px; 
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Registration Rejected</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-primary">
                        <i class="bi bi-clock"></i> Pending
                    </a>
                    <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                        <i class="bi bi-credit-card"></i> Payment Pending
                    </a>
                    <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> Registration History
                    </a>
                </div>
            </div>

            {{-- Rejected Registrations Table --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Students with Rejected Registration
                    </h6>
                    <div>
                        <small>Total: {{ $registrations->count() }}</small>
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
                                        <th>Rejected Date</th>
                                        <th>Rejected Fields</th>
                                        <th>Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
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
                                        <td>{{ $registration->rejected_at ? \Carbon\Carbon::parse($registration->rejected_at)->format('M d, Y g:i A') : 'N/A' }}</td>
                                        <td>
                                            @if($registration->rejected_fields)
                                                @php
                                                    $rejectedFields = json_decode($registration->rejected_fields, true) ?? [];
                                                @endphp
                                                @foreach($rejectedFields as $field)
                                                    <span class="rejected-field badge badge-danger">{{ $field }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No specific fields</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $registration->rejection_reason }}">
                                                {{ $registration->rejection_reason ?? 'No reason provided' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-info btn-sm" onclick="viewRejectedRegistration({{ $registration->registration_id }})">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="editRejection({{ $registration->registration_id }})">
                                                    <i class="bi bi-pencil"></i> Edit Rejection
                                                </button>
                                                <button class="btn btn-success btn-sm" onclick="approveRegistration({{ $registration->registration_id }})">
                                                    <i class="bi bi-check"></i> Approve
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No Rejected Registrations</h5>
                            <p class="text-muted">All registrations are currently pending or approved.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resubmitted Registrations Table --}}
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
                                    <td>{{ $registration->resubmitted_at ? \Carbon\Carbon::parse($registration->resubmitted_at)->format('M d, Y g:i A') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-warning">Previously Rejected</span>
                                        <span class="badge badge-info">Pending Review</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-primary btn-sm" onclick="viewComparison({{ $registration->registration_id }})">
                                                <i class="bi bi-layout-split"></i> Compare
                                            </button>
                                            <button class="btn btn-success btn-sm" onclick="approveResubmission({{ $registration->registration_id }})">
                                                <i class="bi bi-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="rejectAgain({{ $registration->registration_id }})">
                                                <i class="bi bi-x"></i> Reject Again
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
        </div>
    </div>
</div>

{{-- View Registration Modal --}}
<div class="modal fade" id="viewRegistrationModal" tabindex="-1" aria-labelledby="viewRegistrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRegistrationModalLabel">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewRegistrationContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Comparison Modal --}}
<div class="modal fade" id="comparisonModal" tabindex="-1" aria-labelledby="comparisonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comparisonModalLabel">Registration Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-comparison">
                    <div class="comparison-side">
                        <h6 class="bg-danger text-white">Original (Rejected) Submission</h6>
                        <div class="comparison-content" id="originalContent">
                            <!-- Original content will be loaded here -->
                        </div>
                    </div>
                    <div class="comparison-side">
                        <h6 class="bg-success text-white">New (Resubmitted) Data</h6>
                        <div class="comparison-content" id="newContent">
                            <!-- New content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approveResubmissionFromModal()">
                    <i class="bi bi-check"></i> Approve Resubmission
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectAgainFromModal()">
                    <i class="bi bi-x"></i> Reject Again
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Rejection Modal --}}
<div class="modal fade" id="editRejectionModal" tabindex="-1" aria-labelledby="editRejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRejectionModalLabel">Edit Rejection Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRejectionForm">
                <div class="modal-body">
                    <input type="hidden" id="editRegistrationId" name="registration_id">
                    
                    <div class="mb-3">
                        <label for="editRejectionReason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="editRejectionReason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mark Fields for Redo (optional)</label>
                        <div id="editFieldsList" class="form-check-container">
                            <!-- Field checkboxes will be populated here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentRegistrationId = null;

function viewRejectedRegistration(registrationId) {
    currentRegistrationId = registrationId;
    
    fetch(`{{ route('admin.registrations.details', '') }}/${registrationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRegistrationDetails(data.registration);
                const modal = new bootstrap.Modal(document.getElementById('viewRegistrationModal'));
                modal.show();
            } else {
                alert('Error loading registration details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading registration details');
        });
}

function displayRegistrationDetails(registration) {
    const content = document.getElementById('viewRegistrationContent');
    const rejectedFields = registration.rejected_fields ? JSON.parse(registration.rejected_fields) : [];
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Personal Information</h6>
                <table class="table table-sm">
                    <tr class="${rejectedFields.includes('firstname') ? 'field-rejected' : ''}">
                        <td><strong>First Name:</strong></td>
                        <td>${registration.firstname || 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('middlename') ? 'field-rejected' : ''}">
                        <td><strong>Middle Name:</strong></td>
                        <td>${registration.middlename || 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('lastname') ? 'field-rejected' : ''}">
                        <td><strong>Last Name:</strong></td>
                        <td>${registration.lastname || 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('contact_number') ? 'field-rejected' : ''}">
                        <td><strong>Contact Number:</strong></td>
                        <td>${registration.contact_number || 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('emergency_contact_number') ? 'field-rejected' : ''}">
                        <td><strong>Emergency Contact:</strong></td>
                        <td>${registration.emergency_contact_number || 'N/A'}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Program Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Program:</strong></td>
                        <td>${registration.program_name || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Package:</strong></td>
                        <td>${registration.package_name || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Enrollment Type:</strong></td>
                        <td>${registration.enrollment_type || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Learning Mode:</strong></td>
                        <td>${registration.learning_mode || 'N/A'}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Rejection Details</h6>
                <div class="alert alert-danger">
                    <strong>Reason:</strong> ${registration.rejection_reason || 'No reason provided'}
                </div>
                ${rejectedFields.length > 0 ? `
                    <div class="alert alert-warning">
                        <strong>Fields marked for redo:</strong>
                        ${rejectedFields.map(field => `<span class="badge badge-danger me-1">${field}</span>`).join('')}
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

function editRejection(registrationId) {
    currentRegistrationId = registrationId;
    
    fetch(`{{ route('admin.registrations.details', '') }}/${registrationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditForm(data.registration);
                const modal = new bootstrap.Modal(document.getElementById('editRejectionModal'));
                modal.show();
            } else {
                alert('Error loading registration details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading registration details');
        });
}

function populateEditForm(registration) {
    document.getElementById('editRegistrationId').value = registration.registration_id;
    document.getElementById('editRejectionReason').value = registration.rejection_reason || '';
    
    const fieldsContainer = document.getElementById('editFieldsList');
    const commonFields = [
        'firstname', 'middlename', 'lastname', 'contact_number', 'emergency_contact_number',
        'street_address', 'city', 'state_province', 'zipcode', 'student_school',
        'good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'photo_2x2'
    ];
    
    const rejectedFields = registration.rejected_fields ? JSON.parse(registration.rejected_fields) : [];
    
    fieldsContainer.innerHTML = commonFields.map(field => `
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="${field}" 
                   id="edit_${field}" ${rejectedFields.includes(field) ? 'checked' : ''}>
            <label class="form-check-label" for="edit_${field}">
                ${field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
            </label>
        </div>
    `).join('');
}

function viewComparison(registrationId) {
    currentRegistrationId = registrationId;
    
    Promise.all([
        fetch(`{{ route('admin.registrations.details', '') }}/${registrationId}`),
        fetch(`{{ route('admin.registrations.original-data', '') }}/${registrationId}`)
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(([newData, originalData]) => {
        if (newData.success && originalData.success) {
            displayComparison(originalData.registration, newData.registration);
            const modal = new bootstrap.Modal(document.getElementById('comparisonModal'));
            modal.show();
        } else {
            alert('Error loading comparison data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading comparison data');
    });
}

function displayComparison(original, current) {
    const originalContent = document.getElementById('originalContent');
    const newContent = document.getElementById('newContent');
    
    const rejectedFields = original.rejected_fields ? JSON.parse(original.rejected_fields) : [];
    
    function createTable(data, isOriginal = false) {
        return `
            <table class="table table-sm">
                <tr class="${rejectedFields.includes('firstname') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>First Name:</strong></td>
                    <td>${data.firstname || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('middlename') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Middle Name:</strong></td>
                    <td>${data.middlename || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('lastname') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Last Name:</strong></td>
                    <td>${data.lastname || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('contact_number') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Contact Number:</strong></td>
                    <td>${data.contact_number || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('emergency_contact_number') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Emergency Contact:</strong></td>
                    <td>${data.emergency_contact_number || 'N/A'}</td>
                </tr>
            </table>
        `;
    }
    
    originalContent.innerHTML = createTable(original, true);
    newContent.innerHTML = createTable(current, false);
}

function approveRegistration(registrationId) {
    if (confirm('Are you sure you want to approve this registration?')) {
        fetch(`{{ route('admin.registrations.approve', '') }}/${registrationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Registration approved successfully!');
                location.reload();
            } else {
                alert('Error approving registration: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving registration');
        });
    }
}

function approveResubmission(registrationId) {
    if (confirm('Are you sure you want to approve this resubmission?')) {
        fetch(`{{ route('admin.registrations.approve-resubmission', '') }}/${registrationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Resubmission approved successfully!');
                location.reload();
            } else {
                alert('Error approving resubmission: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving resubmission');
        });
    }
}

// Form submission for edit rejection
document.getElementById('editRejectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const registrationId = formData.get('registration_id');
    
    fetch(`{{ route('admin.registrations.update-rejection', '') }}/${registrationId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rejection details updated successfully!');
            bootstrap.Modal.getInstance(document.getElementById('editRejectionModal')).hide();
            location.reload();
        } else {
            alert('Error updating rejection: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating rejection');
    });
});
</script>
@endsection
