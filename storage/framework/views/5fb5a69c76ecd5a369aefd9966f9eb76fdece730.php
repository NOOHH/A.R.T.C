

<?php $__env->startSection('title', 'Payments'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Management</h4>
                    <?php if(isset($isPreview) && $isPreview): ?>
                        <span class="badge bg-info">Preview Mode</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if(isset($payments) && $payments->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($payment->payment_id ?? $payment->id); ?></td>
                                            <td><?php echo e($payment->student_name); ?></td>
                                            <td>$<?php echo e(number_format($payment->amount, 2)); ?></td>
                                            <td><?php echo e($payment->payment_method); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e(($payment->payment_status ?? $payment->status) == 'completed' ? 'success' : 'warning'); ?>">
                                                    <?php echo e(ucfirst($payment->payment_status ?? $payment->status)); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($payment->created_at->format('M d, Y')); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                                <?php if(($payment->payment_status ?? $payment->status) == 'pending'): ?>
                                                    <button class="btn btn-sm btn-success">Approve</button>
                                                    <button class="btn btn-sm btn-danger">Reject</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5>No payments found</h5>
                            <p class="text-muted">Payments will appear here when students make transactions.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/payments/pending.blade.php ENDPATH**/ ?>