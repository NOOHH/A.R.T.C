@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
<style>
    {!! App\Helpers\SettingsHelper::getHomepageStyles() !!}
</style>
@endpush

@section('content')
@php
    use App\Models\Program;
    use App\Models\Module;
    
    $settings = App\Helpers\SettingsHelper::getSettings();
    $homepageTitle = $settings['homepage']['title'] ?? 'ENROLL NOW';
    
    // Fetch non-archived programs
    $programs = Program::where('is_archived', false)->get();
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
            @if($programs->count() > 0)
                @foreach($programs as $program)
                <div class="program-card">
                    <h3>{{ $program->program_name }}</h3>
                    <p>
                        @if(!empty($program->program_description))
                            {{ Str::limit($program->program_description, 150) }}
                        @else
                            No description yet.
                        @endif
                    </p>
                    <a href="javascript:void(0)" onclick="showProgramDetails({{ $program->program_id }})" class="learn-more-btn">Learn More</a>
                </div>
                @endforeach
            @else
                <div class="no-programs-message">
                    No programs are currently offered. Please check back later.
                </div>
            @endif
        </div>
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
