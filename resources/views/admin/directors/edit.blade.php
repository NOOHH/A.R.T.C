@extends('admin.admin-dashboard-layout')

@section('title', 'Edit Director')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-pencil"></i> Edit Director: {{ $director->full_name }}</h2>
                <a href="{{ route('admin.directors.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Directors
                </a>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.directors.update', $director) }}" autocomplete="off">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('directors_first_name') is-invalid @enderror" 
                                           id="directors_first_name" name="directors_first_name" value="{{ old('directors_first_name', $director->directors_first_name) }}" required>
                                    @error('directors_first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('directors_last_name') is-invalid @enderror" 
                                           id="directors_last_name" name="directors_last_name" value="{{ old('directors_last_name', $director->directors_last_name) }}" required>
                                    @error('directors_last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('directors_email') is-invalid @enderror" 
                                           id="directors_email" name="directors_email" value="{{ old('directors_email', $director->directors_email) }}" required autocomplete="off">
                                    @error('directors_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_password" class="form-label">Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control @error('directors_password') is-invalid @enderror" 
                                           id="directors_password" name="directors_password" autocomplete="new-password">
                                    @error('directors_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="referral_code" class="form-label">Referral Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('referral_code') is-invalid @enderror" 
                                               id="referral_code" name="referral_code" 
                                               value="{{ old('referral_code', $director->referral_code) }}" 
                                               placeholder="Auto-generated if empty">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateReferralCode()" title="Generate New Code">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Current: {{ $director->referral_code ?? 'Not set' }}</div>
                                    @error('referral_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Assign Programs <span class="text-danger">*</span></label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllPrograms" onclick="toggleAllProgramsCheckboxes(this)">
                                        <label class="form-check-label" for="selectAllPrograms">Select All Programs</label>
                                    </div>
                                    <div id="programCheckboxList" style="border: 1px solid #ced4da; border-radius: 0.375rem; max-height: 220px; overflow-y: auto; padding: 0.75rem; background: #fafbfc;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="program_all" name="program_access[]" value="all" {{ $director->has_all_program_access ? 'checked' : '' }}>
                                            <label class="form-check-label" for="program_all">All Programs</label>
                                        </div>
                                        @foreach($programs as $program)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="program_{{ $program->program_id }}" name="program_access[]" value="{{ $program->program_id }}" {{ $director->assignedPrograms->contains('program_id', $program->program_id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="program_{{ $program->program_id }}">{{ $program->program_name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-text">Check one or more programs, or select 'All Programs'.</div>
                                    @error('program_access')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.directors.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Director
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
function generateReferralCode() {
    const firstName = document.getElementById('directors_first_name').value.split(' ')[0] || '';
    const lastName = document.getElementById('directors_last_name').value.split(' ')[0] || '';
    
    if (!firstName.trim() && !lastName.trim()) {
        alert('Please enter the director name first');
        return;
    }
    
    // Generate code based on name
    const cleanFirstName = firstName.replace(/[^A-Za-z]/g, '').toUpperCase();
    const cleanLastName = lastName.replace(/[^A-Za-z]/g, '').toUpperCase();
    
    // Get current director ID from URL
    const currentId = window.location.pathname.split('/').pop();
    const directorId = String(currentId).padStart(2, '0');
    
    // Generate code: DIR + ID + NAME_INITIALS
    const nameCode = cleanFirstName.substring(0, 2) + cleanLastName.substring(0, 2);
    const referralCode = 'DIR' + directorId + nameCode;
    
    document.getElementById('referral_code').value = referralCode;
}

function toggleAllProgramsCheckboxes(checkbox) {
    const checkboxes = document.querySelectorAll('#programCheckboxList input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}
</script>
@endpush
