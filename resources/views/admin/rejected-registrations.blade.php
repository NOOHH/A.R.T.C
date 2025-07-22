@extends('admin.admin-dashboard-layout')

@section('title', 'Rejected Registrations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-x-circle text-danger me-2"></i>
                        Rejected Registrations
                    </h4>
                    <span class="badge bg-danger">{{ $rejectedRegistrations->count() }} rejected</span>
                </div>
                <div class="card-body">
                    @if($rejectedRegistrations->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No Rejected Registrations</h5>
                            <p class="text-muted">All registrations are either pending or approved.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="rejectedRegistrationsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Registration ID</th>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Rejection Reason</th>
                                        <th>Rejected Fields</th>
                                        <th>Rejected Date</th>
                                        <th>Resubmittable</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedRegistrations as $registration)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#{{ $registration->registration_id }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $registration->firstname }} {{ $registration->lastname }}</strong>
                                                @if($registration->middlename)
                                                    <br><small class="text-muted">{{ $registration->middlename }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $registration->email ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $registration->program_name ?? 'N/A' }}</span>
                                                @if($registration->package_name)
                                                    <br><small class="text-muted">{{ $registration->package_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->rejection_reason)
                                                    <span class="text-danger">{{ Str::limit($registration->rejection_reason, 50) }}</span>
                                                    @if(strlen($registration->rejection_reason) > 50)
                                                        <button class="btn btn-sm btn-outline-secondary ms-1" 
                                                                onclick="showFullReason('{{ addslashes($registration->rejection_reason) }}')">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No reason provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->rejected_fields && is_array($registration->rejected_fields))
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="showRejectedFields({{ json_encode($registration->rejected_fields) }})">
                                                        <i class="bi bi-list-ul"></i> {{ count($registration->rejected_fields) }} fields
                                                    </button>
                                                @else
                                                    <span class="text-muted">No specific fields</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->rejected_at)
                                                    {{ $registration->rejected_at->format('M j, Y g:i A') }}
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->can_resubmit)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-primary" 
                                                            onclick="viewRegistrationDetails({{ $registration->registration_id }})">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="approveResubmission({{ $registration->registration_id }})">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" 
                                                            onclick="resetStatus({{ $registration->registration_id }})">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="permanentDelete({{ $registration->registration_id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Reason Modal -->
<div class="modal fade" id="fullReasonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Rejection Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="fullReasonText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejected Fields Modal -->
<div class="modal fade" id="rejectedFieldsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejected Fields</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="rejectedFieldsList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Registration Details Modal -->
<div class="modal fade" id="registrationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="registrationDetailsContent">
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

@endsection

@push('styles')
<style>
.rejected-field {
    border-left: 4px solid #dc3545;
    background-color: #f8d7da;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
}
.rejected-field-name {
    font-weight: bold;
    color: #721c24;
}
.rejected-field-comment {
    color: #721c24;
    margin-top: 5px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#rejectedRegistrationsTable').DataTable({
        "order": [[ 6, "desc" ]], // Order by rejected date descending
        "pageLength": 25,
        "responsive": true
    });
});

function showFullReason(reason) {
    $('#fullReasonText').text(reason);
    $('#fullReasonModal').modal('show');
}

function showRejectedFields(fields) {
    let html = '';
    for (const [fieldName, comment] of Object.entries(fields)) {
        html += `
            <div class="rejected-field">
                <div class="rejected-field-name">${fieldName}</div>
                <div class="rejected-field-comment">${comment}</div>
            </div>
        `;
    }
    $('#rejectedFieldsList').html(html);
    $('#rejectedFieldsModal').modal('show');
}

function viewRegistrationDetails(registrationId) {
    $('#registrationDetailsModal').modal('show');
    
    $.get(`/admin/registration/${registrationId}/details`)
        .done(function(data) {
            // Build detailed view of registration
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Personal Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Name:</strong></td><td>${data.firstname} ${data.middlename || ''} ${data.lastname}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${data.email || 'N/A'}</td></tr>
                            <tr><td><strong>Contact:</strong></td><td>${data.contact_number || 'N/A'}</td></tr>
                            <tr><td><strong>Address:</strong></td><td>${data.street_address || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Program Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Program:</strong></td><td>${data.program_name || 'N/A'}</td></tr>
                            <tr><td><strong>Package:</strong></td><td>${data.package_name || 'N/A'}</td></tr>
                            <tr><td><strong>Enrollment Type:</strong></td><td>${data.enrollment_type || 'N/A'}</td></tr>
                            <tr><td><strong>Learning Mode:</strong></td><td>${data.learning_mode || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            
            if (data.rejected_fields && Object.keys(data.rejected_fields).length > 0) {
                html += `
                    <div class="mt-3">
                        <h6>Rejected Fields</h6>
                        <div class="row">
                `;
                for (const [fieldName, comment] of Object.entries(data.rejected_fields)) {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="rejected-field">
                                <div class="rejected-field-name">${fieldName}</div>
                                <div class="rejected-field-comment">${comment}</div>
                            </div>
                        </div>
                    `;
                }
                html += `</div></div>`;
            }
            
            $('#registrationDetailsContent').html(html);
        })
        .fail(function() {
            $('#registrationDetailsContent').html('<div class="alert alert-danger">Failed to load registration details</div>');
        });
}

function approveResubmission(registrationId) {
    if (confirm('Are you sure you want to approve this resubmission?')) {
        $.post(`/admin/registration/${registrationId}/approve-resubmission`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to approve resubmission');
        });
    }
}

function resetStatus(registrationId) {
    if (confirm('Are you sure you want to reset this registration status to pending?')) {
        $.post(`/admin/registration/${registrationId}/reset-status`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to reset status');
        });
    }
}

function permanentDelete(registrationId) {
    if (confirm('Are you sure you want to permanently delete this registration? This action cannot be undone.')) {
        $.post(`/admin/registration/${registrationId}/reject`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function() {
            toastr.success('Registration permanently deleted');
            location.reload();
        })
        .fail(function() {
            toastr.error('Failed to delete registration');
        });
    }
}
</script>
@endpush
