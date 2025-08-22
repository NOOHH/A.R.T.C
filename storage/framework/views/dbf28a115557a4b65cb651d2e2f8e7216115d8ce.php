<?php
    // Expect $website (Client model) or $client
    $site = $website ?? $client ?? null;
    if (!$site) return;
?>

<span class="<?php echo e($site->status_badge_class); ?>" style="font-size:0.8rem;"><?php echo e($site->status_label); ?></span>
<?php if(isset($showDomain) && $showDomain): ?>
    <small class="text-muted ms-2"><?php echo e($site->domain); ?></small>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/website-status-badge.blade.php ENDPATH**/ ?>