@extends('admin.admin-dashboard-layout')

@section('title', 'Rejected Payments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-x-circle text-warning me-2"></i>
                        Rejected Payments
                    </h4>
                    <span class="badge bg-warning">{{ $rejectedPayments->count() }} rejected</span>
                </div>
                <div class="card-body">
                    @if($rejectedPayments->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No Rejected Payments</h5>
                            <p class="text-muted">All payments are either pending or approved.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="rejectedPaymentsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Student</th>
                                        <th>Program</th>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                        <th>Rejection Reason</th>
                                        <th>Rejected Fields</th>
                                        <th>Rejected Date</th>
                                        <th>Resubmittable</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedPayments as $payment)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#{{ $payment->payment_id }}</span>
                                            </td>
                                            <td>
                                                @if($payment->student)
                                                    <strong>{{ $payment->student->firstname }} {{ $payment->student->lastname }}</strong>
                                                    <br><small class="text-muted">{{ $payment->student->email ?? 'N/A' }}</small>
                                                @else
                                                    <span class="text-muted">Student not found</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->program)
                                                    <span class="badge bg-info">{{ $payment->program->program_name }}</span>
                                                @else
                                                    <span class="text-muted">Program not found</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($payment->payment_method) }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">₱{{ number_format($payment->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($payment->rejection_reason)
                                                    <span class="text-danger">{{ Str::limit($payment->rejection_reason, 50) }}</span>
                                                    @if(strlen($payment->rejection_reason) > 50)
                                                        <button class="btn btn-sm btn-outline-secondary ms-1" 
                                                                onclick="showFullReason('{{ addslashes($payment->rejection_reason) }}')">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No reason provided</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->rejected_fields && is_array($payment->rejected_fields))
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="showRejectedFields({{ json_encode($payment->rejected_fields) }})">
                                                        <i class="bi bi-list-ul"></i> {{ count($payment->rejected_fields) }} fields
                                                    </button>
                                                @else
                                                    <span class="text-muted">No specific fields</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->rejected_at)
                                                    {{ $payment->rejected_at->format('M j, Y g:i A') }}
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->can_resubmit)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-primary" 
                                                            onclick="viewPaymentDetails({{ $payment->payment_id }})">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="approvePaymentResubmission({{ $payment->payment_id }})">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" 
                                                            onclick="resetPaymentStatus({{ $payment->payment_id }})">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="permanentDeletePayment({{ $payment->payment_id }})">
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
                <h5 class="modal-title">Rejected Payment Fields</h5>
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

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetailsContent">
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
    border-left: 4px solid #ffc107;
    background-color: #fff3cd;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 4px;
}
.rejected-field-name {
    font-weight: bold;
    color: #856404;
}
.rejected-field-comment {
    color: #856404;
    margin-top: 5px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#rejectedPaymentsTable').DataTable({
        "order": [[ 7, "desc" ]], // Order by rejected date descending
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

function viewPaymentDetails(paymentId) {
    $('#paymentDetailsModal').modal('show');
    
    // For now, we'll build a simple view with available data
    // You can extend this to fetch more detailed payment information
    let html = `
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Payment details view is not yet implemented. Payment ID: ${paymentId}
        </div>
        <p>This feature will show:</p>
        <ul>
            <li>Full payment information</li>
            <li>Submitted payment proof/documents</li>
            <li>Payment method details</li>
            <li>Enrollment information</li>
            <li>Side-by-side comparison for resubmissions</li>
        </ul>
    `;
    
    $('#paymentDetailsContent').html(html);
}

function approvePaymentResubmission(paymentId) {
    if (confirm('Are you sure you want to approve this payment resubmission?')) {
        $.post(`/admin/payment/${paymentId}/approve-resubmission`, {
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
            toastr.error('Failed to approve payment resubmission');
        });
    }
}

function resetPaymentStatus(paymentId) {
    if (confirm('Are you sure you want to reset this payment status to pending?')) {
        // Note: You'll need to add this route and method
        $.post(`/admin/payment/${paymentId}/reset-status`, {
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
            toastr.error('Failed to reset payment status');
        });
    }
}

function permanentDeletePayment(paymentId) {
    if (confirm('Are you sure you want to permanently delete this payment? This action cannot be undone.')) {
        // Note: You'll need to add this route and method
        $.post(`/admin/payment/${paymentId}/delete`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function() {
            toastr.success('Payment permanently deleted');
            location.reload();
        })
        .fail(function() {
            toastr.error('Failed to delete payment');
        });
    }
}
</script>
@endpush
