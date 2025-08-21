

<?php $__env->startSection('title', 'Board Exam Passers Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Board Exam Passers Management</h1>
            <p class="mb-0 text-muted">Manage and track board exam results</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPasserModal">
                <i class="fas fa-plus me-2"></i>Add New Entry
            </button>
            <a href="<?php echo e(route('admin.board-passers.download-template')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-download me-2"></i>Download Template
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="totalPassers"><?php echo e($stats['total_passers'] ?? 0); ?></h4>
                            <p class="mb-0">Total Passers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="totalNonPassers"><?php echo e($stats['total_non_passers'] ?? 0); ?></h4>
                            <p class="mb-0">Non-Passers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
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
                            <h4 class="mb-0" id="passRate"><?php echo e($stats['pass_rate'] ?? 0); ?>%</h4>
                            <p class="mb-0">Pass Rate</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h4 class="mb-0" id="lastUpdated"><?php echo e($stats['last_updated'] ?? 'Never'); ?></h4>
                            <p class="mb-0">Last Updated</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filterExam" class="form-label">Board Exam</label>
                    <select class="form-select" id="filterExam">
                        <option value="">All Exams</option>
                        <option value="CPA">CPA</option>
                        <option value="LET">LET</option>
                        <option value="CE">CE</option>
                        <option value="ME">ME</option>
                        <option value="EE">EE</option>
                        <option value="NAPOLCOM">NAPOLCOM</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterYear" class="form-label">Exam Year</label>
                    <select class="form-select" id="filterYear">
                        <option value="">All Years</option>
                        <?php for($year = date('Y'); $year >= 2020; $year--): ?>
                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterResult" class="form-label">Result</label>
                    <select class="form-select" id="filterResult">
                        <option value="">All Results</option>
                        <option value="PASS">PASS</option>
                        <option value="FAIL">FAIL</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterProgram" class="form-label">Program</label>
                    <select class="form-select" id="filterProgram">
                        <option value="">All Programs</option>
                        <option value="Nursing">Nursing</option>
                        <option value="Civil Engineer">Civil Engineer</option>
                        <option value="Mechanical Engineer">Mechanical Engineer</option>
                        <option value="Electrical Engineer">Electrical Engineer</option>
                        <option value="Accountancy">Accountancy</option>
                        <option value="Education">Education</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Board Passers Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Board Exam Passers</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="passersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Program</th>
                            <th>Board Exam</th>
                            <th>Exam Year</th>
                            <th>Exam Date</th>
                            <th>Result</th>
                            <th>Rating</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $passers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $passer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($passer->id); ?></td>
                            <td><?php echo e($passer->student_id); ?></td>
                            <td><?php echo e($passer->student_name); ?></td>
                            <td><?php echo e($passer->program ?? 'Unknown'); ?></td>
                            <td><?php echo e($passer->board_exam); ?></td>
                            <td><?php echo e($passer->exam_year); ?></td>
                            <td><?php echo e($passer->exam_date ? \Carbon\Carbon::parse($passer->exam_date)->format('M d, Y') : 'N/A'); ?></td>
                            <td>
                                <span class="badge <?php echo e($passer->result === 'PASS' ? 'bg-success' : 'bg-danger'); ?>">
                                    <?php echo e($passer->result); ?>

                                </span>
                            </td>
                            <td><?php echo e($passer->rating ? number_format($passer->rating, 2) : 'N/A'); ?></td>
                            <td><?php echo e(Str::limit($passer->notes, 30) ?? 'N/A'); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editPasser(<?php echo e($passer->id); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePasser(<?php echo e($passer->id); ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($passers->hasPages()): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php echo e($passers->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Passer Modal -->
<div class="modal fade" id="addPasserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Board Passer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passerForm">
                <div class="modal-body">
                    <input type="hidden" id="passerId" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="studentId" class="form-label">Student ID</label>
                                <select class="form-select" id="studentId" name="student_id" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="studentName" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="studentName" name="student_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program" class="form-label">Program</label>
                                <input type="text" class="form-control" id="program" name="program">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="boardExam" class="form-label">Board Exam</label>
                                <select class="form-select" id="boardExam" name="board_exam" required>
                                    <option value="">Select Exam</option>
                                    <option value="CPA">CPA</option>
                                    <option value="LET">LET</option>
                                    <option value="CE">CE</option>
                                    <option value="ME">ME</option>
                                    <option value="EE">EE</option>
                                    <option value="NAPOLCOM">NAPOLCOM</option>
                                    <option value="OTHER">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="examYear" class="form-label">Exam Year</label>
                                <select class="form-select" id="examYear" name="exam_year" required>
                                    <option value="">Select Year</option>
                                    <?php for($year = date('Y'); $year >= 2020; $year--): ?>
                                        <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="examDate" class="form-label">Exam Date</label>
                                <input type="date" class="form-control" id="examDate" name="exam_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="result" class="form-label">Result</label>
                                <select class="form-select" id="result" name="result" required>
                                    <option value="">Select Result</option>
                                    <option value="PASS">PASS</option>
                                    <option value="FAIL">FAIL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (%)</label>
                                <input type="number" class="form-control" id="rating" name="rating" min="0" max="100" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStudents();
    updateStats();
});

function loadStudents() {
    fetch('/admin/analytics/students-list')
        .then(response => response.json())
        .then(students => {
            const select = document.getElementById('studentId');
            select.innerHTML = '<option value="">Select Student</option>';
            students.forEach(student => {
                select.innerHTML += `<option value="${student.student_id}" data-name="${student.name}" data-program="${student.program}">${student.student_id} - ${student.name}</option>`;
            });
        })
        .catch(error => console.error('Error loading students:', error));
}

function updateStats() {
    fetch('/admin/analytics/board-passers/stats')
        .then(response => response.json())
        .then(stats => {
            document.getElementById('totalPassers').textContent = stats.total_passers;
            document.getElementById('totalNonPassers').textContent = stats.total_non_passers;
            document.getElementById('passRate').textContent = stats.pass_rate + '%';
            document.getElementById('lastUpdated').textContent = stats.last_updated || 'Never';
        })
        .catch(error => console.error('Error updating stats:', error));
}

// When student is selected, auto-fill name and program
document.getElementById('studentId').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('studentName').value = selectedOption.dataset.name || '';
        document.getElementById('program').value = selectedOption.dataset.program || '';
    } else {
        document.getElementById('studentName').value = '';
        document.getElementById('program').value = '';
    }
});

// Form submission
document.getElementById('passerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isEdit = document.getElementById('passerId').value !== '';
    const url = isEdit ? `/admin/analytics/board-passers/${formData.get('id')}` : '/admin/analytics/board-passers';
    const method = isEdit ? 'PUT' : 'POST';
    
    if (isEdit) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the entry.');
    });
});

function editPasser(id) {
    // Fetch passer data and populate modal
    fetch(`/admin/analytics/board-passers/${id}`)
        .then(response => response.json())
        .then(passer => {
            document.getElementById('modalTitle').textContent = 'Edit Board Passer';
            document.getElementById('passerId').value = passer.id;
            document.getElementById('studentId').value = passer.student_id;
            document.getElementById('studentName').value = passer.student_name;
            document.getElementById('program').value = passer.program;
            document.getElementById('boardExam').value = passer.board_exam;
            document.getElementById('examYear').value = passer.exam_year;
            document.getElementById('examDate').value = passer.exam_date;
            document.getElementById('result').value = passer.result;
            document.getElementById('rating').value = passer.rating;
            document.getElementById('notes').value = passer.notes;
            
            new bootstrap.Modal(document.getElementById('addPasserModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading passer data.');
        });
}

function deletePasser(id) {
    if (confirm('Are you sure you want to delete this entry?')) {
        fetch(`/admin/analytics/board-passers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the entry.');
        });
    }
}

function applyFilters() {
    const filters = {
        exam: document.getElementById('filterExam').value,
        year: document.getElementById('filterYear').value,
        result: document.getElementById('filterResult').value,
        program: document.getElementById('filterProgram').value
    };
    
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) params.append(key, filters[key]);
    });
    
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

function clearFilters() {
    window.location.href = window.location.pathname;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\board-passers\index.blade.php ENDPATH**/ ?>