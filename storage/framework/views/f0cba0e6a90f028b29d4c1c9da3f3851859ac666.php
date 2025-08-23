

<?php $__env->startSection('title', 'My Meetings'); ?>

<?php $__env->startSection('content'); ?>
<style>
.blink {
    animation: blink-animation 1s steps(5, start) infinite;
}

@keyframes blink-animation {
    to {
        visibility: hidden;
    }
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="bi bi-camera-video me-2"></i>My Meetings
                    </h2>
                    <p class="text-muted mb-0">View your upcoming class meetings and join sessions</p>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Current/Live Meetings -->
            <?php if($currentMeetings->count() > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-broadcast me-2"></i>Live Meetings
                                <span class="badge bg-light text-danger ms-2"><?php echo e($currentMeetings->count()); ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php $__currentLoopData = $currentMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                    ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start bg-danger-subtle">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold"><?php echo e($meeting->title); ?></div>
                                            <p class="mb-1"><?php echo e($meeting->description ?? 'No description provided'); ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-mortarboard me-1"></i><?php echo e($meeting->batch->program->program_name ?? 'N/A'); ?>

                                                •
                                                <i class="bi bi-people me-1"></i><?php echo e($meeting->batch->batch_name ?? 'N/A'); ?>

                                                •
                                                <i class="bi bi-person-workspace me-1"></i><?php echo e($meeting->professor->professor_name ?? 'N/A'); ?>

                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="mb-2">
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-dot text-white blink"></i>LIVE NOW
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <strong><?php echo e($meetingDate->format('h:i A')); ?></strong>
                                                <br>
                                                <small class="text-muted">Started <?php echo e($meetingDate->diffForHumans()); ?></small>
                                            </div>
                                            <?php if($meeting->meeting_url && $meeting->actual_start_time): ?>
                                                <a href="<?php echo e($meeting->meeting_url); ?>" 
                                                   target="_blank" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="logMeetingAccess(<?php echo e($meeting->meeting_id); ?>)">
                                                    <i class="bi bi-camera-video me-1"></i>Join Now
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">No link available</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Today's Meetings -->
            <?php if($todaysMeetings->count() > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-day me-2"></i>Today's Meetings
                                <span class="badge bg-light text-info ms-2"><?php echo e($todaysMeetings->count()); ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php $__currentLoopData = $todaysMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                    ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold"><?php echo e($meeting->title); ?></div>
                                            <small class="text-muted"><?php echo e($meetingDate->format('h:i A')); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-muted small">Not started</span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upcoming Meetings -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-week me-2"></i>Upcoming Meetings
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($upcomingMeetings->count() > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php $__currentLoopData = $upcomingMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                            $isToday = $meetingDate->isToday();
                                            $isTomorrow = $meetingDate->isTomorrow();
                                        ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><?php echo e($meeting->title); ?></div>
                                                <p class="mb-1"><?php echo e($meeting->description ?? 'No description provided'); ?></p>
                                                <small class="text-muted">
                                                    <i class="bi bi-mortarboard me-1"></i><?php echo e($meeting->batch->program->program_name ?? 'N/A'); ?>

                                                    •
                                                    <i class="bi bi-people me-1"></i><?php echo e($meeting->batch->batch_name ?? 'N/A'); ?>

                                                    •
                                                    <i class="bi bi-person-workspace me-1"></i><?php echo e($meeting->professor->professor_name ?? 'N/A'); ?>

                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <div class="mb-2">
                                                    <?php if($isToday): ?>
                                                        <span class="badge bg-success">Today</span>
                                                    <?php elseif($isTomorrow): ?>
                                                        <span class="badge bg-warning">Tomorrow</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary"><?php echo e($meetingDate->format('M d')); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mb-2">
                                                    <strong><?php echo e($meetingDate->format('h:i A')); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo e($meetingDate->diffForHumans()); ?></small>
                                                </div>
                                                <span class="text-muted small">Not started yet</span>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No Upcoming Meetings</h5>
                                    <p class="text-muted">You don't have any meetings scheduled yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Finished Meetings -->
                    <?php if(isset($finishedMeetings) && $finishedMeetings->count() > 0): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-check2-circle me-2"></i>Finished Meetings & Attendance
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php $__currentLoopData = $finishedMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                                        $attendanceLog = $meeting->attendanceLogs->where('student_id', $studentId)->first();
                                        $attendanceStatus = $attendanceLog ? $attendanceLog->attendance_status : 'absent';
                                        $statusLabel = [
                                            'present' => ['label' => 'Present', 'class' => 'success'],
                                            'late' => ['label' => 'Late', 'class' => 'warning'],
                                            'absent' => ['label' => 'Absent', 'class' => 'danger'],
                                            'excused' => ['label' => 'Excused', 'class' => 'info'],
                                            'joined' => ['label' => 'Joined', 'class' => 'primary'],
                                        ][$attendanceStatus] ?? ['label' => ucfirst($attendanceStatus), 'class' => 'secondary'];
                                    ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold"><?php echo e($meeting->title); ?></div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i><?php echo e($meetingDate->format('M d, Y h:i A')); ?>

                                                •
                                                <i class="bi bi-mortarboard me-1"></i><?php echo e($meeting->batch->program->program_name ?? 'N/A'); ?>

                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo e($statusLabel['class']); ?>">
                                                <i class="bi bi-person-check me-1"></i><?php echo e($statusLabel['label']); ?>

                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Today's Schedule -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-calendar-day me-2"></i>Today's Schedule
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if($todaysMeetings->count() > 0): ?>
                                <?php $__currentLoopData = $todaysMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date); ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                                        <div>
                                            <div class="fw-bold small"><?php echo e($meeting->title); ?></div>
                                            <small class="text-muted"><?php echo e($meetingDate->format('h:i A')); ?></small>
                                        </div>
                                        <?php if($meeting->meeting_url): ?>
                                            <a href="<?php echo e($meeting->meeting_url); ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-primary btn-sm"
                                               onclick="logMeetingAccess(<?php echo e($meeting->meeting_id); ?>)">
                                                Join
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">No meetings scheduled for today</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Meeting Statistics -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-bar-chart me-2"></i>My Attendance
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                                $totalMeetings = $upcomingMeetings->count() + $pastMeetings->count();
                                $attendedMeetings = $pastMeetings->filter(function($meeting) use ($studentId) {
                                    return $meeting->attendanceLogs->where('student_id', $studentId)->isNotEmpty();
                                })->count();
                                // Fix division by zero error
                                $attendanceRate = $pastMeetings->count() > 0 ? ($attendedMeetings / $pastMeetings->count()) * 100 : 0;
                            ?>
                            
                            <div class="text-center">
                                <div class="display-6 fw-bold text-primary"><?php echo e(number_format($attendanceRate, 1)); ?>%</div>
                                <p class="text-muted mb-0">Attendance Rate</p>
                            </div>
                            
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo e($attendanceRate); ?>%"></div>
                            </div>
                            
                            <div class="row text-center mt-3">
                                <div class="col-6">
                                    <div class="h6 text-success"><?php echo e($attendedMeetings); ?></div>
                                    <small class="text-muted">Attended</small>
                                </div>
                                <div class="col-6">
                                    <div class="h6 text-danger"><?php echo e($pastMeetings->count() - $attendedMeetings); ?></div>
                                    <small class="text-muted">Missed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('student.calendar')); ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-calendar3 me-2"></i>View Calendar
                                </a>
                                <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-house me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function logMeetingAccess(meetingId) {
    // Log that the student accessed the meeting link
    fetch(`<?php echo e(route('student.meetings.access', ['id' => ':meetingId'])); ?>`.replace(':meetingId', meetingId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            access_time: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Meeting access logged successfully');
        }
    })
    .catch(error => {
        console.error('Error logging meeting access:', error);
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.student-dashboard.student-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/student/student-meetings/meetings.blade.php ENDPATH**/ ?>