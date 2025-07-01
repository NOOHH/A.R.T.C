@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<style>
{!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
{!! App\Helpers\SettingsHelper::getProgramCardStyles() !!}
{!! App\Helpers\SettingsHelper::getButtonStyles() !!}

    .program-card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s, border-color 0.3s, box-shadow 0.3s;
        border-width: 2px;
        border-style: solid;
    }

    .program-card:hover {
        transform: translateY(-5px);
    }

    .enroll-btn {
        transition: background-color 0.3s, transform 0.2s;
        border: none;
        cursor: pointer;
    }

    .enroll-btn:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
        {{-- Complete Plan --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Plan</h3>
            <button onclick="window.location.href='{{ route('enrollment.full') }}'" class="enroll-btn btn-primary" style="border-radius: 20px; padding: 10px 40px; font-size: 1rem;">
                Enroll
            </button>
        </div>

        {{-- Modular Plan --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Plan</h3>
            <button onclick="window.location.href='{{ route('enrollment.modular') }}'" class="enroll-btn btn-primary" style="border-radius: 20px; padding: 10px 40px; font-size: 1rem;">
                Enroll
            </button>
        </div>
    </div>
</div>

@endsection
