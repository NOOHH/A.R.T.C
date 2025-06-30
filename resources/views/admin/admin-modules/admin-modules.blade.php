@extends('admin.admin-dashboard-layout')

@section('title', 'Modules')

@push('styles')
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

  /* Header */
  .modules-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 10px;
  }
  .modules-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  /* Program selector */
  .program-selector {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #667eea;
  }
  .program-selector label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
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
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  /* Modules grid */
  .modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }

  /* Module card */
  .module-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
    position: relative;
    overflow: hidden;
  }
  .module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
  .module-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .module-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .module-title::before {
    content: '📚';
    font-size: 1.2rem;
  }

  .module-description {
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.5;
    font-size: 0.95rem;
  }

  .module-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
  }

  .module-program {
    background: #667eea;
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

  .edit-module-btn, .delete-module-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .edit-module-btn {
    background: #28a745;
    color: white;
  }
  .edit-module-btn:hover {
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

  /* Add button */
  .add-module-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .add-module-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  }

  /* Modal styles */
  .modal-bg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  .modal-bg.show {
    display: flex;
  }

  .modal {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .modal h3 {
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-size: 1.5rem;
    text-align: center;
  }

  .modal input, .modal select, .modal textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
  }

  .modal input:focus, .modal select:focus, .modal textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .modal textarea {
    resize: vertical;
    min-height: 80px;
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
  }

  .cancel-btn, .add-btn, .update-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
  }

  .cancel-btn {
    background: #6c757d;
    color: white;
  }
  .cancel-btn:hover {
    background: #5a6268;
  }

  .add-btn, .update-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  .add-btn:hover, .update-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  /* Drag-and-drop zone */
  .dropzone {
    border: 2px dashed #667eea;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    color: #6c757d;
    cursor: pointer;
    transition: background 0.2s ease;
    position: relative;
    margin-bottom: 15px;
  }
  .dropzone.dragover {
    background: rgba(102, 126, 234, 0.1);
  }
  .dropzone p {
    margin: 0;
    font-size: 1rem;
  }
  .dropzone input[type="file"] {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  /* Empty state */
  .no-modules {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }
  .no-modules::before {
    content: '📚';
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
</style>
@endpush

@section('content')
<!-- Display validation errors -->
@if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Display success message -->
@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="modules-container">
    <div class="modules-header">
        <h1>Modules</h1>
        <button type="button" class="add-module-btn" id="showAddModal" onclick="console.log('Button clicked directly')">
            <span style="font-size:1.3em;">&#43;</span> Add Module
        </button>
    </div>

    <!-- Program Selector -->
    <div class="program-selector">
        <label for="programSelect">Select Program to View/Manage Modules:</label>
        <select id="programSelect" name="program_id">
            <option value="">-- Select a Program --</option>
            @foreach($programs as $program)
                <option value="{{ $program->program_id }}"
                    {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                    {{ $program->program_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Modules Display Area -->
    <div id="modulesDisplayArea">
        @if(request('program_id') && isset($modules))
            @if($modules->count() > 0)
                <div class="modules-grid">
                    @foreach($modules as $module)
                        <div class="module-card">
                            <div class="module-title">{{ $module->module_name }}</div>
                            <div class="module-description">
                                {{ $module->module_description }}
                            </div>
                            @if($module->attachment)
                                <p><a href="{{ asset('storage/'.$module->attachment) }}" target="_blank">
                                    📎 Download file
                                </a></p>
                            @endif
                            <div class="module-meta">
                                <span class="module-program">
                                    {{ $module->program->program_name }}
                                </span>
                                <div class="module-actions">
                                    <button class="edit-module-btn" data-module-id="{{ $module->modules_id }}">
                                        Edit
                                    </button>
                                    <button class="delete-module-btn" data-module-id="{{ $module->modules_id }}">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-modules">
                    No modules found for this program.<br>
                    <small>Click "Add Module" to create the first module.</small>
                </div>
            @endif
        @else
            <div class="select-program-msg">
                Please select a program above to view and manage its modules.
            </div>
        @endif
    </div>
</div>

<!-- Add Module Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Add New Module</h3>
        <form action="{{ route('admin.modules.store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="addModuleForm">
            @csrf
            <select name="program_id" id="modalProgramSelect" required>
                <option value="">-- Select Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>

            <input type="text" name="module_name" placeholder="Module Name" required>
            <textarea name="module_description" placeholder="Module Description (optional)"></textarea>

            <div class="dropzone" id="addDropzone">
                <p>Drop PDF/DOC here or click to browse</p>
                <input type="file"
                       name="attachment"
                       id="addAttachment"
                       accept=".pdf,.doc,.docx">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Module</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal-bg" id="editModalBg">
    <div class="modal">
        <h3>Edit Module</h3>
        <form action="" 
              method="POST"
              enctype="multipart/form-data"
              id="editModuleForm">
            @csrf
            @method('PUT')

            <select name="program_id" id="editModalProgramSelect" required>
                <option value="">-- Select Program --</option>
                @foreach($programs as $program)
                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>

            <input type="text" name="module_name" id="editModuleName" placeholder="Module Name" required>
            <textarea name="module_description" id="editModuleDescription" placeholder="Module Description (optional)"></textarea>

            <div class="dropzone" id="editDropzone">
                <p>Drop PDF/DOC here or click to browse</p>
                <input type="file"
                       name="attachment"
                       id="editAttachment"
                       accept=".pdf,.doc,.docx">
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelEditModal">Cancel</button>
                <button type="submit" class="update-btn">Update Module</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing module functions...');
    
    // Program selector
    const programSelect = document.getElementById('programSelect');
    if (programSelect) {
        programSelect.addEventListener('change', function() {
            const pid = this.value;
            window.location.href = pid
                ? `{{ route('admin.modules.index') }}?program_id=${pid}`
                : `{{ route('admin.modules.index') }}`;
        });
    }

    // Modal elements
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg   = document.getElementById('addModalBg');
    const cancelAdd    = document.getElementById('cancelAddModal');
    const editModalBg  = document.getElementById('editModalBg');
    const cancelEdit   = document.getElementById('cancelEditModal');
    const editForm     = document.getElementById('editModuleForm');

    console.log('Modal elements:', {
        showAddModal: !!showAddModal,
        addModalBg: !!addModalBg,
        cancelAdd: !!cancelAdd
    });

    // Add Modal functionality
    if (showAddModal && addModalBg) {
        showAddModal.addEventListener('click', function() {
            console.log('Add button clicked');
            // Pre-select the current program in modal if one is selected
            const currentProgramId = programSelect?.value;
            if (currentProgramId) {
                const modalProgramSelect = document.getElementById('modalProgramSelect');
                if (modalProgramSelect) {
                    modalProgramSelect.value = currentProgramId;
                }
            }
            addModalBg.classList.add('show');
        });
    }

    if (cancelAdd && addModalBg) {
        cancelAdd.addEventListener('click', function() {
            addModalBg.classList.remove('show');
        });
    }

    if (cancelEdit && editModalBg) {
        cancelEdit.addEventListener('click', function() {
            editModalBg.classList.remove('show');
        });
    }

    // Click outside modal to close
    if (addModalBg) {
        addModalBg.addEventListener('click', function(e) {
            if (e.target === addModalBg) {
                addModalBg.classList.remove('show');
            }
        });
    }

    if (editModalBg) {
        editModalBg.addEventListener('click', function(e) {
            if (e.target === editModalBg) {
                editModalBg.classList.remove('show');
            }
        });
    }

    // Edit and Delete functionality
    document.addEventListener('click', e => {
        if (e.target.classList.contains('edit-module-btn')) {
            const id = e.target.dataset.moduleId;
            console.log('Edit button clicked for module:', id);
            
            // Find the module data from the current page
            const moduleCard = e.target.closest('.module-card');
            const moduleName = moduleCard.querySelector('.module-title').textContent.trim();
            const moduleDesc = moduleCard.querySelector('.module-description').textContent.trim();
            const programName = moduleCard.querySelector('.module-program').textContent.trim();
            
            // Find program ID by name
            const editProgramSelect = document.getElementById('editModalProgramSelect');
            for (let option of editProgramSelect.options) {
                if (option.textContent.trim() === programName) {
                    editProgramSelect.value = option.value;
                    break;
                }
            }
            
            // Fill the form
            document.getElementById('editModuleName').value = moduleName;
            document.getElementById('editModuleDescription').value = moduleDesc;
            
            editForm.action = `/admin/modules/${id}`;
            editModalBg.classList.add('show');
        }
        
        if (e.target.classList.contains('delete-module-btn')) {
            const id = e.target.dataset.moduleId;
            console.log('Delete button clicked for module:', id);
            if (confirm('Are you sure you want to delete this module?')) {
                const f = document.createElement('form');
                f.method = 'POST';
                f.action = `/admin/modules/${id}`;
                f.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(f);
                f.submit();
            }
        }
    });

    // Dropzone setup
    function setupDropzone(dzId, inputId) {
        const dz = document.getElementById(dzId);
        const inp = document.getElementById(inputId);
        if (!dz || !inp) return;
        
        const originalText = dz.querySelector('p').textContent;
        
        dz.addEventListener('click', () => inp.click());
        
        dz.addEventListener('dragover', e => { 
            e.preventDefault(); 
            dz.classList.add('dragover'); 
        });
        
        dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
        
        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                inp.files = e.dataTransfer.files;
                const fileName = e.dataTransfer.files[0].name;
                dz.querySelector('p').textContent = `📎 ${fileName}`;
                dz.style.borderColor = '#28a745';
                dz.style.color = '#28a745';
            }
        });
        
        inp.addEventListener('change', () => {
            if (inp.files.length) {
                const fileName = inp.files[0].name;
                dz.querySelector('p').textContent = `📎 ${fileName}`;
                dz.style.borderColor = '#28a745';
                dz.style.color = '#28a745';
            } else {
                dz.querySelector('p').textContent = originalText;
                dz.style.borderColor = '#667eea';
                dz.style.color = '#6c757d';
            }
        });
    }

    setupDropzone('addDropzone', 'addAttachment');
    setupDropzone('editDropzone', 'editAttachment');
    
    console.log('Module initialization complete');
});
</script>
@endpush
