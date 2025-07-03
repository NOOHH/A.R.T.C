<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ascendo Review')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <style>
        {!! \App\Helpers\SettingsHelper::getNavbarStyles() !!}
        {!! \App\Helpers\SettingsHelper::getFooterStyles() !!}
        {!! \App\Helpers\SettingsHelper::getProgramCardStyles() !!}
    </style>
    @stack('styles') {{-- ✅ to load page-specific styles --}}
</head>

<body class="body-background d-flex flex-column min-vh-100 @if(request()->routeIs('enrollment.*')) enrollment-page @endif">
    @php
        $settings = \App\Helpers\SettingsHelper::getSettings();
        $navbar = $settings['navbar'] ?? [];
        $footer = $settings['footer'] ?? [];
    @endphp
    
    {{-- Bootstrap Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ \App\Helpers\SettingsHelper::getLogoUrl() }}" 
                     alt="Logo" class="logo me-2" style="height: 40px;"
                     onerror="this.src='{{ asset('images/ARTC_Logo.png') }}'">
                <strong>{{ $navbar['brand_name'] ?? 'Ascendo Review and Training Center' }}</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-book"></i> Review Programs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-people"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-envelope"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        @if(session('user_id'))
                            {{-- User is logged in - show user name with dropdown --}}
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ explode(' ', session('user_name'))[0] ?? 'User' }}
                            </a>
                            <ul class="dropdown-menu">
                                @if(session('user_role') === 'student')
                                    <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('student.settings') }}">
                                        <i class="bi bi-gear"></i> Settings
                                    </a></li>
                                @elseif(session('user_role') === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('student.logout') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        @else
                            {{-- User is not logged in - show Account dropdown --}}
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-plus"></i> Account
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('/login') }}">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a></li>
                                <li><a class="dropdown-item" href="{{ url('/enrollment') }}">
                                    <i class="bi bi-person-plus-fill"></i> Sign Up
                                </a></li>
                            </ul>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    </nav>
    
    <main class="main-content flex-grow-1">
        @yield('content')
    </main>
    
    <footer class="footer mt-auto">
        <div class="footer-content">
            <div class="container">
                <div class="row">
                    <!-- Contact Info Section - Mobile First -->
                    <div class="col-12 col-md-4 footer-section">
                        <div class="footer-logo-section">
                            <img src="{{ \App\Helpers\SettingsHelper::getLogoUrl() }}" 
                                 alt="Logo" class="footer-logo"
                                 onerror="this.src='{{ asset('images/ARTC_Logo.png') }}'">
                        </div>
                        <div class="contact-info">
                            <h5>Contact Us</h5>
                            <p><i class="bi bi-telephone"></i> 123-456-7890</p>
                            <p><i class="bi bi-envelope"></i> artc@gmail.com</p>
                            <p><i class="bi bi-geo-alt"></i> Ascendo Review and Training Center</p>
                        </div>
                    </div>
                    
                    <!-- Links Section - Mobile First -->
                    <div class="col-12 col-md-4 footer-section">
                        <h5>Links</h5>
                        <ul class="footer-links">
                            <li><a href="{{ url('/') }}">Review Courses</a></li>
                            <li><a href="{{ url('/') }}">About Us</a></li>
                            <li><a href="{{ url('/') }}">Contact Us</a></li>
                        </ul>
                    </div>
                    
                    <!-- Additional Links Section - Mobile First -->
                    <div class="col-12 col-md-4 footer-section">
                        <h5>&nbsp;</h5>
                        <ul class="footer-links">
                            <li><a href="{{ url('/enrollment') }}">Enrollment</a></li>
                            <li><a href="{{ url('/') }}">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Copyright Section -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <div class="copyright">
                            {!! $footer['text'] ?? '© Copyright Ascendo Review and Training Center. All Rights Reserved.' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
    /* Footer Styles - Mobile First */
    .footer {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 3rem 0 1rem 0;
        margin-top: auto;
    }
    
    .footer-content {
        width: 100%;
    }
    
    .footer-section {
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .footer-logo-section {
        margin-bottom: 1.5rem;
    }
    
    .footer-logo {
        height: 60px;
        margin-bottom: 1rem;
    }
    
    .contact-info h5,
    .footer-section h5 {
        color: #4CAF50;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .contact-info p {
        margin: 0.5rem 0;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin: 0.5rem 0;
    }
    
    .footer-links a {
        color: #bdc3c7;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }
    
    .footer-links a:hover {
        color: #4CAF50;
        text-decoration: none;
    }
    
    .copyright {
        border-top: 1px solid #4a5568;
        padding-top: 1rem;
        font-size: 0.8rem;
        color: #bdc3c7;
    }
    
    /* ==== TABLET DEVICES (768px - 991px) ==== */
    @media (min-width: 768px) {
        .footer-section {
            text-align: left;
            margin-bottom: 1rem;
        }
        
        .footer-logo-section {
            text-align: center;
        }
        
        .contact-info p {
            justify-content: flex-start;
        }
        
        .footer {
            padding: 4rem 0 1rem 0;
        }
    }
    
    /* ==== LAPTOP DEVICES (992px - 1199px) ==== */
    @media (min-width: 992px) {
        .footer {
            padding: 5rem 0 1rem 0;
        }
        
        .footer-logo {
            height: 70px;
        }
        
        .contact-info h5,
        .footer-section h5 {
            font-size: 1.2rem;
        }
        
        .contact-info p,
        .footer-links a {
            font-size: 1rem;
        }
    }
    
    /* ==== PC/DESKTOP DEVICES (1200px+) ==== */
    @media (min-width: 1200px) {
        .footer {
            padding: 6rem 0 1rem 0;
        }
    }
    </style>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts') {{-- Ensure page-specific scripts are loaded before </body> --}}
</body>
</html>
