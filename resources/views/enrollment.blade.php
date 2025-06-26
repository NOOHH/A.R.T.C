@extends('layouts.navbar') {{-- uses layouts/app.blade.php --}}

@section('title', 'Home') {{-- optional for dynamic title --}}

@section('content') {{-- this will be injected into @yield('content') in your layout --}}
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Program</h3>
            <button class="enroll-button" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem;">Enroll</button>
        </div>
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Enrollment</h3>
            <button class="enroll-button" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem;">Enroll</button>
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
