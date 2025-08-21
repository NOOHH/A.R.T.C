<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subject Performance Report - <?php echo e($generated_at); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #667eea;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters h3 {
            margin-top: 0;
            color: #667eea;
        }
        .difficulty-easy { color: #28a745; font-weight: bold; }
        .difficulty-medium { color: #ffc107; font-weight: bold; }
        .difficulty-hard { color: #dc3545; font-weight: bold; }
        .trend-positive { color: #28a745; }
        .trend-negative { color: #dc3545; }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 12px;
        }
        .summary-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #0066cc;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Subject Performance Report</h1>
        <p>Ascendo Review and Training Center</p>
        <p>Generated on: <?php echo e($generated_at); ?></p>
    </div>

    <div class="filters">
        <h3>Report Filters Applied</h3>
        <p>
            <?php if($filters['year']): ?> Year: <?php echo e($filters['year']); ?> | <?php endif; ?>
            <?php if($filters['month']): ?> Month: <?php echo e($filters['month']); ?> | <?php endif; ?>
            <?php if($filters['program']): ?> Program: <?php echo e(ucfirst($filters['program'])); ?> | <?php endif; ?>
            <?php if($filters['batch']): ?> Batch: <?php echo e($filters['batch']); ?> | <?php endif; ?>
            <?php if($filters['subject']): ?> Subject: <?php echo e($filters['subject']); ?> <?php endif; ?>
            <?php if(!array_filter($filters)): ?> All Data (No filters applied) <?php endif; ?>
        </p>
    </div>

    <div class="summary-box">
        <h3>Performance Summary</h3>
        <p>This report provides a comprehensive analysis of subject performance across all enrolled students. The data includes average scores, pass rates, difficulty assessments, and performance trends.</p>
        <ul>
            <li><strong>Total Subjects Analyzed:</strong> <?php echo e(count($subjects)); ?></li>
            <li><strong>Overall Average Score:</strong> <?php echo e(number_format(collect($subjects)->avg('avgScore'), 1)); ?>%</li>
            <li><strong>Overall Pass Rate:</strong> <?php echo e(number_format(collect($subjects)->avg('passRate'), 1)); ?>%</li>
        </ul>
    </div>

    <div class="section">
        <h2>Detailed Subject Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Total Students</th>
                    <th>Average Score</th>
                    <th>Pass Rate</th>
                    <th>Difficulty Level</th>
                    <th>Performance Trend</th>
                    <th>Recommendations</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($subject['name']); ?></strong></td>
                    <td><?php echo e(number_format($subject['totalStudents'])); ?></td>
                    <td><?php echo e($subject['avgScore']); ?>%</td>
                    <td><?php echo e($subject['passRate']); ?>%</td>
                    <td class="difficulty-<?php echo e(strtolower($subject['difficulty'])); ?>">
                        <?php echo e($subject['difficulty']); ?>

                    </td>
                    <td class="<?php echo e($subject['trend'] >= 0 ? 'trend-positive' : 'trend-negative'); ?>">
                        <?php echo e($subject['trend'] > 0 ? '+' : ''); ?><?php echo e($subject['trend']); ?>%
                    </td>
                    <td>
                        <?php if($subject['avgScore'] < 70): ?>
                            <small>⚠️ Review curriculum and teaching methods</small>
                        <?php elseif($subject['avgScore'] < 80): ?>
                            <small>📝 Consider additional practice materials</small>
                        <?php else: ?>
                            <small>✅ Performing well, maintain standards</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Performance Categories</h2>
        <?php
            $excellent = collect($subjects)->where('avgScore', '>=', 90)->count();
            $good = collect($subjects)->whereBetween('avgScore', [80, 89])->count();
            $average = collect($subjects)->whereBetween('avgScore', [70, 79])->count();
            $needsImprovement = collect($subjects)->where('avgScore', '<', 70)->count();
        ?>
        
        <table>
            <thead>
                <tr>
                    <th>Performance Category</th>
                    <th>Score Range</th>
                    <th>Number of Subjects</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Excellent</strong></td>
                    <td>90% - 100%</td>
                    <td><?php echo e($excellent); ?></td>
                    <td><?php echo e(count($subjects) > 0 ? number_format(($excellent / count($subjects)) * 100, 1) : 0); ?>%</td>
                </tr>
                <tr>
                    <td><strong>Good</strong></td>
                    <td>80% - 89%</td>
                    <td><?php echo e($good); ?></td>
                    <td><?php echo e(count($subjects) > 0 ? number_format(($good / count($subjects)) * 100, 1) : 0); ?>%</td>
                </tr>
                <tr>
                    <td><strong>Average</strong></td>
                    <td>70% - 79%</td>
                    <td><?php echo e($average); ?></td>
                    <td><?php echo e(count($subjects) > 0 ? number_format(($average / count($subjects)) * 100, 1) : 0); ?>%</td>
                </tr>
                <tr style="background-color: #ffe6e6;">
                    <td><strong>Needs Improvement</strong></td>
                    <td>Below 70%</td>
                    <td><?php echo e($needsImprovement); ?></td>
                    <td><?php echo e(count($subjects) > 0 ? number_format(($needsImprovement / count($subjects)) * 100, 1) : 0); ?>%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if($needsImprovement > 0): ?>
    <div class="section">
        <h2>Action Items</h2>
        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px;">
            <h4 style="color: #856404; margin-top: 0;">Subjects Requiring Immediate Attention</h4>
            <ul>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($subject['avgScore'] < 70): ?>
                        <li><strong><?php echo e($subject['name']); ?></strong> (<?php echo e($subject['avgScore']); ?>% average) - Consider curriculum review and additional support materials</li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>This report was generated by the Ascendo Review and Training Center Analytics System</p>
        <p>© <?php echo e(date('Y')); ?> Ascendo Review and Training Center. All rights reserved.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-analytics\exports\subject-report.blade.php ENDPATH**/ ?>