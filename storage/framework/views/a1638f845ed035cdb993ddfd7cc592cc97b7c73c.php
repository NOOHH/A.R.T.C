<?php if(isset($menuData)): ?>
    <?php $__currentLoopData = $menuData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($menu['text'] === 'Student Enrollment'): ?>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Student Enrollment
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.batches.index')); ?>" class="nav-link <?php echo e(Request::is('admin/batches*') ? 'active' : ''); ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Batch Management</p>
                        </a>
                    </li>
                    <!-- Existing enrollment menu items -->
                    <?php $__currentLoopData = $menu['submenu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item">
                            <a href="<?php echo e($submenu['url']); ?>" class="nav-link <?php echo e(Request::is($submenu['active']) ? 'active' : ''); ?>">
                                <i class="nav-icon <?php echo e($submenu['icon']); ?>"></i>
                                <p><?php echo e($submenu['text']); ?></p>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </li>
        <?php else: ?>
            <!-- Other menu items -->
            <li class="nav-item">
                <a href="<?php echo e($menu['url']); ?>" class="nav-link <?php echo e(Request::is($menu['active']) ? 'active' : ''); ?>">
                    <i class="nav-icon <?php echo e($menu['icon']); ?>"></i>
                    <p><?php echo e($menu['text']); ?></p>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\layouts\admin-menu.blade.php ENDPATH**/ ?>