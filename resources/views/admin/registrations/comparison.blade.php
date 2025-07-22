<div class="row">
    <div class="col-md-6">
        <h5 class="text-center bg-light p-2">Original Submission</h5>
        <div class="comparison-content">
            <table class="table table-sm table-striped">
                <tr>
                    <td><strong>First Name:</strong></td>
                    <td>{{ $originalData['first_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Last Name:</strong></td>
                    <td>{{ $originalData['last_name'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $originalData['email'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td>{{ $originalData['phone'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Date of Birth:</strong></td>
                    <td>{{ $originalData['date_of_birth'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Gender:</strong></td>
                    <td>{{ ucfirst($originalData['gender'] ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Civil Status:</strong></td>
                    <td>{{ ucfirst($originalData['civil_status'] ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td>{{ $originalData['address'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>City:</strong></td>
                    <td>{{ $originalData['city'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>State:</strong></td>
                    <td>{{ $originalData['state'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Country:</strong></td>
                    <td>{{ $originalData['country'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Postal Code:</strong></td>
                    <td>{{ $originalData['postal_code'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Education Level:</strong></td>
                    <td>{{ $originalData['education_level'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Course:</strong></td>
                    <td>{{ $originalData['course'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Program:</strong></td>
                    <td>{{ $originalData['program'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Learning Mode:</strong></td>
                    <td>{{ ucfirst($originalData['learning_mode'] ?? 'N/A') }}</td>
                </tr>
            </table>
            
            @if(isset($originalData['uploaded_files']) && is_array($originalData['uploaded_files']))
                <h6 class="mt-3">Original Files:</h6>
                <ul class="list-group list-group-flush">
                    @foreach($originalData['uploaded_files'] as $file)
                        <li class="list-group-item p-2">
                            <small>{{ basename($file) }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    
    <div class="col-md-6">
        <h5 class="text-center bg-success text-white p-2">Resubmitted Version</h5>
        <div class="comparison-content">
            <table class="table table-sm table-striped">
                <tr class="{{ $this->isFieldChanged('first_name', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>First Name:</strong></td>
                    <td>
                        {{ $currentData['first_name'] ?? 'N/A' }}
                        @if($this->isFieldChanged('first_name', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('last_name', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Last Name:</strong></td>
                    <td>
                        {{ $currentData['last_name'] ?? 'N/A' }}
                        @if($this->isFieldChanged('last_name', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('email', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Email:</strong></td>
                    <td>
                        {{ $currentData['email'] ?? 'N/A' }}
                        @if($this->isFieldChanged('email', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('phone', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Phone:</strong></td>
                    <td>
                        {{ $currentData['phone'] ?? 'N/A' }}
                        @if($this->isFieldChanged('phone', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('date_of_birth', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Date of Birth:</strong></td>
                    <td>
                        {{ $currentData['date_of_birth'] ?? 'N/A' }}
                        @if($this->isFieldChanged('date_of_birth', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('gender', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Gender:</strong></td>
                    <td>
                        {{ ucfirst($currentData['gender'] ?? 'N/A') }}
                        @if($this->isFieldChanged('gender', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('civil_status', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Civil Status:</strong></td>
                    <td>
                        {{ ucfirst($currentData['civil_status'] ?? 'N/A') }}
                        @if($this->isFieldChanged('civil_status', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('address', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Address:</strong></td>
                    <td>
                        {{ $currentData['address'] ?? 'N/A' }}
                        @if($this->isFieldChanged('address', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('city', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>City:</strong></td>
                    <td>
                        {{ $currentData['city'] ?? 'N/A' }}
                        @if($this->isFieldChanged('city', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('state', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>State:</strong></td>
                    <td>
                        {{ $currentData['state'] ?? 'N/A' }}
                        @if($this->isFieldChanged('state', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('country', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Country:</strong></td>
                    <td>
                        {{ $currentData['country'] ?? 'N/A' }}
                        @if($this->isFieldChanged('country', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('postal_code', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Postal Code:</strong></td>
                    <td>
                        {{ $currentData['postal_code'] ?? 'N/A' }}
                        @if($this->isFieldChanged('postal_code', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('education_level', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Education Level:</strong></td>
                    <td>
                        {{ $currentData['education_level'] ?? 'N/A' }}
                        @if($this->isFieldChanged('education_level', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('course', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Course:</strong></td>
                    <td>
                        {{ $currentData['course'] ?? 'N/A' }}
                        @if($this->isFieldChanged('course', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('program', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Program:</strong></td>
                    <td>
                        {{ $currentData['program'] ?? 'N/A' }}
                        @if($this->isFieldChanged('program', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
                <tr class="{{ $this->isFieldChanged('learning_mode', $originalData, $currentData) ? 'table-warning' : '' }}">
                    <td><strong>Learning Mode:</strong></td>
                    <td>
                        {{ ucfirst($currentData['learning_mode'] ?? 'N/A') }}
                        @if($this->isFieldChanged('learning_mode', $originalData, $currentData))
                            <i class="fas fa-edit text-warning ms-1" title="Changed"></i>
                        @endif
                    </td>
                </tr>
            </table>
            
            @if(isset($currentData['uploaded_files']) && is_array($currentData['uploaded_files']))
                <h6 class="mt-3">
                    Resubmitted Files:
                    @if($this->isFieldChanged('uploaded_files', $originalData, $currentData))
                        <i class="fas fa-edit text-warning ms-1" title="Files Changed"></i>
                    @endif
                </h6>
                <ul class="list-group list-group-flush">
                    @foreach($currentData['uploaded_files'] as $file)
                        <li class="list-group-item p-2">
                            <small>{{ basename($file) }}</small>
                            <a href="{{ Storage::url($file) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">View</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

@if($rejectedFields && count($rejectedFields) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle"></i> Previously Rejected Fields:</h6>
            <div class="row">
                @foreach($rejectedFields as $field)
                    <div class="col-md-4">
                        <span class="badge bg-danger me-1">
                            {{ ucfirst(str_replace('_', ' ', $field)) }}
                            @if($this->isFieldChanged($field, $originalData, $currentData))
                                <i class="fas fa-check ms-1" title="Student has made changes to this field"></i>
                            @else
                                <i class="fas fa-exclamation ms-1" title="No changes made to this field"></i>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<style>
.comparison-content {
    max-height: 600px;
    overflow-y: auto;
}

.table-warning {
    background-color: #fff3cd !important;
}

.comparison-content table tr td {
    border: 1px solid #dee2e6;
    padding: 0.5rem;
}
</style>

@php
function isFieldChanged($field, $original, $current) {
    $originalValue = $original[$field] ?? null;
    $currentValue = $current[$field] ?? null;
    
    // Handle arrays (like uploaded_files)
    if (is_array($originalValue) && is_array($currentValue)) {
        return serialize($originalValue) !== serialize($currentValue);
    }
    
    return $originalValue !== $currentValue;
}
@endphp
