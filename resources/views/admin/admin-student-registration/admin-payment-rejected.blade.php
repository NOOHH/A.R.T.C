@extends('admin.admin-dashboard-layout')

@section('title', 'Payment Rejected')

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
        .payment-proof-img {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .field-rejected { 
            background-color: #ffe6e6 !important; 
            border-left: 4px solid #dc3545; 
            padding-left: 8px; 
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
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment Rejected</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.pending') }}" class="btn btn-outline-primary">
                        <i class="bi bi-clock"></i> Registration Pending
                    </a>
                    <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                        <i class="bi bi-credit-card"></i> Payment Pending
                    </a>
                    <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> Payment History
                    </a>
                </div>
            </div>

            {{-- Rejected Payments Table --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Students with Rejected Payment
                    </h6>
                    <div>
                        <small>Total: {{ $payments->count() }}</small>
                    </div>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
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
                                        <th>Rejected Fields</th>
                                        <th>Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            @if(isset($payment->registration))
                                                {{ ($payment->registration->firstname ?? '') }} 
                                                {{ ($payment->registration->middlename ?? '') }}
                                                {{ ($payment->registration->lastname ?? '') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($payment->registration->user) && $payment->registration->user)
                                                {{ $payment->registration->user->email ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($payment->registration))
                                                {{ $payment->registration->program_name ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>₱{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                        <td>{{ $payment->rejected_at ? \Carbon\Carbon::parse($payment->rejected_at)->format('M d, Y g:i A') : 'N/A' }}</td>
                                        <td>
                                            @if($payment->rejected_fields)
                                                @php
                                                    $rejectedFields = json_decode($payment->rejected_fields, true) ?? [];
                                                @endphp
                                                @foreach($rejectedFields as $field)
                                                    <span class="rejected-field badge badge-danger">{{ $field }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No specific fields</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate" style="max-width: 150px; display: inline-block;" title="{{ $payment->rejection_reason }}">
                                                {{ $payment->rejection_reason ?? 'No reason provided' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-info btn-sm" onclick="viewRejectedPayment({{ $payment->payment_id }})">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="editPaymentRejection({{ $payment->payment_id }})">
                                                    <i class="bi bi-pencil"></i> Edit Rejection
                                                </button>
                                                <button class="btn btn-success btn-sm" onclick="approvePayment({{ $payment->payment_id }})">
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
                            <h5 class="mt-3 text-muted">No Rejected Payments</h5>
                            <p class="text-muted">All payments are currently pending or approved.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resubmitted Payments Table --}}
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
                                    <td>
                                        @if(isset($payment->registration))
                                            {{ ($payment->registration->firstname ?? '') }} 
                                            {{ ($payment->registration->middlename ?? '') }}
                                            {{ ($payment->registration->lastname ?? '') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($payment->registration->user) && $payment->registration->user)
                                            {{ $payment->registration->user->email ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($payment->registration))
                                            {{ $payment->registration->program_name ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                    <td>{{ $payment->resubmitted_at ? \Carbon\Carbon::parse($payment->resubmitted_at)->format('M d, Y g:i A') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-warning">Previously Rejected</span>
                                        <span class="badge badge-info">Pending Review</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-primary btn-sm" onclick="viewPaymentComparison({{ $payment->payment_id }})">
                                                <i class="bi bi-layout-split"></i> Compare
                                            </button>
                                            <button class="btn btn-success btn-sm" onclick="approvePaymentResubmission({{ $payment->payment_id }})">
                                                <i class="bi bi-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="rejectPaymentAgain({{ $payment->payment_id }})">
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

{{-- View Payment Modal --}}
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPaymentModalLabel">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewPaymentContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Payment Comparison Modal --}}
<div class="modal fade" id="paymentComparisonModal" tabindex="-1" aria-labelledby="paymentComparisonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentComparisonModalLabel">Payment Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-comparison d-flex gap-3">
                    <div class="comparison-side">
                        <h6 class="bg-danger text-white">Original (Rejected) Payment</h6>
                        <div class="comparison-content" id="originalPaymentContent">
                            <!-- Original content will be loaded here -->
                        </div>
                    </div>
                    <div class="comparison-side">
                        <h6 class="bg-success text-white">New (Resubmitted) Payment</h6>
                        <div class="comparison-content" id="newPaymentContent">
                            <!-- New content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approvePaymentResubmissionFromModal()">
                    <i class="bi bi-check"></i> Approve Resubmission
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectPaymentAgainFromModal()">
                    <i class="bi bi-x"></i> Reject Again
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Payment Rejection Modal --}}
<div class="modal fade" id="editPaymentRejectionModal" tabindex="-1" aria-labelledby="editPaymentRejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentRejectionModalLabel">Edit Payment Rejection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPaymentRejectionForm">
                <div class="modal-body">
                    <input type="hidden" id="editPaymentId" name="payment_id">
                    
                    <div class="mb-3">
                        <label for="editPaymentRejectionReason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="editPaymentRejectionReason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mark Fields for Redo (optional)</label>
                        <div id="editPaymentFieldsList" class="form-check-container">
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
let currentPaymentId = null;

function viewRejectedPayment(paymentId) {
    currentPaymentId = paymentId;
    
    fetch(`{{ route('admin.payments.details', '') }}/${paymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPaymentDetails(data.payment);
                const modal = new bootstrap.Modal(document.getElementById('viewPaymentModal'));
                modal.show();
            } else {
                alert('Error loading payment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment details');
        });
}

function displayPaymentDetails(payment) {
    const content = document.getElementById('viewPaymentContent');
    const rejectedFields = payment.rejected_fields ? JSON.parse(payment.rejected_fields) : [];
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Payment Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Amount:</strong></td>
                        <td>₱${payment.amount ? Number(payment.amount).toLocaleString() : 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('payment_method') ? 'field-rejected' : ''}">
                        <td><strong>Payment Method:</strong></td>
                        <td>${payment.payment_method || 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('reference_number') ? 'field-rejected' : ''}">
                        <td><strong>Reference Number:</strong></td>
                        <td>${payment.reference_number || 'N/A'}</td>
                    </tr>
                    <tr class="${rejectedFields.includes('transaction_date') ? 'field-rejected' : ''}">
                        <td><strong>Transaction Date:</strong></td>
                        <td>${payment.transaction_date || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge badge-danger">Rejected</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Payment Proof</h6>
                <div class="${rejectedFields.includes('payment_proof') ? 'field-rejected' : ''}">
                    ${payment.payment_proof ? 
                        `<img src="${payment.payment_proof}" class="payment-proof-img" alt="Payment Proof">
                         <br><small class="text-muted">Click to view full size</small>` :
                        '<p class="text-muted">No payment proof uploaded</p>'
                    }
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Rejection Details</h6>
                <div class="alert alert-danger">
                    <strong>Reason:</strong> ${payment.rejection_reason || 'No reason provided'}
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

function editPaymentRejection(paymentId) {
    currentPaymentId = paymentId;
    
    fetch(`{{ route('admin.payments.details', '') }}/${paymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditPaymentForm(data.payment);
                const modal = new bootstrap.Modal(document.getElementById('editPaymentRejectionModal'));
                modal.show();
            } else {
                alert('Error loading payment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading payment details');
        });
}

function populateEditPaymentForm(payment) {
    document.getElementById('editPaymentId').value = payment.payment_id;
    document.getElementById('editPaymentRejectionReason').value = payment.rejection_reason || '';
    
    const fieldsContainer = document.getElementById('editPaymentFieldsList');
    const paymentFields = [
        'payment_method', 'reference_number', 'transaction_date', 'payment_proof',
        'amount', 'additional_notes'
    ];
    
    const rejectedFields = payment.rejected_fields ? JSON.parse(payment.rejected_fields) : [];
    
    fieldsContainer.innerHTML = paymentFields.map(field => `
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="rejected_fields[]" value="${field}" 
                   id="edit_payment_${field}" ${rejectedFields.includes(field) ? 'checked' : ''}>
            <label class="form-check-label" for="edit_payment_${field}">
                ${field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
            </label>
        </div>
    `).join('');
}

function viewPaymentComparison(paymentId) {
    currentPaymentId = paymentId;
    
    Promise.all([
        fetch(`{{ route('admin.payments.details', '') }}/${paymentId}`),
        fetch(`{{ route('admin.payments.original-data', '') }}/${paymentId}`)
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(([newData, originalData]) => {
        if (newData.success && originalData.success) {
            displayPaymentComparison(originalData.payment, newData.payment);
            const modal = new bootstrap.Modal(document.getElementById('paymentComparisonModal'));
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

function displayPaymentComparison(original, current) {
    const originalContent = document.getElementById('originalPaymentContent');
    const newContent = document.getElementById('newPaymentContent');
    
    const rejectedFields = original.rejected_fields ? JSON.parse(original.rejected_fields) : [];
    
    function createPaymentTable(data, isOriginal = false) {
        return `
            <table class="table table-sm">
                <tr>
                    <td><strong>Amount:</strong></td>
                    <td>₱${data.amount ? Number(data.amount).toLocaleString() : 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('payment_method') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Payment Method:</strong></td>
                    <td>${data.payment_method || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('reference_number') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Reference Number:</strong></td>
                    <td>${data.reference_number || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('transaction_date') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Transaction Date:</strong></td>
                    <td>${data.transaction_date || 'N/A'}</td>
                </tr>
                <tr class="${rejectedFields.includes('payment_proof') && isOriginal ? 'field-rejected' : ''}">
                    <td><strong>Payment Proof:</strong></td>
                    <td>
                        ${data.payment_proof ? 
                            `<img src="${data.payment_proof}" class="payment-proof-img" alt="Payment Proof">` :
                            'No proof uploaded'
                        }
                    </td>
                </tr>
            </table>
        `;
    }
    
    originalContent.innerHTML = createPaymentTable(original, true);
    newContent.innerHTML = createPaymentTable(current, false);
}

function approvePayment(paymentId) {
    if (confirm('Are you sure you want to approve this payment?')) {
        fetch(`{{ route('admin.payments.approve', '') }}/${paymentId}`, {
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
                alert('Error approving payment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving payment');
        });
    }
}

function approvePaymentResubmission(paymentId) {
    if (confirm('Are you sure you want to approve this payment resubmission?')) {
        fetch(`{{ route('admin.payments.approve-resubmission', '') }}/${paymentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment resubmission approved successfully!');
                location.reload();
            } else {
                alert('Error approving payment resubmission: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving payment resubmission');
        });
    }
}

// Add this function to handle payment rejection with fields
function rejectPaymentAgain(paymentId) {
    if (!confirm('Are you sure you want to reject this payment again?')) return;
    // Collect rejection reason and fields from modal or prompt
    let reason = prompt('Enter rejection reason:');
    if (reason === null) return; // Cancelled
    // Example: collect fields from checkboxes if you have a modal, else leave as empty array
    let rejectedFields = [];
    // If you have a modal, collect checked fields here
    // rejectedFields = [...document.querySelectorAll('input[name="rejected_fields[]"]:checked')].map(el => el.value);
    fetch(`/admin/payments/${paymentId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reason: reason,
            rejected_fields: rejectedFields
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment rejected successfully!');
            location.reload();
        } else {
            alert('Error rejecting payment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting payment');
    });
}

// Form submission for edit payment rejection
document.getElementById('editPaymentRejectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const paymentId = formData.get('payment_id');
    
    fetch(`{{ route('admin.payments.update-rejection', '') }}/${paymentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment rejection details updated successfully!');
            bootstrap.Modal.getInstance(document.getElementById('editPaymentRejectionModal')).hide();
            location.reload();
        } else {
            alert('Error updating payment rejection: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment rejection');
    });
});
</script>
@endsection
