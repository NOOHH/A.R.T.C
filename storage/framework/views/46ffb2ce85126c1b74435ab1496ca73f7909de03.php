
<header class="main-header">
    <div class="header-left">
        <!-- Brand Logo and Text -->
        <div class="brand-container d-flex align-items-center gap-3">
            <img src="<?php echo e(asset('images/ARTC_logo.png')); ?>" alt="A.R.T.C" class="brand-logo">
            <div class="brand-text-area d-flex flex-column justify-content-center">
                <span class="brand-text fw-bold">Ascendo Review &amp; Training Center</span>
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