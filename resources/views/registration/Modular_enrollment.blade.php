@extends('layouts.navbar')

@section('title', 'Student Registration')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Modular_Enrollment.css') }}">
<style>
    .step { display: none; }
    .step.active { display: block; }
</style>
@endpush

@section('content')
<form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form">
    @csrf
    <input type="hidden" name="enrollment_type" value="{{ request('enrollment_type', 'modular') }}">
    <input type="hidden" name="program_id"      value="{{ request('program_id') }}">
    <input type="hidden" name="package_id"      value="{{ request('package_id') }}">
    <input type="hidden" name="plan_id"         value="{{ request('plan_id') }}">

    {{-- STEP 1: ACCOUNT REGISTRATION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            ACCOUNT REGISTRATION
        </h2>
        <div style="display:flex; flex-direction:column; gap:18px; align-items:center;">
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="text" name="user_firstname" placeholder="First Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc;"
                       value="{{ old('user_firstname') }}">
                <input type="text" name="user_lastname" placeholder="Last Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc;"
                       value="{{ old('user_lastname') }}">
            </div>
            <input type="email" name="email" placeholder="Email" required
                   style="width:100%; max-width:500px; padding:12px 16px; border-radius:8px; border:1px solid #ccc;"
                   value="{{ old('email') }}">
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="password" name="password" placeholder="Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc;">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc;">
            </div>
            <div id="passwordError" style="display:none; color:#e74c3c; text-align:center; margin-bottom:12px; font-weight:600;"></div>
            <button type="button" onclick="nextStep()" id="nextBtn"
                    style="margin-top:10px; background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff;
                           border:none; border-radius:8px; padding:12px 40px; font-size:1.1rem; cursor:pointer;">
                Next
            </button>
        </div>
    </div>

    {{-- STEP 2: STUDENT MODULAR REGISTRATION --}}
    <div class="step" id="step-2">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            STUDENT MODULAR REGISTRATION FORM
        </h2>

        <h3>Student Information</h3>
        <div class="input-row">
            <input type="text" name="firstname" placeholder="First name" required value="{{ old('firstname') }}">
            <input type="text" name="middle_name" placeholder="Middle name" value="{{ old('middle_name') }}">
            <input type="text" name="lastname" placeholder="Last name" required value="{{ old('lastname') }}">
        </div>
        <input type="text" name="student_school" placeholder="Student's school" class="input-full" required value="{{ old('student_school') }}">

        <h3>Address</h3>
        <div class="input-row">
            <input type="text" name="street_address" placeholder="Street Address" required value="{{ old('street_address') }}">
            <input type="text" name="state_province" placeholder="State/Province" required value="{{ old('state_province') }}">
        </div>
        <div class="input-row">
            <input type="text" name="city" placeholder="City" required value="{{ old('city') }}">
            <input type="text" name="zipcode" placeholder="Zip Code" required value="{{ old('zipcode') }}">
        </div>

        <h3>Contact Information</h3>
        <div class="input-row">
            <input type="text" name="contact_number" placeholder="Contact Number" required value="{{ old('contact_number') }}">
            <input type="text" name="emergency_contact_number" placeholder="Emergency Contact Number" required value="{{ old('emergency_contact_number') }}">
        </div>

        <h3>Verification/Document Upload</h3>
        <div class="document-buttons">
            <label>Good Moral <input type="file" name="good_moral" hidden></label>
            <label>PSA Birth Cert. <input type="file" name="birth_cert" hidden></label>
            <label>Course Cert. <input type="file" name="course_cert" hidden></label>
            <label>ToR <input type="file" name="tor" hidden></label>
            <label>Cert. of Graduation <input type="file" name="grad_cert" hidden></label>
            <label>1x1 Photo <input type="file" name="photo" hidden></label>
        </div>

        <div class="input-row" style="margin:16px 0;">
            <label><input type="radio" name="education" value="Undergraduate" checked> Undergraduate</label>
            <label><input type="radio" name="education" value="Graduate"> Graduate</label>
        </div>

        <h3>Course</h3>
        <div class="input-row">
            <select name="program_id" required>
                <option value="">Select Course</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}"
                        {{ old('program_id', $programId ?? '') == $program->program_id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <h3>Start Date</h3>
        <div class="course-box" style="margin-bottom:20px;">
            <input type="date" name="Start_Date" required value="{{ old('Start_Date') }}">
        </div>

        <div style="text-align:left; margin-bottom:24px;">
            <label>
                <input type="checkbox" id="termsCheckbox" required>
                I agree to the 
                <a href="#" id="showTerms" style="color:#1c2951; text-decoration:underline;">Terms and Conditions</a>
            </label>
        </div>

        <div style="display:flex; gap:16px; justify-content:center;">
            <button type="button" onclick="prevStep()" class="back-btn"
                    style="padding:12px 30px; border:none; border-radius:8px; background:#ccc;">
                Back
            </button>
            <button type="submit" id="enrollBtn" class="enroll-btn" disabled
                    style="padding:12px 30px; border:none; border-radius:8px; background:#1c2951; color:#fff;">
                Enroll
            </button>
        </div>
    </div>
</form>

{{-- Terms and Conditions Modal --}}
<div id="termsModal"
     style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh;
            background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
  <div style="background:#fff; padding:30px; border-radius:16px; max-width:600px; width:90%;">
    <h2>Terms and Conditions</h2>
    <div style="max-height:300px; overflow-y:auto; margin:20px 0;">
      <p>
        By registering, you agree that all information provided is accurate and truthful.
        Uploaded documents are for verification only.
      </p>
    </div>
    <button id="agreeBtn" type="button"
            style="background:#1c2951; color:#fff; border:none; border-radius:8px;
                   padding:10px 30px; cursor:pointer;">
      Agree and Continue
    </button>
  </div>
</div>

{{-- Success Modal --}}
@if(session('success'))
  <div id="successModal"
       style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh;
              background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:30px; border-radius:12px; max-width:400px; width:90%; text-align:center;">
      <h2>Registration Successful!</h2>
      <p>{{ session('success') }}</p>
      <button id="successOk" type="button"
             style="margin-top:20px; padding:10px 24px; border:none; border-radius:6px;
                    background:#1c2951; color:#fff; cursor:pointer;">
        Go to Homepage
      </button>
    </div>
  </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
  // ---- Step navigation ----
  function nextStep() {
    const pwd = document.querySelector('input[name="password"]');
    const cpw = document.querySelector('input[name="password_confirmation"]');
    const err = document.getElementById('passwordError');
    err.style.display = 'none';
    err.textContent   = '';

    if (pwd.value.length < 6) {
      err.textContent = 'Password must be at least 6 characters.';
      err.style.display = 'block';
      pwd.focus();
      return;
    }
    if (pwd.value !== cpw.value) {
      err.textContent = 'Passwords do not match.';
      err.style.display = 'block';
      cpw.focus();
      return;
    }
    document.getElementById('step-1').classList.remove('active');
    document.getElementById('step-2').classList.add('active');
  }
  function prevStep() {
    document.getElementById('step-2').classList.remove('active');
    document.getElementById('step-1').classList.add('active');
  }
  window.nextStep = nextStep;
  window.prevStep = prevStep;

  // ---- Terms & Conditions ----
  const showTerms     = document.getElementById('showTerms');
  const termsModal    = document.getElementById('termsModal');
  const agreeBtn      = document.getElementById('agreeBtn');
  const termsCheckbox = document.getElementById('termsCheckbox');
  const enrollBtn     = document.getElementById('enrollBtn');

  termsCheckbox.disabled = true;
  enrollBtn.disabled     = true;

  showTerms.addEventListener('click', function(e) {
    e.preventDefault();
    agreeBtn.disabled        = false;
    termsModal.style.display = 'flex';
  });

  agreeBtn.addEventListener('click', function(e) {
    e.preventDefault();
    termsModal.style.display   = 'none';
    termsCheckbox.disabled     = false;
    termsCheckbox.checked      = true;
    enrollBtn.disabled         = false;
  });

  window.addEventListener('click', function(e) {
    if (e.target === termsModal) {
      termsModal.style.display = 'none';
    }
  });

  // ---- Success Modal ----
  const successModal = document.getElementById('successModal');
  if (successModal) {
    successModal.style.display = 'flex';
    document.getElementById('successOk').addEventListener('click', function() {
      window.location.href = '{{ route("home") }}';
    });
    // optional: auto-redirect after 3s
    // setTimeout(() => window.location.href = '{{ route("home") }}', 3000);
  }
});
</script>
@endsection
