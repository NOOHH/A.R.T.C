@extends('student.student-dashboard-layout')

@section('title', 'My Analytics')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --accent-color: #3498db;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --light-gray: #ecf0f1;
    --dark-gray: #95a5a6;
    --white: #ffffff;
}

.analytics-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px;
}

.dashboard-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    text-align: center;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-left: 4px solid var(--accent-color);
    transition: transform 0.3s ease;
    margin-bottom: 20px;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    margin-bottom: 15px;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--dark-gray);
    font-weight: 500;
}

.chart-container {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.progress-item {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.progress-bar-custom {
    height: 10px;
    border-radius: 5px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 5px;
    transition: width 0.6s ease;
}

.quiz-result-item {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border-left: 4px solid transparent;
}

.quiz-passed {
    border-left-color: var(--success-color);
}

.quiz-failed {
    border-left-color: var(--danger-color);
}

.no-data {
    text-align: center;
    padding: 40px;
    color: var(--dark-gray);
}
</style>
@endpush

@section('content')
<div class="analytics-dashboard">
    <div class="container-fluid">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="display-5 fw-bold mb-2">
                <i class="fas fa-chart-line me-3"></i>My Learning Analytics
            </h1>
            <p class="mb-0">Track your progress and performance</p>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-value">{{ $analytics['total_enrollments'] }}</div>
                    <div class="stat-label">Total Enrollments</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-quiz"></i>
                    </div>
                    <div class="stat-value">{{ $analytics['total_quizzes'] }}</div>
                    <div class="stat-label">Quizzes Taken</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-value">{{ $analytics['average_score'] }}%</div>
                    <div class="stat-label">Average Score</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-value">{{ $analytics['pass_rate'] }}%</div>
                    <div class="stat-label">Pass Rate</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Program Progress -->
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-4"><i class="fas fa-tasks me-2"></i>Program Progress</h5>
                    @if(!empty($analytics['progress_data']))
                        @foreach($analytics['progress_data'] as $progress)
                            <div class="progress-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $progress['program_name'] }}</h6>
                                    <span class="badge bg-primary">{{ $progress['progress_percentage'] }}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-bar-fill bg-primary" style="width: {{ $progress['progress_percentage'] }}%"></div>
                                </div>
                                <div class="mt-2 small text-muted">
                                    {{ $progress['completed_modules'] }} of {{ $progress['total_modules'] }} modules completed
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-data">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <h6>No program progress data available</h6>
                            <p>Complete some modules to see your progress here.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Quiz Results -->
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>Recent Quiz Results</h5>
                    @if($analytics['quiz_results']->count() > 0)
                        <div style="max-height: 400px; overflow-y: auto;">
                            @foreach($analytics['quiz_results']->sortByDesc('created_at')->take(10) as $result)
                                <div class="quiz-result-item {{ $result->score >= 70 ? 'quiz-passed' : 'quiz-failed' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $result->quiz_title }}</h6>
                                            <small class="text-muted">{{ $result->module_name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold {{ $result->score >= 70 ? 'text-success' : 'text-danger' }}">
                                                {{ $result->score }}%
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($result->created_at)->format('M j, Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">
                            <i class="fas fa-quiz fa-3x mb-3"></i>
                            <h6>No quiz results yet</h6>
                            <p>Take some quizzes to see your performance here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        @if($analytics['quiz_results']->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i>Performance Trend</h5>
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($analytics['quiz_results']->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const quizData = @json($analytics['quiz_results']->sortBy('created_at')->values());
    const labels = quizData.map(quiz => quiz.quiz_title.substring(0, 20) + '...');
    const scores = quizData.map(quiz => quiz.score);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quiz Scores (%)',
                data: scores,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
});
</script>
@endif
@endpush
