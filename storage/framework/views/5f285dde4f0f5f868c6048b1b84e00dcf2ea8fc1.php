
<form id="assignmentSubmissionForm" enctype="multipart/form-data" class="mt-3">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="content_id" value="<?php echo e($content->id); ?>">
    <input type="hidden" name="course_id" value="<?php echo e($content->course_id); ?>">
    <input type="hidden" name="module_id" value="<?php echo e($content->course->module_id ?? ''); ?>">
    <?php if(isset($draft)): ?>
        <input type="hidden" name="submission_id" value="<?php echo e($draft->id); ?>">
    <?php endif; ?>
    
    <div class="mb-3">
        <label for="submissionFiles" class="form-label">
            Upload Files
            <?php if(isset($draft)): ?>
                <small class="text-muted">(Leave empty to keep existing files)</small>
            <?php endif; ?>
        </label>
        <input type="file" class="form-control" id="submissionFiles" name="files[]" multiple 
               <?php if(!isset($draft)): ?> required <?php endif; ?>>
        <small class="form-text text-muted">
            <?php if($content->allowed_file_types): ?>
                Allowed types: <?php echo e($content->allowed_file_types); ?>

            <?php else: ?>
                Accepted formats: PDF, DOC, DOCX, ZIP, Images, Videos
            <?php endif; ?>
            <?php if($content->max_file_size): ?>
                (Max: <?php echo e($content->max_file_size); ?>MB each)
            <?php else: ?>
                (Max: 10MB each)
            <?php endif; ?>
        </small>
        
        <?php if(isset($draft) && !empty($draft->files)): ?>
            <div class="mt-2">
                <small class="text-info">
                    <strong>Current files in draft:</strong>
                    <?php
                        $files = is_string($draft->files) ? json_decode($draft->files, true) : $draft->files;
                        $files = is_array($files) ? $files : [];
                    ?>
                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $filePath = is_array($file) ? ($file['path'] ?? $file) : $file;
                            $fileName = is_array($file) ? ($file['original_filename'] ?? basename($filePath)) : basename($filePath);
                        ?>
                        <span class="badge bg-secondary me-1"><?php echo e($fileName); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </small>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <label for="submissionNotes" class="form-label">Notes (Optional)</label>
        <textarea class="form-control" id="submissionNotes" name="comments" rows="3" 
                  placeholder="Add any additional notes about your submission..."><?php echo e(isset($draft) ? $draft->comments : ''); ?></textarea>
    </div>
    
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">
            <i class="bi bi-save"></i> Save Draft
        </button>
        <?php if(isset($draft)): ?>
            <button type="button" class="btn btn-primary" onclick="submitAssignment()">
                <i class="bi bi-upload"></i> Submit Assignment
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="removeDraft()">
                <i class="bi bi-trash"></i> Remove Draft
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-primary" onclick="submitAssignment()">
                <i class="bi bi-upload"></i> Submit Assignment
            </button>
        <?php endif; ?>
    </div>
</form>

<div id="submissionStatus" class="mt-3"></div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\student\content\partials\assignment-form.blade.php ENDPATH**/ ?>