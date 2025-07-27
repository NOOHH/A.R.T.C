@extends('layouts.navbar')

@section('title', 'Welcome - Review Programs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/homepage/review-programs.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- Global UI Styles --}}
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
    {!! App\Helpers\SettingsHelper::getHomepageStyles() !!}
    
    /* Custom styles for review programs */
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 120px 0 80px 0;
        text-align: center;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .hero-subtitle {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }
    
    .programs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .program-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .program-title {
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .program-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    
    .program-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-outline-custom {
        background: transparent;
        border: 2px solid #667eea;
        color: #667eea;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-outline-custom:hover {
        background: #667eea;
        color: white;
        text-decoration: none;
    }
    
    .no-programs {
        text-align: center;
        padding: 3rem;
        color: #666;
    }
    
    .loading-spinner {
        text-align: center;
        padding: 3rem;
    }
    
    .spinner-border-custom {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
        border-color: #667eea;
        border-right-color: transparent;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="hero-title">Review Programs</h1>
        <p class="hero-subtitle">Discover our comprehensive review programs designed to help you succeed in your professional licensure examinations</p>
    </div>
</section>

<!-- Programs Section -->
<section class="py-5">
    <div class="container">
        <div id="programsContainer">
            <div class="loading-spinner">
                <div class="spinner-border spinner-border-custom" role="status">
                    <span class="visually-hidden">Loading programs...</span>
                </div>
                <p class="mt-3">Loading review programs...</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadPrograms();
});

function loadPrograms() {
    fetch('/api/programs')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('programsContainer');
            
            if (data.success && data.data && data.data.length > 0) {
                let html = '<div class="programs-grid">';
                
                data.data.forEach(program => {
                    html += `
                        <div class="program-card">
                            <h3 class="program-title">${program.program_name}</h3>
                            <p class="program-description">
                                ${program.program_description ? program.program_description.substring(0, 150) + (program.program_description.length > 150 ? '...' : '') : 'No description available.'}
                            </p>
                            <div class="program-actions">
                                <a href="#" onclick="showProgramModal(${program.program_id})" class="btn-outline-custom">
                                    <i class="bi bi-eye me-2"></i>Quick View
                                </a>
                                <a href="/programs/${program.program_id}" class="btn-primary-custom">
                                    <i class="bi bi-arrow-right me-2"></i>Full Details
                                </a>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="no-programs">
                        <i class="bi bi-book" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <h4>No Programs Available</h4>
                        <p>No review programs are currently offered. Please check back later.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading programs:', error);
            document.getElementById('programsContainer').innerHTML = `
                <div class="no-programs">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                    <h4>Error Loading Programs</h4>
                    <p>There was an error loading the review programs. Please try again later.</p>
                </div>
            `;
        });
}

function showProgramModal(programId) {
    window.location.href = `/profile/program/${programId}`;
}
</script>
@endpush
