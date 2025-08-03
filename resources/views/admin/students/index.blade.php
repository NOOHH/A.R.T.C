@extends('admin.admin-dashboard-layout')

@push('styles')
  {{-- Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@section('title', 'List of Students')

@section('content')
<div class="container-fluid py-4">
  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> List of Students</h2>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success" onclick="exportToCSV()">
        <i class="bi bi-download"></i> Export to CSV
      </button>
      <a href="{{ route('admin.students.archived') }}" class="btn btn-outline-secondary">
        <i class="bi bi-archive"></i> View Archived
      </a>
    </div>
  </div>

  {{-- Filters --}}
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
              <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
              <option value="pending"  {{ request('status')=='pending'?'selected':''  }}>Pending</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="search" class="form-label">Search</label>
            <input type="text"
                   name="search"
                   id="search"
                   class="form-control"
                   placeholder="Search by name, ID, or email…"
                   value="{{ request('search') }}">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <div class="w-100 d-flex gap-2">
              <button type="submit" class="btn btn-primary w-50">
                <i class="bi bi-search"></i>
              </button>
              <a href="{{ route('admin.students.index') }}"
                 class="btn btn-outline-secondary w-50">
                <i class="bi bi-x"></i>
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Table --}}
  <div class="card shadow">
    <div class="card-body">
      @if($students->count())
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Batch</th>
                <th>Learning Mode</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Registered</th>
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
                    @if($student->enrollment && $student->enrollment->batch)
                      <span class="badge bg-secondary">{{ $student->enrollment->batch->batch_name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    @if($student->enrollment && $student->enrollment->learning_mode)
                      @php
                        $mode = strtolower($student->enrollment->learning_mode);
                        $badgeColor = 'secondary';
                        $displayText = 'Unknown';
                        
                        if (in_array($mode, ['synchronous', 'synch', 'sync'])) {
                          $badgeColor = 'primary';
                          $displayText = 'Synchronous';
                        } elseif (in_array($mode, ['asynchronous', 'async'])) {
                          $badgeColor = 'success';  
                          $displayText = 'Asynchronous';
                        }
                      @endphp
                      <span class="badge bg-{{ $badgeColor }}">{{ $displayText }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    @if($student->enrollment && $student->enrollment->start_date)
                      {{ \Carbon\Carbon::parse($student->enrollment->start_date)->format('M d, Y') }}
                    @elseif($student->enrollment && $student->enrollment->batch && $student->enrollment->batch->start_date)
                      {{ \Carbon\Carbon::parse($student->enrollment->batch->start_date)->format('M d, Y') }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    @if($student->enrollment && $student->enrollment->end_date)
                      {{ \Carbon\Carbon::parse($student->enrollment->end_date)->format('M d, Y') }}
                    @elseif($student->enrollment && $student->enrollment->batch && $student->enrollment->batch->end_date)
                      {{ \Carbon\Carbon::parse($student->enrollment->batch->end_date)->format('M d, Y') }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    @if($student->date_approved)
                      <span class="badge bg-success">Approved</span>
                    @else
                      <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                  </td>
                  <td>{{ $student->created_at->format('M d, Y') }}</td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="{{ route('admin.students.show', $student) }}"
                         class="btn btn-sm btn-outline-info" title="View">
                        <i class="bi bi-eye"></i>
                      </a>
                      @unless($student->date_approved)
                        <form method="POST"
                              action="{{ route('admin.students.approve', $student) }}"
                              class="d-inline"
                              onsubmit="return confirm('Approve this student?')">
                          @csrf @method('PATCH')
                          <button class="btn btn-sm btn-outline-success" title="Approve">
                            <i class="bi bi-check-circle"></i>
                          </button>
                        </form>
                      @endunless
                      <button class="btn btn-sm btn-outline-secondary"
                              data-bs-toggle="modal"
                              data-bs-target="#archiveStudentModal"
                              data-student-id="{{ $student->student_id }}"
                              data-student-name="{{ $student->firstname.' '.$student->lastname }}"
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

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
          {{ $students->links('pagination::bootstrap-5') }}
        </div>
      @else
        <div class="text-center py-5">
          <i class="bi bi-people fs-1 text-muted"></i>
          <h4 class="text-muted mt-3">No Students Found</h4>
          <p class="text-muted">
            @if(request()->hasAny(['program_id','status','search']))
              No results match your filters.
            @else
              No students registered yet.
            @endif
          </p>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Archive Modal --}}
<div class="modal fade" id="archiveStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-archive text-warning"></i> Archive Student
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-3"></i>
        <h5>Archive <strong id="studentNameToArchive"></strong>?</h5>
        <p class="text-muted">You can restore later if needed.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="archiveStudentForm" method="POST" style="display:inline;">
          @csrf @method('PATCH')
          <button class="btn btn-warning">
            <i class="bi bi-archive"></i> Archive
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Archive‐modal setup
    document
      .getElementById('archiveStudentModal')
      .addEventListener('show.bs.modal', function(e) {
        const btn  = e.relatedTarget;
        const id   = btn.dataset.studentId;
        const name = btn.dataset.studentName;
        document.getElementById('studentNameToArchive').textContent = name;
        document.getElementById('archiveStudentForm').action =
          `/admin/students/${id}/archive`;
      });

    // Auto-submit the filter form when Program dropdown changes
    document
      .getElementById('program_id')
      .addEventListener('change', function() {
        this.form.submit();
      });

    // Export to CSV function
    function exportToCSV() {
      // Get current filter values
      const programId = document.getElementById('program_id').value;
      const status = document.getElementById('status').value;
      const search = document.getElementById('search').value;
      
      // Build the export URL with current filters
      let exportUrl = '{{ route("admin.students.export") }}?';
      const params = new URLSearchParams();
      
      if (programId) params.append('program_id', programId);
      if (status) params.append('status', status);
      if (search) params.append('search', search);
      
      exportUrl += params.toString();
      
      // Create a temporary link and trigger download
      const link = document.createElement('a');
      link.href = exportUrl;
      link.download = 'students_export.csv';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>
@endpush

