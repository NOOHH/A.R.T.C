<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grades Export - {{ $program->program_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .program-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .status-passing { color: #28a745; font-weight: bold; }
        .status-risk { color: #ffc107; font-weight: bold; }
        .status-failing { color: #dc3545; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Grade Report</h1>
        <h2>{{ $program->program_name }}</h2>
    </div>

    <div class="program-info">
        <p><strong>Generated on:</strong> {{ now()->format('F j, Y g:i A') }}</p>
        <p><strong>Total Students:</strong> {{ $students->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Total Grades</th>
                <th>Avg Grade</th>
                <th>Total Quizzes</th>
                <th>Avg Quiz Score</th>
                <th>Overall Average</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                @php
                    $averageGrade = $student->grades->avg('grade') ?? 0;
                    $averageQuiz = $student->quizSubmissions->avg('score') ?? 0;
                    $overallAverage = ($averageGrade + $averageQuiz) / 2;
                    
                    if ($overallAverage >= 75) {
                        $status = 'Passing';
                        $statusClass = 'status-passing';
                    } elseif ($overallAverage >= 60) {
                        $status = 'At Risk';
                        $statusClass = 'status-risk';
                    } else {
                        $status = 'Failing';
                        $statusClass = 'status-failing';
                    }
                @endphp
                <tr>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->user->user_firstname ?? 'N/A' }} {{ $student->user->user_lastname ?? '' }}</td>
                    <td>{{ $student->user->user_email ?? 'N/A' }}</td>
                    <td>{{ $student->grades->count() }}</td>
                    <td>{{ number_format($averageGrade, 2) }}%</td>
                    <td>{{ $student->quizSubmissions->count() }}</td>
                    <td>{{ number_format($averageQuiz, 2) }}%</td>
                    <td>{{ number_format($overallAverage, 2) }}%</td>
                    <td class="{{ $statusClass }}">{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary Statistics</h3>
        @php
            $allAverages = $students->map(function ($student) {
                $averageGrade = $student->grades->avg('grade') ?? 0;
                $averageQuiz = $student->quizSubmissions->avg('score') ?? 0;
                return ($averageGrade + $averageQuiz) / 2;
            });
            
            $passingCount = $allAverages->filter(function ($avg) { return $avg >= 75; })->count();
            $atRiskCount = $allAverages->filter(function ($avg) { return $avg >= 60 && $avg < 75; })->count();
            $failingCount = $allAverages->filter(function ($avg) { return $avg < 60; })->count();
        @endphp
        
        <div style="display: flex; justify-content: space-between;">
            <div>
                <p><strong>Class Average:</strong> {{ number_format($allAverages->avg(), 2) }}%</p>
                <p><strong>Highest Average:</strong> {{ number_format($allAverages->max(), 2) }}%</p>
                <p><strong>Lowest Average:</strong> {{ number_format($allAverages->min(), 2) }}%</p>
            </div>
            <div>
                <p><strong>Passing Students:</strong> {{ $passingCount }} ({{ number_format(($passingCount / $students->count()) * 100, 1) }}%)</p>
                <p><strong>At Risk Students:</strong> {{ $atRiskCount }} ({{ number_format(($atRiskCount / $students->count()) * 100, 1) }}%)</p>
                <p><strong>Failing Students:</strong> {{ $failingCount }} ({{ number_format(($failingCount / $students->count()) * 100, 1) }}%)</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the Academic Resource and Training Center (A.R.T.C) system.</p>
        <p>For questions about this report, please contact your system administrator.</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
