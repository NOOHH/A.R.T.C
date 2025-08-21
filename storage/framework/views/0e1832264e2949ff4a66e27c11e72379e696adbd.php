

<?php $__env->startSection('title', 'Create Assignment'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Create New Assignment
                    </h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('professor.assignments.create')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="program_id" class="form-label">Program</label>
                                <select class="form-select" name="program_id" id="program_id" required>
                                    <option value="">Select Program</option>
                                    <?php $__currentLoopData = $assignedPrograms ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Assignment Title</label>
                                <input type="text" class="form-control" name="title" id="title" required 
                                       placeholder="Enter assignment title">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4" 
                                          placeholder="Describe the assignment..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="instructions" class="form-label">Instructions</label>
                                <textarea class="form-control" name="instructions" id="instructions" rows="6" 
                                          placeholder="Provide detailed instructions for students..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="max_points" class="form-label">Maximum Points</label>
                                <input type="number" class="form-control" name="max_points" id="max_points" 
                                       min="1" max="100" value="100" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="datetime-local" class="form-control" name="due_date" id="due_date" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="assignment_type" class="form-label">Assignment Type</label>
                                <select class="form-select" name="assignment_type" id="assignment_type">
                                    <option value="homework">Homework</option>
                                    <option value="project">Project</option>
                                    <option value="research">Research</option>
                                    <option value="presentation">Presentation</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="attachment" class="form-label">Attachment (Optional)</label>
                                <input type="file" class="form-control" name="attachment" id="attachment" 
                                       accept=".pdf,.doc,.docx,.txt,.zip">
                                <small class="text-muted">Supported formats: PDF, DOC, DOCX, TXT, ZIP</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active Assignment
                                    </label>
                                    <small class="d-block text-muted">Students can view and submit this assignment</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo e(route('professor.grading')); ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Assignment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum due date to current date/time
    const dueDateInput = document.getElementById('due_date');
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    dueDateInput.min = now.toISOString().slice(0, 16);
    
    // Set default due date to next week
    const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
    dueDateInput.value = nextWeek.toISOString().slice(0, 16);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\assignments\create.blade.php ENDPATH**/ ?>