<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Edit Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            /* Modern Color Scheme */
            --primary-color: #4361ee;
            --primary-color-dark: #3a56d4;
            --primary-color-light: #4895ef;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            
            /* Bootstrap Color Variables */
            --bs-primary-rgb: 67, 97, 238;
            --bs-secondary-rgb: 63, 55, 201;
            --bs-success-rgb: 46, 204, 113;
            --bs-info-rgb: 52, 152, 219;
            --bs-warning-rgb: 243, 156, 18;
            --bs-danger-rgb: 231, 76, 60;
            
            /* Background Colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            
            /* Text Colors */
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-tertiary: #64748b;
            --text-muted: #94a3b8;
            
            /* Grays */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e2e8f0;
            --gray-300: #d1d5db;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            /* Borders & Shadows */
            --border-color: #e2e8f0;
            --border-radius-sm: 0.375rem;
            --border-radius: 0.5rem;
            --border-radius-md: 0.75rem;
            --border-radius-lg: 1rem;
            --border-radius-xl: 1.5rem;
            
            /* Box Shadows */
            --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-inner: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
            
            /* Transitions */
            --transition-fast: all 0.15s ease;
            --transition: all 0.25s ease;
            --transition-slow: all 0.35s ease;
            
            /* Spacing */
            --spacing-1: 0.25rem;
            --spacing-2: 0.5rem;
            --spacing-3: 0.75rem;
            --spacing-4: 1rem;
            --spacing-5: 1.25rem;
            --spacing-6: 1.5rem;
            --spacing-8: 2rem;
            --spacing-10: 2.5rem;
            --spacing-12: 3rem;
            --spacing-16: 4rem;
            
            /* Layout */
            --container-padding: 1.5rem;
            --header-height: 70px;
            --sidebar-width: 260px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: var(--bg-secondary);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        /* Top Navigation */
        .top-navbar {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
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
            color: var(--primary-color-dark);
        }
        
        .navbar-nav .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--bg-tertiary);
            color: var(--primary-color);
        }
        
        .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
            border-radius: var(--border-radius-md);
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background: var(--bg-tertiary);
            color: var(--primary-color);
        }
        
        /* Main Content */
        .main-content {
            padding: var(--spacing-6);
            min-height: calc(100vh - var(--header-height));
        }
        
        /* Cards */
        .card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: var(--spacing-6);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .card-body {
            padding: var(--spacing-6);
        }
        
        /* Form Styles */
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--spacing-2);
        }
        
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-3) var(--spacing-4);
            transition: var(--transition);
            background: var(--bg-primary);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .form-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: var(--spacing-1);
        }
        
        /* Buttons */
        .btn {
            font-weight: 500;
            border-radius: var(--border-radius);
            padding: var(--spacing-3) var(--spacing-4);
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-2);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-color-dark);
            border-color: var(--primary-color-dark);
            color: white;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        /* Text Colors */
        .text-danger {
            color: var(--danger-color) !important;
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: var(--spacing-4);
            }
            
            .card-header,
            .card-body {
                padding: var(--spacing-4);
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
                    <i class="fas fa-graduation-cap me-2"></i>
                    SmartPrep Admin
                </a>
                <ul class="navbar-nav flex-row">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.clients.index') }}" class="nav-link active">
                            <i class="fas fa-users me-1"></i>
                            Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link">
                            <i class="fas fa-cog me-1"></i>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Auth::guard('smartprep')->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2 text-primary"></i>
                                Edit Client: {{ $client->name }}
                            </h5>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Clients
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.clients.update', $client) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label for="name" class="form-label">Client Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $client->name) }}" required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $client->slug) }}" required>
                                    <div class="form-text">Used in URL paths and database naming. Use only lowercase letters, numbers, and hyphens.</div>
                                    @error('slug')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="domain" class="form-label">Domain</label>
                                    <input type="text" class="form-control" id="domain" name="domain" value="{{ old('domain', $client->domain) }}">
                                    <div class="form-text">The domain where this client will be hosted (optional)</div>
                                    @error('domain')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" value="{{ old('db_name', $client->db_name) }}">
                                    <div class="form-text">The database name for this client</div>
                                    @error('db_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Update Client
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const nameValue = this.value.trim();
            const slugInput = document.getElementById('slug');
            
            // Only update slug if it hasn't been manually edited
            if (!slugInput.dataset.manuallyEdited) {
                slugInput.value = nameValue
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')  // Remove special chars except spaces and hyphens
                    .replace(/\s+/g, '-')      // Replace spaces with hyphens
                    .replace(/-+/g, '-')       // Replace multiple hyphens with single hyphen
                    .trim();
            }
        });
        
        // Track if slug has been manually edited
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.manuallyEdited = 'true';
        });
    </script>
</body>
</html>
