@extends('admin.admin-dashboard-layout')

@section('title', 'Package Management')

@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/admin-packages.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('styles')
<style>
  /* Bootstrap 5 Admin Dashboard Styling */
  .border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
  }
  
  .border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
  }
  
  .border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
  }
  
  .border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
  }
  
  .text-xs {
    font-size: 0.7rem;
  }
  
  .text-gray-300 {
    color: #dddfeb !important;
  }
  
  .text-gray-500 {
    color: #858796 !important;
  }
  
  .text-gray-600 {
    color: #6e707e !important;
  }
  
  .text-gray-800 {
    color: #5a5c69 !important;
  }
  
  .font-weight-bold {
    font-weight: 700 !important;
  }
  
  .shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
  }
  
  .card {
    transition: all 0.3s;
  }
  
  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2) !important;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .container-fluid {
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }
  }

  /* Modal styling */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    backdrop-filter: blur(5px);
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  }

  .modal-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
  }

  .modal-header h2 {
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
  }

  /* Form styling */
  .form-group {
    margin-bottom: 25px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 1rem;
  }

  .form-group label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }

  /* Modal actions */
  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f8f9fa;
  }

  .btn-primary, .btn-secondary {
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
  }

  .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
  }
  
  /* Package item styling */
  .package-item {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
  }

  .package-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
  }

  .package-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    position: relative;
  }

  .package-name {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    text-align: center;
  }

  .package-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
  }

  .package-content {
    padding: 25px;
  }

  .package-description {
    color: #6c757d;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 20px;
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Package details grid */
  .package-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
  }

  .package-detail {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e9ecef;
  }

  .package-detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .package-detail-value {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
  }

  .package-price {
    font-size: 2rem;
    font-weight: 800;
    color: #28a745;
    text-align: center;
    margin-bottom: 25px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
  }

  /* Action buttons */
  .package-actions {
    display: flex;
    gap: 12px;
  }

  .btn-edit, .btn-delete {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s ease;
  }

  .btn-edit {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
  }

  .btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
  }

  .btn-delete {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220,53,69,0.3);
  }

  .btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220,53,69,0.4);
  }

  /* Empty state */
  .no-packages {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    border-radius: 20px;
    color: #6c757d;
  }

  .no-packages i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
  }

  .no-packages h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
  }

  /* Modal styling */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    backdrop-filter: blur(5px);
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  }

  .modal-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
  }

  .modal-header h2 {
    color: #2c3e50;
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
  }

  /* Form styling */
  .form-group {
    margin-bottom: 25px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 1rem;
  }

  .form-group label.required::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  /* Checkbox group styling */
  .checkbox-group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
  }

  .checkbox-option {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
    flex: 1;
  }

  .checkbox-option:hover {
    border-color: #667eea;
    background: #f0f3ff;
  }

  .checkbox-option input[type="radio"] {
    margin-right: 10px;
    transform: scale(1.2);
    accent-color: #667eea;
  }

  .checkbox-option label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
    color: #495057;
    display: flex;
    align-items: center;
  }

  .checkbox-option input[type="radio"]:checked + label {
    color: #667eea;
    font-weight: 600;
  }

  .checkbox-option:has(input[type="radio"]:checked) {
    border-color: #667eea;
    background: #f0f3ff;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }

  /* Selection sections */
  .selection-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
  }

  .selection-section h4 {
    color: #495057;
    margin-bottom: 15px;
    font-weight: 600;
  }

  .selection-info {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #1976d2;
  }

  .checkboxes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
    margin-top: 15px;
  }

  .checkbox-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }

  .checkbox-item:hover {
    background: #f8f9fa;
    border-color: #667eea;
  }

  .checkbox-item input[type="checkbox"] {
    width: auto;
    margin: 0;
    accent-color: #667eea;
  }

  .checkbox-item label {
    margin: 0;
    font-weight: 500;
    cursor: pointer;
    flex: 1;
  }

  .course-count {
    background: #fff3cd;
    color: #856404;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  /* Selected items display */
  .selected-display {
    background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
    padding: 20px;
    border-radius: 12px;
    margin-top: 15px;
    border: 1px solid #c3e6cb;
  }

  .selected-display h5 {
    color: #155724;
    margin-bottom: 10px;
    font-weight: 600;
  }

  .selected-badge {
    display: inline-block;
    background: #28a745;
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin: 3px;
  }

  /* Modal actions */
  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f8f9fa;
  }

  .btn-primary, .btn-secondary {
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
  }

  .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
  }

  /* Loading state */
  .loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    color: #6c757d;
  }

  .loading i {
    animation: spin 1s linear infinite;
    margin-right: 10px;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* Alert styling */
  .alert {
    border-radius: 12px;
    margin-bottom: 20px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }

  /* Responsive design */
  @media (max-width: 768px) {
    .packages-header {
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }
    
    .packages-header h1 {
      font-size: 2rem;
    }
    
    .package-list {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    
    .modal-content {
      padding: 30px 20px;
      margin: 20px;
    }
    
    .modal-actions {
      flex-direction: column;
    }
    
    .form-row {
      grid-template-columns: 1fr;
    }
    
    .checkboxes-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 480px) {
    .analytics-grid {
      grid-template-columns: 1fr;
    }
    
    .package-details {
      grid-template-columns: 1fr;
    }
  }

  
</style>


</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-box-open me-2"></i>Package Management
                </h1>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="fas fa-plus me-2"></i>Add New Package
                </button>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Analytics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Packages
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ count($packages) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
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
                                        Active Enrollments
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $packages->sum('enrollments_count') ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                        Total Value
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱{{ number_format($packages->sum('amount') ?? 0, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
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
                                        Modular Packages
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $packages->where('package_type', 'modular')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Package List</h6>
                </div>
                <div class="card-body">
                    @if(count($packages) > 0)
                        <div class="row">
                            @foreach($packages as $package)
                                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">{{ $package->package_name }}</h5>
                                            <span class="badge bg-{{ $package->package_type === 'modular' ? 'warning' : 'primary' }}">
                                                {{ ucfirst($package->package_type ?? 'Standard') }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-muted small mb-3">
                                                {{ Str::limit($package->description ?? 'No description available', 100) }}
                                            </p>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="text-xs text-muted">TYPE</div>
                                                        <div class="small fw-bold">{{ ucfirst($package->package_type ?? 'Standard') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-xs text-muted">SELECTION</div>
                                                    <div class="small fw-bold">{{ ucfirst($package->selection_type ?? 'Module') }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="row text-center mb-3">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="text-xs text-muted">COUNT MODE</div>
                                                        <div class="small fw-bold">
                                                            @if($package->selection_mode === 'courses')
                                                                <i class="fas fa-book me-1"></i>Course Based
                                                            @else
                                                                <i class="fas fa-layer-group me-1"></i>Module Based
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-xs text-muted">
                                                        @if($package->selection_mode === 'courses')
                                                            COURSE COUNT
                                                        @else
                                                            MODULE COUNT
                                                        @endif
                                                    </div>
                                                    <div class="small fw-bold">
                                                        @if($package->selection_mode === 'courses')
                                                            {{ $package->course_count ?? 'All' }}
                                                        @else
                                                            {{ $package->module_count ?? 'All' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mb-3">
                                                <h4 class="text-success fw-bold">₱{{ number_format($package->amount ?? 0, 2) }}</h4>
                                            </div>
                                            @php
                                                $periodParts = [];
                                                if (!empty($package->access_period_years)) $periodParts[] = $package->access_period_years . ' Year' . ($package->access_period_years > 1 ? 's' : '');
                                                if (!empty($package->access_period_months)) $periodParts[] = $package->access_period_months . ' Month' . ($package->access_period_months > 1 ? 's' : '');
                                                if (!empty($package->access_period_days)) $periodParts[] = $package->access_period_days . ' Day' . ($package->access_period_days > 1 ? 's' : '');
                                            @endphp
                                            @if(count($periodParts) > 0)
                                                <div class="text-center mb-2">
                                                    <span class="badge bg-info text-dark">Access Period: {{ implode(' ', $periodParts) }}</span>
                                                </div>
                                            @endif
                                            
                                            <div class="d-grid gap-2">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="editPackage({{ $package->package_id }})">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deletePackage({{ $package->package_id }})">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No packages found</h5>
                            <p class="text-gray-500">Create your first package to get started!</p>
                            <button class="btn btn-primary" onclick="showAddModal()">
                                <i class="fas fa-plus me-2"></i>Add New Package
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal" id="addPackageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle me-2"></i>Create New Package</h2>
        </div>
        <form id="addPackageForm" method="POST" action="{{ route('admin.packages.store') }}">
            @csrf
            
            <!-- Basic Information -->
            <div class="form-group">
                <label for="package_name" class="required">Package Name</label>
                <input type="text" id="package_name" name="package_name" required placeholder="Enter package name">
            </div>
            
            <div class="form-group">
                <label for="description" class="required">Description</label>
                <textarea id="description" name="description" rows="3" required placeholder="Enter package description"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="package_type" class="required">Package Type</label>
                    <select id="package_type" name="package_type" required>
                        <option value="">Select Package Type</option>
                        <option value="full">Full Enrollment</option>
                        <option value="modular">Modular Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount" class="required">Price (₱)</label>
                    <input type="number" id="amount" name="amount" min="0" step="0.01" required placeholder="0.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="access_period_days">Days</label>
                    <input type="number" id="access_period_days" name="access_period_days" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="access_period_months">Months</label>
                    <input type="number" id="access_period_months" name="access_period_months" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="access_period_years">Years</label>
                    <input type="number" id="access_period_years" name="access_period_years" min="0" placeholder="0">
                </div>
            </div>
            <small class="form-text text-muted mb-3">Set the access period for this package. The period starts after payment approval. Leave blank for unlimited access.</small>

            <div class="form-row">
                <div class="form-group">
                    <label for="module_count">Maximum Modules (Optional)</label>
                    <input type="number" id="module_count" name="module_count" min="1" max="50" placeholder="Leave empty for unlimited">
                    <small class="form-text text-muted">Set module limit (optional)</small>
                </div>
                <div class="form-group">
                    <label for="course_count">Maximum Courses (Optional)</label>
                    <input type="number" id="course_count" name="course_count" min="1" max="50" placeholder="Leave empty for unlimited">
                    <small class="form-text text-muted">Set course limit (optional)</small>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeAddModal()">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save me-1"></i>Create Package
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal" id="editPackageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit me-2"></i>Edit Package</h2>
        </div>
        <form id="editPackageForm" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Same form structure as add modal but with edit_ prefixes -->
            <div class="form-group">
                <label for="edit_package_name" class="required">Package Name</label>
                <input type="text" id="edit_package_name" name="package_name" required>
            </div>
            
            <div class="form-group">
                <label for="edit_description" class="required">Description</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_package_type" class="required">Package Type</label>
                    <select id="edit_package_type" name="package_type" required>
                        <option value="full">Full Enrollment</option>
                        <option value="modular">Modular Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_amount" class="required">Price (₱)</label>
                    <input type="number" id="edit_amount" name="amount" min="0" step="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_access_period_days">Days</label>
                    <input type="number" id="edit_access_period_days" name="access_period_days" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="edit_access_period_months">Months</label>
                    <input type="number" id="edit_access_period_months" name="access_period_months" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="edit_access_period_years">Years</label>
                    <input type="number" id="edit_access_period_years" name="access_period_years" min="0" placeholder="0">
                </div>
            </div>
            <small class="form-text text-muted mb-3">Set the access period for this package. The period starts after payment approval. Leave blank for unlimited access.</small>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_module_count">Maximum Modules (Optional)</label>
                    <input type="number" id="edit_module_count" name="module_count" min="1" max="50" placeholder="Leave empty for unlimited">
                    <small class="form-text text-muted">Set module limit (optional)</small>
                </div>
                <div class="form-group">
                    <label for="edit_course_count">Maximum Courses (Optional)</label>
                    <input type="number" id="edit_course_count" name="course_count" min="1" max="50" placeholder="Leave empty for unlimited">
                    <small class="form-text text-muted">Set course limit (optional)</small>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save me-1"></i>Update Package
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Global variables
let selectedModules = [];
let editSelectedModules = [];
let selectedCourses = [];
let editSelectedCourses = [];

// Utility functions
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) element.style.display = 'flex';
}

function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) element.style.display = 'none';
}

function showElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) element.style.display = 'block';
}

function hideElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) element.style.display = 'none';
}

// Modal functions
function showAddModal() {
    document.getElementById('addPackageModal').classList.add('active');
    // Reset form
    document.getElementById('addPackageForm').reset();
    selectedModules = [];
    selectedCourses = [];
    updateSelectedModulesDisplay();
    updateSelectedCoursesDisplay();
    // Hide conditional fields
    hideElement('selectionTypeGroup');
    hideElement('selectionModeGroup');
    hideElement('countLimitsGroup');
    hideElement('moduleSelection');
    hideElement('courseSelection');
}

function closeAddModal() {
    document.getElementById('addPackageModal').classList.remove('active');
}

function showEditModal() {
    document.getElementById('editPackageModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editPackageModal').classList.remove('active');
    editSelectedModules = [];
    editSelectedCourses = [];
    updateEditSelectedModulesDisplay();
    updateEditSelectedCoursesDisplay();
}

// Package type change handlers
function handlePackageTypeChange() {
    const packageType = document.getElementById('package_type').value;
    
    if (packageType === 'modular') {
        showElement('selectionTypeGroup');
        showElement('selectionModeGroup');
        showElement('countLimitsGroup');
        document.getElementById('selection_type').setAttribute('required', 'required');
        handleSelectionTypeChange();
        handleSelectionModeChange();
    } else {
        hideElement('selectionTypeGroup');
        hideElement('selectionModeGroup');
        hideElement('countLimitsGroup');
        hideElement('moduleSelection');
        hideElement('courseSelection');
        document.getElementById('selection_type').removeAttribute('required');
    }
}

function handleSelectionTypeChange() {
    const selectionType = document.getElementById('selection_type').value;
    const programId = document.getElementById('program_id').value;
    
    hideElement('moduleSelection');
    hideElement('courseSelection');
    
    if (!programId) {
        return; // No program selected yet
    }
    
    if (selectionType === 'module') {
        showElement('moduleSelection');
        loadModulesForProgram(programId, 'add');
    } else if (selectionType === 'course') {
        showElement('courseSelection');
        loadCoursesForProgram(programId, 'add');
    } else if (selectionType === 'both') {
        showElement('moduleSelection');
        showElement('courseSelection');
        loadModulesForProgram(programId, 'add');
        loadCoursesForProgram(programId, 'add');
    }
}

function handleSelectionModeChange() {
    // Selection mode just changes which field is primary, but both are always available
    // This function can be used for UI feedback if needed
}

function handleEditPackageTypeChange() {
    const packageType = document.getElementById('edit_package_type').value;
    
    if (packageType === 'modular') {
        showElement('editSelectionTypeGroup');
        showElement('editSelectionModeGroup');
        showElement('editCountLimitsGroup');
        document.getElementById('edit_selection_type').setAttribute('required', 'required');
        handleEditSelectionTypeChange();
        handleEditSelectionModeChange();
    } else {
        hideElement('editSelectionTypeGroup');
        hideElement('editSelectionModeGroup');
        hideElement('editCountLimitsGroup');
        hideElement('editModuleSelection');
        hideElement('editCourseSelection');
        document.getElementById('edit_selection_type').removeAttribute('required');
    }
}

function handleEditSelectionModeChange() {
    // Selection mode just changes which field is primary, but both are always available
    // This function can be used for UI feedback if needed
}

function handleEditSelectionTypeChange() {
    const selectionType = document.getElementById('edit_selection_type').value;
    const programId = document.getElementById('edit_program_id').value;
    
    hideElement('editModuleSelection');
    hideElement('editCourseSelection');
    
    if (!programId) {
        return;
    }
    
    if (selectionType === 'module') {
        showElement('editModuleSelection');
        loadModulesForProgram(programId, 'edit');
    } else if (selectionType === 'course') {
        showElement('editCourseSelection');
        loadCoursesForProgram(programId, 'edit');
    } else if (selectionType === 'both') {
        showElement('editModuleSelection');
        showElement('editCourseSelection');
        loadModulesForProgram(programId, 'edit');
        loadCoursesForProgram(programId, 'edit');
    }
}

// Program change handlers
function loadProgramData() {
    const programId = document.getElementById('program_id').value;
    const selectionType = document.getElementById('selection_type').value;
    
    if (!programId) return;
    
    if (selectionType === 'module' || selectionType === 'both') {
        loadModulesForProgram(programId, 'add');
    }
    if (selectionType === 'course' || selectionType === 'both') {
        loadCoursesForProgram(programId, 'add');
    }
}

function loadEditProgramData() {
    const programId = document.getElementById('edit_program_id').value;
    const selectionType = document.getElementById('edit_selection_type').value;
    
    if (!programId) return;
    
    if (selectionType === 'module' || selectionType === 'both') {
        loadModulesForProgram(programId, 'edit');
    }
    if (selectionType === 'course' || selectionType === 'both') {
        loadCoursesForProgram(programId, 'edit');
    }
}

// Load modules for program
function loadModulesForProgram(programId, mode) {
    const loadingId = mode === 'add' ? 'moduleLoading' : 'editModuleLoading';
    const containerId = mode === 'add' ? 'moduleCheckboxes' : 'editModuleCheckboxes';
    
    showLoading(loadingId);
    
    fetch(`/get-program-modules?program_id=${programId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoading(loadingId);
            
            if (data.success && data.modules) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';
                
                data.modules.forEach(module => {
                    const courseCount = module.courses ? module.courses.length : 0;
                    const checkbox = document.createElement('div');
                    checkbox.className = 'checkbox-item';
                    checkbox.innerHTML = `
                        <input type="checkbox" id="${mode}_module_${module.modules_id}" 
                               value="${module.modules_id}" 
                               onchange="handleModuleSelection(this, '${mode}')">
                        <label for="${mode}_module_${module.modules_id}">
                            ${module.module_name}
                            <span class="course-count">${courseCount} courses</span>
                        </label>
                    `;
                    container.appendChild(checkbox);
                });
            } else {
                document.getElementById(containerId).innerHTML = '<p class="text-muted">No modules found for this program.</p>';
            }
        })
        .catch(error => {
            hideLoading(loadingId);
            console.error('Error loading modules:', error);
            document.getElementById(containerId).innerHTML = '<p class="text-danger">Error loading modules. Please try again.</p>';
        });
}

// Load courses for program
function loadCoursesForProgram(programId, mode) {
    const loadingId = mode === 'add' ? 'courseLoading' : 'editCourseLoading';
    const containerId = mode === 'add' ? 'courseCheckboxes' : 'editCourseCheckboxes';
    
    showLoading(loadingId);
    
    fetch(`/get-program-modules?program_id=${programId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoading(loadingId);
            
            if (data.success && data.modules) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';
                
                let totalCourses = 0;
                data.modules.forEach(module => {
                    if (module.courses && module.courses.length > 0) {
                        // Add module header
                        const moduleHeader = document.createElement('div');
                        moduleHeader.className = 'module-header';
                        moduleHeader.innerHTML = `<h6 class="text-primary mb-2"><i class="fas fa-layer-group me-1"></i>${module.module_name}</h6>`;
                        container.appendChild(moduleHeader);
                        
                        // Add courses for this module
                        module.courses.forEach(course => {
                            totalCourses++;
                            const checkbox = document.createElement('div');
                            checkbox.className = 'checkbox-item';
                            checkbox.innerHTML = `
                                <input type="checkbox" id="${mode}_course_${course.subject_id}" 
                                       value="${course.subject_id}" 
                                       data-module-id="${module.modules_id}"
                                       onchange="handleCourseSelection(this, '${mode}')">
                                <label for="${mode}_course_${course.subject_id}">
                                    ${course.subject_name}
                                </label>
                            `;
                            container.appendChild(checkbox);
                        });
                    }
                });
                
                if (totalCourses === 0) {
                    container.innerHTML = '<p class="text-muted">No courses found for this program.</p>';
                }
            } else {
                document.getElementById(containerId).innerHTML = '<p class="text-muted">No courses found for this program.</p>';
            }
        })
        .catch(error => {
            hideLoading(loadingId);
            console.error('Error loading courses:', error);
            document.getElementById(containerId).innerHTML = '<p class="text-danger">Error loading courses. Please try again.</p>';
        });
}

// Handle module selection
function handleModuleSelection(checkbox, mode) {
    const moduleId = checkbox.value;
    const moduleLabel = checkbox.nextElementSibling.textContent.trim();
    
    if (mode === 'add') {
        if (checkbox.checked) {
            selectedModules.push({ id: moduleId, name: moduleLabel });
        } else {
            selectedModules = selectedModules.filter(m => m.id !== moduleId);
        }
        updateSelectedModulesDisplay();
    } else {
        if (checkbox.checked) {
            editSelectedModules.push({ id: moduleId, name: moduleLabel });
        } else {
            editSelectedModules = editSelectedModules.filter(m => m.id !== moduleId);
        }
        updateEditSelectedModulesDisplay();
    }
}

// Handle course selection
function handleCourseSelection(checkbox, mode) {
    const courseId = checkbox.value;
    const moduleId = checkbox.getAttribute('data-module-id');
    const courseLabel = checkbox.nextElementSibling.textContent.trim();
    
    if (mode === 'add') {
        if (checkbox.checked) {
            selectedCourses.push({ id: courseId, name: courseLabel, module_id: moduleId });
        } else {
            selectedCourses = selectedCourses.filter(c => c.id !== courseId);
        }
        updateSelectedCoursesDisplay();
    } else {
        if (checkbox.checked) {
            editSelectedCourses.push({ id: courseId, name: courseLabel, module_id: moduleId });
        } else {
            editSelectedCourses = editSelectedCourses.filter(c => c.id !== courseId);
        }
        updateEditSelectedCoursesDisplay();
    }
}

// Update displays
function updateSelectedModulesDisplay() {
    const display = document.getElementById('selectedModulesDisplay');
    const count = document.getElementById('selectedModulesCount');
    const list = document.getElementById('selectedModulesList');
    
    count.textContent = selectedModules.length;
    list.innerHTML = selectedModules.map(module => 
        `<span class="selected-badge">${module.name}</span>`
    ).join('');
    
    display.style.display = selectedModules.length > 0 ? 'block' : 'none';
}

function updateEditSelectedModulesDisplay() {
    const display = document.getElementById('editSelectedModulesDisplay');
    const count = document.getElementById('editSelectedModulesCount');
    const list = document.getElementById('editSelectedModulesList');
    
    count.textContent = editSelectedModules.length;
    list.innerHTML = editSelectedModules.map(module => 
        `<span class="selected-badge">${module.name}</span>`
    ).join('');
    
    display.style.display = editSelectedModules.length > 0 ? 'block' : 'none';
}

function updateSelectedCoursesDisplay() {
    const display = document.getElementById('selectedCoursesDisplay');
    const count = document.getElementById('selectedCoursesCount');
    const list = document.getElementById('selectedCoursesList');
    
    count.textContent = selectedCourses.length;
    list.innerHTML = selectedCourses.map(course => 
        `<span class="selected-badge">${course.name}</span>`
    ).join('');
    
    display.style.display = selectedCourses.length > 0 ? 'block' : 'none';
}

function updateEditSelectedCoursesDisplay() {
    const display = document.getElementById('editSelectedCoursesDisplay');
    const count = document.getElementById('editSelectedCoursesCount');
    const list = document.getElementById('editSelectedCoursesList');
    
    count.textContent = editSelectedCourses.length;
    list.innerHTML = editSelectedCourses.map(course => 
        `<span class="selected-badge">${course.name}</span>`
    ).join('');
    
    display.style.display = editSelectedCourses.length > 0 ? 'block' : 'none';
}

// Edit package function
function editPackage(packageId) {
    fetch(`/admin/packages/${packageId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const packageData = data.package;
                
                // Fill form fields
                document.getElementById('edit_package_name').value = packageData.package_name || '';
                document.getElementById('edit_description').value = packageData.description || '';
                document.getElementById('edit_package_type').value = packageData.package_type || 'full';
                document.getElementById('edit_selection_type').value = packageData.selection_type || 'module';
                document.getElementById('edit_amount').value = packageData.amount || '';
                document.getElementById('edit_program_id').value = packageData.program_id || '';
                document.getElementById('edit_module_count').value = packageData.module_count || '';
                document.getElementById('edit_course_count').value = packageData.course_count || '';
                document.getElementById('edit_access_period_days').value = packageData.access_period_days || '';
                document.getElementById('edit_access_period_months').value = packageData.access_period_months || '';
                document.getElementById('edit_access_period_years').value = packageData.access_period_years || '';
                
                // Set selection mode
                const selectionMode = packageData.selection_mode || 'modules';
                if (selectionMode === 'courses') {
                    document.getElementById('edit_selection_mode_courses').checked = true;
                } else {
                    document.getElementById('edit_selection_mode_modules').checked = true;
                }
                
                // Set form action
                document.getElementById('editPackageForm').action = `/admin/packages/${packageId}`;
                
                // Handle package type specific fields
                handleEditPackageTypeChange();
                
                // Load program data
                if (packageData.program_id) {
                    loadEditProgramData();
                }
                
                // Pre-select existing modules and courses
                if (packageData.modules) {
                    editSelectedModules = packageData.modules.map(m => ({ 
                        id: m.modules_id, 
                        name: m.module_name 
                    }));
                }
                if (packageData.courses) {
                    editSelectedCourses = packageData.courses.map(c => ({ 
                        id: c.subject_id, 
                        name: c.subject_name,
                        module_id: c.module_id
                    }));
                }
                
                // Show modal
                showEditModal();
            } else {
                alert('Error loading package data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading package:', error);
            alert('Error loading package data. Please try again.');
        });
}

// Delete package function
function deletePackage(packageId) {
    if (confirm('Are you sure you want to delete this package? This action cannot be undone.')) {
        fetch(`/admin/packages/${packageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            
            // Handle different status codes
            if (response.status === 400) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Bad Request: Cannot delete package');
                });
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Package deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting package: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Error deleting package: ' + error.message);
        });
    }
}

// Form submission handlers
document.getElementById('addPackageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add selected modules
    selectedModules.forEach(module => {
        formData.append('selected_modules[]', module.id);
    });
    
    // Add selected courses
    selectedCourses.forEach(course => {
        formData.append('selected_courses[]', course.id);
    });
    
    // Disable submit button to prevent double submission
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error creating package: ' + (data.message || 'Unknown error'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating package. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

document.getElementById('editPackageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add selected modules
    editSelectedModules.forEach(module => {
        formData.append('selected_modules[]', module.id);
    });
    
    // Add selected courses
    editSelectedCourses.forEach(course => {
        formData.append('selected_courses[]', course.id);
    });
    
    // Disable submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating package: ' + (data.message || 'Unknown error'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating package. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Close modals on outside click
document.getElementById('addPackageModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editPackageModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Package management initialized');
});
</script>
@endsection
