<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .preview-badge {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="preview-badge">PREVIEW MODE</div>
    
    <div class="preview-header">
        <div class="container">
            <h1 class="text-center">Admin Dashboard Preview</h1>
            <p class="text-center mb-0">This is a preview of the admin dashboard interface</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($analytics['total_students'] ?? 0); ?></div>
                    <div class="text-muted">Total Students</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($analytics['total_programs'] ?? 0); ?></div>
                    <div class="text-muted">Total Programs</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($analytics['total_enrollments'] ?? 0); ?></div>
                    <div class="text-muted">Total Enrollments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($analytics['pending_registrations'] ?? 0); ?></div>
                    <div class="text-muted">Pending Registrations</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Registrations</h5>
                    </div>
                    <div class="card-body">
                        <?php if($registrations && count($registrations) > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Program</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($reg->user_firstname ?? 'N/A'); ?> <?php echo e($reg->user_lastname ?? ''); ?></td>
                                                <td><?php echo e($reg->user_email ?? 'N/A'); ?></td>
                                                <td><?php echo e($reg->program_name ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge bg-warning"><?php echo e(ucfirst($reg->status ?? 'pending')); ?></span>
                                                </td>
                                                <td>$<?php echo e(number_format($reg->total_amount ?? 0, 2)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No registrations to display in preview mode.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/simple-preview.blade.php ENDPATH**/ ?>