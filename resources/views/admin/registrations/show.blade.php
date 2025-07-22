@extends('layouts.admin')

@section('title', 'Registration Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Registration Details</h3>
                    <div>
                        @if($registration->status === 'pending')
                            <button type="button" class="btn btn-success me-2" onclick="approveRegistration({{ $registration->id }})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" onclick="showRejectModal({{ $registration->id }})">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        @elseif($registration->status === 'resubmitted')
                            <button type="button" class="btn btn-info me-2" onclick="showComparisonModal({{ $registration->id }})">
                                <i class="fas fa-eye"></i> Compare Versions
                            </button>
                            <button type="button" class="btn btn-success me-2" onclick="approveRegistration({{ $registration->id }})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" onclick="showRejectModal({{ $registration->id }})">
                                <i class="fas fa-times"></i> Reject Again
                            </button>
                        @endif
                        <a href="{{ route('admin.registrations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $registration->first_name }} {{ $registration->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $registration->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $registration->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td>{{ $registration->date_of_birth ? $registration->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>{{ ucfirst($registration->gender) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Civil Status:</strong></td>
                                    <td>{{ ucfirst($registration->civil_status) }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Education & Program</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Education Level:</strong></td>
                                    <td>{{ $registration->education_level }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $registration->course }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Program:</strong></td>
                                    <td>{{ $registration->program }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Learning Mode:</strong></td>
                                    <td>{{ ucfirst($registration->learning_mode) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $registration->status === 'approved' ? 'success' : ($registration->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Submitted:</strong></td>
                                    <td>{{ $registration->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Address Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $registration->address }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $registration->city }}</td>
                                </tr>
                                <tr>
                                    <td><strong>State/Province:</strong></td>
                                    <td>{{ $registration->state }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Country:</strong></td>
                                    <td>{{ $registration->country }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Postal Code:</strong></td>
                                    <td>{{ $registration->postal_code }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($registration->uploaded_files && is_array($registration->uploaded_files))
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Uploaded Files</h5>
                            <div class="list-group">
                                @foreach($registration->uploaded_files as $file)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ basename($file) }}</span>
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i> View/Download
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($registration->rejections && $registration->rejections->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Rejection History</h5>
                            @foreach($registration->rejections as $rejection)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <strong>Rejected on:</strong> {{ $rejection->created_at->format('M d, Y g:i A') }}
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Reason:</strong> {{ $rejection->reason }}</p>
                                        @if($rejection->rejected_fields && is_array($rejection->rejected_fields))
                                            <p><strong>Rejected Fields:</strong></p>
                                            <ul>
                                                @foreach($rejection->rejected_fields as $field)
                                                    <li>{{ ucfirst(str_replace('_', ' ', $field)) }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea class="form-control" name="reason" rows="3" required placeholder="Please provide a clear reason for rejection..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mark Problematic Fields</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="first_name" id="field_first_name">
                                    <label class="form-check-label" for="field_first_name">First Name</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="last_name" id="field_last_name">
                                    <label class="form-check-label" for="field_last_name">Last Name</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="email" id="field_email">
                                    <label class="form-check-label" for="field_email">Email</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="phone" id="field_phone">
                                    <label class="form-check-label" for="field_phone">Phone</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="date_of_birth" id="field_date_of_birth">
                                    <label class="form-check-label" for="field_date_of_birth">Date of Birth</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="gender" id="field_gender">
                                    <label class="form-check-label" for="field_gender">Gender</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="address" id="field_address">
                                    <label class="form-check-label" for="field_address">Address</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="city" id="field_city">
                                    <label class="form-check-label" for="field_city">City</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="education_level" id="field_education_level">
                                    <label class="form-check-label" for="field_education_level">Education Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="course" id="field_course">
                                    <label class="form-check-label" for="field_course">Course</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="uploaded_files" id="field_uploaded_files">
                                    <label class="form-check-label" for="field_uploaded_files">Uploaded Files</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Comparison Modal -->
<div class="modal fade" id="comparisonModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compare Original vs Resubmitted Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="comparisonContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approveFromComparison()">
                    <i class="fas fa-check"></i> Approve Registration
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectFromComparison()">
                    <i class="fas fa-times"></i> Reject Again
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRegistrationId = {{ $registration->id }};

function showRejectModal(registrationId) {
    currentRegistrationId = registrationId;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function showComparisonModal(registrationId) {
    currentRegistrationId = registrationId;
    
    // Load comparison data
    fetch(`/admin/registrations/${registrationId}/comparison`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('comparisonContent').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('comparisonModal')).show();
        })
        .catch(error => {
            console.error('Error loading comparison:', error);
            alert('Error loading comparison data');
        });
}

function approveRegistration(registrationId) {
    if (confirm('Are you sure you want to approve this registration?')) {
        fetch(`/admin/registrations/${registrationId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
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

function approveFromComparison() {
    approveRegistration(currentRegistrationId);
}

function rejectFromComparison() {
    bootstrap.Modal.getInstance(document.getElementById('comparisonModal')).hide();
    setTimeout(() => {
        showRejectModal(currentRegistrationId);
    }, 300);
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        reason: formData.get('reason'),
        rejected_fields: formData.getAll('rejected_fields[]')
    };
    
    fetch(`/admin/registrations/${currentRegistrationId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error rejecting registration: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting registration');
    });
});
</script>
@endsection
