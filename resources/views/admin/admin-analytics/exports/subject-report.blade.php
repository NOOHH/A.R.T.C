<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subject Performance Report - {{ $generated_at }}</title>
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

    <div class="summary-box">
        <h3>Performance Summary</h3>
        <p>This report provides a comprehensive analysis of subject performance across all enrolled students. The data includes average scores, pass rates, difficulty assessments, and performance trends.</p>
        <ul>
            <li><strong>Total Subjects Analyzed:</strong> {{ count($subjects) }}</li>
            <li><strong>Overall Average Score:</strong> {{ number_format(collect($subjects)->avg('avgScore'), 1) }}%</li>
            <li><strong>Overall Pass Rate:</strong> {{ number_format(collect($subjects)->avg('passRate'), 1) }}%</li>
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
                @foreach($subjects as $subject)
                <tr>
                    <td><strong>{{ $subject['name'] }}</strong></td>
                    <td>{{ number_format($subject['totalStudents']) }}</td>
                    <td>{{ $subject['avgScore'] }}%</td>
                    <td>{{ $subject['passRate'] }}%</td>
                    <td class="difficulty-{{ strtolower($subject['difficulty']) }}">
                        {{ $subject['difficulty'] }}
                    </td>
                    <td class="{{ $subject['trend'] >= 0 ? 'trend-positive' : 'trend-negative' }}">
                        {{ $subject['trend'] > 0 ? '+' : '' }}{{ $subject['trend'] }}%
                    </td>
                    <td>
                        @if($subject['avgScore'] < 70)
                            <small>⚠️ Review curriculum and teaching methods</small>
                        @elseif($subject['avgScore'] < 80)
                            <small>📝 Consider additional practice materials</small>
                        @else
                            <small>✅ Performing well, maintain standards</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Performance Categories</h2>
        @php
            $excellent = collect($subjects)->where('avgScore', '>=', 90)->count();
            $good = collect($subjects)->whereBetween('avgScore', [80, 89])->count();
            $average = collect($subjects)->whereBetween('avgScore', [70, 79])->count();
            $needsImprovement = collect($subjects)->where('avgScore', '<', 70)->count();
        @endphp
        
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
                    <td>{{ $excellent }}</td>
                    <td>{{ count($subjects) > 0 ? number_format(($excellent / count($subjects)) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td><strong>Good</strong></td>
                    <td>80% - 89%</td>
                    <td>{{ $good }}</td>
                    <td>{{ count($subjects) > 0 ? number_format(($good / count($subjects)) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td><strong>Average</strong></td>
                    <td>70% - 79%</td>
                    <td>{{ $average }}</td>
                    <td>{{ count($subjects) > 0 ? number_format(($average / count($subjects)) * 100, 1) : 0 }}%</td>
                </tr>
                <tr style="background-color: #ffe6e6;">
                    <td><strong>Needs Improvement</strong></td>
                    <td>Below 70%</td>
                    <td>{{ $needsImprovement }}</td>
                    <td>{{ count($subjects) > 0 ? number_format(($needsImprovement / count($subjects)) * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($needsImprovement > 0)
    <div class="section">
        <h2>Action Items</h2>
        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px;">
            <h4 style="color: #856404; margin-top: 0;">Subjects Requiring Immediate Attention</h4>
            <ul>
                @foreach($subjects as $subject)
                    @if($subject['avgScore'] < 70)
                        <li><strong>{{ $subject['name'] }}</strong> ({{ $subject['avgScore'] }}% average) - Consider curriculum review and additional support materials</li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated by the Ascendo Review and Training Center Analytics System</p>
        <p>© {{ date('Y') }} Ascendo Review and Training Center. All rights reserved.</p>
    </div>
</body>
</html>
