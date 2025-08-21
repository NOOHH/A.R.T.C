

<?php $__env->startSection('title', 'Package Management'); ?>

<?php $__env->startSection('head'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/admin/admin-packages.css')); ?>">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="page-id" content="admin-packages">
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  /* Bootstrap 5 Admin Dashboard Styling */
  .border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
  }
  
  .border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
  }
  
  .border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
  }
  
  .border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
  }
  
  .text-xs {
    font-size: 0.7rem;
  }
  
  .text-gray-300 {
    color: #dddfeb !important;
  }
  
  .text-gray-500 {
    color: #858796 !important;
  }
  
  .text-gray-600 {
    color: #6e707e !important;
  }
  
  .text-gray-800 {
    color: #5a5c69 !important;
  }
  
  .font-weight-bold {
    font-weight: 700 !important;
  }
  
  .shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
  }
  
  .card {
    transition: all 0.3s;
  }
  
  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2) !important;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .container-fluid {
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }
  }

  /* Modal styling */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    backdrop-filter: blur(5px);
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  }

  .modal-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
  }

  .modal-header h2 {
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
  }

  /* Form styling */
  .form-group {
    margin-bottom: 25px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 1rem;
  }

  .form-group label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }

  /* Modal actions */
  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f8f9fa;
  }

  .btn-primary, .btn-secondary {
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
  }

  .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
  }
  
  /* Package item styling */
  .package-item {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
  }

  .package-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
  }

  .package-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    position: relative;
  }

  .package-name {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    text-align: center;
  }

  .package-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
  }

  .package-content {
    padding: 25px;
  }

  .package-description {
    color: #6c757d;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 20px;
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Package details grid */
  .package-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
  }

  .package-detail {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e9ecef;
  }

  .package-detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .package-detail-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
  }

  .package-price {
    font-size: 2rem;
    font-weight: 800;
    color: #28a745;
    text-align: center;
    margin-bottom: 25px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
  }

  /* Action buttons */
  .package-actions {
    display: flex;
    gap: 12px;
  }

  .btn-edit, .btn-delete {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s ease;
  }

  .btn-edit {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
  }

  .btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
  }

  .btn-delete {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220,53,69,0.3);
  }

  .btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220,53,69,0.4);
  }

  /* Empty state */
  .no-packages {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    border-radius: 20px;
    color: #6c757d;
  }

  .no-packages i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
  }

  .no-packages h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
  }

  /* Modal styling */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    backdrop-filter: blur(5px);
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  }

  .modal-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
  }

  .modal-header h2 {
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
  }

  /* Form styling */
  .form-group {
    margin-bottom: 25px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 1rem;
  }

  .form-group label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  /* Checkbox group styling */
  .checkbox-group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
  }

  .checkbox-option {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
    flex: 1;
  }

  .checkbox-option:hover {
    border-color: #667eea;
    background: #f0f3ff;
  }

  .checkbox-option input[type="radio"] {
    margin-right: 10px;
    transform: scale(1.2);
    accent-color: #667eea;
  }

  .checkbox-option label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
    color: #495057;
    display: flex;
    align-items: center;
  }

  .checkbox-option input[type="radio"]:checked + label {
    color: #667eea;
    font-weight: 600;
  }

  .checkbox-option:has(input[type="radio"]:checked) {
    border-color: #667eea;
    background: #f0f3ff;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }

  /* Selection sections */
  .selection-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
  }

  .selection-section h4 {
    color: #495057;
    margin-bottom: 15px;
    font-weight: 600;
  }

  .selection-info {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #1976d2;
  }

  .checkboxes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
    margin-top: 15px;
  }

  .checkbox-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }

  .checkbox-item:hover {
    background: #f8f9fa;
    border-color: #667eea;
  }

  .checkbox-item input[type="checkbox"] {
    width: auto;
    margin: 0;
    accent-color: #667eea;
  }

  .checkbox-item label {
    margin: 0;
    font-weight: 500;
    cursor: pointer;
    flex: 1;
  }

  .course-count {
    background: #fff3cd;
    color: #856404;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  /* Selected items display */
  .selected-display {
    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
    padding: 20px;
    border-radius: 12px;
    margin-top: 15px;
    border: 1px solid #c3e6cb;
  }

  .selected-display h5 {
    color: #155724;
    margin-bottom: 10px;
    font-weight: 600;
  }

  .selected-badge {
    display: inline-block;
    background: #28a745;
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin: 3px;
  }

  /* Modal actions */
  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f8f9fa;
  }

  .btn-primary, .btn-secondary {
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
  }

  .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
  }

  /* Loading state */
  .loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    color: #6c757d;
  }

  .loading i {
    animation: spin 1s linear infinite;
    margin-right: 10px;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* Alert styling */
  .alert {
    border-radius: 12px;
    margin-bottom: 20px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }

  /* Responsive design */
  @media (max-width: 768px) {
    .packages-header {
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }
    
    .packages-header h1 {
      font-size: 2rem;
    }
    
    .package-list {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    
    .modal-content {
      padding: 30px 20px;
      margin: 20px;
    }
    
    .modal-actions {
      flex-direction: column;
    }
    
    .form-row {
      grid-template-columns: 1fr;
    }
    
    .checkboxes-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 480px) {
    .analytics-grid {
      grid-template-columns: 1fr;
    }
    
    .package-details {
      grid-template-columns: 1fr;
    }
  }

  
</style>


</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-box-open me-2"></i>Package Management
                </h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="fas fa-plus me-2"></i>Add New Package
                </button>
            </div>

            <!-- Alerts -->
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Analytics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Packages
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo e(count($packages)); ?>

                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Active Enrollments
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo e($packages->sum('enrollments_count') ?? 0); ?>

                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Value
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?php echo e(number_format($packages->sum('amount') ?? 0, 2)); ?>

                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Modular Packages
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo e($packages->where('package_type', 'modular')->count()); ?>

                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Package List</h6>
                </div>
                <div class="card-body">
                    <?php if(count($packages) > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"><?php echo e($package->package_name); ?></h5>
                                            <span class="badge bg-<?php echo e($package->package_type === 'modular' ? 'warning' : 'primary'); ?>">
                                                <?php echo e(ucfirst($package->package_type ?? 'Standard')); ?>

                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-muted small mb-3">
                                                <?php echo e(Str::limit($package->description ?? 'No description available', 100)); ?>

                                            </p>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="text-xs text-muted">TYPE</div>
                                                        <div class="small fw-bold"><?php echo e(ucfirst($package->package_type ?? 'Standard')); ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-xs text-muted">SELECTION</div>
                                                    <div class="small fw-bold"><?php echo e(ucfirst($package->selection_type ?? 'Module')); ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="text-xs text-muted">COUNT MODE</div>
                                                        <div class="small fw-bold">
                                                            <?php if($package->selection_mode === 'courses'): ?>
                                                                <i class="fas fa-book me-1"></i>Course Based
                                                            <?php else: ?>
                                                                <i class="fas fa-layer-group me-1"></i>Module Based
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-xs text-muted">
                                                        <?php if($package->selection_mode === 'courses'): ?>
                                                            COURSE COUNT
                                                        <?php else: ?>
                                                            MODULE COUNT
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="small fw-bold">
                                                        <?php if($package->selection_mode === 'courses'): ?>
                                                            <?php echo e($package->course_count ?? 'All'); ?>

                                                        <?php else: ?>
                                                            <?php echo e($package->module_count ?? 'All'); ?>

                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mb-3">
                                                <h4 class="text-success fw-bold">₱<?php echo e(number_format($package->amount ?? 0, 2)); ?></h4>
                                            </div>
                                            <?php
                                                $periodParts = [];
                                                if (!empty($package->access_period_years)) $periodParts[] = $package->access_period_years . ' Year' . ($package->access_period_years > 1 ? 's' : '');
                                                if (!empty($package->access_period_months)) $periodParts[] = $package->access_period_months . ' Month' . ($package->access_period_months > 1 ? 's' : '');
                                                if (!empty($package->access_period_days)) $periodParts[] = $package->access_period_days . ' Day' . ($package->access_period_days > 1 ? 's' : '');
                                            ?>
                                            <?php if(count($periodParts) > 0): ?>
                                                <div class="text-center mb-2">
                                                    <span class="badge bg-info text-dark">Access Period: <?php echo e(implode(' ', $periodParts)); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="d-grid gap-2">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="editPackage(<?php echo e($package->package_id); ?>)">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deletePackage(<?php echo e($package->package_id); ?>)">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No packages found</h5>
                            <p class="text-gray-500">Create your first package to get started!</p>
                            <button class="btn btn-primary" onclick="showAddModal()">
                                <i class="fas fa-plus me-2"></i>Add New Package
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal" id="addPackageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle me-2"></i>Create New Package</h2>
        </div>
        <form id="addPackageForm" method="POST" action="<?php echo e(route('admin.packages.store')); ?>">
            <?php echo csrf_field(); ?>
            
            <!-- Basic Information -->
            <div class="form-group">
                <label for="package_name" class="required">Package Name</label>
                <input type="text" id="package_name" name="package_name" required placeholder="Enter package name">
            </div>
            
            <div class="form-group">
                <label for="description" class="required">Description</label>
                <textarea id="description" name="description" rows="3" required placeholder="Enter package description"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="package_type" class="required">Package Type</label>
                    <select id="package_type" name="package_type" required onchange="handlePackageTypeChange()">
                        <option value="">Select Package Type</option>
                        <option value="full">Full Enrollment</option>
                        <option value="modular">Modular Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount" class="required">Price (₱)</label>
                    <input type="number" id="amount" name="amount" min="0" step="0.01" required placeholder="0.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="access_period_days">Days</label>
                    <input type="number" id="access_period_days" name="access_period_days" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="access_period_months">Months</label>
                    <input type="number" id="access_period_months" name="access_period_months" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="access_period_years">Years</label>
                    <input type="number" id="access_period_years" name="access_period_years" min="0" placeholder="0">
                </div>
            </div>
            <small class="form-text text-muted mb-3">Set the access period for this package. The period starts after payment approval. Leave blank for unlimited access.</small>

            <div class="form-row">
                <div class="form-group">
                    <label for="program_id" class="required">Program</label>
                    <select id="program_id" name="program_id" required onchange="loadProgramData()">
                        <option value="">Select Program</option>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- Modular Package Options -->
            <div class="form-group" id="selectionTypeGroup" style="display: none;">
                <label for="selection_type" class="required">Selection Type</label>
                <select id="selection_type" name="selection_type" onchange="handleSelectionTypeChange()">
                    <option value="">Select Type</option>
                    <option value="module">Module Selection</option>
                    <option value="course">Course Selection</option>
                    <option value="both">Both</option>
                </select>
            </div>

            <div class="form-group" id="selectionModeGroup" style="display: none;">
                <label class="required">Count Based On</label>
                <div class="checkbox-group">
                    <div class="checkbox-option">
                        <input type="radio" id="selection_mode_modules" name="selection_mode" value="modules" checked onchange="handleSelectionModeChange()">
                        <label for="selection_mode_modules">
                            <i class="fas fa-layer-group me-2"></i>Module Count
                        </label>
                    </div>
                    <div class="checkbox-option">
                        <input type="radio" id="selection_mode_courses" name="selection_mode" value="courses" onchange="handleSelectionModeChange()">
                        <label for="selection_mode_courses">
                            <i class="fas fa-book me-2"></i>Course Count
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row" id="countLimitsGroup" style="display: none;">
                <div class="form-group">
                    <label for="module_count">Maximum Modules (Optional)</label>
                    <input type="number" id="module_count" name="module_count" min="1" max="50" placeholder="Leave empty for unlimited">
                    <small class="form-text text-muted">Set module limit (optional)</small>
                </div>
                <div class="form-group">
                    <label for="course_count">Maximum Courses (Optional)</label>
                    <input type="number" id="course_count" name="course_count" min="1" max="50" placeholder="Leave empty for unlimited">
                    <small class="form-text text-muted">Set course limit (optional)</small>
                </div>
            </div>

            <!-- Module Selection -->
            <div class="selection-section" id="moduleSelection" style="display: none;">
                <h4><i class="fas fa-layer-group me-2"></i>Select Modules</h4>
                <div class="selection-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Choose specific modules for this package. Students will only access these modules.
                </div>
                <div class="loading" id="moduleLoading" style="display: none;">
                    <i class="fas fa-spinner"></i>Loading modules...
                </div>
                <div class="checkboxes-grid" id="moduleCheckboxes">
                    <!-- Modules will be loaded here -->
                </div>
                <div class="selected-display" id="selectedModulesDisplay" style="display: none;">
                    <h5>Selected Modules: <span id="selectedModulesCount">0</span></h5>
                    <div id="selectedModulesList"></div>
                </div>
            </div>

            <!-- Course Selection -->
            <div class="selection-section" id="courseSelection" style="display: none;">
                <h4><i class="fas fa-book me-2"></i>Select Courses</h4>
                <div class="selection-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Choose specific courses for this package. Students will only access these courses.
                </div>
                <div class="loading" id="courseLoading" style="display: none;">
                    <i class="fas fa-spinner"></i>Loading courses...
                </div>
                <div class="checkboxes-grid" id="courseCheckboxes">
                    <!-- Courses will be loaded here -->
                </div>
                <div class="selected-display" id="selectedCoursesDisplay" style="display: none;">
                    <h5>Selected Courses: <span id="selectedCoursesCount">0</span></h5>
                    <div id="selectedCoursesList"></div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeAddModal()">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save me-1"></i>Create Package
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal" id="editPackageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit me-2"></i>Edit Package</h2>
        </div>
        <form id="editPackageForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <!-- Same form structure as add modal but with edit_ prefixes -->
            <div class="form-group">
                <label for="edit_package_name" class="required">Package Name</label>
                <input type="text" id="edit_package_name" name="package_name" required>
            </div>
            
            <div class="form-group">
                <label for="edit_description" class="required">Description</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_package_type" class="required">Package Type</label>
                    <select id="edit_package_type" name="package_type" required onchange="handleEditPackageTypeChange()">
                        <option value="full">Full Enrollment</option>
                        <option value="modular">Modular Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_amount" class="required">Price (₱)</label>
                    <input type="number" id="edit_amount" name="amount" min="0" step="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_program_id" class="required">Program</label>
                    <select id="edit_program_id" name="program_id" required onchange="loadEditProgramData()">
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->program_id); ?>"><?php echo e($program->program_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- Edit Modular Package Options -->
            <div class="form-group" id="editSelectionTypeGroup" style="display: none;">
                <label for="edit_selection_type" class="required">Selection Type</label>
                <select id="edit_selection_type" name="selection_type" onchange="handleEditSelectionTypeChange()">
                    <option value="">Select Type</option>
                    <option value="module">Module Selection</option>
                    <option value="course">Course Selection</option>
                    <option value="both">Both</option>
                </select>
            </div>

            <div class="form-group" id="editSelectionModeGroup" style="display: none;">
                <label class="required">Count Based On</label>
                <div class="checkbox-group">
                    <div class="checkbox-option">
                        <input type="radio" id="edit_selection_mode_modules" name="selection_mode" value="modules" checked onchange="handleEditSelectionModeChange()">
                        <label for="edit_selection_mode_modules">
                            <i class="fas fa-layer-group me-2"></i>Module Count
                        </label>
                    </div>
                    <div class="checkbox-option">
                        <input type="radio" id="edit_selection_mode_courses" name="selection_mode" value="courses" onchange="handleEditSelectionModeChange()">
                        <label for="edit_selection_mode_courses">
                            <i class="fas fa-book me-2"></i>Course Count
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_access_period_days">Days</label>
                    <input type="number" id="edit_access_period_days" name="access_period_days" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="edit_access_period_months">Months</label>
                    <input type="number" id="edit_access_period_months" name="access_period_months" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="edit_access_period_years">Years</label>
                    <input type="number" id="edit_access_period_years" name="access_period_years" min="0" placeholder="0">
                </div>
            </div>
            <small class="form-text text-muted mb-3">Set the access period for this package. The period starts after payment approval. Leave blank for unlimited access.</small>



            <!-- Edit Module Selection -->
            <div class="selection-section" id="editModuleSelection" style="display: none;">
                <h4><i class="fas fa-layer-group me-2"></i>Select Modules</h4>
                <div class="selection-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Choose specific modules for this package.
                </div>
                <div class="loading" id="editModuleLoading" style="display: none;">
                    <i class="fas fa-spinner"></i>Loading modules...
                </div>
                <div class="checkboxes-grid" id="editModuleCheckboxes">
                    <!-- Modules will be loaded here -->
                </div>
                <div class="selected-display" id="editSelectedModulesDisplay" style="display: none;">
                    <h5>Selected Modules: <span id="editSelectedModulesCount">0</span></h5>
                    <div id="editSelectedModulesList"></div>
                </div>
            </div>

            <!-- Edit Course Selection -->
            <div class="selection-section" id="editCourseSelection" style="display: none;">
                <h4><i class="fas fa-book me-2"></i>Select Courses</h4>
                <div class="selection-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Choose specific courses for this package.
                </div>
                <div class="loading" id="editCourseLoading" style="display: none;">
                    <i class="fas fa-spinner"></i>Loading courses...
                </div>
                <div class="checkboxes-grid" id="editCourseCheckboxes">
                    <!-- Courses will be loaded here -->
                </div>
                <div class="selected-display" id="editSelectedCoursesDisplay" style="display: none;">
                    <h5>Selected Courses: <span id="editSelectedCoursesCount">0</span></h5>
                    <div id="editSelectedCoursesList"></div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save me-1"></i>Update Package
                </button>
            </div>
        </form>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-packages\admin-packages.blade.php ENDPATH**/ ?>