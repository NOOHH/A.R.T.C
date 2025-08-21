

<?php $__env->startSection('title', 'Edit Module'); ?>

<?php $__env->startPush('styles'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/admin/admin-modules/admin-modules.css')); ?>?v=<?php echo e(time()); ?>">
<style>
    .edit-module-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3498db;
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background: #2980b9;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
        margin-right: 10px;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .content-section {
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .video-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        margin: 20px 0;
        transition: border-color 0.2s;
    }
    
    .video-upload-area:hover {
        border-color: #3498db;
    }
    
    .additional-content {
        margin-top: 20px;
    }
    
    .content-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid #3498db;
    }
    
    .add-content-form {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }
    
    .current-attachment {
        background: #e8f4f8;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #17a2b8;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="edit-module-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Module: <?php echo e($module->module_name); ?></h1>
        <a href="<?php echo e(route('admin.modules.index', ['program_id' => $module->program_id])); ?>" class="btn btn-secondary">
            ← Back to Modules
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <!-- Main Module Form -->
    <form action="<?php echo e(route('admin.modules.update', $module->modules_id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="form-group">
            <label for="module_name">Module Name</label>
            <input type="text" id="module_name" name="module_name" class="form-control" 
                   value="<?php echo e(old('module_name', $module->module_name)); ?>" required>
        </div>

        <div class="form-group">
            <label for="module_description">Module Description</label>
            <textarea id="module_description" name="module_description" class="form-control" rows="4"><?php echo e(old('module_description', $module->module_description)); ?></textarea>
        </div>

        <div class="form-group">
            <label for="program_id">Program</label>
            <select id="program_id" name="program_id" class="form-control" required>
                <option value="">Select Program</option>
                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($program->program_id); ?>" 
                            <?php echo e(old('program_id', $module->program_id) == $program->program_id ? 'selected' : ''); ?>>
                        <?php echo e($program->program_name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <?php if($module->attachment): ?>
            <div class="current-attachment">
                <h6>📎 Current Attachment:</h6>
                <p><?php echo e(basename($module->attachment)); ?></p>
                <a href="<?php echo e(asset('storage/' . $module->attachment)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    View Current File
                </a>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="attachment">Replace Attachment (Optional)</label>
            <input type="file" id="attachment" name="attachment" class="form-control" 
                   accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg,.mp4,.avi,.mov">
            <small class="text-muted">Supported: PDF, DOC, DOCX, ZIP, Images, Videos (Max: 100MB)</small>
        </div>

        <button type="submit" class="btn btn-primary">Update Module</button>
    </form>

    <!-- Video Upload Section -->
    <div class="content-section">
        <h3>📹 Video Content</h3>
        
        <?php if($module->video_path): ?>
            <div class="current-attachment">
                <h6>🎥 Current Video:</h6>
                <video width="100%" height="300" controls>
                    <source src="<?php echo e(asset('storage/' . $module->video_path)); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        <?php endif; ?>

        <div class="video-upload-area">
            <input type="file" id="videoFile" accept=".mp4,.avi,.mov,.wmv" style="display: none;">
            <div class="upload-content">
                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                <h5>Upload New Video</h5>
                <p class="text-muted">Click to select a video file (MP4, AVI, MOV, WMV - Max: 500MB)</p>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('videoFile').click()">
                    Choose Video File
                </button>
            </div>
        </div>
    </div>

    <!-- Additional Content Section -->
    <div class="content-section">
        <h3>📚 Additional Content</h3>
        
        <div class="additional-content" id="additionalContent">
            <?php if($module->additional_content): ?>
                <?php
                    $additionalContent = json_decode($module->additional_content, true) ?: [];
                ?>
                <?php $__currentLoopData = $additionalContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="content-item">
                        <h6><?php echo e($content['title'] ?? 'Untitled Content'); ?></h6>
                        <p class="text-muted"><?php echo e($content['description'] ?? 'No description'); ?></p>
                        <span class="badge badge-info"><?php echo e(ucfirst($content['type'] ?? 'unknown')); ?></span>
                        <?php if(isset($content['file_path'])): ?>
                            <a href="<?php echo e(asset('storage/' . $content['file_path'])); ?>" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                View File
                            </a>
                        <?php elseif(isset($content['url'])): ?>
                            <a href="<?php echo e($content['url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                Visit Link
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p class="text-muted">No additional content added yet.</p>
            <?php endif; ?>
        </div>

        <!-- Add Content Form -->
        <div class="add-content-form">
            <h5>Add New Content</h5>
            <form id="addContentForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="content_type">Content Type</label>
                            <select id="content_type" name="content_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="text">Text Content</option>
                                <option value="file">File Upload</option>
                                <option value="link">External Link</option>
                                <option value="video">Video Upload</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="content_title">Content Title</label>
                            <input type="text" id="content_title" name="content_title" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content_description">Description (Optional)</label>
                    <textarea id="content_description" name="content_description" class="form-control" rows="2"></textarea>
                </div>

                <!-- Content type specific fields -->
                <div id="textContent" class="content-type-field" style="display: none;">
                    <div class="form-group">
                        <label for="content_text">Text Content</label>
                        <textarea id="content_text" name="content_text" class="form-control" rows="4"></textarea>
                    </div>
                </div>

                <div id="fileContent" class="content-type-field" style="display: none;">
                    <div class="form-group">
                        <label for="content_file">Choose File</label>
                        <input type="file" id="content_file" name="content_file" class="form-control">
                    </div>
                </div>

                <div id="linkContent" class="content-type-field" style="display: none;">
                    <div class="form-group">
                        <label for="content_link">URL</label>
                        <input type="url" id="content_link" name="content_link" class="form-control">
                    </div>
                </div>

                <div id="videoContent" class="content-type-field" style="display: none;">
                    <div class="form-group">
                        <label for="content_video">Choose Video</label>
                        <input type="file" id="content_video" name="content_file" class="form-control" accept=".mp4,.avi,.mov,.wmv">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Content</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Content type selector
    const contentTypeSelect = document.getElementById('content_type');
    const contentTypeFields = document.querySelectorAll('.content-type-field');
    
    contentTypeSelect.addEventListener('change', function() {
        // Hide all content type fields
        contentTypeFields.forEach(field => field.style.display = 'none');
        
        // Show selected content type field
        const selectedType = this.value;
        if (selectedType) {
            const targetField = document.getElementById(selectedType + 'Content');
            if (targetField) {
                targetField.style.display = 'block';
            }
        }
    });

    // Video upload handler
    const videoFileInput = document.getElementById('videoFile');
    videoFileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadVideo(this.files[0]);
        }
    });

    // Add content form handler
    const addContentForm = document.getElementById('addContentForm');
    addContentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        addContent();
    });
});

function uploadVideo(file) {
    const formData = new FormData();
    formData.append('video', file);
    
    // Show loading
    const uploadArea = document.querySelector('.video-upload-area');
    uploadArea.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Uploading video...</p></div>';
    
    fetch('<?php echo e(route("admin.modules.upload-video", $module->modules_id)); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show new video
            location.reload();
        } else {
            alert('Video upload failed: ' + data.message);
            // Reset upload area
            resetVideoUploadArea();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during video upload.');
        resetVideoUploadArea();
    });
}

function resetVideoUploadArea() {
    const uploadArea = document.querySelector('.video-upload-area');
    uploadArea.innerHTML = `
        <input type="file" id="videoFile" accept=".mp4,.avi,.mov,.wmv" style="display: none;">
        <div class="upload-content">
            <i class="fas fa-video fa-3x text-muted mb-3"></i>
            <h5>Upload New Video</h5>
            <p class="text-muted">Click to select a video file (MP4, AVI, MOV, WMV - Max: 500MB)</p>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('videoFile').click()">
                Choose Video File
            </button>
        </div>
    `;
    
    // Re-attach event listener
    document.getElementById('videoFile').addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadVideo(this.files[0]);
        }
    });
}

function addContent() {
    const form = document.getElementById('addContentForm');
    const formData = new FormData(form);
    
    fetch('<?php echo e(route("admin.modules.add-content", $module->modules_id)); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show new content
            location.reload();
        } else {
            alert('Failed to add content: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding content.');
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-modules\edit.blade.php ENDPATH**/ ?>