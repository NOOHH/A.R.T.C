@extends('layouts.navbar')

@section('title', 'Enrollment')

@push('styles')
<style>
{!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
{!! App\Helpers\SettingsHelper::getProgramCardStyles() !!}
{!! App\Helpers\SettingsHelper::getButtonStyles() !!}

/* Enrollment Page Layout */
.enrollment-page-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 70vh;
    padding: 40px 20px;
}

.enrollment-cards-container {
    display: flex;
    gap: 60px;
    margin-bottom: 40px;
    flex-wrap: wrap;
    justify-content: center;
}

/* Program Card Enhancements */
.enrollment-program-card {
    width: 350px;
    height: 260px;
    border-radius: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, border-color 0.3s, box-shadow 0.3s;
    border-width: 2px;
    border-style: solid;
    position: relative;
    overflow: hidden;
}

.enrollment-program-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.enrollment-program-card h3 {
    font-size: 2rem;
    font-weight: 500;
    margin-bottom: 24px;
    text-align: center;
}

/* Enrollment Button Styling */
.enroll-btn {
    border-radius: 20px;
    padding: 12px 40px;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

.enroll-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.enroll-btn:active {
    transform: translateY(0);
}

/* Responsive Design */
@media (max-width: 768px) {
    .enrollment-cards-container {
        flex-direction: column;
        gap: 30px;
        align-items: center;
    }
    
    .enrollment-program-card {
        width: 300px;
        height: 220px;
    }
    
    .enrollment-program-card h3 {
        font-size: 1.5rem;
    }
    
    .enroll-btn {
        padding: 10px 30px;
        font-size: 0.9rem;
    }
}
</style>
@endpush

@section('content')
<div class="enrollment-page-content">
    <div class="enrollment-cards-container">
        {{-- Complete Plan --}}
        <div class="program-card enrollment-program-card">
            <h3>Complete Plan</h3>
            <button onclick="window.location.href='{{ route('enrollment.full') }}'" class="enroll-btn enrollment-btn">
                Enroll
            </button>
        </div>

        {{-- Modular Plan --}}
        <div class="program-card enrollment-program-card">
            <h3>Modular Plan</h3>
            <button onclick="window.location.href='{{ route('enrollment.modular') }}'" class="enroll-btn enrollment-btn">
                Enroll
            </button>
        </div>
    </div>
</div>

@endsection
