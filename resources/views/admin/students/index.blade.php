@extends('admin.admin-dashboard-layout')

@section('title', 'List of Students')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-people"></i> List of Students</h2>
                <a href="{{ route('admin.students.archived') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-archive"></i> View Archived
                </a>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.students.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
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
                            
                            <div class="col-md-3">
                                <label for="status" class="form-label">Filter by Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
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
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Students Table -->
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
                                        <th>Registration Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td><strong>{{ $student->student_id }}</strong></td>
                                            <td>{{ $student->firstname }} {{ $student->lastname }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>
                                                @if($student->program)
                                                    <span class="badge bg-info">{{ $student->program->program_name }}</span>
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
                                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.students.show', $student) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($student->date_approved)
                                                        <span class="btn btn-sm btn-outline-success disabled" title="Approved">
                                                            <i class="bi bi-check-circle"></i>
                                                        </span>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.students.approve', $student) }}" 
                                                              class="d-inline" onsubmit="return confirm('Approve this student?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#archiveStudentModal"
                                                            data-student-id="{{ $student->student_id }}"
                                                            data-student-name="{{ $student->firstname }} {{ $student->lastname }}"
                                                            title="Archive">
                                                        <i class="bi bi-archive"></i>
                                                    </button>
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
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Students Found</h4>
                            <p class="text-muted">
                                @if(request()->hasAny(['program_id', 'status', 'search']))
                                    No students match your current filters. Try adjusting your search criteria.
                                @else
                                    There are no students registered yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Student Modal -->
<div class="modal fade" id="archiveStudentModal" tabindex="-1" aria-labelledby="archiveStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveStudentModalLabel">
                    <i class="bi bi-archive text-warning"></i> Archive Student
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-3"></i>
                    <h5>Are you sure you want to archive this student?</h5>
                    <p class="text-muted mb-3">Student: <strong id="studentNameToArchive"></strong></p>
                    <p class="text-muted">This action will move the student to the archived list. You can restore them later if needed.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveStudentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-archive"></i> Archive Student
                    </button>
                </form>
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
// Handle archive student modal
document.getElementById('archiveStudentModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const studentId = button.getAttribute('data-student-id');
    const studentName = button.getAttribute('data-student-name');
    
    // Update modal content
    document.getElementById('studentNameToArchive').textContent = studentName;
    
    // Update form action
    const form = document.getElementById('archiveStudentForm');
    form.action = `/admin/students/${studentId}/archive`;
});
</script>
@endpush
