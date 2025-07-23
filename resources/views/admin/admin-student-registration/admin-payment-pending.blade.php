@extends('admin.admin-dashboard-layout')

@section('title', 'Payment Pending')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment Pending</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-plus"></i> Registration Pending
                    </a>
                    <a href="{{ route('admin.student.registration.payment.rejected') }}" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle"></i> Payment Rejected
                    </a>
                    <a href="{{ route('admin.student.registration.payment.history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> Payment History
                    </a>
                </div>
            </div>

            {{-- Payment Rejected Table --}}
            @php
                $rejectedPayments = \App\Models\Payment::where('payment_status', 'rejected')->orderBy('rejected_at', 'desc')->get();
            @endphp
            @if($rejectedPayments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Students with Rejected Payments
                    </h6>
                    <div>
                        <small>Total: {{ $rejectedPayments->count() }}</small>
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
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Rejected Date</th>
                                    <th>Rejection Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rejectedPayments as $payment)
                                <tr>
                                    <td>{{ $payment->enrollment->student_name ?? 'N/A' }}</td>
                                    <td>{{ $payment->enrollment->student_email ?? 'N/A' }}</td>
                                    <td>{{ $payment->enrollment->program->program_name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>{{ $payment->rejected_at ? \Carbon\Carbon::parse($payment->rejected_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $payment->rejection_reason }}">
                                            {{ $payment->rejection_reason ?? 'No reason provided' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="viewRejectedPaymentDetails({{ $payment->payment_id }})">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="editRejectedPaymentFields({{ $payment->payment_id }})">
                                                <i class="bi bi-pencil"></i> Edit Rejection
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

            {{-- Payment Resubmission Table --}}
            @php
                $resubmittedPayments = \App\Models\Payment::where('payment_status', 'resubmitted')->orderBy('resubmitted_at', 'desc')->get();
            @endphp
            @if($resubmittedPayments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-arrow-repeat me-2"></i>Students with Pending Payment Resubmission
                    </h6>
                    <div>
                        <small>Total: {{ $resubmittedPayments->count() }}</small>
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
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Resubmitted Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resubmittedPayments as $payment)
                                <tr>
                                    <td>{{ $payment->enrollment->student_name ?? 'N/A' }}</td>
                                    <td>{{ $payment->enrollment->student_email ?? 'N/A' }}</td>
                                    <td>{{ $payment->enrollment->program->program_name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>{{ $payment->resubmitted_at ? \Carbon\Carbon::parse($payment->resubmitted_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Previous Rejected</span>
                                        <span class="badge bg-info">Pending</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="viewPaymentResubmissionComparison({{ $payment->payment_id }})">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="approvePaymentResubmission({{ $payment->payment_id }})">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="rejectPaymentResubmission({{ $payment->payment_id }})">
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

            {{-- Payment Pending Approval Table (for submitted payment proofs) --}}
            @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-clock-history me-2"></i>Payment Pending Approval (Submitted Payment Proofs)
                    </h6>
                    <div>
                        <small>Total: {{ $pendingApprovals->count() }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> These students have submitted payment proofs and are waiting for admin verification.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Program</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Reference Number</th>
                                    <th>Submitted Date</th>
                                    <th>Payment Proof</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingApprovals as $enrollment)
                                @php
                                    $payment = $enrollment->payment;
                                    $paymentDetails = is_string($payment->payment_details) 
                                        ? json_decode($payment->payment_details, true) 
                                        : $payment->payment_details;
                                @endphp
                                <tr>
                                    <td>{{ $enrollment->student_name }}</td>
                                    <td>{{ $enrollment->student_email }}</td>
                                    <td>
                                        @if($enrollment->program)
                                            {{ $enrollment->program->program_name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>₱{{ number_format($payment->amount ?? 0, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                                    <td>
                                        {{ $payment->reference_number ?? ($paymentDetails['reference_number'] ?? 'N/A') }}
                                    </td>
                                    <td>{{ $payment->created_at ? $payment->created_at->format('M d, Y g:i A') : 'N/A' }}</td>
                                    <td>
                                        @if(isset($paymentDetails['payment_proof_path']))
                                            <a href="{{ asset('storage/' . $paymentDetails['payment_proof_path']) }}" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-image"></i> View Proof
                                            </a>
                                        @else
                                            <span class="text-muted">No proof</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="approvePaymentSubmission({{ $payment->payment_id ?? 0 }})">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="viewPaymentSubmissionDetails({{ $payment->payment_id ?? 0 }})">
                                                <i class="bi bi-eye"></i> View Details
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="rejectPaymentSubmission({{ $payment->payment_id ?? 0 }})">
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
            @else
            {{-- Show this section when there are no pending approvals --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-clock-history me-2"></i>Payment Pending Approval (Submitted Payment Proofs)
                    </h6>
                    <div>
                        <small>Total: 0</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-check" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3 text-muted">No Payment Submissions Awaiting Approval</h5>
                        <p class="text-muted">Students who submit payment proofs will appear here for admin verification.</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Students with Pending Payments (no payment proof submitted yet) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Students with Pending Payments (No Payment Proof Submitted)
                    </h6>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Amount</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->student_name }}</td>
                                        <td>{{ $enrollment->student_email }}</td>
                                        <td>{{ $enrollment->program->program_name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->package->package_name ?? 'N/A' }}</td>
                                        <td>₱{{ number_format($enrollment->package->amount ?? 0, 2) }}</td>
                                        <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ ucfirst($enrollment->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="showMarkAsPaidModal({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-check-circle"></i> Mark Paid
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="showViewDetailsModal({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectPayment({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-x-circle"></i> Reject
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
                            <i class="bi bi-credit-card-2-front" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Pending Payments</h5>
                            <p class="text-muted">All students have completed their payments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Rejection Modal -->
<div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="rejectPaymentForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectPaymentModalLabel">Reject Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Mark the payment fields that need to be corrected by the student. These fields will be highlighted when the student views their payment.
                    </div>
                    
                    <!-- Payment details will be loaded here -->
                    <div id="paymentRejectionFieldsContainer">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="paymentRejectionReason" class="form-label">Notes/Comments <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="paymentRejectionReason" name="reason" rows="4" 
                                  placeholder="Please provide clear instructions for what needs to be corrected..." required></textarea>
                        <div class="form-text">This message will be sent to the student along with the marked fields.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Reject Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Resubmission Comparison Modal -->
<div class="modal fade" id="paymentResubmissionComparisonModal" tabindex="-1" aria-labelledby="paymentResubmissionComparisonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentResubmissionComparisonModalLabel">Payment Resubmission Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Previous Rejected Payment</h6>
                        <div id="previousPaymentData" class="border p-3 bg-light">
                            <!-- Previous payment data will be loaded here -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="bi bi-arrow-clockwise"></i> New Resubmitted Payment</h6>
                        <div id="newPaymentData" class="border p-3">
                            <!-- New payment data will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approvePaymentResubmissionBtn">
                    <i class="bi bi-check-circle"></i> Approve Payment
                </button>
                <button type="button" class="btn btn-danger" id="rejectPaymentResubmissionBtn">
                    <i class="bi bi-x-circle"></i> Reject Again
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Rejected Payment Fields Modal -->
<div class="modal fade" id="editRejectedPaymentFieldsModal" tabindex="-1" aria-labelledby="editRejectedPaymentFieldsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editRejectedPaymentFieldsForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editRejectedPaymentFieldsModalLabel">Edit Payment Rejection Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editPaymentRejectionFieldsContainer">
                        <!-- Fields will be loaded here -->
                    </div>
                    
                    <div class="mt-4">
                        <label for="editPaymentRejectionReason" class="form-label">Updated Notes/Comments</label>
                        <textarea class="form-control" id="editPaymentRejectionReason" name="reason" rows="4"></textarea>
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

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markAsPaidModalLabel">Mark Payment as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetails">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action will mark the payment as completed. Are you sure you want to proceed?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmMarkPaid">
                    <i class="bi bi-check-circle"></i> Mark as Paid
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="enrollmentDetails">
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

<script>
let currentEnrollmentId = null;
let currentPaymentId = null;
const baseUrl = window.location.origin;
const token = document.querySelector('meta[name="csrf-token"]').content;

function showMarkAsPaidModal(enrollmentId) {
    currentEnrollmentId = enrollmentId;
    loadPaymentDetails(enrollmentId);
    new bootstrap.Modal(document.getElementById('markAsPaidModal')).show();
}

function showViewDetailsModal(enrollmentId) {
    loadEnrollmentDetails(enrollmentId);
    new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
}

// Reject payment function
function rejectPayment(enrollmentId) {
    currentEnrollmentId = enrollmentId;
    const form = document.getElementById('rejectPaymentForm');
    form.action = `${baseUrl}/admin/payments/${enrollmentId}/reject`;
    
    loadPaymentFieldsForRejection(enrollmentId);
    new bootstrap.Modal(document.getElementById('rejectPaymentModal')).show();
}

// Load payment fields for rejection marking
function loadPaymentFieldsForRejection(enrollmentId) {
    const container = document.getElementById('paymentRejectionFieldsContainer');
    container.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    
    fetch(`${baseUrl}/admin/enrollment/${enrollmentId}/payment-details`)
        .then(response => response.json())
        .then(data => {
            let fieldsHtml = '<div class="row">';
            
            // Payment Information Fields
            fieldsHtml += `
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="bi bi-credit-card"></i> Payment Information</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="payment_proof" id="reject_payment_proof">
                        <label class="form-check-label" for="reject_payment_proof">Payment Proof/Receipt</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="reference_number" id="reject_reference_number">
                        <label class="form-check-label" for="reject_reference_number">Reference Number: ${data.reference_number || 'N/A'}</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="payment_method" id="reject_payment_method">
                        <label class="form-check-label" for="reject_payment_method">Payment Method: ${data.payment_method || 'N/A'}</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="amount" id="reject_amount">
                        <label class="form-check-label" for="reject_amount">Amount: ₱${data.amount || 'N/A'}</label>
                    </div>
                </div>
            `;
            
            // Payment Method Specific Fields
            fieldsHtml += `
                <div class="col-md-6">
                    <h6 class="text-info"><i class="bi bi-gear"></i> Payment Method Fields</h6>
            `;
            
            if (data.payment_details) {
                const paymentDetails = typeof data.payment_details === 'string' ? JSON.parse(data.payment_details) : data.payment_details;
                
                // Add dynamic fields based on payment method
                if (paymentDetails.phone_number) {
                    fieldsHtml += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="phone_number" id="reject_phone_number">
                            <label class="form-check-label" for="reject_phone_number">Phone Number: ${paymentDetails.phone_number}</label>
                        </div>
                    `;
                }
                
                if (paymentDetails.gcash_reference_number) {
                    fieldsHtml += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="gcash_reference_number" id="reject_gcash_ref">
                            <label class="form-check-label" for="reject_gcash_ref">GCash Reference: ${paymentDetails.gcash_reference_number}</label>
                        </div>
                    `;
                }
            }
            
            fieldsHtml += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="terms_condition" id="reject_terms">
                        <label class="form-check-label" for="reject_terms">Terms and Conditions Agreement</label>
                    </div>
                </div>
            `;
            
            fieldsHtml += '</div>';
            container.innerHTML = fieldsHtml;
        })
        .catch(error => {
            console.error('Error loading payment fields:', error);
            container.innerHTML = '<div class="alert alert-danger">Failed to load payment fields.</div>';
        });
}

// View rejected payment details
function viewRejectedPaymentDetails(paymentId) {
    // Similar to viewRegistrationDetails but for payments
    fetch(`${baseUrl}/admin/payment/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            // Show payment details modal
            showViewDetailsModal(data.enrollment_id);
        });
}

// View payment resubmission comparison
function viewPaymentResubmissionComparison(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentResubmissionComparisonModal'));
    
    // Load both original and new payment data
    Promise.all([
        fetch(`${baseUrl}/admin/payment/${paymentId}/original-data`),
        fetch(`${baseUrl}/admin/payment/${paymentId}/details`)
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(([originalData, newData]) => {
        displayPaymentComparisonData(originalData, newData, paymentId);
        modal.show();
    })
    .catch(error => {
        console.error('Error loading payment comparison data:', error);
        alert('Failed to load comparison data');
    });
}

// Display payment comparison data
function displayPaymentComparisonData(originalData, newData, paymentId) {
    const previousContainer = document.getElementById('previousPaymentData');
    const newContainer = document.getElementById('newPaymentData');
    
    // Helper function to create field display
    function createPaymentFieldDisplay(label, value, isRejected = false) {
        const className = isRejected ? 'bg-danger text-white p-2 rounded mb-2' : 'mb-2';
        return `<div class="${className}"><strong>${label}:</strong> ${value || 'N/A'}</div>`;
    }
    
    // Get rejected fields
    const rejectedFields = originalData.rejected_fields ? JSON.parse(originalData.rejected_fields) : [];
    
    // Display previous payment data with rejected fields highlighted
    let previousHtml = `
        <h6 class="text-danger mb-3">Rejection Reason:</h6>
        <div class="alert alert-danger">${originalData.rejection_reason}</div>
        <h6 class="mb-3">Previous Payment Information:</h6>
    `;
    previousHtml += createPaymentFieldDisplay('Reference Number', originalData.reference_number, rejectedFields.includes('reference_number'));
    previousHtml += createPaymentFieldDisplay('Payment Method', originalData.payment_method, rejectedFields.includes('payment_method'));
    previousHtml += createPaymentFieldDisplay('Amount', `₱${originalData.amount}`, rejectedFields.includes('amount'));
    
    // Display new payment data
    let newHtml = '<h6 class="mb-3">New Payment Information:</h6>';
    newHtml += createPaymentFieldDisplay('Reference Number', newData.reference_number);
    newHtml += createPaymentFieldDisplay('Payment Method', newData.payment_method);
    newHtml += createPaymentFieldDisplay('Amount', `₱${newData.amount}`);
    
    previousContainer.innerHTML = previousHtml;
    newContainer.innerHTML = newHtml;
    
    // Setup buttons
    document.getElementById('approvePaymentResubmissionBtn').onclick = () => approvePaymentResubmission(paymentId);
    document.getElementById('rejectPaymentResubmissionBtn').onclick = () => rejectPaymentResubmission(paymentId);
}

// Approve payment resubmission
function approvePaymentResubmission(paymentId) {
    if (confirm('Are you sure you want to approve this payment resubmission?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${baseUrl}/admin/payment/${paymentId}/approve-resubmission`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = token;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reject payment resubmission
function rejectPaymentResubmission(paymentId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentResubmissionComparisonModal'));
    modal.hide();
    
    // Get enrollment ID from payment and reject again
    fetch(`${baseUrl}/admin/payment/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            rejectPayment(data.enrollment_id);
        });
}

// Edit rejected payment fields
function editRejectedPaymentFields(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('editRejectedPaymentFieldsModal'));
    const form = document.getElementById('editRejectedPaymentFieldsForm');
    form.action = `${baseUrl}/admin/payment/${paymentId}/update-rejection`;
    
    // Load current rejection data
    fetch(`${baseUrl}/admin/payment/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editPaymentRejectionReason').value = data.rejection_reason || '';
            loadEditablePaymentRejectionFields(paymentId, data.rejected_fields);
            modal.show();
        });
}

function loadEditablePaymentRejectionFields(paymentId, currentRejectedFields) {
    const container = document.getElementById('editPaymentRejectionFieldsContainer');
    const rejectedFields = currentRejectedFields ? JSON.parse(currentRejectedFields) : [];
    
    // Similar to loadPaymentFieldsForRejection but with current selections
    fetch(`${baseUrl}/admin/payment/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            let fieldsHtml = '<div class="row">';
            
            const fields = [
                { name: 'payment_proof', label: 'Payment Proof', value: 'Upload' },
                { name: 'reference_number', label: 'Reference Number', value: data.reference_number },
                { name: 'payment_method', label: 'Payment Method', value: data.payment_method },
                { name: 'amount', label: 'Amount', value: `₱${data.amount}` }
            ];
            
            fieldsHtml += '<div class="col-12"><h6>Select payment fields to mark as needing correction:</h6>';
            
            fields.forEach(field => {
                const isChecked = rejectedFields.includes(field.name) ? 'checked' : '';
                fieldsHtml += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="${field.name}" id="edit_payment_${field.name}" ${isChecked}>
                        <label class="form-check-label" for="edit_payment_${field.name}">${field.label}: ${field.value || 'N/A'}</label>
                    </div>
                `;
            });
            
            fieldsHtml += '</div></div>';
            container.innerHTML = fieldsHtml;
        });
}

function showMarkAsPaidModal(enrollmentId) {
    currentEnrollmentId = enrollmentId;
    loadPaymentDetails(enrollmentId);
    new bootstrap.Modal(document.getElementById('markAsPaidModal')).show();
}

function showViewDetailsModal(enrollmentId) {
    loadEnrollmentDetails(enrollmentId);
    new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
}

function loadPaymentDetails(enrollmentId) {
    const paymentDetails = document.getElementById('paymentDetails');
    paymentDetails.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    fetch(`/admin/enrollment/${enrollmentId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                paymentDetails.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            paymentDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Student Information</h6>
                        <p><strong>Name:</strong> ${data.student_name}</p>
                        <p><strong>Email:</strong> ${data.email}</p>
                        <p><strong>Contact:</strong> ${data.contact_number}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Information</h6>
                        <p><strong>Program:</strong> ${data.program_name}</p>
                        <p><strong>Package:</strong> ${data.package_name}</p>
                        <p><strong>Amount:</strong> ₱${parseFloat(data.amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                        <p><strong>Status:</strong> <span class="badge bg-warning">${data.payment_status}</span></p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            paymentDetails.innerHTML = `<div class="alert alert-danger">Error loading payment details</div>`;
        });
}

function loadEnrollmentDetails(enrollmentId) {
    const enrollmentDetails = document.getElementById('enrollmentDetails');
    enrollmentDetails.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    fetch(`/admin/enrollment/${enrollmentId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                enrollmentDetails.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            enrollmentDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Student Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> ${data.student_name}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Contact:</strong> ${data.contact_number}</p>
                                <p><strong>Enrollment ID:</strong> ${data.enrollment_id}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Program Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Program:</strong> ${data.program_name}</p>
                                <p><strong>Package:</strong> ${data.package_name}</p>
                                <p><strong>Type:</strong> ${data.enrollment_type}</p>
                                <p><strong>Amount:</strong> ₱${parseFloat(data.amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Payment Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Payment Status:</strong> 
                                            <span class="badge ${data.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${data.payment_status}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Payment Method:</strong> ${data.payment_method || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Enrollment Date:</strong> ${data.enrollment_date}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            enrollmentDetails.innerHTML = `<div class="alert alert-danger">Error loading enrollment details</div>`;
        });
}

document.getElementById('confirmMarkPaid').addEventListener('click', function() {
    if (!currentEnrollmentId) return;
    
    fetch(`/admin/enrollment/${currentEnrollmentId}/mark-paid`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('markAsPaidModal')).hide();
            location.reload();
        } else {
            alert('Error updating payment status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment status');
    });
});

// Legacy function for compatibility
function markAsPaid(enrollmentId) {
    showMarkAsPaidModal(enrollmentId);
}

function viewDetails(enrollmentId) {
    showViewDetailsModal(enrollmentId);
}

// New functions for payment submission approval
function approvePaymentSubmission(paymentId) {
    if (confirm('Are you sure you want to approve this payment submission?')) {
        fetch(`/admin/payments/${paymentId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment approved successfully!');
                location.reload();
            } else {
                alert('Error approving payment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving payment');
        });
    }
}

function viewPaymentSubmissionDetails(paymentId) {
    fetch(`/admin/payments/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPaymentSubmissionDetails(data.payment);
                new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
            } else {
                alert('Error loading payment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment details');
        });
}

function displayPaymentSubmissionDetails(payment) {
    const enrollmentDetails = document.getElementById('enrollmentDetails');
    enrollmentDetails.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Payment Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Amount:</strong> ₱${payment.amount ? Number(payment.amount).toLocaleString() : 'N/A'}</p>
                        <p><strong>Payment Method:</strong> ${payment.payment_method || 'N/A'}</p>
                        <p><strong>Reference Number:</strong> ${payment.reference_number || 'N/A'}</p>
                        <p><strong>Transaction Date:</strong> ${payment.transaction_date || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge bg-warning">Pending Approval</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Payment Proof</h6>
                    </div>
                    <div class="card-body">
                        ${payment.payment_proof ? 
                            `<img src="${payment.payment_proof}" class="img-fluid" style="max-height: 300px;" alt="Payment Proof">
                             <br><small class="text-muted">Click to view full size</small>` :
                            '<p class="text-muted">No payment proof uploaded</p>'
                        }
                    </div>
                </div>
            </div>
        </div>
    `;
}

function rejectPaymentSubmission(paymentId) {
    currentEnrollmentId = paymentId; // For compatibility with existing reject modal
    const form = document.getElementById('rejectPaymentForm');
    form.action = `/admin/payments/${paymentId}/reject`;
    
    loadPaymentFieldsForRejection(paymentId);
    new bootstrap.Modal(document.getElementById('rejectPaymentModal')).show();
}
</script>
@endsection
