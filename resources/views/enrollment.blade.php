@extends('layouts.navbar')

@section('title', 'Enrollment')

@push('styles')

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
{!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
{!! App\Helpers\SettingsHelper::getProgramCardStyles() !!}
{!! App\Helpers\SettingsHelper::getButtonStyles() !!}

/* Bootstrap Enrollment Page Styles */
.enrollment-hero {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 0;
}

.enrollment-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 280px;
}

.enrollment-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    border-color: rgba(102, 126, 234, 0.3);
}

.enrollment-card h3 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: inherit;
}

.enrollment-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.enrollment-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a67d8 0%, #6c46a0 100%);
}

.enrollment-btn:active {
    transform: translateY(0);
}

.page-title {
    text-align: center;
    margin-bottom: 3rem;
    color: inherit;
}

.page-title h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-title p {
    font-size: 1.1rem;
    opacity: 0.8;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .enrollment-hero {
        min-height: 60vh;
        padding: 2rem 0;
    }
    
    .enrollment-card {
        margin-bottom: 2rem;
        padding: 2rem 1.5rem;
        min-height: 240px;
    }
    
    .enrollment-card h3 {
        font-size: 1.75rem;
    }
    
    .enrollment-btn {
        padding: 0.6rem 2rem;
        font-size: 1rem;
    }
    
    .page-title h1 {
        font-size: 2rem;
    }
    
    .page-title p {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .enrollment-card {
        min-height: 200px;
        padding: 1.5rem 1rem;
    }
    
    .enrollment-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .enrollment-btn {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="enrollment-hero">
        <div class="container">
            <div class="page-title">
                <h1>Choose Your Learning Path</h1>
                <p>Select the program that best fits your educational goals</p>
            </div>
            
            <div class="row justify-content-center g-4">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="enrollment-card program-card enrollment-program-card">
                        <h3>Complete Plan</h3>
                        <p class="text-muted mb-4">Comprehensive program covering all essential topics</p>
                        <button onclick="window.location.href='{{ route('enrollment.full') }}'" 
                                class="btn enrollment-btn enroll-btn">
                            <i class="bi bi-mortarboard"></i> Enroll Now
                        </button>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="enrollment-card program-card enrollment-program-card">
                        <h3>Modular Plan</h3>
                        <p class="text-muted mb-4">Flexible modules tailored to your specific needs</p>
                        <button onclick="window.location.href='{{ route('enrollment.modular') }}'" 
                                class="btn enrollment-btn enroll-btn">
                            <i class="bi bi-puzzle"></i> Enroll Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
