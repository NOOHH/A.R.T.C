@extends('professor.layout')

@section('title', 'Archived Modules')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/admin/admin-modules.css') }}" rel="stylesheet">
<style>
.program-selector {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.back-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    border: none;
    margin-bottom: 2rem;
    display: inline-block;
}

.back-btn:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    transform: translateY(-2px);
    color: white;
}

.archived-module {
    background: white;
    border: 2px solid #dc3545;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.1);
    opacity: 0.8;
}

.archived-module-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.archived-module-header h5 {
    margin: 0;
    font-weight: 600;
}

.restore-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
}

.restore-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
}

.archived-info {
    padding: 1rem 2rem;
    background: #f8d7da;
    color: #721c24;
    font-size: 0.9rem;
}

.no-modules-message, .select-program-message {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.select-program-message i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.professor-notice {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    text-align: center;
}

.professor-notice i {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Alert Messages -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="modules-container">
    <!-- Header -->
    <div class="modules-header">
        <h1><i class="bi bi-archive"></i> Archived Modules</h1>
        <p>View and restore archived modules from your assigned programs</p>
    </div>

    <!-- Back Button -->
    <a href="{{ route('professor.modules.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Active Modules
    </a>

    <!-- Professor Notice -->
    <div class="professor-notice">
        <i class="bi bi-info-circle"></i>
        You can only view archived modules for programs you are assigned to as a professor.
    </div>

    <!-- Program Selector -->
    <div class="program-selector">
        <label for="programSelect" class="form-label">Select Program to View Archived Modules:</label>
        <div class="d-flex align-items-center gap-3">
            <select id="programSelect" name="program_id" class="form-select" style="max-width: 400px;">
                <option value="">-- Select a Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}"
                        {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Select a program to view its archived modules
            </small>
        </div>
    </div>

    <!-- Archived Modules Display Area -->
    <div id="modulesDisplayArea">
        @if(request('program_id') && isset($modules))
            @if($modules->count() > 0)
                @foreach($modules as $module)
                    <div class="archived-module" data-module-id="{{ $module->module_id }}">
                        <div class="archived-module-header">
                            <div>
                                <h5>{{ $module->module_name }}</h5>
                                @if($module->module_description)
                                    <small>{{ $module->module_description }}</small>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark">
                                    {{ $module->learning_mode }}
                                </span>
                                @if($module->batch)
                                    <span class="badge bg-info">
                                        {{ $module->batch->batch_name }}
                                    </span>
                                @endif
                                <!-- Restore button removed for professor view-only access -->
                            </div>
                        </div>
                        <div class="archived-info">
                            <i class="bi bi-clock"></i>
                            Archived on {{ $module->updated_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-modules-message">
                    <i class="bi bi-archive text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Archived Modules</h4>
                    <p class="text-muted">This program has no archived modules.</p>
                    <a href="{{ route('professor.modules.index') }}?program_id={{ request('program_id') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Module
                    </a>
                </div>
            @endif
        @else
            <div class="select-program-message">
                <i class="bi bi-collection text-muted"></i>
                <h4 class="mt-3 text-muted">Select a Program</h4>
                <p class="text-muted">Choose a program from the dropdown above to view its archived modules.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Program selector change handler
    document.getElementById('programSelect').addEventListener('change', function() {
        const programId = this.value;
        if (programId) {
            window.location.href = `{{ route('professor.modules.archived') }}?program_id=${programId}`;
        } else {
            window.location.href = `{{ route('professor.modules.archived') }}`;
        }
    });
});

// Remove restoreModule function for professors (view-only)
</script>
@endsection
