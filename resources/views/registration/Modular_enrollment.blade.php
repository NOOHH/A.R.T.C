@extends('layouts.navbar')

@section('title', 'Student Registration')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Modular_Enrollment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
  .navbar > .container-fluid {
    max-width: none !important;
    width: 100%    !important;
    margin: 0      !important;
    padding: 0 15px !important; /* keep your horizontal padding */
    background: transparent !important;
  }
    /* CLEAN RESET - Remove all layered containers */
    body {
        background: #f8f9fa !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .main-content,
    .content-wrapper,
    #content,
    .container-fluid {
        background: #f8f9fa !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
    }
    
    /* SINGLE CENTERED CONTAINER - No multiple layers */
    .registration-container {
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 50px auto !important;
        max-width: 1200px !important;
        width: 90% !important;
        min-height: 600px !important;
        padding: 40px !important;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    /* CENTERED FORM CONTENT */
    .registration-form {
        width: 100% !important;
        max-width: 1000px !important;
        margin: 0 auto !important;
        padding: 0 !important;
    }
    
    /* CENTERED STEPS */
    .step {
        display: none !important;
        opacity: 0;
        transform: translateX(50px);
        transition: all 0.5s ease-in-out;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        min-height: 500px !important;
        width: 100% !important;
        padding: 20px !important;
    }
    
    .step.active {
        display: flex !important;
        opacity: 1;
        transform: translateX(0);
        animation: slideIn 0.5s ease-in-out;
    }
    
    /* Get enrollment styles from helper */
    {!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
    {!! App\Helpers\SettingsHelper::getButtonStyles() !!}

    /* STEP TRANSITIONS */
    .step.slide-out-left {
        transform: translateX(-50px);
        opacity: 0;
    }
    .step.slide-out-right {
        transform: translateX(50px);
        opacity: 0;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(50px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* PACKAGE CAROUSEL - CENTERED */
    .packages-carousel-container {
    position: relative;
    margin: 0 auto;
    max-width: calc(200px * 3 + 2rem * 2); /* for 3 cards; use 2*300+1*2rem for 2 cards, etc */
    overflow: hidden;    /* clip the scrolling track here */
    }
    
    .packages-carousel {
        cursor: grab;
        user-select: none;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
        scroll-behavior: smooth;
        padding: 1rem 0;
        display: flex;
        gap: 2rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
    }
    
    .packages-carousel::-webkit-scrollbar {
        display: none;
    }
    
    .packages-carousel.grabbing {
        cursor: grabbing;
    }
    
    .package-card-wrapper {
        scroll-snap-align: start;
        flex-shrink: 0;
        min-width: 280px !important;
        max-width: 300px !important;
    }
    
    .package-card {
        width: 100%;
        height: 400px;
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid transparent !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .package-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .package-card.selected {
        border-color: #1c2951 !important;
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(28, 41, 81, 0.3);
    }
    
    .package-image-header {
        height: 60%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .package-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
    }
    
    .package-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #1c2951;
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    }
    
    .package-content {
        height: 40%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #1a1a1a;
        padding: 20px;
    }
    
    .package-title {
        color: #ffffff;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        text-align: center;
    }
    
    .package-description {
        color: #cccccc;
        font-size: 0.9rem;
        margin: 0 0 12px 0;
        text-align: center;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 4em;
    }
    
    .package-price {
        color: #1c2951;
        font-size: 1.4rem;
        font-weight: 800;
        text-align: center;
        margin: 0;
        background: linear-gradient(90deg, #a259c6, #6a82fb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* CAROUSEL NAVIGATION */
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10;
    }
    
    .carousel-nav:hover {
        background: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    .prev-btn {
        left: 1rem;
    }
    
    .next-btn {
        right: 1rem;
    }
    
    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .registration-container {
            margin: 20px auto !important;
            width: 95% !important;
            padding: 20px !important;
        }
        
        .packages-carousel-container {
            padding: 0 2rem !important;
        }
        
        .package-card-wrapper {
            min-width: 280px;
            max-width: 300px;
        }
        
        .carousel-nav {
            width: 40px;
            height: 40px;
        }
    }
    
    @media (max-width: 480px) {
        .packages-carousel-container {
            padding: 0 2rem !important;
        }
        
        .carousel-nav {
            display: none;
        }
        
        .package-card-wrapper {
            min-width: 260px;
        }
    }
        
    .package-card-wrapper {
        scroll-snap-align: start;
        flex-shrink: 0;
        min-width: 320px;
        max-width: 350px;
    }
    
    .package-card {
        transition: all 0.3s ease;
        border: 3px solid transparent !important;
        border-radius: 20px !important;
        overflow: hidden;
        cursor: pointer;
        height: 400px;
        background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%) !important;
        color: white;
    }
    
    .package-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3) !important;
        border-color: #1c2951 !important;
    }
    
    .package-card.selected {
        border-color: #1c2951 !important;
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(28, 41, 81, 0.5) !important;
    }
    
    .package-image-header {
        height: 60%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .package-icon {
        font-size: 4rem;
        color: white;
    }
    
    .package-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #1c2951;
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .package-card .card-body {
        background: #1a1a1a;
        height: 40%;
        padding: 20px;
    }
    
    .package-title {
        color: #fff !important;
        font-size: 1.4rem !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
        text-align: center;
    }
    
    .package-description {
        color: #ccc !important;
        font-size: 0.9rem !important;
        line-height: 1.4;
        text-align: center;
        margin-bottom: 12px !important;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .package-price {
        font-size: 1.4rem !important;
        font-weight: 800 !important;
        text-align: center;
        margin: 0;
        background: linear-gradient(90deg, #a259c6, #6a82fb);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: 2px solid #667eea;
        border-radius: 50%;
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 1.4rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .carousel-nav:hover {
        background: #667eea;
        color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
    }
    
    .prev-btn {
        left: 15px;
    }
    
    .next-btn {
        right: 15px;
    }
    
    @media (max-width: 768px) {
        .packages-carousel-container {
            padding: 0 1rem;
        }
        
        .carousel-nav {
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
        }
        
        .prev-btn {
            left: 5px;
        }
        
        .next-btn {
            right: 5px;
        }
        
        .package-card-wrapper {
            min-width: 280px;
        }
    }
    
    .step { 
        display: none; 
        opacity: 0;
        transform: translateX(50px);
        transition: all 0.5s ease-in-out;
    }
    .step.active { 
        display: block; 
        opacity: 1;
        transform: translateX(0);
        animation: slideIn 0.5s ease-in-out;
    }
    .step.slide-out-left {
        transform: translateX(-50px);
        opacity: 0;
    }
    .step.slide-out-right {
        transform: translateX(50px);
        opacity: 0;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .package-card {
        background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 20px;
        padding: 0;
        width: 320px;
        height: 400px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 3px solid transparent;
        flex-shrink: 0;
    }
    
    .package-carousel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
        position: relative;
        padding-top: 30px;
    }
    
    .package-slider {
        display: flex;
        gap: 20px;
        overflow: hidden;
        width: 780px;
        position: relative;
        margin: 0 auto;
    }
    
    .package-slider-track {
        display: flex;
        gap: 20px;
        transition: transform 0.3s ease;
    }
    
    .carousel-arrow {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .carousel-arrow:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .carousel-arrow:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
.package-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        min-width: 270px;
        min-height: 70px;
       
}
.package-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    border-color: #1c2951 !important;
}
.package-card.selected {
    border-color: #1c2951 !important;
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(28, 41, 81, 0.5) !important;
}

    .package-image {
        width: 100%;
        height: 60%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: white;
    }
    
    .package-content {
        padding: 20px;
        height: 40%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #1a1a1a;
    }
    
.package-title {
    color: #ffffff !important;
    font-size: 1.3rem !important;
    font-weight: 700 !important;
    margin-bottom: 8px !important;
    text-align: center;
}
    
    .package-description {
        color: #cccccc;
        font-size: 0.9rem;
        margin: 0 0 12px 0;
        text-align: center;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 4em;
    }
.package-price {
    font-size: 1.4rem !important;
    font-weight: 800 !important;
    text-align: center;
    margin: 0;
    background: linear-gradient(90deg, #a259c6, #6a82fb);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
    .package-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #1c2951;
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .payment-method {
        background: #f9f9f9;
        border: 2px solid #ddd;
        border-radius: 12px;
        padding: 20px;
        margin: 10px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .payment-method:hover {
        border-color: #1c2951;
        background: #e0f3ff;
    }
    
    .payment-method.selected {
        border-color: #1c2951;
        background: #e0f3ff;
    }
    
    .payment-icon {
        width: 50px;
        height: 50px;
        background: #1c2951;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    
    /* Learning Mode Cards */
    .learning-mode-card {
        background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 15px;
        padding: 30px 20px;
        width: 250px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid transparent;
        text-align: center;
        color: white;
    }
    
    .learning-mode-card:hover {
        transform: translateY(-5px);
        border-color: #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    /* ==== MOBILE & TABLET DEVICES (768px and below) ==== */
    @media (max-width: 768px) {
        .package-slider {
            width: 340px !important;
        }
        
        .package-card {
            width: 300px;
        }
        
        .carousel-arrow {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }
        
        .package-carousel {
            gap: 10px;
        }
    }
    
    /* ==== SMALL MOBILE DEVICES (480px and below) ==== */
    @media (max-width: 480px) {
        .package-carousel {
            flex-direction: column;
            gap: 20px;
        }
        
        .carousel-arrow {
            order: 3;
        }
        
        .package-slider {
            order: 2;
            width: 280px !important;
        }
        
        .package-card {
            width: 260px;
            height: 350px;
        }
    }
    
    /* PAYMENT METHOD STYLES */
    .payment-method {
        display: flex;
        align-items: center;
        padding: 20px;
        margin: 15px 0;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .payment-method:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }
    
    .payment-method.selected {
        border-color: #667eea;
        background: linear-gradient(145deg, #f8f9ff 0%, #e3f2fd 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .payment-icon {
        font-size: 2rem;
        margin-right: 20px;
        width: 60px;
        text-align: center;
    }
    
    .payment-method h4 {
        color: #333;
        font-weight: 600;
        margin: 0 0 5px 0;
    }
    
    .payment-method p {
        color: #666;
        font-size: 14px;
        margin: 0;
    }
    
    .payment-method.selected h4 {
        color: #667eea;
    }
    
    .payment-method.selected p {
        color: #555;
    }
    
    /* FORM INPUT FIXES */
    input[type="password"],
    input[type="email"],
    input[type="text"],
    input[type="date"],
    input[type="tel"],
    select,
    textarea {
        pointer-events: auto !important;
        position: relative !important;
        z-index: 1 !important;
        background: white !important;
        border: 1px solid #ccc !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
    }
    
    input[type="password"]:focus,
    input[type="email"]:focus,
    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="tel"]:focus,
    select:focus,
    textarea:focus {
        outline: none !important;
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }
    
    /* Ensure form elements are not blocked */
    .step input,
    .step select,
    .step textarea {
        pointer-events: auto !important;
        position: relative !important;
        z-index: 10 !important;
    }
</style>
@endpush

@section('content')
<!-- Validation Errors Display -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px auto; max-width: 1200px;">
        <h6><i class="bi bi-exclamation-triangle"></i> Please correct the following errors:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- SINGLE CENTERED CONTAINER - No nested layers -->
<div class="registration-container">
    <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form" id="enrollmentForm" novalidate>
        @csrf
        <input type="hidden" name="enrollment_type" value="modular">
        <input type="hidden" name="program_id" value="" id="hidden_program_id">
        <input type="hidden" name="package_id" value="">
        <input type="hidden" name="learning_mode" value="">
    <input type="hidden" name="plan_id" value="2">
    <input type="hidden" name="registration_mode" value="sync" id="registration_mode">

    {{-- STEP 1: PACKAGE SELECTION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom:30px; font-weight:700; letter-spacing:1px;">
            SELECT YOUR PACKAGE
        </h2>
        
        <!-- Bootstrap Horizontal Scrolling Package Carousel -->
        <div class="packages-carousel-container">
            <div class="packages-carousel" id="packagesCarousel">
                @foreach($packages as $package)
                <div class="package-card-wrapper">
                    <div class="card package-card h-100 shadow-lg" onclick="selectPackage('{{ $package->package_id }}', '{{ $package->package_name }}', '{{ $package->amount }}')" data-package-id="{{ $package->package_id }}" data-package-price="{{ $package->amount }}">
                        <div class="package-image-header">
                            <div class="package-icon">📦</div>
                            @if($loop->first)
                                <div class="package-badge">Popular</div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title package-title">{{ $package->package_name }}</h5>
                            <p class="card-text package-description flex-grow-1" title="{{ $package->description ?? 'Complete package with all features included.' }}">
                                {{ $package->description ?? 'Complete package with all features included.' }}
                            </p>
                            <div class="mt-auto">
                                <div class="package-price">₱{{ number_format($package->amount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Navigation Arrows -->
            @if($packages->count() > 2)
            <button class="carousel-nav prev-btn" onclick="scrollPackages('left')" id="prevPackageBtn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="carousel-nav next-btn" onclick="scrollPackages('right')" id="nextPackageBtn">
                <i class="bi bi-chevron-right"></i>
            </button>
            @endif
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <div id="selectedPackageDisplay" style="display: none; margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%); border-radius: 12px; border: 2px solid #4caf50; max-width: 400px; margin: 0 auto 20px;">
                <strong style="color: #2e7d2e; font-size: 1.1rem;">Selected Package: <span id="selectedPackageName"></span></strong>
                <div style="color: #2e7d2e; font-size: 1.2rem; font-weight: bold; margin-top: 8px;">
                    Price: <span id="selectedPackagePrice"></span>
                </div>
            </div>
            <button type="button" onclick="nextStep()" id="packageNextBtn" disabled
                    class="btn btn-primary btn-lg" style="opacity: 0.5;">
                Next<i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- STEP 2: LEARNING MODE SELECTION --}}
    <div class="step" id="step-2">
        <h2 style="text-align:center; margin-bottom:40px; font-weight:700; letter-spacing:1px;">
            LEARNING MODE SELECTION
        </h2>
        
        <div style="max-width: 800px; margin: 0 auto; padding: 20px; display: flex; flex-direction: column; align-items: center;">
            <h3 style="margin-bottom: 30px; text-align: center; color: #333;">Choose Your Learning Mode</h3>
            
            <div class="learning-mode-container" style="display: flex; gap: 40px; justify-content: center; margin-bottom: 40px; flex-wrap: wrap;">
                <div class="learning-mode-card" onclick="selectLearningMode('synchronous')" data-mode="synchronous">
                    <div class="mode-icon">🕐</div>
                    <h4>Synchronous</h4>
                    <p>Real-time classes with live interaction, scheduled sessions, and immediate feedback.</p>
                    <div class="mode-features">
                        <span>✓ Live virtual classrooms</span>
                        <span>✓ Real-time Q&A sessions</span>
                        <span>✓ Immediate instructor feedback</span>
                        <span>✓ Group discussions</span>
                    </div>
                </div>
                
                <div class="learning-mode-card" onclick="selectLearningMode('asynchronous')" data-mode="asynchronous">
                    <div class="mode-icon">🎯</div>
                    <h4>Asynchronous</h4>
                    <p>Self-paced learning with recorded materials, flexible schedule, and individual progress.</p>
                    <div class="mode-features">
                        <span>✓ Learn at your own pace</span>
                        <span>✓ Recorded video lectures</span>
                        <span>✓ Flexible scheduling</span>
                        <span>✓ 24/7 access to materials</span>
                    </div>
                </div>
            </div>
            
            <div id="selectedLearningModeDisplay" style="display: none; margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%); border-radius: 12px; text-align: center; border: 2px solid #4caf50; max-width: 400px;">
                <strong style="color: #2e7d2e; font-size: 1.1rem;">Selected Learning Mode: <span id="selectedLearningModeName"></span></strong>
            </div>
            
            <input type="hidden" name="learning_mode" id="learning_mode" value="">
            
            <div style="display:flex; gap:20px; justify-content:center; margin-top: 40px;">
                <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </button>
                <button type="button" onclick="nextStep()" id="learningModeNextBtn" disabled
                        class="btn btn-primary btn-lg" style="opacity: 0.5;">
                    Next<i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 3: ACCOUNT REGISTRATION --}}
    <div class="step" id="step-3">
        <h2 style="text-align:center; margin-bottom:30px; font-weight:700; letter-spacing:1px;">
            ACCOUNT REGISTRATION
        </h2>
        <div style="max-width: 500px; margin: 0 auto; display:flex; flex-direction:column; gap:18px; align-items:center;">
            <div style="display:flex; gap:16px; width:100%;">
                <input type="text" name="user_firstname" id="user_firstname" placeholder="First Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;"
                       value="{{ old('user_firstname') }}">
                <input type="text" name="user_lastname" id="user_lastname" placeholder="Last Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;"
                       value="{{ old('user_lastname') }}">
            </div>
            <input type="email" name="email" id="user_email" placeholder="Email" required
                   style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;"
                   value="{{ old('email') }}">
            <div id="emailError" style="display: none; color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; width: 100%;">
                This email is already registered. Please use a different email.
            </div>
            <div style="display:flex; gap:16px; width:100%;">
                <input type="password" name="password" id="password" placeholder="Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <div id="passwordError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; display: none; width: 100%;">
                Password must be at least 8 characters long.
            </div>
            <div id="passwordMatchError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; display: none; width: 100%;">
                Passwords do not match.
            </div>
            <div style="text-align: center; margin-top: 10px;">
                <p style="color: #666; font-size: 14px; margin: 0;">
                    Already have an account? 
                    <a href="#" onclick="loginWithPackage()" style="color: #1c2951; text-decoration: underline; font-weight: 600;">
                        Click here to login
                    </a>
                </p>
            </div>
            <div style="display:flex; gap:16px; justify-content:center; margin-top: 20px;">
                <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </button>
                <button type="button" onclick="nextStep()" id="step3NextBtn"
                        class="btn btn-primary btn-lg" style="opacity: 0.5;" disabled>
                    Next<i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 4: PAYMENT INFORMATION --}}
    <div class="step" id="step-4">
        <h2 style="text-align:center; margin-bottom:30px; font-weight:700; letter-spacing:1px;">
            PAYMENT INFORMATION
        </h2>
        
        <div style="max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
            {{-- Package Summary --}}
            <div id="paymentPackageSummary" style="margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-radius: 12px; border: 2px solid #2196f3; max-width: 400px; text-align: center;">
                <h4 style="margin: 0 0 10px 0; color: #1976d2;">Selected Package</h4>
                <div style="font-size: 1.1rem; font-weight: bold; color: #1976d2;">
                    <span id="paymentPackageName"></span>
                </div>
                <div style="font-size: 1.3rem; font-weight: bold; color: #1976d2; margin-top: 8px;">
                    <span id="paymentPackagePrice"></span>
                </div>
            </div>
            
            <h3 style="margin-bottom: 20px; text-align: center;">Choose Payment Method</h3>
            
            <div style="width: 100%; max-width: 500px;">
                <div class="payment-method" onclick="selectPaymentMethod('credit_card')">
                    <div class="payment-icon">💳</div>
                    <div>
                        <h4 style="margin: 0 0 5px 0;">Credit/Debit Card</h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">Pay securely with your credit or debit card</p>
                    </div>
                </div>
                
                <div class="payment-method" onclick="selectPaymentMethod('gcash')">
                    <div class="payment-icon">📱</div>
                    <div>
                        <h4 style="margin: 0 0 5px 0;">GCash</h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">Pay using your GCash mobile wallet</p>
                    </div>
                </div>
                
                <div class="payment-method" onclick="selectPaymentMethod('bank_transfer')">
                    <div class="payment-icon">🏦</div>
                    <div>
                        <h4 style="margin: 0 0 5px 0;">Bank Transfer</h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">Transfer payment directly to our bank account</p>
                    </div>
                </div>
                
                <div class="payment-method" onclick="selectPaymentMethod('installment')">
                    <div class="payment-icon">📅</div>
                    <div>
                        <h4 style="margin: 0 0 5px 0;">Installment Plan</h4>
                        <p style="margin: 0; color: #666; font-size: 14px;">Pay in monthly installments</p>
                    </div>
                </div>
            </div>
            
            <div id="selectedPaymentDisplay" style="display: none; margin: 20px 0; padding: 15px; background: #e8f5e8; border-radius: 8px; text-align: center; max-width: 400px;">
                <strong>Selected Payment Method: <span id="selectedPaymentName"></span></strong>
            </div>
            
            <div style="display:flex; gap:20px; justify-content:center; margin-top: 30px;">
                <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </button>
                <button type="button" onclick="nextStep()" id="paymentNextBtn" disabled
                        class="btn btn-primary btn-lg" style="opacity: 0.5;">
                    Next<i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 5: STUDENT MODULAR REGISTRATION --}}
    <div class="step" id="step-5">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            STUDENT MODULAR REGISTRATION FORM
        </h2>

        {{-- Dynamic Form Fields (includes all sections) --}}
        <div id="dynamic-fields-container">
            <x-dynamic-enrollment-form :requirements="$formRequirements" />
        </div>

        <h3>Program</h3>
        <div class="input-row">
            <select name="program_id" id="program_select" required>
                <option value="">Select Program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}"
                        {{ old('program_id', $programId ?? '') == $program->program_id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Module Selection Container --}}
        <div id="module-selection-container" style="display: none; margin-top: 20px;">
            <h3>Select Modules</h3>
            <div class="modules-scroll-container" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #f9f9f9;">
                <div id="modules-list">
                    <!-- Modules will be populated here -->
                </div>
            </div>
        </div>

        <h3>Start Date</h3>
        <div class="course-box" style="margin-bottom:20px;">
            <input type="date" name="Start_Date" required value="{{ old('Start_Date') }}">
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
            <label class="form-check-label" for="termsCheckbox">
                I agree to the 
                <a href="#" id="showTerms" class="text-primary text-decoration-underline">
                  Terms and Conditions
                </a>
            </label>
        </div>

        <div class="d-flex gap-3 justify-content-center flex-column flex-md-row">
            <!-- Mobile: Full width buttons, Tablet & PC: Side by side -->
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg order-2 order-md-1">
                <i class="bi bi-arrow-left me-2"></i>Back
            </button>
            <button type="submit" class="btn btn-primary btn-lg order-1 order-md-2" id="enrollBtn" disabled>
                <i class="bi bi-check-circle me-2"></i>Enroll Now
            </button>
        </div>
    </div>
</form>
</div>

{{-- Terms and Conditions Modal --}}
<div id="termsModal"
     style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh;
            background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
  <div style="background:#fff; padding:30px; border-radius:16px; max-width:600px; width:90%;">
    <h2>Terms and Conditions</h2>
    <div style="max-height:300px; overflow-y:auto; margin:20px 0;">
      <p>
        By registering, you agree to abide by the rules and regulations of the review center.
        You consent to the processing of your personal data for enrollment and communication.
        All fees paid are non-refundable once the review program has started.
      </p>
    </div>
    <button id="agreeBtn" type="button"
            style="background:#1c2951; color:#fff; border:none; border-radius:8px;
                   padding:10px 30px; font-size:1rem; cursor:pointer;">
      Agree and Continue
    </button>
  </div>
</div>

{{-- Success Modal - Only show for registration completion messages --}}
@if(session('success') && str_contains(session('success'), 'registration'))
  <div id="successModal"
       style="display:flex; position:fixed; top:0; left:0; width:100vw; height:100vh;
              background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div class="success-modal-content" style="background:white; border-radius:20px; max-width:500px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideIn 0.3s ease-out;">
      <!-- Success Icon -->
      <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:40px 20px 20px; color:white;">
        <div style="width:80px; height:80px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; backdrop-filter:blur(10px);">
          <i class="bi bi-check-circle-fill" style="font-size:2.5rem; color:white;"></i>
        </div>
        <h2 style="margin:0; font-size:1.8rem; font-weight:700; color:white;">Registration Successful!</h2>
      </div>
      
      <!-- Content -->
      <div style="padding:30px;">
        <p style="color:#666; font-size:1.1rem; margin:0 0 30px; line-height:1.5;">{{ session('success') }}</p>
        
        <!-- Buttons -->
        <div style="display:flex; gap:15px; justify-content:center; flex-wrap:wrap;">
          <button id="successOk" type="button" class="btn btn-primary btn-lg"
                 style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none; padding:12px 30px; border-radius:10px; color:white; font-weight:600; cursor:pointer; transition:all 0.3s ease;">
            <i class="bi bi-house-door me-2"></i>Go to Homepage
          </button>
          <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-lg" 
             style="padding:12px 30px; border-radius:10px; text-decoration:none; transition:all 0.3s ease;">
            <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    @keyframes modalSlideIn {
      from { opacity: 0; transform: translateY(-50px) scale(0.9); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .success-modal-content button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
  </style>
@endif

{{-- Login Success Modal - Shows welcome back message when returning from login --}}
@if(session('success') && str_contains(session('success'), 'Welcome back'))
  <div id="loginSuccessModal" 
       style="position:fixed; top:20px; right:20px; background:#fff; padding:15px 20px; 
              border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000; 
              max-width:300px; animation: slideIn 0.5s ease-out, fadeOut 0.5s ease-out 5s forwards;">
    <p style="margin:0; color:#333;"><strong>{{ session('success') }}</strong></p>
  </div>
  <style>
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; visibility: hidden; }
    }
  </style>
@endif

<script>
// Global variables (declared once at the top)
let currentStep = 1;
let selectedPackageId = null;
let selectedPaymentMethod = null;
let currentPackageIndex = 0;
let packagesPerView = 2;
let totalPackages = <?php echo isset($packages) && is_countable($packages) ? (int)count($packages) : 0; ?>;

// Check if user is logged in (set from server)
const isUserLoggedIn = @if(session('user_id')) true @else false @endif;
const loggedInUserName = '@if(session("user_name")){{ session("user_name") }}@endif';
const loggedInUserFirstname = '@if(session("user_firstname")){{ session("user_firstname") }}@endif';
const loggedInUserLastname = '@if(session("user_lastname")){{ session("user_lastname") }}@endif';
const loggedInUserEmail = '@if(session("user_email")){{ session("user_email") }}@endif';

// Modules data from server
const allModules = @json($allModules ?? []);

// Package carousel functionality
function scrollPackages(direction) {
    const carousel = document.getElementById('packagesCarousel');
    if (!carousel) return;
    
    const scrollAmount = 340; // Package card width + gap
    
    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Keep the old function name for compatibility
function slidePackages(direction) {
    scrollPackages(direction === 1 ? 'right' : 'left');
}

function updateArrowStates() {
    // This function is no longer needed with the new carousel but keeping for compatibility
    return;
}

// Function to update the progress bar based on current step
function updateProgress(step) {
    const progress = document.querySelector('.progress-bar');
    
    if (progress) {
        let percentage;
        
        if (isUserLoggedIn) {
            // For logged in users: 4 steps (Package, Learning Mode, Payment, Registration)
            // Step mapping: 1->25%, 2->50%, 4->75%, 5->100%
            const stepMapping = {1: 25, 2: 50, 4: 75, 5: 100};
            percentage = stepMapping[step] || Math.round((step / 4) * 100);
        } else {
            // For non-logged in users: 5 steps (Package, Learning Mode, Account, Payment, Registration)
            percentage = Math.round((step / 5) * 100);
        }
        
        progress.style.width = percentage + '%';
        progress.setAttribute('aria-valuenow', percentage);
    }
}

// Step navigation with animations
function nextStep() {
    console.log('nextStep() called - Current step:', currentStep, 'User logged in:', isUserLoggedIn);
    
    // Validate current step before proceeding
    if (currentStep === 3) {
        const isValid = validateStep3();
        if (!isValid) {
            console.log('Step 3 validation failed - cannot proceed');
            return;
        }
    }
    
    if (currentStep === 1) {
        // Always go to learning mode selection first
        animateStepTransition('step-1', 'step-2');
        currentStep = 2;
    } else if (currentStep === 2) {
        // From learning mode, check if user is logged in
        if (isUserLoggedIn) {
            // Skip account registration and go directly to payment
            console.log('User logged in - skipping to payment');
            animateStepTransition('step-2', 'step-4');
            currentStep = 4;
            // Update payment step with package info
            updatePaymentStepInfo();
        } else {
            // User not logged in, go to account registration
            console.log('User not logged in - going to account registration');
            animateStepTransition('step-2', 'step-3');
            currentStep = 3;
        }
    } else if (currentStep === 3) {
        // From account registration to payment
        console.log('Going from account registration to payment');
        copyAccountDataToStudentForm();
        animateStepTransition('step-3', 'step-4');
        currentStep = 4;
        // Update payment step with package info
        updatePaymentStepInfo();
    } else if (currentStep === 4) {
        // From payment to student registration
        animateStepTransition('step-4', 'step-5');
        currentStep = 5;
        // Auto-fill user data if logged in
        fillLoggedInUserData();
        // Also auto-fill in case user comes directly to step 5
        copyAccountDataToStudentForm();
    }
    
    // Update progress bar
    updateProgress(currentStep);
}

function prevStep() {
    console.log('Going back from step:', currentStep, 'User logged in:', isUserLoggedIn);
    
    if (currentStep === 5) {
        // From student registration back to payment
        animateStepTransition('step-5', 'step-4', true);
        currentStep = 4;
    } else if (currentStep === 4) {
        // From payment, check if user is logged in
        if (isUserLoggedIn) {
            // Skip account registration and go back to learning mode
            console.log('User logged in - going back to learning mode');
            animateStepTransition('step-4', 'step-2', true);
            currentStep = 2;
        } else {
            // User not logged in, go back to account registration
            console.log('User not logged in - going back to account registration');
            animateStepTransition('step-4', 'step-3', true);
            currentStep = 3;
        }
    } else if (currentStep === 3) {
        // From account registration back to learning mode
        animateStepTransition('step-3', 'step-2', true);
        currentStep = 2;
    } else if (currentStep === 2) {
        // From learning mode back to package selection
        animateStepTransition('step-2', 'step-1', true);
        currentStep = 1;
    }
    
    // Update progress bar
    updateProgress(currentStep);
}

// Alternative function name for consistency
function previousStep() {
    prevStep();
}

function animateStepTransition(fromStepId, toStepId, isBack = false) {
    const fromStep = document.getElementById(fromStepId);
    const toStep = document.getElementById(toStepId);
    
    // Add slide-out class to current step
    fromStep.classList.add(isBack ? 'slide-out-right' : 'slide-out-left');
    
    setTimeout(() => {
        fromStep.classList.remove('active', 'slide-out-left', 'slide-out-right');
        toStep.classList.add('active');
    }, 250);
}

// Package Selection
function selectPackage(packageId, packageName, packagePrice) {
    // Remove selection from all package cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Highlight selected package
    event.target.closest('.package-card').classList.add('selected');
    
    // Store selection
    selectedPackageId = packageId;
    
    // Update hidden input
    const packageInput = document.querySelector('input[name="package_id"]');
    if (packageInput) {
        packageInput.value = packageId;
    } else {
        // Create hidden input if it doesn't exist
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'package_id';
        hiddenInput.value = packageId;
        document.querySelector('form').appendChild(hiddenInput);
    }
    
    // Show selected package display with price
    document.getElementById('selectedPackageName').textContent = packageName;
    const priceElement = document.getElementById('selectedPackagePrice');
    if (priceElement) {
        priceElement.textContent = '₱' + parseFloat(packagePrice).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    document.getElementById('selectedPackageDisplay').style.display = 'block';
    
    // Store in sessionStorage for login continuity
    sessionStorage.setItem('selectedPackageId', packageId);
    sessionStorage.setItem('selectedPackageName', packageName);
    sessionStorage.setItem('selectedPackagePrice', packagePrice);
    
    // Enable next button
    const nextBtn = document.getElementById('packageNextBtn');
    nextBtn.disabled = false;
    nextBtn.style.opacity = '1';
}

// Payment Method Selection
function selectPaymentMethod(method) {
    // Remove selection from all payment methods
    document.querySelectorAll('.payment-method').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Highlight selected payment method
    event.target.closest('.payment-method').classList.add('selected');
    
    // Store selection
    selectedPaymentMethod = method;
    
    // Update display
    const methodNames = {
        'credit_card': 'Credit/Debit Card',
        'gcash': 'GCash',
        'bank_transfer': 'Bank Transfer',
        'installment': 'Installment Plan'
    };
    
    document.getElementById('selectedPaymentName').textContent = methodNames[method];
    document.getElementById('selectedPaymentDisplay').style.display = 'block';
    
    // Enable next button
    const nextBtn = document.getElementById('paymentNextBtn');
    nextBtn.disabled = false;
    nextBtn.style.opacity = '1';
}

// Update Payment Step Info
function updatePaymentStepInfo() {
    const packageName = document.getElementById('selectedPackageName').textContent;
    const packagePrice = document.getElementById('selectedPackagePrice').textContent;
    
    const paymentPackageName = document.getElementById('paymentPackageName');
    const paymentPackagePrice = document.getElementById('paymentPackagePrice');
    
    if (paymentPackageName && packageName) {
        paymentPackageName.textContent = packageName;
    }
    
    if (paymentPackagePrice && packagePrice) {
        paymentPackagePrice.textContent = packagePrice;
    }
}

// Learning Mode Selection
function selectLearningMode(mode) {
    // Remove selection from all learning mode cards
    document.querySelectorAll('.learning-mode-card').forEach(card => {
        card.classList.remove('selected');
        card.style.border = '3px solid transparent';
        card.style.boxShadow = 'none';
    });
    
    // Highlight selected learning mode
    const selectedCard = document.querySelector(`.learning-mode-card[data-mode="${mode}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        selectedCard.style.border = '3px solid #1c2951';
        selectedCard.style.boxShadow = '0 4px 12px rgba(28, 41, 81, 0.2)';
    }
    
    // Update hidden input and display
    document.getElementById('learning_mode').value = mode;
    document.getElementById('selectedLearningModeName').textContent = mode.charAt(0).toUpperCase() + mode.slice(1);
    document.getElementById('selectedLearningModeDisplay').style.display = 'block';
    
    // Enable next button
    const nextBtn = document.getElementById('learningModeNextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
    }
    
    console.log('Learning mode selected:', mode);
}

// Registration Mode Selection
function selectRegistrationMode(mode) {
    // Remove selection from all mode cards
    document.querySelectorAll('.mode-card').forEach(card => {
        card.style.border = '2px solid #ddd';
        card.style.boxShadow = 'none';
    });
    
    // Highlight selected mode card
    const selectedCard = document.querySelector(`.${mode}-card`);
    selectedCard.style.border = '2px solid #1c2951';
    selectedCard.style.boxShadow = '0 4px 12px rgba(28, 41, 81, 0.2)';
    
    // Update hidden input
    document.getElementById('registration_mode').value = mode;
    
    console.log('Registration mode selected:', mode);
}

// Function to fill logged-in user data
function fillLoggedInUserData() {
    if (isUserLoggedIn) {
        console.log('Filling logged-in user data...');
        
        // Auto-fill Step 4 (Modular Student Registration) fields with logged-in user data
        const firstnameField = document.getElementById('firstname');
        const lastnameField = document.getElementById('lastname');
        
        // Use session data if available
        if (firstnameField && loggedInUserFirstname) {
            firstnameField.value = loggedInUserFirstname;
            console.log('Auto-filled firstname from session:', loggedInUserFirstname);
        }
        if (lastnameField && loggedInUserLastname) {
            lastnameField.value = loggedInUserLastname;
            console.log('Auto-filled lastname from session:', loggedInUserLastname);
        }
        
        // Also auto-fill Step 2 (Account Registration) fields if user navigates back
        const userFirstnameField = document.getElementById('user_firstname');
        const userLastnameField = document.getElementById('user_lastname');
        const userEmailField = document.getElementById('user_email');
        
        if (userFirstnameField && loggedInUserFirstname) {
            userFirstnameField.value = loggedInUserFirstname;
        }
        if (userLastnameField && loggedInUserLastname) {
            userLastnameField.value = loggedInUserLastname;
        }
        if (userEmailField && loggedInUserEmail) {
            userEmailField.value = loggedInUserEmail;
        }
        
        console.log('Auto-filled student registration fields with logged-in user data');
    }
}

// Function to copy Account Registration data to Modular Student Registration
function copyAccountDataToStudentForm() {
    // Get values from Step 2 (Account Registration)
    const userFirstname = document.getElementById('user_firstname')?.value || '';
    const userLastname = document.getElementById('user_lastname')?.value || '';
    const userEmail = document.getElementById('user_email')?.value || '';
    
    // Set values in Step 4 (Modular Student Registration)
    const firstnameField = document.getElementById('firstname');
    const lastnameField = document.getElementById('lastname');
    
    if (firstnameField && userFirstname && !firstnameField.value) {
        firstnameField.value = userFirstname;
        console.log('Auto-filled firstname:', userFirstname);
    }
    if (lastnameField && userLastname && !lastnameField.value) {
        lastnameField.value = userLastname;
        console.log('Auto-filled lastname:', userLastname);
    }
    
    console.log('Auto-filled student registration fields from account data');
}

// Function to check if email already exists in database
async function checkEmailExists(email) {
    if (!email) return false;
    
    try {
        const response = await fetch('{{ route("check.email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        return data.exists;
    } catch (error) {
        console.error('Error checking email:', error);
        return false;
    }
}

// Function to validate email on blur
async function validateEmail() {
    const emailField = document.getElementById('user_email');
    const emailError = document.getElementById('emailError');
    
    if (!emailField || !emailError) return;
    
    const email = emailField.value.trim();
    
    if (email) {
        // Show loading state
        emailField.style.borderColor = '#ffc107';
        emailError.style.display = 'none';
        
        const exists = await checkEmailExists(email);
        
        if (exists) {
            emailField.style.borderColor = '#dc3545';
            emailError.style.display = 'block';
        } else {
            emailField.style.borderColor = '#28a745';
            emailError.style.display = 'none';
        }
    } else {
        emailField.style.borderColor = '#ccc';
        emailError.style.display = 'none';
    }
    
    // Validate all Step 2 fields after email validation
    setTimeout(validateStep2, 100);
}

// Password validation function
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField || !passwordError) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        passwordError.style.display = 'block';
        passwordError.textContent = 'Password must be at least 8 characters long.';
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        passwordError.style.display = 'none';
        return true;
    } else {
        passwordField.style.borderColor = '#ccc';
        passwordError.style.display = 'none';
        return true;
    }
}

// Password confirmation validation function
function validatePasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('passwordMatchError');
    
    if (!passwordField || !passwordConfirmField || !passwordMatchError) return true;
    
    const password = passwordField.value;
    const passwordConfirm = passwordConfirmField.value;
    
    if (passwordConfirm.length > 0 && password !== passwordConfirm) {
        passwordConfirmField.style.borderColor = '#dc3545';
        passwordMatchError.style.display = 'block';
        passwordMatchError.textContent = 'Passwords do not match.';
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        passwordMatchError.style.display = 'none';
        return true;
    } else {
        passwordConfirmField.style.borderColor = '#ccc';
        passwordMatchError.style.display = 'none';
        return true;
    }
}

// Step 2 (Learning Mode) validation function  
function validateStep2() {
    const selectedMode = document.getElementById('learning_mode').value;
    const nextBtn = document.getElementById('learningModeNextBtn');
    
    if (!nextBtn) return false;
    
    if (selectedMode) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        return true;
    } else {
        nextBtn.disabled = true;
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
        return false;
    }
}

// Step 3 (Account Registration) validation function
function validateStep3() {
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.getElementById('step3NextBtn');
    
    if (!nextBtn) {
        console.log('No next button found');
        return false;
    }
    
    // Check if all required fields are filled
    const firstname = firstnameField ? firstnameField.value.trim() : '';
    const lastname = lastnameField ? lastnameField.value.trim() : '';
    const email = emailField ? emailField.value.trim() : '';
    const password = passwordField ? passwordField.value : '';
    const passwordConfirm = passwordConfirmField ? passwordConfirmField.value : '';
    
    const allFieldsFilled = firstname && lastname && email && password && passwordConfirm;
    
    // Check validation states
    const isEmailValid = email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    const isPasswordValid = password.length >= 8;
    const isPasswordConfirmValid = password === passwordConfirm && password.length > 0;
    const emailError = document.getElementById('emailError');
    const emailHasError = emailError && emailError.style.display === 'block';
    const passwordError = document.getElementById('passwordError');
    const passwordHasError = passwordError && passwordError.style.display === 'block';
    const passwordMatchError = document.getElementById('passwordMatchError');
    const passwordMatchHasError = passwordMatchError && passwordMatchError.style.display === 'block';
    
    const allValidationsPassed = isEmailValid && isPasswordValid && isPasswordConfirmValid && 
                                !emailHasError && !passwordHasError && !passwordMatchHasError;
    
    // Debug logging
    console.log('Step 3 Validation:', {
        firstname,
        lastname,
        email,
        password: password ? '***' : '',
        passwordConfirm: passwordConfirm ? '***' : '',
        allFieldsFilled,
        isEmailValid,
        isPasswordValid,
        isPasswordConfirmValid,
        emailHasError,
        passwordHasError,
        passwordMatchHasError,
        allValidationsPassed
    });
    
    if (allFieldsFilled && allValidationsPassed) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.background = 'linear-gradient(90deg,#a259c6,#6a82fb)';
        nextBtn.style.pointerEvents = 'auto';
        console.log('Step 3 validation passed - button enabled');
        return true;
    } else {
        nextBtn.disabled = true;
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
        nextBtn.style.background = '#cccccc';
        nextBtn.style.pointerEvents = 'none';
        console.log('Step 3 validation failed - button disabled');
        return false;
    }
}

// Make functions globally accessible
window.slidePackages = slidePackages;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectPackage = selectPackage;
window.selectPaymentMethod = selectPaymentMethod;
window.selectLearningMode = selectLearningMode;

// Email validation function
function validateEmail() {
    const emailField = document.getElementById('user_email');
    const emailError = document.getElementById('emailError');
    
    if (!emailField) return true;
    
    const email = emailField.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailPattern.test(email)) {
        emailField.style.borderColor = '#dc3545';
        emailError.style.display = 'block';
        emailError.textContent = 'Please enter a valid email address.';
        return false;
    } else if (email) {
        // Check for existing email via AJAX
        fetch('/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                emailField.style.borderColor = '#dc3545';
                emailError.style.display = 'block';
                emailError.textContent = 'This email is already registered. Please use a different email.';
                validateStep3();
            } else {
                emailField.style.borderColor = '#28a745';
                emailError.style.display = 'none';
                validateStep3();
            }
        })
        .catch(error => {
            console.error('Email validation error:', error);
            emailField.style.borderColor = '#ccc';
            emailError.style.display = 'none';
            validateStep3();
        });
        
        return true;
    } else {
        emailField.style.borderColor = '#ccc';
        emailError.style.display = 'none';
        return true;
    }
}

// Password validation function
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField || !passwordError) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        passwordError.style.display = 'block';
        passwordError.textContent = 'Password must be at least 8 characters long.';
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        passwordError.style.display = 'none';
        return true;
    } else {
        passwordField.style.borderColor = '#ccc';
        passwordError.style.display = 'none';
        return true;
    }
}

// Password confirmation validation function
function validatePasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('passwordMatchError');
    
    if (!passwordField || !passwordConfirmField || !passwordMatchError) return true;
    
    const password = passwordField.value;
    const passwordConfirm = passwordConfirmField.value;
    
    if (passwordConfirm.length > 0 && password !== passwordConfirm) {
        passwordConfirmField.style.borderColor = '#dc3545';
        passwordMatchError.style.display = 'block';
        passwordMatchError.textContent = 'Passwords do not match.';
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        passwordMatchError.style.display = 'none';
        return true;
    } else {
        passwordConfirmField.style.borderColor = '#ccc';
        passwordMatchError.style.display = 'none';
        return true;
    }
}

// Step 2 (Learning Mode) validation function  
function validateStep2() {
    const selectedMode = document.getElementById('learning_mode').value;
    const nextBtn = document.getElementById('learningModeNextBtn');
    
    if (!nextBtn) return false;
    
    if (selectedMode) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        return true;
    } else {
        nextBtn.disabled = true;
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
        return false;
    }
}

// Step 3 (Account Registration) validation function
function validateStep3() {
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.getElementById('step3NextBtn');
    
    if (!nextBtn) {
        console.log('No next button found');
        return false;
    }
    
    // Check if all required fields are filled
    const firstname = firstnameField ? firstnameField.value.trim() : '';
    const lastname = lastnameField ? lastnameField.value.trim() : '';
    const email = emailField ? emailField.value.trim() : '';
    const password = passwordField ? passwordField.value : '';
    const passwordConfirm = passwordConfirmField ? passwordConfirmField.value : '';
    
    const allFieldsFilled = firstname && lastname && email && password && passwordConfirm;
    
    // Check validation states
    const isEmailValid = email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    const isPasswordValid = password.length >= 8;
    const isPasswordConfirmValid = password === passwordConfirm && password.length > 0;
    const emailError = document.getElementById('emailError');
    const emailHasError = emailError && emailError.style.display === 'block';
    const passwordError = document.getElementById('passwordError');
    const passwordHasError = passwordError && passwordError.style.display === 'block';
    const passwordMatchError = document.getElementById('passwordMatchError');
    const passwordMatchHasError = passwordMatchError && passwordMatchError.style.display === 'block';
    
    const allValidationsPassed = isEmailValid && isPasswordValid && isPasswordConfirmValid && 
                                !emailHasError && !passwordHasError && !passwordMatchHasError;
    
    // Debug logging
    console.log('Step 3 Validation:', {
        firstname,
        lastname,
        email,
        password: password ? '***' : '',
        passwordConfirm: passwordConfirm ? '***' : '',
        allFieldsFilled,
        isEmailValid,
        isPasswordValid,
        isPasswordConfirmValid,
        emailHasError,
        passwordHasError,
        passwordMatchHasError,
        allValidationsPassed
    });
    
    if (allFieldsFilled && allValidationsPassed) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.background = 'linear-gradient(90deg,#a259c6,#6a82fb)';
        nextBtn.style.pointerEvents = 'auto';
        console.log('Step 3 validation passed - button enabled');
        return true;
    } else {
        nextBtn.disabled = true;
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
        nextBtn.style.background = '#cccccc';
        nextBtn.style.pointerEvents = 'none';
        console.log('Step 3 validation failed - button disabled');
        return false;
    }
}

// Make functions globally accessible
window.slidePackages = slidePackages;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectPackage = selectPackage;
window.selectPaymentMethod = selectPaymentMethod;
window.selectLearningMode = selectLearningMode;

// Email validation function
function validateEmail() {
    const emailField = document.getElementById('user_email');
    const emailError = document.getElementById('emailError');
    
    if (!emailField) return true;
    
    const email = emailField.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailPattern.test(email)) {
        emailField.style.borderColor = '#dc3545';
        emailError.style.display = 'block';
        emailError.textContent = 'Please enter a valid email address.';
        return false;
    } else if (email) {
        // Check for existing email via AJAX
        fetch('/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                emailField.style.borderColor = '#dc3545';
                emailError.style.display = 'block';
                emailError.textContent = 'This email is already registered. Please use a different email.';
                validateStep3();
            } else {
                emailField.style.borderColor = '#28a745';
                emailError.style.display = 'none';
                validateStep3();
            }
        })
        .catch(error => {
            console.error('Email validation error:', error);
            emailField.style.borderColor = '#ccc';
            emailError.style.display = 'none';
            validateStep3();
        });
        
        return true;
    } else {
        emailField.style.borderColor = '#ccc';
        emailError.style.display = 'none';
        return true;
    }
}

// Password validation function
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField || !passwordError) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        passwordError.style.display = 'block';
        passwordError.textContent = 'Password must be at least 8 characters long.';
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        passwordError.style.display = 'none';
        return true;
    } else {
        passwordField.style.borderColor = '#ccc';
        passwordError.style.display = 'none';
        return true;
    }
}

// Password confirmation validation function
function validatePasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('passwordMatchError');
    
    if (!passwordField || !passwordConfirmField || !passwordMatchError) return true;
    
    const password = passwordField.value;
    const passwordConfirm = passwordConfirmField.value;
    
    if (passwordConfirm.length > 0 && password !== passwordConfirm) {
        passwordConfirmField.style.borderColor = '#dc3545';
        passwordMatchError.style.display = 'block';
        passwordMatchError.textContent = 'Passwords do not match.';
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        passwordMatchError.style.display = 'none';
        return true;
    } else {
        passwordConfirmField.style.borderColor = '#ccc';
        passwordMatchError.style.display = 'none';
        return true;
    }
}

// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, user logged in:', isUserLoggedIn);
    console.log('User session data:', {
        name: loggedInUserName,
        firstname: loggedInUserFirstname,
        lastname: loggedInUserLastname,
        email: loggedInUserEmail
    });
    
    // Check if we're returning from login with a package selection
    const continueEnrollment = sessionStorage.getItem('continueEnrollment');
    const skipToPayment = sessionStorage.getItem('skipToPayment');
    const savedPackageId = sessionStorage.getItem('selectedPackageId');
    const savedPackageName = sessionStorage.getItem('selectedPackageName');
    const savedPackagePrice = sessionStorage.getItem('selectedPackagePrice');
    
    if (continueEnrollment === 'true' && savedPackageId && savedPackageName) {
        console.log('Continuing enrollment after login');
        // Clear the session flags
        sessionStorage.removeItem('continueEnrollment');
        sessionStorage.removeItem('skipToPayment');
        
        // Auto-select the saved package
        selectedPackageId = savedPackageId;
        
        // Find and highlight the package card
        const packageCard = document.querySelector(`[data-package-id="${savedPackageId}"]`);
        if (packageCard) {
            packageCard.classList.add('selected');
        }
        
        // Update the form
        const packageInput = document.querySelector('input[name="package_id"]');
        if (packageInput) {
            packageInput.value = savedPackageId;
        }
        
        // Show selected package display with price
        document.getElementById('selectedPackageName').textContent = savedPackageName;
        if (savedPackagePrice) {
            const priceElement = document.getElementById('selectedPackagePrice');
            if (priceElement) {
                priceElement.textContent = '₱' + parseFloat(savedPackagePrice).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
        document.getElementById('selectedPackageDisplay').style.display = 'block';
        
        // Enable next button
        const nextBtn = document.getElementById('packageNextBtn');
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        
        // If user logged in from step 2 (account registration), skip to payment step (step 4)
        if (skipToPayment === 'true') {
            setTimeout(() => {
                // Go to step 4 (payment)
                animateStepTransition('step-1', 'step-4');
                currentStep = 4;
                updateProgress(currentStep);
                // Update payment step with package info
                updatePaymentStepInfo();
            }, 500);
        }
    }
    
    // Fill logged-in user data on page load
    if (isUserLoggedIn) {
        console.log('Filling logged-in user data on page load');
        fillLoggedInUserData();
    }
    
    // Initialize registration mode selection (default to sync)
    selectRegistrationMode('sync');
    
    // Initialize carousel first
    updateArrowStates();
    
    // Adjust for responsive
    function adjustCarousel() {
        const slider = document.querySelector('.package-slider');
        if (slider) {
            if (window.innerWidth <= 768) {
                packagesPerView = 1;
                slider.style.width = '340px';
            } else {
                packagesPerView = 2;
                slider.style.width = '700px';
            }
            // Reset position when switching views
            currentPackageIndex = 0;
            const track = document.getElementById('packageTrack');
            if (track) {
                track.style.transform = 'translateX(0px)';
            }
            updateArrowStates();
        }
    }
    
    adjustCarousel();
    window.addEventListener('resize', adjustCarousel);

    // Email validation
    const emailField = document.getElementById('user_email');
    if (emailField) {
        emailField.addEventListener('blur', validateEmail);
        emailField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('emailError').style.display = 'none';
            // Validate all fields when email changes
            setTimeout(validateStep3, 100);
        });
    }

    // First name and last name validation
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    
    if (firstnameField) {
        firstnameField.addEventListener('input', function() {
            // Validate all fields when first name changes
            setTimeout(validateStep3, 100);
        });
    }
    
    if (lastnameField) {
        lastnameField.addEventListener('input', function() {
            // Validate all fields when last name changes
            setTimeout(validateStep3, 100);
        });
    }

    // Password validation
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    
    if (passwordField) {
        passwordField.addEventListener('blur', validatePassword);
        passwordField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            // Don't hide the error here - let validatePassword handle it
            // Also validate confirmation when password changes
            setTimeout(validatePassword, 50);
            setTimeout(validatePasswordConfirmation, 100);
            setTimeout(validateStep3, 200);
        });
    }
    
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('blur', validatePasswordConfirmation);
        passwordConfirmField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('passwordMatchError').style.display = 'none';
            setTimeout(validatePasswordConfirmation, 50);
            setTimeout(validateStep3, 100);
        });
    }

    // Initial validation on page load
    setTimeout(validateStep3, 500);
    
    // Add more frequent validation triggers
    setTimeout(() => {
        const form = document.getElementById('enrollmentForm');
        if (form) {
            form.addEventListener('input', function(e) {
                if (e.target.closest('#step-3')) {
                    setTimeout(validateStep3, 100);
                }
            });
        }
    }, 1000);

    // Terms & Conditions
    const showTerms = document.getElementById('showTerms');
    const termsModal = document.getElementById('termsModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const termsCheckbox = document.getElementById('termsCheckbox');
    const enrollBtn = document.getElementById('enrollBtn');

    if (termsCheckbox && enrollBtn) {
        termsCheckbox.disabled = true;
        enrollBtn.disabled = true;

        // Add event listener for terms checkbox
        termsCheckbox.addEventListener('change', function() {
            validateModuleSelection();
        });

        if (showTerms) {
            showTerms.addEventListener('click', function(e) {
                e.preventDefault();
                agreeBtn.disabled = false;
                termsModal.style.display = 'flex';
            });
        }

        if (agreeBtn) {
            agreeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                termsModal.style.display = 'none';
                termsCheckbox.disabled = false;
                termsCheckbox.checked = true;
                
                // Validate both terms and module selection
                validateModuleSelection();
            });
        }

        window.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                termsModal.style.display = 'none';
            }
        });
    }
    
    // Handle success modal
    const successModal = document.getElementById('successModal');
    if (successModal) {
        successModal.style.display = 'flex';
        
        const successOk = document.getElementById('successOk');
        if (successOk) {
            successOk.onclick = function() {
                window.location.href = '{{ route("home") }}';
            };
        }
    }

    // Initialize program selection handler
    handleProgramSelection();
});

// Program selection handler
function handleProgramSelection() {
    const programSelect = document.getElementById('program_select');
    const moduleContainer = document.getElementById('module-selection-container');
    const modulesList = document.getElementById('modules-list');
    
    if (!programSelect || !moduleContainer || !modulesList) {
        console.error('Program selection elements not found');
        return;
    }
    
    programSelect.addEventListener('change', function() {
        const selectedProgramId = this.value;
        
        // Update hidden input
        const hiddenProgramInput = document.getElementById('hidden_program_id');
        if (hiddenProgramInput) {
            hiddenProgramInput.value = selectedProgramId;
        }
        
        if (selectedProgramId) {
            // Show module selection container
            moduleContainer.style.display = 'block';
            
            // Filter modules for the selected program
            const programModules = allModules.filter(module => 
                module.program_id == selectedProgramId
            );
            
            // Clear existing modules
            modulesList.innerHTML = '';
            
            if (programModules.length > 0) {
                programModules.forEach(module => {
                    const moduleDiv = document.createElement('div');
                    moduleDiv.className = 'module-item';
                    moduleDiv.style.cssText = `
                        padding: 12px;
                        margin-bottom: 10px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        background: white;
                    `;
                    
                    moduleDiv.innerHTML = `
                        <input type="checkbox" name="selected_modules[]" value="${module.modules_id}" 
                               id="module_${module.modules_id}" class="module-checkbox" 
                               style="margin-right: 10px;">
                        <label for="module_${module.modules_id}" style="cursor: pointer; margin: 0; font-weight: 500;">
                            ${module.module_name}
                        </label>
                        ${module.module_description ? `<div style="margin-top: 5px; color: #666; font-size: 14px;">${module.module_description}</div>` : ''}
                    `;
                    
                    // Add click handler for the entire div
                    moduleDiv.addEventListener('click', function(e) {
                        if (e.target.type !== 'checkbox') {
                            const checkbox = this.querySelector('input[type="checkbox"]');
                            checkbox.checked = !checkbox.checked;
                            updateModuleSelection(checkbox);
                        }
                    });
                    
                    // Add change handler for checkbox
                    const checkbox = moduleDiv.querySelector('input[type="checkbox"]');
                    checkbox.addEventListener('change', function() {
                        updateModuleSelection(this);
                    });
                    
                    modulesList.appendChild(moduleDiv);
                });
            } else {
                modulesList.innerHTML = `
                    <div style="text-align: center; color: #666; padding: 20px;">
                        No modules available for this program.
                    </div>
                `;
            }
        } else {
            // Hide module selection container if no program selected
            moduleContainer.style.display = 'none';
        }
        
        // Always validate requirements after program selection changes
        validateModuleSelection();
    });
}

// Update module selection styling
function updateModuleSelection(checkbox) {
    const moduleDiv = checkbox.closest('.module-item');
    
    if (checkbox.checked) {
        moduleDiv.style.borderColor = '#1c2951';
        moduleDiv.style.backgroundColor = '#f8f9ff';
        moduleDiv.style.boxShadow = '0 2px 8px rgba(28, 41, 81, 0.1)';
    } else {
        moduleDiv.style.borderColor = '#e0e0e0';
        moduleDiv.style.backgroundColor = 'white';
        moduleDiv.style.boxShadow = 'none';
    }
    
    // Update any validation or UI state as needed
    validateModuleSelection();
}

// Validate module selection
function validateModuleSelection() {
    const selectedModules = document.querySelectorAll('input[name="selected_modules[]"]:checked');
    const enrollBtn = document.getElementById('enrollBtn');
    const termsCheckbox = document.getElementById('termsCheckbox');
    const programSelect = document.getElementById('program_select');
    
    // Check all requirements
    const hasModulesSelected = selectedModules.length > 0;
    const termsAccepted = termsCheckbox ? termsCheckbox.checked : false;
    const programSelected = programSelect ? programSelect.value !== '' : false;
    
    console.log('Validation Check:', {
        hasModulesSelected,
        termsAccepted,
        programSelected,
        modulesCount: selectedModules.length
    });
    
    if (enrollBtn) {
        // Enable button only if all conditions are met
        if (hasModulesSelected && termsAccepted && programSelected) {
            enrollBtn.disabled = false;
            enrollBtn.style.opacity = '1';
            enrollBtn.style.cursor = 'pointer';
            enrollBtn.classList.remove('disabled');
            console.log('Enroll button enabled');
        } else {
            enrollBtn.disabled = true;
            enrollBtn.style.opacity = '0.5';
            enrollBtn.style.cursor = 'not-allowed';
            enrollBtn.classList.add('disabled');
            console.log('Enroll button disabled - missing requirements');
        }
    }
}

// Function to handle login with package selection
function loginWithPackage() {
    // Store current package selection in session storage
    if (selectedPackageId) {
        const selectedPackageName = document.getElementById('selectedPackageName').textContent;
        const selectedPackagePrice = document.getElementById('selectedPackagePrice').textContent;
        sessionStorage.setItem('selectedPackageId', selectedPackageId);
        sessionStorage.setItem('selectedPackageName', selectedPackageName);
        sessionStorage.setItem('selectedPackagePrice', selectedPackagePrice.replace('₱', '').replace(/,/g, ''));
        sessionStorage.setItem('continueEnrollment', 'true');
        sessionStorage.setItem('skipToPayment', 'true');
    }
    
    // Redirect to login page
    window.location.href = '{{ route("login") }}';
}

// Make functions globally accessible
window.scrollPackages = scrollPackages;
window.slidePackages = slidePackages;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectPackage = selectPackage;
window.selectPaymentMethod = selectPaymentMethod;
window.selectLearningMode = selectLearningMode;
window.selectRegistrationMode = selectRegistrationMode;
window.loginWithPackage = loginWithPackage;
window.handleProgramSelection = handleProgramSelection;
window.updateModuleSelection = updateModuleSelection;
window.validateModuleSelection = validateModuleSelection;

</script>
@endsection
