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
    <input type="hidden" name="enrollment_type" value="{{ request('enrollment_type', 'modular') }}">
    <input type="hidden" name="program_id" value="{{ request('program_id') }}">
    <input type="hidden" name="package_id" value="{{ request('package_id') }}">

    @if(session('success'))
        <div class="alert alert-success" style="background:#e6ffe6; color:#218838; border-radius:8px; padding:14px 24px; margin:18px auto; max-width:500px; text-align:center; font-weight:600; font-size:1.1rem; box-shadow:0 2px 8px rgba(33,136,56,0.08);">
            {{ session('success') }}
        </div>
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

    {{-- STEP 1: ACCOUNT REGISTRATION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom: 24px; font-weight:700; letter-spacing:1px;">ACCOUNT REGISTRATION</h2>
        <div style="display: flex; flex-direction: column; gap: 18px; align-items: center;">
            <div style="display: flex; gap: 16px; width: 100%; max-width: 500px;">
                <input type="text" name="user_firstname" placeholder="First Name" required style="flex:1; padding: 12px 16px; border-radius: 8px; border: 1px solid #ccc;">
                <input type="text" name="user_lastname" placeholder="Last Name" required style="flex:1; padding: 12px 16px; border-radius: 8px; border: 1px solid #ccc;">
            </div>
            <input type="email" name="user_email" placeholder="Email" required value="{{ old('user_email') }}" style="width: 100%; max-width: 500px; padding: 12px 16px; border-radius: 8px; border: 1px solid #ccc;">
            <div style="display: flex; gap: 16px; width: 100%; max-width: 500px;">
                <input type="password" name="password" placeholder="Password" required style="flex:1; padding: 12px 16px; border-radius: 8px; border: 1px solid #ccc;">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required style="flex:1; padding: 12px 16px; border-radius: 8px; border: 1px solid #ccc;">
            </div>
            <button type="button" onclick="nextStep()" style="margin-top: 10px; background: linear-gradient(90deg, #a259c6, #6a82fb); color: #fff; border: none; border-radius: 8px; padding: 12px 40px;">Next</button>
        </div>
    </div>

    {{-- STEP 2: STUDENT MODULAR REGISTRATION --}}
    <div class="step" id="step-2">
        <h2 style="text-align:center;">STUDENT MODULAR REGISTRATION FORM</h2>

        <h3>Student Information</h3>
        <div class="input-row">
            <input type="text" name="firstname" placeholder="First name" required>
            <input type="text" name="middle_name" placeholder="Middle name">
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
            <input type="email" name="email" placeholder="Email" required value="{{ old('user_email') }}">
            <input type="text" name="contact_number" placeholder="Contact Number" required>
        </div>
        <input type="text" name="emergency_contact_number" placeholder="Emergency Contact Number" class="input-full" required>

        <h3>Verification/Document Upload</h3>
        <div class="document-buttons">
            <label>Good Moral <input type="file" name="good_moral" hidden></label>
            <label>PSA Birth Cert. <input type="file" name="birth_cert" hidden></label>
            <label>Course Cert. <input type="file" name="course_cert" hidden></label>
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
                <option value="">Select Course</option>
                <option value="Nursing">Nursing</option>
                <option value="Engineering">Engineering</option>
            </select>
        </div>

        <h3>Start Date</h3>
        <div class="course-box">
            <input type="date" name="Start_Date" required>
        </div>

        <div class="terms">
            <label>
                <input type="checkbox" id="termsCheckbox" name="terms" required disabled>
                I agree to the <a href="javascript:void(0)" onclick="openModal()">Terms and Conditions</a>
            </label>
        </div>

        <button type="button" onclick="prevStep()">Back</button>
        <button type="submit" class="enroll-btn" id="enrollBtn" disabled>Enroll</button>
    </div>
</form>

{{-- Modal --}}
<div id="termsModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:30px; border-radius:16px; max-width:600px; margin:auto; position:relative; top:10vh;">
        <h2>Terms and Conditions</h2>
        <p>By registering, you agree that all information provided is accurate. Uploaded documents are for verification only.</p>
        <label><input type="checkbox" id="agreeInModal"> I agree</label>
        <button onclick="closeModal()" id="agreeBtn" disabled style="margin-top:20px;">Agree and Close</button>
    </div>
</div>

<script>
    function nextStep() {
        document.getElementById('step-1').classList.remove('active');
        document.getElementById('step-2').classList.add('active');
    }
    function prevStep() {
        document.getElementById('step-2').classList.remove('active');
        document.getElementById('step-1').classList.add('active');
    }

    const modal = document.getElementById('termsModal');
    const agreeInModal = document.getElementById('agreeInModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const termsCheckbox = document.getElementById('termsCheckbox');
    const enrollBtn = document.getElementById('enrollBtn');

    function openModal() {
        modal.style.display = 'flex';
        agreeInModal.checked = false;
        agreeBtn.disabled = true;
    }

    function closeModal() {
        modal.style.display = 'none';
        if (agreeInModal.checked) {
            termsCheckbox.checked = true;
            termsCheckbox.disabled = false;
            enrollBtn.disabled = false;
        }
    }

    agreeInModal.addEventListener('change', () => {
        agreeBtn.disabled = !agreeInModal.checked;
    });

    termsCheckbox.addEventListener('change', function () {
        enrollBtn.disabled = !this.checked;
    });

    window.onclick = function (event) {
        if (event.target === modal) {
            closeModal();
        }
    };
</script>
@endsection
