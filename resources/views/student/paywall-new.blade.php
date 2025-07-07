@extends('student.student-dashboard.student-dashboard-layout')

@section('title', 'Course Content')

@section('content')
<!-- Course Content (Background) -->
<div class="container-fluid py-4" id="courseContent">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">{{ $program->program_name }}</h2>
            
            <!-- Mock Course Content -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="bi bi-play-circle me-2"></i>Course Overview</h5>
                        </div>
                        <div class="card-body">
                            <p>Welcome to {{ $program->program_name }}! This comprehensive course will guide you through...</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>Introduction Module</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-lock text-muted me-2"></i>
                                        <span class="text-muted">Advanced Concepts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-list-ul me-2"></i>Course Modules</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-play-circle text-primary me-2"></i>Module 1: Getting Started</span>
                                    <span class="badge bg-success">Free</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center text-muted">
                                    <span><i class="bi bi-lock me-2"></i>Module 2: Core Concepts</span>
                                    <span class="badge bg-warning">Premium</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center text-muted">
                                    <span><i class="bi bi-lock me-2"></i>Module 3: Advanced Topics</span>
                                    <span class="badge bg-warning">Premium</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-info-circle me-2"></i>Course Info</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Duration:</strong> 8 weeks</p>
                            <p><strong>Level:</strong> Beginner to Advanced</p>
                            <p><strong>Certificate:</strong> Yes</p>
                            <p><strong>Instructor:</strong> Professional Team</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Paywall Overlay -->
<div class="paywall-modal" id="paywallModal">
    <div class="paywall-overlay"></div>
    <div class="paywall-content">
        <button class="paywall-close" onclick="closePaywall()">&times;</button>
        
        @if($enrollmentStatus === 'pending')
            <!-- Pending Approval -->
            <div class="paywall-header">
                <i class="bi bi-hourglass-split paywall-icon"></i>
                <h2>Pending Verification</h2>
                <p class="paywall-subtitle">Your enrollment is being reviewed</p>
            </div>
            <div class="paywall-body">
                <p class="paywall-description">
                    Your enrollment for <strong>{{ $program->program_name }}</strong> is currently being reviewed by our administrators.
                </p>
                <div class="paywall-alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Please wait for admin approval. You will be notified once your registration is verified.
                </div>
            </div>
            
        @elseif($enrollmentStatus === 'approved' && $paymentStatus !== 'paid')
            <!-- Payment Required -->
            <div class="paywall-header">
                <h2>Get Access Now</h2>
                <p class="paywall-subtitle">Subscribe & watch everything</p>
            </div>
            <div class="paywall-body">
                <!-- Subscription Plans -->
                <div class="subscription-plans">
                    <div class="plan-card">
                        <div class="plan-price">
                            <span class="currency">₱</span>
                            <span class="amount">299</span>
                            <span class="period">/month</span>
                        </div>
                        <button class="btn btn-subscribe" onclick="selectPaymentMethod('monthly')">
                            Subscribe Now
                        </button>
                    </div>
                    
                    <div class="plan-card">
                        <div class="plan-price">
                            <span class="currency">₱</span>
                            <span class="amount">16</span>
                            <span class="period">/seminar</span>
                        </div>
                        <button class="btn btn-get-access" onclick="selectPaymentMethod('seminar')">
                            Get Access Now
                        </button>
                    </div>
                </div>
                
                <div class="paywall-notice">
                    <small>Not sure which is right for you? <span class="text-link" onclick="showPlanDetails()">See the benefits of each</span></small>
                </div>
                
                <div class="paywall-login">
                    <span>Already have access?</span>
                    <div class="login-form">
                        <input type="email" placeholder="Email" class="form-input">
                        <input type="password" placeholder="Password" class="form-input">
                        <button class="btn btn-sign-in" onclick="handlePaywallLogin()">Sign In</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Paywall Modal Styles - Matching provided images */
.paywall-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out;
}

.paywall-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
}

.paywall-content {
    background: #3a3a3a;
    color: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    position: relative;
    z-index: 10000;
    overflow: hidden;
    animation: slideIn 0.4s ease-out;
}

.paywall-close {
    position: absolute;
    top: 15px;
    right: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    z-index: 10001;
    padding: 5px;
    line-height: 1;
}

.paywall-close:hover {
    color: #ccc;
}

.paywall-header {
    background: #2c2c2c;
    padding: 40px 30px 30px;
    text-align: center;
}

.paywall-icon {
    font-size: 3rem;
    color: #ffa500;
    margin-bottom: 15px;
}

.paywall-header h2 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: white;
}

.paywall-subtitle {
    font-size: 1rem;
    color: #ccc;
    margin: 0;
}

.paywall-body {
    padding: 30px;
}

.paywall-description {
    text-align: center;
    font-size: 1rem;
    line-height: 1.5;
    margin-bottom: 20px;
    color: #e0e0e0;
}

.paywall-alert {
    background: rgba(52, 144, 220, 0.2);
    border: 1px solid #3490dc;
    border-radius: 6px;
    padding: 15px;
    text-align: center;
    color: #87ceeb;
    font-size: 0.9rem;
}

/* Subscription Plans */
.subscription-plans {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.plan-card {
    background: #4a4a4a;
    border-radius: 8px;
    padding: 25px 20px;
    text-align: center;
    border: 2px solid #555;
    transition: all 0.3s ease;
}

.plan-card:hover {
    border-color: #dc3545;
    transform: translateY(-2px);
}

.plan-price {
    margin-bottom: 20px;
}

.plan-price .currency {
    font-size: 1.2rem;
    color: #ccc;
    vertical-align: top;
}

.plan-price .amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
}

.plan-price .period {
    font-size: 1rem;
    color: #ccc;
}

.btn {
    border: none;
    border-radius: 6px;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-subscribe {
    background: #dc3545;
    color: white;
}

.btn-subscribe:hover {
    background: #c82333;
}

.btn-get-access {
    background: #dc3545;
    color: white;
}

.btn-get-access:hover {
    background: #c82333;
}

.btn-sign-in {
    background: #dc3545;
    color: white;
    width: auto;
    padding: 8px 16px;
    font-size: 0.8rem;
}

.btn-sign-in:hover {
    background: #c82333;
}

.paywall-notice {
    text-align: center;
    margin: 20px 0;
    font-size: 0.85rem;
    color: #ccc;
}

.text-link {
    color: #dc3545;
    cursor: pointer;
    text-decoration: underline;
}

.text-link:hover {
    color: #c82333;
}

.paywall-login {
    margin-top: 30px;
    text-align: center;
    border-top: 1px solid #555;
    padding-top: 20px;
}

.paywall-login > span {
    display: block;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #ccc;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 300px;
    margin: 0 auto;
}

.form-input {
    background: #555;
    border: 1px solid #666;
    border-radius: 4px;
    padding: 10px 12px;
    color: white;
    font-size: 0.9rem;
}

.form-input::placeholder {
    color: #999;
}

.form-input:focus {
    outline: none;
    border-color: #dc3545;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .paywall-content {
        margin: 20px;
        max-width: none;
    }
    
    .subscription-plans {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .paywall-header {
        padding: 30px 20px 20px;
    }
    
    .paywall-body {
        padding: 20px;
    }
    
    .plan-price .amount {
        font-size: 2rem;
    }
}

/* Blur background content */
#courseContent.blurred {
    filter: blur(8px);
    pointer-events: none;
}
</style>

<script>
// Paywall functions
function closePaywall() {
    const paywallModal = document.getElementById('paywallModal');
    const courseContent = document.getElementById('courseContent');
    
    if (paywallModal) {
        paywallModal.style.display = 'none';
    }
    
    if (courseContent) {
        courseContent.classList.remove('blurred');
    }
}

function showPaywall() {
    const paywallModal = document.getElementById('paywallModal');
    const courseContent = document.getElementById('courseContent');
    
    if (paywallModal) {
        paywallModal.style.display = 'flex';
    }
    
    if (courseContent) {
        courseContent.classList.add('blurred');
    }
}

function selectPaymentMethod(plan) {
    console.log('Selected payment plan:', plan);
    
    // Here you would integrate with your payment processor
    // For now, just show a mock payment form
    showPaymentForm(plan);
}

function showPaymentForm(plan) {
    let planDetails = {
        'monthly': { amount: '299', period: 'month' },
        'seminar': { amount: '16', period: 'seminar' },
        'annual': { amount: '2999', period: 'year' }
    };
    
    let selected = planDetails[plan] || planDetails['monthly'];
    
    const paymentHTML = `
        <div class="payment-form">
            <h4 style="color: #333; margin-bottom: 20px;">Complete Payment - ₱${selected.amount}/${selected.period}</h4>
            <div class="form-group mb-3">
                <label style="color: #555;">Payment Method</label>
                <select class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option>Credit/Debit Card</option>
                    <option>GCash</option>
                    <option>Bank Transfer</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label style="color: #555;">Card Number</label>
                <input type="text" class="form-control" placeholder="1234 5678 9012 3456" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="row" style="display: flex; gap: 10px;">
                <div class="col-6" style="flex: 1;">
                    <label style="color: #555;">Expiry Date</label>
                    <input type="text" class="form-control" placeholder="MM/YY" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="col-6" style="flex: 1;">
                    <label style="color: #555;">CVV</label>
                    <input type="text" class="form-control" placeholder="123" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>
            <button class="btn btn-primary" onclick="processPayment()" style="width: 100%; margin-top: 20px; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Complete Payment - ₱${selected.amount}
            </button>
        </div>
    `;
    
    // Create temporary modal for payment
    const tempModal = document.createElement('div');
    tempModal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8); z-index: 10001; display: flex;
        align-items: center; justify-content: center;
    `;
    tempModal.innerHTML = `
        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 400px; width: 90%; position: relative;">
            ${paymentHTML}
            <button onclick="this.closest('.temp-modal').remove()" style="position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 20px; cursor: pointer; color: #999;">&times;</button>
        </div>
    `;
    tempModal.className = 'temp-modal';
    document.body.appendChild(tempModal);
}

function handlePaywallLogin() {
    const email = document.querySelector('.paywall-login input[type="email"]').value;
    const password = document.querySelector('.paywall-login input[type="password"]').value;
    
    if (!email || !password) {
        alert('Please enter both email and password');
        return;
    }
    
    // Here you would handle the login process
    console.log('Attempting login with:', email);
    alert('Login functionality would be integrated here');
}

function showPlanDetails() {
    alert('Plan comparison details would be shown here');
}

function processPayment() {
    // Here you would integrate with your payment processor
    alert('Payment processing would be integrated here with your preferred payment gateway.');
    
    // Close all modals
    document.querySelectorAll('.temp-modal').forEach(modal => modal.remove());
    closePaywall();
}

// Initialize paywall on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show paywall automatically if user needs payment/approval
    showPaywall();
    
    // Blur the background content
    const courseContent = document.getElementById('courseContent');
    if (courseContent) {
        courseContent.classList.add('blurred');
    }
});

// Close paywall when clicking outside
window.addEventListener('click', function(e) {
    const paywallModal = document.getElementById('paywallModal');
    const paywallContent = document.querySelector('.paywall-content');
    
    if (e.target === paywallModal && !paywallContent.contains(e.target)) {
        // Don't allow closing paywall by clicking outside for now
        // closePaywall();
    }
});

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaywall();
        document.querySelectorAll('.temp-modal').forEach(modal => modal.remove());
    }
});
</script>

@endsection
