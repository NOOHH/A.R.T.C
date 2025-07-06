@extends('layouts.navbar')

@section('title', 'Review Programs')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
{!! App\Helpers\UIHelper::getNavbarStyles() !!}

<style>
    .programs-container {
        min-height: 70vh;
        padding: 60px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .programs-content {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .programs-header {
        text-align: center;
        margin-bottom: 60px;
        color: white;
    }
    
    .programs-header h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .programs-header p {
        font-size: 1.3rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .programs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .program-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .program-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    
    .program-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        margin-bottom: 25px;
    }
    
    .program-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        line-height: 1.3;
    }
    
    .program-description {
        color: #7f8c8d;
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 25px;
    }
    
    .program-modules {
        margin-bottom: 25px;
    }
    
    .program-modules h4 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .modules-count {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .modules-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .module-tag {
        background: #ecf0f1;
        color: #2c3e50;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .program-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    
    .btn-view {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-view:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    .btn-enroll {
        background: transparent;
        color: #667eea;
        text-decoration: none;
        padding: 12px 24px;
        border: 2px solid #667eea;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-enroll:hover {
        background: #667eea;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .programs-header h1 {
            font-size: 2.5rem;
        }
        
        .programs-header p {
            font-size: 1.1rem;
        }
        
        .programs-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .program-card {
            padding: 20px;
        }
        
        .program-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="programs-container">
    <div class="programs-content">
        <div class="programs-header">
            <h1>Review Programs</h1>
            <p>Choose from our comprehensive selection of review programs designed to help you achieve your certification goals</p>
        </div>
        
        <div class="programs-grid">
            @foreach($programs as $program)
                <div class="program-card">
                    <div class="program-icon">
                        📚
                    </div>
                    
                    <h3 class="program-title">{{ $program->program_name }}</h3>
                    
                    @if($program->program_description)
                        <p class="program-description">{{ $program->program_description }}</p>
                    @endif
                    
                    @if($program->modules->count() > 0)
                        <div class="program-modules">
                            <h4>
                                <i class="bi bi-book"></i>
                                Modules
                                <span class="modules-count">{{ $program->modules->count() }}</span>
                            </h4>
                            <div class="modules-list">
                                @foreach($program->modules->take(4) as $module)
                                    <span class="module-tag">{{ $module->module_name }}</span>
                                @endforeach
                                @if($program->modules->count() > 4)
                                    <span class="module-tag">+{{ $program->modules->count() - 4 }} more</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="program-actions">
                        <a href="{{ route('programs.show', $program->program_id) }}" class="btn-view">
                            <i class="bi bi-eye"></i>
                            View Details
                        </a>
                        <a href="{{ route('enrollment.modular', ['program_id' => $program->program_id]) }}" class="btn-enroll">
                            <i class="bi bi-play-circle"></i>
                            Enroll Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($programs->isEmpty())
            <div style="text-align: center; color: white; margin-top: 60px;">
                <i class="bi bi-book" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.7;"></i>
                <h3>No Programs Available</h3>
                <p>Check back later for new review programs.</p>
            </div>
        @endif
    </div>
</div>
@endsection
