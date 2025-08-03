/**
 * Mobile-Optimized Admin Modules JavaScript
 * Enhanced for touch devices and responsive design
 */

// Global variables for mobile optimization
let isMobile = window.innerWidth <= 768;
let isTouch = 'ontouchstart' in window;
let currentModule = null;
let loadingTimeout = null;

// Mobile detection and setup
function initMobileOptimizations() {
    // Add mobile class to body for CSS targeting
    if (isMobile) {
        document.body.classList.add('mobile-device');
    }
    
    if (isTouch) {
        document.body.classList.add('touch-device');
    }
    
    // Add viewport meta tag if missing
    if (!document.querySelector('meta[name="viewport"]')) {
        const viewport = document.createElement('meta');
        viewport.name = 'viewport';
        viewport.content = 'width=device-width, initial-scale=1.0, user-scalable=no';
        document.head.appendChild(viewport);
    }
    
    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            isMobile = window.innerWidth <= 768;
            handleResponsiveChanges();
        }, 100);
    });
    
    // Handle resize for responsive testing
    window.addEventListener('resize', debounce(function() {
        isMobile = window.innerWidth <= 768;
        handleResponsiveChanges();
    }, 250));
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle responsive layout changes
function handleResponsiveChanges() {
    const moduleGrid = document.querySelector('.modules-grid');
    if (moduleGrid) {
        if (isMobile) {
            moduleGrid.style.gridTemplateColumns = '1fr';
        } else {
            moduleGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(350px, 1fr))';
        }
    }
    
    // Adjust touch targets for mobile
    const buttons = document.querySelectorAll('.action-btn, .btn');
    buttons.forEach(btn => {
        if (isMobile && isTouch) {
            btn.style.minHeight = '48px';
            btn.style.minWidth = '48px';
            btn.style.padding = '12px 20px';
        }
    });
}

// Enhanced module content loading with mobile optimizations
function loadModuleContent(moduleId) {
    // Clear any existing timeout
    if (loadingTimeout) {
        clearTimeout(loadingTimeout);
    }
    
    // Show loading state optimized for mobile
    showMobileLoadingState(moduleId);
    
    // Prepare request with proper headers
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    
    // Add CSRF token if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }
    
    // Enhanced fetch with timeout and error handling
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
    
    fetch(`/admin/modules/${moduleId}/content`, {
        method: 'GET',
        headers: headers,
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned HTML instead of JSON - likely an authentication redirect');
        }
        
        return response.json();
    })
    .then(data => {
        hideMobileLoadingState();
        
        if (data.success) {
            displayModuleContent(data, moduleId);
            currentModule = moduleId;
            
            // Mobile-specific success feedback
            if (isTouch) {
                provideTouchFeedback('success');
            }
            
            console.log('✓ Module content loaded successfully', data);
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        hideMobileLoadingState();
        
        console.error('✗ API Error:', error);
        
        // Enhanced error handling for mobile
        if (error.name === 'AbortError') {
            showMobileError('Request timed out. Please check your connection.');
        } else if (error.message.includes('authentication redirect')) {
            showMobileError('Session expired. Please log in again.', true);
        } else {
            showMobileError(`Error: ${error.message}`);
        }
        
        // Mobile-specific error feedback
        if (isTouch) {
            provideTouchFeedback('error');
        }
    });
}

// Mobile-optimized loading state
function showMobileLoadingState(moduleId) {
    const moduleCard = document.querySelector(`[data-module-id="${moduleId}"]`);
    if (moduleCard) {
        moduleCard.classList.add('loading');
        
        // Add spinner optimized for mobile
        const spinner = document.createElement('div');
        spinner.className = 'mobile-spinner';
        spinner.innerHTML = `
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="loading-text">Loading content...</span>
        `;
        
        moduleCard.appendChild(spinner);
    }
    
    // Set timeout for loading state
    loadingTimeout = setTimeout(() => {
        hideMobileLoadingState();
        showMobileError('Loading is taking longer than expected...');
    }, 15000);
}

// Hide mobile loading state
function hideMobileLoadingState() {
    document.querySelectorAll('.mobile-spinner').forEach(spinner => {
        spinner.remove();
    });
    
    document.querySelectorAll('.loading').forEach(element => {
        element.classList.remove('loading');
    });
    
    if (loadingTimeout) {
        clearTimeout(loadingTimeout);
        loadingTimeout = null;
    }
}

// Enhanced error display for mobile
function showMobileError(message, isAuthError = false) {
    // Remove existing error messages
    document.querySelectorAll('.mobile-error-toast').forEach(toast => {
        toast.remove();
    });
    
    const errorToast = document.createElement('div');
    errorToast.className = `mobile-error-toast ${isAuthError ? 'auth-error' : ''}`;
    errorToast.innerHTML = `
        <div class="error-content">
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
            ${isAuthError ? '<button onclick="location.reload()" class="btn btn-sm btn-primary">Reload Page</button>' : ''}
        </div>
        <button class="close-error" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(errorToast);
    
    // Auto-remove after 5 seconds (unless it's an auth error)
    if (!isAuthError) {
        setTimeout(() => {
            if (errorToast.parentElement) {
                errorToast.remove();
            }
        }, 5000);
    }
}

// Touch feedback for mobile devices
function provideTouchFeedback(type) {
    if (!isTouch) return;
    
    // Haptic feedback if available
    if (navigator.vibrate) {
        if (type === 'success') {
            navigator.vibrate([50]); // Short vibration for success
        } else if (type === 'error') {
            navigator.vibrate([100, 50, 100]); // Pattern for error
        }
    }
    
    // Visual feedback
    const feedback = document.createElement('div');
    feedback.className = `touch-feedback ${type}`;
    feedback.innerHTML = type === 'success' ? '✓' : '✗';
    
    document.body.appendChild(feedback);
    
    setTimeout(() => {
        feedback.remove();
    }, 1000);
}

// Enhanced content display for mobile
function displayModuleContent(data, moduleId) {
    const contentContainer = document.getElementById('module-content-container');
    if (!contentContainer) return;
    
    // Mobile-optimized content layout
    const mobileLayout = isMobile ? 'mobile-layout' : '';
    
    let html = `
        <div class="module-content ${mobileLayout}">
            <div class="content-header">
                <h3>${data.module.module_name}</h3>
                ${isMobile ? '<button class="btn btn-sm btn-secondary close-content" onclick="closeModuleContent()">Close</button>' : ''}
            </div>
    `;
    
    if (data.courses && data.courses.length > 0) {
        html += '<div class="courses-grid">';
        
        data.courses.forEach(course => {
            html += `
                <div class="course-card ${isMobile ? 'mobile-course-card' : ''}">
                    <h4>${course.subject_name}</h4>
                    <p>Course ID: ${course.subject_id}</p>
                    <button class="btn btn-primary ${isMobile ? 'btn-mobile' : ''}" 
                            onclick="loadCourseContent(${moduleId}, ${course.subject_id})">
                        View Content
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
    } else {
        html += '<p class="no-content">No courses found for this module.</p>';
    }
    
    html += '</div>';
    
    contentContainer.innerHTML = html;
    contentContainer.style.display = 'block';
    
    // Mobile-specific scrolling
    if (isMobile) {
        contentContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Close content for mobile
function closeModuleContent() {
    const contentContainer = document.getElementById('module-content-container');
    if (contentContainer) {
        contentContainer.style.display = 'none';
        contentContainer.innerHTML = '';
    }
    currentModule = null;
}

// Test function for mobile API endpoints
function testMobileAPIEndpoints() {
    console.log('🧪 Testing Mobile API Endpoints...');
    
    // Test with first available module
    const firstModule = document.querySelector('[data-module-id]');
    if (firstModule) {
        const moduleId = firstModule.getAttribute('data-module-id');
        console.log(`Testing with module ID: ${moduleId}`);
        loadModuleContent(moduleId);
    } else {
        console.log('❌ No modules found for testing');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Mobile-Optimized Admin Modules...');
    
    initMobileOptimizations();
    
    // Add mobile-specific event listeners
    if (isTouch) {
        // Handle touch events for better mobile experience
        document.addEventListener('touchstart', function() {
            // Prevents 300ms click delay on mobile
        }, { passive: true });
    }
    
    console.log(`📱 Mobile: ${isMobile}, Touch: ${isTouch}`);
    console.log('✅ Mobile optimizations initialized');
    
    // Add test button for development
    if (window.location.search.includes('test=1')) {
        const testBtn = document.createElement('button');
        testBtn.className = 'btn btn-warning mobile-test-btn';
        testBtn.innerHTML = '🧪 Test Mobile API';
        testBtn.onclick = testMobileAPIEndpoints;
        testBtn.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
            min-height: 44px;
            min-width: 44px;
        `;
        document.body.appendChild(testBtn);
    }
});

// Add mobile-specific CSS
const mobileCSS = `
<style>
.mobile-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    margin: 10px 0;
}

.mobile-error-toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #dc3545;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 10000;
    max-width: calc(100vw - 40px);
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease;
}

.mobile-error-toast.auth-error {
    background: #fd7e14;
}

.mobile-error-toast .close-error {
    background: none;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    padding: 0;
    margin-left: auto;
    min-height: 32px;
    min-width: 32px;
}

.touch-feedback {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 48px;
    font-weight: bold;
    z-index: 10001;
    pointer-events: none;
    animation: fadeOut 1s ease;
}

.touch-feedback.success {
    color: #28a745;
}

.touch-feedback.error {
    color: #dc3545;
}

@keyframes slideDown {
    from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
    to { transform: translateX(-50%) translateY(0); opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    to { opacity: 0; transform: translate(-50%, -50%) scale(1.2); }
}

.mobile-device .module-content {
    padding: 15px;
    border-radius: 8px;
}

.mobile-device .courses-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.mobile-device .course-card {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: white;
}

.mobile-device .btn-mobile {
    width: 100%;
    min-height: 44px;
    font-size: 1rem;
}

.touch-device .action-btn:active {
    transform: scale(0.95);
    transition: transform 0.1s ease;
}

@media (max-width: 768px) {
    .mobile-test-btn {
        top: 60px !important;
        right: 10px !important;
        font-size: 0.8rem;
        padding: 8px 12px;
    }
}
</style>
`;

// Inject mobile CSS
document.head.insertAdjacentHTML('beforeend', mobileCSS);
