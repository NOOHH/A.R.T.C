@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
    {!! App\Helpers\SettingsHelper::getHomepageStyles() !!}
    
    /* Override to ensure full width coverage */
    .homepage-hero {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 120px 0 80px 0;
    }
    
    .programs-section {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        background: #f8f9fa;
        padding: 80px 0;
    }
    
    .about-section {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        background: white;
        padding: 80px 0;
    }
    
    .modalities-section {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
    }
    
    /* Programs Carousel Styles */
    .programs-carousel-container {
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        padding: 0 4rem;
    }
    
    .programs-carousel {
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
    
    .programs-carousel::-webkit-scrollbar {
        display: none;
    }
    
    .programs-carousel.grabbing {
        cursor: grabbing;
    }
    
    .program-card-wrapper {
        scroll-snap-align: start;
        flex-shrink: 0;
        min-width: 350px;
    }
    }
    
    .program-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .program-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
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
        .programs-carousel-container {
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
        
        .program-card-wrapper {
            min-width: 280px;
        }
    }
</style>
@endpush

@section('content')
@php
    use App\Models\Module;
    // Note: $programs and $homepageTitle are now passed from HomepageController
@endphp

<!-- Hero Section -->
<section class="homepage-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-text">
                    <h1 class="hero-title fade-in-up display-3 fw-bold">
                        Review <span style="color: #4CAF50;">Smarter.</span><br>
                        Learn <span style="color: #4CAF50;">Better.</span><br>
                        Succeed <span style="color: #4CAF50;">Faster.</span>
                    </h1>
                    <p class="hero-subtitle fade-in-up lead mb-4">
                        At Ascendo Review and Training Center, we guide future licensed professionals 
                        toward exam success with expert-led reviews and flexible learning options.
                    </p>
                    <a href="{{ url('/enrollment') }}" class="btn btn-lg btn-success enroll-btn fade-in-up">
                        <i class="bi bi-mortarboard me-2"></i>{{ $homepageTitle }}
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image fade-in-up text-center">
                    <img src="{{ asset('images/Home page image.png') }}" alt="Student studying with laptop" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Offered Section -->
<section class="programs-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold text-dark">Programs Offered</h2>
            <p class="lead text-muted">Choose from our comprehensive review programs designed for success</p>
        </div>
        
        @if($programs->count() > 0)
            <!-- Programs Carousel Container -->
            <div class="programs-carousel-container position-relative">
                <div class="programs-carousel" id="programsCarousel">
                    @foreach($programs as $program)
                    <div class="program-card-wrapper">
                        <div class="card program-card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary fw-bold">{{ $program->program_name }}</h5>
                                <p class="card-text text-muted flex-grow-1">
                                    @if(!empty($program->program_description))
                                        {{ Str::limit($program->program_description, 150) }}
                                    @else
                                        No description yet.
                                    @endif
                                </p>
                                <div class="mt-auto">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="showProgramDetails({{ $program->program_id }})">
                                            <i class="bi bi-eye me-1"></i>Quick View
                                        </button>
                                        <a href="{{ route('programs.show', $program->program_id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-arrow-right me-1"></i>Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Navigation Arrows -->
                @if($programs->count() > 3)
                <button class="carousel-nav prev-btn" onclick="scrollPrograms('left')">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="carousel-nav next-btn" onclick="scrollPrograms('right')">
                    <i class="bi bi-chevron-right"></i>
                </button>
                @endif
                
                <!-- View All Programs Button -->
                <div class="text-center mt-4">
                    <a href="{{ route('review-programs') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-grid me-2"></i>View All Programs
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-book display-1 text-muted"></i>
                <h4 class="mt-3">No Programs Available</h4>
                <p class="text-muted">No programs are currently offered. Please check back later.</p>
            </div>
        @endif
    </div>
</section>

<!-- Program Details Modal -->
<div id="programDetailsModal" class="program-modal">
    <div class="program-modal-content">
        <div class="program-modal-header">
            <h3 class="program-modal-title" id="modalProgramName">Program Details</h3>
            <button type="button" class="close-modal" onclick="closeProgramModal()">&times;</button>
        </div>
        <div id="programDescription" class="program-description">
            Loading program details...
        </div>
        <div class="modules-section">
            <h4>Modules in this Program</h4>
            <div id="modulesList">
                <div class="no-modules-message">Loading modules...</div>
            </div>
        </div>
    </div>
</div>

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-4 fw-bold text-dark mb-4">ABOUT US</h2>
                <p class="lead text-muted">
                    Ascendo Review and Training Center is dedicated to providing exceptional review programs 
                    and training services for professional licensure examinations. With experienced instructors, 
                    comprehensive materials, and proven methodologies, we help aspiring professionals achieve 
                    their career goals through excellence in education and training.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Available Modalities Section -->
<section class="modalities-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Available Modalities</h2>
            <p class="lead opacity-75">Choose the learning style that fits your schedule and preferences</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100 bg-transparent border-light text-white">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-laptop display-3"></i>
                        </div>
                        <h3 class="card-title fw-bold mb-3">Synchronous</h3>
                        <p class="card-text">Real-time interactive online classes with live instructors. Participate in discussions, 
                           ask questions instantly, and engage with fellow students in a virtual classroom environment.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card h-100 bg-transparent border-light text-white">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-clock-history display-3"></i>
                        </div>
                        <h3 class="card-title fw-bold mb-3">Asynchronous</h3>
                        <p class="card-text">Self-paced learning with recorded lectures and materials available 24/7. Study at your 
                           own convenience with full access to comprehensive review materials and practice tests.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Program Details Modal Functions
function showProgramDetails(programId) {
    // Clear previous content
    document.getElementById('modalProgramName').textContent = 'Loading...';
    document.getElementById('programDescription').innerHTML = 'Loading program details...';
    document.getElementById('modulesList').innerHTML = '<div class="no-modules-message">Loading modules...</div>';
    
    // Show modal
    document.getElementById('programDetailsModal').style.display = 'flex';
    
    // Fetch program details using AJAX
    fetch(`/api/programs/${programId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(program => {
            // Update modal content
            document.getElementById('modalProgramName').textContent = program.program_name;
            document.getElementById('programDescription').innerHTML = program.program_description || 'No description available.';
            
            // Fetch modules for this program
            return fetch(`/api/programs/${programId}/modules`);
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(modules => {
            const modulesList = document.getElementById('modulesList');
            
            if (modules.length > 0) {
                let html = '';
                modules.forEach(module => {
                    html += `
                        <div class="module-item">
                            <div class="module-name">${module.module_name}</div>
                            <div class="module-description">${module.module_description || 'No description available.'}</div>
                        </div>
                    `;
                });
                modulesList.innerHTML = html;
            } else {
                modulesList.innerHTML = '<div class="no-modules-message">No modules found for this program.</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching program details:', error);
            document.getElementById('modalProgramName').textContent = 'Error';
            document.getElementById('programDescription').innerHTML = 'Failed to load program details.';
            document.getElementById('modulesList').innerHTML = '<div class="no-modules-message">Failed to load modules.</div>';
        });
}

function closeProgramModal() {
    document.getElementById('programDetailsModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.addEventListener('click', function(event) {
    const modal = document.getElementById('programDetailsModal');
    if (event.target === modal) {
        closeProgramModal();
    }
});

// Programs Carousel Functionality
function scrollPrograms(direction) {
    const carousel = document.getElementById('programsCarousel');
    const cardWidth = 350 + 32; // Card width + gap
    const scrollAmount = cardWidth * 2; // Scroll by 2 cards for better navigation
    
    if (direction === 'left') {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Enable drag scrolling for programs carousel
function initCarouselDragScroll() {
    const carousel = document.getElementById('programsCarousel');
    if (!carousel) return;
    
    let isDown = false;
    let startX;
    let scrollLeft;

    carousel.addEventListener('mousedown', (e) => {
        isDown = true;
        carousel.classList.add('grabbing');
        startX = e.pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
        e.preventDefault();
    });

    carousel.addEventListener('mouseleave', () => {
        isDown = false;
        carousel.classList.remove('grabbing');
    });

    carousel.addEventListener('mouseup', () => {
        isDown = false;
        carousel.classList.remove('grabbing');
    });

    carousel.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 2;
        carousel.scrollLeft = scrollLeft - walk;
    });

    // Touch events for mobile
    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
    });

    carousel.addEventListener('touchmove', (e) => {
        const x = e.touches[0].pageX - carousel.offsetLeft;
        const walk = (x - startX) * 2;
        carousel.scrollLeft = scrollLeft - walk;
    });
}

// Animation on scroll and immediate visibility
document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel drag scrolling
    initCarouselDragScroll();
    
    // Make sure all content is visible immediately
    document.querySelectorAll('.fade-in-up').forEach(el => {
        el.style.animationPlayState = 'running';
        el.style.opacity = '1';
        el.style.visibility = 'visible';
    });

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all fade-in-up elements
    document.querySelectorAll('.fade-in-up').forEach(el => {
        observer.observe(el);
    });

    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>
@endpush
