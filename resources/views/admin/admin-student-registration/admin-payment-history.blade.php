@extends('admin.admin-dashboard-layout')

@section('title', 'Payment History')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment History</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                        <i class="bi bi-clock"></i> Payment Pending
                    </a>
                    <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> Registration History
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Transaction History</h6>
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
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            @if($enrollment->student && $enrollment->student->user)
                                                {{ $enrollment->student->user->user_name ?? ($enrollment->student->firstname . ' ' . $enrollment->student->lastname) }}
                                            @elseif($enrollment->user)
                                                {{ $enrollment->user->user_name }}
                                            @elseif($enrollment->student)
                                                {{ $enrollment->student->firstname ?? 'N/A' }} {{ $enrollment->student->lastname ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($enrollment->student && $enrollment->student->user)
                                                {{ $enrollment->student->user->user_email ?? $enrollment->student->email }}
                                            @elseif($enrollment->user)
                                                {{ $enrollment->user->user_email }}
                                            @elseif($enrollment->student)
                                                {{ $enrollment->student->email ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $enrollment->program->program_name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->package->package_name ?? 'N/A' }}</td>
                                        <td>₱{{ number_format($enrollment->package->package_price ?? $enrollment->package->amount ?? 0, 2) }}</td>
                                        <td>{{ $enrollment->created_at->format('M d, Y h:i A') }}</td>
                                        <td>{{ $enrollment->updated_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @if($enrollment->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($enrollment->payment_status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($enrollment->payment_status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($enrollment->payment_status === 'cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($enrollment->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewPaymentDetails({{ $enrollment->enrollment_id }})">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                @if($enrollment->payment_status === 'failed')
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="retryPayment({{ $enrollment->enrollment_id }})">
                                                        <i class="bi bi-arrow-clockwise"></i> Retry
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
                            <i class="bi bi-receipt" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Payment History</h5>
                            <p class="text-muted">No payment transactions have been completed yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailsModalLabel">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="payment-details-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function viewPaymentDetails(enrollmentId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
    const contentDiv = document.getElementById('payment-details-content');
    const baseUrl = window.location.origin;
    
    // Show loading state
    contentDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading payment details...</p></div>';
    modal.show();
    
    // Fetch enrollment/payment details
    fetch(`${baseUrl}/admin/student/enrollment/${enrollmentId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch payment details');
            }
            return response.json();
        })
        .then(data => {
            function na(value) {
                return (value === undefined || value === null || value === '' || value === 'null') ? 'N/A' : value;
            }
            
            // Build the payment details content
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-person"></i> Student Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Name:</strong></div>
                                    <div class="col-sm-8">${na(data.student_name)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">${na(data.email)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Phone:</strong></div>
                                    <div class="col-sm-8">${na(data.contact_number)}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-mortarboard"></i> Program Details</h6>
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
                                    <div class="col-sm-4"><strong>Enrollment Type:</strong></div>
                                    <div class="col-sm-8">${na(data.enrollment_type)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="bi bi-credit-card"></i> Payment Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Amount:</strong></div>
                                    <div class="col-sm-8">₱${na(data.amount ? parseFloat(data.amount).toLocaleString() : '0.00')}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Payment Status:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge ${data.payment_status === 'paid' || data.payment_status === 'completed' ? 'bg-success' : data.payment_status === 'failed' ? 'bg-danger' : 'bg-warning'}">
                                            ${na(data.payment_status)}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Payment Method:</strong></div>
                                    <div class="col-sm-8">${na(data.payment_method)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Reference No:</strong></div>
                                    <div class="col-sm-8">${na(data.reference_number)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Transaction ID:</strong></div>
                                    <div class="col-sm-8">${na(data.transaction_id)}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-clock"></i> Timeline</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Enrolled:</strong></div>
                                    <div class="col-sm-8">${na(data.enrollment_date)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Payment Date:</strong></div>
                                    <div class="col-sm-8">${na(data.payment_date)}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                    <div class="col-sm-8">${na(data.updated_at)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            contentDiv.innerHTML = content;
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> Failed to load payment details. Please try again.
                </div>
            `;
        });
}

function retryPayment(enrollmentId) {
    if (confirm('Are you sure you want to retry this payment?')) {
        const baseUrl = window.location.origin;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`${baseUrl}/admin/enrollment/${enrollmentId}/retry-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment retry initiated');
                location.reload();
            } else {
                alert('Error retrying payment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error retrying payment');
        });
    }
}

// Fix aria-hidden accessibility issue
document.addEventListener('DOMContentLoaded', function() {
    const paymentDetailsModal = document.getElementById('paymentDetailsModal');
    if (paymentDetailsModal) {
        paymentDetailsModal.addEventListener('show.bs.modal', function () {
            // Remove aria-hidden when modal is opening
            paymentDetailsModal.removeAttribute('aria-hidden');
        });
        
        paymentDetailsModal.addEventListener('hidden.bs.modal', function () {
            // Add aria-hidden when modal is completely closed
            paymentDetailsModal.setAttribute('aria-hidden', 'true');
        });
    }
});
</script>
