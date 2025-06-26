@extends('layouts.navbar')

@section('title', 'Home')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
        {{-- Complete Program --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Program</h3>
            <a href="{{ route('enrollment.full') }}" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; text-decoration: none;">
                Enroll
            </a>
        </div>

        {{-- Modular Enrollment --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Enrollment</h3>
            <a href="{{ route('enrollment.modular') }}" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; text-decoration: none;">
                Enroll
            </a>
        </div>
    </div>

    <div class="select-container" style="margin-top: 0;">
        <select style="padding: 12px 30px; border-radius: 20px; font-size: 1rem; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <option selected disabled>Please select your course</option>
            <option value="1">Nursing</option>
            <option value="2">Engineering</option>
            <option value="3">Accountancy</option>
        </select>
    </div>
</div>
@endsection
