@extends('layouts.navbar')

@section('title', 'Student Registration')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
<style>
    .step { display: none; }
    .step.active { display: block; }
</style>
@endpush

@section('content')
<form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form">
    @csrf

    {{-- STEP 1: ACCOUNT REGISTRATION --}}
    <div class="step active" id="step-1">
        <h2>ACCOUNT REGISTRATION</h2>
        <input type="text" name="user_firstname" placeholder="First Name" required>
        <input type="text" name="user_lastname" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <button type="button" onclick="nextStep()">Next</button>
    </div>

    {{-- STEP 2: FULL STUDENT REGISTRATION --}}
    <div class="step" id="step-2">
        <h2>STUDENT FULL PROGRAM REGISTRATION</h2>

        <h3>Student Information</h3>
        <div class="input-row">
            <input type="text" name="firstname" placeholder="First name" required>
            <input type="text" name="middle_name" placeholder="Middle name">
            <input type="text" name="lastname" placeholder="Last name" required>
            <input type="text" name="student_school" placeholder="Student's school" required>
        </div>

        <h3>Address</h3>
        <div class="input-row">
            <input type="text" name="street_address" placeholder="Street Address" required>
            <input type="text" name="state_province" placeholder="State/Province" required>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="zipcode" placeholder="Zip Code" required>
        </div>

        <h3>Contact Information</h3>
        <div class="input-row">
            <input type="text" name="contact_number" placeholder="Contact Number" required>
            <input type="text" name="emergency_contact_number" placeholder="Emergency Contact Number" required>
        </div>

        <h3>Verification/Document Upload</h3>
        <div class="document-buttons">
            <label>Good Moral <input type="file" name="good_moral" hidden></label>
            <label>PSA Birth Cert. <input type="file" name="PSA" hidden></label>
            <label>Course Cert. <input type="file" name="Course_Cert" hidden></label>
            <label>ToR <input type="file" name="TOR" hidden></label>
            <label>Cert. of Graduation <input type="file" name="Cert_of_Grad" hidden></label>
            <label>1x1 Photo <input type="file" name="photo_2x2" hidden></label>
        </div>

        <div class="education-options">
            <label><input type="radio" name="education" value="Undergraduate" checked> Undergraduate</label>
            <label><input type="radio" name="education" value="Graduate"> Graduate</label>
        </div>

        <h3>Start Date</h3>
        <div class="course-box">
            <input type="date" name="Start_Date" required>
        </div>
        <button type="button" onclick="prevStep()">Back</button>
        <button type="submit" class="enroll-btn">Enroll</button>
    </div>
</form>

@if(session('success'))
<div style="color: green;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="color: red;">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<script>
    function nextStep() {
        document.getElementById('step-1').classList.remove('active');
        document.getElementById('step-2').classList.add('active');
    }
    function prevStep() {
        document.getElementById('step-2').classList.remove('active');
        document.getElementById('step-1').classList.add('active');
    }

    // Debug: Log form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.registration-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitted!');
                // Remove the next line after debugging
                // alert('Form submitted!');
            });
        }
    });
</script>
@endsection
