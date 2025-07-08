@extends('layouts.navbar')

@section('title', 'Student Registration')
@section('hide_footer', true)
@section('body_class', 'registration-page')

@push('styles')
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Critical JavaScript functions for immediate availability -->
<script>
// Global variables (declare first for immediate availability)
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

// Define critical functions immediately for onclick handlers
function selectPackage(packageId, packageName, packagePrice) {
    // Remove selection from all package cards
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Highlight selected package
    if (event && event.target) {
        event.target.closest('.package-card').classList.add('selected');
    }
    
    // Store selection in global variable
    selectedPackageId = packageId;
    window.selectedPackageId = packageId;
    
    console.log('Package selected:', packageId, packageName); // Debug log
    
    // Store package selection in session storage
    sessionStorage.setItem('selectedPackageId', packageId);
    sessionStorage.setItem('selectedPackageName', packageName);
    sessionStorage.setItem('selectedPackagePrice', packagePrice);
    
    // Update hidden form input
    const packageInput = document.querySelector('input[name="package_id"]');
    if (packageInput) {
        packageInput.value = packageId;
    }
    
    // Show selected package display
    const selectedDisplay = document.getElementById('selectedPackageName');
    const selectedPackageDisplay = document.getElementById('selectedPackageDisplay');
    if (selectedDisplay) selectedDisplay.textContent = packageName;
    if (selectedPackageDisplay) selectedPackageDisplay.style.display = 'block';
    
    // Enable next button
    const nextBtn = document.getElementById('packageNextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.classList.add('enabled');
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
    }
}

function scrollPackages(direction) {
    const carousel = document.getElementById('packagesCarousel');
    if (!carousel) return;
    
    const scrollAmount = 340; // Package card width + gap
    
    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

function selectLearningMode(mode) {
    console.log('Selecting learning mode:', mode);
    
    // Remove selection from all learning mode cards
    document.querySelectorAll('.learning-mode-card').forEach(card => {
        card.style.border = '3px solid transparent';
        card.style.boxShadow = 'none';
    });
    
    // Highlight selected learning mode using data attribute
    const selectedCard = document.querySelector(`[data-mode="${mode}"]`);
    if (selectedCard) {
        selectedCard.style.border = '3px solid #667eea';
        selectedCard.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.3)';
        console.log('Selected card highlighted:', selectedCard);
    } else {
        console.error('No card found for mode:', mode);
    }
    
    // Update hidden input
    const learningModeInput = document.getElementById('learning_mode');
    if (learningModeInput) {
        learningModeInput.value = mode;
        console.log('Learning mode input updated:', mode);
    }
    
    // Update display
    const modeNames = {
        'synchronous': 'Synchronous (Live Classes)',
        'asynchronous': 'Asynchronous (Self-Paced)'
    };
    
    const selectedDisplay = document.getElementById('selectedLearningModeName');
    const displayContainer = document.getElementById('selectedLearningModeDisplay');
    
    if (selectedDisplay) selectedDisplay.textContent = modeNames[mode] || mode;
    if (displayContainer) displayContainer.style.display = 'block';
    
    // Enable next button with proper styling
    const nextBtn = document.getElementById('learningModeNextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.background = 'linear-gradient(90deg,#a259c6,#6a82fb)';
        nextBtn.classList.add('enabled');
        console.log('Next button enabled and styled');
    } else {
        console.error('Next button not found');
    }
}

function nextStep() {
    console.log('nextStep called, current step:', currentStep);
    console.log('selectedPackageId:', selectedPackageId);
    console.log('window.selectedPackageId:', window.selectedPackageId);
    
    if (currentStep === 1) {
        // Validate package selection with multiple checks
        const packageInput = document.querySelector('input[name="package_id"]');
        const sessionPackageId = sessionStorage.getItem('selectedPackageId');
        
        console.log('Package validation - selectedPackageId:', selectedPackageId);
        console.log('Package validation - window.selectedPackageId:', window.selectedPackageId);
        console.log('Package validation - packageInput.value:', packageInput?.value);
        console.log('Package validation - sessionStorage:', sessionPackageId);
        
        if (!selectedPackageId && !window.selectedPackageId && !packageInput?.value && !sessionPackageId) {
            showWarning('Please select a package before proceeding.');
            return;
        }
        
        // Ensure selectedPackageId is set if any other method has the value
        if (!selectedPackageId && (window.selectedPackageId || packageInput?.value || sessionPackageId)) {
            selectedPackageId = window.selectedPackageId || packageInput?.value || sessionPackageId;
            console.log('Updated selectedPackageId to:', selectedPackageId);
        }
        
        // Go to learning mode selection
        animateStepTransition('step-1', 'step-2');
        currentStep = 2;
    } else if (currentStep === 2) {
        // Validate learning mode selection
        const learningModeValue = document.getElementById('learning_mode')?.value;
        if (!learningModeValue) {
            showWarning('Please select a learning mode before proceeding.');
            return;
        }
        // Check if user is logged in
        if (isUserLoggedIn) {
            // Skip to student registration
            console.log('User logged in - skipping to student registration');
            animateStepTransition('step-2', 'step-4');
            currentStep = 4;
            // Auto-fill user data
            setTimeout(() => {
                fillLoggedInUserData();
            }, 300);
        } else {
            // Go to account registration
            console.log('User not logged in - going to account registration');
            animateStepTransition('step-2', 'step-3');
            currentStep = 3;
        }
    } else if (currentStep === 3) {
        // Validate account registration
        if (!validateStep3()) {
            showWarning('Please fill in all required fields correctly.');
            return;
        }
        // Go to student registration
        copyAccountDataToStudentForm();
        animateStepTransition('step-3', 'step-4');
        currentStep = 4;
        // Auto-fill user data
        setTimeout(() => {
            fillLoggedInUserData();
            copyAccountDataToStudentForm();
        }, 300);
    }
}

function prevStep() {
    console.log('prevStep called, current step:', currentStep);
    
    if (currentStep === 4) {
        // From student registration, check if user is logged in
        if (isUserLoggedIn) {
            // Skip account registration and go back to learning mode
            console.log('User logged in - going back to learning mode');
            animateStepTransition('step-4', 'step-2', true);
            currentStep = 2;
        } else {
            // User not logged in, go back to account registration
            console.log('User not logged in - going back to account registration');
            animateStepTransition('step-4', 'step-3', true);
            currentStep = 3;
        }
    } else if (currentStep === 3) {
        // From account registration back to learning mode
        animateStepTransition('step-3', 'step-2', true);
        currentStep = 2;
    } else if (currentStep === 2) {
        // From learning mode back to package selection
        animateStepTransition('step-2', 'step-1', true);
        currentStep = 1;
    }
}

// Helper function to show warning messages
function showWarning(message) {
    const warningModal = document.getElementById('warningModal');
    const warningMessage = document.getElementById('warningMessage');
    if (warningModal && warningMessage) {
        warningMessage.textContent = message;
        warningModal.style.display = 'flex';
    } else {
        alert(message); // Fallback
    }
}

// Helper function to close warning modal
function closeWarningModal() {
    const warningModal = document.getElementById('warningModal');
    if (warningModal) {
        warningModal.style.display = 'none';
    }
}

// Function to handle step transitions with animation
function animateStepTransition(fromStep, toStep, isBack = false) {
    const from = document.getElementById(fromStep);
    const to = document.getElementById(toStep);
    
    if (!from || !to) {
        console.error('Step elements not found:', fromStep, toStep);
        return;
    }
    
    // Add transition classes
    from.style.transition = 'all 0.3s ease-in-out';
    to.style.transition = 'all 0.3s ease-in-out';
    
    // Hide current step
    from.style.opacity = '0';
    from.style.transform = isBack ? 'translateX(50px)' : 'translateX(-50px)';
    
    setTimeout(() => {
        // Hide current step completely
        from.style.display = 'none';
        from.classList.remove('active');
        
        // Show new step
        to.style.display = 'block';
        to.style.opacity = '0';
        to.style.transform = isBack ? 'translateX(-50px)' : 'translateX(50px)';
        to.classList.add('active');
        
        // Animate in new step
        setTimeout(() => {
            to.style.opacity = '1';
            to.style.transform = 'translateX(0)';
        }, 50);
        
        // Reset transforms after animation
        setTimeout(() => {
            from.style.transform = '';
            to.style.transform = '';
        }, 350);
    }, 300);
}
</script>


<style>
  .navbar > .container-fluid {
    max-width: none !important;
    width: 100%    !important;
    margin: 0      !important;
    padding: 0 15px !important; /* keep your horizontal padding */
    background: transparent !important;
  }
    /* CLEAN RESET - Remove all layered containers */
    body {
        background: #f8f9fa !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .main-content,
    .content-wrapper,
    #content,
    .container-fluid {
        background: #f8f9fa !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: none !important;
        width: 100% !important;
    }
    
    /* SINGLE CENTERED CONTAINER - No multiple layers */
    .registration-container {
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 50px auto !important;
        max-width: 1200px !important;
        width: 90% !important;
        min-height: 600px !important;
        padding: 40px !important;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    /* CENTERED FORM CONTENT */
    .registration-form {
        width: 100% !important;
        max-width: 1000px !important;
        margin: 0 auto !important;
        padding: 0 !important;
    }
    
    /* CENTERED STEPS */
    .step {
        display: none !important;
        opacity: 0;
        transform: translateX(50px);
        transition: all 0.5s ease-in-out;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        min-height: 500px !important;
        width: 100% !important;
        padding: 20px !important;
    }
    
    .step.active {
        display: flex !important;
        opacity: 1;
        transform: translateX(0);
        animation: slideIn 0.5s ease-in-out;
    }
    
    /* Get enrollment styles from helper */
    {!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
    {!! App\Helpers\SettingsHelper::getButtonStyles() !!}

    /* STEP TRANSITIONS */
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

    /* PACKAGE CAROUSEL - CENTERED */
    .packages-carousel-container {
    position: relative;
    margin: 0 auto;
    max-width: calc(200px * 3 + 2rem * 2); /* for 3 cards; use 2*300+1*2rem for 2 cards, etc */
    overflow: hidden;    /* clip the scrolling track here */
    }
    
    .packages-carousel {
        cursor: grab;
        user-select: none;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
        scroll-behavior: smooth;
        padding: 1rem 0;
        display: flex;
        gap: 2rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
    }
    
    .packages-carousel::-webkit-scrollbar {
        display: none;
    }
    
    .packages-carousel.grabbing {
        cursor: grabbing;
    }
    
    .package-card-wrapper {
        scroll-snap-align: start;
        flex-shrink: 0;
        min-width: 280px !important;
        max-width: 300px !important;
    }
    
    .package-card {
        width: 100%;
        height: 400px;
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid transparent !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .package-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .package-card.selected {
        border-color: #1c2951 !important;
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(28, 41, 81, 0.3);
    }
    
    .package-image-header {
        height: 60%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .package-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
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
    
    .package-content {
        height: 40%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #1a1a1a;
        padding: 20px;
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
        line-clamp: 3;
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
    
    /* CAROUSEL NAVIGATION */
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10;
    }
    
    .carousel-nav:hover {
        background: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    .prev-btn {
        left: 1rem;
    }
    
    .next-btn {
        right: 1rem;
    }
    
    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .registration-container {
            margin: 20px auto !important;
            width: 95% !important;
            padding: 20px !important;
        }
        
        .packages-carousel-container {
            padding: 0 2rem !important;
        }
        
        .package-card-wrapper {
            min-width: 280px;
            max-width: 300px;
        }
        
        .carousel-nav {
            width: 40px;
            height: 40px;
        }
    }
    
    @media (max-width: 480px) {
        .packages-carousel-container {
            padding: 0 2rem !important;
        }
        
        .carousel-nav {
            display: none;
        }
        
        .package-card-wrapper {
            min-width: 260px;
        }
    }
        
    .package-card-wrapper {
        scroll-snap-align: start;
        flex-shrink: 0;
        min-width: 320px;
        max-width: 350px;
    }
    
    .package-card {
        transition: all 0.3s ease;
        border: 3px solid transparent !important;
        border-radius: 20px !important;
        overflow: hidden;
        cursor: pointer;
        height: 400px;
        background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%) !important;
        color: white;
    }
    
    .package-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3) !important;
        border-color: #1c2951 !important;
    }
    
    .package-card.selected {
        border-color: #1c2951 !important;
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(28, 41, 81, 0.5) !important;
    }
    
    .package-image-header {
        height: 60%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .package-icon {
        font-size: 4rem;
        color: white;
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
    
    .package-card .card-body {
        background: #1a1a1a;
        height: 40%;
        padding: 20px;
    }
    
    .package-title {
        color: #fff !important;
        font-size: 1.4rem !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
        text-align: center;
    }
    
    .package-description {
        color: #ccc !important;
        font-size: 0.9rem !important;
        line-height: 1.4;
        text-align: center;
        margin-bottom: 12px !important;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .package-price {
        font-size: 1.4rem !important;
        font-weight: 800 !important;
        text-align: center;
        margin: 0;
        background: linear-gradient(90deg, #a259c6, #6a82fb);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: 2px solid #667eea;
        border-radius: 50%;
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 1.4rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .carousel-nav:hover {
        background: #667eea;
        color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
    }
    
    .prev-btn {
        left: 15px;
    }
    
    .next-btn {
        right: 15px;
    }
    
    @media (max-width: 768px) {
        .packages-carousel-container {
            padding: 0 1rem;
        }
        
        .carousel-nav {
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
        }
        
        .prev-btn {
            left: 5px;
        }
        
        .next-btn {
            right: 5px;
        }
        
        .package-card-wrapper {
            min-width: 280px;
        }
    }
    
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
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        min-width: 270px;
        min-height: 70px;
       
}
.package-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    border-color: #1c2951 !important;
}
.package-card.selected {
    border-color: #1c2951 !important;
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(28, 41, 81, 0.5) !important;
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
    color: #ffffff !important;
    font-size: 1.3rem !important;
    font-weight: 700 !important;
    margin-bottom: 8px !important;
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
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 4em;
    }
.package-price {
    font-size: 1.4rem !important;
    font-weight: 800 !important;
    text-align: center;
    margin: 0;
    background: linear-gradient(90deg, #a259c6, #6a82fb);
    -webkit-background-clip: text;
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
    
    /* Learning Mode Cards */
    .learning-mode-card {
        background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 15px;
        padding: 30px 20px;
        width: 250px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid transparent;
        text-align: center;
        color: white;
    }
    
    .learning-mode-card:hover {
        transform: translateY(-5px);
        border-color: #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    /* ==== MOBILE & TABLET DEVICES (768px and below) ==== */
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
    
    /* ==== SMALL MOBILE DEVICES (480px and below) ==== */
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
    
    /* PAYMENT METHOD STYLES */
    .payment-method {
        display: flex;
        align-items: center;
        padding: 20px;
        margin: 15px 0;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .payment-method:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }
    
    .payment-method.selected {
        border-color: #667eea;
        background: linear-gradient(145deg, #f8f9ff 0%, #e3f2fd 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .payment-icon {
        font-size: 2rem;
        margin-right: 20px;
        width: 60px;
        text-align: center;
    }
    
    .payment-method h4 {
        color: #333;
        font-weight: 600;
        margin: 0 0 5px 0;
    }
    
    .payment-method p {
        color: #666;
        font-size: 14px;
        margin: 0;
    }
    
    .payment-method.selected h4 {
        color: #667eea;
    }
    
    .payment-method.selected p {
        color: #555;
    }
    
    /* BUTTON STATES */
    button.enabled {
        opacity: 1 !important;
        cursor: pointer !important;
        transition: all 0.3s ease;
    }
    
    button.enabled:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
    }
    
    button:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
    
    /* FORM INPUT FIXES */
    input[type="password"],
    input[type="email"],
    input[type="text"],
    input[type="date"],
    input[type="tel"],
    select,
    textarea {
        pointer-events: auto !important;
        position: relative !important;
        z-index: 1 !important;
        background: white !important;
        border: 1px solid #ccc !important;
        border-radius: 8px !important;
        padding: 12px 16px !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
    }
    
    input[type="password"]:focus,
    input[type="email"]:focus,
    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="tel"]:focus,
    select:focus,
    textarea:focus {
        outline: none !important;
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }
    
    /* Ensure form elements are not blocked */
    .step input,
    .step select,
    .step textarea {
        pointer-events: auto !important;
        position: relative !important;
        z-index: 10 !important;
    }
</style>
@endpush

@section('content')
<!-- Validation Errors Display -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px auto; max-width: 1200px;">
        <h6><i class="bi bi-exclamation-triangle"></i> Please correct the following errors:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- SINGLE CENTERED CONTAINER - No nested layers -->
<div class="registration-container">
    <form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form" id="enrollmentForm" novalidate>
        @csrf
        <input type="hidden" name="enrollment_type" value="full">
        <input type="hidden" name="package_id" value="">
        <input type="hidden" name="plan_id" value="1">

    {{-- STEP 1: PACKAGE SELECTION --}}
    <div class="step active" id="step-1">
        <h2 style="text-align:center; margin-bottom:30px; font-weight:700; letter-spacing:1px;">
            SELECT YOUR PACKAGE
        </h2>
        
        <!-- Bootstrap Horizontal Scrolling Package Carousel -->
        <div class="packages-carousel-container">
            <div class="packages-carousel" id="packagesCarousel">
                @foreach($packages as $package)
                <div class="package-card-wrapper">
                    <div class="card package-card h-100 shadow-lg" onclick="selectPackage('{{ $package->package_id }}', '{{ $package->package_name }}', '{{ $package->amount }}')" data-package-id="{{ $package->package_id }}" data-package-price="{{ $package->amount }}">
                        <div class="package-image-header">
                            <div class="package-icon">📦</div>
                            @if($loop->first)
                                <div class="package-badge">Popular</div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title package-title">{{ $package->package_name }}</h5>
                            <p class="card-text package-description flex-grow-1" title="{{ $package->description ?? 'Complete package with all features included.' }}">
                                {{ $package->description ?? 'Complete package with all features included.' }}
                            </p>
                            <div class="mt-auto">
                                <div class="package-price">₱{{ number_format($package->amount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Navigation Arrows -->
            @if($packages->count() > 2)
                <button type="button" class="carousel-nav prev-btn" onclick="scrollPackages('left')" id="prevPackageBtn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="carousel-nav next-btn" onclick="scrollPackages('right')" id="nextPackageBtn">
                    <i class="bi bi-chevron-right"></i>
                </button>

            @endif
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <div id="selectedPackageDisplay" style="display: none; margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%); border-radius: 12px; border: 2px solid #4caf50; max-width: 400px; margin: 0 auto 20px;">
                <strong style="color: #2e7d2e; font-size: 1.1rem;">Selected Package: <span id="selectedPackageName"></span></strong>
                <div style="color: #2e7d2e; font-size: 1.2rem; font-weight: bold; margin-top: 8px;">
                    Price: <span id="selectedPackagePrice"></span>
                </div>
            </div>
            <button type="button" onclick="nextStep()" id="packageNextBtn" disabled
                    class="btn btn-primary btn-lg" style="opacity: 0.5;">
                Next<i class="bi bi-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    {{-- STEP 2: LEARNING MODE SELECTION --}}
    <div class="step" id="step-2">
        <h2 style="text-align:center; margin-bottom:24px; font-weight:700; letter-spacing:1px;">
            LEARNING MODE SELECTION
        </h2>
        
        <div style="max-width: 600px; margin: 0 auto;">
            <h3 style="margin-bottom: 20px; text-align: center;">Choose Your Learning Mode</h3>
            
            <div class="learning-mode-container" style="display: flex; gap: 30px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap;">
                <div class="learning-mode-card" onclick="selectLearningMode('synchronous')" data-mode="synchronous"
                     style="background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 15px; padding: 30px 20px; width: 250px; cursor: pointer; 
                            transition: all 0.3s ease; border: 3px solid transparent; text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🕐</div>
                    <h4 style="margin: 0 0 10px 0; color: #fff;">Synchronous</h4>
                    <p style="margin: 0; color: #ccc; font-size: 14px;">Real-time classes with live interaction, scheduled sessions, and immediate feedback.</p>
                </div>
                
                <div class="learning-mode-card" onclick="selectLearningMode('asynchronous')" data-mode="asynchronous"
                     style="background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%); border-radius: 15px; padding: 30px 20px; width: 250px; cursor: pointer; 
                            transition: all 0.3s ease; border: 3px solid transparent; text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🎯</div>
                    <h4 style="margin: 0 0 10px 0; color: #fff;">Asynchronous</h4>
                    <p style="margin: 0; color: #ccc; font-size: 14px;">Self-paced learning with recorded materials, flexible schedule, and individual progress.</p>
                </div>
            </div>
            
            <div id="selectedLearningModeDisplay" style="display: none; margin: 20px 0; padding: 15px; background: #e8f5e8; border-radius: 8px; text-align: center;">
                <strong>Selected Learning Mode: <span id="selectedLearningModeName"></span></strong>
            </div>
            
            <input type="hidden" name="learning_mode" id="learning_mode" value="">
            
            <div style="display:flex; gap:16px; justify-content:center; margin-top: 30px;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="learningModeNextBtn" disabled
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff; border:none; 
                               border-radius:8px; padding:12px 40px; font-size:1.1rem; cursor:pointer; opacity: 0.5;">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- STEP 3: ACCOUNT REGISTRATION --}}
    <div class="step" id="step-3">
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
                <input type="password" name="password" id="password" placeholder="Password"
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password"
                       style="flex:1; padding:12px 16px; border-radius:8px; border:1px solid #ccc; font-size:1rem;">
            </div>
            <div id="passwordError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; display: none;">
                Password must be at least 8 characters long.
            </div>
            <div id="passwordMatchError" style="color: #dc3545; font-size: 14px; margin-top: -10px; text-align: center; min-height: 20px; display: none;">
                Passwords do not match.
            </div>
            <div style="text-align: center; margin-top: -10px;">
                <p style="color: #666; font-size: 14px; margin: 0;">
                    Already have an account? 
                    <a href="#" onclick="loginWithPackage()" style="color: #1c2951; text-decoration: underline; font-weight: 600;">
                        Click here to login
                    </a>
                </p>
            </div>
            <div style="display:flex; gap:16px; justify-content:center;">
                <button type="button" onclick="prevStep()" class="back-btn"
                        style="padding:12px 30px; border:none; border-radius:8px; background:#ccc; cursor:pointer;">
                    Back
                </button>
                <button type="button" onclick="nextStep()" id="step3NextBtn"
                        style="background:linear-gradient(90deg,#a259c6,#6a82fb); color:#fff;
                               border:none; border-radius:8px; padding:12px 40px; font-size:1.1rem; font-weight:600;
                               box-shadow:0 2px 8px rgba(160,89,198,0.08); cursor:not-allowed; opacity: 0.5;" disabled>
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

        @if($student)
        <div class="alert alert-info" style="background-color:#e7f3ff; border:1px solid #b3d9ff; color:#0066cc; padding:12px; border-radius:6px; margin-bottom:20px; text-align:center;">
            <i class="bi bi-info-circle"></i> Your existing information has been pre-filled. You can update any field as needed.
        </div>
        @endif

        {{-- Dynamic Form Fields (includes all sections) --}}
        <div id="dynamic-fields-container">
            <x-dynamic-enrollment-form :requirements="$formRequirements" />
        </div>

        <h3><i class="bi bi-book me-2"></i>Program</h3>
        <div class="input-row">
            <select name="program_id" class="form-select" required id="programSelect">
                <option value="">Select Program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}"
                        {{ old('program_id', $programId ?? '') == $program->program_id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <h3><i class="bi bi-calendar-event me-2"></i>Start Date</h3>
        <div class="course-box" style="margin-bottom:20px;">
            <input type="date" name="Start_Date" class="form-control"
                   value="{{ $student->start_date ?? old('Start_Date') }}" required>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
            <label class="form-check-label" for="termsCheckbox">
                I agree to the 
                <a href="#" id="showTerms" class="text-primary text-decoration-underline">
                  Terms and Conditions
                </a>
            </label>
        </div>

        <div class="d-flex gap-3 justify-content-center flex-column flex-md-row">
            <!-- Mobile: Full width buttons, Tablet & PC: Side by side -->
            <button type="button" onclick="prevStep()" class="btn btn-outline-secondary btn-lg order-2 order-md-1">
                <i class="bi bi-arrow-left me-2"></i>Back
            </button>
            <button type="submit" class="btn btn-primary btn-lg order-1 order-md-2" id="enrollBtn">
                <i class="bi bi-check-circle me-2"></i>Enroll Now
            </button>
        </div>
    </div>    </form>
</div> <!-- END SINGLE CENTERED CONTAINER -->

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

{{-- Success Modal - Only show for registration completion messages --}}
@if(session('success') && str_contains(session('success'), 'registration'))
  <div id="successModal"
       style="display:flex; position:fixed; top:0; left:0; width:100vw; height:100vh;
              background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div class="success-modal-content" style="background:white; border-radius:20px; max-width:500px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideIn 0.3s ease-out;">
      <!-- Success Icon -->
      <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:40px 20px 20px; color:white;">
        <div style="width:80px; height:80px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; backdrop-filter:blur(10px);">
          <i class="bi bi-check-circle-fill" style="font-size:2.5rem; color:white;"></i>
        </div>
        <h2 style="margin:0; font-size:1.8rem; font-weight:700; color:white;">Registration Successful!</h2>
      </div>
      
      <!-- Content -->
      <div style="padding:30px;">
        <p style="color:#666; font-size:1.1rem; margin:0 0 30px; line-height:1.5;">{{ session('success') }}</p>
        
        <!-- Buttons -->
        <div style="display:flex; gap:15px; justify-content:center, flex-wrap:wrap;">
          <button id="successOk" type="button" class="btn btn-primary btn-lg"
                 style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none; padding:12px 30px; border-radius:10px; color:white; font-weight:600; cursor:pointer; transition:all 0.3s ease;">
            <i class="bi bi-house-door me-2"></i>Go to Homepage
          </button>
          <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-lg" 
             style="padding:12px 30px; border-radius:10px; text-decoration:none; transition:all 0.3s ease;">
            <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    @keyframes modalSlideIn {
      from { opacity: 0; transform: translateY(-50px) scale(0.9); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .success-modal-content button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
  </style>
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

{{-- Warning Modal - Shows validation warnings --}}
<div id="warningModal"
     style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); 
            display:none; justify-content:center; align-items:center; z-index:10000;">
  <div class="warning-modal-content" style="background:white; border-radius:20px; max-width:500px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden; animation:modalSlideIn 0.3s ease-out;">
    <div style="background:linear-gradient(135deg, #FFA726 0%, #FF9800 100%); padding:30px; color:white;">
      <i class="bi bi-exclamation-triangle" style="font-size:3rem; margin-bottom:15px;"></i>
      <h3 style="margin:0; font-weight:600;">Warning</h3>
    </div>
    <div style="padding:30px;">
      <p id="warningMessage" style="margin:0 0 25px 0; font-size:16px; color:#555; line-height:1.5;"></p>
      <button onclick="closeWarningModal()" 
              style="background:linear-gradient(135deg, #FFA726 0%, #FF9800 100%); color:white; border:none; 
                     padding:12px 30px; border-radius:25px; font-size:16px; font-weight:600; 
                     cursor:pointer; transition:all 0.3s ease; box-shadow:0 4px 15px rgba(255,152,0,0.3);">
        OK
      </button>
    </div>
  </div>
</div>

<script>
// Functions are defined above in the critical section for immediate availability

// Make all functions globally accessible
window.selectedPackageId = selectedPackageId;
window.selectPackage = selectPackage;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.selectLearningMode = selectLearningMode;
window.showWarning = showWarning;
window.closeWarningModal = closeWarningModal;
window.animateStepTransition = animateStepTransition;
window.loginWithPackage = loginWithPackage;

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

// Function to fill logged-in user data
function fillLoggedInUserData() {
    if (isUserLoggedIn) {
        console.log('Filling logged-in user data...');
        
        // Auto-fill Step 4 (Full Student Registration) fields with logged-in user data
        const firstnameField = document.getElementById('First_Name');
        const lastnameField = document.getElementById('Last_Name');
        const middlenameField = document.getElementById('Middle_Name');
        
        // Use session data if available
        if (firstnameField && loggedInUserFirstname) {
            firstnameField.value = loggedInUserFirstname;
            console.log('Auto-filled First_Name from session:', loggedInUserFirstname);
        }
        if (lastnameField && loggedInUserLastname) {
            lastnameField.value = loggedInUserLastname;
            console.log('Auto-filled Last_Name from session:', loggedInUserLastname);
        }
        
        // Auto-fill other fields from student data if available
        @if($student)
        const studentData = {
            firstname: '{{ $student->firstname ?? '' }}',
            lastname: '{{ $student->lastname ?? '' }}',
            middlename: '{{ $student->middlename ?? '' }}',
            student_school: '{{ $student->student_school ?? '' }}',
            street_address: '{{ $student->street_address ?? '' }}',
            state_province: '{{ $student->state_province ?? '' }}',
            city: '{{ $student->city ?? '' }}',
            zipcode: '{{ $student->zipcode ?? '' }}',
            contact_number: '{{ $student->contact_number ?? '' }}',
            emergency_contact_number: '{{ $student->emergency_contact_number ?? '' }}',
        };
        
        // Fill all available student data
        Object.keys(studentData).forEach(fieldName => {
            const field = document.getElementById(fieldName) || document.querySelector(`input[name="${fieldName}"]`) || document.querySelector(`select[name="${fieldName}"]`);
            if (field && studentData[fieldName] && !field.value) {
                field.value = studentData[fieldName];
                console.log(`Auto-filled ${fieldName}:`, studentData[fieldName]);
            }
        });
        @endif
        
        // Also auto-fill Step 3 (Account Registration) fields if user navigates back
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

// Function to copy Account Registration data to Full Student Registration
function copyAccountDataToStudentForm() {
    // Get values from Step 3 (Account Registration)
    const userFirstname = document.getElementById('user_firstname')?.value || '';
    const userLastname = document.getElementById('user_lastname')?.value || '';
    const userEmail = document.getElementById('user_email')?.value || '';
    
    console.log('Copying account data - firstname:', userFirstname, 'lastname:', userLastname, 'email:', userEmail);
    
    // Set values in Step 4 (Full Student Registration) - using field names from form requirements
    const firstnameField = document.getElementById('First_Name');
    const lastnameField = document.getElementById('Last_Name');
    
    if (firstnameField && userFirstname && !firstnameField.value) {
        firstnameField.value = userFirstname;
        console.log('Auto-filled First_Name:', userFirstname);
    }
    if (lastnameField && userLastname && !lastnameField.value) {
        lastnameField.value = userLastname;
        console.log('Auto-filled Last_Name:', userLastname);
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
    
    // Validate all Step 3 fields after email validation
    setTimeout(validateStep3, 100);
}

// Function to validate password length
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    
    if (!passwordField || !passwordError) return true;
    
    const password = passwordField.value;
    
    if (password.length > 0 && password.length < 8) {
        passwordField.style.borderColor = '#dc3545';
        passwordError.style.display = 'block';
        return false;
    } else if (password.length >= 8) {
        passwordField.style.borderColor = '#28a745';
        passwordError.style.display = 'none';
        return true;
    } else {
        passwordField.style.borderColor = '#ccc';
        passwordError.style.display = 'none';
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
        passwordMatchError.style.display = 'block';
        return false;
    } else if (passwordConfirm.length > 0 && password === passwordConfirm) {
        passwordConfirmField.style.borderColor = '#28a745';
        passwordMatchError.style.display = 'none';
        return true;
    } else {
        passwordConfirmField.style.borderColor = '#ccc';
        passwordMatchError.style.display = 'none';
        return true;
    }
}

// Function to validate all Step 3 (Account Registration) fields
function validateStep3() {
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    const emailField = document.getElementById('user_email');
    const passwordField = document.getElementById('password');
    const passwordConfirmField = document.getElementById('password_confirmation');
    const nextBtn = document.getElementById('step3NextBtn');
    
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
    // Hide Step 2 if user is logged in and remove required attributes
    if (isUserLoggedIn) {
        const step2 = document.getElementById('step-2');
        if (step2) {
            step2.style.display = 'none';
            
            // Remove required attributes from Step 2 fields to prevent form validation errors
            const step2Fields = step2.querySelectorAll('input[required]');
            step2Fields.forEach(field => {
                field.removeAttribute('required');
                console.log('Removed required attribute from:', field.name);
            });
        }
        console.log('User is logged in - Step 2 (Account Registration) hidden and validation disabled');
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
        nextBtn.style.opacity = '1';
        
        // If user logged in from step 2 (account registration), skip to learning mode step (step 3)
        if (skipToPayment === 'true') {
            setTimeout(() => {
                // Go to step 3 (learning mode)
                animateStepTransition('step-1', 'step-3');
                currentStep = 3;
            }, 500);
        }
    }
    
    // Fill logged-in user data on page load
    if (isUserLoggedIn) {
        fillLoggedInUserData();
    }
    
    // Add program selection handler to update hidden input
    const programSelectField = document.getElementById('programSelect');
    if (programSelectField) {
        programSelectField.addEventListener('change', function() {
            const hiddenProgramInput = document.querySelector('input[name="program_id"]');
            if (hiddenProgramInput) {
                hiddenProgramInput.value = this.value;
                console.log('Updated hidden program_id input to:', this.value);
            }
        });
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
            setTimeout(validateStep3, 100);
        });
    }

    // First name and last name validation
    const firstnameField = document.getElementById('user_firstname');
    const lastnameField = document.getElementById('user_lastname');
    
    if (firstnameField) {
        firstnameField.addEventListener('input', function() {
            // Validate all fields when first name changes
            setTimeout(validateStep3, 100);
        });
    }
    
    if (lastnameField) {
        lastnameField.addEventListener('input', function() {
            // Validate all fields when last name changes
            setTimeout(validateStep3, 100);
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
            setTimeout(validateStep3, 200);
        });
    }
    
    if (passwordConfirmField) {
        passwordConfirmField.addEventListener('blur', validatePasswordConfirmation);
        passwordConfirmField.addEventListener('input', function() {
            // Reset styling when user starts typing
            this.style.borderColor = '#ccc';
            document.getElementById('passwordMatchError').style.display = 'none';
            setTimeout(validateStep3, 100);
        });
    }

    // Initial validation on page load
    setTimeout(validateStep3, 500);

    // Terms & Conditions
    const showTerms = document.getElementById('showTerms');
    const termsModal = document.getElementById('termsModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const termsCheckbox = document.getElementById('termsCheckbox');
    const enrollBtn = document.getElementById('enrollBtn');

    if (termsCheckbox && enrollBtn) {
        // For logged-in users doing multiple enrollments, enable the button immediately
        if (isUserLoggedIn) {
            termsCheckbox.disabled = false;
            termsCheckbox.checked = true;
            enrollBtn.disabled = false;
        } else {
            // For new users, require terms agreement
            termsCheckbox.disabled = true;
            enrollBtn.disabled = true;
        }

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

        }

        // Add event listener for checkbox change
        termsCheckbox.addEventListener('change', function() {
            enrollBtn.disabled = !this.checked;
        });

        window.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                termsModal.style.display = 'none';
            }
        });
    }

    // Handle program selection
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            // Update the hidden program_id input
            const hiddenProgramInput = document.querySelector('input[name="program_id"]');
            if (hiddenProgramInput) {
                hiddenProgramInput.value = this.value;
            }
            console.log('Program selected:', this.value);
        });
    }

    // Add form submission debugging
    const enrollmentForm = document.getElementById('enrollmentForm');
    if (enrollmentForm) {
        enrollmentForm.addEventListener('submit', function(e) {
            console.log('Form submission attempt detected');
            console.log('User logged in:', isUserLoggedIn);
            console.log('Selected package ID:', selectedPackageId);
            console.log('Learning mode:', document.getElementById('learning_mode')?.value);
            console.log('Payment method:', selectedPaymentMethod);
            console.log('Terms checked:', document.getElementById('termsCheckbox')?.checked);
            
            // Check if required fields are filled
            const programSelect = document.querySelector('select[name="program_id"]');
            const startDate = document.querySelector('input[name="Start_Date"]');
            
            console.log('Program selected:', programSelect?.value);
            console.log('Start date:', startDate?.value);
            
            // For debugging - don't prevent submission, just log
            // e.preventDefault();
        });
    }

    // Add form submission debugging
    const formElement = document.getElementById('enrollmentForm');
    if (formElement) {
        formElement.addEventListener('submit', function(e) {
            console.log('Form submission attempted...');
            
            // Check all required fields
            const requiredFields = formElement.querySelectorAll('[required]');
            let missingFields = [];
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    missingFields.push(field.name || field.id);
                }
            });
            
            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                e.preventDefault();
                showWarning('Please fill in all required fields: ' + missingFields.join(', '));
                return;
            }
            
            // Check if program is selected
            const programSelect = document.getElementById('programSelect');
            if (programSelect && !programSelect.value) {
                console.error('No program selected');
                e.preventDefault();
                showWarning('Please select a program');
                return;
            }
            
            // Check if package is selected
            const packageInput = document.querySelector('input[name="package_id"]');
            if (packageInput && !packageInput.value) {
                console.error('No package selected');
                e.preventDefault();
                showWarning('Please select a package');
                return;
            }
            
            // Check if learning mode is selected
            const learningModeInput = document.getElementById('learning_mode');
            if (learningModeInput && !learningModeInput.value) {
                console.error('No learning mode selected');
                e.preventDefault();
                showWarning('Please select a learning mode');
                return;
            }
            
            console.log('Form validation passed, submitting...');
            console.log('Program ID:', programSelect ? programSelect.value : 'not found');
            console.log('Package ID:', packageInput ? packageInput.value : 'not found');
            console.log('Learning Mode:', learningModeInput ? learningModeInput.value : 'not found');
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

// Form validation before submission
document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default submission
    
    console.log('Form submission attempted');
    
    // Check if we're on the final step
    if (currentStep !== 4) {
        // Show warning modal instead of alert
        showWarning('Please complete all steps before enrolling.');
        return false;
    }
    
    // Validate required fields based on user login status
    let missingFields = [];
    
    // Always required fields
    const packageId = document.querySelector('input[name="package_id"]').value;
    const programId = document.querySelector('select[name="program_id"]').value;
    const startDate = document.querySelector('input[name="Start_Date"]').value;
    const termsAccepted = document.querySelector('#termsCheckbox').checked;
    
    if (!packageId) missingFields.push('Package selection');
    if (!programId) missingFields.push('Program selection');
    if (!startDate) missingFields.push('Start date');
    if (!termsAccepted) missingFields.push('Terms and conditions agreement');
    
    // Check password fields only if user is not logged in
    if (!isUserLoggedIn) {
        const password = document.querySelector('#password').value;
        const passwordConfirm = document.querySelector('#password_confirmation').value;
        const email = document.querySelector('#user_email').value;
        const firstName = document.querySelector('#user_firstname').value;
        const lastName = document.querySelector('#user_lastname').value;
        
        if (!email) missingFields.push('Email');
        if (!firstName) missingFields.push('First name');
        if (!lastName) missingFields.push('Last name');
        if (!password) missingFields.push('Password');
        if (!passwordConfirm) missingFields.push('Password confirmation');
        if (password !== passwordConfirm) missingFields.push('Password confirmation (passwords must match)');
    }
    
    if (missingFields.length > 0) {
        showWarning('Please fill in the following required fields:\n• ' + missingFields.join('\n• '));
        return false;
    }
    
    // If validation passes, submit the form
    console.log('Form validation passed, submitting...');
    this.submit();
});

// Initialize prefill on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing prefill logic');
    
    // If user is logged in and we're on step 4, fill their data
    if (isUserLoggedIn && currentStep === 4) {
        fillLoggedInUserData();
    }
    
    // Auto-fill step 4 when step becomes active (for transitions)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                if (target.id === 'step-4' && target.classList.contains('active')) {
                    setTimeout(() => {
                        fillLoggedInUserData();
                        copyAccountDataToStudentForm();
                    }, 100);
                }
            }
        });
    });
    
    const step4Element = document.getElementById('step-4');
    if (step4Element) {
        observer.observe(step4Element, { attributes: true });
    }
});
</script>
@endsection
