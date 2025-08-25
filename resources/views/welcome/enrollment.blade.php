@extends('layouts.navbar')

@section('title', 'Enrollment')
@section('hide_footer', true)

@push('styles')

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
{!! App\Helpers\SettingsHelper::getEnrollmentStyles() !!}
{!! App\Helpers\SettingsHelper::getProgramCardStyles() !!}
{!! App\Helpers\SettingsHelper::getButtonStyles() !!}

body {
    padding-top: 0 !important;
}

/* ENROLLMENT PAGE SPECIFIC DROPDOWN FIXES */
.enrollment-page .navbar .dropdown-menu {
    background: white !important;
    background-color: white !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    border-radius: 8px !important;
    padding: 8px 0 !important;
    margin-top: 8px !important;
    color: #222 !important;
    max-height: none !important;
    overflow: visible !important;
    transform: none !important;
    transition: none !important;
    width: auto !important;
    min-width: 200px !important;
    z-index: 1050 !important;
    position: absolute !important;
    display: none !important;
}

.enrollment-page .navbar .dropdown-menu.show {
    display: block !important;
}

.enrollment-page .navbar .dropdown-menu .dropdown-item {
    padding: 10px 20px !important;
    transition: all 0.2s ease !important;
    background: transparent !important;
    border: none !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-weight: 500 !important;
    color: #222 !important;
    opacity: 1 !important;
    transform: none !important;
    pointer-events: auto !important;
    text-align: left !important;
    white-space: normal !important;
    box-sizing: border-box !important;
}

.enrollment-page .navbar .dropdown-menu .dropdown-item:hover {
    background: rgba(92, 47, 145, 0.1) !important;
    color: #5c2f91 !important;
    transform: translateX(5px) !important;
    border-radius: 0 !important;
}

.enrollment-page .navbar .dropdown-menu .dropdown-item:focus,
.enrollment-page .navbar .dropdown-menu .dropdown-item:active {
    background: rgba(92, 47, 145, 0.1) !important;
    color: #5c2f91 !important;
    outline: none !important;
    box-shadow: none !important;
}

.enrollment-page .navbar .dropdown-menu .dropdown-item.text-danger:hover {
    background: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545 !important;
}

.enrollment-page .navbar .dropdown-menu .dropdown-divider {
    margin: 8px 0 !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
}

/* Force override any enrollment page specific styles that might interfere */
.enrollment-page .navbar-nav .dropdown {
    position: relative !important;
}

.enrollment-page .navbar-nav .dropdown-toggle::after {
    display: inline-block !important;
    margin-left: 0.255em !important;
    vertical-align: 0.255em !important;
    content: "" !important;
    border-top: 0.3em solid !important;
    border-right: 0.3em solid transparent !important;
    border-bottom: 0 !important;
    border-left: 0.3em solid transparent !important;
}

.enrollment-page .navbar-nav .dropdown-toggle[aria-expanded="true"]::after {
    transform: rotate(180deg) !important;
}

/* Ensure proper positioning for right-aligned dropdowns */
.enrollment-page .navbar-nav.ms-auto .dropdown-menu {
    right: 0 !important;
    left: auto !important;
    transform: none !important;
}

.enrollment-hero {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 0;
    padding-bottom: 0;
    flex-direction: column;
}

.enrollment-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* ⬅️ Important to push button to bottom */
    height: 100%; /* ⬅️ Make card take full height */
}

.enrollment-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    border-color: rgba(102, 126, 234, 0.3);
}

.enrollment-card h3 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: inherit;
}

.enrollment-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.enrollment-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a67d8 0%, #6c46a0 100%);
}

.enrollment-btn:active {
    transform: translateY(0);
}

.page-title {
    text-align: center;
    margin-bottom: 2rem;
    color: inherit;
}

.page-title h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-title p {
    font-size: 1.1rem;
    opacity: 0.8;
    margin: 0;
}

.enrollment-card .form-group {
    margin-bottom: 1.2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .enrollment-hero {
        padding-top: 30px;
        padding-bottom: 30px;
    }
    .enrollment-card {
        margin-bottom: 2rem;
        padding: 2rem 1.5rem;
        min-height: 240px;
    }
    .enrollment-card h3 {
        font-size: 1.75rem;
    }
    .enrollment-btn {
        padding: 0.6rem 2rem;
        font-size: 1rem;
    }
    .page-title h1 {
        font-size: 2rem;
    }
    .page-title p {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .enrollment-card {
        min-height: 200px;
        padding: 1.5rem 1rem;
    }
    .enrollment-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .enrollment-btn {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="enrollment-hero">
        <div class="container">
            <div class="page-title">
                <h1>Choose Your Learning Path</h1>
                <p>Select the program that best fits your educational goals</p>
            </div>

            <div class="row justify-content-center g-4">
                <div class="col-12 col-md-6 col-lg-5 d-flex">
                    <div class="enrollment-card program-card enrollment-program-card h-100 w-100">
                        <h3>Complete Plan</h3>
                        <p class="text-muted mb-4">Comprehensive program covering all essential topics</p>
                        <a href="{{ tenant_enrollment_url('full') }}" 
                                class="btn enrollment-btn enroll-btn">
                            <i class="bi bi-mortarboard"></i> Enroll Now
                        </a>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-5 d-flex">
                    <div class="enrollment-card program-card enrollment-program-card h-100 w-100">
                        <h3>Modular Plan</h3>
                        <p class="text-muted mb-4">Flexible modules tailored to your specific needs</p>
                        <!-- Tenant-aware enrollment link -->
                        <a href="{{ tenant_enrollment_url('modular') }}" 
                                class="btn enrollment-btn enroll-btn" 
                                id="modular-enroll-btn"
                                data-url="{{ tenant_enrollment_url('modular') }}"
                                data-target="modular_enrollment"
                                onclick="window.location.href='{{ tenant_enrollment_url('modular') }}'; return false;">
                            <i class="bi bi-puzzle"></i> Enroll Now
                        </a>

                        
                        <script>
                            // Enhanced modular enrollment button handler with multiple backup methods
                            document.addEventListener('DOMContentLoaded', function() {
                                var modularBtn = document.getElementById('modular-enroll-btn');
                                if (modularBtn) {
                                    console.log('Found modular button, adding reliable navigation handler');
                                    
                                    modularBtn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        console.log('Modular button clicked, attempting multi-approach navigation');
                                        
                                        // Method 1: Direct assignment to location.href
                                        try {
                                            window.location.href = '{{ tenant_enrollment_url('modular') }}';
                                            return false;
                                        } catch (err) {
                                            console.error('Method 1 failed:', err);
                                        }
                                        
                                        // Method 2: Open in same window
                                        try {
                                            window.open('{{ tenant_enrollment_url('modular') }}', '_self');
                                            return false;
                                        } catch (err) {
                                            console.error('Method 2 failed:', err);
                                        }
                                        
                                        // Method 3: Try the PHP helper
                                        try {
                                            window.location.href = '/direct-to-modular.php';
                                            return false;
                                        } catch (err) {
                                            console.error('Method 3 failed:', err);
                                        }
                                        
                                        // Method 4: Form submission approach
                                        try {
                                            var form = document.createElement('form');
                                            form.method = 'GET';
                                            form.action = '{{ tenant_enrollment_url('modular') }}';
                                            document.body.appendChild(form);
                                            form.submit();
                                            return false;
                                        } catch (err) {
                                            console.error('Method 4 failed:', err);
                                            alert('Navigation failed. Please try the Alternative Link below.');
                                        }
                                        
                                        return false;
                                    });
                                }
                            });
                        </script>
                        
                        <!-- Additional helper script for reliable navigation -->
                        <script>
                            // Backup navigation function
                            function tryModularEnrollment() {
                                console.log('Backup navigation function called');
                                
                                // Try each route in sequence
                                const tryRoutes = [
                                    '{{ tenant_enrollment_url('modular') }}',
                                    '/modular-enrollment',
                                    '/emergency-modular',
                                    '/go-to-modular.php',
                                    '/test-modular.php'
                                ];
                                        
                                        let currentRoute = 0;
                                        
                                        function tryNextRoute() {
                                            if (currentRoute >= tryRoutes.length) {
                                                alert('Unable to access modular enrollment. Please try again later or contact support.');
                                                return;
                                            }
                                            
                                            console.log('Trying route:', tryRoutes[currentRoute]);
                                            
                                            // Try to navigate to the route
                                            window.location.href = tryRoutes[currentRoute];
                                            
                                            // Set a timeout to try the next route if navigation didn't work
                                            setTimeout(function() {
                                                currentRoute++;
                                                tryNextRoute();
                                            }, 3000); // Try next route after 3 seconds
                                        }
                                        
                                        // Start the navigation attempt
                                        tryNextRoute();
                                        return false;
                                }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// ENROLLMENT PAGE SPECIFIC DROPDOWN FIXES
document.addEventListener('DOMContentLoaded', function() {
    // Ensure Bootstrap dropdowns are properly initialized
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Additional dropdown functionality for enrollment page
    var dropdownToggles = document.querySelectorAll('.navbar-nav .dropdown-toggle');
    
    dropdownToggles.forEach(function(toggle) {
        // Ensure proper event handling
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var dropdownMenu = this.nextElementSibling;
            var isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Close all other dropdowns first
            document.querySelectorAll('.navbar-nav .dropdown-menu.show').forEach(function(menu) {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle current dropdown
            if (isExpanded) {
                dropdownMenu.classList.remove('show');
                this.setAttribute('aria-expanded', 'false');
            } else {
                dropdownMenu.classList.add('show');
                this.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Handle dropdown item clicks
        var dropdownMenu = toggle.nextElementSibling;
        if (dropdownMenu) {
            var dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(function(item) {
                item.addEventListener('click', function(e) {
                    // Close dropdown after item click
                    setTimeout(function() {
                        dropdownMenu.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                    }, 100);
                });
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.navbar-nav .dropdown-menu.show').forEach(function(menu) {
                menu.classList.remove('show');
                menu.previousElementSibling.setAttribute('aria-expanded', 'false');
            });
        }
    });
    
    // Close dropdowns on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.navbar-nav .dropdown-menu.show').forEach(function(menu) {
                menu.classList.remove('show');
                menu.previousElementSibling.setAttribute('aria-expanded', 'false');
            });
        }
    });
    
    console.log('Enrollment page dropdown fixes applied');
});
</script>
@endpush

