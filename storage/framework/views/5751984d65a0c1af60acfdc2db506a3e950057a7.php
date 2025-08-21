<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'ARTC - Academic Resource and Training Center'); ?></title>
    
    <?php
        // Proper authentication logic that matches student dashboard
        $user = null;
        $isLoggedIn = false;
        
        // Check if user is logged in via Laravel Auth
        if (Auth::check()) {
            $user = Auth::user();
            $isLoggedIn = true;
        }
        // Check if user is logged in via session (for student authentication)
        elseif (session('user_id') && session('user_role')) {
            $user = (object) [
                'id' => session('user_id'),
                'name' => session('user_name') ?? session('user_firstname') . ' ' . session('user_lastname'),
                'role' => session('user_role'),
                'email' => session('user_email')
            ];
            $isLoggedIn = true;
        }
        
        // Force student role if session indicates student
        if ($isLoggedIn && session('user_role') === 'student') {
            $user->role = 'student';
        }
    ?>

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        window.myId = <?php echo json_encode($isLoggedIn && $user ? $user->id : null, 15, 512) ?>;
        window.myName = <?php echo json_encode($isLoggedIn && $user ? $user->name : 'Guest', 15, 512) ?>;
        window.isAuthenticated = <?php echo json_encode($isLoggedIn && (bool) $user, 15, 512) ?>;
        window.userRole = <?php echo json_encode($isLoggedIn && $user ? $user->role : 'guest', 15, 512) ?>;
        window.csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
        
        // Global chat state
        window.currentChatType = null;
        window.currentChatUser = null;
        
        // Make variables available without window prefix
        var myId = window.myId;
        var myName = window.myName;
        var isAuthenticated = window.isAuthenticated;
        var userRole = window.userRole;
        var csrfToken = window.csrfToken;
        var currentChatType = window.currentChatType;
        var currentChatUser = window.currentChatUser;
        
        console.log('App Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/homepage/homepage.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/student/student-navbar.css')); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
            min-height: 100vh;
        }
        
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
        }
        
        .footer {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        /* Override student navbar for app layout */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        /* Hide sidebar toggle button for app layout */
        .sidebar-toggle-btn {
            display: none !important;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Student Navbar -->
    <header class="main-header">
        <div class="header-left">
            <a href="<?php echo e(route('home')); ?>" class="brand-link">
                <img src="<?php echo e(asset('images/ARTC_logo.png')); ?>" alt="Logo">
                <div class="brand-text">
                    Ascendo Review<br>and Training Center
                </div>
            </a>
        </div>

        <div class="header-search">
            <?php echo $__env->make('components.student-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <div class="header-right">
            <span class="notification-icon chat-trigger"
                  data-bs-toggle="offcanvas"
                  data-bs-target="#chatOffcanvas"
                  aria-label="Open chat"
                  role="button">
                <i class="bi bi-chat-dots"></i>
            </span>
            <span class="profile-icon">
                <?php
                    $student = \App\Models\Student::where('user_id', session('user_id'))->first();
                    $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
                ?>
                
                <?php if($profilePhoto): ?>
                    <img src="<?php echo e(asset('storage/profile-photos/' . $profilePhoto)); ?>" 
                         alt="Profile" 
                         class="navbar-profile-image">
                <?php else: ?>
                    <div class="navbar-profile-placeholder">
                        <?php echo e(substr(session('user_firstname', 'U'), 0, 1)); ?><?php echo e(substr(session('user_lastname', 'U'), 0, 1)); ?>

                    </div>
                <?php endif; ?>
            </span>
        </div>
    </header>
    
    <?php echo $__env->make('components.global-chat', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Main Content -->
    <main class="main-content flex-grow-1">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-2">ARTC</h6>
                    <p class="text-muted small mb-0">Academic Resource and Training Center</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-0">&copy; <?php echo e(date('Y')); ?> ARTC. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\layouts\app.blade.php ENDPATH**/ ?>