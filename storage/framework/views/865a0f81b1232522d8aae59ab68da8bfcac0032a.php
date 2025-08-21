<?php
    // Get brand name from settings if available, otherwise use default
    $brandName = $settings['navbar']['brand_name'] ?? 
                 $navbarBrandName ?? 
                 'Ascendo Review & Training Center';
    
    // Get brand logo from settings if available
    $brandLogo = $settings['navbar']['brand_logo'] ?? null;
    $defaultLogo = asset('images/ARTC_logo.png');
?>

<header class="main-header">
  <div class="header-left">
    <a href="<?php echo e(route('home')); ?>" class="brand-link">
      <?php if($brandLogo): ?>
        <img src="<?php echo e(asset($brandLogo)); ?>" 
             alt="<?php echo e($brandName); ?>" 
             onerror="this.src='<?php echo e($defaultLogo); ?>'">
      <?php else: ?>
        <img src="<?php echo e($defaultLogo); ?>" alt="<?php echo e($brandName); ?>">
      <?php endif; ?>
      <div class="brand-text">
        <?php echo e($brandName); ?>

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
        // Check if this is preview mode
        $isPreview = request()->has('preview') || request()->query('preview') === 'true';
        
        if ($isPreview) {
          // Use mock data for preview mode
          $profilePhoto = null;
        } else {
          // Only query database if not in preview mode
          try {
            $student = \App\Models\Student::where('user_id', session('user_id'))->first();
            $profilePhoto = $student && $student->profile_photo ? $student->profile_photo : null;
          } catch (\Exception $e) {
            // If there's an error (e.g., table doesn't exist), use null
            $profilePhoto = null;
          }
        }
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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/components/student-navbar.blade.php ENDPATH**/ ?>