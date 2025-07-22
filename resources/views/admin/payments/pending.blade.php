@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Pending Payments')

@push('styles')
<style>
    .payment-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        margin-bottom: 20px;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }
    
    .payment-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .payment-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
    }
    
    .payment-body {
        padding: 20px;
    }
    
    .payment-proof-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
    }
    
    .dynamic-fields {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin: 10px 0;
    }
    
    .field-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .field-item:last-child {
        border-bottom: none;
    }
    
    .field-label {
        font-weight: 600;
        color: #495057;
    }
    
    .field-value {
        color: #6c757d;
        text-align: right;
        max-width: 60%;
        word-wrap: break-word;
    }
    
    .reject-modal .modal-content {
        border-radius: 15px;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-12 mb-4">
            <div class="row" id="statsContainer">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <h3 class="text-warning" id="pendingCount">{{ count($payments) }}</h3>
                        <p class="mb-0">Pending Payments</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <h3 class="text-success" id="approvedCount">0</h3>
                        <p class="mb-0">Approved Today</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <h3 class="text-danger" id="rejectedCount">0</h3>
                        <p class="mb-0">Rejected Today</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <h3 class="text-info" id="totalAmount">₱0.00</h3>
                        <p class="mb-0">Total Pending</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-credit-card me-2"></i>Pending Payments</h2>
                <div>
                    <button class="btn btn-outline-primary" onclick="refreshPayments()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                    <a href="{{ route('admin.payments.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-2"></i>Payment History
                    </a>
                </div>
            </div>
            
            @if($payments->count() > 0)
                @foreach($payments as $payment)
                <div class="payment-card">
                    <div class="payment-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-1">
                                    <i class="bi bi-person-circle me-2"></i>
                                    {{ $payment->enrollment->student->first_name }} {{ $payment->enrollment->student->last_name }}
                                </h5>
                                <small>Student ID: {{ $payment->enrollment->student->student_id }}</small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h4 class="mb-1">₱{{ number_format($payment->amount, 2) }}</h4>
                                <small>{{ $payment->created_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="bi bi-book me-2"></i>Course Information</h6>
                                <p class="mb-1"><strong>Program:</strong> {{ $payment->enrollment->program->program_name }}</p>
                                <p class="mb-1"><strong>Package:</strong> {{ $payment->enrollment->package->package_name }}</p>
                                <p class="mb-3"><strong>Payment Method:</strong> 
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-info-circle me-2"></i>Enrollment Details</h6>
                                <p class="mb-1"><strong>Enrollment ID:</strong> #{{ $payment->enrollment_id }}</p>
                                <p class="mb-1"><strong>Status:</strong> 
                                    <span class="badge bg-warning">{{ ucfirst($payment->enrollment->enrollment_status) }}</span>
                                </p>
                                <p class="mb-3"><strong>Payment Status:</strong> 
                                    <span class="badge bg-warning">{{ ucfirst($payment->payment_status) }}</span>
                                </p>
                            </div>
                        </div>
                        
                        @php
                            $paymentDetails = json_decode($payment->payment_details, true) ?? [];
                        @endphp
                        
                        @if(!empty($paymentDetails))
                        <div class="dynamic-fields">
                            <h6><i class="bi bi-list-check me-2"></i>Payment Information</h6>
                            @foreach($paymentDetails as $key => $value)
                                @if($key !== 'payment_proof_path' && $key !== 'uploaded_at')
                                <div class="field-item">
                                    <span class="field-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="field-value">{{ $value }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($paymentDetails['payment_proof_path']))
                        <div class="payment-proof-section">
                            <h6><i class="bi bi-image me-2"></i>Payment Proof</h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <span>Payment proof uploaded</span>
                                <div>
                                    <a href="{{ route('admin.payments.download-proof', $payment->payment_id) }}" 
                                       class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewProof('{{ $paymentDetails['payment_proof_path'] }}')">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="mt-4 d-flex gap-2">
                            <button class="btn btn-success" onclick="approvePayment({{ $payment->payment_id }})">
                                <i class="bi bi-check-circle me-2"></i>Approve Payment
                            </button>
                            <button class="btn btn-danger" onclick="showRejectModal({{ $payment->payment_id }})">
                                <i class="bi bi-x-circle me-2"></i>Reject Payment
                            </button>
                            <a href="{{ route('admin.payments.show', $payment->payment_id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Pending Payments</h4>
                    <p class="text-muted">All payments have been processed!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade reject-modal" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> The student will be notified and can resubmit their payment.
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Rejection Reason *</label>
                        <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" 
                                  placeholder="Please provide a clear reason for rejection..." required></textarea>
                        <div class="form-text">This message will be shown to the student.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Reject Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Payment Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Confirm:</strong> This will approve the payment and grant course access to the student.
                    </div>
                    
                    <div class="mb-3">
                        <label for="receiptNumber" class="form-label">Receipt Number (Optional)</label>
                        <input type="text" class="form-control" id="receiptNumber" name="receipt_number" 
                               placeholder="e.g., RCT-2025-001">
                    </div>
                    
                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="approvalNotes" name="notes" rows="3" 
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Approve Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showRejectModal(paymentId) {
    const form = document.getElementById('rejectForm');
    form.action = `{{ route('admin.payments.reject', '') }}/${paymentId}`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function approvePayment(paymentId) {
    const form = document.getElementById('approveForm');
    form.action = `{{ route('admin.payments.approve', '') }}/${paymentId}`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function viewProof(filePath) {
    // Open image in new window/tab
    window.open(`{{ url('storage') }}/${filePath}`, '_blank');
}

function refreshPayments() {
    location.reload();
}

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("admin.payments.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('pendingCount').textContent = data.pending || 0;
            document.getElementById('approvedCount').textContent = data.approved || 0;
            document.getElementById('rejectedCount').textContent = data.rejected || 0;
            document.getElementById('totalAmount').textContent = '₱' + (data.total_amount_pending || 0).toLocaleString();
        })
        .catch(error => console.log('Error loading stats:', error));
});
</script>
@endpush
