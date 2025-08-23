@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Archived Students')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive"></i> Archived Students</h2>
                @if(isset($isPreview) && $isPreview && isset($previewTenant))
                    <a href="/t/draft/{{ $previewTenant }}/admin/students?website={{ request('website') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Active Students
                    </a>
                @else
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Active Students
                    </a>
                @endif
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

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    @if(isset($isPreview) && $isPreview && isset($previewTenant))
                        <form method="GET" action="/t/draft/{{ $previewTenant }}/admin/students/archived">
                            <input type="hidden" name="website" value="{{ request('website') }}">
                    @else
                        <form method="GET" action="{{ route('admin.students.archived') }}">
                    @endif
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="program_id" class="form-label">Filter by Program</label>
                                <select name="program_id" id="program_id" class="form-select">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->program_id }}" 
                                                {{ request('program_id') == $program->program_id ? 'selected' : '' }}>
                                            {{ $program->program_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Search by name, ID, or email..." value="{{ request('search') }}">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    @if(isset($isPreview) && $isPreview && isset($previewTenant))
                                        <a href="/t/draft/{{ $previewTenant }}/admin/students/archived?website={{ request('website') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.students.archived') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Status</th>
                                        <th>Archived Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td>{{ $student->student_id }}</td>
                                            <td>{{ $student->student_first_name }} {{ $student->student_last_name }}</td>
                                            <td>{{ $student->student_email }}</td>
                                            <td>
                                                @if($student->programs && $student->programs->count() > 0)
                                                    {{ $student->programs->first()->program_name }}
                                                    @if($student->programs->count() > 1)
                                                        <small class="text-muted">(+{{ $student->programs->count() - 1 }} more)</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No Program</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($student->date_approved)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $student->updated_at->format('M d, Y') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    @if(isset($isPreview) && $isPreview)
                                                        <button type="button" onclick="alert('Preview mode - View not available')"
                                                                class="btn btn-sm btn-outline-info" title="View (Preview)">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button type="button" onclick="alert('Preview mode - Restore not available')"
                                                                class="btn btn-sm btn-outline-success" title="Restore (Preview)">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                        <button type="button" onclick="alert('Preview mode - Delete not available')"
                                                                class="btn btn-sm btn-outline-danger" title="Delete (Preview)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('admin.students.show', $student) }}" 
                                                           class="btn btn-sm btn-outline-info" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.students.restore', $student) }}" 
                                                              style="display: inline;" onsubmit="return confirm('Restore this student?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Student">
                                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                title="Delete Permanently" onclick="deleteStudent({{ $student->student_id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Archived Students</h4>
                            <p class="text-muted">
                                @if(request()->hasAny(['program_id', 'search']))
                                    No archived students match your current filters.
                                @else
                                    There are no archived students at the moment.
                                @endif
                            </p>
                            @if(isset($isPreview) && $isPreview && isset($previewTenant))
                                <a href="/t/draft/{{ $previewTenant }}/admin/students?website={{ request('website') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Active Students
                                </a>
                            @else
                                <a href="{{ route('admin.students.index') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Active Students
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this student? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteStudentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteStudent(studentId) {
    const form = document.getElementById('deleteStudentForm');
    form.action = `/admin/students/${studentId}/force-delete`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
