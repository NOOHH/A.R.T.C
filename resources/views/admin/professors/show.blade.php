@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Professor Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-badge"></i> Professor Details</h2>
        <a href="{{ route('admin.professors.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Professors
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">{{ $professor->name ?? 'Professor Information' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Professor ID:</strong> {{ $professor->professor_id ?? 'N/A' }}</p>
                            <p><strong>Name:</strong> {{ $professor->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $professor->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Department:</strong> {{ $professor->department ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> 
                                @if($professor->professor_archived ?? false)
                                    <span class="badge bg-secondary">Archived</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </p>
                            <p><strong>Created:</strong> {{ $professor->created_at ? $professor->created_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="mb-0">Assigned Programs</h6>
                </div>
                <div class="card-body">
                    @if($professor->programs ?? false)
                        @foreach($professor->programs as $program)
                            <span class="badge bg-primary me-2 mb-2">{{ $program->program_name ?? 'N/A' }}</span>
                        @endforeach
                    @else
                        <p class="text-muted">No programs assigned</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
