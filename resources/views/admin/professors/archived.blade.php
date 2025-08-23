@extends('admin.admin-dashboard.admin-dashboard-layout')

@section('title', 'Archived Professors')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Archived Professors</h2>
                    <p class="text-muted">Manage archived professors</p>
                </div>
                <div>
                    @if(isset($isPreview) && $isPreview && isset($previewTenant))
                        <a href="/t/draft/{{ $previewTenant }}/admin/professors?website={{ request('website') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Active Professors
                        </a>
                    @else
                        <a href="{{ route('admin.professors.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Active Professors
                        </a>
                    @endif
                </div>
            </div>

            <!-- Archived Professors Table -->
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Archived Professors</h5>
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
                                        <th>Archived Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($professors as $professor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
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
                                                        <span class="badge bg-secondary">{{ $program->program_name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No assignments</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($isPreview) && $isPreview && is_string($professor->updated_at))
                                                {{ $professor->updated_at }}
                                            @else
                                                {{ $professor->updated_at->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                                                        data-professor-id="{{ $professor->id }}"
                                                        data-professor-name="{{ $professor->full_name }}">
                                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-professor-id="{{ $professor->id }}"
                                                        data-professor-name="{{ $professor->full_name }}">
                                                    <i class="bi bi-trash"></i> Delete
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
                            <i class="bi bi-archive display-1 text-muted"></i>
                            <h5 class="mt-3">No Archived Professors</h5>
                            <p class="text-muted">No professors have been archived yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore <strong id="restoreProfessorName"></strong>?</p>
                <p class="text-muted">This professor will be moved back to the active professors list.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="restoreForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Restore</button>
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
                <form id="deleteForm" method="POST" style="display: inline;">
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
    // Restore modal
    $('#restoreModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('restoreProfessorName').textContent = professorName;
        document.getElementById('restoreForm').action = `/admin/professors/${professorId}/restore`;
    });

    // Delete modal
    $('#deleteModal').on('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const professorId = button.getAttribute('data-professor-id');
        const professorName = button.getAttribute('data-professor-name');
        
        document.getElementById('deleteProfessorName').textContent = professorName;
        document.getElementById('deleteForm').action = `/admin/professors/${professorId}`;
    });
});
</script>
@endpush
