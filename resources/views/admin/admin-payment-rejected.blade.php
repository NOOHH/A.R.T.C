@extends('admin.admin-dashboard-layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Payment Rejected</h1>
                    <p class="text-muted">Manage rejected payments and resubmissions</p>
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
                                        Rejected Payments
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejectedPaymentCount">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card fa-2x text-gray-300"></i>
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
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="resubmissionPaymentCount">0</div>
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
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolvedPaymentCount">0</div>
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
                                        Total Amount
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRejectedAmount">₱0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            <h6 class="m-0 font-weight-bold text-primary">Filter Rejected Payments</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or payment ID...">
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
                                <label for="methodFilter">Payment Method:</label>
                                <select class="form-control" id="methodFilter" onchange="applyFilters()">
                                    <option value="">All Methods</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="cash">Cash</option>
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
                                    <option value="amount_desc">Highest Amount</option>
                                    <option value="amount_asc">Lowest Amount</option>
                                    <option value="name_asc">Name A-Z</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Payments Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rejected Payments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="rejectedPaymentsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="150">Student Name</th>
                                    <th width="100">Amount</th>
                                    <th width="100">Method</th>
                                    <th width="80">Status</th>
                                    <th width="120">Rejected Date</th>
                                    <th width="120">Resubmitted Date</th>
                                    <th width="100">Rejected By</th>
                                    <th width="180">Rejection Reason</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rejectedPaymentsBody">
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Loading rejected payments...
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

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details & Rejection Management</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Original Payment -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Original Payment
                                </h6>
                            </div>
                            <div class="card-body" id="originalPaymentContent">
                                <!-- Original payment details will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resubmitted Payment (if any) -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-redo me-2"></i>Resubmitted Payment
                                    <span class="badge badge-light ms-2" id="paymentResubmissionBadge">None</span>
                                </h6>
                            </div>
                            <div class="card-body" id="resubmittedPaymentContent">
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
                            <div class="card-body" id="paymentRejectionDetailsContent">
                                <!-- Rejection details will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approvePaymentResubmissionBtn" style="display: none;">
                    <i class="fas fa-check me-1"></i>Approve Payment
                </button>
                <button type="button" class="btn btn-warning" id="requestPaymentChangesBtn">
                    <i class="fas fa-edit me-1"></i>Request Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let paymentsData = [];
let filteredPaymentsData = [];

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
    console.log('Loading rejected payments...');
    
    // Load rejected payments
    loadRejectedPayments();
    
    // Load statistics
    loadPaymentStatistics();
}

function loadRejectedPayments() {
    const tbody = document.getElementById('rejectedPaymentsBody');
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading rejected payments...</td></tr>';
    
    fetch(`/admin/api/payments/rejected?page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            console.log('Rejected payments loaded:', data);
            
            if (data.success) {
                paymentsData = data.payments;
                filteredPaymentsData = [...paymentsData];
                renderPaymentsTable();
                updatePagination(data.pagination);
            } else {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger">Failed to load rejected payments</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading rejected payments:', error);
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger">Error loading data</td></tr>';
        });
}

function loadPaymentStatistics() {
    fetch('/admin/api/payments/rejected/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('rejectedPaymentCount').textContent = data.stats.rejected_count || 0;
                document.getElementById('resubmissionPaymentCount').textContent = data.stats.resubmission_count || 0;
                document.getElementById('resolvedPaymentCount').textContent = data.stats.resolved_today || 0;
                document.getElementById('totalRejectedAmount').textContent = '₱' + (data.stats.total_amount || 0).toLocaleString();
            }
        })
        .catch(error => {
            console.error('Error loading payment statistics:', error);
        });
}

function renderPaymentsTable() {
    const tbody = document.getElementById('rejectedPaymentsBody');
    
    if (filteredPaymentsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">No rejected payments found</td></tr>';
        return;
    }
    
    tbody.innerHTML = filteredPaymentsData.map(payment => {
        const statusBadge = getPaymentStatusBadge(payment.payment_status);
        const rejectedDate = payment.rejected_at ? new Date(payment.rejected_at).toLocaleDateString() : '-';
        const resubmittedDate = payment.resubmitted_at ? new Date(payment.resubmitted_at).toLocaleDateString() : '-';
        
        return `
            <tr>
                <td>${payment.payment_id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">${payment.student_name || 'N/A'}</div>
                            <small class="text-muted">${payment.student_email || ''}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="font-weight-bold text-success">₱${parseFloat(payment.amount || 0).toLocaleString()}</span>
                </td>
                <td>
                    <span class="badge badge-info">${formatPaymentMethod(payment.payment_method)}</span>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <small class="text-muted">${rejectedDate}</small>
                </td>
                <td>
                    <small class="text-muted">${resubmittedDate}</small>
                </td>
                <td>
                    <small class="text-muted">${payment.rejected_by_name || 'System'}</small>
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 160px;" title="${payment.rejection_reason || ''}">
                        ${payment.rejection_reason || 'No reason provided'}
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewPaymentDetails(${payment.payment_id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${payment.payment_status === 'resubmitted' ? 
                            `<button class="btn btn-outline-success btn-sm" onclick="approvePaymentResubmission(${payment.payment_id})" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>` : 
                            ''
                        }
                        <button class="btn btn-outline-warning btn-sm" onclick="requestPaymentChanges(${payment.payment_id})" title="Request Changes">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function getPaymentStatusBadge(status) {
    switch (status) {
        case 'rejected':
            return '<span class="badge badge-danger">Rejected</span>';
        case 'resubmitted':
            return '<span class="badge badge-warning">Resubmitted</span>';
        case 'paid':
            return '<span class="badge badge-success">Approved</span>';
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}

function formatPaymentMethod(method) {
    switch (method) {
        case 'gcash':
            return 'GCash';
        case 'bank_transfer':
            return 'Bank Transfer';
        case 'credit_card':
            return 'Credit Card';
        case 'cash':
            return 'Cash';
        default:
            return method || 'Unknown';
    }
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const methodFilter = document.getElementById('methodFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const sortBy = document.getElementById('sortBy').value;
    
    filteredPaymentsData = paymentsData.filter(payment => {
        if (statusFilter && payment.payment_status !== statusFilter) return false;
        if (methodFilter && payment.payment_method !== methodFilter) return false;
        
        if (dateFilter) {
            const rejectedDate = new Date(payment.rejected_at);
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
    applyPaymentSorting(sortBy);
    
    renderPaymentsTable();
    updateDataTableInfo();
}

function applyPaymentSorting(sortBy) {
    switch (sortBy) {
        case 'rejected_at_desc':
            filteredPaymentsData.sort((a, b) => new Date(b.rejected_at) - new Date(a.rejected_at));
            break;
        case 'rejected_at_asc':
            filteredPaymentsData.sort((a, b) => new Date(a.rejected_at) - new Date(b.rejected_at));
            break;
        case 'amount_desc':
            filteredPaymentsData.sort((a, b) => parseFloat(b.amount || 0) - parseFloat(a.amount || 0));
            break;
        case 'amount_asc':
            filteredPaymentsData.sort((a, b) => parseFloat(a.amount || 0) - parseFloat(b.amount || 0));
            break;
        case 'name_asc':
            filteredPaymentsData.sort((a, b) => (a.student_name || '').localeCompare(b.student_name || ''));
            break;
    }
}

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    
    if (!searchTerm) {
        filteredPaymentsData = [...paymentsData];
    } else {
        filteredPaymentsData = paymentsData.filter(payment => {
            const name = (payment.student_name || '').toLowerCase();
            const email = (payment.student_email || '').toLowerCase();
            const id = payment.payment_id.toString();
            
            return name.includes(searchTerm) || 
                   email.includes(searchTerm) || 
                   id.includes(searchTerm);
        });
    }
    
    renderPaymentsTable();
    updateDataTableInfo();
}

function refreshTable() {
    currentPage = 1;
    loadRejectedPayments();
    loadPaymentStatistics();
}

function updatePagination(pagination) {
    totalPages = pagination.last_page || 1;
    currentPage = pagination.current_page || 1;
}

function updateDataTableInfo() {
    const total = filteredPaymentsData.length;
    const info = `Showing ${total} ${total === 1 ? 'entry' : 'entries'}`;
    document.getElementById('dataTableInfo').textContent = info;
}

function viewPaymentDetails(paymentId) {
    console.log('Viewing payment details for ID:', paymentId);
    
    // Load payment details
    fetch(`/admin/api/payments/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populatePaymentModal(data.payment);
                $('#paymentDetailsModal').modal('show');
            } else {
                alert('Failed to load payment details');
            }
        })
        .catch(error => {
            console.error('Error loading payment details:', error);
            alert('Error loading payment details');
        });
}

function populatePaymentModal(payment) {
    // Populate original payment
    document.getElementById('originalPaymentContent').innerHTML = generatePaymentHTML(payment.original_data || payment);
    
    // Populate resubmission if exists
    if (payment.payment_status === 'resubmitted' && payment.current_data) {
        document.getElementById('resubmittedPaymentContent').innerHTML = generatePaymentHTML(payment.current_data);
        document.getElementById('paymentResubmissionBadge').textContent = 'Available';
        document.getElementById('paymentResubmissionBadge').className = 'badge badge-success ms-2';
        document.getElementById('approvePaymentResubmissionBtn').style.display = 'inline-block';
    } else {
        document.getElementById('resubmittedPaymentContent').innerHTML = '<p class="text-muted text-center py-4">No resubmission yet</p>';
        document.getElementById('paymentResubmissionBadge').textContent = 'None';
        document.getElementById('paymentResubmissionBadge').className = 'badge badge-light ms-2';
        document.getElementById('approvePaymentResubmissionBtn').style.display = 'none';
    }
    
    // Populate rejection details
    document.getElementById('paymentRejectionDetailsContent').innerHTML = generatePaymentRejectionDetailsHTML(payment);
}

function generatePaymentHTML(data) {
    let html = '<div class="row">';
    
    // Payment Information
    html += `
        <div class="col-12 mb-3">
            <h6 class="text-primary"><i class="fas fa-credit-card me-1"></i>Payment Information</h6>
            <table class="table table-sm">
                <tr><td width="30%">Amount:</td><td class="font-weight-bold text-success">₱${parseFloat(data.amount || 0).toLocaleString()}</td></tr>
                <tr><td>Method:</td><td>${formatPaymentMethod(data.payment_method)}</td></tr>
                <tr><td>Reference Number:</td><td>${data.reference_number || 'N/A'}</td></tr>
                <tr><td>Receipt Number:</td><td>${data.receipt_number || 'N/A'}</td></tr>
            </table>
        </div>
    `;
    
    // Payment Proof
    if (data.payment_proof || data.receipt_path) {
        html += `
            <div class="col-12 mb-3">
                <h6 class="text-primary"><i class="fas fa-receipt me-1"></i>Payment Proof</h6>
                <div class="text-center">
                    <img src="${data.payment_proof || data.receipt_path}" 
                         class="img-fluid rounded border" 
                         style="max-height: 300px; cursor: pointer;" 
                         onclick="window.open(this.src, '_blank')"
                         alt="Payment Proof">
                    <br>
                    <small class="text-muted">Click to view full size</small>
                </div>
            </div>
        `;
    }
    
    html += '</div>';
    return html;
}

function generatePaymentRejectionDetailsHTML(payment) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><td width="30%">Rejected By:</td><td>${payment.rejected_by_name || 'System'}</td></tr>
                    <tr><td>Rejected Date:</td><td>${payment.rejected_at ? new Date(payment.rejected_at).toLocaleString() : 'N/A'}</td></tr>
                    <tr><td>Reason:</td><td>${payment.rejection_reason || 'No reason provided'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Payment Status:</h6>
                <div class="payment-status mb-3">
                    ${getPaymentStatusBadge(payment.payment_status)}
                </div>
                <h6>Student Information:</h6>
                <table class="table table-sm">
                    <tr><td>Name:</td><td>${payment.student_name || 'N/A'}</td></tr>
                    <tr><td>Email:</td><td>${payment.student_email || 'N/A'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    return html;
}

function approvePaymentResubmission(paymentId) {
    if (!confirm('Are you sure you want to approve this payment resubmission?')) return;
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('action', 'approve');
    
    fetch(`/admin/payments/${paymentId}/review`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment approved successfully!');
            $('#paymentDetailsModal').modal('hide');
            refreshTable();
        } else {
            alert('Failed to approve payment: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error approving payment:', error);
        alert('Error approving payment');
    });
}

function requestPaymentChanges(paymentId) {
    const reason = prompt('Please enter the reason for requesting payment changes:');
    if (!reason || !reason.trim()) return;
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('action', 'request_changes');
    formData.append('reason', reason.trim());
    
    fetch(`/admin/payments/${paymentId}/review`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Change request sent successfully!');
            $('#paymentDetailsModal').modal('hide');
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

.img-fluid {
    transition: transform 0.2s;
}

.img-fluid:hover {
    transform: scale(1.05);
}
</style>
@endsection
