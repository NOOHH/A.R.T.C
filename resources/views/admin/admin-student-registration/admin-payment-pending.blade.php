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
                    <a href="{{ route('admin.student.registration.payment.history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> Payment History
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Students with Pending Payments</h6>
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
</script>
@endsection
