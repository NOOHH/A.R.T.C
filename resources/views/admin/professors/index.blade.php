@extends('admin.admin-dashboard-layout')

@section('title', 'Professor Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Professor Management</h2>
                    <p class="text-muted">Manage professors and their program assignments</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.professors.archived') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-archive"></i> Archived Professors
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
                        <i class="bi bi-plus-circle"></i> Add Professor
                    </button>
                </div>
            </div>

            <!-- Professors Table -->
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Active Professors</h5>
                </div>
                <div class="card-body p-0">
                    @if($professors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Professor</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Assigned Programs</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($professors as $professor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $professor->full_name }}</div>
                                                    <small class="text-muted">{{ $professor->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $professor->email }}</td>
                                        <td>N/A</td>
                                        <td>
                                            @if($professor->programs->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($professor->programs as $program)
                                                        <span class="badge bg-success">{{ $program->program_name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No assignments</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.professors.edit', $professor->professor_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal" data-bs-target="#videosModal"
                                                        data-professor-id="{{ $professor->professor_id }}"
                                                        data-professor-name="{{ $professor->full_name }}">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        data-bs-toggle="modal" data-bs-target="#archiveModal"
                                                        data-professor-id="{{ $professor->professor_id }}"
                                                        data-professor-name="{{ $professor->full_name }}">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-professor-id="{{ $professor->professor_id }}"
                                                        data-professor-name="{{ $professor->full_name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($professors->hasPages())
                            <div class="p-3">
                                {{ $professors->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-workspace display-1 text-muted"></i>
                            <h5 class="mt-3">No Professors Found</h5>
                            <p class="text-muted">Start by adding your first professor.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Professor Modal -->
<div class="modal fade" id="addProfessorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.professors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Professor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assign to Programs</label>
                        <div class="row">
                            @foreach($programs as $program)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="programs[]" value="{{ $program->program_id }}" 
                                               id="program_{{ $program->program_id }}">
                                        <label class="form-check-label" for="program_{{ $program->program_id }}">
                                            {{ $program->program_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Professor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Video Management Modal -->
<div class="modal fade" id="videosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Video Links - <span id="professorNameSpan"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="videoModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archive Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive <strong id="archiveProfessorName"></strong>?</p>
                <p class="text-muted">Archived professors can be restored later.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="archiveForm" action="" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">Archive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete <strong id="deleteProfessorName"></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="{{ route('admin.professors.index') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Archive modal
    $('#archiveModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        console.log('Archive modal - Professor ID:', professorId);
        document.getElementById('archiveProfessorName').textContent = professorName;
        const actionUrl = `/admin/professors/${professorId}/archive`;
        document.getElementById('archiveForm').action = actionUrl;
        console.log('Archive form action set to:', actionUrl);
    });

    // Delete modal
    $('#deleteModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        console.log('Delete modal - Professor ID:', professorId);
        document.getElementById('deleteProfessorName').textContent = professorName;
        const actionUrl = `/admin/professors/${professorId}`;
        document.getElementById('deleteForm').action = actionUrl;
        console.log('Delete form action set to:', actionUrl);
    });

    // Videos modal
    $('#videosModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('professorNameSpan').textContent = professorName;
        
        // Fetch professor's program video links
        fetch(`/admin/professors/${professorId}/videos`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('videoModalBody').innerHTML = data.html;
            })
            .catch(error => {
                document.getElementById('videoModalBody').innerHTML = '<p class="text-danger">Error loading video data.</p>';
            });
    });
});
</script>
@endpush
