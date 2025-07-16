@extends('admin.admin-dashboard-layout')

@section('title', 'Add Director')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-plus"></i> Add New Director</h2>
                <a href="{{ route('admin.directors.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Directors
                </a>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.directors.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_name" class="form-label">Director Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('directors_name') is-invalid @enderror" 
                                           id="directors_name" name="directors_name" value="{{ old('directors_name') }}" required>
                                    @error('directors_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('directors_email') is-invalid @enderror" 
                                           id="directors_email" name="directors_email" value="{{ old('directors_email') }}" required>
                                    @error('directors_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('directors_password') is-invalid @enderror" 
                                           id="directors_password" name="directors_password" required>
                                    @error('directors_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="referral_code" class="form-label">Referral Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('referral_code') is-invalid @enderror" 
                                               id="referral_code" name="referral_code" value="{{ old('referral_code') }}" 
                                               placeholder="Auto-generated if empty">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateReferralCode()" title="Generate New Code">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Leave empty to auto-generate based on name (e.g., DIR01NAME)</div>
                                    @error('referral_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.directors.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Director
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
    const firstName = document.getElementById('directors_name').value.split(' ')[0] || '';
    const lastName = document.getElementById('directors_name').value.split(' ').slice(1).join(' ') || '';
    
    if (!firstName.trim()) {
        alert('Please enter the director name first');
        return;
    }
    
    // Generate code based on name
    const cleanFirstName = firstName.replace(/[^A-Za-z]/g, '').toUpperCase();
    const cleanLastName = lastName.replace(/[^A-Za-z]/g, '').toUpperCase();
    
    // Get next director ID (simplified - in production should be from server)
    const nextId = String(Math.floor(Math.random() * 99) + 1).padStart(2, '0');
    
    // Generate code: DIR + ID + NAME_INITIALS
    const nameCode = cleanFirstName.substring(0, 1) + cleanLastName.substring(0, 3);
    const referralCode = 'DIR' + nextId + nameCode;
    
    document.getElementById('referral_code').value = referralCode;
}

// Auto-generate when name changes
document.getElementById('directors_name').addEventListener('input', function() {
    const referralCodeField = document.getElementById('referral_code');
    if (!referralCodeField.value.trim()) {
        setTimeout(generateReferralCode, 500); // Small delay for better UX
    }
});
</script>
@endpush
