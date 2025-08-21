<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'notifications',
    'unreadNotificationsCount',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'notifications',
    'unreadNotificationsCount',
]); ?>
<?php foreach (array_filter(([
    'notifications',
    'unreadNotificationsCount',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div <?php echo e($attributes); ?>>
    <?php if($notifications->count()): ?>
        <div class="mt-2 text-sm">
            <?php if($unreadNotificationsCount): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'notifications::components.link','data' => ['wire:click' => 'markAllDatabaseNotificationsAsRead','color' => 'secondary','tag' => 'button','tabindex' => '-1','wire:target' => 'markAllDatabaseNotificationsAsRead','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-70 cursor-wait']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('notifications::link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'markAllDatabaseNotificationsAsRead','color' => 'secondary','tag' => 'button','tabindex' => '-1','wire:target' => 'markAllDatabaseNotificationsAsRead','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-70 cursor-wait']); ?>
                    <?php echo e(__('notifications::database.modal.buttons.mark_all_as_read.label')); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

                <span>&bull;</span>
            <?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'notifications::components.link','data' => ['wire:click' => 'clearDatabaseNotifications','xOn:click' => 'close()','color' => 'secondary','tag' => 'button','tabindex' => '-1','wire:target' => 'clearDatabaseNotifications','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-70 cursor-wait']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('notifications::link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'clearDatabaseNotifications','x-on:click' => 'close()','color' => 'secondary','tag' => 'button','tabindex' => '-1','wire:target' => 'clearDatabaseNotifications','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-70 cursor-wait']); ?>
                <?php echo e(__('notifications::database.modal.buttons.clear.label')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\vendor\filament\notifications\resources\views\components\database\modal\actions.blade.php ENDPATH**/ ?>