<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        }
        
        /* Top Navigation */
        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand:hover {
            color: var(--secondary-color);
        }
        
        .navbar-nav .nav-link {
            color: var(--gray-600);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: 1px solid var(--gray-200);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary-color);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link {
            color: var(--gray-600) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
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
            color: white !important;
        }
        
        /* Dashboard Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 0 4rem;
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
            margin-top: -2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1;
        }
        
        .stats-label {
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
        
        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            overflow: hidden;
            transition: all 0.3s ease;
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
            padding: 0;
        }
        
        /* List Items */
        .list-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            transition: background 0.2s ease;
        }
        
        .list-item:last-child {
            border-bottom: none;
        }
        
        .list-item:hover {
            background: var(--gray-50);
        }
        
        .list-item-title {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        
        .list-item-meta {
            color: var(--gray-500);
            font-size: 0.875rem;
        }
        
        /* Badges */
        .badge-status {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .badge-pending {
            background: #fef5e7;
            color: #92400e;
        }
        
        .badge-approved {
            background: #f0fff4;
            color: #276749;
        }
        
        .badge-rejected {
            background: #fed7d7;
            color: #c53030;
        }
        
        /* Buttons */
        .btn-primary-custom {
            background: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: white;
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
            color: white;
        }
        
        .btn-outline-custom {
            background: white;
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
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--gray-500);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 2rem 0 3rem;
            }
            
            .stats-grid {
                margin-top: -1.5rem;
            }
            
            .stats-card {
                padding: 1.5rem;
            }
            
            .stats-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>SmartPrep
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('smartprep.admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.website-requests') }}">
                            <i class="fas fa-clock me-2"></i>Requests
                            @if($stats['pending_requests'] > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_requests'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.clients') }}">
                            <i class="fas fa-users me-2"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.admin.settings') }}">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i>View Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('smartprep.logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
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

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-2">
                        Welcome back, Admin
                    </h1>
                    <p class="mb-0 opacity-90 fs-5">Manage your SmartPrep platform and monitor activity</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="d-flex flex-column align-items-lg-end">
                        <div class="text-white-50 mb-2">{{ now()->format('l, F j, Y') }}</div>
                        <div class="fs-4 fw-bold">{{ now()->format('g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <div class="stats-icon" style="background: rgba(56, 161, 105, 0.1); color: var(--success-color);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">{{ $stats['total_users'] }}</div>
                    <div class="stats-label">Total Users</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(49, 130, 206, 0.1); color: var(--accent-color);">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stats-number">{{ $stats['active_websites'] }}</div>
                    <div class="stats-label">Active Websites</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(214, 158, 46, 0.1); color: var(--warning-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number">{{ $stats['pending_requests'] }}</div>
                    <div class="stats-label">Pending Requests</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: rgba(26, 54, 93, 0.1); color: var(--primary-color);">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stats-number">{{ $stats['total_clients'] }}</div>
                    <div class="stats-label">Total Clients</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Website Requests -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-clock me-2"></i>Recent Website Requests</h5>
                    </div>
                    <div class="card-body-custom">
                        @if($recentRequests->count() > 0)
                            @foreach($recentRequests->take(5) as $request)
                            <div class="list-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="list-item-title">{{ $request->business_name }}</div>
                                        <div class="list-item-meta">{{ $request->user->name }} • {{ $request->created_at->diffForHumans() }}</div>
                                    </div>
                                    <span class="badge-status badge-{{ $request->status_color }}">{{ $request->status_text }}</span>
                                </div>
                            </div>
                            @endforeach
                            <div class="p-3 bg-light">
                                <a href="{{ route('smartprep.admin.website-requests') }}" class="btn-outline-custom btn-sm-custom w-100">
                                    <i class="fas fa-eye me-2"></i>View All Requests
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h6>No requests yet</h6>
                                <p class="small mb-0">New website requests will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-users me-2"></i>Recent Clients</h5>
                    </div>
                    <div class="card-body-custom">
                        @if($recentClients->count() > 0)
                            @foreach($recentClients->take(5) as $client)
                            <div class="list-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="list-item-title">{{ $client->name }}</div>
                                        <div class="list-item-meta">
                                            @if($client->user)
                                                {{ $client->user->name }} • 
                                            @endif
                                            {{ $client->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <a href="/t/{{ $client->slug }}" class="btn-outline-custom btn-sm-custom" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            <div class="p-3 bg-light">
                                <a href="{{ route('smartprep.admin.clients') }}" class="btn-outline-custom btn-sm-custom w-100">
                                    <i class="fas fa-users me-2"></i>Manage All Clients
                                </a>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h6>No clients yet</h6>
                                <p class="small mb-0">Client websites will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

