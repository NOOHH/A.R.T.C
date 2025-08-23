@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('content')
    @php
        // Get tenant branding
        $tenant = session('preview_tenant', 'default');
        $brandingConfig = config("customizations.$tenant.branding", []);
        $primaryColor = $brandingConfig['primary_color'] ?? '#0074D9';
        $tenantName = $brandingConfig['institution_name'] ?? 'SmartPrep Institute';
    @endphp

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Certificate Management</h1>
                <p class="text-muted">Manage and track student certificates</p>
            </div>
            @if(isset($isPreview) && $isPreview)
                <div class="alert alert-info d-inline-block mb-0" role="alert">
                    <i class="fas fa-eye me-1"></i> Preview Mode Active
                </div>
            @endif
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Certificates
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ isset($certificates) ? $certificates->count() : 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-certificate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Issued
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ isset($certificates) ? $certificates->where('status', 'issued')->count() : 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ isset($certificates) ? $certificates->where('status', 'pending')->count() : 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    This Month
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ isset($certificates) ? $certificates->where('issued_date', '>=', now()->startOfMonth())->count() : 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" style="background-color: {{ $primaryColor }}; border-color: {{ $primaryColor }};">
                        <i class="fas fa-plus me-1"></i> Generate Certificate
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-download me-1"></i> Export List
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="fas fa-filter me-1"></i> Filter Certificates
                    </button>
                    @if(isset($isPreview) && $isPreview)
                        <span class="badge bg-warning ms-auto align-self-center">
                            <i class="fas fa-eye me-1"></i> Preview Data
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Certificates Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold" style="color: {{ $primaryColor }};">
                    Certificate Records
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Actions:</div>
                        <a class="dropdown-item" href="#">Bulk Generate</a>
                        <a class="dropdown-item" href="#">Export All</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Settings</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(isset($certificates) && $certificates->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="certificatesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Certificate #</th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Type</th>
                                    <th>Issue Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($certificates as $certificate)
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold text-primary">
                                                {{ $certificate->certificate_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px; font-size: 14px;">
                                                        {{ strtoupper(substr($certificate->student_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $certificate->student_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-sm">{{ $certificate->program_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $certificate->certificate_type }}</span>
                                        </td>
                                        <td>
                                            <span class="text-sm">
                                                {{ \Carbon\Carbon::parse($certificate->issued_date)->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($certificate->status === 'issued')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check me-1"></i> Issued
                                                </span>
                                            @elseif($certificate->status === 'pending')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-question me-1"></i> {{ ucfirst($certificate->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Certificate actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary" title="View Certificate">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                @if($certificate->status === 'pending')
                                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Issue Certificate">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Revoke">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $certificates->count() }} of {{ $certificates->count() }} certificates
                        </div>
                        <nav aria-label="Certificate pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">1</span>
                                </li>
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            </ul>
                        </nav>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-certificate fa-3x text-gray-300"></i>
                        </div>
                        <h5 class="text-gray-600">No Certificates Found</h5>
                        <p class="text-muted mb-4">No certificates have been generated yet.</p>
                        <button class="btn btn-primary" style="background-color: {{ $primaryColor }}; border-color: {{ $primaryColor }};">
                            <i class="fas fa-plus me-1"></i> Generate First Certificate
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold" style="color: {{ $primaryColor }};">
                            Recent Certificate Activity
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($isPreview) && $isPreview)
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">Certificate Generated</div>
                                        <small class="text-muted">CERT-2025-002 for Carlos Garcia</small>
                                    </div>
                                    <small class="text-muted">2 hours ago</small>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">Certificate Issued</div>
                                        <small class="text-muted">CERT-2025-001 for Maria Santos</small>
                                    </div>
                                    <small class="text-muted">1 day ago</small>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">Bulk Certificate Generation</div>
                                        <small class="text-muted">5 certificates for Nursing Program</small>
                                    </div>
                                    <small class="text-muted">3 days ago</small>
                                </div>
                            </div>
                        @else
                            <p class="text-center text-muted">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($isPreview) && $isPreview)
        <!-- Preview Mode Footer -->
        <div class="fixed-bottom">
            <div class="alert alert-warning mb-0 rounded-0 text-center" role="alert">
                <strong><i class="fas fa-eye me-1"></i> Preview Mode:</strong> 
                This is a demonstration of the certificate management interface with mock data for tenant: <strong>{{ $tenant }}</strong>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable if not in preview mode
    @if(!isset($isPreview) || !$isPreview)
        $('#certificatesTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": true,
            "responsive": true
        });
    @endif

    // Certificate action handlers
    $('.btn-group .btn').on('click', function(e) {
        e.preventDefault();
        @if(isset($isPreview) && $isPreview)
            alert('This is a preview. Certificate actions are not functional.');
        @else
            // Real functionality would go here
        @endif
    });
});
</script>
@endpush
