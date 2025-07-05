@extends('student.student-dashboard.student-dashboard-layout')

@section('title', $module->module_name ?? 'Module')

@push('styles')
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom Module Styles -->
<style>
    .module-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    /* Module Header */
    .module-header {
        background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .module-header h1 {
        margin: 0 0 15px 0;
        font-size: 2.2rem;
        font-weight: 700;
    }
    
    .module-breadcrumb {
        opacity: 0.9;
        font-size: 1rem;
        margin-bottom: 20px;
    }
    
    .module-breadcrumb a {
        color: white;
        text-decoration: none;
    }
    
    .module-breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .back-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    
    .back-btn:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    
    /* Content Cards */
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    
    .content-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .content-card h3 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 20px;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }
    
    .module-description {
        color: #555;
        line-height: 1.8;
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    
    /* Sidebar Card */
    .sidebar-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        position: sticky;
        top: 20px;
    }
    
    .sidebar-card h4 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 20px;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #7f8c8d;
    }
    
    .info-value {
        color: #2c3e50;
        font-weight: 500;
    }
    
    /* Attachment Card */
    .attachment-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 20px;
    }
    
    .download-btn {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    
    .download-btn:hover {
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
    
    /* Complete Button */
    .complete-btn {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s;
    }
    
    .complete-btn:disabled {
        background: #bdc3c7;
        cursor: not-allowed;
        transform: none;
    }
    
    .complete-btn:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
    }
    
    /* Coming Soon Alert */
    .coming-soon {
        background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    
    .coming-soon i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .module-container {
            padding: 15px;
        }
        
        .module-header {
            padding: 20px;
        }
        
        .module-header h1 {
            font-size: 1.8rem;
        }
        
        .content-card {
            padding: 20px;
        }
        
        .sidebar-card {
            margin-top: 20px;
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="module-container">
    <!-- Module Header -->
    <div class="module-header">
        <div class="module-breadcrumb">
            <a href="{{ route('student.dashboard') }}">Dashboard</a> > 
            <a href="{{ route('student.course', $module->program_id) }}">{{ $program->program_name }}</a> > 
            {{ $module->module_name }}
        </div>
        <h1>{{ $module->module_name }}</h1>
        <a href="{{ route('student.course', $module->program_id) }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Course
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Module Description -->
            <div class="content-card">
                <h3><i class="bi bi-book"></i> Module Description</h3>
                <div class="module-description">
                    {{ $module->module_description ?? 'No description available for this module.' }}
                </div>
            </div>
            
            <!-- Course Materials -->
            @if($module->attachment)
                <div class="content-card">
                    <h3><i class="bi bi-paperclip"></i> Course Materials</h3>
                    <p>Download the attached materials for this module:</p>
                    <div class="attachment-card">
                        <i class="bi bi-file-earmark-arrow-down" style="font-size: 2rem; color: #3498db; margin-bottom: 15px;"></i>
                        <br>
                        <a href="{{ asset('storage/' . $module->attachment) }}" target="_blank" class="download-btn">
                            <i class="bi bi-download"></i> Download Attachment
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Module Content -->
            <div class="content-card">
                <h3><i class="bi bi-play-circle"></i> Module Content</h3>
                <div class="coming-soon">
                    <i class="bi bi-gear"></i>
                    <h5>Interactive Content Coming Soon</h5>
                    <p class="mb-0">This module will soon include videos, interactive exercises, reading materials, and assessments to enhance your learning experience.</p>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-card">
                <h4><i class="bi bi-info-circle"></i> Module Information</h4>
                
                <div class="info-item">
                    <span class="info-label">Program:</span>
                    <span class="info-value">{{ $program->program_name }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Module ID:</span>
                    <span class="info-value">#{{ $module->modules_id }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Created:</span>
                    <span class="info-value">{{ $module->created_at->format('M d, Y') }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span style="color: #3498db; font-weight: 600;">
                            <i class="bi bi-unlock"></i> Available
                        </span>
                    </span>
                </div>
                
                <button class="complete-btn" disabled>
                    <i class="bi bi-check-circle"></i>
                    Mark as Complete
                </button>
                <small style="color: #7f8c8d; text-align: center; display: block; margin-top: 10px;">
                    Progress tracking coming soon
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
