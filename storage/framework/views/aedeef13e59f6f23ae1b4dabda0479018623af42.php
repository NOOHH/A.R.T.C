<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($client->name); ?> - Professional Training Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/css/admin/artc-theme.css" rel="stylesheet">
    <style>
        .hero-section {
            background: var(--gradient-primary);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="50%" cy="50%" r="50%" fill="url(%23a)"/></svg>') center/cover;
            opacity: 0.1;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(102, 126, 234, 0.1);
            transition: var(--transition);
            margin-bottom: 2rem;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .stats-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .stats-label {
            color: #6c757d;
            font-weight: 600;
        }
        .program-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(102, 126, 234, 0.1);
            transition: var(--transition);
            height: 100%;
        }
        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .program-card .card-body {
            padding: 1.5rem;
        }
        .announcement-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary-color);
        }
        .announcement-urgent {
            border-left-color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="/t/<?php echo e($client->slug); ?>">
                <i class="fas fa-graduation-cap me-2"></i><?php echo e($client->name); ?>

            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/t/<?php echo e($client->slug); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/t/<?php echo e($client->slug); ?>/programs">Programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/t/<?php echo e($client->slug); ?>/admin/dashboard">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">SmartPrep Platform</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Welcome to <?php echo e($client->name); ?></h1>
                    <p class="lead mb-4">Your premier destination for professional training and certification preparation. Join thousands of successful students who have achieved their goals with our comprehensive programs.</p>
                    <div>
                        <a href="/t/<?php echo e($client->slug); ?>/programs" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-book me-2"></i>View Programs
                        </a>
                        <a href="/t/<?php echo e($client->slug); ?>/admin/dashboard" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-cog me-2"></i>Admin Portal
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-trophy" style="font-size: 8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo e($stats['total_programs']); ?></div>
                        <div class="stats-label">Programs</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo e($stats['total_courses']); ?></div>
                        <div class="stats-label">Courses</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo e($stats['total_modules']); ?></div>
                        <div class="stats-label">Modules</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo e($stats['active_students']); ?></div>
                        <div class="stats-label">Students</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Our Programs</h2>
                    <p class="lead text-muted">Discover our comprehensive training programs designed to help you achieve professional excellence</p>
                </div>
            </div>
            
            <?php if($programs->count() > 0): ?>
                <div class="row g-4">
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="program-card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-primary mb-3"><?php echo e($program->program_name); ?></h5>
                                <p class="card-text text-muted"><?php echo e(Str::limit($program->program_description ?? 'Comprehensive training program designed for professional development.', 100)); ?></p>
                                <a href="/t/<?php echo e($client->slug); ?>/programs/<?php echo e($program->program_id); ?>" class="btn btn-sp">
                                    <i class="fas fa-arrow-right me-2"></i>Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <?php if($programs->count() >= 6): ?>
                <div class="text-center mt-4">
                    <a href="/t/<?php echo e($client->slug); ?>/programs" class="btn btn-sp-outline btn-lg">
                        <i class="fas fa-list me-2"></i>View All Programs
                    </a>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-book text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h4 class="text-muted mt-3">No Programs Available</h4>
                    <p class="text-muted">Programs will be available soon. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Announcements Section -->
    <?php if($announcements->count() > 0): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h3 class="fw-bold mb-4 text-center">
                        <i class="fas fa-bullhorn me-2 text-primary"></i>Latest Announcements
                    </h3>
                    
                    <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="announcement-card <?php echo e(($announcement->type ?? null) === 'urgent' ? 'announcement-urgent' : ''); ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-2">
                                    <?php if(($announcement->type ?? null) === 'urgent'): ?>
                                        <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                    <?php endif; ?>
                                    <?php echo e($announcement->title); ?>

                                </h6>
                                <p class="mb-2"><?php echo e(Str::limit($announcement->content, 200)); ?></p>
                                <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($announcement->created_at)->format('M j, Y')); ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h5 class="fw-bold mb-3"><?php echo e($client->name); ?></h5>
                    <p class="text-light opacity-75">Professional training center dedicated to excellence in education and certification preparation.</p>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/t/<?php echo e($client->slug); ?>" class="text-light text-decoration-none opacity-75">Home</a></li>
                        <li class="mb-2"><a href="/t/<?php echo e($client->slug); ?>/programs" class="text-light text-decoration-none opacity-75">Programs</a></li>
                        <li class="mb-2"><a href="/t/<?php echo e($client->slug); ?>/admin/dashboard" class="text-light text-decoration-none opacity-75">Admin</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-light text-decoration-none opacity-75">SmartPrep Platform</a></li>
                        <li class="mb-2"><span class="text-light opacity-75">Powered by SmartPrep</span></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 opacity-75">&copy; <?php echo e(date('Y')); ?> <?php echo e($client->name); ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 opacity-75">Powered by <a href="/" class="text-white">SmartPrep Platform</a></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\tenant\artc\home.blade.php ENDPATH**/ ?>