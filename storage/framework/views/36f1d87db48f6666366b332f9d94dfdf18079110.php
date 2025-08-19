

<?php $__env->startSection('title', 'Module Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Hierarchical Module Structure */
.modules-hierarchy {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.module-container {
  border: 2px solid #e1e5e9;
  border-radius: 15px;
  background: white;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.module-container:hover {
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.module-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s ease;
}   

.module-header:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
}

.module-title-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.module-title-section h4 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
}

.module-title-section small {
  font-size: 1rem;
  opacity: 0.9;
}

.module-toggle-icon {
  transition: transform 0.3s ease;
  font-size: 1.2rem;
}

.module-toggle-icon.expanded {
  transform: rotate(90deg);
}

.module-content {
  display: none;
  background: #f8f9fa;
  border-top: 1px solid #dee2e6;
}

.module-content.expanded {
  display: block;
}

.courses-list {
  padding: 1.5rem;
}

.course-container {
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.course-header {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
  padding: 1.5rem 2rem;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s ease;
}

.course-header:hover {
  background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
}

.course-header h5 {
  font-size: 1.2rem;
  font-weight: 600;
  margin: 0;
}

.course-content {
  display: none;
  padding: 1.5rem;
  background: #ffffff;
}

.course-content.expanded {
  display: block;
}

.content-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  margin-bottom: 0.75rem;
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.content-item:hover {
  background: #e9ecef;
  border-color: #dee2e6;
  transform: translateX(5px);
}

.content-item-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.content-item-type {
  background: #007bff;
  color: white;
  padding: 0.4rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.content-item-type.assignment {
  background: #dc3545;
}

.content-item-type.pdf {
  background: #fd7e14;
}

.content-item-type.lesson {
  background: #17a2b8;
}

.content-item-type.quiz {
  background: #6f42c1;
}

.content-item-type.test {
  background: #e83e8c;
}

.content-item-type.link {
  background: #20c997;
}

.content-item-actions {
  display: flex;
  gap: 0.5rem;
}

.content-item-actions .btn {
  padding: 0.4rem 0.8rem;
  font-size: 0.875rem;
  border-radius: 6px;
  border: none;
  font-weight: 500;
  transition: all 0.2s ease;
}

.content-item-actions .btn:hover {
  transform: translateY(-1px);
}

.module-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.module-actions .btn {
  padding: 0.7rem 1.2rem;
  font-size: 0.9rem;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
}

.add-course-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white !important;
  border: 2px solid rgba(255, 255, 255, 0.3) !important;
}

.add-course-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  color: white !important;
  border-color: rgba(255, 255, 255, 0.5) !important;
  transform: translateY(-2px);
}

.btn-outline-light {
  background: rgba(255, 255, 255, 0.15);
  color: white !important;
  border: 2px solid rgba(255, 255, 255, 0.3) !important;
}

.btn-outline-light:hover {
  background: rgba(255, 255, 255, 0.25);
  color: white !important;
  border-color: rgba(255, 255, 255, 0.5) !important;
  transform: translateY(-2px);
}

/* Module action buttons */
.module-actions .btn-outline-light {
  background: rgba(255, 255, 255, 0.2);
  color: white !important;
  border: 1px solid rgba(255, 255, 255, 0.4) !important;
  padding: 0.5rem 0.8rem;
  font-size: 0.875rem;
}

.module-actions .btn-outline-light:hover {
  background: rgba(255, 255, 255, 0.3);
  color: white !important;
  border-color: rgba(255, 255, 255, 0.6) !important;
  transform: translateY(-1px);
}

/* Ensure edit and delete buttons are visible */
.module-actions .btn-outline-light i {
  color: white !important;
}

.module-actions .btn-outline-light:hover i {
  color: white !important;
}

.no-courses-message {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
  font-style: italic;
}

.drag-handle,
.module-drag-handle,
.course-drag-handle,
.content-drag-handle {
  cursor: move;
  color: rgba(108, 117, 125, 0.7);
  margin-right: 0.5rem;
  font-size: 1.1rem;
  transition: color 0.2s ease;
}

.drag-handle:hover,
.module-drag-handle:hover,
.course-drag-handle:hover,
.content-drag-handle:hover {
  color: rgba(108, 117, 125, 1);
}

.module-header .module-drag-handle {
  color: rgba(255, 255, 255, 0.7);
}

.module-header .module-drag-handle:hover {
  color: rgba(255, 255, 255, 1);
}

.sortable-ghost {
  opacity: 0.5;
  background: #f1f3f4;
}

.sortable-chosen {
  transform: scale(1.02);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.action-buttons .btn {
  padding: 1rem 1.8rem;
  font-weight: 500;
  border-radius: 6px;
  transition: all 0.2s ease;
  text-decoration: none;
  border: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 200px;
  background: white;
  color: #374151;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  position: relative;
}

.action-buttons .btn i {
  font-size: 1.25rem;
  transition: transform 0.2s ease;
}

.action-buttons .btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #cbd5e1;
}

.action-buttons .btn:hover i {
  transform: scale(1.1);
}

.add-course-content-btn {
  border-color: #3b82f6;
  color: #3b82f6;
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.add-course-content-btn:hover {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.view-archived-btn {
  border-color: #6b7280;
  color: #6b7280;
  background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.view-archived-btn:hover {
  background: #6b7280;
  color: white;
  border-color: #6b7280;
}

.quiz-generator-btn {
  border-color: #8b5cf6;
  color: #8b5cf6;
  background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}

.quiz-generator-btn:hover {
  background: #8b5cf6;
  color: white;
  border-color: #8b5cf6;
}

/* Cross-module drag and drop styles */
.drag-over {
  border: 2px dashed #007bff !important;
  background: rgba(0, 123, 255, 0.1) !important;
  transform: scale(1.02);
}

.sortable-drag {
  opacity: 0.8;
  transform: rotate(5deg);
}

.course-selection-item {
  padding: 1rem;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  margin-bottom: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.course-selection-item:hover {
  background: #f8f9fa;
  border-color: #007bff;
  transform: translateX(5px);
}

.course-selection-item i {
  color: #007bff;
  margin-right: 0.5rem;
}

.course-selection-item p {
  margin: 0.5rem 0 0 0;
  color: #6c757d;
  font-size: 0.9rem;
}

/* Notification animations */
@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Split Layout Styles */
.admin-split-container {
  display: flex;
  height: calc(100vh - 200px);
  gap: 1.5rem;
  margin-top: 2rem;
}

.admin-modules-panel {
  flex: 0 0 60%;
  background: white;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  padding: 1.5rem;
}

.admin-content-panel {
  flex: 1;
  background: white;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.content-viewer-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1.5rem;
  border-radius: 15px 15px 0 0;
}

.content-viewer-header h3 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 600;
}

.content-viewer-header small {
  opacity: 0.9;
  font-size: 0.9rem;
}

.content-viewer-body {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.content-placeholder {
  text-align: center;
  color: #6c757d;
}

.content-display {
  width: 100%;
  height: 100%;
}

.content-display iframe {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 10px;
}

.content-display .content-text {
  line-height: 1.6;
  font-size: 1.1rem;
}

.courses-preview, .content-items-preview {
  max-height: 400px;
  overflow-y: auto;
}

.course-preview-item, .content-item-preview {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  border-left: 4px solid #667eea;
}

.course-preview-item h6, .content-item-preview h6 {
  margin: 0 0 0.5rem 0;
  color: #495057;
  font-weight: 600;
}

.course-preview-item p, .content-item-preview p {
  margin: 0;
  font-size: 0.9rem;
}

.content-item-preview .badge {
  margin-top: 0.5rem;
}

.content-item.active {
  background: #e3f2fd !important;
  border-color: #2196f3 !important;
  box-shadow: 0 4px 12px rgba(33, 150, 243, 0.2) !important;
}

.content-frame {
  width: 100%;
  height: 500px;
  border: none;
  border-radius: 10px;
}

.link-preview, .assignment-preview {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 2rem;
  text-align: center;
}

.video-container, .pdf-viewer {
  background: #000;
  border-radius: 10px;
  overflow: hidden;
}

.content-details {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 1.5rem;
  border-left: 4px solid #667eea;
}

.content-text {
  margin-top: 1rem;
}

.content-text > div {
  max-height: 400px;
  overflow-y: auto;
  line-height: 1.6;
}

/* Enhanced content viewer styles */
.content-display {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.content-display .btn {
  transition: all 0.2s ease;
}

.content-display .btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

@media (max-width: 1200px) {
  .admin-split-container {
    flex-direction: column;
    height: auto;
  }
  
  .admin-modules-panel {
    flex: none;
    max-height: 500px;
  }
  
  .admin-content-panel {
    flex: none;
    height: 400px;
  }
}

/* Modules Container */
.modules-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  position: relative;
  z-index: 1;
}

.modules-header {
  text-align: center;
  margin-bottom: 2rem;
}

.modules-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.modules-header p {
  font-size: 1.1rem;
  color: #6c757d;
  margin: 0;
}

.program-selector {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
  border: 1px solid #e9ecef;
}

.filter-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
  border: 1px solid #e9ecef;
}

.filter-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.filter-group label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #495057;
}

.select-program-msg {
  text-align: center;
  padding: 3rem;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* Modal Styles */
.modal-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 99999;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s, visibility 0.3s;
}

.modal-bg.show {
  display: flex;
  opacity: 1;
  visibility: visible;
}

.modal {
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  transform: scale(0.9);
  transition: transform 0.3s;
  z-index: 100000;
}

.modal-bg.show .modal {
  transform: scale(1);
}

.modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1.5rem;
  border-radius: 15px 15px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 600;
}

.modal-close {
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s ease;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.2);
}

.modal-body {
  padding: 2rem;
}

.modal-actions {
  padding: 1.5rem;
  border-top: 1px solid #dee2e6;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #495057;
}

.form-control, .form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus, .form-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.cancel-btn {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.cancel-btn:hover {
  background: #545b62;
}

.add-btn {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-btn:hover {
  background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
  transform: translateY(-1px);
}

.text-muted {
  color: #6c757d !important;
  font-size: 0.875rem;
}

.text-danger {
  color: #dc3545 !important;
}

/* Additional modal styling for better appearance */
.modal {
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-bg.show {
  animation: modalBgFadeIn 0.3s ease-out;
}

@keyframes modalBgFadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Form styling improvements */
.form-group small {
  margin-top: 0.25rem;
  display: block;
}

.form-control:focus, .form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Button styling improvements */
.modal-actions .btn {
  min-width: 100px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.modal-actions .btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<!-- Alert Messages -->
<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="modules-container">
    <!-- Header -->
    <div class="modules-header">
        <h1><i class="bi bi-journals"></i> Module Management</h1>
        <p>Create, organize, and manage your educational content</p>
    </div>

    <!-- Program Selector -->
    <div class="program-selector">
        <label for="programSelect" class="form-label">Select Program to View/Manage Modules:</label>
        <div class="d-flex align-items-center gap-3">
            <select id="programSelect" name="program_id" class="form-select" style="max-width: 400px;">
                <option value="">-- Select a Program --</option>
                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($program->program_id); ?>"
                        <?php echo e(request('program_id') == $program->program_id ? 'selected' : ''); ?>>
                        <?php echo e($program->program_name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Select a program to view and manage its modules
            </small>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section" id="filterSection" style="display: none;">
        <div class="filter-row">
            <div class="filter-group">
                <label for="batchFilter">Filter by Batch:</label>
                <select id="batchFilter" class="form-select">
                    <option value="">All Batches</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="courseFilter">Filter by Course:</label>
                <select id="courseFilter" class="form-select">
                    <option value="">All Courses</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="learningModeFilter">Filter by Learning Mode:</label>
                <select id="learningModeFilter" class="form-select">
                    <option value="">All Learning Modes</option>
                    <option value="synchronous">Synchronous</option>
                    <option value="asynchronous">Asynchronous</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="http://127.0.0.1:8000/professor/modules/course-content-upload?program_id=40&module_id=86&course_id=60" class="btn add-course-content-btn">
            <i class="bi bi-plus-circle"></i> Add Course Content
        </a>
        <a href="<?php echo e(route('professor.modules.archived')); ?>" class="btn view-archived-btn">
            <i class="bi bi-archive"></i> View Archived
        </a>
        <a href="<?php echo e(route('professor.quiz-generator')); ?>" class="btn quiz-generator-btn">
            <i class="bi bi-robot"></i> AI Quiz Generator
        </a>
    </div>

    <!-- Split Layout Container -->
    <div class="admin-split-container">
        <!-- Left Panel - Modules List -->
        <div class="admin-modules-panel">
            <div id="modulesDisplayArea">
                <?php if(request('program_id') && isset($modules)): ?>
                    <?php if($modules->count() > 0): ?>
                        <div class="modules-hierarchy" id="modulesHierarchy">
                            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $escapedModuleName = addslashes($module->module_name); ?>
                                <div class="module-container" data-module-id="<?php echo e($module->modules_id); ?>">
                                    <div class="module-header" onclick="toggleModule(<?php echo e($module->modules_id); ?>)">
                                        <div class="module-title-section">
                                            <i class="module-drag-handle bi bi-grip-vertical"></i>
                                            <i class="module-toggle-icon bi bi-chevron-right"></i>
                                            <div>
                                                <h4 class="mb-0"><?php echo e($module->module_name); ?></h4>
                                                <?php if($module->module_description): ?>
                                                    <small class="opacity-75"><?php echo e($module->module_description); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="module-actions" onclick="event.stopPropagation();">
                                        </div>
                                    </div>
                                    
                                    <div class="module-content" id="module-content-<?php echo e($module->modules_id); ?>">
                                        <div class="courses-list">
                                            <div id="courses-container-<?php echo e($module->modules_id); ?>">
                                                <!-- Courses will be loaded dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="select-program-msg">
                            <div class="empty-state">
                                <i class="bi bi-journals" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                                <h4 style="color: #6c757d; margin-bottom: 1rem;">No Modules Found</h4>
                                <p style="color: #6c757d; margin-bottom: 2rem;">No modules are available for the selected program yet.</p>
                                <button type="button" class="add-module-btn" onclick="document.getElementById('addModalBg').classList.add('show');">
                                    <i class="bi bi-plus-circle"></i> Create First Module
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="select-program-msg">
                        <div class="empty-state">
                            <i class="bi bi-arrow-up-circle" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                            <h4 style="color: #6c757d; margin-bottom: 1rem;">Select a Program</h4>
                            <p style="color: #6c757d;">Select a program from the dropdown above to view and manage its modules</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Panel - Content Viewer -->
        <div class="admin-content-panel">
            <div class="content-viewer-header">
                <h3 id="content-title">Content Viewer</h3>
                <small id="content-subtitle">Select a module or course to view content</small>
            </div>
            
            <div class="content-viewer-body" id="contentViewer">
                <div class="content-placeholder">
                    <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                    <h4 style="color: #6c757d;">No Content Selected</h4>
                    <p style="color: #6c757d;">Click on a module or course to view its content here</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Global variables
let currentArchiveModuleId = null;
let currentOverrideModuleId = null;

// Ensure functions are globally accessible
window.toggleModule = function(moduleId) {
    const content = document.getElementById(`module-content-${moduleId}`);
    const icon = content.previousElementSibling.querySelector('.module-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
        loadModuleCourses(moduleId);
    }
    
    // Load module content in viewer
    loadModuleContentInViewer(moduleId);
};

window.showAddCourseModal = function(moduleId, moduleName = '') {
    const modal = document.getElementById('addCourseModalBg');
    const form = document.getElementById('addCourseForm');
    
    if (modal && form) {
        // Pre-fill the module selection if moduleId is provided
        if (moduleId) {
            // Set the program first by finding which program this module belongs to
            fetch(`/professor/modules/by-program?module_id=${moduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.program_id) {
                        const programSelect = document.getElementById('courseProgramSelect');
                        const moduleSelect = document.getElementById('courseModuleSelect');
                        
                        if (programSelect) {
                            programSelect.value = data.program_id;
                            // Load modules for this program
                            loadModulesForProgram(data.program_id, 'courseModuleSelect');
                            
                            // Set the module after loading
                            setTimeout(() => {
                                if (moduleSelect) {
                                    moduleSelect.value = moduleId;
                                }
                            }, 500);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading module info:', error);
                });
        }
        
        modal.classList.add('show');
    } else {
        console.error('Add course modal or form not found');
    }
};

// Modal functionality
function setupModalEventListeners() {
    const addModalBg = document.getElementById('addModalBg');
    const closeAddModal = document.getElementById('closeAddModal');
    const closeAddModalBtn = document.getElementById('closeAddModalBtn');
    const addContentForm = document.getElementById('addContentForm');
    
    // Only add event listeners if elements exist
    if (addModalBg) {
        // Close modal when clicking on background
        addModalBg.addEventListener('click', function(e) {
            if (e.target === addModalBg) {
                addModalBg.classList.remove('show');
            }
        });
    }
    
    if (closeAddModal) {
        // Close modal when clicking close button
        closeAddModal.addEventListener('click', function() {
            addModalBg.classList.remove('show');
        });
    }
    
    if (closeAddModalBtn) {
        // Close modal when clicking cancel button
        closeAddModalBtn.addEventListener('click', function() {
            addModalBg.classList.remove('show');
        });
    }
    
    if (addContentForm) {
        // Handle form submission
        addContentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitContentForm();
        });
    }
    
    // Handle plan selection to show/hide batch field
    const planSelectElement = document.getElementById('planSelect');
    if (planSelectElement) {
        planSelectElement.addEventListener('change', function() {
            const batchGroup = document.getElementById('batchGroup');
            if (batchGroup) {
                if (this.value === 'full') {
                    batchGroup.style.display = 'block';
                } else {
                    batchGroup.style.display = 'none';
                }
            }
        });
    }
    
    // Handle program selection to load modules and batches
    const modalProgramSelectElement = document.getElementById('modalProgramSelect');
    if (modalProgramSelectElement) {
        modalProgramSelectElement.addEventListener('change', function() {
            if (this.value) {
                loadModulesForProgram(this.value);
                loadBatchesForProgram(this.value);
            }
        });
    }
}

        // Load modules for selected program
        function loadModulesForProgram(programId, targetSelectId = null) {
            console.log('Loading modules for program:', programId);
            fetch(`/professor/modules/by-program?program_id=${programId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Modules data received:', data);
                    if (data.error) {
                        console.error('Error loading modules:', data.error);
                        return;
                    }
                    
                    // If targetSelectId is provided, populate that select
                    if (targetSelectId) {
                        const targetSelect = document.getElementById(targetSelectId);
                        if (targetSelect) {
                            targetSelect.innerHTML = '<option value="">-- Select Module --</option>';
                            if (data.modules && data.modules.length > 0) {
                                data.modules.forEach(module => {
                                    const option = document.createElement('option');
                                    option.value = module.modules_id;
                                    option.textContent = module.module_name;
                                    targetSelect.appendChild(option);
                                });
                                targetSelect.disabled = false;
                            }
                        }
                    }
                    
                    console.log('Modules loaded for program:', data);
                })
                .catch(error => {
                    console.error('Error loading modules:', error);
                    console.error('Error details:', error.message);
                });
        }

        // Load batches for selected program
        function loadBatchesForProgram(programId) {
            console.log('Loading batches for program:', programId);
            fetch(`/professor/modules/batches?program_id=${programId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Batches response status:', response.status);
                    console.log('Batches response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Batches data received:', data);
                    if (data.error) {
                        console.error('Error loading batches:', data.error);
                        return;
                    }
                    const batchSelect = document.getElementById('batchSelect');
                    batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';
                    data.forEach(batch => {
                        batchSelect.innerHTML += `<option value="${batch.batch_id}">${batch.batch_name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error loading batches:', error);
                    console.error('Batches error details:', error.message);
                });
        }

// Submit content form
function submitContentForm() {
    const form = document.getElementById('addContentForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Creating...';
    submitBtn.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            document.getElementById('addModalBg').classList.remove('show');
            // Show success message
            alert('Content created successfully!');
            // Reload page to show new content
            location.reload();
        } else {
            alert('Error creating content: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating content. Please try again.');
    })
    .finally(() => {
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}



// Course toggle functionality with content viewer
function toggleCourse(moduleId, courseId) {
    const content = document.getElementById(`course-content-${moduleId}-${courseId}`);
    const icon = content.previousElementSibling.querySelector('.course-toggle-icon');
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('expanded');
    } else {
        content.classList.add('expanded');
        icon.classList.add('expanded');
        loadCourseContent(moduleId, courseId);
    }
    
    // Load course content in viewer
    loadCourseContentInViewer(moduleId, courseId);
}

// Load module content in the content viewer
function loadModuleContentInViewer(moduleId) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Module...';
    subtitleElement.textContent = 'Fetching module details';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch module content
    fetch(`/professor/modules/${moduleId}/content`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                titleElement.textContent = data.module.module_name;
                subtitleElement.textContent = `Module • ${data.courses?.length || 0} courses`;
                
                let contentHtml = `
                    <div class="content-display">
                        <h4>Module Overview</h4>
                        <p><strong>Description:</strong> ${data.module.module_description || 'No description available'}</p>
                        <p><strong>Type:</strong> ${data.module.type || 'Standard'}</p>
                        <p><strong>Order:</strong> ${data.module.module_order || 'Not set'}</p>
                        
                        <h5 class="mt-4">Courses (${data.courses?.length || 0})</h5>
                        <div class="courses-preview">
                `;
                
                if (data.courses && data.courses.length > 0) {
                    data.courses.forEach(course => {
                        contentHtml += `
                            <div class="course-preview-item">
                                <h6>${course.course_name}</h6>
                                <p class="text-muted">${course.course_description || 'No description'}</p>
                            </div>
                        `;
                    });
                } else {
                    contentHtml += '<p class="text-muted">No courses available</p>';
                }
                
                contentHtml += `
                        </div>
                    </div>
                `;
                
                viewerBody.innerHTML = contentHtml;
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load module content</div>';
            }
        })
        .catch(error => {
            console.error('Error loading module content:', error);
            viewerBody.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Load course content in the content viewer
function loadCourseContentInViewer(moduleId, courseId) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Course...';
    subtitleElement.textContent = 'Fetching course content';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch course content
    fetch(`/professor/courses/${courseId}/content`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                titleElement.textContent = data.course.subject_name;
                subtitleElement.textContent = `Course • ${data.content?.length || 0} items`;
                
                let contentHtml = `
                    <div class="content-display">
                        <h4>Course Overview</h4>
                        <p><strong>Description:</strong> ${data.course.subject_description || 'No description available'}</p>
                        <p><strong>Type:</strong> ${data.course.type || 'Standard'}</p>
                        
                        <h5 class="mt-4">Content Items (${data.content?.length || 0})</h5>
                        <div class="content-items-preview">
                `;
                
                if (data.content && data.content.length > 0) {
                    data.content.forEach(item => {
                        contentHtml += `
                            <div class="content-item-preview">
                                <h6>${item.content_title}</h6>
                                <p class="text-muted">${item.content_description || 'No description'}</p>
                                <span class="badge bg-primary">${item.content_type}</span>
                            </div>
                        `;
                    });
                } else {
                    contentHtml += '<p class="text-muted">No content available</p>';
                }
                
                contentHtml += `
                        </div>
                    </div>
                `;
                
                viewerBody.innerHTML = contentHtml;
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load course content</div>';
            }
        })
        .catch(error => {
            console.error('Error loading course content:', error);
            viewerBody.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Load module courses
function loadModuleCourses(moduleId) {
    const container = document.getElementById(`courses-container-${moduleId}`);
    
    // Show loading state
    container.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading courses...</div>';
    
    // Fetch courses for this module
    fetch(`/professor/modules/${moduleId}/courses`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.courses.length > 0) {
                let coursesHtml = '';
                data.courses.forEach(course => {
                    coursesHtml += `
                        <div class="course-container" data-course-id="${course.subject_id}">
                            <div class="course-header" onclick="toggleCourse(${moduleId}, ${course.subject_id})">
                                <div class="course-title-section">
                                    <i class="course-drag-handle bi bi-grip-vertical"></i>
                                    <i class="course-toggle-icon bi bi-chevron-right"></i>
                                    <h5 class="mb-0">${course.subject_name}</h5>
                                </div>
                                <div class="course-actions" onclick="event.stopPropagation();">
                                    <button class="btn btn-sm btn-outline-light" onclick="showAddContentModal(${moduleId}, ${course.subject_id}, '${course.subject_name}')">
                                        <i class="bi bi-plus"></i> Add Content
                                    </button>
                                </div>
                            </div>
                            <div class="course-content" id="course-content-${moduleId}-${course.subject_id}">
                                <!-- Course content will be loaded dynamically -->
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = coursesHtml;
            } else {
                container.innerHTML = '<div class="no-courses-message">No courses available for this module</div>';
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading courses</div>';
        });
}

// Load course content
function loadCourseContent(moduleId, courseId) {
    const container = document.getElementById(`course-content-${moduleId}-${courseId}`);
    
    // Show loading state
    container.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading content...</div>';
    
    // Fetch content for this course
    fetch(`/professor/courses/${courseId}/content`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.content.length > 0) {
                let contentHtml = '';
                data.content.forEach(item => {
                    contentHtml += `
                        <div class="content-item" onclick="viewContent(${item.id}, '${item.content_type}', '${item.content_title}')">
                            <div class="content-item-info">
                                <span class="content-item-type ${item.content_type}">${item.content_type}</span>
                                <div>
                                    <strong>${item.content_title}</strong>
                                    <br><small class="text-muted">${item.content_description || 'No description'}</small>
                                </div>
                            </div>
                            <div class="content-item-actions" onclick="event.stopPropagation();">
                                <button class="btn btn-sm btn-primary" onclick="editContent(${item.id})" title="Edit Content">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="archiveContent(${item.id})" title="Archive Content">
                                    <i class="bi bi-archive"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                // Add "Add Content" button at the end
                contentHtml += `
                    <div class="content-item" style="border: 2px dashed #dee2e6; background: #f8f9fa;">
                        <div class="content-item-info">
                            <span class="content-item-type" style="background: #6c757d;">Add New</span>
                            <div>
                                <strong>Add New Content</strong>
                                <br><small class="text-muted">Click to add new content to this course</small>
                            </div>
                        </div>
                        <div class="content-item-actions">
                            <button class="btn btn-sm btn-success" onclick="showAddContentModal(${moduleId}, ${courseId})">
                                <i class="bi bi-plus-circle"></i> Add Content
                            </button>
                        </div>
                    </div>
                `;
                container.innerHTML = contentHtml;
            } else {
                container.innerHTML = `
                    <div class="text-muted text-center">
                        <p>No content available for this course</p>
                        <button class="btn btn-primary btn-sm" onclick="showAddContentModal(${moduleId}, ${courseId})">
                            <i class="bi bi-plus-circle"></i> Add Content
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
        });
}

// Program selection handler
const programSelectElement = document.getElementById('programSelect');
if (programSelectElement) {
    programSelectElement.addEventListener('change', function() {
        const programId = this.value;
        if (programId) {
            window.location.href = `<?php echo e(route('professor.modules.index')); ?>?program_id=${programId}`;
        } else {
            window.location.href = `<?php echo e(route('professor.modules.index')); ?>`;
        }
    });
}

// Show filter section when program is selected
function showFilterSection() {
    const filterSection = document.getElementById('filterSection');
    if (filterSection) {
        filterSection.style.display = 'block';
    }
}

// Hide filter section when no program is selected
function hideFilterSection() {
    const filterSection = document.getElementById('filterSection');
    if (filterSection) {
        filterSection.style.display = 'none';
    }
}

// Module action functions
function editModule(moduleId) {
    window.location.href = `/professor/modules/${moduleId}/edit`;
}

function deleteModule(moduleId) {
    if (confirm('Are you sure you want to delete this module? This action cannot be undone.')) {
        fetch(`/professor/modules/${moduleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting module: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting module');
        });
    }
}



function showAddContentModal(moduleId, courseId, courseName = '') {
    // Find the program_id for this module
    let programId = null;
    const moduleElem = document.querySelector(`[data-module-id='${moduleId}']`);
    if (moduleElem && moduleElem.dataset.programId) {
        programId = moduleElem.dataset.programId;
    } else if (typeof window.currentProgramId !== 'undefined') {
        programId = window.currentProgramId;
    } else if (document.getElementById('programSelect')) {
        programId = document.getElementById('programSelect').value;
    }
    // Build the URL with parameters
    const urlParams = new URLSearchParams();
    if (programId) urlParams.append('program_id', programId);
    if (moduleId) urlParams.append('module_id', moduleId);
    if (courseId) urlParams.append('course_id', courseId);
    const baseUrl = '/professor/modules/course-content-upload';
    const fullUrl = urlParams.toString() ? `${baseUrl}?${urlParams.toString()}` : baseUrl;
    console.log('Opening course content upload page:', fullUrl);
    window.location.href = fullUrl;
}

function openOverrideModal(type, id, name) {
    // Implementation for override settings modal
    alert(`Override settings for ${type}: ${name}`);
}

// Course action functions
function editCourse(courseId) {
    // Open edit course page
    window.location.href = `/professor/courses/${courseId}/edit`;
}

function archiveCourse(courseId) {
    if (confirm('Are you sure you want to archive this course? It will be moved to archived courses but can be restored later.')) {
        fetch(`/professor/courses/${courseId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Course archived successfully!');
                location.reload();
            } else {
                alert('Error archiving course: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error archiving course');
        });
    }
}

// Content action functions
function viewContent(contentId, contentType, contentTitle) {
    const titleElement = document.getElementById('content-title');
    const subtitleElement = document.getElementById('content-subtitle');
    const viewerBody = document.getElementById('contentViewer');
    
    // Show loading state
    titleElement.textContent = 'Loading Content...';
    subtitleElement.textContent = 'Fetching content details';
    viewerBody.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading...</div>';
    
    // Fetch content details
    fetch(`/professor/content/${contentId}/view`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                titleElement.textContent = data.content.content_title || contentTitle;
                subtitleElement.textContent = `${contentType} • Content Item`;
                
                let contentHtml = `
                    <div class="content-display">
                        <div class="content-details">
                            <h4>${data.content.content_title}</h4>
                            <p><strong>Type:</strong> ${data.content.content_type}</p>
                            <p><strong>Description:</strong> ${data.content.content_description || 'No description available'}</p>
                `;
                
                // Display content based on type
                if (data.content.content_type === 'pdf' && data.content.file_path) {
                    contentHtml += `
                        <div class="pdf-viewer mt-3">
                            <iframe src="${data.content.file_path}" class="content-frame"></iframe>
                        </div>
                    `;
                } else if (data.content.content_type === 'video' && data.content.file_path) {
                    contentHtml += `
                        <div class="video-container mt-3">
                            <video controls class="w-100" style="max-height: 400px;">
                                <source src="${data.content.file_path}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    `;
                } else if (data.content.content_type === 'link' && data.content.content_url) {
                    contentHtml += `
                        <div class="link-preview mt-3">
                            <h5>External Link</h5>
                            <p><a href="${data.content.content_url}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right"></i> Open Link
                            </a></p>
                            <small class="text-muted">URL: ${data.content.content_url}</small>
                        </div>
                    `;
                } else if (data.content.content_type === 'assignment') {
                    contentHtml += `
                        <div class="assignment-preview mt-3">
                            <h5>Assignment Details</h5>
                            <p><strong>Instructions:</strong> ${data.content.content_text || 'No instructions provided'}</p>
                            ${data.content.file_path ? `
                                <p><a href="${data.content.file_path}" target="_blank" class="btn btn-secondary">
                                    <i class="bi bi-download"></i> Download Assignment File
                                </a></p>
                            ` : ''}
                        </div>
                    `;
                } else if (data.content.content_text) {
                    contentHtml += `
                        <div class="content-text mt-3">
                            <h5>Content</h5>
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                                ${data.content.content_text}
                            </div>
                        </div>
                    `;
                }
                
                contentHtml += `
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" onclick="editContent(${contentId})">
                                <i class="bi bi-pencil"></i> Edit Content
                            </button>
                            <button class="btn btn-warning ms-2" onclick="archiveContent(${contentId})">
                                <i class="bi bi-archive"></i> Archive Content
                            </button>
                        </div>
                    </div>
                `;
                
                viewerBody.innerHTML = contentHtml;
            } else {
                viewerBody.innerHTML = '<div class="alert alert-danger">Failed to load content details</div>';
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
            titleElement.textContent = contentTitle || 'Content';
            subtitleElement.textContent = `${contentType} • Error loading`;
            viewerBody.innerHTML = `
                <div class="alert alert-warning">
                    <h5>Content Preview Unavailable</h5>
                    <p>Unable to load content preview. You can still edit or manage this content using the buttons below.</p>
                    <div class="mt-3">
                        <button class="btn btn-primary" onclick="editContent(${contentId})">
                            <i class="bi bi-pencil"></i> Edit Content
                        </button>
                        <button class="btn btn-warning ms-2" onclick="archiveContent(${contentId})">
                            <i class="bi bi-archive"></i> Archive Content
                        </button>
                    </div>
                </div>
            `;
        });
}

function editContent(contentId) {
    window.location.href = `/professor/content/${contentId}/edit`;
}

function archiveContent(contentId) {
    if (confirm('Are you sure you want to archive this content? It will be moved to archived content but can be restored later.')) {
        fetch(`/professor/content/${contentId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Content archived successfully!');
                location.reload();
            } else {
                alert('Error archiving content: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error archiving content');
        });
    }
}

// Action button handlers
// Always show modal when Add New Content is clicked (admin logic)
const showAddModalBtn = document.getElementById('showAddModal');
if (showAddModalBtn) {
    showAddModalBtn.addEventListener('click', function() {
        // Optionally pre-select the program if only one is available
        const programSelect = document.getElementById('programSelect');
        if (programSelect && programSelect.value) {
            const modalProgramSelect = document.getElementById('modalProgramSelect');
            if (modalProgramSelect) {
                modalProgramSelect.value = programSelect.value;
                loadModulesForProgram(programSelect.value);
                loadBatchesForProgram(programSelect.value);
            }
        }
        // Show the modal regardless
        const addModalBg = document.getElementById('addModalBg');
        if (addModalBg) {
            addModalBg.classList.add('show');
        }
    });
}

// Add Course Modal Event Listeners
const addCourseModalBtn = document.getElementById('showAddCourseModal');
const addCourseModalBg = document.getElementById('addCourseModalBg');
const closeAddCourseModal = document.getElementById('closeAddCourseModal');
const closeAddCourseModalBtn = document.getElementById('closeAddCourseModalBtn');

if (addCourseModalBtn) {
    addCourseModalBtn.addEventListener('click', function() {
        addCourseModalBg.classList.add('show');
    });
}

if (addCourseModalBg) {
    addCourseModalBg.addEventListener('click', function(e) {
        // Close modal when clicking outside the modal content
        if (e.target === addCourseModalBg) {
            addCourseModalBg.classList.remove('show');
        }
    });
}

if (closeAddCourseModal) {
    closeAddCourseModal.addEventListener('click', function() {
        addCourseModalBg.classList.remove('show');
    });
}

if (closeAddCourseModalBtn) {
    closeAddCourseModalBtn.addEventListener('click', function() {
        addCourseModalBg.classList.remove('show');
    });
}

const showBatchModalBtn = document.getElementById('showBatchModal');
if (showBatchModalBtn) {
    showBatchModalBtn.addEventListener('click', function() {
        // Implementation for batch upload modal
        alert('Batch upload content');
    });
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Setup modal event listeners
    setupModalEventListeners();
    
    // Show filter section if program is selected
    const programSelect = document.getElementById('programSelect');
    if (programSelect && programSelect.value) {
        showFilterSection();
    }
    
    // Setup course modal event listeners
    const courseProgramSelect = document.getElementById('courseProgramSelect');
    const courseModuleSelect = document.getElementById('courseModuleSelect');
    
    if (courseProgramSelect) {
        courseProgramSelect.addEventListener('change', function() {
            const programId = this.value;
            if (programId) {
                courseModuleSelect.disabled = false;
                loadModulesForProgram(programId, 'courseModuleSelect');
            } else {
                courseModuleSelect.disabled = true;
                courseModuleSelect.innerHTML = '<option value="">-- Select Module --</option>';
            }
        });
    }
    
    // Handle course form submission
    const addCourseForm = document.getElementById('addCourseForm');
    if (addCourseForm) {
        addCourseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Course created successfully!');
                    addCourseModalBg.classList.remove('show');
                    location.reload();
                } else {
                    alert('Error creating course: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating course. Please try again.');
            });
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/professor/modules/index.blade.php ENDPATH**/ ?>