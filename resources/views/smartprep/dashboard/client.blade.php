<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Client Dashboard v2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!-- DEBUG: Professional Dashboard v2.0 Loading -->
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: var(--gray-50);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            margin: 0;
            color: var(--gray-800);
            line-height: 1.6;
        }
        
        /* Top Navigation */
        .top-navbar {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            text-decoration: none;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--gray-600) !important;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gray-100);
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link.active {
            background: var(--primary-color);
            color: var(--white) !important;
        }
        
        /* Dashboard Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--white);
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="rgba(255,255,255,0.05)"><circle cx="20" cy="20" r="2"/></g></svg>');
            opacity: 0.3;
        }
        
        .dashboard-header .container {
            position: relative;
            z-index: 2;
        }
        
        /* Stats Cards */
        .stats-grid {
            margin-top: -1rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .stats-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            opacity: 0.8;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: var(--gray-500);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Content Cards */
        .content-card {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .content-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .card-header-custom {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem;
        }
        
        .card-header-custom h5 {
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            font-size: 1.125rem;
        }
        
        .card-body-custom {
            padding: 1.5rem;
        }
        
        /* Action Card */
        .action-card {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transition: all 0.5s ease;
            transform: scale(0);
        }
        
        .action-card:hover::before {
            transform: scale(1);
        }
        
        .action-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }
        
        /* Buttons */
        .btn-primary-custom {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: var(--white);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .btn-primary-custom:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-1px);
            color: var(--white);
        }
        
        .btn-outline-custom {
            background: var(--white);
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .btn-outline-custom:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-800);
        }
        
        .btn-sm-custom {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        /* Table Styles */
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-modern thead th {
            background: var(--gray-50);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .table-modern thead th:first-child {
            border-radius: 8px 0 0 0;
        }
        
        .table-modern thead th:last-child {
            border-radius: 0 8px 0 0;
        }
        
        .table-modern tbody td {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--gray-200);
            vertical-align: middle;
        }
        
        .table-modern tbody tr:hover {
            background: var(--gray-50);
        }
        
        /* Badges */
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .badge-success {
            background: #f0fff4;
            color: #276749;
        }
        
        .badge-warning {
            background: #fef5e7;
            color: #92400e;
        }
        
        .badge-info {
            background: #e0f7ff;
            color: #075985;
        }
        
        .badge-danger {
            background: #fed7d7;
            color: #c53030;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--gray-500);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.4;
        }
        
        /* Modal Improvements */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .modal-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }
        
        .modal-title {
            font-weight: 700;
            color: var(--gray-900);
        }
        
        .form-control-modern {
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: var(--white);
        }
        
        .form-control-modern:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem 0;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .action-card {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- DEBUG BANNER - Remove in production -->
    <div style="background: #10b981; color: white; text-align: center; padding: 5px; font-weight: bold;">
        🎨 PROFESSIONAL DASHBOARD v2.0 - CHANGES APPLIED! 🎨
    </div>
    
    <!-- Top Navigation -->
    <nav class="top-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="/" class="navbar-brand me-4">
                        <i class="fas fa-graduation-cap me-2"></i>SmartPrep
                    </a>
                    <ul class="navbar-nav d-flex flex-row mb-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.customize-website') }}">
                                <i class="fas fa-palette me-2"></i>Customize Website
                            </a>
                        </li>
                        @if($activeWebsites->count() > 0)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="websitesDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-globe me-2"></i>My Websites
                            </a>
                            <ul class="dropdown-menu">
                                @foreach($activeWebsites as $website)
                                <li><a class="dropdown-item" href="/t/{{ $website->slug }}" target="_blank">{{ $website->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @endif
                    </ul>
                </div>
                
                <ul class="navbar-nav d-flex flex-row mb-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>Home</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container pb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 stats-grid">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stats-number">{{ $activeWebsites->count() }}</div>
                    <div class="stats-label">Active Websites</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(6, 182, 212, 0.1); color: var(--info-color);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stats-number">{{ $websiteRequests->count() }}</div>
                    <div class="stats-label">Total Requests</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number">{{ $websiteRequests->where('status', 'pending')->count() }}</div>
                    <div class="stats-label">Pending</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">{{ $websiteRequests->where('status', 'completed')->count() }}</div>
                    <div class="stats-label">Completed</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Active Websites -->
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-globe me-2"></i>My Active Websites</h5>
                    </div>
                    <div class="card-body-custom">
                        @if($activeWebsites->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>Website Details</th>
                                            <th>URL</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeWebsites as $website)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="fw-bold text-gray-900">{{ $website->name }}</div>
                                                    <small class="text-muted">Created {{ $website->created_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="bg-gray-100 px-2 py-1 rounded">/t/{{ $website->slug }}</code>
                                            </td>
                                            <td>
                                                <span class="badge-status badge-success">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Active
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="/t/{{ $website->slug }}" class="btn-outline-custom btn-sm-custom" target="_blank" title="Visit Website">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <a href="/t/{{ $website->slug }}/admin/dashboard" class="btn-primary-custom btn-sm-custom" target="_blank" title="Manage Website">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-globe"></i>
                                <h6>No websites yet</h6>
                                <p class="mb-0">Request your first website to get started with SmartPrep CMS</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Website Requests -->
        @if($websiteRequests->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-history me-2"></i>Request History</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Business Details</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date Requested</th>
                                        <th>Admin Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($websiteRequests as $request)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-bold text-gray-900">{{ $request->business_name }}</div>
                                                <small class="text-muted">{{ $request->contact_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-info">{{ $request->business_type }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($request->status) {
                                                    'pending' => 'badge-warning',
                                                    'approved' => 'badge-success',
                                                    'completed' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    default => 'badge-info'
                                                };
                                            @endphp
                                            <span class="badge-status {{ $statusClass }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-medium">{{ $request->created_at->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ $request->created_at->format('g:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($request->admin_notes)
                                                <div class="text-muted" style="max-width: 200px;">
                                                    {{ Str::limit($request->admin_notes, 60) }}
                                                </div>
                                            @else
                                                <small class="text-muted fst-italic">No notes available</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>

