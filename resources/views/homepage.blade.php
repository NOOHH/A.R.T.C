@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<style>
{!! App\Helpers\SettingsHelper::getHomepageStyles() !!}

/* Bootstrap Homepage Styles */
.homepage-hero {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.homepage-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    z-index: -1;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.enroll-link {
    font-size: clamp(2.5rem, 8vw, 4rem);
    font-weight: 700;
    letter-spacing: 0.1em;
    text-decoration: none;
    transition: all 0.3s ease;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
    display: inline-block;
    padding: 1rem 2rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.2);
}

.enroll-link:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    text-decoration: none;
}

.hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-top: 1rem;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .homepage-hero {
        min-height: 60vh;
        padding: 2rem 1rem;
    }
    
    .enroll-link {
        padding: 0.8rem 1.5rem;
        font-size: clamp(2rem, 10vw, 3rem);
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .homepage-hero {
        min-height: 50vh;
    }
    
    .enroll-link {
        padding: 0.6rem 1.2rem;
        letter-spacing: 0.05em;
    }
}
</style>
@endpush

@section('content')
@php
    $settings = App\Helpers\SettingsHelper::getSettings();
    $homepageTitle = $settings['homepage']['title'] ?? 'ENROLL NOW';
@endphp

<div class="container-fluid p-0">
    <div class="homepage-hero">
        <div class="hero-content">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <a href="{{ url('/enrollment') }}" class="enroll-link">
                        {{ $homepageTitle }}
                    </a>
                    <div class="hero-subtitle mt-3">
                        Start Your Journey with Excellence
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
