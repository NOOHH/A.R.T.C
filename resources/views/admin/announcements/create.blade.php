@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Create Announcement')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
.form-card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 8px;
}

.form-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.targeting-options {
    display: none;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    transition: all 0.3s ease;
}

.targeting-options.show {
    display: block !important;
    opacity: 1;
    visibility: visible;
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.checkbox-item {
    flex: 1;
    min-width: 200px;
}

.preview-section {
    background: #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.required-field {
    color: #dc3545;
}

.form-floating > label {
    color: #6c757d;
}

.announcement-preview {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.type-badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.editor-toolbar {
    border: 1px solid #ced4da;
    border-bottom: none;
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 0.375rem 0.375rem 0 0;
}

.editor-content {
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    min-height: 150px;
    padding: 0.75rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle me-2"></i>Create New Announcement
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.announcements.index') }}">Announcements</a>
                    </li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.announcements.store') }}" method="POST" id="announcementForm">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card form-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="{{ old('title') }}" required>
                                    <label for="title">Announcement Title <span class="required-field">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>General</option>
                                        <option value="urgent" {{ old('type') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        <option value="event" {{ old('type') === 'event' ? 'selected' : '' }}>Event</option>
                                        <option value="system" {{ old('type') === 'system' ? 'selected' : '' }}>System</option>
                                    </select>
                                    <label for="type">Type <span class="required-field">*</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="description" name="description" 
                                      style="height: 100px">{{ old('description') }}</textarea>
                            <label for="description">Short Description (optional)</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content <span class="required-field">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="url" class="form-control" id="video_link" name="video_link" 
                                   value="{{ old('video_link') }}">
                            <label for="video_link">Video Link (optional)</label>
                        </div>
                    </div>
                </div>

                <!-- Target Audience -->
                <div class="card form-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-people me-2"></i>Target Audience
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Audience Scope <span class="required-field">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_scope" id="scope_all" 
                                       value="all" {{ old('target_scope', 'all') === 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="scope_all">
                                    <strong>All Users</strong> - Send to everyone in the system
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_scope" id="scope_specific" 
                                       value="specific" {{ old('target_scope') === 'specific' ? 'checked' : '' }}>
                                <label class="form-check-label" for="scope_specific">
                                    <strong>Specific Groups</strong> - Target specific users and programs
                                </label>
                            </div>
                        </div>

                        <div class="targeting-options" id="targetingOptions">
                            <!-- User Types -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Target User Types:</label>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_users[]" 
                                                   value="students" id="target_students"
                                                   {{ in_array('students', old('target_users', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_students">
                                                <i class="bi bi-person-badge me-2"></i>Students
                                            </label>
                                        </div>
                                    </div>
                                    <div class="checkbox-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_users[]" 
                                                   value="professors" id="target_professors"
                                                   {{ in_array('professors', old('target_users', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_professors">
                                                <i class="bi bi-person-workspace me-2"></i>Professors
                                            </label>
                                        </div>
                                    </div>
                                    <div class="checkbox-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_users[]" 
                                                   value="directors" id="target_directors"
                                                   {{ in_array('directors', old('target_users', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_directors">
                                                <i class="bi bi-person-fill me-2"></i>Directors
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Programs -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Target Programs (optional):</label>
                                <div class="checkbox-group">
                                    @foreach($programs as $program)
                                        <div class="checkbox-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="target_programs[]" 
                                                       value="{{ $program->program_id }}" id="program_{{ $program->program_id }}"
                                                       {{ in_array($program->program_id, old('target_programs', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="program_{{ $program->program_id }}">
                                                    {{ $program->program_name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Batches -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Target Batches (optional):</label>
                                <div id="batchesContainer" class="checkbox-group">
                                    <div class="text-muted">Select programs above to see their batches</div>
                                </div>
                            </div>

                            <!-- Enrollment Plans -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Target Enrollment Plans (optional):</label>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_plans[]" 
                                                   value="full" id="plan_full"
                                                   {{ in_array('full', old('target_plans', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="plan_full">
                                                <i class="bi bi-mortarboard me-2"></i>Full Program
                                            </label>
                                        </div>
                                    </div>
                                    <div class="checkbox-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_plans[]" 
                                                   value="modular" id="plan_modular"
                                                   {{ in_array('modular', old('target_plans', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="plan_modular">
                                                <i class="bi bi-puzzle me-2"></i>Modular Program
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publishing Options -->
                <div class="card form-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar3 me-2"></i>Publishing Options
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="is_published" value="0">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
                                   {{ old('is_published', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Publish immediately
                            </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="datetime-local" class="form-control" id="publish_date" name="publish_date" 
                                   value="{{ old('publish_date') }}">
                            <label for="publish_date">Publish Date (optional)</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="datetime-local" class="form-control" id="expire_date" name="expire_date" 
                                   value="{{ old('expire_date') }}">
                            <label for="expire_date">Expiry Date (optional)</label>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card form-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-eye me-2"></i>Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="announcement-preview" id="announcementPreview">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0" id="previewTitle">Announcement Title</h6>
                                <span class="badge type-badge" id="previewType">General</span>
                            </div>
                            <p class="text-muted mb-2" id="previewDescription">Description will appear here...</p>
                            <div id="previewContent">Content will appear here...</div>
                            <hr>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                <span id="previewDate">{{ date('M d, Y g:i A') }}</span>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-2"></i>Create Announcement
                    </button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // All batches data for filtering
    const allBatches = @json($batches);
    
    // Toggle targeting options
    const scopeAll = document.getElementById('scope_all');
    const scopeSpecific = document.getElementById('scope_specific');
    const targetingOptions = document.getElementById('targetingOptions');

    function toggleTargetingOptions() {
        console.log('Toggle function called');
        console.log('Scope specific checked:', scopeSpecific.checked);
        
        if (scopeSpecific.checked) {
            targetingOptions.classList.add('show');
            console.log('Showing targeting options');
        } else {
            targetingOptions.classList.remove('show');
            console.log('Hiding targeting options');
        }
    }

    if (scopeAll && scopeSpecific && targetingOptions) {
        scopeAll.addEventListener('change', toggleTargetingOptions);
        scopeSpecific.addEventListener('change', toggleTargetingOptions);
        
        // Initialize
        toggleTargetingOptions();
        console.log('Initialized targeting options toggle');
    } else {
        console.error('Some targeting elements not found:', {
            scopeAll: !!scopeAll,
            scopeSpecific: !!scopeSpecific,
            targetingOptions: !!targetingOptions
        });
    }

    // Handle program selection for dynamic batch loading
    function updateBatchesDisplay() {
        const selectedPrograms = Array.from(document.querySelectorAll('input[name="target_programs[]"]:checked')).map(input => parseInt(input.value));
        const batchesContainer = document.getElementById('batchesContainer');
        
        if (selectedPrograms.length === 0) {
            batchesContainer.innerHTML = '<div class="text-muted">Select programs above to see their batches</div>';
            return;
        }
        
        const relevantBatches = allBatches.filter(batch => selectedPrograms.includes(batch.program_id));
        
        if (relevantBatches.length === 0) {
            batchesContainer.innerHTML = '<div class="text-muted">No batches found for selected programs</div>';
            return;
        }
        
        // Group batches by program
        const batchesByProgram = {};
        relevantBatches.forEach(batch => {
            if (!batchesByProgram[batch.program_id]) {
                batchesByProgram[batch.program_id] = [];
            }
            batchesByProgram[batch.program_id].push(batch);
        });
        
        let html = '';
        Object.keys(batchesByProgram).forEach(programId => {
            const program = @json($programs).find(p => p.program_id == programId);
            const batches = batchesByProgram[programId];
            
            html += `<div class="program-batches mb-3">
                        <h6 class="text-primary mb-2"><i class="bi bi-collection"></i> ${program.program_name}</h6>
                        <div class="row">`;
            
            batches.forEach(batch => {
                const oldBatches = @json(old('target_batches', []));
                const isChecked = oldBatches.includes(batch.batch_id) ? 'checked' : '';
                
                html += `<div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_batches[]" 
                                       value="${batch.batch_id}" id="batch_${batch.batch_id}" ${isChecked}>
                                <label class="form-check-label" for="batch_${batch.batch_id}">
                                    ${batch.batch_name}
                                </label>
                            </div>
                         </div>`;
            });
            
            html += '</div></div>';
        });
        
        batchesContainer.innerHTML = html;
    }
    
    // Add event listeners to program checkboxes
    document.querySelectorAll('input[name="target_programs[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateBatchesDisplay);
    });
    
    // Initialize batch display
    updateBatchesDisplay();

    // Preview functionality
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const contentInput = document.getElementById('content');
    const typeSelect = document.getElementById('type');
    
    const previewTitle = document.getElementById('previewTitle');
    const previewDescription = document.getElementById('previewDescription');
    const previewContent = document.getElementById('previewContent');
    const previewType = document.getElementById('previewType');

    function updatePreview() {
        previewTitle.textContent = titleInput.value || 'Announcement Title';
        previewDescription.textContent = descriptionInput.value || 'Description will appear here...';
        previewContent.textContent = contentInput.value || 'Content will appear here...';
        
        const typeValue = typeSelect.value || 'general';
        previewType.textContent = typeValue.charAt(0).toUpperCase() + typeValue.slice(1);
        previewType.className = `badge type-badge announcement-type-${typeValue}`;
    }

    titleInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    contentInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', updatePreview);

    // Form validation
    document.getElementById('announcementForm').addEventListener('submit', function(e) {
        const title = titleInput.value.trim();
        const content = contentInput.value.trim();
        const type = typeSelect.value;

        if (!title || !content || !type) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }

        // If specific targeting is selected, at least one user type should be selected
        if (scopeSpecific.checked) {
            const userTypes = document.querySelectorAll('input[name="target_users[]"]:checked');
            console.log('Specific targeting selected, user types checked:', userTypes.length);
            
            if (userTypes.length === 0) {
                e.preventDefault();
                alert('Please select at least one user type for specific targeting.');
                return false;
            }
            
            // Log what user types are selected
            const selectedTypes = Array.from(userTypes).map(input => input.value);
            console.log('Selected user types:', selectedTypes);
        }
    });
});
</script>

<style>
.announcement-type-general { background-color: #3498db !important; }
.announcement-type-urgent { background-color: #e74c3c !important; }
.announcement-type-event { background-color: #f39c12 !important; }
.announcement-type-system { background-color: #9b59b6 !important; }
</style>
@endpush
