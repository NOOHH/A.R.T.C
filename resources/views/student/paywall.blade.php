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
        
        @if($enrollmentStatus === 'pending' && $paymentStatus === 'unpaid')
            <!-- Pending Approval -->
            <div class="paywall-header">
                <i class="bi bi-hourglass-split paywall-icon"></i>
                <h2>Enrollment Under Review</h2>
                <p class="paywall-subtitle">Please wait for administrator approval</p>
            </div>
            <div class="paywall-body">
                <p class="paywall-description">
                    Your enrollment application for <strong>{{ $program->program_name }}</strong> has been submitted successfully and is currently being reviewed by our administrators.
                </p>
                <div class="paywall-alert">
                    <i class="bi bi-clock-history me-2"></i>
                    <strong>What happens next?</strong><br>
                    • Our administrators will review your application within 24-48 hours<br>
                    • You will receive an email notification once your enrollment is approved<br>
                    • After approval, you can proceed with payment to access course materials<br>
                    • You can check your enrollment status anytime in your dashboard
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-house-door me-2"></i>Return to Dashboard
                    </a>
                </div>
            </div>
            
        @elseif($enrollmentStatus === 'approved' && $paymentStatus === 'paid')
            <!-- Continue Learning -->
            <div class="paywall-header">
                <i class="bi bi-play-circle paywall-icon" style="color: #28a745;"></i>
                <h2>Welcome to Your Course!</h2>
                <p class="paywall-subtitle">You're all set to continue learning</p>
            </div>
            <div class="paywall-body">
                <p class="paywall-description">
                    You have full access to <strong>{{ $program->program_name }}</strong>. Continue your learning journey and explore all available course materials.
                </p>
                
                <!-- Course Progress Section -->
                <div class="course-progress-section">
                    <h5 style="color: white; margin-bottom: 15px;">
                        <i class="bi bi-bar-chart me-2"></i>Your Progress
                    </h5>
                    <div class="progress-bar-container">
                        <div class="progress" style="height: 10px; background: #555;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 25%"></div>
                        </div>
                        <small style="color: #ccc; margin-top: 5px; display: block;">25% Complete</small>
                    </div>
                </div>
                
                <!-- Quick Access Buttons -->
                <div class="quick-access-section" style="margin: 25px 0;">
                    <div class="row g-3">
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100" onclick="showCourseModules()">
                                <i class="bi bi-list-ul me-2"></i>Modules
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100" onclick="showAssignments()">
                                <i class="bi bi-file-earmark-text me-2"></i>Assignments
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100" onclick="showResources()">
                                <i class="bi bi-folder me-2"></i>Resources
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-light w-100" onclick="showQuizzes()">
                                <i class="bi bi-question-circle me-2"></i>Quizzes
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Continue Learning Button -->
                <div class="continue-learning-section" style="text-align: center; margin-top: 30px;">
                    <button class="btn btn-success btn-lg" onclick="closePaywall(); showCourseContent();" style="padding: 15px 30px; font-size: 1.1rem; font-weight: 600; border-radius: 8px; width: 100%;">
                        <i class="bi bi-play-circle me-2"></i>Continue Learning
                    </button>
                    <p style="color: #ccc; font-size: 0.9rem; margin-top: 10px;">
                        Last accessed: <span id="lastAccessTime">Today</span>
                    </p>
                </div>
            </div>
            
        @elseif($paymentStatus === 'unpaid' || ($enrollmentStatus === 'approved' && $paymentStatus !== 'paid'))
            <!-- Payment Required -->
            <div class="paywall-header">
                <h2>Pay to Get Access Now</h2>
            </div>
            <div class="paywall-body">
                <!-- Payment Method Selection -->
                <div class="payment-methods-section">
                    <h4 style="color: white; margin-bottom: 20px; text-align: center;">Choose Payment Method</h4>
                    <div id="paymentMethodsContainer">
                        <!-- Payment methods will be loaded here -->
                        <div class="loading-payment-methods" style="text-align: center; color: #ccc;">
                            <i class="bi bi-hourglass-split"></i> Loading payment methods...
                        </div>
                    </div>
                </div>
                
                <!-- Payment Button -->
                <div class="payment-action-section" style="margin: 30px 0; text-align: center;">
                    <button class="btn btn-primary btn-lg" id="processPaymentBtn" onclick="processPayment()" disabled style="background: #dc3545; border: none; padding: 15px 30px; font-size: 1.1rem; font-weight: 600; border-radius: 8px; width: 100%; opacity: 0.6;">
                        <i class="fas fa-lock"></i> Select Payment Method First
                    </button>
                    <p style="color: #ccc; font-size: 0.9rem; margin-top: 10px;">
                        Package: <strong>{{ $packageName ?? 'Selected Package' }}</strong><br>
                        Amount: <strong>₱{{ number_format($enrollmentFee ?? 5000, 2) }}</strong>
                    </p>
                </div>
                
                <!-- QR Code and Payment Instructions Container -->
                <div id="qrCodeContainer" style="display: none;"></div>
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

/* Payment Methods */
.payment-methods-section {
    margin-bottom: 30px;
}

.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.payment-method-card {
    background: #4a4a4a;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    border: 2px solid #555;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.payment-method-card:hover {
    border-color: #dc3545;
    transform: translateY(-2px);
}

.payment-method-card.selected {
    border-color: #dc3545;
    background: #5a2d2d;
}

.payment-method-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 15px;
    background: #666;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.payment-method-name {
    font-weight: 600;
    color: white;
    margin-bottom: 5px;
}

.payment-method-description {
    font-size: 0.8rem;
    color: #ccc;
    margin-bottom: 15px;
}

.qr-code-preview {
    max-width: 80px;
    height: auto;
    margin: 0 auto 10px;
    display: block;
    border-radius: 4px;
}

.loading-payment-methods {
    padding: 40px;
    text-align: center;
    color: #ccc;
}

.payment-form-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 10001;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out;
}

.payment-form-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    padding: 30px;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

.qr-payment-section {
    text-align: center;
    padding: 20px;
}

.qr-code-display {
    background: white;
    padding: 20px;
    border-radius: 8px;
    display: inline-block;
    margin: 20px 0;
}

.qr-code-display img {
    max-width: 200px;
    height: auto;
    display: block;
}

.payment-instructions {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
    border-left: 4px solid #dc3545;
}

.payment-amount-display {
    background: #dc3545;
    color: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    font-size: 1.2rem;
    font-weight: 600;
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

/* Course Progress Styles */
.course-progress-section {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.progress-bar-container {
    margin: 10px 0;
}

/* Quick Access Styles */
.quick-access-section .btn {
    padding: 12px 8px;
    font-size: 0.9rem;
    border: 1px solid #555;
    transition: all 0.3s ease;
}

.quick-access-section .btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: #dc3545;
    transform: translateY(-1px);
}

/* Continue Learning Section */
.continue-learning-section .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

.continue-learning-section .btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1ba085 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

/* Course Content Display Styles */
.course-content-display {
    display: none;
    background: white;
    border-radius: 12px;
    margin: 20px 0;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.course-content-header {
    background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
    color: white;
    padding: 20px;
    text-align: center;
}

.course-content-body {
    padding: 30px;
    color: #333;
}

.module-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.module-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.module-item:hover {
    background: #e9ecef;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.module-info h6 {
    margin: 0 0 5px 0;
    color: #495057;
    font-weight: 600;
}

.module-info p {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

.module-status {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-in-progress {
    background: #fff3cd;
    color: #856404;
}

.status-locked {
    background: #f8d7da;
    color: #721c24;
}
</style>

<script>
// Paywall functions
let selectedPaymentMethod = null;
let selectedPlan = null;
let selectedAmount = 0;

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

// Load payment methods from admin settings
async function loadPaymentMethods() {
    try {
        const response = await fetch('/payment-methods/enabled');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            renderPaymentMethods(data.data);
        } else {
            renderDefaultPaymentMethods();
        }
    } catch (error) {
        console.error('Error loading payment methods:', error);
        renderDefaultPaymentMethods();
    }
}

function renderPaymentMethods(paymentMethods) {
    const container = document.getElementById('paymentMethodsContainer');
    
    const methodsHTML = paymentMethods.map(method => `
        <div class="payment-method-card" onclick="selectPaymentMethod('${method.payment_method_id}', '${method.method_name}', '${method.method_type}', '${method.qr_code_path || ''}', event)">>
            <div class="payment-method-icon">
                ${method.qr_code_path ? 
                    `<img src="/storage/${method.qr_code_path}" class="qr-code-preview" alt="${method.method_name}">` :
                    getPaymentMethodIcon(method.method_type)
                }
            </div>
            <div class="payment-method-name">${method.method_name}</div>
            <div class="payment-method-description">${method.description || getPaymentMethodDescription(method.method_type)}</div>
        </div>
    `).join('');
    
    container.innerHTML = `<div class="payment-methods-grid">${methodsHTML}</div>`;
}

function renderDefaultPaymentMethods() {
    const container = document.getElementById('paymentMethodsContainer');
    container.innerHTML = `
        <div class="payment-methods-grid">
            <div class="payment-method-card" onclick="selectPaymentMethod('gcash', 'GCash', 'digital_wallet', '', event)">>
                <div class="payment-method-icon">💳</div>
                <div class="payment-method-name">GCash</div>
                <div class="payment-method-description">Pay with GCash wallet</div>
            </div>
            <div class="payment-method-card" onclick="selectPaymentMethod('maya', 'Maya', 'digital_wallet', '', event)">>
                <div class="payment-method-icon">📱</div>
                <div class="payment-method-name">Maya</div>
                <div class="payment-method-description">Pay with Maya wallet</div>
            </div>
        </div>
    `;
}

function getPaymentMethodIcon(type) {
    const icons = {
        'cash': '💵',
        'bank_transfer': '🏦',
        'credit_card': '💳',
        'digital_wallet': '📱',
        'cryptocurrency': '₿'
    };
    return icons[type] || '💳';
}

function getPaymentMethodDescription(type) {
    const descriptions = {
        'cash': 'Pay with cash',
        'bank_transfer': 'Bank to bank transfer',
        'credit_card': 'Credit or debit card',
        'digital_wallet': 'Digital wallet payment',
        'cryptocurrency': 'Cryptocurrency payment'
    };
    return descriptions[type] || 'Payment method';
}

function selectPaymentMethod(id, name, type, qrPath, event) {
    // Remove selection from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    if (event) {
        const card = event.target.closest('.payment-method-card');
        if (card) {
            card.classList.add('selected');
        }
    }
    
    selectedPaymentMethod = {
        id: id,
        name: name,
        type: type,
        qr_path: qrPath
    };
    
    // Enable the payment button
    const paymentBtn = document.getElementById('processPaymentBtn');
    if (paymentBtn) {
        paymentBtn.disabled = false;
        paymentBtn.style.opacity = '1';
        paymentBtn.innerHTML = `<i class="fas fa-credit-card"></i> Proceed with ${name}`;
    }
    
    console.log('Selected payment method:', selectedPaymentMethod);
}

// New function to process payment directly
function processPayment() {
    if (!selectedPaymentMethod) {
        showAlert('Please select a payment method first', 'warning');
        return;
    }

    const amount = parseFloat('{{ $enrollmentFee ?? 5000 }}');
    const packageName = '{{ $packageName ?? "Selected Package" }}';
    const studentId = '{{ auth()->user()->id ?? "guest" }}';
    
    // Show loading state
    const processBtn = document.getElementById('processPaymentBtn');
    const originalText = processBtn.innerHTML;
    processBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    processBtn.disabled = true;

    // Check if it's a QR payment method
    if (selectedPaymentMethod.qr_path && selectedPaymentMethod.qr_path !== '') {
        showQRCodePayment(selectedPaymentMethod, amount, packageName);
        resetProcessButton();
    } else if (selectedPaymentMethod.name.toLowerCase().includes('maya')) {
        // Maya payment processing
        fetch('/process-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method_id: selectedPaymentMethod.id,
                amount: amount,
                student_id: studentId,
                payment_type: 'maya_redirect'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.redirect_url) {
                showAlert('Redirecting to Maya payment gateway...', 'info');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else {
                showAlert(data.error || 'Payment setup failed', 'danger');
                resetProcessButton();
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            showAlert('Payment processing failed. Please try again.', 'danger');
            resetProcessButton();
        });
    } else {
        // Manual payment method
        showManualPaymentInstructions(selectedPaymentMethod, amount, packageName);
        resetProcessButton();
    }

    function resetProcessButton() {
        processBtn.innerHTML = originalText;
        processBtn.disabled = false;
    }
}

// Function to show QR code payment interface
function showQRCodePayment(paymentMethod, amount, packageName) {
    const qrContainer = document.getElementById('qrCodeContainer');
    if (qrContainer) {
        qrContainer.style.display = 'block';
        qrContainer.innerHTML = `
            <div class="qr-payment-section" style="background: white; border-radius: 8px; padding: 30px; margin-top: 20px; color: #333;">
                <div style="text-align: center;">
                    <h4 style="margin-bottom: 20px; color: #333;">Pay with ${paymentMethod.name}</h4>
                    
                    <div class="payment-amount-display" style="background: #dc3545; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="font-size: 1.1rem; margin-bottom: 5px;">Package: ${packageName}</div>
                        <div style="font-size: 1.3rem; font-weight: bold;">Amount: ₱${amount.toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                    </div>
                    
                    <div class="qr-code-display" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <img src="/storage/${paymentMethod.qr_path}" alt="QR Code" style="max-width: 200px; height: auto;">
                    </div>
                    
                    <div class="payment-instructions" style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 15px 0; text-align: left;">
                        <h6 style="color: #1976d2; margin-bottom: 10px;">Payment Instructions:</h6>
                        <ol style="margin: 0; padding-left: 20px;">
                            <li>Scan the QR code using your ${paymentMethod.name} app</li>
                            <li>Enter the exact amount: ₱${amount.toLocaleString('en-PH', {minimumFractionDigits: 2})}</li>
                            <li>Complete the payment</li>
                            <li>Take a screenshot of the payment confirmation</li>
                            <li>Upload the screenshot below for verification</li>
                        </ol>
                    </div>
                    
                    <div style="margin: 20px 0; text-align: left;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Upload Payment Proof *</label>
                        <input type="file" accept="image/*" class="form-control" id="paymentProofFile" 
                               style="width: 100%; padding: 10px; border: 2px dashed #ddd; border-radius: 8px; background: #fafafa;">
                        <small style="color: #666; font-size: 0.9rem;">Upload a clear photo of your payment receipt/confirmation</small>
                    </div>
                    
                    <div style="margin: 20px 0; text-align: left;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Reference Number (Optional)</label>
                        <input type="text" placeholder="Enter reference number if available" id="referenceNumber"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                    
                    <button onclick="uploadPaymentProof(${amount}, '${packageName}')" 
                            style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; margin-top: 20px;">
                        <i class="fas fa-upload"></i> Submit Payment Proof
                    </button>
                    
                    <button onclick="document.getElementById('qrCodeContainer').style.display='none'" 
                            style="width: 100%; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 8px; margin-top: 10px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        `;
        
        // Scroll to QR code section
        qrContainer.scrollIntoView({ behavior: 'smooth' });
    }
}

// Function to show manual payment instructions
function showManualPaymentInstructions(paymentMethod, amount, packageName) {
    const qrContainer = document.getElementById('qrCodeContainer');
    if (qrContainer) {
        qrContainer.style.display = 'block';
        
        let instructions = getPaymentInstructions(paymentMethod, amount);
        
        qrContainer.innerHTML = `
            <div class="manual-payment-section" style="background: white; border-radius: 8px; padding: 30px; margin-top: 20px; color: #333;">
                <div style="text-align: center;">
                    <h4 style="margin-bottom: 20px; color: #333;">Pay with ${paymentMethod.name}</h4>
                    
                    <div class="payment-amount-display" style="background: #dc3545; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="font-size: 1.1rem; margin-bottom: 5px;">Package: ${packageName}</div>
                        <div style="font-size: 1.3rem; font-weight: bold;">Amount: ₱${amount.toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                    </div>
                    
                    <div class="payment-instructions" style="background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: left;">
                        <h6 style="color: #856404; margin-bottom: 15px;">Payment Instructions:</h6>
                        <div style="margin: 0; line-height: 1.6;">${instructions}</div>
                    </div>
                    
                    <div style="margin: 20px 0; text-align: left;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Upload Payment Proof *</label>
                        <input type="file" accept="image/*" class="form-control" id="paymentProofFile" 
                               style="width: 100%; padding: 10px; border: 2px dashed #ddd; border-radius: 8px; background: #fafafa;">
                        <small style="color: #666; font-size: 0.9rem;">Upload a clear photo of your payment receipt/confirmation</small>
                    </div>
                    
                    <div style="margin: 20px 0; text-align: left;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Reference Number (Optional)</label>
                        <input type="text" placeholder="Enter reference number if available" id="referenceNumber"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                    
                    <button onclick="uploadPaymentProof(${amount}, '${packageName}')" 
                            style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; margin-top: 20px;">
                        <i class="fas fa-upload"></i> Submit Payment Proof
                    </button>
                    
                    <button onclick="document.getElementById('qrCodeContainer').style.display='none'" 
                            style="width: 100%; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 8px; margin-top: 10px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        `;
        
        // Scroll to payment section
        qrContainer.scrollIntoView({ behavior: 'smooth' });
    }
}

// Function to upload payment proof
function uploadPaymentProof(amount, packageName) {
    const fileInput = document.getElementById('paymentProofFile');
    const referenceInput = document.getElementById('referenceNumber');
    
    if (!fileInput.files[0]) {
        showAlert('Please select a payment proof file to upload', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('payment_proof', fileInput.files[0]);
    formData.append('reference_number', referenceInput.value || '');
    formData.append('payment_method_id', selectedPaymentMethod.id);
    formData.append('amount', amount);
    formData.append('package_name', packageName);
    formData.append('student_id', '{{ auth()->user()->id ?? "guest" }}');

    // Show uploading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    submitBtn.disabled = true;

    fetch('/upload-payment-proof', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message || 'Payment proof uploaded successfully! Your payment will be verified within 24 hours.', 'success');
            setTimeout(() => {
                document.getElementById('qrCodeContainer').style.display = 'none';
                closePaywall();
                window.location.href = '{{ route("student.dashboard") }}';
            }, 3000);
        } else {
            showAlert(data.error || 'Upload failed. Please try again.', 'danger');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showAlert('Upload failed. Please try again.', 'danger');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Function to get payment instructions based on method type
function getPaymentInstructions(paymentMethod, amount) {
    const formattedAmount = amount.toLocaleString('en-PH', {minimumFractionDigits: 2});
    
    const instructions = {
        'gcash': `1. Open your GCash app<br>2. Go to "Send Money" or "Pay Bills"<br>3. Send exactly ₱${formattedAmount} to the account details provided<br>4. Take a screenshot of the successful transaction<br>5. Upload the screenshot above for verification`,
        'maya': `1. Open your Maya app<br>2. Select "Send Money" or "Pay Bills"<br>3. Send exactly ₱${formattedAmount} to the account details provided<br>4. Take a screenshot of the successful transaction<br>5. Upload the screenshot above for verification`,
        'bank_transfer': `1. Go to your bank (online, mobile app, or branch)<br>2. Transfer exactly ₱${formattedAmount} to our bank account<br>3. Save the transfer receipt or take a photo of the deposit slip<br>4. Upload the receipt above for verification`,
        'cash': `1. Visit our office during business hours<br>2. Pay exactly ₱${formattedAmount} at the cashier<br>3. Request an official receipt<br>4. Take a photo of the receipt and upload it above for verification`
    };

    const methodType = paymentMethod.type || 'manual';
    const methodName = paymentMethod.name.toLowerCase();
    
    // Try to match by name first, then by type
    if (methodName.includes('gcash')) return instructions.gcash;
    if (methodName.includes('maya')) return instructions.maya;
    if (methodName.includes('bank')) return instructions.bank_transfer;
    if (methodName.includes('cash')) return instructions.cash;
    
    // Fallback based on type
    return instructions[methodType] || `Complete your payment using ${paymentMethod.name} for exactly ₱${formattedAmount} and upload proof of payment above for verification.`;
}

function selectPaymentPlan(plan, amount) {
    selectedPlan = plan;
    selectedAmount = amount;
    
    if (!selectedPaymentMethod) {
        alert('Please select a payment method first');
        return;
    }
    
    console.log('Selected plan:', plan, 'Amount:', amount);
    showPaymentForm();
}

function showPaymentForm() {
    if (!selectedPaymentMethod || !selectedPlan) {
        alert('Please select a payment method and plan');
        return;
    }
    
    const isQRPayment = selectedPaymentMethod.qr_path && selectedPaymentMethod.qr_path !== '';
    
    let paymentHTML = '';
    
    if (isQRPayment) {
        // QR Code Payment
        paymentHTML = `
            <div class="qr-payment-section">
                <h4 style="color: #333; margin-bottom: 20px;">Pay with ${selectedPaymentMethod.name}</h4>
                
                <div class="payment-amount-display">
                    Amount to Pay: ₱${selectedAmount}
                </div>
                
                <div class="qr-code-display">
                    <img src="/storage/${selectedPaymentMethod.qr_path}" alt="QR Code" style="max-width: 200px;">
                </div>
                
                <div class="payment-instructions">
                    <h6>Instructions:</h6>
                    <ol style="text-align: left; margin: 10px 0;">
                        <li>Scan the QR code using your ${selectedPaymentMethod.name} app</li>
                        <li>Enter the amount: ₱${selectedAmount}</li>
                        <li>Complete the payment</li>
                        <li>Take a screenshot of the payment confirmation</li>
                        <li>Upload the screenshot below</li>
                    </ol>
                </div>
                
                <div class="form-group" style="margin: 20px 0;">
                    <label style="color: #555; display: block; margin-bottom: 5px;">Upload Payment Proof</label>
                    <input type="file" accept="image/*" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group">
                    <label style="color: #555; display: block; margin-bottom: 5px;">Reference Number (Optional)</label>
                    <input type="text" placeholder="Enter reference number" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <button class="btn btn-primary" onclick="processQRPayment()" style="width: 100%; margin-top: 20px; padding: 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Submit Payment
                </button>
            </div>
        `;
    } else {
        // Form-based Payment (Maya API integration)
        paymentHTML = `
            <div class="form-payment-section">
                <h4 style="color: #333; margin-bottom: 20px;">Pay with ${selectedPaymentMethod.name} - ₱${selectedAmount}</h4>
                
                <div class="payment-amount-display">
                    Amount to Pay: ₱${selectedAmount}
                </div>
                
                ${selectedPaymentMethod.name.toLowerCase().includes('maya') ? `
                    <div class="maya-payment-form">
                        <p style="text-align: center; color: #666; margin-bottom: 20px;">
                            You will be redirected to Maya to complete your payment securely.
                        </p>
                        <button class="btn btn-primary" onclick="processMayaPayment()" style="width: 100%; padding: 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Pay with Maya - ₱${selectedAmount}
                        </button>
                    </div>
                ` : `
                    <div class="manual-payment-form">
                        <div class="payment-instructions">
                            <h6>Payment Instructions:</h6>
                            <p>Please contact our support team to complete your payment with ${selectedPaymentMethod.name}.</p>
                        </div>
                        <button class="btn btn-primary" onclick="contactSupport()" style="width: 100%; padding: 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Contact Support
                        </button>
                    </div>
                `}
            </div>
        `;
    }
    
    // Create payment modal
    const paymentModal = document.createElement('div');
    paymentModal.className = 'payment-form-modal';
    paymentModal.innerHTML = `
        <div class="payment-form-content">
            ${paymentHTML}
            <button onclick="this.closest('.payment-form-modal').remove()" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
        </div>
    `;
    
    document.body.appendChild(paymentModal);
}

function processQRPayment() {
    const fileInput = document.querySelector('.payment-form-modal input[type="file"]');
    const referenceInput = document.querySelector('.payment-form-modal input[type="text"]');
    
    if (!fileInput.files[0]) {
        showAlert('Please upload payment proof', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('payment_proof', fileInput.files[0]);
    formData.append('reference_number', referenceInput.value || '');
    formData.append('payment_method_id', selectedPaymentMethod.id);
    formData.append('amount', selectedAmount);
    formData.append('student_id', '{{ auth()->user()->id ?? "guest" }}');

    fetch('/upload-payment-proof', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                document.querySelectorAll('.payment-form-modal').forEach(modal => modal.remove());
                closePaywall();
                window.location.href = '{{ route("student.dashboard") }}';
            }, 2000);
        } else {
            showAlert(data.error || 'Upload failed', 'danger');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showAlert('Upload failed. Please try again.', 'danger');
    });
}

function processMayaPayment() {
    if (!selectedPaymentMethod || !selectedAmount) {
        showAlert('Invalid payment data', 'danger');
        return;
    }

    const processBtn = document.querySelector('.maya-payment-form button');
    const originalText = processBtn.innerHTML;
    processBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    processBtn.disabled = true;

    fetch('/process-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_method_id: selectedPaymentMethod.id,
            amount: selectedAmount,
            student_id: '{{ auth()->user()->id ?? "guest" }}',
            payment_type: 'maya_redirect'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect_url) {
            showAlert('Redirecting to Maya payment gateway...', 'info');
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 1000);
        } else {
            showAlert(data.error || 'Payment setup failed', 'danger');
            processBtn.innerHTML = originalText;
            processBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Payment error:', error);
        showAlert('Payment processing failed. Please try again.', 'danger');
        processBtn.innerHTML = originalText;
        processBtn.disabled = false;
    });
}

function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert.custom-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} custom-alert alert-dismissible fade show`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10002;
        min-width: 300px;
        background: ${type === 'success' ? '#d4edda' : type === 'danger' ? '#f8d7da' : type === 'warning' ? '#fff3cd' : '#d1ecf1'};
        color: ${type === 'success' ? '#155724' : type === 'danger' ? '#721c24' : type === 'warning' ? '#856404' : '#0c5460'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'danger' ? '#f5c6cb' : type === 'warning' ? '#ffeaa7' : '#bee5eb'};
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" style="background: none; border: none; float: right; font-size: 20px; font-weight: bold; color: inherit; cursor: pointer;" onclick="this.parentElement.remove()">&times;</button>
    `;

    document.body.appendChild(alertDiv);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Course Content Functions
function showCourseContent() {
    const courseContent = document.getElementById('courseContent');
    if (courseContent) {
        courseContent.classList.remove('blurred');
        
        // Show actual course content - unlock the modules
        const lockedModules = courseContent.querySelectorAll('.list-group-item.text-muted');
        lockedModules.forEach(module => {
            module.classList.remove('text-muted');
            const lockIcon = module.querySelector('.bi-lock');
            if (lockIcon) {
                lockIcon.classList.remove('bi-lock');
                lockIcon.classList.add('bi-play-circle', 'text-primary');
            }
            const premiumBadge = module.querySelector('.badge.bg-warning');
            if (premiumBadge) {
                premiumBadge.classList.remove('bg-warning');
                premiumBadge.classList.add('bg-success');
                premiumBadge.textContent = 'Available';
            }
        });
        
        showAlert('Course content unlocked! You can now access all modules.', 'success');
    }
}

function showCourseModules() {
    const existingDisplay = document.querySelector('.course-content-display');
    if (existingDisplay) {
        existingDisplay.remove();
    }
    
    const courseDisplay = document.createElement('div');
    courseDisplay.className = 'course-content-display';
    courseDisplay.innerHTML = `
        <div class="course-content-header">
            <h4><i class="bi bi-list-ul me-2"></i>Course Modules</h4>
            <p>Progress through your learning journey</p>
        </div>
        <div class="course-content-body">
            <ul class="module-list">
                <li class="module-item">
                    <div class="module-info">
                        <h6>Module 1: Getting Started</h6>
                        <p>Introduction and basic concepts</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-completed">Completed</span>
                        <button class="btn btn-sm btn-primary">View</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Module 2: Core Concepts</h6>
                        <p>Deep dive into fundamental principles</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-in-progress">In Progress</span>
                        <button class="btn btn-sm btn-primary">Continue</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Module 3: Advanced Topics</h6>
                        <p>Advanced techniques and best practices</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-locked">Locked</span>
                        <button class="btn btn-sm btn-secondary" disabled>Start</button>
                    </div>
                </li>
            </ul>
            <div class="text-center">
                <button class="btn btn-secondary" onclick="this.closest('.course-content-display').remove()">Close</button>
            </div>
        </div>
    `;
    
    document.querySelector('.paywall-body').appendChild(courseDisplay);
    courseDisplay.style.display = 'block';
    courseDisplay.scrollIntoView({ behavior: 'smooth' });
}

function showAssignments() {
    const existingDisplay = document.querySelector('.course-content-display');
    if (existingDisplay) {
        existingDisplay.remove();
    }
    
    const assignmentDisplay = document.createElement('div');
    assignmentDisplay.className = 'course-content-display';
    assignmentDisplay.innerHTML = `
        <div class="course-content-header">
            <h4><i class="bi bi-file-earmark-text me-2"></i>Assignments</h4>
            <p>Your current assignments and deadlines</p>
        </div>
        <div class="course-content-body">
            <ul class="module-list">
                <li class="module-item">
                    <div class="module-info">
                        <h6>Assignment 1: Basic Concepts Review</h6>
                        <p>Due: Tomorrow, 11:59 PM</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-in-progress">Pending</span>
                        <button class="btn btn-sm btn-warning">Start</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Assignment 2: Practical Application</h6>
                        <p>Due: Next week, Friday</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-locked">Locked</span>
                        <button class="btn btn-sm btn-secondary" disabled>Locked</button>
                    </div>
                </li>
            </ul>
            <div class="text-center">
                <button class="btn btn-secondary" onclick="this.closest('.course-content-display').remove()">Close</button>
            </div>
        </div>
    `;
    
    document.querySelector('.paywall-body').appendChild(assignmentDisplay);
    assignmentDisplay.style.display = 'block';
    assignmentDisplay.scrollIntoView({ behavior: 'smooth' });
}

function showResources() {
    const existingDisplay = document.querySelector('.course-content-display');
    if (existingDisplay) {
        existingDisplay.remove();
    }
    
    const resourceDisplay = document.createElement('div');
    resourceDisplay.className = 'course-content-display';
    resourceDisplay.innerHTML = `
        <div class="course-content-header">
            <h4><i class="bi bi-folder me-2"></i>Course Resources</h4>
            <p>Download materials and additional resources</p>
        </div>
        <div class="course-content-body">
            <ul class="module-list">
                <li class="module-item">
                    <div class="module-info">
                        <h6>Course Handbook (PDF)</h6>
                        <p>Complete course guide and reference material</p>
                    </div>
                    <div class="module-status">
                        <button class="btn btn-sm btn-success">Download</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Video Lectures</h6>
                        <p>Recorded sessions for review</p>
                    </div>
                    <div class="module-status">
                        <button class="btn btn-sm btn-primary">Watch</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Practice Exercises</h6>
                        <p>Additional exercises and examples</p>
                    </div>
                    <div class="module-status">
                        <button class="btn btn-sm btn-info">Access</button>
                    </div>
                </li>
            </ul>
            <div class="text-center">
                <button class="btn btn-secondary" onclick="this.closest('.course-content-display').remove()">Close</button>
            </div>
        </div>
    `;
    
    document.querySelector('.paywall-body').appendChild(resourceDisplay);
    resourceDisplay.style.display = 'block';
    resourceDisplay.scrollIntoView({ behavior: 'smooth' });
}

function showQuizzes() {
    const existingDisplay = document.querySelector('.course-content-display');
    if (existingDisplay) {
        existingDisplay.remove();
    }
    
    const quizDisplay = document.createElement('div');
    quizDisplay.className = 'course-content-display';
    quizDisplay.innerHTML = `
        <div class="course-content-header">
            <h4><i class="bi bi-question-circle me-2"></i>Quizzes & Tests</h4>
            <p>Test your knowledge and track progress</p>
        </div>
        <div class="course-content-body">
            <ul class="module-list">
                <li class="module-item">
                    <div class="module-info">
                        <h6>Quiz 1: Basics Assessment</h6>
                        <p>10 questions • 15 minutes • Score: 85%</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-completed">Completed</span>
                        <button class="btn btn-sm btn-outline-primary">Review</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Quiz 2: Core Concepts</h6>
                        <p>15 questions • 20 minutes • Available</p>
                    </div>
                    <div class="module-status">
                        <button class="btn btn-sm btn-warning">Take Quiz</button>
                    </div>
                </li>
                <li class="module-item">
                    <div class="module-info">
                        <h6>Final Exam</h6>
                        <p>50 questions • 90 minutes • Unlock after Module 3</p>
                    </div>
                    <div class="module-status">
                        <span class="status-badge status-locked">Locked</span>
                        <button class="btn btn-sm btn-secondary" disabled>Locked</button>
                    </div>
                </li>
            </ul>
            <div class="text-center">
                <button class="btn btn-secondary" onclick="this.closest('.course-content-display').remove()">Close</button>
            </div>
        </div>
    `;
    
    document.querySelector('.paywall-body').appendChild(quizDisplay);
    quizDisplay.style.display = 'block';
    quizDisplay.scrollIntoView({ behavior: 'smooth' });
}

function contactSupport() {
    showAlert('Please contact support at support@artc.edu.ph or call +63-XXX-XXX-XXXX', 'info');
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

// Initialize paywall on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load payment methods
    loadPaymentMethods();
    
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
        document.querySelectorAll('.payment-form-modal').forEach(modal => modal.remove());
    }
});
</script>

@endsection
