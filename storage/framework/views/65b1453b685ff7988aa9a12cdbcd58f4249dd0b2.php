<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Website Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body {
            background: var(--gray-50);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            margin: 0;
            color: var(--gray-800);
        }
        
        /* Top Navigation */
        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand:hover {
            color: var(--secondary-color);
        }
        
        .navbar-nav .nav-link {
            color: var(--gray-600);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: 1px solid var(--gray-200);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link {
            color: var(--gray-600) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 6px;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gray-100);
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link.active {
            background: var(--primary-color);
            color: white !important;
        }
        
        /* Dashboard Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="rgba(255,255,255,0.05)"><circle cx="20" cy="20" r="2"/></g></svg>');
            opacity: 0.3;
        }
        
        .dashboard-header .container {
            position: relative;
            z-index: 2;
        }
        
        /* Request Cards */
        .request-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }
        
        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Status Badges */
        .badge-status {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .badge-pending {
            background: #fef5e7;
            color: #92400e;
        }
        
        .badge-approved {
            background: #f0fff4;
            color: #276749;
        }
        
        .badge-rejected {
            background: #fed7d7;
            color: #c53030;
        }
        
        /* Buttons */
        .btn-primary-custom {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-primary-custom:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-1px);
            color: white;
        }
        
        .btn-success-custom {
            background: var(--success-color);
            border: 1px solid var(--success-color);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-success-custom:hover {
            background: #2f855a;
            border-color: #2f855a;
            color: white;
        }
        
        .btn-danger-custom {
            background: var(--danger-color);
            border: 1px solid var(--danger-color);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-danger-custom:hover {
            background: #c53030;
            border-color: #c53030;
            color: white;
        }
        
        /* Filter Cards */
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-500);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>SmartPrep
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('smartprep.admin.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('smartprep.admin.website-requests')); ?>">
                            <i class="fas fa-clock me-2"></i>Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('smartprep.admin.clients')); ?>">
                            <i class="fas fa-users me-2"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('smartprep.admin.settings')); ?>">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>View Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('smartprep.logout')); ?>" class="d-inline w-100">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-2">
                        <i class="fas fa-clock me-3"></i>Website Requests
                    </h1>
                    <p class="mb-0 opacity-90 fs-5">Review and manage client website requests</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="d-flex flex-column align-items-lg-end">
                        <div class="text-white-50 mb-2"><?php echo e($requests->count()); ?> Total Requests</div>
                        <div class="fs-4 fw-bold"><?php echo e($requests->where('status', 'pending')->count()); ?> Pending</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-card mt-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Requests</h5>
                </div>
                <div class="col-md-4">
                    <form method="GET" action="<?php echo e(route('smartprep.admin.website-requests')); ?>">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?php echo e(request('status') == 'all' ? 'selected' : ''); ?>>All Requests</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                            <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <?php if($requests->count() > 0): ?>
            <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="request-card">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="fw-bold text-dark mb-1"><?php echo e($request->business_name); ?></h4>
                                <div class="text-muted">
                                    <i class="fas fa-user me-1"></i><?php echo e($request->user->name); ?> • 
                                    <i class="fas fa-envelope me-1"></i><?php echo e($request->contact_email ?? $request->user->email); ?> • 
                                    <i class="fas fa-calendar me-1"></i><?php echo e($request->created_at->format('M j, Y g:i A')); ?>

                                </div>
                            </div>
                            <span class="badge-status badge-<?php echo e($request->status); ?>">
                                <?php echo e(ucfirst($request->status)); ?>

                            </span>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <strong>Business Type:</strong> <?php echo e($request->business_type ?? 'Not specified'); ?>

                            </div>
                            <div class="col-md-6">
                                <strong>Preferred Domain:</strong> 
                                <?php if($request->domain_preference): ?>
                                    <code>/t/<?php echo e(Str::slug($request->domain_preference)); ?></code>
                                <?php else: ?>
                                    <span class="text-muted">Auto-generate</span>
                                <?php endif; ?>
                            </div>
                            <?php if($request->contact_phone): ?>
                            <div class="col-md-6">
                                <strong>Phone:</strong> <?php echo e($request->contact_phone); ?>

                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if($request->description): ?>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p class="mb-0 mt-1 text-muted"><?php echo e($request->description); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($request->template_data): ?>
                        <div class="mb-3">
                            <strong>Customization Data:</strong>
                            <div class="mt-1 p-2 bg-light rounded">
                                <small class="text-success"><i class="fas fa-check me-1"></i>Client has provided customization settings</small>
                                <button class="btn btn-sm btn-outline-primary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#customizationData<?php echo e($request->id); ?>">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </button>
                                <div class="collapse mt-2" id="customizationData<?php echo e($request->id); ?>">
                                    <?php
                                        $td = $request->template_data;
                                        $pretty = is_array($td)
                                            ? json_encode($td, JSON_PRETTY_PRINT)
                                            : json_encode(json_decode($td ?: '{}', true), JSON_PRETTY_PRINT);
                                    ?>
                                    <pre class="small bg-white p-2 rounded border"><?php echo e($pretty); ?></pre>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($request->admin_notes): ?>
                        <div class="mb-3">
                            <strong>Admin Notes:</strong>
                            <p class="mb-0 mt-1 text-muted"><?php echo e($request->admin_notes); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($request->client): ?>
                        <div class="mb-3">
                            <strong>Created Website:</strong>
                            <a href="/t/<?php echo e($request->client->slug); ?>" target="_blank" class="btn-primary-custom">
                                <i class="fas fa-external-link-alt me-1"></i>Visit /t/<?php echo e($request->client->slug); ?>

                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <?php if($request->status === 'pending'): ?>
                        <div class="d-flex flex-column gap-2">
                            <button type="button" class="btn-success-custom" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo e($request->id); ?>">
                                <i class="fas fa-check me-2"></i>Approve & Create Website
                            </button>
                            <button type="button" class="btn-danger-custom" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($request->id); ?>">
                                <i class="fas fa-times me-2"></i>Reject Request
                            </button>
                        </div>
                        <?php elseif($request->status === 'completed' && $request->client): ?>
                        <div class="d-flex flex-column gap-2">
                            <a href="/t/<?php echo e($request->client->slug); ?>" target="_blank" class="btn-primary-custom">
                                <i class="fas fa-external-link-alt me-2"></i>Visit Website
                            </a>
                            <a href="/t/<?php echo e($request->client->slug); ?>/admin/courses" target="_blank" class="btn-primary-custom">
                                <i class="fas fa-cog me-2"></i>Admin Panel
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <div class="modal fade" id="approveModal<?php echo e($request->id); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="<?php echo e(route('smartprep.admin.approve-request', $request)); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Approve Website Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to approve this request and create a website for <strong><?php echo e($request->business_name); ?></strong>?</p>
                                <div class="mb-3">
                                    <label class="form-label">Admin Notes (Optional)</label>
                                    <textarea class="form-control" name="admin_notes" rows="3" placeholder="Add any notes for the client..."></textarea>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This will create a new database and website at <code>/t/<?php echo e(Str::slug($request->business_name)); ?></code>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn-success-custom">
                                    <i class="fas fa-check me-2"></i>Approve & Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal<?php echo e($request->id); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="<?php echo e(route('smartprep.admin.reject-request', $request)); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="modal-header">
                                <h5 class="modal-title">Reject Website Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to reject the request for <strong><?php echo e($request->business_name); ?></strong>?</p>
                                <div class="mb-3">
                                    <label class="form-label">Reason for Rejection *</label>
                                    <textarea class="form-control" name="admin_notes" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn-danger-custom">
                                    <i class="fas fa-times me-2"></i>Reject Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($requests->links()); ?>

            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>No website requests yet</h4>
                <p class="text-muted">Requests will appear here when users submit them</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\smartprep\admin\website-requests.blade.php ENDPATH**/ ?>