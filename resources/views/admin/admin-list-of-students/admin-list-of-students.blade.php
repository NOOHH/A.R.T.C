@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'List of Students')
<style>
    </style>
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-people"></i> List of Students</h2>
            </div>

            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> 
                This is the students list page. For full functionality, please visit the 
                <a href="{{ route('admin.students.index') }}" class="alert-link">Admin Students Management</a> page.
            </div>

            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Students Management</h4>
                    <p class="text-muted">Manage all students from the main admin panel.</p>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right"></i> Go to Students Management
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@endpush
