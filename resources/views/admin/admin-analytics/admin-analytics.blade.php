@extends('admin.admin-dashboard-layout')

@section('title', 'Analytics Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/admin-analytics/admin-analytics.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
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
    --border-radius: 8px;
    --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    --transition: all 0.3s ease;
}

.analytics-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 20px;
}

.analytics-header {
    background: var(--primary-color);
    color: var(--white);
    border-radius: var(--border-radius);
    padding: 40px 30px;
    margin-bottom: 30px;
    text-align: center;
    box-shadow: var(--box-shadow);
}

.analytics-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--white);
}

.analytics-header .lead {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 300;
}

.analytics-nav {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: var(--box-shadow);
}

.nav-tabs {
    border-bottom: 2px solid var(--light-gray);
}

.nav-tabs .nav-link {
    border: none;
    border-radius: 0;
    color: var(--dark-gray);
    font-weight: 600;
    padding: 15px 25px;
    transition: var(--transition);
}

.nav-tabs .nav-link.active {
    background: var(--primary-color);
    color: var(--white);
    border-bottom: 3px solid var(--accent-color);
}

.nav-tabs .nav-link:hover {
    background: var(--light-gray);
    color: var(--primary-color);
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    border: 1px solid #e9ecef;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.stat-card .card-body {
    padding: 25px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--white);
    margin-bottom: 15px;
}

.stat-icon.primary { background: var(--primary-color); }
.stat-icon.success { background: var(--success-color); }
.stat-icon.warning { background: var(--warning-color); }
.stat-icon.danger { background: var(--danger-color); }
.stat-icon.info { background: var(--accent-color); }

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.95rem;
    color: var(--dark-gray);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-trend {
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 8px;
}

.trend-up { color: var(--success-color); }
.trend-down { color: var(--danger-color); }

.chart-container {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 25px;
    box-shadow: var(--box-shadow);
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--light-gray);
}

.filter-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 25px;
    box-shadow: var(--box-shadow);
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: var(--border-radius);
    padding: 10px 15px;
    font-size: 0.95rem;
    transition: var(--transition);
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.btn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
    border-radius: var(--border-radius);
    padding: 10px 20px;
    font-weight: 600;
    transition: var(--transition);
}

.btn-primary:hover {
    background: var(--secondary-color);
    border-color: var(--secondary-color);
    transform: translateY(-1px);
}

.btn-success {
    background: var(--success-color);
    border-color: var(--success-color);
}

.btn-warning {
    background: var(--warning-color);
    border-color: var(--warning-color);
}

.btn-info {
    background: var(--accent-color);
    border-color: var(--accent-color);
}

.table-professional {
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
    border: 1px solid #e9ecef;
}

.table-professional thead {
    background: var(--primary-color);
    color: var(--white);
}

.table-professional thead th {
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 15px 12px;
    letter-spacing: 0.5px;
}

.table-professional tbody td {
    border: none;
    padding: 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f8f9fa;
    font-size: 0.9rem;
}

.table-professional tbody tr:hover {
    background-color: #f8f9fa;
}

.progress-professional {
    height: 6px;
    border-radius: 3px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-professional .progress-bar {
    border-radius: 3px;
    transition: width 0.6s ease;
}

.badge-professional {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5em 0.75em;
    border-radius: 4px;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 40px;
    color: var(--dark-gray);
}

.board-passer-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 25px;
    box-shadow: var(--box-shadow);
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}

.upload-area {
    border: 2px dashed var(--accent-color);
    border-radius: var(--border-radius);
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    transition: var(--transition);
}

.upload-area:hover {
    background: #e9ecef;
}

.section-divider {
    border: 0;
    height: 2px;
    background: linear-gradient(to right, transparent, var(--light-gray), transparent);
    margin: 40px 0;
}

@media (max-width: 768px) {
    .analytics-header {
        padding: 25px 20px;
    }
    
    .analytics-header h1 {
        font-size: 2rem;
    }
    
    .stat-card {
        margin-bottom: 20px;
    }
    
    .chart-container {
        padding: 20px;
    }
    
    .filter-section {
        padding: 20px;
    }
}

@media (max-width: 576px) {
    .stat-value {
        font-size: 2rem;
    }
    
    .analytics-header h1 {
        font-size: 1.75rem;
    }
}
</style>
@endpush

@section('content')
<div class="analytics-dashboard">
    <div class="container-fluid px-4">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <h1 class="display-4 fw-bold mb-0">
                <i class="fas fa-chart-line me-3"></i>Analytics Dashboard
            </h1>
            <p class="mb-0 mt-2">Comprehensive Review Center Performance Analytics</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-bold">Year</label>
                <select class="form-select" id="yearFilter">
                    <option value="">All Years</option>
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Month</label>
                <select class="form-select" id="monthFilter">
                    <option value="">All Months</option>
                    @for($month = 1; $month <= 12; $month++)
                        <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Program</label>
                <select class="form-select" id="programFilter">
                    <option value="">All Programs</option>
                    <option value="full">Full Program</option>
                    <option value="modular">Modular Program</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Batch</label>
                <select class="form-select" id="batchFilter">
                    <option value="">All Batches</option>
                    <!-- Will be populated via AJAX -->
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Subject</label>
                <select class="form-select" id="subjectFilter">
                    <option value="">All Subjects</option>
                    <!-- Will be populated via AJAX -->
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn export-btn w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
            </div>
        </div>
        
        <!-- Export Options -->
        <div class="row mt-3">
            <div class="col-12 text-end">
                <div class="btn-group">
                    <button type="button" class="btn export-btn" onclick="exportData('pdf')">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <button type="button" class="btn export-btn mx-2" onclick="exportData('excel')">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <button type="button" class="btn export-btn" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Board Passer Data Management -->
    <div class="board-passer-section">
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="upload-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap me-2"></i>Board Passer Data Management
                            </h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-upload me-2"></i>Upload CSV
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                                    <i class="fas fa-plus me-2"></i>Manual Entry
                                </button>
                                <button type="button" class="btn btn-info" onclick="downloadTemplate()">
                                    <i class="fas fa-download me-2"></i>Template
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <h6>Board Passers</h6>
                                        <h4 id="totalPassers">--</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon bg-danger">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div>
                                        <h6>Non-Passers</h6>
                                        <h4 id="totalNonPassers">--</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon bg-primary">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                    <div>
                                        <h6>Pass Rate</h6>
                                        <h4 id="overallPassRate">--%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6>Last Updated</h6>
                                        <small id="lastUpdated">--</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading analytics data...</p>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row g-4 mb-4" id="metricsSection">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Board Pass Rate</h6>
                        <h3 class="mb-0 fw-bold" id="boardPassRate">--%</h3>
                        <small class="metric-trend trend-up" id="boardPassTrend">
                            <i class="fas fa-arrow-up"></i> +5.2% from last period
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Students</h6>
                        <h3 class="mb-0 fw-bold" id="totalStudents">--</h3>
                        <small class="metric-trend trend-up" id="studentsTrend">
                            <i class="fas fa-arrow-up"></i> +12 this month
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Avg Quiz Score</h6>
                        <h3 class="mb-0 fw-bold" id="avgQuizScore">--%</h3>
                        <small class="metric-trend trend-up" id="quizScoreTrend">
                            <i class="fas fa-arrow-up"></i> +3.1% improvement
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Completion Rate</h6>
                        <h3 class="mb-0 fw-bold" id="completionRate">--%</h3>
                        <small class="metric-trend trend-up" id="completionTrend">
                            <i class="fas fa-arrow-up"></i> +7.8% this quarter
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <!-- Board Pass Rate Trend -->
            <div class="col-xl-8">
                <div class="analytics-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-line-chart me-2"></i>Board Pass Rate Trend
                            </h5>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary active" onclick="changeChartPeriod('monthly')">Monthly</button>
                                <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('quarterly')">Quarterly</button>
                                <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('yearly')">Yearly</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="boardPassChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Program Distribution -->
            <div class="col-xl-4">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-pie-chart me-2"></i>Program Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="programDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Quiz Performance by Subject -->
            <div class="col-xl-6">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bar-chart me-2"></i>Quiz Performance by Subject
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="subjectPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Student Progress -->
            <div class="col-xl-6">
                <div class="analytics-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>Student Progress Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="progressDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics Tables -->
        <div class="row g-4">
            <!-- Top Performers -->
            <div class="col-xl-6">
                <div class="analytics-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Top Performers
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewAllStudents('top')">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Program</th>
                                        <th>Score</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody id="topPerformersTable">
                                    <!-- Data will be populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Performers -->
            <div class="col-xl-6">
                <div class="analytics-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Students Needing Attention
                            </h5>
                            <button class="btn btn-sm btn-outline-warning" onclick="viewAllStudents('bottom')">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Program</th>
                                        <th>Score</th>
                                        <th>Issues</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bottomPerformersTable">
                                    <!-- Data will be populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Batch Performance Analysis -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-layer-group me-2"></i>Batch Performance Analysis
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" onclick="changeBatchView('overview')">Overview</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeBatchView('comparison')">Comparison</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeBatchView('detailed')">Detailed</button>
                    </div>
                </div>
                <div id="batchAnalysisContent">
                    <canvas id="batchPerformanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-wise Performance -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-book me-2"></i>Subject-wise Performance Breakdown
                    </h5>
                    <button class="btn btn-sm btn-outline-info" onclick="generateSubjectReport()">
                        Generate Report <i class="fas fa-file-alt ms-1"></i>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Total Students</th>
                                <th>Avg Score</th>
                                <th>Pass Rate</th>
                                <th>Difficulty</th>
                                <th>Trend</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="subjectBreakdownTable">
                            <!-- Data will be populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Detailed Student View -->
<div class="modal fade" id="studentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Performance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentDetailContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- CSV Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Board Passer Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload a CSV file with columns: Student ID, Student Name, Program, Batch, Board Exam, Exam Date, Result (PASS/FAIL)
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="examYear" class="form-label">Exam Year</label>
                        <select class="form-select" id="examYear" name="exam_year" required>
                            <option value="">Select Year</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="boardExam" class="form-label">Board Exam Type</label>
                        <select class="form-select" id="boardExam" name="board_exam" required>
                            <option value="">Select Exam</option>
                            <option value="CPA">CPA (Certified Public Accountant)</option>
                            <option value="LET">LET (Licensure Examination for Teachers)</option>
                            <option value="CE">CE (Civil Engineer)</option>
                            <option value="ME">ME (Mechanical Engineer)</option>
                            <option value="EE">EE (Electrical Engineer)</option>
                            <option value="NURSE">Nursing Board Exam</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="mb-3" id="otherExamDiv" style="display: none;">
                        <label for="otherExam" class="form-label">Specify Other Exam</label>
                        <input type="text" class="form-control" id="otherExam" name="other_exam">
                    </div>
                </form>
                <div class="alert alert-info">
                    <h6><i class="fas fa-file-download me-2"></i>CSV Format Requirements:</h6>
                    <ul class="mb-0">
                        <li>Student ID (required)</li>
                        <li>Student Name (required)</li>
                        <li>Program (optional - will match with existing data)</li>
                        <li>Batch (optional - will match with existing data)</li>
                        <li>Board Exam (required)</li>
                        <li>Exam Date (YYYY-MM-DD format)</li>
                        <li>Result (PASS or FAIL)</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadCSV()">
                    <i class="fas fa-upload me-2"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Manual Entry Modal -->
<div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualEntryModalLabel">
                    <i class="fas fa-plus me-2"></i>Manual Board Passer Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="manualEntryForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="studentSelect" class="form-label">Student</label>
                                <select class="form-select" id="studentSelect" name="student_id" required>
                                    <option value="">Search and select student...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manualBoardExam" class="form-label">Board Exam</label>
                                <select class="form-select" id="manualBoardExam" name="board_exam" required>
                                    <option value="">Select Exam</option>
                                    <option value="CPA">CPA (Certified Public Accountant)</option>
                                    <option value="LET">LET (Licensure Examination for Teachers)</option>
                                    <option value="CE">CE (Civil Engineer)</option>
                                    <option value="ME">ME (Mechanical Engineer)</option>
                                    <option value="EE">EE (Electrical Engineer)</option>
                                    <option value="NURSE">Nursing Board Exam</option>
                                    <option value="OTHER">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="examDate" class="form-label">Exam Date</label>
                                <input type="date" class="form-control" id="examDate" name="exam_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="result" class="form-label">Result</label>
                                <select class="form-select" id="result" name="result" required>
                                    <option value="">Select Result</option>
                                    <option value="PASS">PASS</option>
                                    <option value="FAIL">FAIL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about the exam result..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveManualEntry()">
                    <i class="fas fa-save me-2"></i>Save Entry
                </button>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables for charts
let boardPassChart, programChart, subjectChart, progressChart, batchChart;
let currentFilters = {
    year: '',
    month: '',
    program: '',
    batch: '',
    subject: ''
};

document.addEventListener('DOMContentLoaded', function() {
    initializeAnalytics();
    loadBatches();
    loadSubjects();
    loadAnalyticsData();
});

function initializeAnalytics() {
    console.log('Initializing analytics dashboard...');
    
    // Initialize charts
    initializeBoardPassChart();
    initializeProgramChart();
    initializeSubjectChart();
    initializeProgressChart();
    initializeBatchChart();
    
    // Set up event listeners
    setupEventListeners();
}

function setupEventListeners() {
    // Filter change listeners
    document.getElementById('yearFilter').addEventListener('change', updateFilters);
    document.getElementById('monthFilter').addEventListener('change', updateFilters);
    document.getElementById('programFilter').addEventListener('change', updateFilters);
    document.getElementById('batchFilter').addEventListener('change', updateFilters);
    document.getElementById('subjectFilter').addEventListener('change', updateFilters);
}

function updateFilters() {
    currentFilters = {
        year: document.getElementById('yearFilter').value,
        month: document.getElementById('monthFilter').value,
        program: document.getElementById('programFilter').value,
        batch: document.getElementById('batchFilter').value,
        subject: document.getElementById('subjectFilter').value
    };
}

function applyFilters() {
    updateFilters();
    showLoading();
    loadAnalyticsData();
}

function showLoading() {
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('metricsSection').style.opacity = '0.5';
}

function hideLoading() {
    document.getElementById('loadingSpinner').style.display = 'none';
    document.getElementById('metricsSection').style.opacity = '1';
}

function loadBatches() {
    fetch('/admin/analytics/batches')
        .then(response => response.json())
        .then(data => {
            const batchSelect = document.getElementById('batchFilter');
            batchSelect.innerHTML = '<option value="">All Batches</option>';
            
            data.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch.id;
                option.textContent = batch.name;
                batchSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading batches:', error);
        });
}

function loadSubjects() {
    fetch('/admin/analytics/subjects')
        .then(response => response.json())
        .then(data => {
            const subjectSelect = document.getElementById('subjectFilter');
            subjectSelect.innerHTML = '<option value="">All Subjects</option>';
            
            data.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                subjectSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
        });
}

function loadAnalyticsData() {
    const queryParams = new URLSearchParams(currentFilters);
    
    fetch(`/admin/analytics/data?${queryParams}`)
        .then(response => response.json())
        .then(data => {
            updateMetrics(data.metrics);
            updateCharts(data.charts);
            updateTables(data.tables);
            hideLoading();
        })
        .catch(error => {
            console.error('Error loading analytics data:', error);
            hideLoading();
            showAlert('Error loading analytics data. Please try again.', 'danger');
        });
}

function updateMetrics(metrics) {
    document.getElementById('boardPassRate').textContent = metrics.boardPassRate + '%';
    document.getElementById('totalStudents').textContent = metrics.totalStudents.toLocaleString();
    document.getElementById('avgQuizScore').textContent = metrics.avgQuizScore + '%';
    document.getElementById('completionRate').textContent = metrics.completionRate + '%';
    
    // Update trends
    updateTrend('boardPassTrend', metrics.boardPassTrend);
    updateTrend('studentsTrend', metrics.studentsTrend);
    updateTrend('quizScoreTrend', metrics.quizScoreTrend);
    updateTrend('completionTrend', metrics.completionTrend);
}

function updateTrend(elementId, trend) {
    const element = document.getElementById(elementId);
    const isPositive = trend.value >= 0;
    
    element.className = `metric-trend ${isPositive ? 'trend-up' : 'trend-down'}`;
    element.innerHTML = `
        <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i> 
        ${isPositive ? '+' : ''}${trend.value}% ${trend.period}
    `;
}

function updateCharts(chartData) {
    // Update board pass chart
    boardPassChart.data.labels = chartData.boardPass.labels;
    boardPassChart.data.datasets[0].data = chartData.boardPass.data;
    boardPassChart.update();
    
    // Update program distribution chart
    programChart.data.labels = chartData.programDistribution.labels;
    programChart.data.datasets[0].data = chartData.programDistribution.data;
    programChart.update();
    
    // Update subject performance chart
    subjectChart.data.labels = chartData.subjectPerformance.labels;
    subjectChart.data.datasets[0].data = chartData.subjectPerformance.data;
    subjectChart.update();
    
    // Update progress distribution chart
    progressChart.data.labels = chartData.progressDistribution.labels;
    progressChart.data.datasets[0].data = chartData.progressDistribution.data;
    progressChart.update();
    
    // Update batch performance chart
    batchChart.data.labels = chartData.batchPerformance.labels;
    batchChart.data.datasets[0].data = chartData.batchPerformance.data;
    batchChart.update();
}

function updateTables(tableData) {
    // Update top performers table
    updateTopPerformersTable(tableData.topPerformers);
    
    // Update bottom performers table
    updateBottomPerformersTable(tableData.bottomPerformers);
    
    // Update subject breakdown table
    updateSubjectBreakdownTable(tableData.subjectBreakdown);
}

function updateTopPerformersTable(data) {
    const tbody = document.getElementById('topPerformersTable');
    tbody.innerHTML = '';
    
    data.forEach((student, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><span class="badge bg-success">#${index + 1}</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <div class="fw-bold">${student.name}</div>
                        <small class="text-muted">${student.email}</small>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-info">${student.program}</span></td>
            <td><span class="fw-bold text-success">${student.score}%</span></td>
            <td>
                <div class="progress progress-modern">
                    <div class="progress-bar" style="width: ${student.progress}%"></div>
                </div>
                <small class="text-muted">${student.progress}%</small>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateBottomPerformersTable(data) {
    const tbody = document.getElementById('bottomPerformersTable');
    tbody.innerHTML = '';
    
    data.forEach(student => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-2">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <div class="fw-bold">${student.name}</div>
                        <small class="text-muted">${student.email}</small>
                    </div>
                </div>
            </td>
            <td><span class="badge bg-info">${student.program}</span></td>
            <td><span class="fw-bold text-warning">${student.score}%</span></td>
            <td>
                <span class="badge bg-warning">${student.issues}</span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewStudentDetail(${student.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateSubjectBreakdownTable(data) {
    const tbody = document.getElementById('subjectBreakdownTable');
    tbody.innerHTML = '';
    
    data.forEach(subject => {
        const row = document.createElement('tr');
        const difficultyColor = subject.difficulty === 'Hard' ? 'danger' : subject.difficulty === 'Medium' ? 'warning' : 'success';
        const trendIcon = subject.trend >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger';
        
        row.innerHTML = `
            <td class="fw-bold">${subject.name}</td>
            <td>${subject.totalStudents}</td>
            <td><span class="fw-bold">${subject.avgScore}%</span></td>
            <td>
                <div class="progress progress-modern">
                    <div class="progress-bar" style="width: ${subject.passRate}%"></div>
                </div>
                <small class="text-muted">${subject.passRate}%</small>
            </td>
            <td><span class="badge bg-${difficultyColor}">${subject.difficulty}</span></td>
            <td><i class="fas ${trendIcon}"></i> ${Math.abs(subject.trend)}%</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="viewSubjectDetail(${subject.id})">
                    <i class="fas fa-chart-bar"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Chart initialization functions
function initializeBoardPassChart() {
    const ctx = document.getElementById('boardPassChart').getContext('2d');
    boardPassChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Board Pass Rate (%)',
                data: [],
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function initializeProgramChart() {
    const ctx = document.getElementById('programDistributionChart').getContext('2d');
    programChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(240, 147, 251, 0.8)',
                    'rgba(79, 172, 254, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function initializeSubjectChart() {
    const ctx = document.getElementById('subjectPerformanceChart').getContext('2d');
    subjectChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Average Score (%)',
                data: [],
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function initializeProgressChart() {
    const ctx = document.getElementById('progressDistributionChart').getContext('2d');
    progressChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Number of Students',
                data: [],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.8)',   // 0-25%
                    'rgba(255, 193, 7, 0.8)',   // 26-50%
                    'rgba(13, 202, 240, 0.8)',  // 51-75%
                    'rgba(25, 135, 84, 0.8)'    // 76-100%
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initializeBatchChart() {
    const ctx = document.getElementById('batchPerformanceChart').getContext('2d');
    batchChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Average Performance (%)',
                data: [],
                backgroundColor: 'rgba(240, 147, 251, 0.8)',
                borderColor: 'rgba(240, 147, 251, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

// Export functions
function exportData(format) {
    const queryParams = new URLSearchParams(currentFilters);
    queryParams.append('format', format);
    
    showAlert(`Preparing ${format.toUpperCase()} export...`, 'info');
    
    window.open(`/admin/analytics/export?${queryParams}`, '_blank');
}

// Additional functions
function refreshData() {
    showAlert('Refreshing analytics data...', 'info');
    loadAnalyticsData();
}

function changeChartPeriod(period) {
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Reload chart data with new period
    currentFilters.period = period;
    loadAnalyticsData();
}

function changeBatchView(view) {
    // Update active button
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Load different batch view
    loadBatchView(view);
}

function loadBatchView(view) {
    // Implementation for different batch views
    console.log('Loading batch view:', view);
}

function viewAllStudents(type) {
    // Open modal or navigate to detailed student list
    console.log('Viewing all students:', type);
}

function viewStudentDetail(studentId) {
    // Load student detail modal
    const modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
    
    fetch(`/admin/analytics/student/${studentId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('studentDetailContent').innerHTML = data.html;
            modal.show();
        })
        .catch(error => {
            console.error('Error loading student details:', error);
            showAlert('Error loading student details', 'danger');
        });
}

function viewSubjectDetail(subjectId) {
    // Navigate to subject-specific analytics
    window.location.href = `/admin/analytics/subject/${subjectId}`;
}

function generateSubjectReport() {
    showAlert('Generating subject performance report...', 'info');
    
    const queryParams = new URLSearchParams(currentFilters);
    window.open(`/admin/analytics/subject-report?${queryParams}`, '_blank');
}

// Board Passer Management Functions
function uploadCSV() {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);
    
    if (!formData.get('csv_file') || !formData.get('exam_year') || !formData.get('board_exam')) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    showLoadingSpinner();
    
    fetch('/admin/analytics/upload-board-passers', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        if (data.success) {
            showNotification('Board passer data uploaded successfully!', 'success');
            $('#uploadModal').modal('hide');
            updateBoardPasserStats();
            loadAnalyticsData(); // Refresh all analytics
        } else {
            showNotification(data.message || 'Upload failed', 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Upload error:', error);
        showNotification('An error occurred during upload', 'error');
    });
}

function saveManualEntry() {
    const form = document.getElementById('manualEntryForm');
    const formData = new FormData(form);
    
    if (!formData.get('student_id') || !formData.get('board_exam') || !formData.get('result')) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    fetch('/admin/analytics/add-board-passer', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Board passer entry saved successfully!', 'success');
            $('#manualEntryModal').modal('hide');
            updateBoardPasserStats();
            form.reset();
        } else {
            showNotification(data.message || 'Save failed', 'error');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        showNotification('An error occurred while saving', 'error');
    });
}

function downloadTemplate() {
    const link = document.createElement('a');
    link.href = '/admin/analytics/download-template';
    link.download = 'board_passer_template.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showNotification('Template downloaded successfully!', 'info');
}

function updateBoardPasserStats() {
    fetch('/admin/analytics/board-passer-stats')
    .then(response => response.json())
    .then(data => {
        document.getElementById('totalPassers').textContent = data.total_passers || '--';
        document.getElementById('totalNonPassers').textContent = data.total_non_passers || '--';
        document.getElementById('overallPassRate').textContent = (data.pass_rate || 0) + '%';
        document.getElementById('lastUpdated').textContent = data.last_updated || '--';
    })
    .catch(error => {
        console.error('Error updating board passer stats:', error);
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Modal event handlers
document.getElementById('boardExam').addEventListener('change', function() {
    const otherDiv = document.getElementById('otherExamDiv');
    if (this.value === 'OTHER') {
        otherDiv.style.display = 'block';
        document.getElementById('otherExam').required = true;
    } else {
        otherDiv.style.display = 'none';
        document.getElementById('otherExam').required = false;
    }
});

// Initialize student select dropdown for manual entry
function initializeStudentSelect() {
    const studentSelect = document.getElementById('studentSelect');
    
    fetch('/admin/analytics/students-list')
    .then(response => response.json())
    .then(students => {
        studentSelect.innerHTML = '<option value="">Search and select student...</option>';
        students.forEach(student => {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = `${student.name} (${student.student_id}) - ${student.program}`;
            studentSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading students:', error);
    });
}

// Initialize when manual entry modal is shown
$('#manualEntryModal').on('show.bs.modal', function() {
    initializeStudentSelect();
});
</script>
@endpush

<meta name="csrf-token" content="{{ csrf_token() }}">
