<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report - <?php echo e($generated_at); ?></title>
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
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .metric-label {
            color: #666;
            margin-top: 5px;
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
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Analytics Report</h1>
        <p>Ascendo Review and Training Center</p>
        <p>Generated on: <?php echo e($generated_at); ?></p>
        <?php if(isset($exported_by)): ?>
            <p>Exported by: <?php echo e($exported_by); ?></p>
        <?php endif; ?>
    </div>

    <div class="filters">
        <h3>Report Filters Applied</h3>
        <p>
            <?php if($filters['year']): ?> <strong>Year:</strong> <?php echo e($filters['year']); ?> | <?php endif; ?>
            <?php if($filters['month']): ?> <strong>Month:</strong> <?php echo e($filters['month']); ?> | <?php endif; ?>
            <?php if($filters['program']): ?> <strong>Program:</strong> <?php echo e(ucfirst($filters['program'])); ?> | <?php endif; ?>
            <?php if($filters['batch']): ?> <strong>Batch:</strong> <?php echo e($filters['batch']); ?> | <?php endif; ?>
            <?php if($filters['subject']): ?> <strong>Subject:</strong> <?php echo e($filters['subject']); ?> <?php endif; ?>
            <?php if(!$filters['year'] && !$filters['month'] && !$filters['program'] && !$filters['batch'] && !$filters['subject']): ?>
                <em>No filters applied - showing all data</em>
            <?php endif; ?>
            <?php if(!array_filter($filters)): ?> All Data (No filters applied) <?php endif; ?>
        </p>
    </div>

    <div class="section">
        <h2>Key Metrics</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value"><?php echo e($metrics['boardPassRate']); ?>%</div>
                <div class="metric-label">Board Pass Rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-value"><?php echo e(number_format($metrics['totalStudents'])); ?></div>
                <div class="metric-label">Total Students</div>
            </div>
            <div class="metric-card">
                <div class="metric-value"><?php echo e($metrics['avgQuizScore']); ?>%</div>
                <div class="metric-label">Avg Quiz Score</div>
            </div>
            <div class="metric-card">
                <div class="metric-value"><?php echo e($metrics['completionRate']); ?>%</div>
                <div class="metric-label">Completion Rate</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Recently Enrolled Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Plan</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tables['recentlyEnrolled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($student['name']); ?></td>
                    <td><?php echo e($student['email']); ?></td>
                    <td><?php echo e($student['program']); ?></td>
                    <td><?php echo e($student['plan']); ?></td>
                    <td><?php echo e($student['enrollment_date']); ?></td>
                    <td><?php echo e($student['status']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Recently Completed Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Plan</th>
                    <th>Completion Date</th>
                    <th>Final Score</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tables['recentlyCompleted']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($student['name']); ?></td>
                    <td><?php echo e($student['email']); ?></td>
                    <td><?php echo e($student['program']); ?></td>
                    <td><?php echo e($student['plan']); ?></td>
                    <td><?php echo e($student['completion_date']); ?></td>
                    <td><?php echo e($student['final_score']); ?>%</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Recent Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tables['recentPayments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($payment['student_name']); ?></td>
                    <td><?php echo e($payment['program']); ?></td>
                    <td>₱<?php echo e(number_format($payment['amount'], 2)); ?></td>
                    <td><?php echo e($payment['payment_date']); ?></td>
                    <td><?php echo e($payment['status']); ?></td>
                    <td><?php echo e($payment['payment_method']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Board Exam Passers</h2>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Program</th>
                    <th>Exam Date</th>
                    <th>Result</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tables['boardPassers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $passer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($passer['student_id']); ?></td>
                    <td><?php echo e($passer['full_name']); ?></td>
                    <td><?php echo e($passer['program_name'] ?? $passer['program'] ?? 'Unknown Program'); ?></td>
                    <td><?php echo e($passer['exam_date']); ?></td>
                    <td><?php echo e($passer['result']); ?></td>
                    <td><?php echo e($passer['rating'] ? $passer['rating'] . '%' : 'N/A'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Batch Performance Analysis</h2>
        <table>
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Number of Students</th>
                    <th>Average Score</th>
                    <th>Pass Rate</th>
                    <th>Completion Rate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tables['batchPerformance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($batch['batch_name']); ?></td>
                    <td><?php echo e($batch['student_count']); ?></td>
                    <td><?php echo e($batch['average_score']); ?>%</td>
                    <td><?php echo e($batch['pass_rate']); ?>%</td>
                    <td><?php echo e($batch['completion_rate']); ?>%</td>
                    <td><?php echo e($batch['status']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div style="margin-bottom: 20px;">
            <h3 style="color: #667eea; margin-bottom: 10px;">Report Summary</h3>
            <p>This report provides comprehensive analytics data for the review center's performance metrics, 
               including student performance, program effectiveness, and overall training outcomes.</p>
            <p><strong>Key Insights:</strong></p>
            <ul style="text-align: left; max-width: 600px; margin: 0 auto;">
                <li>Board pass rate: <?php echo e($metrics['boardPassRate']); ?>% (Target: 70%+)</li>
                <li>Student completion rate: <?php echo e($metrics['completionRate']); ?>%</li>
                <li>Average quiz performance: <?php echo e($metrics['avgQuizScore']); ?>%</li>
                <li>Total active students: <?php echo e($metrics['totalStudents']); ?></li>
            </ul>
        </div>
        
        <hr style="margin: 20px 0; border: 1px solid #e0e0e0;">
        
        <div>
            <p><strong>Report Information</strong></p>
            <p>Generated by: <?php echo e($exported_by ?? 'System Administrator'); ?></p>
            <p>Export Format: <?php echo e(isset($export_format) ? strtoupper($export_format) : 'PDF'); ?></p>
            <p>Report ID: ARC-<?php echo e(date('Ymd-His')); ?></p>
            <p style="margin-top: 20px;">
                <em>This report is confidential and intended for authorized personnel only.</em><br>
                <small>© <?php echo e(date('Y')); ?> Ascendo Review and Training Center. All rights reserved.</small>
            </p>
        </div>
    </div>
        <p>This report was generated by the Ascendo Review and Training Center Analytics System</p>
        <p>© <?php echo e(date('Y')); ?> Ascendo Review and Training Center. All rights reserved.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-analytics\exports\pdf-report.blade.php ENDPATH**/ ?>