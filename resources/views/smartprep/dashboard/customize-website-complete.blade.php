@php
    // Ensure auth context variables exist before head is rendered
    $user = Auth::guard('smartprep_admin')->user() ?: Auth::guard('smartprep')->user() ?: Auth::user();
    $isLoggedIn = Auth::guard('smartprep_admin')->check() || Auth::guard('smartprep')->check() || Auth::check();
    $userRole = 'guest';
    if ($isLoggedIn && $user) {
        if (Auth::guard('smartprep_admin')->check()) {
            $userRole = 'admin';
        } elseif (Auth::guard('smartprep')->check()) {
            $userRole = $user->role ?? 'user';
        } else {
            $userRole = $user->role ?? 'user';
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SmartPrep - Customize Your Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App context for JS -->
    <meta name="x-my-id" content="{{ $isLoggedIn && isset($user) ? $user->id : '' }}">
    <meta name="x-my-name" content="{{ $isLoggedIn && isset($user) ? ($user->name ?? 'User') : 'Guest' }}">
    <meta name="x-is-authenticated" content="{{ $isLoggedIn && isset($user) ? '1' : '0' }}">
    <meta name="x-user-role" content="{{ $userRole ?? 'guest' }}">
    <meta name="x-selected-website-id" content="{{ $selectedWebsite->id ?? '' }}">
    
    <!-- Include the exact same styles as admin settings -->
    @include('smartprep.dashboard.partials.customize-styles')
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>SmartPrep Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('smartprep.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('smartprep.dashboard.customize') }}">
                            <i class="fas fa-paint-brush me-2"></i>Customize Website
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Website Selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-2"></i>
                            {{ $selectedWebsite ? $selectedWebsite->name : 'Select Website' }}
                        </a>
                        <ul class="dropdown-menu">
                            @forelse($activeWebsites as $website)
                                <li>
                                    <a class="dropdown-item {{ request('website') == $website->id ? 'active' : '' }}" 
                                       href="{{ route('smartprep.dashboard.customize', ['website' => $website->id]) }}">
                                        <i class="fas fa-globe me-2"></i>{{ $website->name }}
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted">No websites found</span></li>
                            @endforelse
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="openCreateWebsite()">
                                    <i class="fas fa-plus me-2"></i>Create New Website
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>{{ $user->name ?? 'User' }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('smartprep.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('smartprep.logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('smartprep-logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                                <form id="smartprep-logout-form" action="{{ route('smartprep.logout') }}" method="POST" class="d-none">
                                    @csrf
                                    @if($selectedWebsite)
                                        <input type="hidden" name="tenant" value="{{ $selectedWebsite->slug }}">
                                    @endif
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(!$selectedWebsite)
        <!-- No website selected - show selection prompt -->
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-globe fa-3x text-primary mb-3"></i>
                            <h3>Select a Website to Customize</h3>
                            <p class="text-muted mb-4">Choose an existing website or create a new one to start customizing your settings.</p>
                            
                            @if($activeWebsites->count() > 0)
                                <div class="row">
                                    @foreach($activeWebsites as $website)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $website->name }}</h5>
                                                                    {{-- Website status badge and domain --}}
                                                                    <div class="mb-2">
                                                                        @include('smartprep.dashboard.partials.website-status-badge', ['website' => $website, 'showDomain' => true])
                                                                    </div>
                                                                    <a href="{{ route('smartprep.dashboard.customize', ['website' => $website->id]) }}" 
                                                                       class="btn btn-primary">
                                                                        <i class="fas fa-edit me-2"></i>Customize
                                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="mt-4">
                                <button class="btn btn-success btn-lg" onclick="openCreateWebsite()">
                                    <i class="fas fa-plus me-2"></i>Create New Website
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Website selected - show customization interface -->
        @include('smartprep.dashboard.partials.customize-interface')
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Create new website function
        function openCreateWebsite(){
            const name = prompt('Enter new website name');
            if(!name) return;
            const form = document.createElement('form');
            form.method='POST';
            form.action="{{ route('smartprep.dashboard.websites.store') }}";
            form.innerHTML = `@csrf<input type="hidden" name="name" value="${name.replace(/"/g,'&quot;')}">`;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    @if($selectedWebsite)
        @include('smartprep.dashboard.partials.customize-scripts')
    @endif
</body>
</html>
