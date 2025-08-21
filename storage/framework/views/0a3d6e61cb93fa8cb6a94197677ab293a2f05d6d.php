<?php if($professor->programs->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $professor->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 mb-3">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><?php echo e($program->program_name); ?></h6>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('admin.professors.updateVideoLink', ['professor' => $professor->professor_id, 'program' => $program->program_id])); ?>" 
                              method="POST" class="video-form">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="video_link_<?php echo e($program->program_id); ?>" class="form-label">Video Link</label>
                                <input type="url" class="form-control" 
                                       id="video_link_<?php echo e($program->program_id); ?>" 
                                       name="video_link" 
                                       value="<?php echo e($program->pivot->video_link ?? ''); ?>"
                                       placeholder="https://meet.google.com/...">
                            </div>
                            <div class="mb-3">
                                <label for="video_description_<?php echo e($program->program_id); ?>" class="form-label">Description</label>
                                <textarea class="form-control" 
                                          id="video_description_<?php echo e($program->program_id); ?>" 
                                          name="video_description" 
                                          rows="2"
                                          placeholder="Meeting description or instructions"><?php echo e($program->pivot->video_description ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save"></i> Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="text-center py-4">
        <i class="bi bi-play-circle display-4 text-muted"></i>
        <h5 class="mt-3">No Programs Assigned</h5>
        <p class="text-muted">This professor is not assigned to any programs yet.</p>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submissions
    document.querySelectorAll('.video-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Saved!';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-success');
                    
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.classList.remove('btn-success');
                        submitBtn.classList.add('btn-primary');
                        submitBtn.disabled = false;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to save');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-danger');
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.classList.remove('btn-danger');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.disabled = false;
                }, 3000);
            });
        });
    });
});
</script> 
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\professors\partials\video-management.blade.php ENDPATH**/ ?>