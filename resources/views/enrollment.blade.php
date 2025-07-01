@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<style>
    .program-card {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .program-card:hover {
        transform: translateY(-5px);
    }

    .enroll-btn:hover {
        background-color: #0f1a3a !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
        {{-- Complete Plan --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Plan</h3>
            <a href="{{ route('enrollment.full') }}" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; cursor: pointer; display: inline-block; text-align: center; text-decoration: none;">
                Enroll
            </a>
        </div>

        {{-- Modular Plan --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Plan</h3>
            <a href="{{ route('enrollment.modular') }}" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; cursor: pointer; display: inline-block; text-align: center; text-decoration: none;">
                Enroll
            </a>
        </div>
    </div>
</div>

@endsection
