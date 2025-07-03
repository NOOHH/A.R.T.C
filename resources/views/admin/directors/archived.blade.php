@extends('admin.admin-dashboard-layout')

@section('title', 'Archived Directors')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive"></i> Archived Directors</h2>
                <a href="{{ route('admin.directors.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Active Directors
                </a>
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
                                        <th>Archived Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($directors as $director)
                                        <tr class="table-light">
                                            <td>
                                                <strong>{{ $director->full_name }}</strong>
                                                <span class="badge bg-secondary ms-2">Archived</span>
                                            </td>
                                            <td>{{ $director->directors_email }}</td>
                                            <td>N/A</td>
                                            <td>N/A</td>
                                            <td>
                                                @if($director->programs->count() > 0)
                                                    <span class="badge bg-warning">{{ $director->programs->count() }} program(s)</span>
                                                @else
                                                    <span class="text-muted">No programs assigned</span>
                                                @endif
                                            </td>
                                            <td>{{ $director->updated_at->format('M d, Y') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <form method="POST" action="{{ route('admin.directors.restore', $director) }}" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to restore this director?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.directors.destroy', $director) }}" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this director? This action cannot be undone!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Permanently">
                                                            <i class="bi bi-trash"></i>
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
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Archived Directors</h4>
                            <p class="text-muted">There are no archived directors at the moment.</p>
                            <a href="{{ route('admin.directors.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to Active Directors
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
