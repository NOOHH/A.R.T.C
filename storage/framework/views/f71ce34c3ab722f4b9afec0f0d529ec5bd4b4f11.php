

<?php $__env->startSection('title', 'Grading Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-clipboard-check"></i> Grading Management</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="bi bi-plus-circle"></i> Add Grade
                </button>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('professor.grading')); ?>" class="row g-3">
                        <div class="col-md-8">
                            <label for="program_id" class="form-label">Select Program</label>
                            <select name="program_id" id="program_id" class="form-select" required>
                                <option value="">Choose a program...</option>
                                <?php $__currentLoopData = $assignedPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($program->program_id); ?>" 
                                            <?php echo e($selectedProgramId == $program->program_id ? 'selected' : ''); ?>>
                                        <?php echo e($program->program_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Load Students</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($selectedProgramId && $students->count() > 0): ?>
                
                
                <?php if($programAnalytics): ?>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?php echo e($programAnalytics['total_students']); ?></h4>
                                        <p class="mb-0">Total Students</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?php echo e(number_format($programAnalytics['average_grade'], 1)); ?>%</h4>
                                        <p class="mb-0">Avg Grade</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?php echo e(number_format($programAnalytics['average_quiz_score'], 1)); ?>%</h4>
                                        <p class="mb-0">Avg Quiz Score</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?php echo e(number_format($programAnalytics['completion_rate'], 1)); ?>%</h4>
                                        <p class="mb-0">Completion Rate</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Grade Distribution</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gradeDistributionChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary" onclick="autoGradeQuizzes()">
                                        <i class="bi bi-robot"></i> Auto-Grade Quiz Submissions
                                    </button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success" onclick="exportGrades('csv')">
                                            <i class="bi bi-file-earmark-excel"></i> Export CSV
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="exportGrades('pdf')">
                                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#performanceModal">
                                        <i class="bi bi-bar-chart"></i> View Performance Analytics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Students and Grades -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Students and Grades</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Student ID</th>
                                        <th>Grades</th>
                                        <th>Average</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $studentGrades = $grades->get($student->student_id, collect());
                                            $average = $studentGrades->count() > 0 ? $studentGrades->avg('grade') : 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($student->full_name); ?></strong><br>
                                                <small class="text-muted"><?php echo e($student->email); ?></small>
                                            </td>
                                            <td><?php echo e($student->student_id); ?></td>
                                            <td>
                                                <?php if($studentGrades->count() > 0): ?>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php $__currentLoopData = $studentGrades->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="badge bg-secondary" title="<?php echo e($grade->assignment_name); ?>">
                                                                <?php echo e($grade->grade); ?>/<?php echo e($grade->max_points); ?>

                                                            </span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($studentGrades->count() > 3): ?>
                                                            <span class="badge bg-light text-dark">
                                                                +<?php echo e($studentGrades->count() - 3); ?> more
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">No grades yet</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($average > 0): ?>
                                                    <strong class="
                                                        <?php if($average >= 90): ?> text-success
                                                        <?php elseif($average >= 80): ?> text-info
                                                        <?php elseif($average >= 70): ?> text-warning
                                                        <?php else: ?> text-danger
                                                        <?php endif; ?>
                                                    "><?php echo e(number_format($average, 1)); ?>%</strong>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-primary btn-sm" 
                                                            onclick="addGrade(<?php echo e($student->student_id); ?>, '<?php echo e($student->full_name); ?>')">
                                                        <i class="bi bi-plus"></i> Add Grade
                                                    </button>
                                                    <a href="<?php echo e(route('professor.grading.student', ['student' => $student->student_id, 'program_id' => $selectedProgramId])); ?>" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif($selectedProgramId): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Students Found</h5>
                        <p class="text-muted">No students are enrolled in the selected program.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-award text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Select a Program</h5>
                        <p class="text-muted">Please select a program to manage student grades.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('professor.grading')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="program_id" value="<?php echo e($selectedProgramId); ?>">
                    <input type="hidden" name="student_id" id="modal_student_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" id="modal_student_name" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignment_name" class="form-label">Assignment Name</label>
                        <input type="text" class="form-control" id="assignment_name" name="assignment_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="grade" class="form-label">Grade</label>
                            <input type="number" class="form-control" id="grade" name="grade" 
                                   min="0" max="100" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="max_points" class="form-label">Max Points</label>
                            <input type="number" class="form-control" id="max_points" name="max_points" 
                                   min="1" step="0.1" value="100" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label for="feedback" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if($selectedProgramId && $programAnalytics): ?>
<div class="modal fade" id="performanceModal" tabindex="-1" aria-labelledby="performanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="performanceModalLabel">
                    <i class="bi bi-bar-chart"></i> Performance Analytics
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Quiz Performance Overview</h6>
                        <?php if(!empty($programAnalytics['quiz_performance'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Submissions</th>
                                            <th>Avg Score</th>
                                            <th>Completion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $programAnalytics['quiz_performance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e(substr($quiz['quiz_title'], 0, 30)); ?>...</td>
                                                <td><?php echo e($quiz['total_submissions']); ?></td>
                                                <td><?php echo e(number_format($quiz['average_score'], 1)); ?>%</td>
                                                <td><?php echo e(number_format($quiz['completion_rate'], 1)); ?>%</td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No quiz data available yet.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6>Students Needing Attention</h6>
                        <?php if($programAnalytics['low_performers']->isNotEmpty()): ?>
                            <div class="list-group">
                                <?php $__currentLoopData = $programAnalytics['low_performers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $performer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo e($performer['student']->user->user_firstname ?? 'N/A'); ?> <?php echo e($performer['student']->user->user_lastname ?? ''); ?></strong><br>
                                            <small class="text-muted">ID: <?php echo e($performer['student']->student_id); ?></small>
                                        </div>
                                        <span class="badge bg-danger"><?php echo e(number_format($performer['average'], 1)); ?>%</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">All students are performing well!</p>
                        <?php endif; ?>
                        
                        <h6 class="mt-4">Top Performers</h6>
                        <?php if($programAnalytics['top_performers']->isNotEmpty()): ?>
                            <div class="list-group">
                                <?php $__currentLoopData = $programAnalytics['top_performers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $performer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo e($performer['student']->user->user_firstname ?? 'N/A'); ?> <?php echo e($performer['student']->user->user_lastname ?? ''); ?></strong><br>
                                            <small class="text-muted">ID: <?php echo e($performer['student']->student_id); ?></small>
                                        </div>
                                        <span class="badge bg-success"><?php echo e(number_format($performer['average'], 1)); ?>%</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No top performers identified yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php $__env->stopPush(); ?>

<script>
function addGrade(studentId, studentName) {
    document.getElementById('modal_student_id').value = studentId;
    document.getElementById('modal_student_name').value = studentName;
    document.getElementById('assignment_name').value = '';
    document.getElementById('grade').value = '';
    document.getElementById('max_points').value = '100';
    document.getElementById('feedback').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('addGradeModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when program changes
    const programSelect = document.getElementById('program_id');
    
    programSelect.addEventListener('change', function() {
        if (this.value) {
            this.closest('form').submit();
        }
    });
    
    <?php if($selectedProgramId && $programAnalytics): ?>
    // Grade Distribution Chart
    const ctx = document.getElementById('gradeDistributionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (60-69)', 'F (<60)'],
                datasets: [{
                    label: 'Number of Students',
                    data: [
                        <?php echo e($programAnalytics['grade_distribution']['A']); ?>,
                        <?php echo e($programAnalytics['grade_distribution']['B']); ?>,
                        <?php echo e($programAnalytics['grade_distribution']['C']); ?>,
                        <?php echo e($programAnalytics['grade_distribution']['D']); ?>,
                        <?php echo e($programAnalytics['grade_distribution']['F']); ?>

                    ],
                    backgroundColor: [
                        '#28a745',
                        '#17a2b8',
                        '#ffc107',
                        '#fd7e14',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});

function autoGradeQuizzes() {
    if (confirm('This will automatically create grade entries for all ungraded quiz submissions. Continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("professor.grading.auto-grade-quizzes")); ?>';
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(token);
        
        const programId = document.createElement('input');
        programId.type = 'hidden';
        programId.name = 'program_id';
        programId.value = '<?php echo e($selectedProgramId); ?>';
        form.appendChild(programId);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function exportGrades(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("professor.grading.export")); ?>';
    
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(token);
    
    const programId = document.createElement('input');
    programId.type = 'hidden';
    programId.name = 'program_id';
    programId.value = '<?php echo e($selectedProgramId); ?>';
    form.appendChild(programId);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Existing function - keeping for compatibility
function addGrade(studentId, studentName) {
    // Your existing addGrade implementation
    console.log('Add grade for student:', studentId, studentName);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/professor/grading/index.blade.php ENDPATH**/ ?>