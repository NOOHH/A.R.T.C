@extends('admin.admin-dashboard-layout')

@section('title', 'Archived Modules')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-modules.css') }}">
<style>
  /* Main wrapper */
  .main-content-wrapper {
    align-items: flex-start !important;
  }

  /* Container */
  .modules-container {
    background: #fff;
    padding: 40px 20px 60px;
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  /* Header with Bootstrap approach */
  .modules-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #6c757d;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .back-to-modules-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .back-to-modules-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
  }

  /* Program selector */
  .program-selector {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #6c757d;
  }
  .program-selector label {
    display: block;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 10px;
    font-size: 1.1rem;
  }
  .program-selector select {
    width: 100%;
    max-width: 400px;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    transition: border-color 0.3s ease;
  }
  .program-selector select:focus {
    outline: none;
    border-color: #6c757d;
    box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.1);
  }

  /* Modules grid */
  .modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }

  /* Module card - archived styling */
  .module-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    position: relative;
    opacity: 0.7;
    border-left: 5px solid #6c757d;
  }

  .module-card:hover {
    opacity: 1;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  .module-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #6c757d;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .module-description {
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.5;
    font-size: 0.95rem;
  }

  .download-link {
    display: inline-block;
    color: #6c757d;
    text-decoration: none;
    font-size: 0.9rem;
    margin: 8px 0;
    padding: 4px 8px;
    border: 1px solid #6c757d;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .download-link:hover {
    background-color: #6c757d;
    color: white;
    text-decoration: none;
  }

  .module-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
  }

  .module-program {
    background: #6c757d;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .module-actions {
    display: flex;
    gap: 8px;
  }

  .unarchive-btn, .delete-module-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .unarchive-btn {
    background: #28a745;
    color: white;
  }
  .unarchive-btn:hover {
    background: #218838;
    transform: scale(1.05);
  }

  .delete-module-btn {
    background: #dc3545;
    color: white;
  }
  .delete-module-btn:hover {
    background: #c82333;
    transform: scale(1.05);
  }

  .content-type-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 8px;
    opacity: 0.7;
  }

  .content-type-badge.module { background: #e7f3ff; color: #0066cc; }
  .content-type-badge.assignment { background: #fff3e0; color: #ff9800; }
  .content-type-badge.quiz { background: #f3e5f5; color: #9c27b0; }
  .content-type-badge.test { background: #ffebee; color: #f44336; }
  .content-type-badge.link { background: #e8f5e8; color: #4caf50; }

  .content-details {
    margin: 10px 0;
    font-size: 0.9rem;
    color: #666;
    opacity: 0.8;
  }

  /* Empty state */
  .no-modules {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }
  .no-modules::before {
    content: '🗃️';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }

  /* Program not selected state */
  .select-program-msg {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
  }
  .select-program-msg::before {
    content: '👆';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
  }

  /* Messages */
  .success-message, .error-message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
  }

  .success-message {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
  }

  .error-message {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
  }

  .error-message ul {
    margin: 0;
    padding-left: 20px;
  }

  .archived-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #6c757d;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  /* Batch actions and selection styles */
  .batch-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #6c757d;
  }

  .select-all-container {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
  }

  .batch-delete-btn {
    display: none;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .batch-delete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
  }

  .module-checkbox {
    margin-right: 10px;
    transform: scale(1.2);
  }
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
                                            {{ $module->content_type_display ?? 'Module/Lesson' }}
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

// Archive/Unarchive module function
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
</script>
    }
}

// Program selector functionality
document.addEventListener('DOMContentLoaded', () => {
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
</script>
@endpush
