@extends('admin.admin-dashboard-layout')

@section('title', 'Analytics Dashboard')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Summary Cards Row (like the image) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-mortarboard fs-5"></i></span>
                    </div>
                    <div class="fw-bold fs-4" id="boardPassRate">--%</div>
                    <div class="text-muted small">Board Pass Rate</div>
                    <div class="text-success small" id="boardPassTrend"><i class="bi bi-arrow-up"></i> +5.2% from last period</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <span class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-people-fill fs-5"></i></span>
                    </div>
                    <div class="fw-bold fs-4" id="totalStudents">--</div>
                    <div class="text-muted small">Total Students</div>
                    <div class="text-success small" id="studentsTrend"><i class="bi bi-arrow-up"></i> +12% this month</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <span class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-clock-history fs-5"></i></span>
                    </div>
                    <div class="fw-bold fs-4" id="completionRate">--%</div>
                    <div class="text-muted small">Completion Rate</div>
                    <div class="text-success small" id="completionTrend"><i class="bi bi-arrow-up"></i> +7.8% this quarter</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end">
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
                    <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                </div>
            </form>

            <!-- Export Options (Admin Only) -->
            @if(isset($isAdmin) && $isAdmin)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border border-primary shadow-sm">
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-download me-2"></i>Data Export Options</h6>
                                <p class="text-muted small mb-0">Export analytics data in various formats. Admin access only.</p>
                            </div>
                            <div class="btn-group ms-auto">
                                <button type="button" class="btn btn-outline-danger" onclick="exportData('pdf')" title="Export as PDF">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="exportData('excel')" title="Export as JSON">
                                    <i class="bi bi-filetype-json me-1"></i>JSON
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="exportData('csv')" title="Export as CSV">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-database me-1"></i>Complete Export
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="exportCompleteData('csv')">
                                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>All Data (CSV)
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportCompleteData('json')">
                                            <i class="bi bi-filetype-json me-1"></i>All Data (JSON)
                                        </a></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-outline-primary" onclick="refreshData()" title="Refresh Data">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Management Tools (Admin Only) -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border border-info shadow-sm">
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="bi bi-gear me-2"></i>Management Tools</h6>
                                <p class="text-muted small mb-0">Access data management and specialized interfaces.</p>
                            </div>
                            <div class="btn-group ms-auto">
                                <a href="{{ route('admin.board-passers.index') }}" class="btn btn-info" title="Manage Board Exam Passers">
                                    <i class="bi bi-trophy me-1"></i>Manage Board Passers
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Data export functionality is restricted to administrators only.
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Referral Analytics Section (Admin Only) -->
    @if(isset($isAdmin) && $isAdmin)
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-share me-2"></i>Referral Analytics</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" onclick="refreshReferralData()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
                <button type="button" class="btn btn-outline-success" onclick="exportReferralData()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-people-fill fs-2 text-info"></i></div>
                            <h6 class="text-muted">Total Referrers</h6>
                            <h4 id="totalReferrers">--</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-person-plus-fill fs-2 text-success"></i></div>
                            <h6 class="text-muted">Total Referrals</h6>
                            <h4 id="totalReferrals">--</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-graph-up-arrow fs-2 text-primary"></i></div>
                            <h6 class="text-muted">Active Referrers</h6>
                            <h4 id="activeReferrers">--</h4>
                        </div>
                    </div>
                </div>
            </div>
            <h6 class="mb-3">Top Referrers</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="referralTable">
                    <thead class="table-light">
                        <tr>
                            <th>Referrer</th>
                            <th>Type</th>
                            <th>Referral Code</th>
                            <th>Total Referrals</th>
                        </tr>
                    </thead>
                    <tbody id="referralTableBody">
                        <tr>
                            <td colspan="4" class="text-center">Loading referral data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Board Passer Data Management -->
    <div class="card mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Board Passer Data Management</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-upload me-1"></i>Upload CSV
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                    <i class="bi bi-plus-lg me-1"></i>Manual Entry
                </button>
                <button type="button" class="btn btn-outline-info" onclick="downloadTemplate()">
                    <i class="bi bi-download me-1"></i>Template
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-check-circle-fill fs-2 text-success"></i></div>
                            <h6 class="text-muted">Board Passers</h6>
                            <h4 id="totalPassers">--</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-x-circle-fill fs-2 text-danger"></i></div>
                            <h6 class="text-muted">Non-Passers</h6>
                            <h4 id="totalNonPassers">--</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-percent fs-2 text-primary"></i></div>
                            <h6 class="text-muted">Pass Rate</h6>
                            <h4 id="overallPassRate">--%</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-light">
                        <div class="card-body">
                            <div class="mb-2"><i class="bi bi-clock-history fs-2 text-warning"></i></div>
                            <h6 class="text-muted">Last Updated</h6>
                            <small id="lastUpdated">--</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card shadow-sm h-100" style="min-height:260px;max-height:320px;">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Board Pass Rate Trend</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" onclick="changeChartPeriod('monthly')">Monthly</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('quarterly')">Quarterly</button>
                        <button type="button" class="btn btn-outline-primary" onclick="changeChartPeriod('yearly')">Yearly</button>
                    </div>
                </div>
                <div class="card-body p-2 overflow-auto" style="min-height:180px;max-height:240px;">
                    <canvas id="boardPassChart" height="180" style="width:100%;max-width:100%;display:block;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card shadow-sm h-100" style="min-height:260px;max-height:320px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Program Distribution</h5>
                </div>
                <div class="card-body p-2 overflow-auto" style="min-height:180px;max-height:240px;">
                    <canvas id="programDistributionChart" height="180" style="width:100%;max-width:100%;display:block;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Number of Students</h5>
                </div>
                <div class="card-body p-2 overflow-auto" style="min-height:180px;max-height:240px;">
                    <canvas id="progressDistributionChart" height="180" style="width:100%;max-width:100%;display:block;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Enrolled and Recent Payments -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Recently Enrolled</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Plan</th>
                                    <th>Enrollment Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentlyEnrolledTable">
                                <!-- Data will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Recent Payments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentPaymentsTable">
                                <!-- Data will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Tables -->
    <div class="row g-4">
        <!-- Recently Completed Section (replaces Top Performers) -->
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0"><i class="bi bi-check2-circle me-2"></i>Recently Completed</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Program</th>
                                    <th>Plan</th>
                                    <th>Completion Date</th>
                                    <th>Final Score</th>
                                </tr>
                            </thead>
                            <tbody id="recentlyCompletedTable">
                                <!-- Data will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Board Exam Passers Section -->
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Board Exam Passers</h5>
                    <a href="{{ route('admin.board-passers.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-gear me-1"></i>Manage
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Board Exam</th>
                                    <th>Result</th>
                                    <th>Year</th>
                                </tr>
                            </thead>
                            <tbody id="boardPassersTable">
                                <!-- Data will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Batch Performance Analysis -->
    <div class="card my-4" style="max-width:900px;margin:auto;min-height:180px;max-height:260px;">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="fw-bold mb-0"><i class="bi bi-layers me-2"></i>Batch Performance Analysis</h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary active" onclick="changeBatchView('overview', event)">Overview</button>
                <button type="button" class="btn btn-outline-primary" onclick="changeBatchView('comparison', event)">Comparison</button>
                <button type="button" class="btn btn-outline-primary" onclick="changeBatchView('detailed', event)">Detailed</button>
            </div>
        </div>
        <div class="card-body p-2 overflow-auto" style="min-height:120px;max-height:180px;">
            <canvas id="batchPerformanceChart" height="120" style="width:100%;max-width:100%;display:block;"></canvas>
        </div>
    </div>

    <!-- Subject-wise Performance Breakdown REMOVED -->

    <!-- Modals -->
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
                        <i class="bi bi-upload me-2"></i>Upload Board Passer Data
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
                                <i class="bi bi-info-circle me-1"></i>
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
                                <!-- Options will be loaded dynamically from programs table -->
                            </select>
                        </div>
                        <div class="mb-3" id="otherExamDiv" style="display: none;">
                            <label for="otherExam" class="form-label">Specify Other Exam</label>
                            <input type="text" class="form-control" id="otherExam" name="other_exam">
                        </div>
                    </form>
                    <div class="alert alert-info">
                        <h6><i class="bi bi-file-earmark-arrow-down me-2"></i>CSV Format Requirements:</h6>
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
                        <i class="bi bi-upload me-2"></i>Upload
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
                        <i class="bi bi-plus-lg me-2"></i>Manual Board Passer Entry
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
                                        <!-- Options will be loaded dynamically from programs table -->
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
                        <i class="bi bi-save me-2"></i>Save Entry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-analytics.js') }}"></script>
<script>
// Global variables
let boardPassChart, programChart, subjectChart, progressChart, batchChart;
let currentFilters = {
    year: '',
    month: '',
    program: '',
    batch: '',
    subject: '',
    period: 'monthly',
    batchView: 'overview'
};

// Declare functions that are called from onclick handlers immediately
function changeChartPeriod(period) {
    console.log('changeChartPeriod called with:', period);
    try {
        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        if (event && event.target) {
            event.target.classList.add('active');
        }
        
        // Reload chart data with new period
        currentFilters.period = period;
        loadAnalyticsData();
    } catch (error) {
        console.error('Error in changeChartPeriod:', error);
    }
}

function changeBatchView(view, e) {
    console.log('changeBatchView called with:', view);
    try {
        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Use the passed event parameter or the global event
        const evt = e || event;
        if (evt && evt.target) {
            evt.target.classList.add('active');
        }
        
        // Update chart based on view
        currentFilters.batchView = view;
        loadAnalyticsData();
    } catch (error) {
        console.error('Error in changeBatchView:', error);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== ANALYTICS DASHBOARD LOADING ===');
    console.log('Session data:', {
        user_type: '{{ session("user_type") }}',
        user_name: '{{ session("user_name") }}'
    });
    console.log('Auth::user()', null);
    console.log('Auth::guard("director")->user()', null);
    
    // Check if required elements exist
    const requiredElements = [
        'yearFilter', 'monthFilter', 'programFilter', 'batchFilter', 'subjectFilter',
        'boardPassRate', 'totalStudents', 'completionRate',
        'recentlyEnrolledTable', 'recentPaymentsTable', 'recentlyCompletedTable', 'boardPassersTable'
    ];
    
    let missingElements = [];
    requiredElements.forEach(elementId => {
        if (!document.getElementById(elementId)) {
            missingElements.push(elementId);
        }
    });
    
    if (missingElements.length > 0) {
        console.warn('Missing elements:', missingElements);
    } else {
        console.log('All required elements found');
    }
    
    console.log('Global variables initialized:', {
        boardPassChart: typeof boardPassChart,
        programChart: typeof programChart,
        progressChart: typeof progressChart,
        batchChart: typeof batchChart,
        currentFilters: currentFilters
    });
    
    try {
        initializeAnalytics();
        loadBatches();
        loadSubjects();
        loadAnalyticsData();
    } catch (error) {
        console.error('Initialization error:', error);
        showNotification('Failed to initialize analytics dashboard', 'error');
    }
});

function initializeAnalytics() {
    console.log('Initializing analytics dashboard...');
    
    // Initialize charts
    initializeBoardPassChart();
    initializeProgramChart();
    initializeSubjectChart();
    initializeProgressChart();
    initializeBatchChart();
    
    // Load programs for board exam dropdowns
    loadPrograms();
    
    // Set up event listeners
    setupEventListeners();
}

function loadPrograms() {
    console.log('Loading programs from API...');
    fetch('/admin/analytics/programs')
        .then(response => {
            console.log('Programs API response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(programs => {
            console.log('Programs received:', programs);
            
            // Check if programs is actually an array
            if (!Array.isArray(programs)) {
                console.error('Programs data is not an array:', programs);
                throw new Error('Invalid programs data format');
            }
            
            const boardExamSelect = document.getElementById('boardExam');
            const manualBoardExamSelect = document.getElementById('manualBoardExam');
            
            // Clear existing options (keep the first "Select" option)
            if (boardExamSelect) {
                console.log('Updating boardExam dropdown');
                boardExamSelect.innerHTML = '<option value="">Select Exam</option>';
                programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.name;
                    option.textContent = program.name + ' Board Exam';
                    boardExamSelect.appendChild(option);
                    console.log('Added option:', program.name + ' Board Exam');
                });
                
                // Add "Other" option at the end
                const otherOption = document.createElement('option');
                otherOption.value = 'OTHER';
                otherOption.textContent = 'Other';
                boardExamSelect.appendChild(otherOption);
            }
            
            if (manualBoardExamSelect) {
                console.log('Updating manualBoardExam dropdown');
                manualBoardExamSelect.innerHTML = '<option value="">Select Exam</option>';
                programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.name;
                    option.textContent = program.name + ' Board Exam';
                    manualBoardExamSelect.appendChild(option);
                    console.log('Added manual option:', program.name + ' Board Exam');
                });
                
                // Add "Other" option at the end
                const otherOption = document.createElement('option');
                otherOption.value = 'OTHER';
                otherOption.textContent = 'Other';
                manualBoardExamSelect.appendChild(otherOption);
            }
            
            console.log('Programs loaded successfully');
        })
        .catch(error => {
            console.error('Failed to load programs:', error);
            console.log('Using fallback options');
            // Fallback to basic options based on database programs if fetch fails
            const fallbackOptions = [
                { value: 'Nursing', text: 'Nursing Board Exam' },
                { value: 'Mechanical Engineer', text: 'Mechanical Engineer Board Exam' },
                { value: 'OTHER', text: 'Other' }
            ];
            
            [document.getElementById('boardExam'), document.getElementById('manualBoardExam')].forEach(select => {
                if (select) {
                    console.log('Setting fallback options for', select.id);
                    select.innerHTML = '<option value="">Select Exam</option>';
                    fallbackOptions.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt.value;
                        option.textContent = opt.text;
                        select.appendChild(option);
                        console.log('Added fallback option:', opt.text);
                    });
                }
            });
        });
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
    loadAnalyticsData();
}

function showLoading() {
    const metricsSection = document.getElementById('metricsSection');
    
    if (metricsSection) {
        metricsSection.style.opacity = '0.5';
    }
}

function hideLoading() {
    const metricsSection = document.getElementById('metricsSection');
    
    if (metricsSection) {
        metricsSection.style.opacity = '1';
    }
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
            if (subjectSelect) {
                subjectSelect.innerHTML = '<option value="">All Subjects</option>';
                
                if (data && Array.isArray(data)) {
                    data.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.name;
                        subjectSelect.appendChild(option);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
        });
}

function loadAnalyticsData() {
    console.log('Loading analytics data...');
    const queryParams = new URLSearchParams(currentFilters);
    const url = `/admin/analytics/data?${queryParams}`;
    console.log('Fetching URL:', url);
    console.log('Current filters:', currentFilters);
    
    fetch(url)
        .then(response => {
            console.log('Response received:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok,
                headers: Object.fromEntries(response.headers.entries())
            });
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Error response body:', text.substring(0, 500));
                    throw new Error(`HTTP ${response.status}: ${response.statusText}\nBody: ${text.substring(0, 200)}`);
                });
            }
            
            return response.text().then(text => {
                console.log('Raw response:', text.substring(0, 500));
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.log('Full response text:', text);
                    throw new Error('Invalid JSON response: ' + e.message);
                }
            });
        })
        .then(data => {
            console.log('Analytics data received:', data);
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.metrics) {
                updateMetrics(data.metrics);
            } else {
                console.warn('No metrics data received');
            }
            
            if (data.charts) {
                updateCharts(data.charts);
            } else {
                console.warn('No charts data received');
            }
            
                    if (data.tables) {
            console.log('Tables data received:', data.tables);
            console.log('Board passers in tables:', data.tables.boardPassers);
            console.log('Board passers type:', typeof data.tables.boardPassers);
            console.log('Board passers length:', data.tables.boardPassers ? data.tables.boardPassers.length : 'undefined');
            updateTables(data.tables);
        } else {
            console.warn('No tables data received');
        }
            
            hideLoading();
        })
        .catch(error => {
            console.error('Error loading analytics data:', error);
            hideLoading();
            showNotification('Error loading analytics data: ' + error.message, 'error');
            
            // Show fallback message in the dashboard
            document.getElementById('boardPassRate').textContent = 'Error';
            document.getElementById('totalStudents').textContent = 'Error';
            document.getElementById('completionRate').textContent = 'Error';
        });
}

function updateMetrics(metrics) {
    document.getElementById('boardPassRate').textContent = metrics.boardPassRate + '%';
    document.getElementById('totalStudents').textContent = metrics.totalStudents.toLocaleString();
    document.getElementById('completionRate').textContent = metrics.completionRate + '%';
    
    // Update trends
    updateTrend('boardPassTrend', metrics.boardPassTrend);
    updateTrend('studentsTrend', metrics.studentsTrend);
    updateTrend('completionTrend', metrics.completionTrend);
}

function updateTrend(elementId, trend) {
    const element = document.getElementById(elementId);
    const isPositive = trend.value >= 0;
    
    element.className = `metric-trend ${isPositive ? 'text-success' : 'text-danger'}`;
    element.innerHTML = `
        <i class="bi bi-arrow-${isPositive ? 'up' : 'down'}"></i> 
        ${isPositive ? '+' : ''}${trend.value}% ${trend.period}
    `;
}

function updateCharts(chartData) {
    if (!chartData) {
        console.warn('Chart data is undefined');
        return;
    }

    // Update board pass chart
    if (boardPassChart && chartData.boardPass) {
        boardPassChart.data.labels = chartData.boardPass.labels || [];
        boardPassChart.data.datasets[0].data = chartData.boardPass.data || [];
        boardPassChart.update();
    }

    // Update program distribution chart
    if (programChart && chartData.programDistribution) {
        programChart.data.labels = chartData.programDistribution.labels || [];
        programChart.data.datasets[0].data = chartData.programDistribution.data || [];
        programChart.update();
    }

    // Update progress distribution chart
    if (progressChart && chartData.progressDistribution) {
        progressChart.data.labels = chartData.progressDistribution.labels || [];
        progressChart.data.datasets[0].data = chartData.progressDistribution.data || [];
        progressChart.update();
    }

    // Update batch performance chart
    if (batchChart && chartData.batchPerformance) {
        batchChart.data.labels = chartData.batchPerformance.labels || [];
        batchChart.data.datasets[0].data = chartData.batchPerformance.data || [];
        batchChart.update();
    }
}

function updateTables(tableData) {
    console.log('updateTables called with data:', tableData);
    console.log('tableData keys:', Object.keys(tableData));
    
    // Only update tables that actually exist in the HTML
    try {
        // Update new tables
        if (tableData.recentlyEnrolled) {
            console.log('Updating recently enrolled table with', tableData.recentlyEnrolled.length, 'records');
            updateRecentlyEnrolledTable(tableData.recentlyEnrolled);
        }
        
        if (tableData.recentPayments) {
            console.log('Updating recent payments table with', tableData.recentPayments.length, 'records');
            updateRecentPaymentsTable(tableData.recentPayments);
        }
        
        if (tableData.recentlyCompleted) {
            console.log('Updating recently completed table with', tableData.recentlyCompleted.length, 'records');
            updateRecentlyCompletedTable(tableData.recentlyCompleted);
        }
        
        if (tableData.boardPassers) {
            console.log('Updating board passers table with', tableData.boardPassers.length, 'records');
            console.log('Board passers data:', tableData.boardPassers);
            updateBoardPassersTable(tableData.boardPassers);
        } else {
            console.log('No board passers data in tableData');
            console.log('Available keys in tableData:', Object.keys(tableData));
        }
    } catch (error) {
        console.error('Error updating tables:', error);
        console.error('Error stack:', error.stack);
    }
}

// New table update functions
function updateRecentlyEnrolledTable(data) {
    const tbody = document.getElementById('recentlyEnrolledTable');
    if (!tbody) {
        console.warn('recentlyEnrolledTable element not found');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (data && data.length > 0) {
        data.forEach(enrollment => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="fw-bold">${enrollment.name || 'Unknown'}</div>
                    <small class="text-muted">${enrollment.student_id || ''}</small>
                </td>
                <td><span class="badge bg-info">${enrollment.program || 'N/A'}</span></td>
                <td><span class="badge bg-primary">${enrollment.plan || 'N/A'}</span></td>
                <td><small>${enrollment.enrollment_date || 'N/A'}</small></td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No recent enrollments</td></tr>';
    }
}

function updateRecentPaymentsTable(data) {
    const tbody = document.getElementById('recentPaymentsTable');
    if (!tbody) {
        console.warn('recentPaymentsTable element not found');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (data && data.length > 0) {
        data.forEach(payment => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="fw-bold">${payment.student_name || 'Unknown'}</div>
                    <small class="text-muted">${payment.student_id || ''}</small>
                </td>
                <td><span class="badge bg-info">${payment.program || 'N/A'}</span></td>
                <td><span class="fw-bold text-success">₱${payment.amount || '0'}</span></td>
                <td><small>${payment.payment_date || 'N/A'}</small></td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No recent payments</td></tr>';
    }
}

function updateRecentlyCompletedTable(data) {
    const tbody = document.getElementById('recentlyCompletedTable');
    if (!tbody) {
        console.warn('recentlyCompletedTable element not found');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (data && data.length > 0) {
        data.forEach(completion => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="fw-bold">${completion.name || 'Unknown'}</div>
                    <small class="text-muted">${completion.student_id || ''}</small>
                </td>
                <td><small>${completion.email || 'N/A'}</small></td>
                <td><span class="badge bg-success">${completion.program || 'N/A'}</span></td>
                <td><span class="badge bg-primary">${completion.plan || 'N/A'}</span></td>
                <td><small>${completion.completion_date || 'N/A'}</small></td>
                <td><span class="badge bg-info">${completion.final_score || 'N/A'}</span></td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No recent completions</td></tr>';
    }
}

function updateBoardPassersTable(data) {
    console.log('updateBoardPassersTable called with data:', data);
    
    const tbody = document.getElementById('boardPassersTable');
    if (!tbody) {
        console.warn('boardPassersTable element not found');
        return;
    }
    
    console.log('Found boardPassersTable element, updating...');
    tbody.innerHTML = '';
    
    if (data && data.length > 0) {
        console.log('Processing', data.length, 'board passers');
        data.forEach((passer, index) => {
            console.log('Processing passer', index + 1, ':', passer);
            const row = document.createElement('tr');
            const resultBadge = passer.result === 'PASS' ? 'bg-success' : 'bg-danger';
            row.innerHTML = `
                <td>
                    <div class="fw-bold">${passer.full_name || passer.student_name || 'Unknown'}</div>
                    <small class="text-muted">${passer.student_id || ''}</small>
                </td>
                <td><span class="badge bg-info">${passer.program_name || passer.program || 'N/A'}</span></td>
                <td><span class="badge bg-secondary">${passer.board_exam || 'N/A'}</span></td>
                <td><span class="badge ${resultBadge}">${passer.result || 'N/A'}</span></td>
                <td><small>${passer.exam_year || 'N/A'}</small></td>
            `;
            tbody.appendChild(row);
        });
        console.log('Board passers table updated successfully');
    } else {
        console.log('No board passers data, showing empty message');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No board passer data</td></tr>';
    }
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
    const ctx = document.getElementById('subjectPerformanceChart');
    if (!ctx) {
        console.warn('Subject performance chart element not found, skipping initialization');
        return;
    }
    
    subjectChart = new Chart(ctx.getContext('2d'), {
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
    
    // Check if user is admin
    var isAdmin = {{ isset($isAdmin) && $isAdmin ? 'true' : 'false' }};
    if (!isAdmin) {
        showAlert('Export functionality is restricted to administrators only.', 'error');
        return;
    }
    
    try {
        const exportUrl = `/admin/analytics/export?${queryParams}`;
        
        if (format === 'csv') {
            // For CSV, create a download link
            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = `analytics-report-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showAlert('CSV export completed successfully!', 'success');
        } else {
            // For other formats, open in new window
            window.open(exportUrl, '_blank');
            showAlert(`${format.toUpperCase()} export opened in new window.`, 'success');
        }
    } catch (error) {
        console.error('Export error:', error);
        showAlert('Export failed. Please try again.', 'error');
    }
}

function exportCompleteData(format) {
    var isAdmin = {{ isset($isAdmin) && $isAdmin ? 'true' : 'false' }};
    if (!isAdmin) {
        showAlert('Complete data export is restricted to administrators only.', 'error');
        return;
    }
    
    // Show loading state
    showAlert(`Preparing complete ${format.toUpperCase()} export... This may take a while.`, 'info');
    
    try {
        const exportUrl = `/admin/analytics/export-complete?format=${format}`;
        
        if (format === 'csv') {
            // For CSV, create a download link
            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = `complete-analytics-export-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showAlert('Complete CSV export completed successfully!', 'success');
        } else {
            // For JSON, download as file
            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = `complete-analytics-export-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showAlert('Complete JSON export completed successfully!', 'success');
        }
    } catch (error) {
        console.error('Complete export error:', error);
        showAlert('Complete export failed. Please try again.', 'error');
    }
}

function printReport() {
    var isAdmin = {{ isset($isAdmin) && $isAdmin ? 'true' : 'false' }};
    if (!isAdmin) {
        showAlert('Print functionality is restricted to administrators only.', 'error');
        return;
    }
    
    showAlert('Preparing report for printing...', 'info');
    
    try {
        const queryParams = new URLSearchParams(currentFilters);
        queryParams.append('format', 'pdf');
        const printUrl = `/admin/analytics/export?${queryParams}`;
        
        // Open PDF in new window for printing
        const printWindow = window.open(printUrl, '_blank');
        
        // Add event listener to trigger print dialog when PDF loads
        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 1000);
        };
        
        showAlert('Print dialog will open when the report is ready.', 'success');
    } catch (error) {
        console.error('Print error:', error);
        showAlert('Print preparation failed. Please try again.', 'error');
    }
}

// Additional functions
function refreshData() {
    showAlert('Refreshing analytics data...', 'info');
    loadAnalyticsData();
}

// Functions already declared at top of script

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
    
    fetch('/admin/analytics/upload-board-passers', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
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
        console.error('Upload error:', error);
        showNotification('An error occurred during upload', 'error');
    });
}

function saveManualEntry() {
    const form = document.getElementById('manualEntryForm');
    const formData = new FormData(form);
    
    // Validate form inputs
    if (!formData.get('student_id')) {
        showNotification('Please select a student', 'error');
        return;
    }
    
    if (!formData.get('board_exam')) {
        showNotification('Please select a board exam', 'error');
        return;
    }
    
    if (!formData.get('exam_date')) {
        showNotification('Please select an exam date', 'error');
        return;
    }
    
    if (!formData.get('result')) {
        showNotification('Please select a result', 'error');
        return;
    }
    
    console.log('Saving board passer entry with data:', {
        student_id: formData.get('student_id'),
        board_exam: formData.get('board_exam'),
        exam_date: formData.get('exam_date'),
        result: formData.get('result')
    });
    
    // Show loading state
    const saveButton = document.querySelector('#manualEntryModal .btn-success');
    const originalButtonText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
    saveButton.disabled = true;
    
    fetch('/admin/analytics/add-board-passer', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Save response:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok
        });
        
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || `HTTP error ${response.status}: ${response.statusText}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Save response data:', data);
        
        if (data.success) {
            showNotification('Board passer entry saved successfully!', 'success');
            $('#manualEntryModal').modal('hide');
            updateBoardPasserStats();
            form.reset();
        } else {
            throw new Error(data.message || 'Save failed for unknown reason');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        showNotification('An error occurred while saving: ' + error.message, 'error');
    })
    .finally(() => {
        // Restore button state
        saveButton.innerHTML = originalButtonText;
        saveButton.disabled = false;
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
        const totalPassersEl = document.getElementById('totalPassers');
        const totalNonPassersEl = document.getElementById('totalNonPassers');
        const overallPassRateEl = document.getElementById('overallPassRate');
        const lastUpdatedEl = document.getElementById('lastUpdated');
        
        if (totalPassersEl) totalPassersEl.textContent = data.total_passers || '--';
        if (totalNonPassersEl) totalNonPassersEl.textContent = data.total_non_passers || '--';
        if (overallPassRateEl) overallPassRateEl.textContent = (data.pass_rate || 0) + '%';
        if (lastUpdatedEl) lastUpdatedEl.textContent = data.last_updated || '--';
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
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function showAlert(message, type = 'info') {
    showNotification(message, type);
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
    if (!studentSelect) {
        console.error('Student select element not found');
        return;
    }
    
    console.log('Initializing student select dropdown...');
    
    // Show loading indicator
    studentSelect.innerHTML = '<option value="">Loading students...</option>';
    studentSelect.disabled = true;
    
    fetch('/admin/analytics/students-list')
    .then(response => {
        console.log('Students list response:', {
            status: response.status,
            statusText: response.statusText,
            ok: response.ok
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Students data received:', data ? `${data.length} students` : 'No data');
        studentSelect.innerHTML = '<option value="">Search and select student...</option>';
        
        if (data && Array.isArray(data)) {
            if (data.length === 0) {
                console.warn('No students found in the database');
                studentSelect.innerHTML = '<option value="">No students found</option>';
            } else {
                data.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = `${student.name} (${student.student_id}) - ${student.program}`;
                    // Store program information in data attribute for later use
                    option.setAttribute('data-program', student.program);
                    studentSelect.appendChild(option);
                });
                console.log('Student select populated with', data.length, 'students');
            }
        } else {
            console.error('Invalid data format received:', data);
            throw new Error('Invalid data format received from server');
        }
        
        studentSelect.disabled = false;
    })
    .catch(error => {
        console.error('Error loading students:', error);
        studentSelect.innerHTML = '<option value="">Error loading students</option>';
        studentSelect.disabled = false;
        showNotification('Failed to load students list: ' + error.message, 'error');
    });
}

// Function to update board exam dropdown based on selected student's program
function updateBoardExamDropdown() {
    const studentSelect = document.getElementById('studentSelect');
    const boardExamSelect = document.getElementById('manualBoardExam');
    
    if (!studentSelect || !boardExamSelect) {
        console.error('Required select elements not found');
        return;
    }
    
    const selectedOption = studentSelect.options[studentSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        // Reset to all options if no student selected
        resetBoardExamDropdown();
        return;
    }
    
    const studentProgram = selectedOption.getAttribute('data-program');
    console.log('Selected student program:', studentProgram);
    
    // Fetch board exams from database
    fetch('/admin/analytics/board-exams')
        .then(response => response.json())
        .then(exams => {
            // Clear current options
            boardExamSelect.innerHTML = '<option value="">Select Exam</option>';
            
            // Filter exams based on student's program
            const programBoardExams = {
                'Nursing': ['NURSE'],
                'Mechanical Engineer': ['ME'],
                'Civil Engineer': ['CE'],
                'Electrical Engineer': ['EE'],
                'Accountancy': ['CPA'],
                'Education': ['LET'],
                'Teacher': ['LET'],
                'Teaching': ['LET']
            };
            
            const allowedExams = programBoardExams[studentProgram] || Object.keys(exams);
            
            // Add filtered options
            Object.entries(exams).forEach(([examCode, examName]) => {
                if (allowedExams.includes(examCode) || allowedExams.includes('OTHER')) {
                    boardExamSelect.innerHTML += `<option value="${examCode}">${examName}</option>`;
                }
            });
            
            console.log('Board exam dropdown updated for program:', studentProgram);
        })
        .catch(error => {
            console.error('Error loading board exams:', error);
            // Fallback to hardcoded options
            resetBoardExamDropdown();
        });
}

// Function to reset board exam dropdown to show all options
function resetBoardExamDropdown() {
    const boardExamSelect = document.getElementById('manualBoardExam');
    if (!boardExamSelect) return;
    
    // Fetch all board exams from database
    fetch('/admin/analytics/board-exams')
        .then(response => response.json())
        .then(exams => {
            boardExamSelect.innerHTML = '<option value="">Select Exam</option>';
            
            // Add all available exams
            Object.entries(exams).forEach(([examCode, examName]) => {
                boardExamSelect.innerHTML += `<option value="${examCode}">${examName}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading board exams:', error);
            // Fallback to hardcoded options
            boardExamSelect.innerHTML = `
                <option value="">Select Exam</option>
                <option value="CPA">CPA (Certified Public Accountant)</option>
                <option value="LET">LET (Licensure Examination for Teachers)</option>
                <option value="CE">CE (Civil Engineer)</option>
                <option value="ME">ME (Mechanical Engineer)</option>
                <option value="EE">EE (Electrical Engineer)</option>
                <option value="NURSE">Nursing Board Exam</option>
                <option value="OTHER">Other</option>
            `;
        });
}

// Initialize when manual entry modal is shown
$('#manualEntryModal').on('show.bs.modal', function() {
    initializeStudentSelect();
    resetBoardExamDropdown(); // Reset board exam dropdown when modal opens
});

// Add event listener for student selection change
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('studentSelect');
    if (studentSelect) {
        studentSelect.addEventListener('change', function() {
            updateBoardExamDropdown();
        });
    }
});

// Referral Analytics Functions
function refreshReferralData() {
    loadReferralAnalytics();
    showNotification('Referral data refreshed!', 'info');
}

function loadReferralAnalytics() {
  fetch('/api/referral/analytics')
    .then(res => res.json())
    .then(json => {
      const d = json.data;
      // If you want “totalReferrers” to be the number of keys in top_referrers:
      // Update stats
      document.getElementById('totalReferrers').textContent = d.top_referrers ? d.top_referrers.length : 0;
      document.getElementById('totalReferrals').textContent = d.total_referrals || 0;
      document.getElementById('activeReferrers').textContent = d.active_referrers || 0;

      // Populate referral table
      const body = document.getElementById('referralTableBody');
      if (d.top_referrers && Array.isArray(d.top_referrers) && d.top_referrers.length > 0) {
        body.innerHTML = d.top_referrers.map(r => `
          <tr>
            <td>${r.name || 'Unknown'}</td>
            <td><span class="badge bg-${r.type === 'director' ? 'primary' : 'success'}">
                 ${r.type ? r.type.charAt(0).toUpperCase() + r.type.slice(1) : 'Unknown'}</span></td>
            <td><code>${r.referral_code || 'N/A'}</code></td>
            <td>${r.total_referrals || 0}</td>
          </tr>
        `).join('');
      } else {
        body.innerHTML = '<tr><td colspan="4" class="text-center">No referral data available</td></tr>';
      }
    })
    .catch(err => {
      console.error('Error loading referral analytics:', err);
      showNotification('Failed to load referral data', 'error');
    });
}


function exportReferralData() {
    fetch('/api/referral/export')
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'referral_analytics.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        showNotification('Referral data exported successfully!', 'success');
    })
    .catch(error => {
        console.error('Error exporting referral data:', error);
        showNotification('Failed to export referral data', 'error');
    });
}

// Load referral analytics on page load if admin
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.referral-analytics-section')) {
        loadReferralAnalytics();
    }
});
</script>
@endpush