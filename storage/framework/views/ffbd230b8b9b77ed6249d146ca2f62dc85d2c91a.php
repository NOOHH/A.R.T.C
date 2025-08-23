<?php if(filled($brand = config('filament.brand'))): ?>
    <div
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'filament-brand text-xl font-bold leading-5 tracking-tight',
            'dark:text-white' => config('filament.dark_mode'),
        ]) ?>"
    >
        <?php echo e($brand); ?>

    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\vendor\filament\filament\resources\views/components/brand.blade.php ENDPATH**/ ?>