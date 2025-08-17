<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPrep - Multi-Tenant Learning Management Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #059669;
            --accent-color: #0891b2;
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            --gradient-secondary: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --surface: #f8fafc;
            --surface-dark: #1e293b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: var(--text-dark); overflow-x: hidden; }

        /* Navigation */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            box-shadow: 0 4px 30px rgba(37, 99, 235, 0.12);
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(37, 99, 235, 0.08);
            transition: all 0.4s ease;
        }

        .navbar-scrolled {
            padding: 0.8rem 0;
            background: rgba(255, 255, 255, 0.99);
            box-shadow: 0 6px 40px rgba(37, 99, 235, 0.18);
        }

        .navbar-brand {
            font-weight: 900;
            font-size: 2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .navbar-brand:hover {
            transform: translateY(-1px);
            text-decoration: none;
            color: inherit;
        }

        .navbar-brand i {
            background: var(--gradient-primary);
            color: white !important;
            -webkit-text-fill-color: white !important;
            padding: 12px;
            border-radius: 12px;
            margin-right: 12px;
            font-size: 1.5rem;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }

        .navbar-brand:hover i {
            transform: rotate(-5deg) scale(1.1);
            box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
        }

        .navbar-nav {
            align-items: center;
        }

        .navbar-nav .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin: 0 4px;
            position: relative;
            overflow: hidden;
        }

        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .navbar-nav .nav-link:hover::before {
            left: 100%;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary-color) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }

        .navbar-nav .btn-hero {
            background: var(--gradient-primary);
            color: white !important;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            margin-left: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .navbar-nav .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
            color: white !important;
            text-decoration: none;
        }

        .navbar-nav .btn-hero i {
            margin-right: 8px;
        }

        .navbar-toggler {
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%232563eb' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.15"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="50%" cy="50%" r="50%" fill="url(%23a)"/></svg>') center/cover;
            opacity: 0.3;
        }

        .hero-content { position: relative; z-index: 2; }
        .hero-title { 
            font-size: 4.5rem; 
            font-weight: 900; 
            color: white; 
            margin-bottom: 1.5rem; 
            line-height: 1.1;
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-subtitle { 
            font-size: 1.5rem; 
            color: rgba(255, 255, 255, 0.95); 
            margin-bottom: 3rem; 
            font-weight: 400;
            line-height: 1.6;
        }

        .btn-hero {
            background: white;
            color: var(--primary-color);
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            color: var(--primary-color);
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.8);
            color: white;
            transform: translateY(-2px);
        }

        /* Features Section */
        .features-section { 
            padding: 120px 0; 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%); 
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 50px 35px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before { 
            transform: scaleX(1); 
        }

        .feature-card:hover { 
            transform: translateY(-12px); 
            box-shadow: 0 25px 60px rgba(37, 99, 235, 0.15); 
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 2.2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
        }

        .feature-title { 
            font-size: 1.6rem; 
            font-weight: 700; 
            margin-bottom: 20px; 
            color: var(--text-dark); 
        }
        .feature-description { 
            color: var(--text-light); 
            font-size: 1.05rem; 
            line-height: 1.7; 
        }

        /* Stats Section */
        .stats-section { 
            padding: 100px 0; 
            background: var(--gradient-secondary); 
            color: white; 
        }
        .stat-item { 
            text-align: center; 
            padding: 30px 20px; 
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }
        .stat-number { 
            font-size: 3.5rem; 
            font-weight: 900; 
            margin-bottom: 15px; 
            display: block; 
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label { 
            font-size: 1.2rem; 
            opacity: 0.95; 
            font-weight: 600; 
        }

        /* CTA Section */
        .cta-section { 
            padding: 120px 0; 
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); 
        }
        .cta-card { 
            background: var(--gradient-primary); 
            border-radius: 24px; 
            padding: 80px 50px; 
            color: white; 
            text-align: center; 
            position: relative;
            overflow: hidden;
        }
        .cta-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }
        .cta-card h2 {
            position: relative;
            z-index: 2;
        }
        .cta-card p {
            position: relative;
            z-index: 2;
        }
        .cta-card .btn {
            position: relative;
            z-index: 2;
        }

        /* Footer */
        .footer { 
            background: var(--surface-dark); 
            color: white; 
            padding: 80px 0 40px; 
        }
        .footer-title { 
            font-size: 2rem; 
            font-weight: 800; 
            margin-bottom: 25px; 
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .footer-text { 
            opacity: 0.8; 
            margin-bottom: 35px; 
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .social-links a {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            text-align: center;
            line-height: 50px;
            margin-right: 15px;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
        }

        /* Floating elements */
        .floating-shape { 
            position: absolute; 
            border-radius: 50%; 
            background: rgba(255, 255, 255, 0.15); 
            animation: float 8s ease-in-out infinite; 
        }
        .floating-shape:nth-child(1) { 
            width: 120px; 
            height: 120px; 
            top: 15%; 
            left: 8%; 
            animation-delay: 0s; 
        }
        .floating-shape:nth-child(2) { 
            width: 180px; 
            height: 180px; 
            top: 55%; 
            right: 8%; 
            animation-delay: 2s; 
        }
        .floating-shape:nth-child(3) { 
            width: 100px; 
            height: 100px; 
            bottom: 15%; 
            left: 15%; 
            animation-delay: 4s; 
        }

        @keyframes float { 
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
            } 
            50% { 
                transform: translateY(-30px) rotate(180deg); 
            } 
        }

        /* New animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Section headers */
        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-header h2 {
            font-size: 3.5rem;
            font-weight: 900;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .section-header p {
            font-size: 1.3rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero-subtitle { font-size: 1.2rem; }
            .feature-card { margin-bottom: 30px; }
            .auth-left, .auth-right {
                padding: 40px 30px;
            }
            .navbar-brand {
                font-size: 1.8rem;
            }
            .section-header h2 {
                font-size: 2.5rem;
            }
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(37, 99, 235, 0.15);
            border-radius: 12px;
            padding: 10px;
            margin-top: 10px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-graduation-cap"></i>SmartPrep
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    @auth('smartprep')
                        <li class="nav-item">
                            <a class="btn btn-hero" href="{{ route('smartprep.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::guard('smartprep')->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="{{ route('smartprep.logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('smartprep.login') }}">Login</a></li>
                        <li class="nav-item">
                            <a class="btn btn-hero" href="{{ route('smartprep.register') }}">
                                <i class="fas fa-rocket"></i>Get Started
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-in-up">
                        <h1 class="hero-title">Transform Education with SmartPrep</h1>
                        <p class="hero-subtitle">Empower your educational institution with our cutting-edge multi-tenant learning management platform. Build professional training websites that scale with your success.</p>
                        <div>
                            @auth('smartprep')
                                <a href="{{ route('smartprep.dashboard') }}" class="btn btn-hero me-3">
                                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('smartprep.register') }}" class="btn btn-hero me-3">
                                    <i class="fas fa-rocket me-2"></i>Create Account
                                </a>
                                <a href="{{ route('smartprep.login') }}" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <div style="position: relative; display: inline-block;">
                            <div style="width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(20px); border: 2px solid rgba(255,255,255,0.2);">
                                <i class="fas fa-graduation-cap" style="font-size: 8rem; color: rgba(255,255,255,0.9);"></i>
                            </div>
                            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-rocket" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <div style="position: absolute; bottom: -10px; left: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: white;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Powerful Features for Modern Education</h2>
                <p>Discover the comprehensive tools and capabilities that make SmartPrep the preferred choice for educational institutions worldwide.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-sitemap"></i></div>
                        <h3 class="feature-title">Multi-Tenancy</h3>
                        <p class="feature-description">Create unlimited client websites, each with isolated databases and custom configurations.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-palette"></i></div>
                        <h3 class="feature-title">Full Customization</h3>
                        <p class="feature-description">Complete CMS control over every aspect - colors, logos, content, user roles, and entity naming.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-users-cog"></i></div>
                        <h3 class="feature-title">Role Management</h3>
                        <p class="feature-description">Hierarchical user roles with granular permissions for admins, directors, professors, and students.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
                        <h3 class="feature-title">LMS Features</h3>
                        <p class="feature-description">Complete learning management with courses, modules, quizzes, assignments, and progress tracking.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-credit-card"></i></div>
                        <h3 class="feature-title">Payment Integration</h3>
                        <p class="feature-description">Built-in payment processing with multiple payment methods and automated enrollment workflows.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h3 class="feature-title">Analytics & Reports</h3>
                        <p class="feature-description">Comprehensive analytics with student progress, enrollment statistics, and revenue tracking.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About Section -->
    <section class="section-professional" id="about" style="background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); padding: 120px 0;">
        <div class="container">
            <div class="section-header">
                <h2>About SmartPrep</h2>
                <p>Transforming education through innovative technology and comprehensive learning management solutions</p>
            </div>
            
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h3 class="h2 fw-bold mb-4" style="color: var(--text-dark);">Our Mission</h3>
                        <p class="lead mb-4" style="color: var(--text-light); line-height: 1.8;">
                            SmartPrep empowers educational institutions with cutting-edge multi-tenant LMS technology, 
                            enabling them to create, manage, and scale professional learning platforms that deliver 
                            exceptional educational experiences.
                        </p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div style="width: 50px; height: 50px; background: var(--gradient-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-award text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fw-bold mb-1">Award-Winning Platform</h6>
                                        <p class="mb-0 text-muted">Recognized excellence in EdTech</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div style="width: 50px; height: 50px; background: var(--gradient-secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-users text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fw-bold mb-1">10,000+ Users</h6>
                                        <p class="mb-0 text-muted">Trusted by educators worldwide</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image text-center">
                        <div style="position: relative; display: inline-block;">
                            <div style="width: 400px; height: 300px; background: var(--gradient-primary); border-radius: 20px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 1000 1000&quot;><defs><radialGradient id=&quot;a&quot; cx=&quot;50%&quot; cy=&quot;50%&quot;><stop offset=&quot;0%&quot; stop-color=&quot;%23ffffff&quot; stop-opacity=&quot;0.1&quot;/><stop offset=&quot;100%&quot; stop-color=&quot;%23ffffff&quot; stop-opacity=&quot;0&quot;/></radialGradient></defs><circle cx=&quot;50%&quot; cy=&quot;50%&quot; r=&quot;50%&quot; fill=&quot;url(%23a)&quot;/></svg>') center/cover; opacity: 0.3;"></div>
                                <i class="fas fa-chalkboard-teacher" style="font-size: 6rem; color: rgba(255,255,255,0.9); position: relative; z-index: 2;"></i>
                            </div>
                            <div style="position: absolute; -top: 20px; -right: 20px; top: -10px; right: -10px; width: 100px; height: 100px; background: rgba(255,255,255,0.95); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                                <div style="text-align: center;">
                                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color);">99%</div>
                                    <div style="font-size: 0.8rem; color: var(--text-light);">Satisfaction</div>
                                </div>
                            </div>
                            <div style="position: absolute; bottom: -10px; left: -10px; width: 80px; height: 80px; background: rgba(5, 150, 105, 0.95); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);">
                                <i class="fas fa-rocket" style="font-size: 1.5rem; color: white;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="text-center p-4" style="background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(37, 99, 235, 0.08); height: 100%;">
                        <div style="width: 80px; height: 80px; background: var(--gradient-primary); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                            <i class="fas fa-lightbulb" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Innovation</h5>
                        <p class="text-muted">Cutting-edge technology meets educational excellence to create transformative learning experiences.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="text-center p-4" style="background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(37, 99, 235, 0.08); height: 100%;">
                        <div style="width: 80px; height: 80px; background: var(--gradient-secondary); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                            <i class="fas fa-handshake" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Partnership</h5>
                        <p class="text-muted">We work alongside educators to understand their unique needs and deliver tailored solutions.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="text-center p-4" style="background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(37, 99, 235, 0.08); height: 100%;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                            <i class="fas fa-trophy" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Excellence</h5>
                        <p class="text-muted">Committed to delivering the highest quality products and services that exceed expectations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- CTA Section -->
    <section class="cta-section" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="cta-card">
                        <h2 class="display-4 fw-bold mb-4">Ready to Revolutionize Your Educational Platform?</h2>
                        <p class="lead mb-5">Join thousands of educators and training centers who trust SmartPrep to deliver exceptional learning experiences.</p>
                        @auth('smartprep')
                            <a href="{{ route('smartprep.dashboard') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>Access Dashboard
                            </a>
                        @else
                            <a href="{{ route('smartprep.register') }}" class="btn btn-light btn-lg me-3">
                                <i class="fas fa-rocket me-2"></i>Create Your Account
                            </a>
                            <a href="#contact" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-phone me-2"></i>Contact Sales
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h3 class="footer-title">SmartPrep Platform</h3>
                    <p class="footer-text">Empowering educational institutions with cutting-edge multi-tenant LMS technology.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-github"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="text-light text-decoration-none">Features</a></li>
                        <li class="mb-2"><a href="#about" class="text-light text-decoration-none">About</a></li>
                        @auth('smartprep')
                            <li class="mb-2"><a href="{{ route('smartprep.dashboard') }}" class="text-light text-decoration-none">Dashboard</a></li>
                        @else
                            <li class="mb-2"><a href="{{ route('smartprep.login') }}" class="text-light text-decoration-none">Login</a></li>
                            <li class="mb-2"><a href="{{ route('smartprep.register') }}" class="text-light text-decoration-none">Register</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-4">Technology</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><span class="text-light">Laravel Framework</span></li>
                        <li class="mb-2"><span class="text-light">Multi-Tenant Architecture</span></li>
                        <li class="mb-2"><span class="text-light">Bootstrap UI</span></li>
                        <li class="mb-2"><span class="text-light">MySQL Database</span></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 opacity-75">&copy; 2024 SmartPrep Platform. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 opacity-75">Built with ❤️ using Laravel & Bootstrap</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling and navbar effects
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Enhanced navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            const scrolled = window.scrollY;
            
            if (scrolled > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }

            // Subtle parallax effect for floating shapes
            document.querySelectorAll('.floating-shape').forEach((shape, index) => {
                const speed = 0.5 + (index * 0.2);
                shape.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Observe stat items
        document.querySelectorAll('.stat-item').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'all 0.6s ease';
            observer.observe(item);
        });
    </script>
</body>
</html>

