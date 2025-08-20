<header class="main-header">
  <div class="header-left">
    <a href="<?php echo e(route('home')); ?>" class="brand-link">
      <?php
        $brandingSettings = \App\Helpers\UiSettingsHelper::getSection('navbar');
        $logoUrl = $brandingSettings['brand_logo'] ?? null;
        $brandName = $brandingSettings['brand_name'] ?? 'Ascendo Review and Training Center';
      ?>
      
      <?php if($logoUrl): ?>
        <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($logoUrl)); ?>" alt="Logo">
      <?php else: ?>
        <img src="<?php echo e(asset('images/ARTC_logo.png')); ?>" alt="Logo">
      <?php endif; ?>
      <div class="brand-text">
        <?php echo e(str_replace(' and ', '<br>and ', $brandName)); ?>

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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/components/student-navbar.blade.php ENDPATH**/ ?>