@extends('layouts.app') {{-- uses layouts/app.blade.php --}}

@section('title', 'Home') {{-- optional for dynamic title --}}

@section('content') {{-- this will be injected into @yield('content') in your layout --}}
    <div class="program-container">
        <div class="program-card">
            <h3>Complete Program</h3>
            <button class="enroll-button">Enroll</button>
        </div>
        <div class="program-card">
            <h3>Modular Enrollment</h3>
            <button class="enroll-button">Enroll</button>
        </div>
    </div>

    <div class="select-container">
        <select>
            <option selected disabled>Please select your course</option>
            <option value="1">Nursing</option>
            <option value="2">Engineering</option>
            <option value="3">Accountancy</option>
        </select>
    </div>
@endsection
