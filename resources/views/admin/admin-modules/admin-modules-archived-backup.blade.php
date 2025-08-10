@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Archived Modules')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-modules-archived.css') }}">
<style>
</style>
@endpush

@section('content')
<!-- Display messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="modules-container">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h1 class="display-4 fw-bold text-uppercase text-muted mb-0" style="letter-spacing: 2px;">Archived Modules</h1>
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('admin.modules.index') }}" class="btn btn-lg text-white fw-semibold px-4 py-2 rounded-pill shadow back-to-modules-btn">
                <i class="fas fa-arrow-left me-2"></i>Back to Modules
            </a>
        </div>
    </div>

    <!-- Program Selector -->
    <div class="mb-4">
        <label for="programSelect" class="form-label fw-semibold">Select Program to View Archived Modules:</label>
        <select id="programSelect" name="program_id" class="form-select">
            <option value="">-- Select a Program --</option>
            @foreach($programs as $program)
                <option value="{{ $program->program_id }}"
                    {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                    {{ $program->program_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Batch Actions -->
    @if(request('program_id') && isset($archivedModules) && $archivedModules->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllModules" onchange="toggleSelectAll()">
            <label class="form-check-label fw-semibold" for="selectAllModules">
                Select All Modules
            </label>
        </div>
        <button type="button" class="btn btn-danger fw-semibold" id="batchDeleteBtn" onclick="batchDeleteModules()">
            <i class="fas fa-trash me-2"></i>Delete Selected
        </button>
    </div>
    @endif

    <!-- Modules Display Area -->
    <div id="modulesDisplayArea">
        @if(request('program_id') && isset($archivedModules))
            @if($archivedModules->count() > 0)
                <div class="modules-grid">
                    @foreach($archivedModules as $module)
                        <div class="module-card">
                            <div class="archived-badge">ARCHIVED</div>
                            <div class="d-flex align-items-start gap-3">
                                <input type="checkbox" 
                                       class="form-check-input module-checkbox mt-1" 
                                       data-module-id="{{ $module->modules_id }}"
                                       onchange="toggleModuleSelection({{ $module->modules_id }}, this)">
                                <div class="flex-grow-1">
                                    <div class="module-title">
                                        {{ $module->content_type_icon ?? '📚' }} {{ $module->module_name }}
                                        <span class="content-type-badge {{ $module->content_type ?? 'module' }}">
                                            {{ $module->content_type_display ?? 'Module' }}
                                        </span>
                                    </div>
                                    
                                    <div class="module-description">
                                        {{ $module->module_description }}
                                    </div>

                                    @if($module->content_data)
                                        <div class="content-details">
                                            @php $data = $module->content_data @endphp
                                            @switch($module->content_type)
                                                @case('assignment')
                                                    @if(!empty($data['assignment_title']))
                                                        <strong>Title:</strong> {{ $data['assignment_title'] }}<br>
                                                    @endif
                                                    @if(!empty($data['due_date']))
                                                        <strong>Due:</strong> {{ \Carbon\Carbon::parse($data['due_date'])->format('M d, Y g:i A') }}<br>
                                                    @endif
                                                    @if(!empty($data['max_points']))
                                                        <strong>Points:</strong> {{ $data['max_points'] }}
                                                    @endif
                                                    @break
                                                @case('quiz')
                                                    @if(!empty($data['quiz_title']))
                                                        <strong>Title:</strong> {{ $data['quiz_title'] }}<br>
                                                    @endif
                                                    @if(!empty($data['time_limit']))
                                                        <strong>Time:</strong> {{ $data['time_limit'] }} minutes<br>
                                                    @endif
                                                    @if(!empty($data['question_count']))
                                                        <strong>Questions:</strong> {{ $data['question_count'] }}
                                                    @endif
                                                    @break
                                                @case('test')
                                                    @if(!empty($data['test_title']))
                                                        <strong>Title:</strong> {{ $data['test_title'] }}<br>
                                                    @endif
                                                    @if(!empty($data['test_date']))
                                                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($data['test_date'])->format('M d, Y g:i A') }}<br>
                                                    @endif
                                                    @if(!empty($data['total_marks']))
                                                        <strong>Total Marks:</strong> {{ $data['total_marks'] }}
                                                    @endif
                                                    @break
                                                @case('link')
                                                    @if(!empty($data['link_title']))
                                                        <strong>Link:</strong> {{ $data['link_title'] }}<br>
                                                    @endif
                                                    @if(!empty($data['external_url']))
                                                        <a href="{{ $data['external_url'] }}" target="_blank" class="download-link">
                                                            🔗 Open Link
                                                        </a>
                                                    @endif
                                                    @break
                                            @endswitch
                                        </div>
                                    @endif

                                    @if($module->attachment)
                                        <p><a href="{{ asset('storage/'.$module->attachment) }}" target="_blank" class="download-link">
                                            📎 Download file
                                        </a></p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="module-meta">
                                <span class="module-program">
                                    {{ $module->program->program_name }}
                                </span>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-success btn-sm fw-semibold" 
                                            onclick="toggleArchiveModule({{ $module->modules_id }}, true)">
                                        <i class="fas fa-folder-open me-1"></i>Unarchive
                                    </button>
                                    <button class="btn btn-warning btn-sm fw-semibold" 
                                            onclick="showOverrideModal({{ $module->modules_id }}, '{{ addslashes($module->module_name) }}')">
                                        <i class="fas fa-unlock me-1"></i>Override
                                    </button>
                                    <form action="{{ route('admin.modules.destroy', $module->modules_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this module?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm fw-semibold">
                                            <i class="fas fa-trash me-1"></i>Delete Permanently
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="bg-light border border-2 border-dashed rounded-4 p-5">
                        <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                        <div class="text-muted h5 mb-2">No archived modules found for this program.</div>
                        <small class="text-muted">
                            <a href="{{ route('admin.modules.index') }}" class="text-primary text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Go back to active modules
                            </a>
                        </small>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="bg-light border border-2 border-dashed rounded-4 p-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <div class="text-muted h5 mb-2">Please select a program above to view its archived modules.</div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Override Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1" aria-labelledby="overrideModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="overrideModalLabel">
                    <i class="fas fa-unlock me-2"></i>Admin Override Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="overrideForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <p>Configure override settings for: <strong id="overrideModuleName"></strong></p>
                    
                    <div class="admin-override-checkboxes">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_completion_arch" name="admin_override[]" value="completion">
                            <label class="form-check-label" for="override_completion_arch">
                                <i class="bi bi-check-circle"></i> Override Completion Requirements
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_prerequisites_arch" name="admin_override[]" value="prerequisites">
                            <label class="form-check-label" for="override_prerequisites_arch">
                                <i class="bi bi-arrow-right-circle"></i> Override Prerequisites
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_time_limits_arch" name="admin_override[]" value="time_limits">
                            <label class="form-check-label" for="override_time_limits_arch">
                                <i class="bi bi-clock"></i> Override Time Limits
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="override_access_control_arch" name="admin_override[]" value="access_control">
                            <label class="form-check-label" for="override_access_control_arch">
                                <i class="bi bi-unlock"></i> Override Access Control
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyOverride()">Apply Override</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Program selector
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const pid = this.value;
            window.location.href = pid
                ? `{{ route('admin.modules.archived') }}?program_id=${pid}`
                : `{{ route('admin.modules.archived') }}`;
        });
    }
});

// Override functionality
let currentOverrideModuleId = null;

function showOverrideModal(moduleId, moduleName) {
    currentOverrideModuleId = moduleId;
    document.getElementById('overrideModuleName').textContent = moduleName;
    document.getElementById('overrideForm').action = `/admin/modules/${moduleId}/override`;
    
    // Load current override settings
    fetch(`/admin/modules/${moduleId}/override-settings`)
        .then(response => response.json())
        .then(data => {
            // Clear all checkboxes first
            document.querySelectorAll('#overrideModal input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check boxes based on current settings
            if (data.admin_override && Array.isArray(data.admin_override)) {
                data.admin_override.forEach(override => {
                    const checkbox = document.querySelector(`#override_${override}_arch`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        })
        .catch(error => console.error('Error loading override settings:', error));
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('overrideModal'));
    modal.show();
}

function applyOverride() {
    if (!currentOverrideModuleId) return;
    
    const form = document.getElementById('overrideForm');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('overrideModal'));
            modal.hide();
            
            // Show success message
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success alert-dismissible fade show';
            successAlert.innerHTML = `
                Override settings updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.modules-container').insertBefore(successAlert, document.querySelector('.modules-container').firstChild);
            
            // Auto-remove alert after 5 seconds
            setTimeout(() => {
                successAlert.remove();
            }, 5000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating override settings.');
    });
}

// ...existing code...
function toggleArchiveModule(moduleId, isArchived) {
    if (confirm(isArchived ? 'Are you sure you want to unarchive this module?' : 'Are you sure you want to archive this module?')) {
        fetch(`/admin/modules/${moduleId}/archive`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_archived: !isArchived })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the module.');
        });
    }
}

// Batch delete functionality
let selectedModules = new Set();

function toggleModuleSelection(moduleId, checkbox) {
    if (checkbox.checked) {
        selectedModules.add(moduleId);
    } else {
        selectedModules.delete(moduleId);
    }
    updateBatchDeleteButton();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllModules');
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    
    moduleCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        const moduleId = parseInt(checkbox.dataset.moduleId);
        if (selectAllCheckbox.checked) {
            selectedModules.add(moduleId);
        } else {
            selectedModules.delete(moduleId);
        }
    });
    
    updateBatchDeleteButton();
}

function updateBatchDeleteButton() {
    const batchDeleteBtn = document.getElementById('batchDeleteBtn');
    const selectedCount = selectedModules.size;
    
    if (selectedCount > 0) {
        batchDeleteBtn.style.display = 'inline-block';
        batchDeleteBtn.textContent = `Delete Selected (${selectedCount})`;
    } else {
        batchDeleteBtn.style.display = 'none';
    }
}

function batchDeleteModules() {
    if (selectedModules.size === 0) return;
    
    if (confirm(`Are you sure you want to permanently delete ${selectedModules.size} selected module(s)? This action cannot be undone.`)) {
        fetch('/admin/modules/batch-delete', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ module_ids: Array.from(selectedModules) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting modules.');
        });
    }
}

// Show override modal
function showOverrideModal(moduleId, moduleName) {
    const modal = new bootstrap.Modal(document.getElementById('overrideModal'));
    document.getElementById('overrideModuleName').textContent = moduleName;
    document.getElementById('overrideForm').setAttribute('action', `/admin/modules/${moduleId}/override`);
    
    // Reset checkboxes
    const checkboxes = document.querySelectorAll('.admin-override-checkboxes .form-check-input');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    modal.show();
}

// Apply override settings
function applyOverride() {
    const form = document.getElementById('overrideForm');
    const formData = new FormData(form);
    
    fetch(form.getAttribute('action'), {
        method: 'PATCH',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while applying overrides.');
    });
}
</script>
@endpush
