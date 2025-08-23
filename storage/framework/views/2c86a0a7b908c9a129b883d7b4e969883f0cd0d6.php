

<?php $__env->startSection('title', 'Archived Students'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive"></i> Archived Students</h2>
                <?php if(isset($isPreview) && $isPreview && isset($previewTenant)): ?>
                    <a href="/t/draft/<?php echo e($previewTenant); ?>/admin/students?website=<?php echo e(request('website')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Active Students
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Active Students
                    </a>
                <?php endif; ?>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if(isset($isPreview) && $isPreview && isset($previewTenant)): ?>
                        <form method="GET" action="/t/draft/<?php echo e($previewTenant); ?>/admin/students/archived">
                            <input type="hidden" name="website" value="<?php echo e(request('website')); ?>">
                    <?php else: ?>
                        <form method="GET" action="<?php echo e(route('admin.students.archived')); ?>">
                    <?php endif; ?>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="program_id" class="form-label">Filter by Program</label>
                                <select name="program_id" id="program_id" class="form-select">
                                    <option value="">All Programs</option>
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($program->program_id); ?>" 
                                                <?php echo e(request('program_id') == $program->program_id ? 'selected' : ''); ?>>
                                            <?php echo e($program->program_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Search by name, ID, or email..." value="<?php echo e(request('search')); ?>">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    <?php if(isset($isPreview) && $isPreview && isset($previewTenant)): ?>
                                        <a href="/t/draft/<?php echo e($previewTenant); ?>/admin/students/archived?website=<?php echo e(request('website')); ?>" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('admin.students.archived')); ?>" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <?php if($students->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Status</th>
                                        <th>Archived Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($student->student_id); ?></td>
                                            <td><?php echo e($student->student_first_name); ?> <?php echo e($student->student_last_name); ?></td>
                                            <td><?php echo e($student->student_email); ?></td>
                                            <td>
                                                <?php if($student->programs && $student->programs->count() > 0): ?>
                                                    <?php echo e($student->programs->first()->program_name); ?>

                                                    <?php if($student->programs->count() > 1): ?>
                                                        <small class="text-muted">(+<?php echo e($student->programs->count() - 1); ?> more)</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No Program</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($student->date_approved): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(isset($isPreview) && $isPreview && is_string($student->updated_at)): ?>
                                                    <?php echo e($student->updated_at); ?>

                                                <?php else: ?>
                                                    <?php echo e($student->updated_at->format('M d, Y')); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <?php if(isset($isPreview) && $isPreview): ?>
                                                        <button type="button" onclick="alert('Preview mode - View not available')"
                                                                class="btn btn-sm btn-outline-info" title="View (Preview)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button type="button" onclick="alert('Preview mode - Restore not available')"
                                                                class="btn btn-sm btn-outline-success" title="Restore (Preview)">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                        <button type="button" onclick="alert('Preview mode - Delete not available')"
                                                                class="btn btn-sm btn-outline-danger" title="Delete (Preview)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('admin.students.show', $student)); ?>" 
                                                           class="btn btn-sm btn-outline-info" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form method="POST" action="<?php echo e(route('admin.students.restore', $student)); ?>" 
                                                              style="display: inline;" onsubmit="return confirm('Restore this student?')">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Student">
                                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                title="Delete Permanently" onclick="deleteStudent(<?php echo e($student->student_id); ?>)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($students->appends(request()->query())->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Archived Students</h4>
                            <p class="text-muted">
                                <?php if(request()->hasAny(['program_id', 'search'])): ?>
                                    No archived students match your current filters.
                                <?php else: ?>
                                    There are no archived students at the moment.
                                <?php endif; ?>
                            </p>
                            <?php if(isset($isPreview) && $isPreview && isset($previewTenant)): ?>
                                <a href="/t/draft/<?php echo e($previewTenant); ?>/admin/students?website=<?php echo e(request('website')); ?>" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Active Students
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Active Students
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this student? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteStudentForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteStudent(studentId) {
    const form = document.getElementById('deleteStudentForm');
    form.action = `/admin/students/${studentId}/force-delete`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/students/archived.blade.php ENDPATH**/ ?>