<div
    <?php echo e($attributes->class([
            'mx-auto my-6 flex flex-col items-center justify-center space-y-4 bg-white text-center',
            'dark:bg-gray-800' => config('notifications.dark_mode'),
        ])); ?>

>
    <div
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-500',
            'dark:bg-gray-700' => config('notifications.dark_mode'),
        ]) ?>"
    >
        <?php if (isset($component)) { $__componentOriginalcd9972c8156dfa6e5fd36675ca7bf5f21b506e2e = $component; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('heroicon-o-bell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd9972c8156dfa6e5fd36675ca7bf5f21b506e2e)): ?>
<?php $component = $__componentOriginalcd9972c8156dfa6e5fd36675ca7bf5f21b506e2e; ?>
<?php unset($__componentOriginalcd9972c8156dfa6e5fd36675ca7bf5f21b506e2e); ?>
<?php endif; ?>
    </div>

    <div class="max-w-md space-y-1">
        <h2
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'text-lg font-bold tracking-tight',
                'dark:text-white' => config('notifications.dark_mode'),
            ]) ?>"
        >
            <?php echo e(__('notifications::database.modal.empty.heading')); ?>

        </h2>

        <p
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'whitespace-normal text-sm font-medium text-gray-500',
                'dark:text-gray-400' => config('notifications.dark_mode'),
            ]) ?>"
        >
            <?php echo e(__('notifications::database.modal.empty.description')); ?>

        </p>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\vendor\filament\notifications\resources\views\components\database\modal\empty-state.blade.php ENDPATH**/ ?>