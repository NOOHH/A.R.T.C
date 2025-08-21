<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon-button','data' => ['label' => __('filament::layout.buttons.database_notifications.label'),'icon' => 'heroicon-o-bell','color' => $unreadNotificationsCount ? 'primary' : 'secondary','indicator' => $unreadNotificationsCount,'class' => '-mr-1 ml-4 rtl:-ml-1 rtl:mr-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament::layout.buttons.database_notifications.label')),'icon' => 'heroicon-o-bell','color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($unreadNotificationsCount ? 'primary' : 'secondary'),'indicator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($unreadNotificationsCount),'class' => '-mr-1 ml-4 rtl:-ml-1 rtl:mr-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\vendor\filament\filament\resources\views\components\layouts\app\topbar\database-notifications-trigger.blade.php ENDPATH**/ ?>