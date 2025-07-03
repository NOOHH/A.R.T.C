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
                    <form method="POST" action="{{ route('admin.directors.update', $director) }}">
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
                                           id="directors_email" name="directors_email" value="{{ old('directors_email', $director->directors_email) }}" required>
                                    @error('directors_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="directors_password" class="form-label">Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control @error('directors_password') is-invalid @enderror" 
                                           id="directors_password" name="directors_password">
                                    @error('directors_password')
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
