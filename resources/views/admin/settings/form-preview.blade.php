@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Form Preview - ' . ucfirst($programType))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Form Preview - {{ ucfirst($programType) }} Program
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This is a preview of how the registration form will appear to students.
                    </div>

                    <form class="preview-form">
                        @csrf
                        <x-dynamic-enrollment-form :program-type="$programType" />
                    </form>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Settings
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('admin.settings.form-requirements.preview', 'complete') }}" 
                               class="btn btn-outline-primary {{ $programType === 'complete' ? 'active' : '' }}">
                                Complete Program
                            </a>
                            <a href="{{ route('admin.settings.form-requirements.preview', 'modular') }}" 
                               class="btn btn-outline-primary {{ $programType === 'modular' ? 'active' : '' }}">
                                Modular Program
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.preview-form {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
}

.preview-form .form-group {
    margin-bottom: 1rem;
}

.preview-form .form-control {
    background: white;
    border: 1px solid #ced4da;
}

.preview-form .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
@endsection
