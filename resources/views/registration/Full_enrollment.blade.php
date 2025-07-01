@extends('layouts.navbar')

@section('title', 'Student Registration')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
<style>
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
        animation: slideIn 0.5s ease-in-out;
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
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .package-card {
        background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 20px;
        padding: 0;
        width: 320px;
        height: 400px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 3px solid transparent;
        flex-shrink: 0;
    }
    
    .package-carousel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
        position: relative;
        padding-top: 30px;
    }
    
    .package-slider {
        display: flex;
        gap: 20px;
        overflow: hidden;
        width: 780px;
        position: relative;
        margin: 0 auto;
    }
    
    .package-slider-track {
        display: flex;
        gap: 20px;
        transition: transform 0.3s ease;
    }
    
    .carousel-arrow {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .carousel-arrow:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .carousel-arrow:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .package-card {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .package-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        border-color: #1c2951;
    }
    
    .package-card.selected {
        border-color: #1c2951;
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(28, 41, 81, 0.5);
    }
    
    .package-image {
        width: 100%;
        height: 60%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
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
        color: #ffffff;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        text-align: center;
    }
    
    .package-description {
        color: #cccccc;
        font-size: 0.9rem;
        margin: 0 0 12px 0;
        text-align: center;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 4em;
    }
    
    .package-price {
        color: #1c2951;
        font-size: 1.4rem;
        font-weight: 800;
        text-align: center;
        margin: 0;
        background: linear-gradient(90deg, #a259c6, #6a82fb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
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
        
        .package-carousel {
            gap: 10px;
        }
    }
    
    @media (max-width: 480px) {
        .package-carousel {
            flex-direction: column;
            gap: 20px;
        }
        
        .carousel-arrow {
            order: 3;
        }
        
        .package-slider {
            order: 2;
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
<!-- FIXED SIZE REGISTRATION CONTAINER - prevents form resizing -->
<div class="registration-container" style="min-height: 800px; max-width: 1200px; margin: 0 auto; padding: 20px; position: relative;">
<form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form">
    @csrf
    <input type="hidden" name="enrollment_type" value="full">
    <input type="hidden" name="program_id" value="1">
    <input type="hidden" name="package_id" value="">
    <input type="hidden" name="plan_id" value="1">

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
                <input type="text" name="user_firstname" id="user_firstname" placeholder="First Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="text" name="user_lastname" id="user_lastname" placeholder="Last Name" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <input type="email" name="email" id="user_email" placeholder="Email" required
                   style="width:100%; max-width:500px; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            <div id="emailError" style="display: none; color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center;">
                This email is already registered. Please use a different email.
            </div>
            <div style="display:flex; gap:16px; width:100%; max-width:500px;">
                <input type="password" name="password" id="password" placeholder="Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <div id="passwordError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; visibility: hidden;">
                Password must be at least 8 characters long.
            </div>
            <div id="passwordMatchError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; visibility: hidden;">
                Passwords do not match.
            </div>
            <div style="text-align: center; margin-top: -10px;">
                <p style="color: #666; font-size: 14px; margin: 0;">
                    Already have an account? 
                    <a href="{{ route('login') }}" style="color: #1c2951; text-decoration: underline; font-weight: 600;">
                        Click here to login
                    </a>
                </p>
            </div>
            <div style="display:flex; gap:16px; justify-content:center;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="step2NextBtn"
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff;
                               border:none; border-radius:8px; padding:12px 40px; font-size:1.1rem; font-weight:600;
                               box-shadow:0 2px 8px rgba(160,89,198,0.08); cursor:not-allowed; opacity: 0.5;" disabled>
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

    {{-- STEP 4: FULL STUDENT REGISTRATION --}}
    <div class="step" id="step-4">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            STUDENT FULL PROGRAM REGISTRATION
        </h2>

        <h3>Student Information</h3>
        <div class="input-row">
            <input type="text" name="firstname" id="firstname" placeholder="First name" required>
            <input type="text" name="middle_name" id="middle_name" placeholder="Middle name">
            <input type="text" name="lastname" id="lastname" placeholder="Last name" required>
            <input type="text" name="student_school" id="student_school" placeholder="Student's school" required>
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
</div> <!-- END FIXED SIZE REGISTRATION CONTAINER -->

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
// Global variables (declared once at the top)
let currentStep = 1;
let selectedPackageId = null;
let selectedPaymentMethod = null;
let currentPackageIndex = 0;
let packagesPerView = 2;
let totalPackages = <?php echo isset($packages) && is_countable($packages) ? (int)count($packages) : 0; ?>;

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

// Step navigation with animations
function nextStep() {
    if (currentStep === 1) {
        animateStepTransition('step-1', 'step-2');
        currentStep = 2;
    } else if (currentStep === 2) {
        // Copy Account Registration data to Full Student Registration before moving to step 3
        copyAccountDataToStudentForm();
        animateStepTransition('step-2', 'step-3');
        currentStep = 3;
    } else if (currentStep === 3) {
        animateStepTransition('step-3', 'step-4');
        currentStep = 4;
        // Also auto-fill in case user comes directly to step 4
        copyAccountDataToStudentForm();
    }
}

function prevStep() {
    if (currentStep === 4) {
        animateStepTransition('step-4', 'step-3', true);
        currentStep = 3;
    } else if (currentStep === 3) {
        animateStepTransition('step-3', 'step-2', true);
        currentStep = 2;
    } else if (currentStep === 2) {
        animateStepTransition('step-2', 'step-1', true);
        currentStep = 1;
    }
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

// Make functions globally accessible
window.slidePackages = slidePackages;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectPackage = selectPackage;
window.selectPaymentMethod = selectPaymentMethod;

// Function to copy Account Registration data to Full Student Registration
function copyAccountDataToStudentForm() {
    // Get values from Step 2 (Account Registration)
    const userFirstname = document.getElementById('user_firstname')?.value || '';
    const userLastname = document.getElementById('user_lastname')?.value || '';
    const userEmail = document.getElementById('user_email')?.value || '';
    
    // Set values in Step 4 (Full Student Registration)
    const firstnameField = document.getElementById('firstname');
    const lastnameField = document.getElementById('lastname');
    
    if (firstnameField && userFirstname) {
        firstnameField.value = userFirstname;
        console.log('Auto-filled firstname:', userFirstname);
    }
    if (lastnameField && userLastname) {
        lastnameField.value = userLastname;
        console.log('Auto-filled lastname:', userLastname);
    }
    
    console.log('Auto-filled student registration fields from account data');
}

// Function to check if email already exists in database
async function checkEmailExists(email) {
    if (!email) return false;
    
    try {
        const response = await fetch('{{ route("check.email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        return data.exists;
    } catch (error) {
        console.error('Error checking email:', error);
        return false;
    }
}

// Function to validate email on blur
async function validateEmail() {
    const emailField = document.getElementById('user_email');
    const emailError = document.getElementById('emailError');
    
    if (!emailField || !emailError) return;
    
    const email = emailField.value.trim();
    
    if (email) {
        // Show loading state
        emailField.style.borderColor = '#ffc107';
        emailError.style.display = 'none';
        
        const exists = await checkEmailExists(email);
        
        if (exists) {
            emailField.style.borderColor = '#dc3545';
            emailError.style.display = 'block';
        } else {
            emailField.style.borderColor = '#28a745';
            emailError.style.display = 'none';
        }
    } else {
        emailField.style.borderColor = '#ccc';
        emailError.style.display = 'none';
    }
    
    // Validate all Step 2 fields after email validation
    setTimeout(validateStep2, 100);
}

// Function to validate password length
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField || !passwordError) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        passwordError.style.visibility = 'visible';
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        passwordError.style.visibility = 'hidden';
        return true;
    } else {
        passwordField.style.borderColor = '#ccc';
        passwordError.style.visibility = 'hidden';
        return true;
    }
}

// Function to validate password confirmation
function validatePasswordConfirmation() {
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('passwordMatchError');
    
    if (!passwordField || !passwordConfirmField || !passwordMatchError) return true;
    
    const password = passwordField.value;
    const passwordConfirm = passwordConfirmField.value;
    
    if (passwordConfirm.length > 0 && password !== passwordConfirm) {
        passwordConfirmField.style.borderColor = '#dc3545';
        passwordMatchError.style.visibility = 'visible';
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        passwordMatchError.style.visibility = 'hidden';
        return true;
    } else {
        passwordConfirmField.style.borderColor = '#ccc';
        passwordMatchError.style.visibility = 'hidden';
        return true;
    }
}

// Function to validate all Step 2 fields
function validateStep2() {
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.querySelector('#step-2 button[onclick="nextStep()"]');
    
    // Check if all required fields are filled
    const isFirstnameFilled = firstnameField && firstnameField.value.trim().length > 0;
    const isLastnameFilled = lastnameField && lastnameField.value.trim().length > 0;
    const isEmailFilled = emailField && emailField.value.trim().length > 0;
    const isPasswordFilled = passwordField && passwordField.value.length > 0;
    const isPasswordConfirmFilled = passwordConfirmField && passwordConfirmField.value.length > 0;
    
    // Check if validations pass
    const isPasswordValid = validatePassword();
    const isPasswordConfirmValid = validatePasswordConfirmation();
    
    // Check if email field has error (red border means email exists)
    const emailHasError = emailField && emailField.style.borderColor === 'rgb(220, 53, 69)';
    
    // Enable next button only if all conditions are met
    const allFieldsFilled = isFirstnameFilled && isLastnameFilled && isEmailFilled && isPasswordFilled && isPasswordConfirmFilled;
    const allValidationsPassed = isPasswordValid && isPasswordConfirmValid && !emailHasError;
    
    if (nextBtn) {
        if (allFieldsFilled && allValidationsPassed) {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
        } else {
            nextBtn.disabled = true;
            nextBtn.style.opacity = '0.5';
            nextBtn.style.cursor = 'not-allowed';
        }
    }
    
    return allFieldsFilled && allValidationsPassed;
}

// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
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
                slider.style.width = '700px';
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

    // Email validation
    const emailField = document.getElementById('user_email');
    if (emailField) {
        emailField.addEventListener('blur', validateEmail);
        emailField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('emailError').style.display = 'none';
            // Validate all fields when email changes
            setTimeout(validateStep2, 100);
        });
    }

    // First name and last name validation
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    
    if (firstnameField) {
        firstnameField.addEventListener('input', function() {
            // Validate all fields when first name changes
            setTimeout(validateStep2, 100);
        });
    }
    
    if (lastnameField) {
        lastnameField.addEventListener('input', function() {
            // Validate all fields when last name changes
            setTimeout(validateStep2, 100);
        });
    }

    // Password validation
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    
    if (passwordField) {
        passwordField.addEventListener('blur', validatePassword);
        passwordField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            // Don't hide the error here - let validatePassword handle it
            // Also validate confirmation when password changes
            setTimeout(validatePassword, 50);
            setTimeout(validatePasswordConfirmation, 100);
            setTimeout(validateStep2, 200);
        });
    }
    
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('blur', validatePasswordConfirmation);
        passwordConfirmField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('passwordMatchError').style.visibility = 'hidden';
            setTimeout(validateStep2, 100);
        });
    }

    // Initial validation on page load
    setTimeout(validateStep2, 500);

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
    const successOk = document.getElementById('successOk');
    if (successModal) {
        successModal.style.display = 'flex';
        if (successOk) {
            successOk.addEventListener('click', function() {
                window.location.href = '{{ route("home") }}';
            });
        }
    }
});
</script>
@endsection
