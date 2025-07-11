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
    </div>

    <div class="filters">
        <h3>Report Filters Applied</h3>
        <p>
            @if($filters['year']) Year: {{ $filters['year'] }} | @endif
            @if($filters['month']) Month: {{ $filters['month'] }} | @endif
            @if($filters['program']) Program: {{ ucfirst($filters['program']) }} | @endif
            @if($filters['batch']) Batch: {{ $filters['batch'] }} | @endif
            @if($filters['subject']) Subject: {{ $filters['subject'] }} @endif
            @if(!array_filter($filters)) All Data (No filters applied) @endif
        </p>
    </div>

    <div class="section">
        <h2>Key Metrics</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['boardPassRate'] }}%</div>
                <div class="metric-label">Board Pass Rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ number_format($metrics['totalStudents']) }}</div>
                <div class="metric-label">Total Students</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['avgQuizScore'] }}%</div>
                <div class="metric-label">Avg Quiz Score</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['completionRate'] }}%</div>
                <div class="metric-label">Completion Rate</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Top Performers</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Score</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['topPerformers'] as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student['name'] }}</td>
                    <td>{{ $student['email'] }}</td>
                    <td>{{ $student['program'] }}</td>
                    <td>{{ $student['score'] }}%</td>
                    <td>{{ $student['progress'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Students Needing Attention</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Score</th>
                    <th>Issues</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['bottomPerformers'] as $student)
                <tr>
                    <td>{{ $student['name'] }}</td>
                    <td>{{ $student['email'] }}</td>
                    <td>{{ $student['program'] }}</td>
                    <td>{{ $student['score'] }}%</td>
                    <td>{{ $student['issues'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Subject Performance Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Total Students</th>
                    <th>Avg Score</th>
                    <th>Pass Rate</th>
                    <th>Difficulty</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['subjectBreakdown'] as $subject)
                <tr>
                    <td>{{ $subject['name'] }}</td>
                    <td>{{ $subject['totalStudents'] }}</td>
                    <td>{{ $subject['avgScore'] }}%</td>
                    <td>{{ $subject['passRate'] }}%</td>
                    <td>{{ $subject['difficulty'] }}</td>
                    <td>{{ $subject['trend'] > 0 ? '+' : '' }}{{ $subject['trend'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated by the Ascendo Review and Training Center Analytics System</p>
        <p>© {{ date('Y') }} Ascendo Review and Training Center. All rights reserved.</p>
    </div>
</body>
</html>
