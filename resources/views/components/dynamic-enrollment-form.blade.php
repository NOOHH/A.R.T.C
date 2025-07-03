{{-- Dynamic Enrollment Form Component --}}
<div class="dynamic-form-container">
    @foreach($requirements as $requirement)
        <div class="form-group mb-3" data-field="{{ $requirement->field_name }}">
            @if($requirement->field_type === 'text' || $requirement->field_type === 'email' || $requirement->field_type === 'tel')
                <label for="{{ $requirement->field_name }}" class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <input type="{{ $requirement->field_type }}" 
                       class="form-control" 
                       id="{{ $requirement->field_name }}" 
                       name="{{ $requirement->field_name }}"
                       placeholder="{{ $requirement->field_label }}"
                       @if($requirement->is_required) required @endif>
            
            @elseif($requirement->field_type === 'date')
                <label for="{{ $requirement->field_name }}" class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <input type="date" 
                       class="form-control" 
                       id="{{ $requirement->field_name }}" 
                       name="{{ $requirement->field_name }}"
                       @if($requirement->is_required) required @endif>
            
            @elseif($requirement->field_type === 'file')
                <label for="{{ $requirement->field_name }}" class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <div class="file-upload-container">
                    <button type="button" class="btn btn-outline-primary file-upload-btn" data-field="{{ $requirement->field_name }}">
                        <i class="bi bi-cloud-upload"></i> {{ $requirement->field_label }}
                    </button>
                    <input type="file" 
                           class="form-control d-none" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}"
                           @if($requirement->is_required) required @endif>
                    <div class="file-status mt-2" id="status-{{ $requirement->field_name }}"></div>
                </div>
            
            @elseif($requirement->field_type === 'textarea')
                <label for="{{ $requirement->field_name }}" class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <textarea class="form-control" 
                          id="{{ $requirement->field_name }}" 
                          name="{{ $requirement->field_name }}"
                          rows="3"
                          placeholder="{{ $requirement->field_label }}"
                          @if($requirement->is_required) required @endif></textarea>
            
            @elseif($requirement->field_type === 'number')
                <label for="{{ $requirement->field_name }}" class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <input type="number" 
                       class="form-control" 
                       id="{{ $requirement->field_name }}" 
                       name="{{ $requirement->field_name }}"
                       placeholder="{{ $requirement->field_label }}"
                       @if($requirement->is_required) required @endif>
                       
            @elseif($requirement->field_type === 'checkbox')
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="{{ $requirement->field_name }}" 
                           name="{{ $requirement->field_name }}"
                           value="1"
                           @if($requirement->is_required) required @endif>
                    <label class="form-check-label" for="{{ $requirement->field_name }}">
                        {{ $requirement->field_label }}
                        @if($requirement->is_required)<span class="text-danger">*</span>@endif
                    </label>
                </div>
                
            @elseif($requirement->field_type === 'radio')
                <label class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                @if($requirement->field_options)
                    @foreach($requirement->field_options as $index => $option)
                        <div class="form-check">
                            <input type="radio" 
                                   class="form-check-input" 
                                   id="{{ $requirement->field_name }}_{{ $index }}" 
                                   name="{{ $requirement->field_name }}"
                                   value="{{ $option }}"
                                   @if($requirement->is_required) required @endif>
                            <label class="form-check-label" for="{{ $requirement->field_name }}_{{ $index }}">
                                {{ $option }}
                            </label>
                        </div>
                    @endforeach
                @endif
            
            @elseif($requirement->field_type === 'select')
                <label for="{{ $requirement->field_name }}" class="form-label">
                    {{ $requirement->field_label }}
                    @if($requirement->is_required)<span class="text-danger">*</span>@endif
                </label>
                <select class="form-select" 
                        id="{{ $requirement->field_name }}" 
                        name="{{ $requirement->field_name }}"
                        @if($requirement->is_required) required @endif>
                    <option value="">Select an option</option>
                    @if($requirement->field_options)
                        @foreach($requirement->field_options as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    @endif
                </select>
            @endif
            
            {{-- Display validation error for this field --}}
            @error($requirement->field_name)
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    @endforeach
</div>

@push('styles')
<style>
.file-upload-btn {
    width: 100%;
    padding: 12px;
    border: 2px dashed #007bff;
    border-radius: 8px;
    background: transparent;
    transition: all 0.3s ease;
}

.file-upload-btn:hover {
    background: rgba(0, 123, 255, 0.1);
    border-color: #0056b3;
}

.file-status {
    font-size: 0.875rem;
    color: #6c757d;
}

.file-status.success {
    color: #28a745;
}

.file-status.error {
    color: #dc3545;
}

.dynamic-form-container .form-control:focus,
.dynamic-form-container .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle file upload buttons
    document.querySelectorAll('.file-upload-btn').forEach(button => {
        button.addEventListener('click', function() {
            const fieldName = this.dataset.field;
            const fileInput = document.getElementById(fieldName);
            fileInput.click();
        });
    });
    
    // Handle file input changes
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const fieldName = this.name;
            const statusDiv = document.getElementById(`status-${fieldName}`);
            const button = document.querySelector(`[data-field="${fieldName}"]`);
            
            if (this.files.length > 0) {
                const file = this.files[0];
                statusDiv.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${file.name} selected`;
                statusDiv.className = 'file-status success';
                button.innerHTML = `<i class="bi bi-check-circle"></i> ${file.name}`;
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
            } else {
                statusDiv.innerHTML = '';
                statusDiv.className = 'file-status';
                const originalLabel = button.closest('.form-group').querySelector('label').textContent.replace('*', '').trim();
                button.innerHTML = `<i class="bi bi-cloud-upload"></i> ${originalLabel}`;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }
        });
    });
});
</script>
@endpush