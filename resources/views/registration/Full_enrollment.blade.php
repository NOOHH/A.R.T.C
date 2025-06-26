@extends('layouts.navbar')

@section('title', 'Student Registration')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
@endpush

@section('content')
<form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form">
    @csrf
    <h2>STUDENT FULL PROGRAM <br> REGISTRATION FORM</h2>

    {{-- Show validation error --}}
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    {{-- Form fields --}}
    <h3>Student Information</h3>
    <div class="input-row">
        <input type="text" name="firstname" placeholder="First name" required>
        <input type="text" name="middlename" placeholder="Middle name">
        <input type="text" name="lastname" placeholder="Last name" required>
    </div>
    <input type="text" name="student_school" placeholder="Student's school" class="input-full" required>

    <h3>Address</h3>
    <div class="input-row">
        <input type="text" name="street_address" placeholder="Street Address" required>
        <input type="text" name="state_province" placeholder="State/Province" required>
    </div>
    <div class="input-row">
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="zipcode" placeholder="Zip Code" required>
    </div>

    <h3>Contact Information</h3>
    <div class="input-row">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
    </div>
    <input type="text" name="emergency_contact_number" placeholder="Emergency Contact Number" class="input-full" required>

    <h3>Verification/Document Upload</h3>
    <div class="document-buttons">
        <label>Good Moral <input type="file" name="good_moral" hidden></label>
        <label>PSA Birth Cert. <input type="file" name="birth_cert" hidden></label>
        <label>Course Cert. <input type="file" name="course_cert" hidden></label>
        @if(session('ocr_text'))
            <div class="ocr-result" style="margin-top:10px; background:#f3f3f3; padding:10px; border-radius:8px;">
                <strong>Extracted Text:</strong>
                <pre style="white-space:pre-wrap;">{{ session('ocr_text') }}</pre>
                <strong>Suggested Programs:</strong>
                <ul>
                    @foreach(session('ocr_suggestions', []) as $suggestion)
                        <li>{{ $suggestion }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <label>ToR <input type="file" name="tor" hidden></label>
        <label>Cert. of Graduation <input type="file" name="grad_cert" hidden></label>
        <label>1x1 Photo <input type="file" name="photo" hidden></label>
    </div>

    <div class="input-row">
        <label><input type="radio" name="education" value="Undergraduate" checked> Undergraduate</label>
        <label><input type="radio" name="education" value="Graduate"> Graduate</label>
    </div>

    <h3>Courses</h3>
    <div class="input-row">
        <select name="course_1" required>
            <option value="">Course 1</option>
            <option value="Nursing">Nursing</option>
            <option value="Engineering">Engineering</option>
        </select>
    </div>

    <h3>Start Date</h3>
    <div class="course-box">
        <label for="start_date">
            <input type="date" name="start_date" id="start_date" required>
        </label>
    </div>

    <button type="submit" class="enroll-btn">Enroll</button>
    @if(session('success'))
        <div style="color: green; margin-top: 1em;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="color: red; margin-top: 1em;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>
@endsection
