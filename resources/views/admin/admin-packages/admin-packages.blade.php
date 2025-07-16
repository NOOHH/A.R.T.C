@extends('admin.admin-dashboard-layout')

@section('title', 'Package Management')

@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/admin-packages.css') }}">
@endsection

@push('styles')
<style>
  .main-content-wrapper {
    align-items: flex-start !important;
  }

  .packages-container {
    background: #fff;
    padding: 30px;
    margin: 20px 0;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  .analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .analytics-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  .analytics-card h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
  }

  .analytics-card p {
    margin: 10px 0 0;
    opacity: 0.9;
  }

  .package-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 30px;
  }

  .package-item {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    border: 1px solid #e9ecef;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }

  .package-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }

  .package-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    position: relative;
  }

  .package-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    text-align: center;
  }

  .package-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
  }

  .package-content {
    padding: 25px;
  }

  .package-description {
    color: #6c757d;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 20px;
  }

  .package-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
  }

  .package-detail {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
  }

  .package-detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    margin-bottom: 5px;
  }

  .package-detail-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #333;
  }

  .package-price {
    font-size: 2rem;
    font-weight: 700;
    color: #28a745;
    text-align: center;
    margin-bottom: 20px;
  }

  .package-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
  }

  .btn-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
  }

  .btn-delete {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
  }

  .add-package-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 30px;
  }

  .add-package-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
  }

  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
  }

  .modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 15px;
    padding: 30px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
  }

  .modal-header {
    text-align: center;
    margin-bottom: 30px;
  }

  .modal-header h2 {
    color: #333;
    margin: 0;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
  }

  .btn-secondary {
    background: #6c757d;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-secondary:hover {
    background: #5a6268;
  }

  .module-selection {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
  }

  .module-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 15px;
  }

  .module-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .module-checkbox input[type="checkbox"] {
    width: auto;
    margin: 0;
  }

  .selected-modules-display {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
  }

  .selected-modules-display h4 {
    margin: 0 0 10px;
    color: #1976d2;
  }

  .module-count-info {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
  }

  .popularity-badge {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .popularity-badge.high {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  }

  .popularity-badge.medium {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
  }

  .popularity-badge.low {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
  }
      font-size: 2rem;
    font-weight: 800;
    color: #2c3e50;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Action buttons */
  .package-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
  }
  .edit-package-btn,
  .delete-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s ease;
  }
  .edit-package-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
  }
  .edit-package-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
  }
  .delete-btn {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220,53,69,0.3);
  }
  .delete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220,53,69,0.4);
  }

  /* “No packages” message */
  .no-packages {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 20px;
    color: #6c757d;
    font-size: 1.2rem;
  }

  /* Modals (unchanged) */
  .modal-bg { /* … */ }
  .modal { /* … */ }
  .modal h3 { /* … */ }
  .modal input, .modal textarea { /* … */ }
  .modal-actions { /* … */ }
  .cancel-btn, .add-btn { /* … */ }

  /* Responsive */
  @media (max-width: 768px) {
    .packages-header {
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }
    .packages-header h1 {
      font-size: 2rem;
    }
    .package-list {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .modal {
      padding: 30px 20px;
      margin: 20px;
    }
    .modal-actions {
      flex-direction: column;
    }
  }
  @media (max-width: 480px) {
    .package-list {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
<div class="main-content-wrapper">
    <div class="packages-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Package Management</h1>
            <button class="add-package-btn" onclick="showAddModal()">
                <i class="fas fa-plus"></i> Add New Package
            </button>
        </div>

        <!-- Analytics Section -->
        <div class="analytics-grid">
            <div class="analytics-card">
                <h3>{{ $totalPackages ?? 0 }}</h3>
                <p><i class="fas fa-box"></i> Total Packages</p>
            </div>
            <div class="analytics-card">
                <h3>{{ $activeEnrollments ?? 0 }}</h3>
                <p><i class="fas fa-users"></i> Active Enrollments</p>
            </div>
            <div class="analytics-card">
                <h3>₱{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                <p><i class="fas fa-chart-line"></i> Total Revenue</p>
            </div>
            <div class="analytics-card">
                <h3>{{ $popularityRate ?? 0 }}%</h3>
                <p><i class="fas fa-star"></i> Popularity Rate</p>
            </div>
        </div>

        <!-- Package List -->
        <div class="package-list">
            @foreach($packages as $package)
                <div class="package-item" data-package-id="{{ $package->package_id }}">
                    <div class="package-header">
                        <div class="package-badge">{{ ucfirst($package->package_type) }}</div>
                        <h3 class="package-name">{{ $package->package_name }}</h3>
                    </div>
                    <div class="package-content">
                        <p class="package-description">{{ $package->description }}</p>
                        
                        <div class="package-details">
                            <div class="package-detail">
                                <div class="package-detail-label">Type</div>
                                <div class="package-detail-value">{{ ucfirst($package->package_type) }}</div>
                            </div>
                            <div class="package-detail">
                                <div class="package-detail-label">Modules</div>
                                <div class="package-detail-value">{{ $package->module_count ?? 'All' }}</div>
                            </div>
                            <div class="package-detail">
                                <div class="package-detail-label">Enrollments</div>
                                <div class="package-detail-value">{{ $package->enrollments_count ?? 0 }}</div>
                            </div>
                            <div class="package-detail">
                                <div class="package-detail-label">
                                    Popularity 
                                    @php
                                        $popularity = $package->enrollments_count ?? 0;
                                        $popularityClass = $popularity > 50 ? 'high' : ($popularity > 20 ? 'medium' : 'low');
                                        $popularityText = $popularity > 50 ? 'High' : ($popularity > 20 ? 'Medium' : 'Low');
                                    @endphp
                                    <span class="popularity-badge {{ $popularityClass }}">{{ $popularityText }}</span>
                                </div>
                                <div class="package-detail-value">{{ $popularity }}%</div>
                            </div>
                        </div>
                        
                        <div class="package-price">₱{{ number_format($package->price ?? 0, 2) }}</div>
                        
                        <div class="package-actions">
                            <button class="btn-edit" onclick="editPackage({{ $package->package_id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-delete" onclick="deletePackage({{ $package->package_id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal" id="addPackageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Package</h2>
        </div>
        <form id="addPackageForm" method="POST" action="{{ route('admin.packages.store') }}">
            @csrf
            <div class="form-group">
                <label for="package_name">Package Name</label>
                <input type="text" id="package_name" name="package_name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="package_type">Package Type</label>
                    <select id="package_type" name="package_type" required onchange="handlePackageTypeChange()">
                        <option value="full">Full Enrollment</option>
                        <option value="modular">Modular Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="program_id">Program</label>
                    <select id="program_id" name="program_id" required>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group" id="moduleCountGroup" style="display: none;">
                    <label for="module_count">Module Count</label>
                    <input type="number" id="module_count" name="module_count" min="1" max="50">
                </div>
            </div>
            
            <div class="module-selection" id="moduleSelection" style="display: none;">
                <h4>Select Modules (Optional)</h4>
                <div class="module-count-info">
                    <small>Select specific modules for this package. If none selected, all modules will be available.</small>
                </div>
                <div class="module-checkboxes" id="moduleCheckboxes">
                    <!-- Modules will be loaded dynamically -->
                </div>
                <div class="selected-modules-display" id="selectedModulesDisplay" style="display: none;">
                    <h4>Selected Modules: <span id="selectedModulesCount">0</span></h4>
                    <div id="selectedModulesList"></div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create Package</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal" id="editPackageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Package</h2>
        </div>
        <form id="editPackageForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_package_name">Package Name</label>
                <input type="text" id="edit_package_name" name="package_name" required>
            </div>
            
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_package_type">Package Type</label>
                    <select id="edit_package_type" name="package_type" required onchange="handleEditPackageTypeChange()">
                        <option value="full">Full Enrollment</option>
                        <option value="modular">Modular Enrollment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_price">Price (₱)</label>
                    <input type="number" id="edit_price" name="price" min="0" step="0.01" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_program_id">Program</label>
                    <select id="edit_program_id" name="program_id" required>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group" id="editModuleCountGroup" style="display: none;">
                    <label for="edit_module_count">Module Count</label>
                    <input type="number" id="edit_module_count" name="module_count" min="1" max="50">
                </div>
            </div>
            
            <div class="module-selection" id="editModuleSelection" style="display: none;">
                <h4>Select Modules (Optional)</h4>
                <div class="module-count-info">
                    <small>Select specific modules for this package. If none selected, all modules will be available.</small>
                </div>
                <div class="module-checkboxes" id="editModuleCheckboxes">
                    <!-- Modules will be loaded dynamically -->
                </div>
                <div class="selected-modules-display" id="editSelectedModulesDisplay" style="display: none;">
                    <h4>Selected Modules: <span id="editSelectedModulesCount">0</span></h4>
                    <div id="editSelectedModulesList"></div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Update Package</button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedModules = [];
let editSelectedModules = [];

// Modal functions
function showAddModal() {
    document.getElementById('addPackageModal').classList.add('active');
    loadModulesForProgram(document.getElementById('program_id').value, 'add');
}

function closeAddModal() {
    document.getElementById('addPackageModal').classList.remove('active');
    document.getElementById('addPackageForm').reset();
    selectedModules = [];
    updateSelectedModulesDisplay();
}

function editPackage(packageId) {
    // Load package data via AJAX
    fetch(`/admin/packages/${packageId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_package_name').value = data.package_name;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_package_type').value = data.package_type;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_program_id').value = data.program_id;
            document.getElementById('edit_module_count').value = data.module_count || '';
            
            // Handle package type specific fields
            handleEditPackageTypeChange();
            
            // Set form action
            document.getElementById('editPackageForm').action = `/admin/packages/${packageId}`;
            
            // Show modal
            document.getElementById('editPackageModal').classList.add('active');
            
            // Load modules for the selected program
            loadModulesForProgram(data.program_id, 'edit');
        })
        .catch(error => {
            console.error('Error loading package:', error);
            alert('Error loading package data');
        });
}

function closeEditModal() {
    document.getElementById('editPackageModal').classList.remove('active');
    editSelectedModules = [];
    updateEditSelectedModulesDisplay();
}

function deletePackage(packageId) {
    if (confirm('Are you sure you want to delete this package?')) {
        fetch(`/admin/packages/${packageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting package');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting package');
        });
    }
}

// Package type change handlers
function handlePackageTypeChange() {
    const packageType = document.getElementById('package_type').value;
    const moduleCountGroup = document.getElementById('moduleCountGroup');
    const moduleSelection = document.getElementById('moduleSelection');
    
    if (packageType === 'modular') {
        moduleCountGroup.style.display = 'block';
        moduleSelection.style.display = 'block';
        document.getElementById('module_count').setAttribute('required', 'required');
    } else {
        moduleCountGroup.style.display = 'none';
        moduleSelection.style.display = 'none';
        document.getElementById('module_count').removeAttribute('required');
    }
}

function handleEditPackageTypeChange() {
    const packageType = document.getElementById('edit_package_type').value;
    const moduleCountGroup = document.getElementById('editModuleCountGroup');
    const moduleSelection = document.getElementById('editModuleSelection');
    
    if (packageType === 'modular') {
        moduleCountGroup.style.display = 'block';
        moduleSelection.style.display = 'block';
        document.getElementById('edit_module_count').setAttribute('required', 'required');
    } else {
        moduleCountGroup.style.display = 'none';
        moduleSelection.style.display = 'none';
        document.getElementById('edit_module_count').removeAttribute('required');
    }
}

// Load modules for program
function loadModulesForProgram(programId, mode) {
    fetch(`/get-program-modules?program_id=${programId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const containerId = mode === 'add' ? 'moduleCheckboxes' : 'editModuleCheckboxes';
                const container = document.getElementById(containerId);
                
                container.innerHTML = data.modules.map(module => `
                    <div class="module-checkbox">
                        <input type="checkbox" id="${mode}_module_${module.id}" value="${module.id}" 
                               onchange="handleModuleSelection(this, '${mode}')">
                        <label for="${mode}_module_${module.id}">${module.name}</label>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading modules:', error);
        });
}

// Handle module selection
function handleModuleSelection(checkbox, mode) {
    const moduleId = checkbox.value;
    const moduleLabel = checkbox.nextElementSibling.textContent;
    
    if (mode === 'add') {
        if (checkbox.checked) {
            selectedModules.push({ id: moduleId, name: moduleLabel });
        } else {
            selectedModules = selectedModules.filter(m => m.id !== moduleId);
        }
        updateSelectedModulesDisplay();
    } else {
        if (checkbox.checked) {
            editSelectedModules.push({ id: moduleId, name: moduleLabel });
        } else {
            editSelectedModules = editSelectedModules.filter(m => m.id !== moduleId);
        }
        updateEditSelectedModulesDisplay();
    }
}

// Update selected modules display
function updateSelectedModulesDisplay() {
    const display = document.getElementById('selectedModulesDisplay');
    const count = document.getElementById('selectedModulesCount');
    const list = document.getElementById('selectedModulesList');
    
    count.textContent = selectedModules.length;
    list.innerHTML = selectedModules.map(module => 
        `<span class="badge bg-primary me-2">${module.name}</span>`
    ).join('');
    
    display.style.display = selectedModules.length > 0 ? 'block' : 'none';
}

function updateEditSelectedModulesDisplay() {
    const display = document.getElementById('editSelectedModulesDisplay');
    const count = document.getElementById('editSelectedModulesCount');
    const list = document.getElementById('editSelectedModulesList');
    
    count.textContent = editSelectedModules.length;
    list.innerHTML = editSelectedModules.map(module => 
        `<span class="badge bg-primary me-2">${module.name}</span>`
    ).join('');
    
    display.style.display = editSelectedModules.length > 0 ? 'block' : 'none';
}

// Program change handlers
document.getElementById('program_id').addEventListener('change', function() {
    loadModulesForProgram(this.value, 'add');
});

document.getElementById('edit_program_id').addEventListener('change', function() {
    loadModulesForProgram(this.value, 'edit');
});

// Form submission handlers
document.getElementById('addPackageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add selected modules to form data
    selectedModules.forEach(module => {
        formData.append('selected_modules[]', module.id);
    });
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error creating package: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating package');
    });
});

document.getElementById('editPackageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add selected modules to form data
    editSelectedModules.forEach(module => {
        formData.append('selected_modules[]', module.id);
    });
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating package: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating package');
    });
});

// Close modals on outside click
document.getElementById('addPackageModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editPackageModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to meta tag if not exists
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }
});
</script>
@endsection

</style>

@section('content')
<div class="main-content-wrapper" style="display: flex; flex-direction: column; align-items: center; width: 100%; min-width: 0;">
    <div class="packages-container">
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <h1 class="display-4 fw-bold text-uppercase text-dark mb-0" style="letter-spacing: 2px;">Packages</h1>
            <button class="btn btn-lg text-white fw-semibold px-4 py-3 rounded-pill shadow add-package-btn" 
                    id="showAddModal" 
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span style="font-size:1.3em;">&#43;</span> Add Package
            </button>
        </div>
        
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
        
        <div class="package-list">
            @forelse($packages as $package)
            <div class="package-item">
                <div class="package-header">
                    <h3 class="package-name">{{ $package->package_name }}</h3>
                    <div class="package-badge">Premium</div>
                </div>
                <div class="package-content">
                    <div class="package-description" title="{{ $package->description }}">
                        {{ $package->description }}
                    </div>
                    <div class="package-price">₱{{ number_format($package->amount ?? 0, 2) }}</div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-success flex-fill fw-semibold edit-package-btn" 
                                data-id="{{ $package->package_id }}" 
                                data-name="{{ $package->package_name }}" 
                                data-description="{{ $package->description }}" 
                                data-amount="{{ $package->amount }}">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <form action="{{ route('admin.packages.delete', $package->package_id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 fw-semibold" 
                                    onclick="return confirm('Are you sure you want to delete this package?')">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-light border border-2 border-dashed rounded-4 p-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted mb-2">No packages found</h3>
                        <p class="text-muted">Create your first package to get started!</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Create Package</h3>
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf
            <input type="text" name="package_name" placeholder="Package Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="amount" placeholder="Amount (₱)" min="0" step="0.01" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Package</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-bg" id="editModalBg">
    <div class="modal">
        <h3>Edit Package</h3>
        <form id="editPackageForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="package_name" id="editPackageName" placeholder="Package Name" required>
            <textarea name="description" id="editPackageDescription" placeholder="Description" required></textarea>
            <input type="number" name="amount" id="editPackageAmount" placeholder="Amount (₱)" min="0" step="0.01" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelEditModal">Cancel</button>
                <button type="submit" class="add-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal functionality
    document.getElementById('showAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.add('active');
    };
    document.getElementById('cancelAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.remove('active');
    };
    document.getElementById('addModalBg').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };

    // Edit package functionality
    document.querySelectorAll('.edit-package-btn').forEach(function(btn) {
        btn.onclick = function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            var desc = this.getAttribute('data-description');
            var amount = this.getAttribute('data-amount');
            
            document.getElementById('editPackageName').value = name;
            document.getElementById('editPackageDescription').value = desc;
            document.getElementById('editPackageAmount').value = amount;
            
            var form = document.getElementById('editPackageForm');
            form.action = '/admin/packages/' + id;
            document.getElementById('editModalBg').classList.add('active');
        };
    });
    
    document.getElementById('cancelEditModal').onclick = function () {
        document.getElementById('editModalBg').classList.remove('active');
    };
    document.getElementById('editModalBg').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };

    // Add smooth scrolling to top when modals close
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection
