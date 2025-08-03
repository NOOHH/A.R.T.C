@extends('layouts.navbar')

@section('title', 'Modular Enrollment - Multi-Step Form')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@php
    // Check if user is already logged in
    $isUserLoggedIn = auth()->check() || session('user_id');
    $loggedInUser = auth()->check() ? auth()->user() : (session('user_id') ? \App\Models\User::find(session('user_id')) : null);
@endphp

@push('styles')
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Modular_enrollment.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .step-indicator {
            display: flex;
            margin-bottom: 30px;
            justify-content: space-between;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            position: relative;
        }
        .step.active {
            font-weight: bold;
            color: #007bff;
        }
        .step::after {
            content: '';
            position: absolute;
            height: 2px;
            background-color: #ddd;
            top: 50%;
            left: 50%;
            width: 100%;
            z-index: -1;
        }
        .step:last-child::after {
            display: none;
        }
    </style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">Modular Enrollment</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Welcome to Modular Enrollment!</strong>
                <p>You can now enroll in specific modules based on your educational needs and interests.</p>
            </div>

            <div class="step-indicator">
                <div class="step {{ !$isUserLoggedIn ? 'active' : '' }}">Account</div>
                <div class="step {{ $isUserLoggedIn ? 'active' : '' }}">Program Selection</div>
                <div class="step">Module Selection</div>
                <div class="step">Learning Mode</div>
                <div class="step">Complete Registration</div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <h3>Available Programs</h3>
            <div class="row">
                @if(isset($programs) && count($programs) > 0)
                    @foreach($programs as $program)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $program['program_name'] }}</h5>
                                    <p class="card-text">{{ $program['program_description'] }}</p>
                                    <button type="button" class="btn btn-primary" 
                                            onclick="selectProgram({{ $program['program_id'] }})">
                                        Select Program
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-warning">
                            No programs available for modular enrollment at this time.
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('enrollment.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Enrollment Options
                </a>
                
                @if(isset($programs) && count($programs) > 0)
                    <button type="button" class="btn btn-primary float-end" id="nextButton" disabled>
                        Continue <i class="bi bi-arrow-right"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple program selection functionality
    let selectedProgramId = null;
    
    function selectProgram(programId) {
        selectedProgramId = programId;
        
        // Remove active class from all program cards
        document.querySelectorAll('.card').forEach(card => {
            card.classList.remove('border-primary');
        });
        
        // Add active class to selected program card
        const selectedCard = event.target.closest('.card');
        if (selectedCard) {
            selectedCard.classList.add('border-primary');
        }
        
        // Enable next button
        document.getElementById('nextButton').disabled = false;
    }
    
    // Document ready function
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Modular enrollment page loaded');
    });
</script>
@endpush
