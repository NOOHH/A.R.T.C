
<div id="permissions-settings" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-shield-alt me-2"></i>Permissions
    </h3>
    <p class="text-muted small mb-3">Configure access permissions for different user roles on your website.</p>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Permission Management:</strong> Control what features are available to directors and professors to customize the user experience on your training platform.
    </div>
    
    <div class="permission-overview">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
                        <h6>Director Access</h6>
                        <p class="text-muted small">Configure administrative features for directors</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="showSection('director-features')">
                            <i class="fas fa-cog me-1"></i>Configure
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chalkboard-teacher fa-2x text-success mb-2"></i>
                        <h6>Professor Access</h6>
                        <p class="text-muted small">Configure teaching features for professors</p>
                        <button class="btn btn-outline-success btn-sm" onclick="showSection('professor-features')">
                            <i class="fas fa-cog me-1"></i>Configure
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php echo $__env->make('smartprep.dashboard.partials.settings.director-features', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('smartprep.dashboard.partials.settings.professor-features', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/settings/advanced.blade.php ENDPATH**/ ?>