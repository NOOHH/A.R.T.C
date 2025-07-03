@extends('admin.admin-dashboard-layout')

@section('title', 'Directors Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-badge"></i> Directors Management</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.directors.archived') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> View Archived
                    </a>
                    <a href="{{ route('admin.directors.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Director
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-body">
                    @if($directors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Assigned Programs</th>
                                        <th>Hire Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($directors as $director)
                                        <tr>
                                            <td>
                                                <strong>{{ $director->full_name }}</strong>
                                            </td>
                                            <td>{{ $director->email }}</td>
                                            <td>{{ $director->phone ?? 'N/A' }}</td>
                                            <td>{{ $director->department ?? 'N/A' }}</td>
                                            <td>
                                                @if($director->programs->count() > 0)
                                                    <span class="badge bg-info">{{ $director->programs->count() }} program(s)</span>
                                                @else
                                                    <span class="text-muted">No programs assigned</span>
                                                @endif
                                            </td>
                                            <td>{{ $director->hire_date ? $director->hire_date->format('M d, Y') : 'N/A' }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.directors.show', $director) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.directors.edit', $director) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.directors.archive', $director) }}" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to archive this director?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Archive">
                                                            <i class="bi bi-archive"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-badge fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Directors Found</h4>
                            <p class="text-muted">Start by adding your first director to the system.</p>
                            <a href="{{ route('admin.directors.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Director
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endpush
