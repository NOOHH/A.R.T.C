

<?php $__env->startSection('title', 'Registration Details'); ?>

<?php $__env->startSection('head'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .detail-card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .detail-header {
            border-radius: 10px 10px 0 0;
        }
        .status-badge {
            font-size: 1rem;
            padding: 0.5em 1em;
        }
        .field-label {
            font-weight: 600;
            color: #495057;
        }
        .field-value {
            color: #212529;
        }
        .documents-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }
        .action-buttons {
            position: sticky;
            top: 20px;
            z-index: 1000;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Registration Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.student.registration.pending')); ?>">Registrations</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </nav>
                </div>
                <div class="action-buttons">
                    <a href="<?php echo e(route('admin.student.registration.pending')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Registration Info Card -->
            <div class="card detail-card mb-4">
                <div class="card-header detail-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Registration Information</h5>
                    <span class="badge status-badge 
                        <?php if($registration->status === 'pending'): ?> bg-warning text-dark
                        <?php elseif($registration->status === 'approved'): ?> bg-success
                        <?php elseif($registration->status === 'rejected'): ?> bg-danger
                        <?php else: ?> bg-secondary <?php endif; ?>">
                        <?php echo e(ucfirst($registration->status)); ?>

                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="bi bi-person"></i> Personal Information</h6>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Full Name:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->firstname); ?> <?php echo e($registration->middlename); ?> <?php echo e($registration->lastname); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Email:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->user->email ?? 'N/A'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Contact:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->contact_number ?: 'N/A'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Address:</div>
                                <div class="col-sm-8 field-value">
                                    <?php echo e($registration->street_address); ?>, <?php echo e($registration->city); ?>, <?php echo e($registration->state_province); ?> <?php echo e($registration->zipcode); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="bi bi-book"></i> Program Information</h6>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Program:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->program_name ?: 'N/A'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Package:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->package_name ?: 'N/A'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Plan:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->plan_name ?: 'N/A'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Learning Mode:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->learning_mode ?: 'N/A'); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 field-label">Start Date:</div>
                                <div class="col-sm-8 field-value"><?php echo e($registration->Start_Date ? \Carbon\Carbon::parse($registration->Start_Date)->format('M d, Y') : 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <?php if($registration->photo_2x2 || $registration->good_moral || $registration->PSA || $registration->Course_Cert || $registration->TOR): ?>
            <div class="card detail-card mb-4">
                <div class="card-header detail-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Submitted Documents</h5>
                </div>
                <div class="card-body">
                    <div class="documents-section">
                        <div class="row">
                            <?php if($registration->photo_2x2): ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-camera text-primary me-2"></i>
                                    <div>
                                        <div class="field-label">2x2 Photo</div>
                                        <a href="<?php echo e(asset('storage/' . $registration->photo_2x2)); ?>" target="_blank" class="text-decoration-none">
                                            <small>View Document</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($registration->good_moral): ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-check text-success me-2"></i>
                                    <div>
                                        <div class="field-label">Good Moral</div>
                                        <a href="<?php echo e(asset('storage/' . $registration->good_moral)); ?>" target="_blank" class="text-decoration-none">
                                            <small>View Document</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($registration->PSA): ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-person text-info me-2"></i>
                                    <div>
                                        <div class="field-label">PSA Birth Certificate</div>
                                        <a href="<?php echo e(asset('storage/' . $registration->PSA)); ?>" target="_blank" class="text-decoration-none">
                                            <small>View Document</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($registration->TOR): ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text text-warning me-2"></i>
                                    <div>
                                        <div class="field-label">Transcript of Records</div>
                                        <a href="<?php echo e(asset('storage/' . $registration->TOR)); ?>" target="_blank" class="text-decoration-none">
                                            <small>View Document</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($registration->Course_Cert): ?>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-award text-primary me-2"></i>
                                    <div>
                                        <div class="field-label">Course Certificate</div>
                                        <a href="<?php echo e(asset('storage/' . $registration->Course_Cert)); ?>" target="_blank" class="text-decoration-none">
                                            <small>View Document</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <?php if($registration->status === 'pending'): ?>
            <div class="card detail-card mb-4">
                <div class="card-header detail-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="approveRegistration(<?php echo e($registration->registration_id); ?>)">
                            <i class="bi bi-check-circle"></i> Approve Registration
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectRegistration(<?php echo e($registration->registration_id); ?>)">
                            <i class="bi bi-x-circle"></i> Reject Registration
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <?php if($registration->selected_modules || $registration->selected_courses): ?>
            <div class="card detail-card mb-4">
                <div class="card-header detail-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Selected Items</h5>
                </div>
                <div class="card-body">
                    <?php if($registration->selected_modules): ?>
                    <h6 class="text-secondary">Selected Modules:</h6>
                    <div class="mb-3">
                        <?php
                            $modules = is_string($registration->selected_modules) ? json_decode($registration->selected_modules, true) : $registration->selected_modules;
                        ?>
                        <?php if(is_array($modules)): ?>
                            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-primary me-1"><?php echo e(is_array($module) ? ($module['name'] ?? 'Unknown') : $module); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="text-muted">No modules selected</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if($registration->selected_courses): ?>
                    <h6 class="text-secondary">Selected Courses:</h6>
                    <div>
                        <?php
                            $courses = is_string($registration->selected_courses) ? json_decode($registration->selected_courses, true) : $registration->selected_courses;
                        ?>
                        <?php if(is_array($courses)): ?>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-info me-1"><?php echo e(is_array($course) ? ($course['name'] ?? 'Unknown') : $course); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="text-muted">No courses selected</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include the admin functions JavaScript -->
<script src="<?php echo e(asset('js/admin/admin-functions.js')); ?>?v=<?php echo e(time()); ?>"></script>

<script>
    // Override the functions to work with the single registration view
    function approveRegistration(registrationId) {
        if (confirm('Are you sure you want to approve this registration?')) {
            fetch(`/admin/registration/${registrationId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration approved successfully!');
                    window.location.href = '<?php echo e(route("admin.student.registration.pending")); ?>';
                } else {
                    alert('Error: ' + (data.message || 'Failed to approve registration'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error approving registration');
            });
        }
    }

    function rejectRegistration(registrationId) {
        const reason = prompt('Please enter rejection reason:');
        if (reason) {
            fetch(`/admin/registration/${registrationId}/reject-with-reason`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ rejection_reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration rejected successfully!');
                    window.location.href = '<?php echo e(route("admin.student.registration.pending")); ?>';
                } else {
                    alert('Error: ' + (data.message || 'Failed to reject registration'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting registration');
            });
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\registrations\show.blade.php ENDPATH**/ ?>