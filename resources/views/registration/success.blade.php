@extends('layouts.navbar')

@section('title', 'Registration Successful')
@section('hide_navbar', true)
@section('hide_footer', true)


@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
    .success-container {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        margin-top: -100px;
    }
    
    .success-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 60px 40px;
        text-align: center;
        max-width: 600px;
        max-height: 600px;
        width: 100%;
    }
    
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #4CAF50, #45a049);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        animation: bounceIn 0.8s ease-out;
    }
    
    .success-icon i {
        color: white;
        font-size: 3rem;
    }
    
    .success-title {
        color: #2c3e50;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }
    
    .success-message {
        color: #7f8c8d;
        font-size: 1.2rem;
        line-height: 1.6;
        margin-bottom: 40px;
        animation: fadeInUp 0.8s ease-out 0.4s both;
    }
    
    .success-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp 0.8s ease-out 0.6s both;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary {
        background: transparent;
        border: 2px solid #bdc3c7;
        padding: 15px 30px;
        border-radius: 50px;
        color: #7f8c8d;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-secondary:hover {
        border-color: #95a5a6;
        color: #2c3e50;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
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
    
    @media (max-width: 768px) {
        .success-card {
            padding: 40px 20px;
            margin: 20px;
        }
        
        .success-title {
            font-size: 2rem;
        }
        
        .success-message {
            font-size: 1.1rem;
        }
        
        .success-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-primary,
        .btn-secondary {
            width: 250px;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        
        <h1 class="success-title">Registration Successful!</h1>
        
        <p class="success-message">
            Thank you for registering with us! Your registration has been submitted successfully and is now under review. 
            You will receive an email notification once your registration has been approved by our admin team.
        </p>
        
        <div class="success-actions">
            <a href="{{ route('login') }}" class="btn-primary">
                <i class="bi bi-box-arrow-in-right"></i>
                Log in now!
            </a>
            
            <a href="{{ route('home') }}" class="btn-secondary">
                <i class="bi bi-house-fill"></i>
                Go to Homepage
            </a>
        </div>
        
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #ecf0f1;">
            <p style="color: #95a5a6; font-size: 0.9rem; margin: 0;">
                <i class="bi bi-info-circle"></i>
                Need help? Contact our support team at 
                <a href="mailto:support@artc.com" style="color: #3498db;">support@artc.com</a>
            </p>
        </div>
    </div>
</div>
@endsection
