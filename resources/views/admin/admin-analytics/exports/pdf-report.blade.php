<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report - {{ $generated_at }}</title>
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
        <p>Generated on: {{ $generated_at }}</p>
        @if(isset($exported_by))
            <p>Exported by: {{ $exported_by }}</p>
        @endif
    </div>

    <div class="filters">
        <h3>Report Filters Applied</h3>
        <p>
            @if($filters['year']) <strong>Year:</strong> {{ $filters['year'] }} | @endif
            @if($filters['month']) <strong>Month:</strong> {{ $filters['month'] }} | @endif
            @if($filters['program']) <strong>Program:</strong> {{ ucfirst($filters['program']) }} | @endif
            @if($filters['batch']) <strong>Batch:</strong> {{ $filters['batch'] }} | @endif
            @if($filters['subject']) <strong>Subject:</strong> {{ $filters['subject'] }} @endif
            @if(!$filters['year'] && !$filters['month'] && !$filters['program'] && !$filters['batch'] && !$filters['subject'])
                <em>No filters applied - showing all data</em>
            @endif
            @if(!array_filter($filters)) All Data (No filters applied) @endif
        </p>
    </div>

    <div class="section">
        <h2>Key Metrics</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['boardPassRate'] ?? 0 }}%</div>
                <div class="metric-label">Board Pass Rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ number_format($metrics['totalStudents'] ?? 0) }}</div>
                <div class="metric-label">Total Students</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['avgQuizScore'] ?? 0 }}%</div>
                <div class="metric-label">Avg Quiz Score</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['completionRate'] ?? 0 }}%</div>
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
                @foreach($tables['recentlyEnrolled'] ?? [] as $student)
                <tr>
                    <td>{{ $student['name'] ?? '' }}</td>
                    <td>{{ $student['email'] ?? '' }}</td>
                    <td>{{ $student['program'] ?? '' }}</td>
                    <td>{{ $student['plan'] ?? '' }}</td>
                    <td>{{ $student['enrollment_date'] ?? '' }}</td>
                    <td>{{ $student['status'] ?? '' }}</td>
                </tr>
                @endforeach
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
                @foreach($tables['recentlyCompleted'] ?? [] as $student)
                <tr>
                    <td>{{ $student['name'] ?? '' }}</td>
                    <td>{{ $student['email'] ?? '' }}</td>
                    <td>{{ $student['program'] ?? '' }}</td>
                    <td>{{ $student['plan'] ?? '' }}</td>
                    <td>{{ $student['completion_date'] ?? '' }}</td>
                    <td>{{ $student['final_score'] ?? 0 }}%</td>
                </tr>
                @endforeach
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
                @foreach($tables['recentPayments'] ?? [] as $payment)
                <tr>
                    <td>{{ $payment['student_name'] ?? '' }}</td>
                    <td>{{ $payment['program'] ?? '' }}</td>
                    <td>₱{{ number_format($payment['amount'] ?? 0, 2) }}</td>
                    <td>{{ $payment['payment_date'] ?? '' }}</td>
                    <td>{{ $payment['status'] ?? '' }}</td>
                    <td>{{ $payment['payment_method'] ?? '' }}</td>
                </tr>
                @endforeach
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
                @foreach($tables['boardPassers'] ?? [] as $passer)
                <tr>
                    <td>{{ $passer['student_id'] ?? '' }}</td>
                    <td>{{ $passer['full_name'] ?? '' }}</td>
                    <td>{{ $passer['program_name'] ?? $passer['program'] ?? 'Unknown Program' }}</td>
                    <td>{{ $passer['exam_date'] ?? '' }}</td>
                    <td>{{ $passer['result'] ?? '' }}</td>
                    <td>{{ $passer['rating'] ? $passer['rating'] . '%' : 'N/A' }}</td>
                </tr>
                @endforeach
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
                @foreach($tables['batchPerformance'] ?? [] as $batch)
                <tr>
                    <td>{{ $batch['batch_name'] ?? '' }}</td>
                    <td>{{ $batch['student_count'] ?? 0 }}</td>
                    <td>{{ $batch['average_score'] ?? 0 }}%</td>
                    <td>{{ $batch['pass_rate'] ?? 0 }}%</td>
                    <td>{{ $batch['completion_rate'] ?? 0 }}%</td>
                    <td>{{ $batch['status'] ?? '' }}</td>
                </tr>
                @endforeach
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
                <li>Board pass rate: {{ $metrics['boardPassRate'] ?? 0 }}% (Target: 70%+)</li>
                <li>Student completion rate: {{ $metrics['completionRate'] ?? 0 }}%</li>
                <li>Average quiz performance: {{ $metrics['avgQuizScore'] ?? 0 }}%</li>
                <li>Total active students: {{ $metrics['totalStudents'] ?? 0 }}</li>
            </ul>
        </div>
        
        <hr style="margin: 20px 0; border: 1px solid #e0e0e0;">
        
        <div>
            <p><strong>Report Information</strong></p>
            <p>Generated by: {{ $exported_by ?? 'System Administrator' }}</p>
            <p>Export Format: {{ isset($export_format) ? strtoupper($export_format) : 'PDF' }}</p>
            <p>Report ID: ARC-{{ date('Ymd-His') }}</p>
            <p style="margin-top: 20px;">
                <em>This report is confidential and intended for authorized personnel only.</em><br>
                <small>© {{ date('Y') }} Ascendo Review and Training Center. All rights reserved.</small>
            </p>
        </div>
    </div>
        <p>This report was generated by the Ascendo Review and Training Center Analytics System</p>
        <p>© {{ date('Y') }} Ascendo Review and Training Center. All rights reserved.</p>
    </div>
</body>
</html>
