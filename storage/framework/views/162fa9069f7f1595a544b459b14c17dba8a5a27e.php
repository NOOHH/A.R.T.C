

<?php $__env->startSection('title', 'Form Preview - ' . ucfirst($programType)); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Form Preview - <?php echo e(ucfirst($programType)); ?> Program
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This is a preview of how the registration form will appear to students.
                    </div>

                    <form class="preview-form">
                        <?php echo csrf_field(); ?>
                        <?php if (isset($component)) { $__componentOriginal4e1a55167f3d054c665b5a3c991c5eb8a7f952a9 = $component; } ?>
<?php $component = App\View\Components\DynamicEnrollmentForm::resolve(['programType' => $programType] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dynamic-enrollment-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\DynamicEnrollmentForm::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e1a55167f3d054c665b5a3c991c5eb8a7f952a9)): ?>
<?php $component = $__componentOriginal4e1a55167f3d054c665b5a3c991c5eb8a7f952a9; ?>
<?php unset($__componentOriginal4e1a55167f3d054c665b5a3c991c5eb8a7f952a9); ?>
<?php endif; ?>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('admin.settings.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Settings
                        </a>
                        <div class="btn-group">
                            <a href="<?php echo e(route('admin.settings.form-requirements.preview', 'complete')); ?>" 
                               class="btn btn-outline-primary <?php echo e($programType === 'complete' ? 'active' : ''); ?>">
                                Complete Program
                            </a>
                            <a href="<?php echo e(route('admin.settings.form-requirements.preview', 'modular')); ?>" 
                               class="btn btn-outline-primary <?php echo e($programType === 'modular' ? 'active' : ''); ?>">
                                Modular Program
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.preview-form {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
}

.preview-form .form-group {
    margin-bottom: 1rem;
}

.preview-form .form-control {
    background: white;
    border: 1px solid #ced4da;
}

.preview-form .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin-dashboard.admin-dashboard-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\settings\form-preview.blade.php ENDPATH**/ ?>