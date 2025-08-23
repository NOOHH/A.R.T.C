

<?php $__env->startSection('title', 'Payment History'); ?>

<?php $__env->startSection('head'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('head'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="base-url" content="<?php echo e(url('')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Payment History</h1>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.student.registration.payment.pending')); ?>" class="btn btn-outline-warning">
                        <i class="bi bi-clock"></i> Payment Pending
                    </a>
                    <a href="<?php echo e(route('admin.student.registration.history')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> Registration History
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Transaction History</h6>
                </div>
                <div class="card-body">
                    <?php if($paymentHistory->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $paymentHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php if($payment->enrollment && $payment->enrollment->student): ?>
                                                <?php echo e($payment->enrollment->student->firstname ?? 'N/A'); ?> <?php echo e($payment->enrollment->student->lastname ?? ''); ?>

                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($payment->enrollment && $payment->enrollment->student): ?>
                                                <?php echo e($payment->enrollment->student->email ?? 'N/A'); ?>

                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($payment->enrollment && $payment->enrollment->program ? $payment->enrollment->program->program_name : 'N/A'); ?></td>
                                        <td><?php echo e($payment->enrollment && $payment->enrollment->package ? $payment->enrollment->package->package_name : 'N/A'); ?></td>
                                        <td>₱<?php echo e(number_format($payment->amount ?? 0, 2)); ?></td>
                                        <td><?php echo e($payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y h:i A') : 'N/A'); ?></td>
                                        <td>
                                            <?php if($payment->payment_status === 'paid' || $payment->payment_status === 'completed'): ?>
                                                <span class="badge bg-success"><?php echo e(ucfirst($payment->payment_status)); ?></span>
                                            <?php elseif($payment->payment_status === 'failed'): ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php elseif($payment->payment_status === 'cancelled'): ?>
                                                <span class="badge bg-secondary">Cancelled</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo e(ucfirst($payment->payment_status)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewPaymentDetails(<?php echo e($payment->payment_history_id); ?>)">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <?php if($payment->payment_status === 'paid' || $payment->payment_status === 'completed'): ?>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="undoPaymentHistory(<?php echo e($payment->payment_history_id); ?>)">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Undo
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-receipt" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Payment History</h5>
                            <p class="text-muted">No payment transactions have been completed yet.</p>
                        </div>
                    <?php endif; ?>
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

<!-- Undo Payment History Modal -->
<div class="modal fade" id="undoPaymentHistoryModal" tabindex="-1" aria-labelledby="undoPaymentHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="undoPaymentHistoryForm">
        <div class="modal-header">
          <h5 class="modal-title" id="undoPaymentHistoryModalLabel">Undo Payment History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="undoHistoryReason" class="form-label">Reason for undoing <span class="text-danger">*</span></label>
            <textarea class="form-control" id="undoHistoryReason" name="reason" rows="3" required placeholder="Enter reason..."></textarea>
          </div>
          <input type="hidden" id="undoPaymentHistoryId" name="payment_history_id" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Undo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function viewPaymentDetails(paymentHistoryId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
    const contentDiv = document.getElementById('payment-details-content');
    const baseUrl = window.location.origin;
    
    // Show loading state
    contentDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading payment details...</p></div>';
    modal.show();
    
    // Fetch payment history details
    fetch(`${baseUrl}/admin/payment-history/${paymentHistoryId}/details`)
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

function undoPaymentApproval(paymentId) {
    if (confirm('Are you sure you want to undo this payment approval? This will return the payment to pending approval.')) {
        const baseUrl = window.location.origin;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`${baseUrl}/admin/payments/${paymentId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                alert('Payment approval undone successfully! Payment is now pending approval.');
                location.reload();
            } else {
                alert('Error undoing payment approval: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error undoing payment approval');
        });
    }
}

function undoPaymentHistory(paymentHistoryId) {
    document.getElementById('undoPaymentHistoryId').value = paymentHistoryId;
    document.getElementById('undoHistoryReason').value = '';
    new bootstrap.Modal(document.getElementById('undoPaymentHistoryModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    var undoForm = document.getElementById('undoPaymentHistoryForm');
    if (undoForm) {
        undoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const paymentHistoryId = document.getElementById('undoPaymentHistoryId').value;
            const reason = document.getElementById('undoHistoryReason').value.trim();
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (!reason) {
                alert('A reason is required.');
                return;
            }

            fetch(`/admin/payment-history/${paymentHistoryId}/undo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('undoPaymentHistoryModal')).hide();
                    alert('Payment history undone and removed successfully!');
                    location.reload();
                } else {
                    alert('Error undoing payment history: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error undoing payment history');
            });
        });
    }
});

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

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/admin-student-registration/admin-payment-history.blade.php ENDPATH**/ ?>