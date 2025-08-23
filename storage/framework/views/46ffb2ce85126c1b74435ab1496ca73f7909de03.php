
<?php
    // Get brand name from tenant settings if available, otherwise use default
    $brandName = $settings['navbar']['brand_name'] ?? 
                 $navbarBrandName ?? 
                 'Ascendo Review & Training Center';
    
    // Get brand logo from tenant settings if available
    $brandLogo = $settings['navbar']['brand_logo'] ?? null;
    $defaultLogo = asset('images/ARTC_logo.png');
?>

<header class="main-header">
    <div class="header-left">
        <!-- Brand Logo and Text -->
        <div class="brand-container d-flex align-items-center gap-3">
            <?php if($brandLogo): ?>
                <img src="<?php echo e(asset($brandLogo)); ?>" 
                     alt="<?php echo e($brandName); ?>" 
                     class="brand-logo"
                     onerror="this.src='<?php echo e($defaultLogo); ?>'">
            <?php else: ?>
                <img src="<?php echo e($defaultLogo); ?>" alt="<?php echo e($brandName); ?>" class="brand-logo">
            <?php endif; ?>
            <div class="brand-text-area d-flex flex-column justify-content-center">
                <span class="brand-text fw-bold"><?php echo e($brandName); ?></span>
                <span class="brand-subtext text-muted">Professor Portal</span>
            </div>
        </div>
    </div>

    <div class="header-center">
        <!-- Universal Search -->
        <div class="search-container">
            <?php echo $__env->make('components.universal-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>

    <div class="header-right">
        <!-- Chat Icon Button -->
        <button class="btn btn-link p-0 ms-2" id="chatTriggerBtn" title="Open Chat" style="font-size: 1.5rem; color: #764ba2;">
            <i class="bi bi-chat-dots"></i>
        </button>
        
        <!-- Mobile Profile Icon -->
    </div>
</header>
  <?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/professor/professor-layouts/professor-header.blade.php ENDPATH**/ ?>