@extends('layouts.navbar')

@section('title', $program->program_name)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
    .program-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 0;
    }
    
    .program-content {
        background: white;
        margin-top: -40px;
        border-radius: 30px 30px 0 0;
        padding: 60px 0;
        position: relative;
        z-index: 2;
    }
    
    .hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }
    
    .program-icon {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.2);
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        margin: 0 auto 30px;
        backdrop-filter: blur(10px);
    }
    
    .program-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .program-subtitle {
        font-size: 1.3rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto 40px;
    }
    
    .program-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        text-align: center;
        background: rgba(255,255,255,0.1);
        padding: 20px;
        border-radius: 15px;
        backdrop-filter: blur(10px);
        min-width: 150px;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        display: block;
    }
    
    .stat-label {
        font-size: 1rem;
        opacity: 0.8;
    }
    
    .content-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .content-section {
        margin-bottom: 60px;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .description-box {
        background: #f8f9fa;
        padding: 40px;
        border-radius: 20px;
        border-left: 5px solid #667eea;
        margin-bottom: 40px;
    }
    
    .description-text {
        font-size: 1.2rem;
        line-height: 1.8;
        color: #34495e;
        margin: 0;
    }
    
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 25px;
        margin-top: 40px;
    }
    
    .module-card {
        background: white;
        border: 2px solid #ecf0f1;
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .module-card:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.1);
    }
    
    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .module-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .module-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 12px;
    }
    
    .module-description {
        color: #7f8c8d;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .module-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .meta-tag {
        background: #ecf0f1;
        color: #2c3e50;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0;
        border-radius: 30px;
        text-align: center;
        margin: 60px 0;
    }
    
    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .cta-description {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .cta-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-primary-cta {
        background: white;
        color: #667eea;
        text-decoration: none;
        padding: 15px 30px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-primary-cta:hover {
        color: #667eea;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(255,255,255,0.3);
    }
    
    .btn-secondary-cta {
        background: transparent;
        color: white;
        text-decoration: none;
        padding: 15px 30px;
        border: 2px solid white;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-secondary-cta:hover {
        background: white;
        color: #667eea;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .program-title {
            font-size: 2.5rem;
        }
        
        .program-stats {
            gap: 20px;
        }
        
        .stat-item {
            min-width: 120px;
            padding: 15px;
        }
        
        .modules-grid {
            grid-template-columns: 1fr;
        }
        
        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-primary-cta,
        .btn-secondary-cta {
            width: 250px;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="program-hero">
    <div class="hero-content">
        <div class="program-icon">
            📚
        </div>
        
        <h1 class="program-title">{{ $program->program_name }}</h1>
        
        @if($program->program_description)
            <p class="program-subtitle">{{ $program->program_description }}</p>
        @endif
        
        <div class="program-stats">
            <div class="stat-item">
                <span class="stat-number">{{ $program->modules->count() }}</span>
                <span class="stat-label">Modules</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $program->modules->where('content_type', 'module')->count() }}</span>
                <span class="stat-label">Lessons</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $program->modules->where('content_type', 'quiz')->count() }}</span>
                <span class="stat-label">Quizzes</span>
            </div>
        </div>
    </div>
</div>

<div class="program-content">
    <div class="content-container">
        @if($program->program_description)
            <div class="content-section">
                <div class="description-box">
                    <p class="description-text">{{ $program->program_description }}</p>
                </div>
            </div>
        @endif
        
        @if($program->modules->count() > 0)
            <div class="content-section">
                <h2 class="section-title">Course Modules</h2>
                
                <div class="modules-grid">
                    @foreach($program->modules as $index => $module)
                        <div class="module-card">
                            <div class="module-number">{{ $index + 1 }}</div>
                            
                            <h3 class="module-title">{{ $module->module_name }}</h3>
                            
                            @if($module->module_description)
                                <p class="module-description">{{ $module->module_description }}</p>
                            @endif
                            
                            <div class="module-meta">
                                <span class="meta-tag">
                                    <i class="bi bi-{{ $module->content_type === 'quiz' ? 'question-circle' : ($module->content_type === 'assignment' ? 'clipboard-check' : 'book') }}"></i>
                                    {{ ucfirst($module->content_type_display ?? $module->content_type) }}
                                </span>
                                @if($module->module_order)
                                    <span class="meta-tag">
                                        <i class="bi bi-list-ol"></i>
                                        Order: {{ $module->module_order }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <div class="cta-section">
            <h2 class="cta-title">Ready to Start Your Journey?</h2>
            <p class="cta-description">
                Join thousands of students who have successfully achieved their certification goals with our comprehensive review program.
            </p>
            
            <div class="cta-buttons">
                <a href="{{ route('enrollment.modular', ['program_id' => $program->program_id]) }}" class="btn-primary-cta">
                    <i class="bi bi-play-circle-fill"></i>
                    Enroll Now
                </a>
                <a href="{{ route('programs.index') }}" class="btn-secondary-cta">
                    <i class="bi bi-arrow-left"></i>
                    Back to Programs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
