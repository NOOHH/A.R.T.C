

<?php $__env->startSection('title', 'Archived Professors'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Archived Professors</h2>
                    <p class="text-muted">Manage archived professors</p>
                </div>
                <div>
                    <?php if(isset($isPreview) && $isPreview && isset($previewTenant)): ?>
                        <a href="/t/draft/<?php echo e($previewTenant); ?>/admin/professors?website=<?php echo e(request('website')); ?>" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Active Professors
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.professors.index')); ?>" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Active Professors
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Archived Professors Table -->
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Archived Professors</h5>
                </div>
                <div class="card-body p-0">
                    <?php if($professors->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Professor</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Assigned Programs</th>
                                        <th>Archived Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $professors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $professor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?php echo e($professor->full_name); ?></div>
                                                    <small class="text-muted"><?php echo e($professor->email); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e($professor->email); ?></td>
                                        <td>N/A</td>
                                        <td>
                                            <?php if($professor->programs->count() > 0): ?>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php $__currentLoopData = $professor->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge bg-secondary"><?php echo e($program->program_name); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No assignments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($isPreview) && $isPreview && is_string($professor->updated_at)): ?>
                                                <?php echo e($professor->updated_at); ?>

                                            <?php else: ?>
                                                <?php echo e($professor->updated_at->format('M d, Y')); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                                                        data-professor-id="<?php echo e($professor->id); ?>"
                                                        data-professor-name="<?php echo e($professor->full_name); ?>">
                                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-professor-id="<?php echo e($professor->id); ?>"
                                                        data-professor-name="<?php echo e($professor->full_name); ?>">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if($professors->hasPages()): ?>
                            <div class="p-3">
                                <?php echo e($professors->links()); ?>

                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-archive display-1 text-muted"></i>
                            <h5 class="mt-3">No Archived Professors</h5>
                            <p class="text-muted">No professors have been archived yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore <strong id="restoreProfessorName"></strong>?</p>
                <p class="text-muted">This professor will be moved back to the active professors list.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="restoreForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-success">Restore</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete <strong id="deleteProfessorName"></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restore modal
    $('#restoreModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('restoreProfessorName').textContent = professorName;
        document.getElementById('restoreForm').action = `/admin/professors/${professorId}/restore`;
    });

    // Delete modal
    $('#deleteModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('deleteProfessorName').textContent = professorName;
        document.getElementById('deleteForm').action = `/admin/professors/${professorId}`;
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/professors/archived.blade.php ENDPATH**/ ?>