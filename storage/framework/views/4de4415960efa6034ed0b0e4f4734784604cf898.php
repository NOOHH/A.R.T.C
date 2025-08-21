

<?php $__env->startSection('title', 'Edit Content'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Content</h4>
                    <a href="<?php echo e(route('professor.modules.index')); ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Modules
                    </a>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('professor.content.update', $content->id)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="content_title" class="form-label">Content Title</label>
                                    <input type="text" class="form-control" id="content_title" name="content_title" 
                                           value="<?php echo e(old('content_title', $content->content_title)); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="content_type" class="form-label">Content Type</label>
                                    <select class="form-control" id="content_type" name="content_type" required>
                                        <option value="PDF" <?php echo e($content->content_type == 'PDF' ? 'selected' : ''); ?>>PDF</option>
                                        <option value="Video" <?php echo e($content->content_type == 'Video' ? 'selected' : ''); ?>>Video</option>
                                        <option value="Document" <?php echo e($content->content_type == 'Document' ? 'selected' : ''); ?>>Document</option>
                                        <option value="Link" <?php echo e($content->content_type == 'Link' ? 'selected' : ''); ?>>Link</option>
                                        <option value="Other" <?php echo e($content->content_type == 'Other' ? 'selected' : ''); ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content_description" class="form-label">Description</label>
                            <textarea class="form-control" id="content_description" name="content_description" 
                                      rows="3"><?php echo e(old('content_description', $content->content_description)); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content_file" class="form-label">Content File</label>
                            <input type="file" class="form-control" id="content_file" name="content_file">
                            <?php if($content->content_path): ?>
                                <small class="text-muted">
                                    Current file: <?php echo e(basename($content->content_path)); ?>

                                </small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3" id="content_url_div" style="<?php echo e($content->content_type == 'Link' ? 'display: block;' : 'display: none;'); ?>">
                            <label for="content_url" class="form-label">Content URL</label>
                            <input type="url" class="form-control" id="content_url" name="content_url" 
                                   value="<?php echo e(old('content_url', $content->content_url)); ?>">
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(route('professor.modules.index')); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Content</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('content_type').addEventListener('change', function() {
    const urlDiv = document.getElementById('content_url_div');
    if (this.value === 'Link') {
        urlDiv.style.display = 'block';
    } else {
        urlDiv.style.display = 'none';
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\modules\edit-content.blade.php ENDPATH**/ ?>