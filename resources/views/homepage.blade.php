@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}
{!! App\Helpers\SettingsHelper::getHomepageCustomStyles() !!}

<style>
    {!! App\Helpers\SettingsHelper::getHomepageStyles() !!}
    
    /* Modern Program Card Design - Inspired by meme card layout */
.program-card-modern {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    max-width: 320px;
    margin: 0 auto;
    border: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    height: 100%;
}

    
    .program-card-modern:hover {
        transform: translateY(-6px);
         box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12);
    }
    
.program-image-container {
    height: 180px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 2.5rem;
}
    
.program-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
    
    .program-card-modern:hover .program-image {
        transform: scale(1.05);
    }
    
    .program-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }
    
.program-content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
    
.program-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}
    
.program-description {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 1rem;
    flex-grow: 1;
}
    
.program-learn-more-btn {
    background: #ffffff;
    border: 2px solid #764ba2;
    color: #764ba2;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    align-self: start;
    text-decoration: none;
}
    
.program-learn-more-btn:hover {
        background: white;
        color: #1a1a1a;
        border-color: white;
    }
    

.carousel-nav {
    background: #ffffff;
    border: 2px solid #764ba2;
    color: #764ba2;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    cursor: pointer;
    transition: all 0.3s ease;
}

.carousel-nav:hover {
    background: #764ba2;
    color: #fff;
}
.carousel-nav.prev-btn {
    left: -22px;
}

.carousel-nav.next-btn {
    right: -22px;
}
.programs-section {
    padding: 60px 0;
    overflow: hidden; /* added */
}

.programs-carousel-container {
    padding: 50px; /* added to contain edge cards */
}
/* Responsive */
@media (max-width: 768px) {
    .program-card-modern {
        max-width: 280px;
    }
    .program-image-container {
        height: 150px;
    }
}
    /* Carousel adjustments for new design */
    .programs-carousel {
        display: flex;
        gap: 30px;
        overflow-x: auto;
        padding: 20px 0;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .programs-carousel::-webkit-scrollbar {
        display: none;
    }
    
    .program-card-wrapper {
        flex: 0 0 auto;
        width: 320px;
    }
    
    /* Center cards when there are only 2 programs */
    .programs-carousel:has(.program-card-wrapper:nth-child(2):last-child) {
        justify-content: center;
    }
    
    /* Alternative for browsers that don't support :has() */
    .programs-carousel.two-cards {
        justify-content: center;
    }
    
    /* Additional centering for exactly 2 cards */
    .program-card-wrapper:nth-child(1):nth-last-child(2),
    .program-card-wrapper:nth-child(2):nth-last-child(1) {
        /* This targets the first card when there are exactly 2 cards total,
           and the second card when there are exactly 2 cards total */
    }
    
    /* Center container when exactly 2 cards */
    .programs-carousel-container:has(.program-card-wrapper:nth-child(2):last-child) .programs-carousel {
        justify-content: center;
    }
    
    @media (max-width: 768px) {
        .program-card-wrapper {
            width: 280px;
        }
        
        .program-card-modern {
            max-width: 280px;
        }
        
        .program-image-container {
            height: 160px;
        }
    }
    
    /* Professional Modal Styles */
    .program-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .program-modal-content {
        background: white;
        border-radius: 16px;
        max-width: 700px;
        width: 100%;
        max-height: 85vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .program-modal-header {
        padding: 2rem 2rem 1rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .program-modal-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6c757d;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.3s ease;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .close-modal:hover {
        background: #f8f9fa;
        color: #495057;
    }
    
    .program-description {
        padding: 1rem 2rem;
        color: #6c757d;
        line-height: 1.6;
        font-size: 1rem;
    }
    
    .modules-section {
        padding: 1rem 2rem 2rem;
    }
    
    .modules-section h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .module-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .module-item:hover {
        background: #e9ecef;
        transform: translateX(2px);
    }
    
    .module-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .module-description {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }
    
    .courses-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .courses-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
    
    .courses-list {
        display: grid;
        gap: 0.5rem;
    }
    
    .course-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .course-item:hover {
        border-color: #667eea;
        background: #f8f9fb;
    }
    
    .course-name {
        font-weight: 500;
        color: #2c3e50;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .course-description {
        color: #6c757d;
        font-size: 0.85rem;
        line-height: 1.4;
    }
    
    .no-modules-message {
        text-align: center;
        color: #adb5bd;
        font-style: italic;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 10px;
        border: 2px dashed #dee2e6;
    }
    
    /* Responsive Modal */
    @media (max-width: 768px) {
        .program-modal {
            padding: 1rem;
        }
        
        .program-modal-content {
            max-height: 90vh;
        }
        
        .program-modal-header,
        .program-description,
        .modules-section {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .program-modal-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
@php
    use App\Models\Module;
    // Note: $programs and $homepageTitle are now passed from HomepageController
$homepageContent = \App\Helpers\SettingsHelper::getHomepageContent();
@endphp

<!-- Hero Section -->
<section class="homepage-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-text">
                    <h1 class="hero-title fade-in-up display-3 fw-bold">
                        {!! $homepageContent['hero_title'] !!}
                    </h1>
                    <p class="hero-subtitle fade-in-up lead mb-4">
                        {{ $homepageContent['hero_subtitle'] }}
                    </p>
                    <a href="{{ url('/enrollment') }}" class="btn btn-lg btn-success enroll-btn fade-in-up">
                        <i class="bi bi-mortarboard me-2"></i>{{ $homepageContent['hero_button_text'] }}
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
            <h2 class="display-4 fw-bold text-dark">{{ $homepageContent['programs_title'] }}</h2>
            <p class="lead text-muted">{{ $homepageContent['programs_subtitle'] }}</p>
        </div>
        
        @if($programs->count() > 0)
            <!-- Programs Carousel Container -->
            <div class="programs-carousel-container position-relative">
                <div class="programs-carousel" id="programsCarousel">
                    @foreach($programs as $program)
                    <div class="program-card-wrapper">
                        <div class="program-card-modern">
                            <!-- Program Image -->
                            <div class="program-image-container">
                                @if(isset($program->program_image) && $program->program_image)
                                    <img src="{{ asset('storage/program-images/' . $program->program_image) }}" 
                                         alt="{{ $program->program_name }}" 
                                         class="program-image">
                                @else
                                    <div class="program-image-placeholder">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Program Content -->
                            <div class="program-content">
                                <h3 class="program-title">{{ $program->program_name }}</h3>
                                <p class="program-description">
                                    @if(!empty($program->program_description))
                                        {{ Str::limit($program->program_description, 120) }}
                                    @else
                                        Discover comprehensive learning opportunities designed to advance your career and knowledge in this specialized field.
                                    @endif
                                </p>
                                <button class="program-learn-more-btn" onclick="showProgramDetails({{ $program->program_id }})">
                                    Learn more
                                </button>
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



<!-- Available Modalities Section -->
<section class="modalities-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">{{ $homepageContent['modalities_title'] }}</h2>
            <p class="lead opacity-75">{{ $homepageContent['modalities_subtitle'] }}</p>
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

    <!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-4 fw-bold text-dark mb-4">{{ $homepageContent['about_title'] }}</h2>
                <p class="lead text-muted">
                    {{ $homepageContent['about_subtitle'] }}
                </p>
            </div>
        </div>
    </div>
</section>
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
        .then(data => {
            const modulesList = document.getElementById('modulesList');
            
            if (data.success && data.modules && data.modules.length > 0) {
                let html = '';
                data.modules.forEach(module => {
                    html += `
                        <div class="module-item">
                            <div class="module-name">
                                <i class="bi bi-collection me-2"></i>${module.module_name || module.name}
                            </div>
                            <div class="module-description">${module.module_description || module.description || 'No description available.'}</div>
                    `;
                    
                    // Add courses if they exist
                    if (module.courses && module.courses.length > 0) {
                        html += '<div class="courses-section">';
                        html += '<div class="courses-title"><i class="bi bi-book me-1"></i>Courses:</div>';
                        html += '<div class="courses-list">';
                        module.courses.forEach(course => {
                            html += `
                                <div class="course-item">
                                    <div class="course-name">${course.course_name}</div>
                                    ${course.description ? `<div class="course-description">${course.description}</div>` : ''}
                                </div>
                            `;
                        });
                        html += '</div></div>';
                    }
                    
                    html += '</div>';
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
    
    // Center cards if there are exactly 2
    const carousel = document.getElementById('programsCarousel');
    if (carousel) {
        const cardCount = carousel.querySelectorAll('.program-card-wrapper').length;
        
        if (cardCount === 2) {
            carousel.classList.add('two-cards');
            // Force centering for 2 cards
            carousel.style.justifyContent = 'center';
        } else if (cardCount === 1) {
            // Also center single card
            carousel.style.justifyContent = 'center';
        } else {
            // Default alignment for 3+ cards
            carousel.style.justifyContent = 'flex-start';
        }
    }
    
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
