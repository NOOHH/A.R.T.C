@extends('admin.admin-dashboard-layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Registration Rejected</h1>
                    <p class="text-muted">Manage rejected registrations and resubmissions</p>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Rejected Registrations
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejectedCount">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Resubmissions
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="resubmissionCount">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-redo fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Resolved Today
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolvedCount">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Avg Response Time
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgResponseTime">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="m-0 font-weight-bold text-primary">Filter Rejected Registrations</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or ID...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" onclick="performSearch()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="statusFilter">Status:</label>
                                <select class="form-control" id="statusFilter" onchange="applyFilters()">
                                    <option value="">All Status</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="resubmitted">Resubmitted</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="programFilter">Program:</label>
                                <select class="form-control" id="programFilter" onchange="applyFilters()">
                                    <option value="">All Programs</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dateFilter">Date Range:</label>
                                <select class="form-control" id="dateFilter" onchange="applyFilters()">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sortBy">Sort By:</label>
                                <select class="form-control" id="sortBy" onchange="applyFilters()">
                                    <option value="rejected_at_desc">Latest Rejected</option>
                                    <option value="rejected_at_asc">Oldest Rejected</option>
                                    <option value="resubmitted_at_desc">Latest Resubmitted</option>
                                    <option value="name_asc">Name A-Z</option>
                                    <option value="name_desc">Name Z-A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Registrations Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rejected Registrations</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="rejectedRegistrationsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="150">Student Name</th>
                                    <th width="120">Program</th>
                                    <th width="80">Status</th>
                                    <th width="120">Rejected Date</th>
                                    <th width="120">Resubmitted Date</th>
                                    <th width="100">Rejected By</th>
                                    <th width="200">Rejection Reason</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rejectedRegistrationsBody">
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Loading rejected registrations...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="dataTables_info" id="dataTableInfo">
                                Showing 0 to 0 of 0 entries
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Table navigation">
                                <ul class="pagination justify-content-end" id="pagination">
                                    <!-- Pagination will be inserted here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Details Modal -->
<div class="modal fade" id="registrationDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Details & Rejection Management</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Original Submission -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Original Submission
                                </h6>
                            </div>
                            <div class="card-body" id="originalSubmissionContent">
                                <!-- Original submission details will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resubmission (if any) -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-redo me-2"></i>Resubmission
                                    <span class="badge badge-light ms-2" id="resubmissionBadge">None</span>
                                </h6>
                            </div>
                            <div class="card-body" id="resubmissionContent">
                                <p class="text-muted text-center py-4">No resubmission yet</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rejection Details -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-times-circle me-2"></i>Rejection Details
                                </h6>
                            </div>
                            <div class="card-body" id="rejectionDetailsContent">
                                <!-- Rejection details will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approveResubmissionBtn" style="display: none;">
                    <i class="fas fa-check me-1"></i>Approve Resubmission
                </button>
                <button type="button" class="btn btn-warning" id="requestChangesBtn">
                    <i class="fas fa-edit me-1"></i>Request Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let registrationsData = [];
let filteredData = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadInitialData();
    setupEventListeners();
});

function setupEventListeners() {
    // Search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
}

function loadInitialData() {
    console.log('Loading rejected registrations...');
    
    // Load programs for filter
    loadPrograms();
    
    // Load rejected registrations
    loadRejectedRegistrations();
    
    // Load statistics
    loadStatistics();
}

function loadPrograms() {
    fetch('/admin/api/programs')
        .then(response => response.json())
        .then(data => {
            const programFilter = document.getElementById('programFilter');
            programFilter.innerHTML = '<option value="">All Programs</option>';
            
            if (data.success && data.programs) {
                data.programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.program_id;
                    option.textContent = program.program_name;
                    programFilter.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading programs:', error);
        });
}

function loadRejectedRegistrations() {
    const tbody = document.getElementById('rejectedRegistrationsBody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading rejected registrations...</td></tr>';
    
    fetch(`/admin/api/registrations/rejected?page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            console.log('Rejected registrations loaded:', data);
            
            if (data.success) {
                registrationsData = data.registrations;
                filteredData = [...registrationsData];
                renderRegistrationsTable();
                updatePagination(data.pagination);
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load rejected registrations</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading rejected registrations:', error);
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data</td></tr>';
        });
}

function loadStatistics() {
    fetch('/admin/api/registrations/rejected/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('rejectedCount').textContent = data.stats.rejected_count || 0;
                document.getElementById('resubmissionCount').textContent = data.stats.resubmission_count || 0;
                document.getElementById('resolvedCount').textContent = data.stats.resolved_today || 0;
                document.getElementById('avgResponseTime').textContent = data.stats.avg_response_time || '-';
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

function renderRegistrationsTable() {
    const tbody = document.getElementById('rejectedRegistrationsBody');
    
    if (filteredData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No rejected registrations found</td></tr>';
        return;
    }
    
    tbody.innerHTML = filteredData.map(registration => {
        const statusBadge = getStatusBadge(registration.status);
        const rejectedDate = registration.rejected_at ? new Date(registration.rejected_at).toLocaleDateString() : '-';
        const resubmittedDate = registration.resubmitted_at ? new Date(registration.resubmitted_at).toLocaleDateString() : '-';
        
        return `
            <tr>
                <td>${registration.registration_id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">${registration.firstname} ${registration.lastname}</div>
                            <small class="text-muted">${registration.email || ''}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-primary">${registration.program_name || 'N/A'}</span>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <small class="text-muted">${rejectedDate}</small>
                </td>
                <td>
                    <small class="text-muted">${resubmittedDate}</small>
                </td>
                <td>
                    <small class="text-muted">${registration.rejected_by_name || 'System'}</small>
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 180px;" title="${registration.rejection_reason || ''}">
                        ${registration.rejection_reason || 'No reason provided'}
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewRegistrationDetails(${registration.registration_id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${registration.status === 'resubmitted' ? 
                            `<button class="btn btn-outline-success btn-sm" onclick="approveResubmission(${registration.registration_id})" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>` : 
                            ''
                        }
                        <button class="btn btn-outline-warning btn-sm" onclick="requestMoreChanges(${registration.registration_id})" title="Request Changes">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusBadge(status) {
    switch (status) {
        case 'rejected':
            return '<span class="badge badge-danger">Rejected</span>';
        case 'resubmitted':
            return '<span class="badge badge-warning">Resubmitted</span>';
        case 'approved':
            return '<span class="badge badge-success">Approved</span>';
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const programFilter = document.getElementById('programFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const sortBy = document.getElementById('sortBy').value;
    
    filteredData = registrationsData.filter(registration => {
        if (statusFilter && registration.status !== statusFilter) return false;
        if (programFilter && registration.program_id != programFilter) return false;
        
        if (dateFilter) {
            const rejectedDate = new Date(registration.rejected_at);
            const now = new Date();
            
            switch (dateFilter) {
                case 'today':
                    if (rejectedDate.toDateString() !== now.toDateString()) return false;
                    break;
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (rejectedDate < weekAgo) return false;
                    break;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    if (rejectedDate < monthAgo) return false;
                    break;
            }
        }
        
        return true;
    });
    
    // Apply sorting
    applySorting(sortBy);
    
    renderRegistrationsTable();
    updateDataTableInfo();
}

function applySorting(sortBy) {
    switch (sortBy) {
        case 'rejected_at_desc':
            filteredData.sort((a, b) => new Date(b.rejected_at) - new Date(a.rejected_at));
            break;
        case 'rejected_at_asc':
            filteredData.sort((a, b) => new Date(a.rejected_at) - new Date(b.rejected_at));
            break;
        case 'resubmitted_at_desc':
            filteredData.sort((a, b) => {
                if (!a.resubmitted_at && !b.resubmitted_at) return 0;
                if (!a.resubmitted_at) return 1;
                if (!b.resubmitted_at) return -1;
                return new Date(b.resubmitted_at) - new Date(a.resubmitted_at);
            });
            break;
        case 'name_asc':
            filteredData.sort((a, b) => (a.firstname + ' ' + a.lastname).localeCompare(b.firstname + ' ' + b.lastname));
            break;
        case 'name_desc':
            filteredData.sort((a, b) => (b.firstname + ' ' + b.lastname).localeCompare(a.firstname + ' ' + a.lastname));
            break;
    }
}

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    
    if (!searchTerm) {
        filteredData = [...registrationsData];
    } else {
        filteredData = registrationsData.filter(registration => {
            const name = (registration.firstname + ' ' + registration.lastname).toLowerCase();
            const email = (registration.email || '').toLowerCase();
            const id = registration.registration_id.toString();
            
            return name.includes(searchTerm) || 
                   email.includes(searchTerm) || 
                   id.includes(searchTerm);
        });
    }
    
    renderRegistrationsTable();
    updateDataTableInfo();
}

function refreshTable() {
    currentPage = 1;
    loadRejectedRegistrations();
    loadStatistics();
}

function updatePagination(pagination) {
    totalPages = pagination.last_page || 1;
    currentPage = pagination.current_page || 1;
    
    // Update pagination UI here if needed
}

function updateDataTableInfo() {
    const total = filteredData.length;
    const info = `Showing ${total} ${total === 1 ? 'entry' : 'entries'}`;
    document.getElementById('dataTableInfo').textContent = info;
}

function viewRegistrationDetails(registrationId) {
    console.log('Viewing registration details for ID:', registrationId);
    
    // Load registration details
    fetch(`/admin/api/registrations/${registrationId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateRegistrationModal(data.registration);
                $('#registrationDetailsModal').modal('show');
            } else {
                alert('Failed to load registration details');
            }
        })
        .catch(error => {
            console.error('Error loading registration details:', error);
            alert('Error loading registration details');
        });
}

function populateRegistrationModal(registration) {
    // Populate original submission
    document.getElementById('originalSubmissionContent').innerHTML = generateSubmissionHTML(registration.original_data || registration);
    
    // Populate resubmission if exists
    if (registration.status === 'resubmitted' && registration.current_data) {
        document.getElementById('resubmissionContent').innerHTML = generateSubmissionHTML(registration.current_data);
        document.getElementById('resubmissionBadge').textContent = 'Available';
        document.getElementById('resubmissionBadge').className = 'badge badge-success ms-2';
        document.getElementById('approveResubmissionBtn').style.display = 'inline-block';
    } else {
        document.getElementById('resubmissionContent').innerHTML = '<p class="text-muted text-center py-4">No resubmission yet</p>';
        document.getElementById('resubmissionBadge').textContent = 'None';
        document.getElementById('resubmissionBadge').className = 'badge badge-light ms-2';
        document.getElementById('approveResubmissionBtn').style.display = 'none';
    }
    
    // Populate rejection details
    document.getElementById('rejectionDetailsContent').innerHTML = generateRejectionDetailsHTML(registration);
}

function generateSubmissionHTML(data) {
    // Generate HTML for submission data
    let html = '<div class="row">';
    
    // Personal Information
    html += `
        <div class="col-12 mb-3">
            <h6 class="text-primary"><i class="fas fa-user me-1"></i>Personal Information</h6>
            <table class="table table-sm">
                <tr><td width="30%">Name:</td><td>${data.firstname || ''} ${data.lastname || ''}</td></tr>
                <tr><td>Email:</td><td>${data.email || 'N/A'}</td></tr>
                <tr><td>Contact:</td><td>${data.contact_number || 'N/A'}</td></tr>
                <tr><td>Address:</td><td>${data.street_address || ''}, ${data.city || ''}, ${data.state_province || ''}</td></tr>
            </table>
        </div>
    `;
    
    // Program Information
    html += `
        <div class="col-12 mb-3">
            <h6 class="text-primary"><i class="fas fa-graduation-cap me-1"></i>Program Information</h6>
            <table class="table table-sm">
                <tr><td width="30%">Program:</td><td>${data.program_name || 'N/A'}</td></tr>
                <tr><td>Package:</td><td>${data.package_name || 'N/A'}</td></tr>
                <tr><td>Learning Mode:</td><td>${data.learning_mode || 'N/A'}</td></tr>
            </table>
        </div>
    `;
    
    // Documents
    if (data.good_moral || data.PSA || data.Course_Cert || data.TOR) {
        html += `
            <div class="col-12 mb-3">
                <h6 class="text-primary"><i class="fas fa-file-alt me-1"></i>Documents</h6>
                <table class="table table-sm">
        `;
        
        if (data.good_moral) html += `<tr><td width="30%">Good Moral:</td><td><a href="${data.good_moral}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td></tr>`;
        if (data.PSA) html += `<tr><td>PSA:</td><td><a href="${data.PSA}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td></tr>`;
        if (data.Course_Cert) html += `<tr><td>Course Certificate:</td><td><a href="${data.Course_Cert}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td></tr>`;
        if (data.TOR) html += `<tr><td>TOR:</td><td><a href="${data.TOR}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td></tr>`;
        
        html += '</table></div>';
    }
    
    html += '</div>';
    return html;
}

function generateRejectionDetailsHTML(registration) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><td width="30%">Rejected By:</td><td>${registration.rejected_by_name || 'System'}</td></tr>
                    <tr><td>Rejected Date:</td><td>${registration.rejected_at ? new Date(registration.rejected_at).toLocaleString() : 'N/A'}</td></tr>
                    <tr><td>Reason:</td><td>${registration.rejection_reason || 'No reason provided'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Rejected Fields:</h6>
                <div class="rejected-fields">
    `;
    
    if (registration.rejected_fields && Array.isArray(registration.rejected_fields)) {
        registration.rejected_fields.forEach(field => {
            html += `<span class="badge badge-danger me-1 mb-1">${field}</span>`;
        });
    } else {
        html += '<span class="text-muted">No specific fields marked</span>';
    }
    
    html += `
                </div>
            </div>
        </div>
    `;
    
    return html;
}

function approveResubmission(registrationId) {
    if (!confirm('Are you sure you want to approve this resubmission?')) return;
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('action', 'approve');
    
    fetch(`/admin/registrations/${registrationId}/review`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Resubmission approved successfully!');
            $('#registrationDetailsModal').modal('hide');
            refreshTable();
        } else {
            alert('Failed to approve resubmission: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error approving resubmission:', error);
        alert('Error approving resubmission');
    });
}

function requestMoreChanges(registrationId) {
    const reason = prompt('Please enter the reason for requesting more changes:');
    if (!reason || !reason.trim()) return;
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('action', 'request_changes');
    formData.append('reason', reason.trim());
    
    fetch(`/admin/registrations/${registrationId}/review`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Change request sent successfully!');
            $('#registrationDetailsModal').modal('hide');
            refreshTable();
        } else {
            alert('Failed to send change request: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error sending change request:', error);
        alert('Error sending change request');
    });
}
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.badge {
    font-size: 0.75em;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.modal-xl {
    max-width: 1200px;
}

.rejected-fields .badge {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.card.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
</style>
@endsection
