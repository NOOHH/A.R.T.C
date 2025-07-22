@extends('layouts.admin')

@section('title', 'Student Enrollment Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="bi bi-people-fill me-2"></i>Student Enrollment Management
                    </h3>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#batchEnrollModal">
                            <i class="bi bi-plus-circle me-1"></i>Batch Enroll Students
                        </button>
                        <button type="button" class="btn btn-info" onclick="exportEnrollments()">
                            <i class="bi bi-download me-1"></i>Export Enrollments
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(isset($dbError))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $dbError }}
                        </div>
                    @endif
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $totalEnrollments }}</h3>
                                            <p class="mb-0">Total Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-people fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $activeEnrollments }}</h3>
                                            <p class="mb-0">Active Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-check-circle fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ $pendingEnrollments }}</h3>
                                            <p class="mb-0">Pending Enrollments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-clock fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>{{ count($approvedStudents ?? []) }}</h3>
                                            <p class="mb-0">Registered Students</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-person-check fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enrollments and Quick Actions -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Enrollments</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadAllEnrollments()">
                                        View All
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="recentEnrollmentsTable">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Program</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">
                                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                        Loading recent enrollments...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="bi bi-person-plus me-2"></i>Quick Enroll Single Student</h5>
                                </div>
                                <div class="card-body">
                                    <form id="quickEnrollForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="quick_student_id" class="form-label">Select Student</label>
                                            <select name="student_id" id="quick_student_id" class="form-select" required>
                                                <option value="">Choose a student...</option>
                                                @foreach($approvedStudents ?? [] as $student)
                                                    <option value="{{ $student->student_id }}">
                                                        {{ $student->student_id }} - 
                                                        @if($student->user)
                                                            {{ $student->user->user_firstname }} {{ $student->user->user_lastname }}
                                                        @else
                                                            No user data
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="quick_program_id" class="form-label">Program</label>
                                            <select name="program_id" id="quick_program_id" class="form-select" required>
                                                <option value="">Select program...</option>
                                                @foreach($programs ?? [] as $program)
                                                    <option value="{{ $program->program_id }}">
                                                        {{ $program->program_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="quick_package_id" class="form-label">Package</label>
                                            <select name="package_id" id="quick_package_id" class="form-select" required>
                                                <option value="">Select package...</option>
                                                @foreach($packages ?? [] as $package)
                                                    <option value="{{ $package->package_id }}">
                                                        {{ $package->package_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="quick_enrollment_type" class="form-label">Type</label>
                                                    <select name="enrollment_type" id="quick_enrollment_type" class="form-select" required>
                                                        <option value="">Select type...</option>
                                                        <option value="Full">Full</option>
                                                        <option value="Modular">Modular</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="quick_learning_mode" class="form-label">Mode</label>
                                                    <select name="learning_mode" id="quick_learning_mode" class="form-select" required>
                                                        <option value="">Select mode...</option>
                                                        <option value="Synchronous">Synchronous</option>
                                                        <option value="Asynchronous">Asynchronous</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="quick_batch_id" class="form-label">Batch (Optional)</label>
                                            <select name="batch_id" id="quick_batch_id" class="form-select">
                                                <option value="">Individual Learning</option>
                                                @foreach($batches ?? [] as $batch)
                                                    <option value="{{ $batch->batch_id }}">
                                                        {{ $batch->batch_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <button type="button" class="btn btn-success w-100" onclick="processQuickEnroll()">
                                            <i class="bi bi-check-circle me-2"></i>Enroll Student
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All Enrollments Table (Initially Hidden) -->
                    <div class="row mt-4" id="allEnrollmentsSection" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">All Enrollments</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Student ID</th>
                                                    <th>Student Name</th>
                                                    <th>Program</th>
                                                    <th>Package</th>
                                                    <th>Type</th>
                                                    <th>Mode</th>
                                                    <th>Status</th>
                                                    <th>Payment</th>
                                                    <th>Batch</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="allEnrollmentsTableBody">
                                                <!-- Will be populated via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Batch Enrollment Modal -->
<div class="modal fade" id="batchEnrollModal" tabindex="-1" aria-labelledby="batchEnrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchEnrollModalLabel">Batch Enroll Multiple Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="batchEnrollForm">
                    @csrf
                    <div class="row">
                        <!-- Student Selection -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Select Students</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllStudents">
                                <label class="form-check-label fw-bold" for="selectAllStudents">
                                    Select All Students
                                </label>
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control" id="studentSearch" placeholder="Search students...">
                            </div>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;" id="studentsList">
                                @foreach($approvedStudents ?? [] as $student)
                                    <div class="form-check student-item">
                                        <input class="form-check-input student-checkbox" type="checkbox" value="{{ $student->student_id }}" id="student_{{ $student->student_id }}">
                                        <label class="form-check-label" for="student_{{ $student->student_id }}">
                                            {{ $student->student_id }} - 
                                            @if($student->user)
                                                {{ $student->user->user_firstname }} {{ $student->user->user_lastname }}
                                                <small class="text-muted d-block">{{ $student->user->user_email }}</small>
                                            @else
                                                <span class="text-muted">No user data</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <span id="selectedCount">0</span> students selected
                                </small>
                            </div>
                        </div>

                        <!-- Enrollment Details -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Enrollment Details</h6>
                            
                            <div class="mb-3">
                                <label for="batchProgram" class="form-label">Program *</label>
                                <select class="form-select" id="batchProgram" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs ?? [] as $program)
                                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="batchPackage" class="form-label">Package *</label>
                                <select class="form-select" id="batchPackage" name="package_id" required>
                                    <option value="">Select Package</option>
                                    @foreach($packages ?? [] as $package)
                                        <option value="{{ $package->package_id }}">{{ $package->package_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="batchEnrollmentType" class="form-label">Enrollment Type *</label>
                                        <select class="form-select" id="batchEnrollmentType" name="enrollment_type" required>
                                            <option value="">Select Type</option>
                                            <option value="Full">Full</option>
                                            <option value="Modular">Modular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="batchLearningMode" class="form-label">Learning Mode *</label>
                                        <select class="form-select" id="batchLearningMode" name="learning_mode" required>
                                            <option value="">Select Mode</option>
                                            <option value="Synchronous">Synchronous</option>
                                            <option value="Asynchronous">Asynchronous</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="batchBatch" class="form-label">Batch (Optional)</label>
                                <select class="form-select" id="batchBatch" name="batch_id">
                                    <option value="">Individual Learning</option>
                                    @foreach($batches ?? [] as $batch)
                                        <option value="{{ $batch->batch_id }}">{{ $batch->batch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="batchStartDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="batchStartDate" name="start_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processBatchEnrollment()">
                    <i class="bi bi-people-fill me-1"></i>Enroll Selected Students
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add More Enrollments Modal -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-labelledby="addEnrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEnrollmentModalLabel">Add More Enrollments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="studentInfo" class="alert alert-info mb-3">
                    <!-- Student info will be loaded here -->
                </div>
                
                <div id="existingEnrollments" class="mb-4">
                    <h6 class="fw-bold">Current Enrollments</h6>
                    <div id="enrollmentsList">
                        <!-- Current enrollments will be loaded here -->
                    </div>
                </div>

                <form id="addEnrollmentForm">
                    @csrf
                    <input type="hidden" id="addStudentId" name="student_id">
                    
                    <h6 class="fw-bold mb-3">New Enrollment</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addProgram" class="form-label">Program *</label>
                                <select class="form-select" id="addProgram" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs ?? [] as $program)
                                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addPackage" class="form-label">Package *</label>
                                <select class="form-select" id="addPackage" name="package_id" required>
                                    <option value="">Select Package</option>
                                    @foreach($packages ?? [] as $package)
                                        <option value="{{ $package->package_id }}">{{ $package->package_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addEnrollmentType" class="form-label">Enrollment Type *</label>
                                <select class="form-select" id="addEnrollmentType" name="enrollment_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Full">Full</option>
                                    <option value="Modular">Modular</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addLearningMode" class="form-label">Learning Mode *</label>
                                <select class="form-select" id="addLearningMode" name="learning_mode" required>
                                    <option value="">Select Mode</option>
                                    <option value="Synchronous">Synchronous</option>
                                    <option value="Asynchronous">Asynchronous</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="addBatch" class="form-label">Batch (Optional)</label>
                        <select class="form-select" id="addBatch" name="batch_id">
                            <option value="">Individual Learning</option>
                            @foreach($batches ?? [] as $batch)
                                <option value="{{ $batch->batch_id }}">{{ $batch->batch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processAddEnrollment()">
                    <i class="bi bi-plus-circle me-1"></i>Add Enrollment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Enrollments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="exportProgram" class="form-label">Program</label>
                        <select class="form-select" id="exportProgram" name="program_id">
                            <option value="">All Programs</option>
                            @foreach($programs ?? [] as $program)
                                <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exportStatus" class="form-label">Enrollment Status</label>
                        <select class="form-select" id="exportStatus" name="enrollment_status">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="exportType" class="form-label">Enrollment Type</label>
                        <select class="form-select" id="exportType" name="enrollment_type">
                            <option value="">All Types</option>
                            <option value="Full">Full</option>
                            <option value="Modular">Modular</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exportDateFrom" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="exportDateFrom" name="date_from">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exportDateTo" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="exportDateTo" name="date_to">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processExport()">
                    <i class="bi bi-download me-1"></i>Export CSV
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadRecentEnrollments();
    setupEventListeners();
});

function setupEventListeners() {
    // Select All functionality
    document.getElementById('selectAllStudents').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox:not([style*="display: none"])');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateSelectedCount();
    });

    // Update count when individual checkboxes change
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Student search functionality
    document.getElementById('studentSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const studentItems = document.querySelectorAll('.student-item');
        
        studentItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                // Uncheck hidden items
                const checkbox = item.querySelector('.student-checkbox');
                if (checkbox.checked) {
                    checkbox.checked = false;
                    updateSelectedCount();
                }
            }
        });
    });
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.student-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
}

function loadRecentEnrollments() {
    fetch('/admin/enrollments/recent')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.querySelector('#recentEnrollmentsTable tbody');
                if (data.enrollments.length > 0) {
                    tbody.innerHTML = data.enrollments.map(enrollment => `
                        <tr>
                            <td>
                                ${enrollment.student_id}<br>
                                <small class="text-muted">${enrollment.student_name || 'N/A'}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">${enrollment.program_name || 'N/A'}</span>
                            </td>
                            <td>
                                <span class="badge bg-${enrollment.enrollment_type === 'Full' ? 'success' : 'info'}">${enrollment.enrollment_type}</span>
                            </td>
                            <td>
                                <span class="badge bg-${enrollment.enrollment_status === 'approved' ? 'success' : 'warning'}">${enrollment.enrollment_status}</span>
                            </td>
                            <td>${new Date(enrollment.created_at).toLocaleDateString()}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addMoreEnrollments('${enrollment.student_id}')">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                No recent enrollments found
                            </td>
                        </tr>
                    `;
                }
            }
        })
        .catch(error => console.error('Error loading recent enrollments:', error));
}

function processQuickEnroll() {
    const form = document.getElementById('quickEnrollForm');
    const formData = new FormData(form);
    
    fetch('/admin/enrollments/quick-enroll', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Student enrolled successfully');
            form.reset();
            loadRecentEnrollments();
        } else {
            showAlert('error', data.message || 'Failed to enroll student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred during enrollment');
    });
}

function processBatchEnrollment() {
    const selectedStudents = [];
    document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
        selectedStudents.push(checkbox.value);
    });
    
    if (selectedStudents.length === 0) {
        showAlert('error', 'Please select at least one student');
        return;
    }
    
    const formData = new FormData(document.getElementById('batchEnrollForm'));
    formData.append('student_ids', JSON.stringify(selectedStudents));
    
    fetch('/admin/enrollments/batch-enroll', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `Successfully enrolled ${data.enrolled_count} students. ${data.failed_count} failed.`);
            document.getElementById('batchEnrollModal').querySelector('.btn-close').click();
            loadRecentEnrollments();
        } else {
            showAlert('error', data.message || 'Batch enrollment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred during batch enrollment');
    });
}

function addMoreEnrollments(studentId) {
    fetch(`/admin/students/${studentId}/enrollments`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update student info
                const studentInfo = document.getElementById('studentInfo');
                studentInfo.innerHTML = `
                    <strong>Student:</strong> ${data.student.student_id} - 
                    ${data.student.user ? data.student.user.user_firstname + ' ' + data.student.user.user_lastname : 'N/A'}
                    ${data.student.user ? `<br><strong>Email:</strong> ${data.student.user.user_email}` : ''}
                `;
                
                // Update existing enrollments
                const enrollmentsList = document.getElementById('enrollmentsList');
                if (data.enrollments.length > 0) {
                    enrollmentsList.innerHTML = data.enrollments.map(enrollment => `
                        <div class="border rounded p-2 mb-2">
                            <strong>${enrollment.program ? enrollment.program.program_name : 'N/A'}</strong> 
                            <span class="badge bg-${enrollment.enrollment_status === 'approved' ? 'success' : 'warning'}">${enrollment.enrollment_status}</span>
                            <br>
                            <small class="text-muted">Type: ${enrollment.enrollment_type} | Mode: ${enrollment.learning_mode}</small>
                        </div>
                    `).join('');
                } else {
                    enrollmentsList.innerHTML = '<p class="text-muted">No existing enrollments</p>';
                }
                
                // Set student ID
                document.getElementById('addStudentId').value = studentId;
                
                // Show modal
                new bootstrap.Modal(document.getElementById('addEnrollmentModal')).show();
            }
        })
        .catch(error => console.error('Error loading student data:', error));
}

function processAddEnrollment() {
    const form = document.getElementById('addEnrollmentForm');
    const formData = new FormData(form);
    
    fetch('/admin/enrollments/add-enrollment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Additional enrollment created successfully');
            document.getElementById('addEnrollmentModal').querySelector('.btn-close').click();
            loadRecentEnrollments();
        } else {
            showAlert('error', data.message || 'Failed to create enrollment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while creating the enrollment');
    });
}

function exportEnrollments() {
    new bootstrap.Modal(document.getElementById('exportModal')).show();
}

function processExport() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    
    // Convert form data to URL parameters
    const params = new URLSearchParams();
    for (const [key, value] of formData) {
        if (value) params.append(key, value);
    }
    
    // Download the file
    window.location.href = `/admin/enrollments/export?${params.toString()}`;
    
    // Close modal
    document.getElementById('exportModal').querySelector('.btn-close').click();
}

function loadAllEnrollments() {
    const section = document.getElementById('allEnrollmentsSection');
    section.style.display = 'block';
    section.scrollIntoView({ behavior: 'smooth' });
    
    fetch('/admin/enrollments/all')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('allEnrollmentsTableBody');
                if (data.enrollments.length > 0) {
                    tbody.innerHTML = data.enrollments.map(enrollment => `
                        <tr>
                            <td>${enrollment.enrollment_id}</td>
                            <td>${enrollment.student_id}</td>
                            <td>${enrollment.student_name || 'N/A'}</td>
                            <td><span class="badge bg-primary">${enrollment.program_name || 'N/A'}</span></td>
                            <td>${enrollment.package_name || 'N/A'}</td>
                            <td><span class="badge bg-${enrollment.enrollment_type === 'Full' ? 'success' : 'info'}">${enrollment.enrollment_type}</span></td>
                            <td><span class="badge bg-${enrollment.learning_mode === 'Synchronous' ? 'warning' : 'secondary'}">${enrollment.learning_mode}</span></td>
                            <td><span class="badge bg-${enrollment.enrollment_status === 'approved' ? 'success' : enrollment.enrollment_status === 'pending' ? 'warning' : 'danger'}">${enrollment.enrollment_status}</span></td>
                            <td><span class="badge bg-${enrollment.payment_status === 'paid' ? 'success' : enrollment.payment_status === 'pending' ? 'warning' : 'danger'}">${enrollment.payment_status}</span></td>
                            <td>${enrollment.batch_name || 'Individual'}</td>
                            <td>${new Date(enrollment.created_at).toLocaleDateString()}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addMoreEnrollments('${enrollment.student_id}')">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="12" class="text-center text-muted py-3">No enrollments found</td></tr>';
                }
            }
        })
        .catch(error => console.error('Error loading all enrollments:', error));
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        <i class="${icon} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alert, container.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endsection
