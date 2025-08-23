

<?php $__env->startPush('styles'); ?>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'List of Students'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
  
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> List of Students</h2>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success" onclick="exportToCSV()">
        <i class="bi bi-download"></i> Export to CSV
      </button>
      <a href="<?php echo e(route('admin.students.archived')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-archive"></i> View Archived
      </a>
    </div>
  </div>

  
  <div class="card shadow mb-4">
    <div class="card-body">
      <form method="GET" action="<?php echo e(route('admin.students.index')); ?>">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="program_id" class="form-label">Filter by Program</label>
            <select name="program_id" id="program_id" class="form-select">
              <option value="">All Programs</option>
              <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($program->program_id); ?>"
                  <?php echo e(request('program_id') == $program->program_id ? 'selected' : ''); ?>>
                  <?php echo e($program->program_name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Filter by Status</label>
            <select name="status" id="status" class="form-select">
              <option value="">All Status</option>
              <option value="approved" <?php echo e(request('status')=='approved'?'selected':''); ?>>Approved</option>
              <option value="pending"  <?php echo e(request('status')=='pending'?'selected':''); ?>>Pending</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="search" class="form-label">Search</label>
            <input type="text"
                   name="search"
                   id="search"
                   class="form-control"
                   placeholder="Search by name, ID, or email…"
                   value="<?php echo e(request('search')); ?>">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <div class="w-100 d-flex gap-2">
              <button type="submit" class="btn btn-primary w-50">
                <i class="bi bi-search"></i>
              </button>
              <a href="<?php echo e(route('admin.students.index')); ?>"
                 class="btn btn-outline-secondary w-50">
                <i class="bi bi-x"></i>
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  
  <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e(session('success')); ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  
  <div class="card shadow">
    <div class="card-body">
      <?php if($students->count()): ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Batch</th>
                <th>Learning Mode</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Registered</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><strong><?php echo e($student->student_id); ?></strong></td>
                  <td><?php echo e($student->firstname); ?> <?php echo e($student->lastname); ?></td>
                  <td><?php echo e($student->email); ?></td>
                  <td>
                    <?php if($student->program): ?>
                      <span class="badge bg-info"><?php echo e($student->program->program_name); ?></span>
                    <?php else: ?>
                      <span class="text-muted">No Program</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($student->enrollment && $student->enrollment->batch): ?>
                      <span class="badge bg-secondary"><?php echo e($student->enrollment->batch->batch_name); ?></span>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($student->enrollment && $student->enrollment->learning_mode): ?>
                      <?php
                        $mode = strtolower($student->enrollment->learning_mode);
                        $badgeColor = 'secondary';
                        $displayText = 'Unknown';
                        
                        if (in_array($mode, ['synchronous', 'synch', 'sync'])) {
                          $badgeColor = 'primary';
                          $displayText = 'Synchronous';
                        } elseif (in_array($mode, ['asynchronous', 'async'])) {
                          $badgeColor = 'success';  
                          $displayText = 'Asynchronous';
                        }
                      ?>
                      <span class="badge bg-<?php echo e($badgeColor); ?>"><?php echo e($displayText); ?></span>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($student->enrollment && $student->enrollment->start_date): ?>
                      <?php echo e(\Carbon\Carbon::parse($student->enrollment->start_date)->format('M d, Y')); ?>

                    <?php elseif($student->enrollment && $student->enrollment->batch && $student->enrollment->batch->start_date): ?>
                      <?php echo e(\Carbon\Carbon::parse($student->enrollment->batch->start_date)->format('M d, Y')); ?>

                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($student->enrollment && $student->enrollment->end_date): ?>
                      <?php echo e(\Carbon\Carbon::parse($student->enrollment->end_date)->format('M d, Y')); ?>

                    <?php elseif($student->enrollment && $student->enrollment->batch && $student->enrollment->batch->end_date): ?>
                      <?php echo e(\Carbon\Carbon::parse($student->enrollment->batch->end_date)->format('M d, Y')); ?>

                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($student->date_approved): ?>
                      <span class="badge bg-success">Approved</span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($student->created_at->format('M d, Y')); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <?php if(session('preview_mode')): ?>
                        <a href="#" onclick="alert('Preview mode - View details not available')"
                           class="btn btn-sm btn-outline-info" title="View (Preview)">
                          <i class="bi bi-eye"></i>
                        </a>
                      <?php else: ?>
                        <a href="<?php echo e(route('admin.students.show', $student)); ?>"
                           class="btn btn-sm btn-outline-info" title="View">
                          <i class="bi bi-eye"></i>
                        </a>
                      <?php endif; ?>
                      <?php if (! ($student->date_approved)): ?>
                        <?php if(session('preview_mode')): ?>
                          <button type="button" onclick="alert('Preview mode - Actions not available')"
                                  class="btn btn-sm btn-outline-success" title="Approve (Preview)">
                            <i class="bi bi-check-circle"></i>
                          </button>
                        <?php else: ?>
                          <form method="POST"
                                action="<?php echo e(route('admin.students.approve', $student)); ?>"
                                class="d-inline"
                                onsubmit="return confirm('Approve this student?')">
                            <?php echo csrf_field(); ?> 
                            <?php echo method_field('PATCH'); ?>
                            <button class="btn btn-sm btn-outline-success" title="Approve">
                              <i class="bi bi-check-circle"></i>
                            </button>
                          </form>
                        <?php endif; ?>
                      <?php endif; ?>
                      <?php if(session('preview_mode')): ?>
                        <button type="button" onclick="alert('Preview mode - Archive not available')"
                                class="btn btn-sm btn-outline-secondary" title="Archive (Preview)">
                          <i class="bi bi-archive"></i>
                        </button>
                      <?php else: ?>
                        <button class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#archiveStudentModal"
                                data-student-id="<?php echo e($student->student_id); ?>"
                                data-student-name="<?php echo e($student->firstname.' '.$student->lastname); ?>"
                                title="Archive">
                          <i class="bi bi-archive"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>

        
        <div class="d-flex justify-content-center mt-4">
          <?php echo e($students->links('pagination::bootstrap-5')); ?>

        </div>
      <?php else: ?>
        <div class="text-center py-5">
          <i class="bi bi-people fs-1 text-muted"></i>
          <h4 class="text-muted mt-3">No Students Found</h4>
          <p class="text-muted">
            <?php if(request()->hasAny(['program_id','status','search'])): ?>
              No results match your filters.
            <?php else: ?>
              No students registered yet.
            <?php endif; ?>
          </p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>


<div class="modal fade" id="archiveStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-archive text-warning"></i> Archive Student
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-3"></i>
        <h5>Archive <strong id="studentNameToArchive"></strong>?</h5>
        <p class="text-muted">You can restore later if needed.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="archiveStudentForm" method="POST" style="display:inline;">
          <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
          <button class="btn btn-warning">
            <i class="bi bi-archive"></i> Archive
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Archive‐modal setup
    document
      .getElementById('archiveStudentModal')
      .addEventListener('show.bs.modal', function(e) {
        const btn  = e.relatedTarget;
        const id   = btn.dataset.studentId;
        const name = btn.dataset.studentName;
        document.getElementById('studentNameToArchive').textContent = name;
        document.getElementById('archiveStudentForm').action =
          `/admin/students/${id}/archive`;
      });

    // Auto-submit the filter form when Program dropdown changes
    document
      .getElementById('program_id')
      .addEventListener('change', function() {
        this.form.submit();
      });

    // Export to CSV function
    function exportToCSV() {
      console.log('Starting CSV export...');
      
      // Get current filter values
      const programId = document.getElementById('program_id').value;
      const status = document.getElementById('status').value;
      const search = document.getElementById('search').value;
      
      // Build the export URL with current filters
      let exportUrl = '<?php echo e(route("admin.students.export")); ?>?';
      const params = new URLSearchParams();
      
      if (programId) params.append('program_id', programId);
      if (status) params.append('status', status);
      if (search) params.append('search', search);
      
      exportUrl += params.toString();
      
      console.log('Export URL:', exportUrl);
      
      // Show loading indicator
      const exportBtn = document.querySelector('button[onclick="exportToCSV()"]');
      const originalText = exportBtn.innerHTML;
      exportBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Exporting...';
      exportBtn.disabled = true;
      
      // Create a temporary link and trigger download
      const link = document.createElement('a');
      link.href = exportUrl;
      link.download = 'students_export.csv';
      
      // Add error handling
      link.onerror = function() {
        console.error('Export download failed');
        alert('Export failed. Please try again or contact support.');
        // Restore button
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
      };
      
      // Monitor for successful download completion
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      // Restore button after a short delay
      setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
        console.log('Export process completed');
      }, 2000);
      
      // Test if the export URL is accessible
      fetch(exportUrl, { method: 'HEAD' })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
          }
          console.log('Export URL is accessible');
        })
        .catch(error => {
          console.error('Export URL test failed:', error);
          alert('Export URL is not accessible. Error: ' + error.message);
          // Restore button
          exportBtn.innerHTML = originalText;
          exportBtn.disabled = false;
        });
    }
  </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/students/index.blade.php ENDPATH**/ ?>