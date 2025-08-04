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
                        <a href="{{ route('enrollment.full') }}" 
                                class="btn enrollment-btn enroll-btn">
                            <i class="bi bi-mortarboard"></i> Enroll Now
                        </a>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-5 d-flex">
                    <div class="enrollment-card program-card enrollment-program-card h-100 w-100">
                        <h3>Modular Plan</h3>
                        <p class="text-muted mb-4">Flexible modules tailored to your specific needs</p>
                        <!-- Direct link with both route() helper and explicit URL -->
                        <a href="{{ route('enrollment.modular') }}" 
                                class="btn enrollment-btn enroll-btn" 
                                id="modular-enroll-btn"
                                data-url="/enrollment/modular"
                                data-target="modular_enrollment"
                                onclick="window.location.href='/enrollment/modular'; return false;">
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
                                            window.location.href = '/enrollment/modular';
                                            return false;
                                        } catch (err) {
                                            console.error('Method 1 failed:', err);
                                        }
                                        
                                        // Method 2: Open in same window
                                        try {
                                            window.open('/enrollment/modular', '_self');
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
                                            form.action = '/enrollment/modular';
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
                                    '/enrollment/modular',
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
