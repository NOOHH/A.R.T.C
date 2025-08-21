

<?php $__env->startSection('title', 'My Batches - Professor Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Scoped styles for professor batches page */
        .professor-batches-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .content-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 3rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }

        .page-header-content {
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .stats-overview {
            padding: 2rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .batches-content {
            padding: 2rem;
        }

        .batch-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .batch-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            border-color: #4f46e5;
        }

        .batch-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 2rem;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        .batch-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

        .batch-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .batch-info {
            flex: 1;
        }

        .batch-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .batch-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }

        .batch-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #4a5568;
        }

        .meta-item i {
            color: #4f46e5;
            width: 16px;
        }

        .batch-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .student-count-badge {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.active {
            background: #d1fae5;
            color: #065f46;
        }

        .batch-body {
            padding: 2rem;
        }

        .students-section h3 {
            color: #1a202c;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .students-section h3 i {
            color: #4f46e5;
        }

        .students-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .students-table table {
            margin: 0;
        }

        .students-table thead {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }

        .students-table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .students-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .students-table tbody tr:hover {
            background: #f8fafc;
        }

        .student-name {
            font-weight: 600;
            color: #1a202c;
        }

        .student-email {
            color: #4a5568;
            font-size: 0.9rem;
        }

        .btn-view-student {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-view-student:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .no-students {
            text-align: center;
            padding: 3rem 2rem;
            color: #4a5568;
            background: #f8fafc;
            border-radius: 12px;
            margin: 1rem 0;
        }

        .no-students i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #f8fafc;
            border-radius: 12px;
            margin: 2rem 0;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #718096;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .professor-batches-container {
                padding: 1rem;
            }

            .page-header {
                padding: 2rem 1rem 1.5rem;
            }

            .page-header h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .stats-overview {
                padding: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }

            .batches-content {
                padding: 1.5rem;
            }

            .batch-header {
                padding: 1.5rem;
            }

            .batch-header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .batch-body {
                padding: 1.5rem;
            }

            .batch-meta {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .students-table {
                font-size: 0.85rem;
            }

            .students-table thead th,
            .students-table tbody td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="professor-batches-container">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>
                    <i class="bi bi-collection"></i>
                    My Assigned Batches
                </h1>
                <p>Manage and monitor your student batches and track their progress</p>
            </div>
        </div>

        <!-- Stats Overview -->
        <?php if($batches->count() > 0): ?>
            <div class="stats-overview">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-collection"></i>
                        </div>
                        <div class="stat-number"><?php echo e($batches->count()); ?></div>
                        <div class="stat-label">Total Batches</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number"><?php echo e($batches->sum(function($batch) { return $batch->students->count(); })); ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <div class="stat-number"><?php echo e($batches->unique('program_id')->count()); ?></div>
                        <div class="stat-label">Programs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="stat-number"><?php echo e($batches->filter(function($batch) { return $batch->students->count() > 0; })->count()); ?></div>
                        <div class="stat-label">Active Batches</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Batches Content -->
        <div class="batches-content">
            <?php if($batches->count() > 0): ?>
                <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="batch-card fade-in">
                        <!-- Batch Header -->
                        <div class="batch-header">
                            <div class="batch-header-content">
                                <div class="batch-info">
                                    <div class="batch-name">
                                        <div class="batch-icon">
                                            <i class="bi bi-collection-fill"></i>
                                        </div>
                                        <?php echo e($batch->batch_name); ?>

                                    </div>
                                    <div class="batch-meta">
                                        <div class="meta-item">
                                            <i class="bi bi-mortarboard"></i>
                                            <span><strong>Program:</strong> <?php echo e($batch->program->program_name ?? 'Unknown Program'); ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-calendar-check"></i>
                                            <span><strong>Start:</strong> <?php echo e($batch->start_date ? \Carbon\Carbon::parse($batch->start_date)->format('M d, Y') : 'Not set'); ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="bi bi-calendar-x"></i>
                                            <span><strong>End:</strong> <?php echo e($batch->end_date ? \Carbon\Carbon::parse($batch->end_date)->format('M d, Y') : 'Not set'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="batch-badges">
                                    <span class="student-count-badge">
                                        <i class="bi bi-people"></i>
                                        <?php echo e($batch->students->count()); ?> Student<?php echo e($batch->students->count() !== 1 ? 's' : ''); ?>

                                    </span>
                                    <span class="status-badge active">
                                        Active
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Batch Body -->
                        <div class="batch-body">
                            <div class="students-section">
                                <h3>
                                    <i class="bi bi-people"></i>
                                    Enrolled Students
                                    <span class="badge bg-primary ms-2"><?php echo e($batch->students->count()); ?></span>
                                </h3>

                                <?php if($batch->students->count() > 0): ?>
                                    <div class="students-table">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Email Address</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $batch->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <div class="student-name"><?php echo e($student->student_firstname); ?> <?php echo e($student->student_lastname); ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="student-email"><?php echo e($student->student_email); ?></div>
                                                        </td>
                                                        <td>
                                                            <span class="status-badge active">Active</span>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo e(route('professor.grading.student', $student->student_id)); ?>?program_id=<?php echo e($batch->program_id); ?>" 
                                                               class="btn-view-student">
                                                                <i class="bi bi-eye"></i>
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="no-students">
                                        <i class="bi bi-person-x"></i>
                                        <h5>No Students Enrolled</h5>
                                        <p>This batch doesn't have any students enrolled yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="bi bi-collection"></i>
                    <h3>No Batches Assigned</h3>
                    <p>You don't have any batches assigned to you yet.</p>
                    <p>Contact your administrator to get assigned to batches.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations to cards
        const cards = document.querySelectorAll('.batch-card, .stat-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                }
            });
        }, { threshold: 0.1 });

        cards.forEach((card) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
        
        // Enhanced hover effects for batch cards
        const batchCards = document.querySelectorAll('.batch-card');
        batchCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-6px) scale(1.01)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Stat cards animation
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.05)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Table row hover effects
        const tableRows = document.querySelectorAll('.students-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.01)';
                this.style.transition = 'all 0.2s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\students\batches.blade.php ENDPATH**/ ?>