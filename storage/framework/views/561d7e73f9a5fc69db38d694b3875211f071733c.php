

<?php $__env->startSection('title', 'Attendance Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Attendance Reports</h2>
                <a href="<?php echo e(route('professor.attendance')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-check"></i> Record Attendance
                </a>
            </div>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('professor.attendance.reports')); ?>" class="row g-3">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo e($startDate); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?php echo e($endDate); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($selectedProgramId && count($attendanceReport) > 0): ?>
                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <?php
                        $totalStudents = count($attendanceReport);
                        $avgAttendance = collect($attendanceReport)->avg('attendance_percentage');
                        $excellentAttendance = collect($attendanceReport)->where('attendance_percentage', '>=', 90)->count();
                        $poorAttendance = collect($attendanceReport)->where('attendance_percentage', '<', 70)->count();
                    ?>
                    
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-primary"><?php echo e($totalStudents); ?></h3>
                                <p class="mb-0">Total Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-success"><?php echo e(number_format($avgAttendance, 1)); ?>%</h3>
                                <p class="mb-0">Average Attendance</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-info"><?php echo e($excellentAttendance); ?></h3>
                                <p class="mb-0">Excellent (≥90%)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-warning"><?php echo e($poorAttendance); ?></h3>
                                <p class="mb-0">Poor (<70%)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Report -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Attendance Report (<?php echo e($startDate); ?> to <?php echo e($endDate); ?>)</h5>
                        <button class="btn btn-outline-secondary btn-sm" onclick="printReport()">
                            <i class="bi bi-printer"></i> Print Report
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Student ID</th>
                                        <th>Total Days</th>
                                        <th>Present</th>
                                        <th>Late</th>
                                        <th>Absent</th>
                                        <th>Attendance %</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $attendanceReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($report['student']->full_name); ?></strong><br>
                                                <small class="text-muted"><?php echo e($report['student']->email); ?></small>
                                            </td>
                                            <td><?php echo e($report['student']->student_id); ?></td>
                                            <td><?php echo e($report['total_days']); ?></td>
                                            <td>
                                                <span class="badge bg-success"><?php echo e($report['present_days']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning"><?php echo e($report['late_days']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo e($report['absent_days']); ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo e($report['attendance_percentage']); ?>%</strong>
                                            </td>
                                            <td>
                                                <?php if($report['attendance_percentage'] >= 90): ?>
                                                    <span class="badge bg-success">Excellent</span>
                                                <?php elseif($report['attendance_percentage'] >= 80): ?>
                                                    <span class="badge bg-info">Good</span>
                                                <?php elseif($report['attendance_percentage'] >= 70): ?>
                                                    <span class="badge bg-warning">Average</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Poor</span>
                                                <?php endif; ?>
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
                        <i class="bi bi-graph-up text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Attendance Data</h5>
                        <p class="text-muted">No attendance records found for the selected period.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-graph-up text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Select Program and Date Range</h5>
                        <p class="text-muted">Please select a program and date range to view attendance reports.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function printReport() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when program changes
    const programSelect = document.getElementById('program_id');
    
    programSelect.addEventListener('change', function() {
        if (this.value) {
            this.closest('form').submit();
        }
    });
});
</script>

<style>
@media print {
    .btn, .card-header button {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\attendance\reports.blade.php ENDPATH**/ ?>