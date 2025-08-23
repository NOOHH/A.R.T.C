

<?php $__env->startSection('title', 'My Programs'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Modern Programs Page Styles */
.programs-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
    position: relative;
    overflow: hidden;
}

.programs-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.program-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
    height: 100%;
}

.program-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
    border-color: rgba(37, 99, 235, 0.3);
}

.program-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #7c3aed, #06b6d4);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.program-card:hover::before {
    transform: scaleX(1);
}

.program-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    margin-bottom: 1rem;
}

.program-stats {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
}

.stat-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.action-button {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: white;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, #1d4ed8, #6d28d9);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.5);
    color: white;
}

.btn-secondary-modern {
    background: rgba(100, 116, 139, 0.1);
    color: #475569;
    border: 1px solid rgba(100, 116, 139, 0.2);
}

.btn-secondary-modern:hover {
    background: rgba(100, 116, 139, 0.2);
    color: #334155;
    transform: translateY(-2px);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.empty-state-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
}

.modal-content-modern {
    border-radius: 20px;
    border: none;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
}

.modal-header-modern {
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: white;
    border: none;
    padding: 1.5rem;
}

.form-control-modern {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Animation Classes */
.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stagger-animation {
    animation-delay: calc(var(--animation-order) * 0.1s);
}
</style>

<div class="container-fluid">
    <!-- Programs Header -->
    <div class="programs-header">
        <div class="position-relative">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="bi bi-mortarboard me-3"></i>My Programs
                    </h1>
                    <p class="lead mb-0 opacity-90">
                        Manage and oversee your assigned educational programs with comprehensive tools and insights.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-flex align-items-center bg-white bg-opacity-20 rounded-pill px-4 py-2">
                        <i class="bi bi-calendar-check me-2"></i>
                        <span class="fw-semibold"><?php echo e(now()->format('F Y')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($assignedPrograms->count() > 0): ?>
        <!-- Programs Grid -->
        <div class="row">
            <?php $__currentLoopData = $assignedPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-xl-4 mb-4 fade-in-up stagger-animation" style="--animation-order: <?php echo e($index); ?>">
                    <div class="program-card">
                        <div class="card-body p-4">
                            <!-- Program Icon & Title -->
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="program-icon">
                                    <i class="bi bi-mortarboard text-white fs-3"></i>
                                </div>
                            </div>

                            <h5 class="card-title fw-bold mb-3 text-dark"><?php echo e($program->program_name); ?></h5>
                            
                            <?php if($program->program_description): ?>
                                <p class="card-text text-muted mb-3">
                                    <?php echo e(Str::limit($program->program_description, 120)); ?>

                                </p>
                            <?php endif; ?>

                            <!-- Program Stats -->
                            <div class="program-stats">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="bi bi-people text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?php echo e($program->students->count()); ?> Students</div>
                                        <small class="text-muted">Enrolled in program</small>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="bi bi-book text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?php echo e($program->modules->count()); ?> Modules</div>
                                        <small class="text-muted">Course modules available</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-2 mt-4">
                                <div class="col-12">
                                    <?php
                                        // Check if we're in tenant preview mode
                                        $tenantSlug = request()->route('tenant') ?? session('preview_tenant');
                                        $routePrefix = $tenantSlug ? 'tenant.draft.' : '';
                                        $routeParams = $tenantSlug ? ['tenant' => $tenantSlug, 'program' => $program->program_id] : ['program' => $program->program_id];
                                        $programDetailsRoute = $tenantSlug ? $routePrefix . 'professor.programs' : 'professor.program.details';
                                    ?>
                                    <a href="<?php echo e(route($programDetailsRoute, $routeParams)); ?>" 
                                       class="btn btn-primary-modern action-button w-100">
                                        <i class="bi bi-eye me-2"></i>View Program Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <!-- Enhanced Empty State -->
        <div class="empty-state fade-in-up">
            <div class="empty-state-icon">
                <i class="bi bi-mortarboard" style="font-size: 3rem; color: #94a3b8;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-3">No Programs Assigned Yet</h3>
            <p class="text-muted mb-4 lead">
                You haven't been assigned to any educational programs at the moment. 
                Once you're assigned to programs, you'll be able to manage students, modules, and course content from here.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <button class="btn btn-primary-modern action-button">
                    <i class="bi bi-envelope me-2"></i>Contact Administrator
                </button>
                <button class="btn btn-secondary-modern action-button">
                    <i class="bi bi-question-circle me-2"></i>Learn More
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add staggered animation to cards
    const cards = document.querySelectorAll('.fade-in-up');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Enhanced hover effects
    const programCards = document.querySelectorAll('.program-card');
    programCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Button ripple effect
    const buttons = document.querySelectorAll('.action-button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Add ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/professor/programs.blade.php ENDPATH**/ ?>