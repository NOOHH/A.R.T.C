@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
{!! App\Helpers\SettingsHelper::getHomepageStyles() !!}

/* Mobile-First Responsive Homepage Styles */
/* ==== MOBILE DEVICES (320px - 767px) ==== */
.homepage-hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
    padding: 2rem 1rem; /* Mobile: Smaller padding */
}

.homepage-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    z-index: -1;
}

.hero-content {
    position: relative;
    z-index: 2;
    color: white;
    max-width: 100%;
    width: 100%;
}

.hero-main-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-text {
    flex: 1;
    max-width: 500px;
    text-align: center;
}

.hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.6;
}

.enroll-btn {
    display: inline-block;
    background: #4CAF50;
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.enroll-btn:hover {
    background: #45a049;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
    text-decoration: none;
    color: white;
}

.hero-image {
    flex: 1;
    max-width: 300px;
    width: 100%;
}

.hero-image img {
    width: 100%;
    height: auto;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

/* Programs Section - Mobile First */
.programs-section {
    padding: 4rem 1rem;
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
}

.programs-container {
    max-width: 1200px;
    margin: 0 auto;
}

.programs-title {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 3rem;
    color: white;
}

.programs-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.program-card {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    color: #333;
}

.program-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.program-card h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #6f42c1;
}

.program-card p {
    line-height: 1.6;
    margin-bottom: 1.5rem;
    color: #666;
    font-size: 0.95rem;
}

.learn-more-btn {
    background: #6f42c1;
    color: white;
    padding: 0.7rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
    font-size: 0.9rem;
}

.learn-more-btn:hover {
    background: #5a2d9f;
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

/* About Section */
.about-section {
    padding: 4rem 1rem;
    background: #f8f9fa;
    text-align: center;
}

.about-container {
    max-width: 800px;
    margin: 0 auto;
}

.about-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.about-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #666;
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
}

/* Available Modalities Section - Mobile First */
.modalities-section {
    padding: 4rem 1rem;
    background: linear-gradient(135deg, #8e44ad 0%, #6f42c1 100%);
    color: white;
}

.modalities-container {
    max-width: 1000px;
    margin: 0 auto;
}

.modalities-title {
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 3rem;
    color: white;
}

.modalities-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.modality-card {
    background: white;
    padding: 2.5rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    color: #333;
    text-align: center;
}

.modality-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modality-icon {
    font-size: 3rem;
    color: #8e44ad;
    margin-bottom: 1.5rem;
}

.modality-card h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.modality-card p {
    line-height: 1.6;
    color: #666;
    font-size: 0.95rem;
}

/* Fix for proper section visibility */
.programs-section, .about-section {
    width: 100%;
    min-height: auto;
    display: block;
}

/* Ensure content is visible and properly spaced */
.programs-grid .program-card {
    opacity: 1;
    visibility: visible;
}

/* ==== TABLET DEVICES (768px - 991px) ==== */
@media (min-width: 768px) {
    .hero-main-content {
        flex-direction: row;
        text-align: left;
        gap: 3rem;
    }
    
    .hero-text {
        text-align: left;
        max-width: 600px;
    }
    
    .hero-image {
        max-width: 350px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .enroll-btn {
        font-size: 1.1rem;
        padding: 1rem 2.5rem;
    }
    
    .programs-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .programs-section {
        padding: 5rem 2rem;
    }
    
    .about-section {
        padding: 5rem 2rem;
    }
    
    .modalities-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 3rem;
    }
    
    .modalities-section {
        padding: 5rem 2rem;
    }
}

/* ==== LAPTOP DEVICES (992px - 1199px) ==== */
@media (min-width: 992px) {
    .hero-content {
        padding: 3rem 2rem;
    }
    
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-image {
        max-width: 400px;
    }
    
    .programs-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .programs-title {
        font-size: 2.5rem;
    }
    
    .modalities-title {
        font-size: 2.5rem;
    }
    
    .modalities-section {
        padding: 6rem 2rem;
    }
}

/* ==== PC/DESKTOP DEVICES (1200px - 1399px) ==== */
@media (min-width: 1200px) {
    .hero-content {
        padding: 4rem 2rem;
    }
    
    .hero-title {
        font-size: 3.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.3rem;
    }
    
    .hero-image {
        max-width: 450px;
    }
    
    .programs-section {
        padding: 6rem 2rem;
    }
    
    .about-section {
        padding: 6rem 2rem;
    }
    
    .modalities-section {
        padding: 6rem 2rem;
    }
}

/* Extra Large Desktop Design (1400px and up) */
/* ==== LARGE PC/DESKTOP DEVICES (1400px+) ==== */
@media (min-width: 1400px) {
    .hero-title {
        font-size: 4rem;
    }
    
    .hero-subtitle {
        font-size: 1.4rem;
    }
    
    .hero-image {
        max-width: 500px;
    }
}

/* Animation Classes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.8s ease-out;
    animation-fill-mode: both;
    opacity: 1;
    visibility: visible;
}

.fade-in-up:nth-child(2) {
    animation-delay: 0.2s;
}

.fade-in-up:nth-child(3) {
    animation-delay: 0.4s;
}

/* Smooth transitions for better UX */
* {
    scroll-behavior: smooth;
}

/* Ensure all content is visible */
.programs-grid .program-card,
.hero-text,
.hero-image {
    opacity: 1 !important;
    visibility: visible !important;
}
</style>
@endpush

@section('content')
@php
    $settings = App\Helpers\SettingsHelper::getSettings();
    $homepageTitle = $settings['homepage']['title'] ?? 'ENROLL NOW';
@endphp

<!-- Hero Section -->
<section class="homepage-hero">
    <div class="hero-content">
        <div class="hero-main-content">
            <div class="hero-text">
                <h1 class="hero-title fade-in-up">
                    Review <span style="color: #4CAF50;">Smarter.</span><br>
                    Learn <span style="color: #4CAF50;">Better.</span><br>
                    Succeed <span style="color: #4CAF50;">Faster.</span>
                </h1>
                <p class="hero-subtitle fade-in-up">
                    At Ascendo Review and Training Center, we guide future licensed professionals 
                    toward exam success with expert-led reviews and flexible learning options.
                </p>
                <a href="{{ url('/enrollment') }}" class="enroll-btn fade-in-up">
                    {{ $homepageTitle }}
                </a>
            </div>
            <div class="hero-image fade-in-up">
                <img src="{{ asset('images/Home page image.png') }}" alt="Student studying with laptop">
            </div>
        </div>
    </div>
</section>

<!-- Programs Offered Section -->
<section class="programs-section">
    <div class="programs-container">
        <h2 class="programs-title">Programs Offered</h2>
        <div class="programs-grid">
            <div class="program-card">
                <h3>Engineering Review Program</h3>
                <p>Comprehensive review program designed for engineering licensure examinations. 
                   Covering mathematics, engineering sciences, and specialized engineering subjects 
                   with expert instructors and updated materials.</p>
                <a href="#" class="learn-more-btn">Learn More</a>
            </div>
            
            <div class="program-card">
                <h3>Nursing Review Program</h3>
                <p>Intensive nursing review program for NCLEX and local nursing board examinations. 
                   Features comprehensive coverage of nursing fundamentals, clinical specialties, 
                   and test-taking strategies.</p>
                <a href="#" class="learn-more-btn">Learn More</a>
            </div>
            
            <div class="program-card">
                <h3>Medical Review Program</h3>
                <p>Complete medical review program for physician licensure examinations. 
                   Includes clinical medicine, basic sciences, and comprehensive board exam 
                   preparation with simulation tests.</p>
                <a href="#" class="learn-more-btn">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="about-container">
        <h2 class="about-title">ABOUT US</h2>
        <p class="about-text">
            Ascendo Review and Training Center is dedicated to providing exceptional review programs 
            and training services for professional licensure examinations. With experienced instructors, 
            comprehensive materials, and proven methodologies, we help aspiring professionals achieve 
            their career goals through excellence in education and training.
        </p>
    </div>
</section>

<!-- Available Modalities Section -->
<section class="modalities-section">
    <div class="modalities-container">
        <h2 class="modalities-title">Available Modalities</h2>
        <div class="modalities-grid">
            <div class="modality-card">
                <div class="modality-icon">
                    <i class="bi bi-laptop"></i>
                </div>
                <h3>Synchronous</h3>
                <p>Real-time interactive online classes with live instructors. Participate in discussions, 
                   ask questions instantly, and engage with fellow students in a virtual classroom environment.</p>
            </div>
            
            <div class="modality-card">
                <div class="modality-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3>Asynchronous</h3>
                <p>Self-paced learning with recorded lectures and materials available 24/7. Study at your 
                   own convenience with full access to comprehensive review materials and practice tests.</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Animation on scroll and immediate visibility
document.addEventListener('DOMContentLoaded', function() {
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
