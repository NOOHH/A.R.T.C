

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Dynamic Form Requirements Management</h3>
                    <p class="text-muted">Manage form fields for registration - add, remove, or modify requirements</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Current Form Requirements</h4>
                            <div id="requirements-list">
                                <?php $__currentLoopData = $requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="requirement-item mb-3 p-3 border rounded" data-id="<?php echo e($requirement->id); ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">
                                                    <?php echo e($requirement->field_label); ?>

                                                    <?php if($requirement->is_required): ?>
                                                        <span class="badge badge-danger">Required</span>
                                                    <?php endif; ?>
                                                    <?php if(!$requirement->is_active): ?>
                                                        <span class="badge badge-warning">Archived</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted">
                                                    Field: <?php echo e($requirement->field_name); ?> | 
                                                    Type: <?php echo e($requirement->field_type); ?> | 
                                                    Program: <?php echo e($requirement->program_type); ?>

                                                </small>
                                            </div>
                                            <div class="btn-group">
                                                <?php if($requirement->is_active): ?>
                                                    <button class="btn btn-sm btn-warning" onclick="archiveField('<?php echo e($requirement->field_name); ?>')">
                                                        Archive
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-success" onclick="restoreField('<?php echo e($requirement->field_name); ?>')">
                                                        Restore
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-secondary" onclick="editField(<?php echo e($requirement->id); ?>)">
                                                    Edit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4>Add New Requirement</h4>
                            <form id="add-requirement-form">
                                <div class="form-group mb-3">
                                    <label>Field Name</label>
                                    <input type="text" class="form-control" name="field_name" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Field Label</label>
                                    <input type="text" class="form-control" name="field_label" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Field Type</label>
                                    <select class="form-control" name="field_type" required>
                                        <option value="">Select Type</option>
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="tel">Phone</option>
                                        <option value="date">Date</option>
                                        <option value="file">File</option>
                                        <option value="select">Select</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="radio">Radio</option>
                                        <option value="number">Number</option>
                                        <option value="section">Section</option>
                                        <option value="module_selection">Module Selection</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Program Type</label>
                                    <select class="form-control" name="program_type" required>
                                        <option value="both">Both</option>
                                        <option value="modular">Modular</option>
                                        <option value="complete">Complete</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Section Name</label>
                                    <input type="text" class="form-control" name="section_name">
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" name="is_required" value="1">
                                    <label class="form-check-label">Required Field</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Requirement</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function archiveField(fieldName) {
    if (confirm('Are you sure you want to archive this field? It will be hidden from the registration form but data will be preserved.')) {
        fetch('/admin/form-requirements/archive', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ field_name: fieldName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error archiving field: ' + data.message);
            }
        });
    }
}

function restoreField(fieldName) {
    if (confirm('Are you sure you want to restore this field? It will be shown in the registration form again.')) {
        fetch('/admin/form-requirements/restore', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ field_name: fieldName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error restoring field: ' + data.message);
            }
        });
    }
}

document.getElementById('add-requirement-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/admin/form-requirements', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error adding requirement: ' + data.message);
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-settings\form-requirements.blade.php ENDPATH**/ ?>