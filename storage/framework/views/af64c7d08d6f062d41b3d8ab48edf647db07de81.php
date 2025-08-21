

<?php $__env->startSection('title', 'Batch Enrollment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="batch-enrollment-container">
    <div class="header">
        <h2>Batch Enrollment Management</h2>
        <button onclick="showCreateBatchModal()" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Batch
        </button>
    </div>

    <div class="program-filter">
        <label for="programFilter">Filter by Program:</label>
        <select id="programFilter" onchange="filterBatches()">
            <option value="">All Programs</option>
            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($program->id); ?>"><?php echo e($program->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="batch-grid">
        <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="batch-card" data-program="<?php echo e($batch->program_id); ?>">
            <div class="batch-header">
                <h3><?php echo e($batch->batch_name); ?></h3>
                <span class="status-badge <?php echo e($batch->batch_status); ?>">
                    <?php echo e(ucfirst($batch->batch_status)); ?>

                </span>
            </div>
            <div class="batch-info">
                <p><strong>Program:</strong> <?php echo e($batch->program_name); ?></p>
                <p><strong>Students:</strong> <?php echo e($batch->current_students); ?>/<?php echo e($batch->max_students); ?></p>
                <p><strong>Enrollment Deadline:</strong> <?php echo e($batch->enrollment_deadline); ?></p>
                <p><strong>Start Date:</strong> <?php echo e($batch->start_date); ?></p>
            </div>
            <div class="batch-actions">
                <button onclick="editBatch(<?php echo e($batch->id); ?>)" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button onclick="toggleBatchStatus(<?php echo e($batch->id); ?>)" class="btn btn-sm btn-<?php echo e($batch->batch_status === 'closed' ? 'success' : 'warning'); ?>">
                    <?php echo e($batch->batch_status === 'closed' ? 'Reopen' : 'Close'); ?>

                </button>
                <button onclick="viewStudents(<?php echo e($batch->id); ?>)" class="btn btn-sm btn-info">
                    <i class="bi bi-people"></i> View Students
                </button>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<!-- Create/Edit Batch Modal -->
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Batch</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="batchForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="batch_id" id="batchId">
                    
                    <div class="form-group">
                        <label for="batchName">Batch Name</label>
                        <input type="text" class="form-control" id="batchName" name="batch_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="programId">Program</label>
                        <select class="form-control" id="programId" name="program_id" required>
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($program->id); ?>"><?php echo e($program->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="maxStudents">Maximum Students</label>
                        <input type="number" class="form-control" id="maxStudents" name="max_students" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollmentDeadline">Enrollment Deadline</label>
                        <input type="datetime-local" class="form-control" id="enrollmentDeadline" name="enrollment_deadline" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="datetime-local" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="datetime-local" class="form-control" id="endDate" name="end_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveBatch()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View Students Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batch Students</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="studentsList"></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.batch-enrollment-container {
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.program-filter {
    margin-bottom: 20px;
}

.batch-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.batch-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.batch-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
}

.status-badge.available {
    background-color: #28a745;
    color: white;
}

.status-badge.ongoing {
    background-color: #ffc107;
    color: black;
}

.status-badge.closed {
    background-color: #dc3545;
    color: white;
}

.batch-info {
    margin-bottom: 15px;
}

.batch-actions {
    display: flex;
    gap: 10px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function showCreateBatchModal() {
    $('#batchId').val('');
    $('#batchForm')[0].reset();
    $('#batchModal').modal('show');
}

function editBatch(batchId) {
    fetch(`/admin/batches/${batchId}`)
        .then(response => response.json())
        .then(data => {
            $('#batchId').val(data.id);
            $('#batchName').val(data.batch_name);
            $('#programId').val(data.program_id);
            $('#maxStudents').val(data.max_students);
            $('#enrollmentDeadline').val(data.enrollment_deadline);
            $('#startDate').val(data.start_date);
            $('#endDate').val(data.end_date);
            $('#batchModal').modal('show');
        });
}

function saveBatch() {
    const formData = new FormData($('#batchForm')[0]);
    const batchId = $('#batchId').val();
    const url = batchId ? `/admin/batches/${batchId}` : '/admin/batches';
    const method = batchId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#batchModal').modal('hide');
            location.reload();
        } else {
            alert('Error saving batch: ' + data.message);
        }
    });
}

function toggleBatchStatus(batchId) {
    if (!confirm('Are you sure you want to change this batch\'s status?')) return;

    fetch(`/admin/batches/${batchId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating batch status: ' + data.message);
        }
    });
}

function viewStudents(batchId) {
    fetch(`/admin/batches/${batchId}/students`)
        .then(response => response.json())
        .then(data => {
            const studentsList = document.getElementById('studentsList');
            studentsList.innerHTML = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Enrollment Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(student => `
                            <tr>
                                <td>${student.firstname} ${student.lastname}</td>
                                <td>${student.email}</td>
                                <td>${new Date(student.enrollment_date).toLocaleDateString()}</td>
                                <td>${student.status}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            $('#studentsModal').modal('show');
        });
}

function filterBatches() {
    const programId = document.getElementById('programFilter').value;
    const batchCards = document.querySelectorAll('.batch-card');
    
    batchCards.forEach(card => {
        if (!programId || card.dataset.program === programId) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-student-enrollment\batch-enrollment.blade.php ENDPATH**/ ?>