<?php
    // Ensure auth context variables exist before head is rendered
    $user = Auth::guard('smartprep_admin')->user() ?: Auth::guard('smartprep')->user() ?: Auth::user();
    $isLoggedIn = Auth::guard('smartprep_admin')->check() || Auth::guard('smartprep')->check() || Auth::check();
    $userRole = 'guest';
    if ($isLoggedIn && $user) {
        if (Auth::guard('smartprep_admin')->check()) {
            $userRole = 'admin';
        } elseif (Auth::guard('smartprep')->check()) {
            $userRole = $user->role ?? 'user';
        } else {
            $userRole = $user->role ?? 'user';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Customize Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- App context for JS -->
    <meta name="x-my-id" content="<?php echo e($isLoggedIn && isset($user) ? $user->id : ''); ?>">
    <meta name="x-my-name" content="<?php echo e($isLoggedIn && isset($user) ? ($user->name ?? 'User') : 'Guest'); ?>">
    <meta name="x-is-authenticated" content="<?php echo e($isLoggedIn && isset($user) ? '1' : '0'); ?>">
    <meta name="x-user-role" content="<?php echo e($userRole ?? 'guest'); ?>">
    
    <!-- Include the exact same styles as admin settings -->
    <?php echo $__env->make('smartprep.dashboard.partials.customize-styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>SmartPrep Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('smartprep.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('smartprep.dashboard.customize')); ?>">
                            <i class="fas fa-paint-brush me-2"></i>Customize Website
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Website Selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-2"></i>
                            <?php echo e($selectedWebsite ? $selectedWebsite->name : 'Select Website'); ?>

                        </a>
                        <ul class="dropdown-menu">
                            <?php $__empty_1 = true; $__currentLoopData = $activeWebsites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $website): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li>
                                    <a class="dropdown-item <?php echo e(request('website') == $website->id ? 'active' : ''); ?>" 
                                       href="<?php echo e(route('smartprep.dashboard.customize', ['website' => $website->id])); ?>">
                                        <i class="fas fa-globe me-2"></i><?php echo e($website->name); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li><span class="dropdown-item text-muted">No websites found</span></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="openCreateWebsite()">
                                    <i class="fas fa-plus me-2"></i>Create New Website
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo e($user->name ?? 'User'); ?>

                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(route('smartprep.dashboard')); ?>">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('smartprep.logout')); ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if(!$selectedWebsite): ?>
        <!-- No website selected - show selection prompt -->
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-globe fa-3x text-primary mb-3"></i>
                            <h3>Select a Website to Customize</h3>
                            <p class="text-muted mb-4">Choose an existing website or create a new one to start customizing your settings.</p>
                            
                            <?php if($activeWebsites->count() > 0): ?>
                                <div class="row">
                                    <?php $__currentLoopData = $activeWebsites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $website): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo e($website->name); ?></h5>
                                                    <p class="card-text text-muted"><?php echo e($website->domain); ?></p>
                                                    <a href="<?php echo e(route('smartprep.dashboard.customize', ['website' => $website->id])); ?>" 
                                                       class="btn btn-primary">
                                                        <i class="fas fa-edit me-2"></i>Customize
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <button class="btn btn-success btn-lg" onclick="openCreateWebsite()">
                                    <i class="fas fa-plus me-2"></i>Create New Website
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Website selected - show customization interface -->
        <?php echo $__env->make('smartprep.dashboard.partials.customize-interface', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Create new website function
        function openCreateWebsite(){
            const name = prompt('Enter new website name');
            if(!name) return;
            const form = document.createElement('form');
            form.method='POST';
            form.action="<?php echo e(route('smartprep.dashboard.websites.store')); ?>";
            form.innerHTML = `<?php echo csrf_field(); ?><input type="hidden" name="name" value="${name.replace(/"/g,'&quot;')}">`;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <?php if($selectedWebsite): ?>
        <?php echo $__env->make('smartprep.dashboard.partials.customize-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/customize-website-complete.blade.php ENDPATH**/ ?>