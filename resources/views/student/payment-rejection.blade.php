@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card text-warning me-2"></i>
                        Payment Status: {{ ucfirst($payment->status ?? 'Unknown') }}
                    </h4>
                    <div>
                        @if(($payment->status ?? '') === 'rejected')
                            <span class="badge badge-danger px-3 py-2">Needs Attention</span>
                        @elseif(($payment->status ?? '') === 'resubmitted')
                            <span class="badge badge-warning px-3 py-2">Under Review</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if(($payment->status ?? '') === 'rejected')
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle me-2"></i>Payment Rejected</h5>
                            <p class="mb-2">Your payment has been rejected for the following reason:</p>
                            <blockquote class="blockquote mb-3">
                                <p class="mb-0">"{{ $payment->rejection_reason ?? 'No reason provided' }}"</p>
                                <footer class="blockquote-footer mt-2">
                                    Rejected on {{ isset($payment->rejected_at) ? $payment->rejected_at->format('M d, Y \a\t h:i A') : 'Unknown date' }}
                                </footer>
                            </blockquote>
                            
                            @if(isset($rejectedFields) && count($rejectedFields) > 0)
                                <h6>Fields that need attention:</h6>
                                <div class="rejected-fields mb-3">
                                    @foreach($rejectedFields as $field)
                                        <span class="badge badge-danger me-1 mb-1">{{ ucwords(str_replace('_', ' ', $field)) }}</span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <p class="mb-0">
                                <strong>What to do next:</strong> Please review the highlighted fields below, make the necessary corrections, and resubmit your payment.
                            </p>
                        </div>
                    @elseif(($payment->status ?? '') === 'resubmitted')
                        <div class="alert alert-info">
                            <h5><i class="fas fa-clock me-2"></i>Under Review</h5>
                            <p class="mb-2">
                                Thank you for resubmitting your payment. It is currently under review by our finance team.
                            </p>
                            <p class="mb-0">
                                <strong>Resubmitted on:</strong> {{ isset($payment->resubmitted_at) ? $payment->resubmitted_at->format('M d, Y \a\t h:i A') : 'Recently' }}
                            </p>
                        </div>
                    @endif
                    
                    <!-- Payment Resubmission Form -->
                    <form id="paymentResubmissionForm" method="POST" action="{{ route('student.payment.resubmit', $payment->payment_id ?? 0) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Payment Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-money-bill-wave me-2"></i>Payment Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">
                                                Amount
                                                @if(in_array('amount', $rejectedFields ?? []))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="number" 
                                                       step="0.01"
                                                       class="form-control @if(in_array('amount', $rejectedFields ?? [])) is-invalid @endif" 
                                                       id="amount" 
                                                       name="amount" 
                                                       value="{{ old('amount', $payment->amount ?? '') }}" 
                                                       required>
                                                @if(in_array('amount', $rejectedFields ?? []))
                                                    <div class="invalid-feedback">This field was marked for correction</div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">
                                                Payment Method
                                                @if(in_array('payment_method', $rejectedFields ?? []))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <select class="form-control @if(in_array('payment_method', $rejectedFields ?? [])) is-invalid @endif" 
                                                    id="payment_method" 
                                                    name="payment_method" 
                                                    required>
                                                <option value="">Select Payment Method</option>
                                                <option value="GCash" {{ old('payment_method', $payment->payment_method ?? '') === 'GCash' ? 'selected' : '' }}>GCash</option>
                                                <option value="Maya" {{ old('payment_method', $payment->payment_method ?? '') === 'Maya' ? 'selected' : '' }}>Maya (PayMaya)</option>
                                                <option value="Bank Transfer" {{ old('payment_method', $payment->payment_method ?? '') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="Credit Card" {{ old('payment_method', $payment->payment_method ?? '') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="Cash" {{ old('payment_method', $payment->payment_method ?? '') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                            </select>
                                            @if(in_array('payment_method', $rejectedFields ?? []))
                                                <div class="invalid-feedback">This field was marked for correction</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Proof -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-receipt me-2"></i>Payment Proof
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="payment_proof" class="form-label">
                                                Upload Payment Proof
                                                @if(in_array('payment_proof', $rejectedFields ?? []))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            
                                            @if(isset($payment->payment_proof) && $payment->payment_proof)
                                                <div class="current-file mb-2">
                                                    <div class="alert alert-info py-2">
                                                        <i class="fas fa-file me-1"></i>
                                                        Current proof: 
                                                        <a href="{{ $payment->payment_proof }}" target="_blank" class="text-decoration-none">
                                                            View current document
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <input type="file" 
                                                   class="form-control @if(in_array('payment_proof', $rejectedFields ?? [])) is-invalid @endif" 
                                                   id="payment_proof" 
                                                   name="payment_proof" 
                                                   accept=".pdf,.jpg,.jpeg,.png">
                                            @if(in_array('payment_proof', $rejectedFields ?? []))
                                                <div class="invalid-feedback">This document was marked for correction - please upload a new file</div>
                                            @else
                                                <div class="form-text">Upload a new file only if you need to replace the current proof</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enrollment Information (Read-only) -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Enrollment Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Enrollment ID:</strong><br>
                                        <span class="text-muted">{{ $payment->enrollment_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Payment Date:</strong><br>
                                        <span class="text-muted">{{ isset($payment->created_at) ? $payment->created_at->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Original Amount:</strong><br>
                                        <span class="text-muted">₱{{ number_format($payment->amount ?? 0, 2) }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Payment ID:</strong><br>
                                        <span class="text-muted">{{ $payment->payment_id ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if(($payment->status ?? '') === 'rejected')
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Resubmit Payment
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        By resubmitting, you acknowledge that you have addressed the concerns mentioned above.
                                    </small>
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Your payment resubmission is being reviewed. You will be notified of the outcome soon.
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentResubmissionForm');
    
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        }
    });
    
    // Highlight rejected fields
    const rejectedFields = document.querySelectorAll('.is-invalid');
    rejectedFields.forEach(field => {
        field.addEventListener('focus', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.875em;
}

.is-invalid {
    border-color: #dc3545;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.rejected-fields .badge {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.current-file .alert {
    margin-bottom: 0.5rem;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
}

.text-danger {
    font-weight: bold;
}
</style>
@endsection
