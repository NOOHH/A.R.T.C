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
    <input type="hidden" name="enrollment_type" value="{{ old('enrollment_type', $enrollmentType ?? '') }}">
    <input type="hidden" name="program_id"      value="{{ old('program_id', $programId ?? '') }}">
    <input type="hidden" name="package_id"      value="{{ old('package_id', $packageId ?? '') }}">
    <input type="hidden" name="plan_id"         value="{{ request('plan_id') }}">

    {{-- STEP 1: ACCOUNT REGISTRATION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            ACCOUNT REGISTRATION
        </h2>
        <div style="display:flex; flex-direction:column; gap:18px; align-items:center;">
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="text" name="user_firstname" placeholder="First Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="text" name="user_lastname" placeholder="Last Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <input type="email" name="email" placeholder="Email" required
                   style="width:100%; max-width:500px; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="password" name="password" placeholder="Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <button type="button" onclick="nextStep()"
                    style="margin-top:10px; background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff;
                           border:none; border-radius:8px; padding:12px 40px; font-size:1.1rem; font-weight:600;
                           box-shadow:0 2px 8px rgba(160,89,198,0.08); cursor:pointer;">
                Next
            </button>
        </div>
    </div>

    {{-- STEP 2: FULL STUDENT REGISTRATION --}}
    <div class="step" id="step-2">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            STUDENT FULL PROGRAM REGISTRATION
        </h2>

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

        <div class="education-options" style="margin:16px 0;">
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
            <input type="date" name="Start_Date" required>
        </div>

        <div style="text-align:left; margin-bottom:24px;">
            <label>
                <input type="checkbox" id="termsCheckbox" required>
                I agree to the 
                <a href="#" id="showTerms" style="color:#1c2951; text-decoration:underline;">
                  Terms and Conditions
                </a>
            </label>
        </div>

        <div style="display:flex; gap:16px; justify-content:center;">
            <button type="button" onclick="prevStep()"
                    style="padding:12px 30px; border:none; border-radius:8px; background:#ccc;">
                Back
            </button>
            <button type="submit" class="enroll-btn" id="enrollBtn" disabled
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
        By registering, you agree to abide by the rules and regulations of the review center.
        You consent to the processing of your personal data for enrollment and communication.
        All fees paid are non-refundable once the review program has started.
      </p>
    </div>
    <button id="agreeBtn" type="button"
            style="background:#1c2951; color:#fff; border:none; border-radius:8px;
                   padding:10px 30px; font-size:1rem; cursor:pointer;">
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
  const successOk    = document.getElementById('successOk');
  if (successModal) {
    successModal.style.display = 'flex';
    successOk.addEventListener('click', function() {
      window.location.href = '{{ route("home") }}';
    });
    // optional: auto-redirect after 3s
    // setTimeout(() => window.location.href = '{{ route("home") }}', 3000);
  }
});
</script>
@endsection
