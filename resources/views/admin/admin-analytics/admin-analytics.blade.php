@extends('admin.admin-dashboard-layout')

@section('title', 'Analytics Dashboard')

@push('styles')
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
                        <span class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-bar-chart-line fs-5"></i></span>
                    </div>
                    <div class="fw-bold fs-4" id="avgQuizScore">--%</div>
                    <div class="text-muted small">Avg Quiz Score</div>
                    <div class="text-success small" id="quizScoreTrend"><i class="bi bi-arrow-up"></i> +3.1% improvement</div>
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

    <!-- Loading Spinner -->
    <div class="d-flex justify-content-center align-items-center my-4" id="loadingSpinner" style="display:none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="ms-3">Loading analytics data...</span>
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
                    <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Student Progress Distribution</h5>
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
                                    <th>Program</th>
                                    <th>Completion Date</th>
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
        <!-- Students Needing Attention (unchanged) -->
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Students Needing Attention</h5>
                    <button class="btn btn-sm btn-outline-warning" onclick="viewAllStudents('bottom')">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
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
    <div class="card my-4" style="max-width:900px;margin:auto;min-height:180px;max-height:260px;">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="fw-bold mb-0"><i class="bi bi-layers me-2"></i>Batch Performance Analysis</h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary active" onclick="changeBatchView('overview')">Overview</button>
                <button type="button" class="btn btn-outline-primary" onclick="changeBatchView('comparison')">Comparison</button>
                <button type="button" class="btn btn-outline-primary" onclick="changeBatchView('detailed')">Detailed</button>
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
                        <i class="bi bi-save me-2"></i>Save Entry
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
    updateTrend('quizScoreTrend', metrics.quizScoreTrend);    updateTrend('quizScoreTrend', metrics.quizScoreTrend);
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
    // Update board pass chart
    boardPassChart.data.labels = chartData.boardPass.labels;
    boardPassChart.data.datasets[0].data = chartData.boardPass.data;
    boardPassChart.update();

    // Update program distribution chart
    programChart.data.labels = chartData.programDistribution.labels;
    programChart.data.datasets[0].data = chartData.programDistribution.data;
    programChart.update();

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
                        <i class="bi bi-person text-white"></i>
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
                        <i class="bi bi-person text-white"></i>
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
                    <i class="bi bi-eye"></i>
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
        const trendIcon = subject.trend >= 0 ? 'bi bi-arrow-up text-success' : 'bi bi-arrow-down text-danger';
        
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
            <td><i class="${trendIcon}"></i> ${Math.abs(subject.trend)}%</td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="viewSubjectDetail(${subject.id})">
                    <i class="bi bi-graph-bar"></i>
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
    
    // Check if user is admin
    @if(!isset($isAdmin) || !$isAdmin)
        showAlert('Export functionality is restricted to administrators only.', 'error');
        return;
    @endif
    
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
    @if(!isset($isAdmin) || !$isAdmin)
        showAlert('Complete data export is restricted to administrators only.', 'error');
        return;
    @endif
    
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

<meta name="csrf-token" content="{{ csrf_token() }}">
