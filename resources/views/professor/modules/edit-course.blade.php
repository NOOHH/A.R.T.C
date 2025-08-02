@extends('professor.professor-layouts.professor-layout')

@section('title', 'Edit Course')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Course</h4>
                    <small class="text-muted">
                        Program: {{ $program->program_name }} > Module: {{ $module->module_name }}
                    </small>
                </div>
                <div class="card-body">
                    <form id="editCourseForm" method="POST" action="{{ route('professor.courses.update', $course->subject_id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject_name" name="subject_name" 
                                           value="{{ old('subject_name', $course->subject_name) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_price" class="form-label">Course Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="subject_price" name="subject_price" 
                                               step="0.01" min="0" value="{{ old('subject_price', $course->subject_price) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject_description" class="form-label">Course Description</label>
                            <textarea class="form-control" id="subject_description" name="subject_description" 
                                      rows="4" placeholder="Enter course description...">{{ old('subject_description', $course->subject_description) }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_order" class="form-label">Course Order</label>
                                    <input type="number" class="form-control" id="subject_order" name="subject_order" 
                                           min="1" value="{{ old('subject_order', $course->subject_order) }}">
                                    <small class="form-text text-muted">Order in which this course appears in the module</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_required" name="is_required" 
                                               value="1" {{ old('is_required', $course->is_required) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_required">
                                            Required Course
                                        </label>
                                        <small class="form-text text-muted d-block">Students must complete this course to progress</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Course
                                </label>
                                <small class="form-text text-muted d-block">Only active courses are visible to students</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('professor.modules.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Modules
                            </a>
                            
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Update Course
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@endsection

@section('scripts')
<script>
document.getElementById('editCourseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-half"></i> Updating...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('Course updated successfully!', 'success');
            
            // Redirect back to modules after a short delay
            setTimeout(() => {
                window.location.href = '{{ route('professor.modules.index') }}';
            }, 1500);
        } else {
            showAlert('Error updating course: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating course. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-check-lg"></i> Update Course';
    });
});

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}
</script>
@endsection
