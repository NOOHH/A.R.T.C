@extends('admin.admin-dashboard-layout')

@section('title', 'Module Management')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/admin-settings/admin-settings.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-puzzle-piece me-2"></i>Module Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <button type="button" class="btn btn-success" id="addModuleBtn">
                            <i class="fas fa-plus"></i> Add New Module
                        </button>
                        <button type="button" class="btn btn-info" id="saveOrderBtn">
                            <i class="fas fa-save"></i> Save Order
                        </button>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Drag and drop modules to reorder them. The order will be reflected in the enrollment forms.
                    </div>

                    <div id="modulesContainer">
                        @foreach($modules as $module)
                            <div class="module-item border rounded p-3 mb-3 d-flex align-items-center" 
                                 data-module-id="{{ $module->modules_id }}">
                                <div class="module-handle me-3">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="mb-1">{{ $module->module_name }}</h6>
                                            <small class="text-muted">ID: {{ $module->modules_id }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0">{{ Str::limit($module->module_description, 100) }}</p>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge bg-secondary">Order: {{ $module->module_order }}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                    onclick="editModule({{ $module->modules_id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteModule({{ $module->modules_id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($modules->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-puzzle-piece fa-3x text-muted mb-3"></i>
                            <h5>No modules found</h5>
                            <p class="text-muted">Create your first module to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable for modules
    const modulesContainer = document.getElementById('modulesContainer');
    if (modulesContainer) {
        new Sortable(modulesContainer, {
            animation: 150,
            ghostClass: 'dragging-placeholder',
            handle: '.module-handle',
            onEnd: function (evt) {
                console.log(`Module moved: ${evt.oldIndex} -> ${evt.newIndex}`);
                updateModuleOrder();
            }
        });
    }

    // Save order button
    document.getElementById('saveOrderBtn').addEventListener('click', function() {
        saveModuleOrder();
    });
});

function updateModuleOrder() {
    const moduleItems = document.querySelectorAll('.module-item');
    const moduleIds = [];
    
    moduleItems.forEach((item, index) => {
        const moduleId = item.getAttribute('data-module-id');
        moduleIds.push(moduleId);
        
        // Update the order badge
        const orderBadge = item.querySelector('.badge');
        if (orderBadge) {
            orderBadge.textContent = `Order: ${index + 1}`;
        }
    });
    
    return moduleIds;
}

function saveModuleOrder() {
    const moduleIds = updateModuleOrder();
    
    fetch('/admin/modules/update-sort-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            module_ids: moduleIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Module order saved successfully!', 'success');
        } else {
            showAlert('Error saving module order: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving module order', 'danger');
    });
}

function editModule(moduleId) {
    // Implement edit functionality
    console.log('Edit module:', moduleId);
    // You can redirect to edit page or show modal
}

function deleteModule(moduleId) {
    if (confirm('Are you sure you want to delete this module?')) {
        // Implement delete functionality
        console.log('Delete module:', moduleId);
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
