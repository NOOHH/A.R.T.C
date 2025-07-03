@extends('layouts.navbar')

@section('title', 'Student Registration')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Modular_Enrollment.css') }}">
<style>
{!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
{!! App\Helpers\SettingsHelper::getButtonStyles() !!}

  /* STEP TRANSITIONS */
  .step {
    display: none;
    opacity: 0;
    transform: translateX(50px);
    transition: all 0.5s ease-in-out;
  }
  .step.active {
    display: block;
    opacity: 1;
    transform: translateX(0);
    animation: slideIn 0.5s // Function to handle login with package selection
  }
  .step.slide-out-left {
    transform: translateX(-50px);
    opacity: 0;
  }
  .step.slide-out-right {
    transform: translateX(50px);
    opacity: 0;
  }
  @keyframes slideIn {
    from { opacity: 0; transform: translateX(50px); }
    to   { opacity: 1; transform: translateX(0); }
  }

  /* PACKAGE CARDS */
  .package-card {
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 20px;
    width: 320px;
    height: 400px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 3px solid transparent;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    flex-shrink: 0;
    transform-origin: top center;
    z-index: 1;
  }
  .package-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    border-color: #1c2951;
    z-index: 10;
  }
  .package-card.selected {
    border-color: #1c2951;
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(28, 41, 81, 0.5);
  }

  /* CAROUSEL LAYOUT */
  .package-carousel {
    position: relative;
    overflow: hidden;
    padding: 50px 20px 30px 20px;
    margin-bottom: 30px;
  }
  .package-slider {
    display: flex;
    gap: 20px;
    overflow: hidden;
    width: 100%;
    max-width: 780px;
    margin: 0 auto;
  }
  .package-slider-track {
    display: flex;
    gap: 20px;
    width: max-content;
    margin: 0 auto;
    transition: transform 0.3s ease;
  }

  /* NAVIGATION ARROWS */
  .carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    width: 50px;
    height: 50px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  #prevBtn { left: 0; }
  #nextBtn { right: 0; }
  .carousel-arrow:hover {
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }
  .carousel-arrow:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* CARD CONTENT */
  .package-image {
    width: 100%;
    height: 60%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
  }
  .package-content {
    padding: 20px;
    height: 40%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #1a1a1a;
  }
  .package-title {
    color: #fff;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 8px;
    text-align: center;
  }
  .package-description {
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.4;
    text-align: center;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 4em;
  }
  .package-price {
    font-size: 1.4rem;
    font-weight: 800;
    text-align: center;
    margin: 0;
    background: linear-gradient(90deg, #a259c6, #6a82fb);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .package-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #1c2951;
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  /* PAYMENT METHODS (unchanged) */
  .payment-method {
    background: #f9f9f9;
    border: 2px solid #ddd;
    border-radius: 12px;
    padding: 20px;
    margin: 10px 0;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
  }
  .payment-method:hover {
    border-color: #1c2951;
    background: #e0f3ff;
  }
  .payment-method.selected {
    border-color: #1c2951;
    background: #e0f3ff;
  }
  .payment-icon {
    width: 50px;
    height: 50px;
    background: #1c2951;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
  }

  /* RESPONSIVE */
  @media (max-width: 768px) {
    .package-slider {
      width: 340px !important;
    }
    .package-card {
      width: 300px;
    }
    .carousel-arrow {
      width: 40px;
      height: 40px;
      font-size: 1.2rem;
    }
  }
  @media (max-width: 480px) {
    .package-carousel {
      flex-direction: column;
      padding: 0;
    }
    .carousel-arrow {
      position: static;
      margin: 20px auto;
    }
    .package-slider {
      width: 280px !important;
    }
    .package-card {
      width: 260px;
      height: 350px;
    }
  }
</style>
@endpush

@section('content')
<form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form">
    @csrf
    <input type="hidden" name="enrollment_type" value="modular">
    <input type="hidden" name="program_id" value="2">
    <input type="hidden" name="package_id" value="">
    <input type="hidden" name="plan_id" value="2">

    {{-- STEP 1: PACKAGE SELECTION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            SELECT YOUR PACKAGE
        </h2>
        <div class="package-carousel">
            <button class="carousel-arrow" id="prevBtn" onclick="slidePackages(-1)">‹</button>
            <div class="package-slider">
                <div class="package-slider-track" id="packageTrack">
                    @foreach($packages as $package)
                        <div class="package-card" onclick="selectPackage('{{ $package->package_id }}', '{{ $package->package_name }}')" data-package-id="{{ $package->package_id }}">
                            <div class="package-image">
                                📦
                            </div>
                            <div class="package-content">
                                <h4 class="package-title">{{ $package->package_name }}</h4>
                                <p class="package-description" title="{{ $package->description ?? 'Complete package with all features included.' }}">{{ $package->description ?? 'Complete package with all features included.' }}</p>
                                <p class="package-price">₱{{ number_format($package->price, 2) }}</p>
                            </div>
                            <div class="package-badge">Popular</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <button class="carousel-arrow" id="nextBtn" onclick="slidePackages(1)">›</button>
        </div>
        <div style="text-align: center;">
            <div id="selectedPackageDisplay" style="display: none; margin-bottom: 20px; color: #1c2951; font-weight: 600;">
                Selected Package: <span id="selectedPackageName"></span>
            </div>
            <button type="button" onclick="nextStep()" id="packageNextBtn" disabled
                    style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff; border:none; 
                           border-radius:8px; padding:12px 40px; font-size:1.1rem; font-weight:600;
                           box-shadow:0 2px 8px rgba(160,89,198,0.08); cursor:pointer; opacity: 0.5;">
                Next
            </button>
        </div>
    </div>

    {{-- STEP 2: ACCOUNT REGISTRATION --}}
    <div class="step" id="step-2">
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
            
            <div style="text-align:center; margin-bottom:15px; font-size:0.9rem; color:#666;">
                Already have an account? 
                <a href="#" onclick="loginWithPackage()" style="color: #1c2951; text-decoration: underline; font-weight: 600;">
                    Click here to login
                </a>
            </div>
            
            <div style="display:flex; gap:16px; justify-content:center;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="nextBtn"
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff;
                               border:none; border-radius:8px; padding:12px 40px; font-size:1.1rem; cursor:pointer;">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 3: PAYMENT INFORMATION --}}
    <div class="step" id="step-3">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            PAYMENT INFORMATION
        </h2>
        
        <div style="max-width: 600px; margin: 0 auto;">
            <h3 style="margin-bottom: 20px;">Choose Payment Method</h3>
            
            <div class="payment-method" onclick="selectPaymentMethod('credit_card')">
                <div class="payment-icon">💳</div>
                <div>
                    <h4 style="margin: 0 0 5px 0;">Credit/Debit Card</h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">Pay securely with your credit or debit card</p>
                </div>
            </div>
            
            <div class="payment-method" onclick="selectPaymentMethod('gcash')">
                <div class="payment-icon">📱</div>
                <div>
                    <h4 style="margin: 0 0 5px 0;">GCash</h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">Pay using your GCash mobile wallet</p>
                </div>
            </div>
            
            <div class="payment-method" onclick="selectPaymentMethod('bank_transfer')">
                <div class="payment-icon">🏦</div>
                <div>
                    <h4 style="margin: 0 0 5px 0;">Bank Transfer</h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">Transfer payment directly to our bank account</p>
                </div>
            </div>
            
            <div class="payment-method" onclick="selectPaymentMethod('installment')">
                <div class="payment-icon">📅</div>
                <div>
                    <h4 style="margin: 0 0 5px 0;">Installment Plan</h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">Pay in monthly installments</p>
                </div>
            </div>
            
            <div id="selectedPaymentDisplay" style="display: none; margin: 20px 0; padding: 15px; background: #e8f5e8; border-radius: 8px; text-align: center;">
                <strong>Selected Payment Method: <span id="selectedPaymentName"></span></strong>
            </div>
            
            <div style="display:flex; gap:16px; justify-content:center; margin-top: 30px;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="paymentNextBtn" disabled
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff; border:none; 
                               border-radius:8px; padding:12px 40px; font-size:1.1rem; cursor:pointer; opacity: 0.5;">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 4: STUDENT MODULAR REGISTRATION --}}
    <div class="step" id="step-4">
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

        <h3>Program</h3>
        <div class="input-row">
            <select name="program_id" required>
                <option value="">Select Program</option>
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

{{-- Success Modal - Only show for registration completion messages --}}
@if(session('success') && str_contains(session('success'), 'registration'))
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

{{-- Login Success Modal - Shows welcome back message when returning from login --}}
@if(session('success') && str_contains(session('success'), 'Welcome back'))
  <div id="loginSuccessModal" 
       style="position:fixed; top:20px; right:20px; background:#fff; padding:15px 20px; 
              border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000; 
              max-width:300px; animation: slideIn 0.5s ease-out, fadeOut 0.5s ease-out 5s forwards;">
    <p style="margin:0; color:#333;"><strong>{{ session('success') }}</strong></p>
  </div>
  <style>
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; visibility: hidden; }
    }
  </style>
@endif

<script>
// Global variables (declared once at the top)
let currentStep = 1;
let selectedPackageId = null;
let selectedPaymentMethod = null;
let currentPackageIndex = 0;
let packagesPerView = 2;
let totalPackages = <?php echo isset($packages) && is_countable($packages) ? (int)count($packages) : 0; ?>;

// Check if user is logged in (set from server)
const isUserLoggedIn = @if(session('user_id')) true @else false @endif;
const loggedInUserName = '@if(session("user_name")){{ session("user_name") }}@endif';
const loggedInUserFirstname = '@if(session("user_firstname")){{ session("user_firstname") }}@endif';
const loggedInUserLastname = '@if(session("user_lastname")){{ session("user_lastname") }}@endif';
const loggedInUserEmail = '@if(session("user_email")){{ session("user_email") }}@endif';

// Package carousel functionality
function slidePackages(direction) {
    const track = document.getElementById('packageTrack');
    if (!track) return;
    
    const packageWidth = 320; // package card width
    const gap = 20; // gap between cards
    const moveDistance = packageWidth + gap;
    
    currentPackageIndex += direction;
    
    // Boundary checks
    if (currentPackageIndex < 0) {
        currentPackageIndex = 0;
    } else if (currentPackageIndex > totalPackages - packagesPerView) {
        currentPackageIndex = Math.max(0, totalPackages - packagesPerView);
    }
    
    const translateX = -currentPackageIndex * moveDistance;
    track.style.transform = `translateX(${translateX}px)`;
    
    // Update arrow states
    updateArrowStates();
}

function updateArrowStates() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn && nextBtn) {
        prevBtn.disabled = currentPackageIndex === 0;
        nextBtn.disabled = currentPackageIndex >= totalPackages - packagesPerView;
    }
}

// Function to update the progress bar based on current step
function updateProgress(step) {
    const totalSteps = 4; // Total number of steps in the form
    const progress = document.querySelector('.progress-bar');
    
    if (progress) {
        const percentage = Math.round((step / totalSteps) * 100);
        progress.style.width = percentage + '%';
        progress.setAttribute('aria-valuenow', percentage);
    }
}

// Step navigation with animations
function nextStep() {
    if (currentStep === 1) {
        // Check if user is logged in - skip account registration step
        if (isUserLoggedIn) {
            // Skip step 2 and go directly to step 3 (payment)
            animateStepTransition('step-1', 'step-3');
            currentStep = 3;
        } else {
            // User not logged in, go to account registration
            animateStepTransition('step-1', 'step-2');
            currentStep = 2;
        }
    } else if (currentStep === 2) {
        // Copy Account Registration data to Student Registration before moving to step 3
        copyAccountDataToStudentForm();
        animateStepTransition('step-2', 'step-3');
        currentStep = 3;
    } else if (currentStep === 3) {
        animateStepTransition('step-3', 'step-4');
        currentStep = 4;
        // Auto-fill user data if logged in
        fillLoggedInUserData();
        // Also auto-fill in case user comes directly to step 4
        copyAccountDataToStudentForm();
    }
    
    // Update progress bar
    updateProgress(currentStep);
}

function prevStep() {
    if (currentStep === 4) {
        animateStepTransition('step-4', 'step-3', true);
        currentStep = 3;
    } else if (currentStep === 3) {
        // Check if user is logged in - skip back to step 1 if logged in
        if (isUserLoggedIn) {
            // Skip step 2 and go back to step 1
            animateStepTransition('step-3', 'step-1', true);
            currentStep = 1;
        } else {
            // User not logged in, go back to account registration
            animateStepTransition('step-3', 'step-2', true);
            currentStep = 2;
        }
    } else if (currentStep === 2) {
        animateStepTransition('step-2', 'step-1', true);
        currentStep = 1;
    }
    
    // Update progress bar
    updateProgress(currentStep);
}

function animateStepTransition(fromStepId, toStepId, isBack = false) {
    const fromStep = document.getElementById(fromStepId);
    const toStep = document.getElementById(toStepId);
    
    // Add slide-out class to current step
    fromStep.classList.add(isBack ? 'slide-out-right' : 'slide-out-left');
    
    setTimeout(() => {
        fromStep.classList.remove('active', 'slide-out-left', 'slide-out-right');
        toStep.classList.add('active');
    }, 250);
}

// Package Selection
function selectPackage(packageId, packageName) {
    // Remove selection from all package cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Highlight selected package
    event.target.closest('.package-card').classList.add('selected');
    
    // Store selection
    selectedPackageId = packageId;
    
    // Update hidden input
    const packageInput = document.querySelector('input[name="package_id"]');
    if (packageInput) {
        packageInput.value = packageId;
    } else {
        // Create hidden input if it doesn't exist
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'package_id';
        hiddenInput.value = packageId;
        document.querySelector('form').appendChild(hiddenInput);
    }
    
    // Show selected package display
    document.getElementById('selectedPackageName').textContent = packageName;
    document.getElementById('selectedPackageDisplay').style.display = 'block';
    
    // Enable next button
    const nextBtn = document.getElementById('packageNextBtn');
    nextBtn.disabled = false;
    nextBtn.style.opacity = '1';
}

// Payment Method Selection
function selectPaymentMethod(method) {
    // Remove selection from all payment methods
    document.querySelectorAll('.payment-method').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Highlight selected payment method
    event.target.closest('.payment-method').classList.add('selected');
    
    // Store selection
    selectedPaymentMethod = method;
    
    // Update display
    const methodNames = {
        'credit_card': 'Credit/Debit Card',
        'gcash': 'GCash',
        'bank_transfer': 'Bank Transfer',
        'installment': 'Installment Plan'
    };
    
    document.getElementById('selectedPaymentName').textContent = methodNames[method];
    document.getElementById('selectedPaymentDisplay').style.display = 'block';
    
    // Enable next button
    const nextBtn = document.getElementById('paymentNextBtn');
    nextBtn.disabled = false;
    nextBtn.style.opacity = '1';
}

// Function to fill logged-in user data
function fillLoggedInUserData() {
    if (isUserLoggedIn) {
        console.log('Filling logged-in user data...');
        
        // Auto-fill Step 4 (Modular Student Registration) fields with logged-in user data
        const firstnameField = document.getElementById('firstname');
        const lastnameField = document.getElementById('lastname');
        
        // Use session data if available
        if (firstnameField && loggedInUserFirstname) {
            firstnameField.value = loggedInUserFirstname;
            console.log('Auto-filled firstname from session:', loggedInUserFirstname);
        }
        if (lastnameField && loggedInUserLastname) {
            lastnameField.value = loggedInUserLastname;
            console.log('Auto-filled lastname from session:', loggedInUserLastname);
        }
        
        // Also auto-fill Step 2 (Account Registration) fields if user navigates back
        const userFirstnameField = document.getElementById('user_firstname');
        const userLastnameField = document.getElementById('user_lastname');
        const userEmailField = document.getElementById('user_email');
        
        if (userFirstnameField && loggedInUserFirstname) {
            userFirstnameField.value = loggedInUserFirstname;
        }
        if (userLastnameField && loggedInUserLastname) {
            userLastnameField.value = loggedInUserLastname;
        }
        if (userEmailField && loggedInUserEmail) {
            userEmailField.value = loggedInUserEmail;
        }
        
        console.log('Auto-filled student registration fields with logged-in user data');
    }
}

// Function to copy Account Registration data to Modular Student Registration
function copyAccountDataToStudentForm() {
    // Get values from Step 2 (Account Registration)
    const userFirstname = document.getElementById('user_firstname')?.value || '';
    const userLastname = document.getElementById('user_lastname')?.value || '';
    const userEmail = document.getElementById('user_email')?.value || '';
    
    // Set values in Step 4 (Modular Student Registration)
    const firstnameField = document.getElementById('firstname');
    const lastnameField = document.getElementById('lastname');
    
    // Only auto-fill if the fields are empty (to avoid overwriting user input)
    if (firstnameField && userFirstname && !firstnameField.value) {
        firstnameField.value = userFirstname;
        console.log('Auto-filled firstname:', userFirstname);
    }
    if (lastnameField && userLastname && !lastnameField.value) {
        lastnameField.value = userLastname;
        console.log('Auto-filled lastname:', userLastname);
    }
    
    console.log('Account data copied to student form');
}

// Function to handle login with package selection
function loginWithPackage() {
    // Store the current package selection if any
    if (selectedPackageId) {
        sessionStorage.setItem('selectedPackageId', selectedPackageId);
        sessionStorage.setItem('selectedPackageName', document.getElementById('selectedPackageName').textContent);
    }
    
    // Store that we're coming from enrollment and should skip to payment
    sessionStorage.setItem('continueEnrollment', 'true');
    sessionStorage.setItem('skipToPayment', 'true');
    
    // Redirect to login with enrollment flag
    window.location.href = '{{ route("login") }}?from_enrollment=true';
}

// Make functions globally accessible
window.slidePackages = slidePackages;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectPackage = selectPackage;
window.selectPaymentMethod = selectPaymentMethod;
// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
    // Hide Step 2 if user is logged in
    if (isUserLoggedIn) {
        const step2 = document.getElementById('step-2');
        if (step2) {
            step2.style.display = 'none';
        }
        console.log('User is logged in - Step 2 (Account Registration) hidden');
    }
    
    // Check if we're returning from login with a package selection
    const continueEnrollment = sessionStorage.getItem('continueEnrollment');
    const skipToPayment = sessionStorage.getItem('skipToPayment');
    const savedPackageId = sessionStorage.getItem('selectedPackageId');
    const savedPackageName = sessionStorage.getItem('selectedPackageName');
    
    if (continueEnrollment === 'true' && savedPackageId && savedPackageName) {
        // Clear the session flags
        sessionStorage.removeItem('continueEnrollment');
        sessionStorage.removeItem('skipToPayment');
        
        // Auto-select the saved package
        selectedPackageId = savedPackageId;
        
        // Find and highlight the package card
        const packageCard = document.querySelector(`[data-package-id="${savedPackageId}"]`);
        if (packageCard) {
            packageCard.classList.add('selected');
        }
        
        // Update the form
        const packageInput = document.querySelector('input[name="package_id"]');
        if (packageInput) {
            packageInput.value = savedPackageId;
        }
        
        // Show selected package display
        document.getElementById('selectedPackageName').textContent = savedPackageName;
        document.getElementById('selectedPackageDisplay').style.display = 'block';
        
        // Enable next button
        const nextBtn = document.getElementById('packageNextBtn');
        nextBtn.disabled = false;
        
        // Skip to payment if needed
        if (skipToPayment === 'true') {
            sessionStorage.removeItem('skipToPayment');
            
            // Since we need to go to step 3 (payment), but step 1 is currently active
            setTimeout(() => {
                // Transition from step 1 to step 3 directly
                const step1 = document.getElementById('step-1');
                const step3 = document.getElementById('step-3');
                
                if (step1 && step3) {
                    step1.classList.remove('active');
                    step3.classList.add('active');
                    currentStep = 3;
                    
                    // Update progress bar
                    updateProgress(currentStep);
                    console.log('Skipped to payment step after login');
                }
            }, 100); // Small delay to ensure DOM is ready
        }
    }
    
    // Fill logged-in user data on page load
    if (isUserLoggedIn) {
        fillLoggedInUserData();
    }
    
    // Initialize carousel first
    updateArrowStates();
    
    // Adjust for responsive
    function adjustCarousel() {
        const slider = document.querySelector('.package-slider');
        if (slider) {
            if (window.innerWidth <= 768) {
                packagesPerView = 1;
                slider.style.width = '340px';
            } else {
                packagesPerView = 2;
                slider.style.width = '780px';
            }
            // Reset position when switching views
            currentPackageIndex = 0;
            const track = document.getElementById('packageTrack');
            if (track) {
                track.style.transform = 'translateX(0px)';
            }
            updateArrowStates();
        }
    }
    
    adjustCarousel();
    window.addEventListener('resize', adjustCarousel);

    // Terms & Conditions
    const showTerms = document.getElementById('showTerms');
    const termsModal = document.getElementById('termsModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const termsCheckbox = document.getElementById('termsCheckbox');
    const enrollBtn = document.getElementById('enrollBtn');

    if (termsCheckbox && enrollBtn) {
        termsCheckbox.disabled = true;
        enrollBtn.disabled = true;

        if (showTerms) {
            showTerms.addEventListener('click', function(e) {
                e.preventDefault();
                agreeBtn.disabled = false;
                termsModal.style.display = 'flex';
            });
        }

        if (agreeBtn) {
            agreeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                termsModal.style.display = 'none';
                termsCheckbox.disabled = false;
                termsCheckbox.checked = true;
                enrollBtn.disabled = false;
            });
        }

        window.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                termsModal.style.display = 'none';
            }
        });
    }

    // Success Modal
    const successModal = document.getElementById('successModal');
    if (successModal) {
        successModal.style.display = 'flex';
        const successOk = document.getElementById('successOk');
        if (successOk) {
            successOk.addEventListener('click', function() {
                window.location.href = '{{ route("home") }}';
            });
        }
    }
});
</script>
@endsection
