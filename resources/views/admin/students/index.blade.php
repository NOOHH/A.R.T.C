@extends('admin.admin-dashboard-layout')

@push('styles')
  {{-- Bootstrap CSS for pagination, forms, table, modal, etc. --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#batchEnrollmentModal">
        <i class="bi bi-plus-circle"></i> Batch Enroll Students
      </button>
      <a href="{{ route('admin.students.export') }}" class="btn btn-success">
        <i class="bi bi-download"></i> Export to CSV
      </a>
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
                      <span class="badge bg-{{ $student->enrollment->learning_mode === 'synchronous' ? 'primary' : 'success' }}">
                        {{ ucfirst($student->enrollment->learning_mode) }}
                      </span>
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

    // Batch enrollment functionality
    let selectedStudents = [];
    let studentsData = [];

    document.addEventListener('DOMContentLoaded', function() {
      // Load students when modal opens
      document.getElementById('batchEnrollmentModal').addEventListener('shown.bs.modal', function() {
        loadStudentsForBatchEnrollment();
      });

      // Student search
      document.getElementById('student_search').addEventListener('input', function() {
        filterStudents(this.value);
      });

      // Enroll students button
      document.getElementById('enrollStudentsBtn').addEventListener('click', function() {
        batchEnrollStudents();
      });
    });

    function loadStudentsForBatchEnrollment() {
      fetch('/admin/students/batch-enrollment/students')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            studentsData = data.students;
            renderStudentsList(studentsData);
          }
        })
        .catch(error => {
          console.error('Error loading students:', error);
          document.getElementById('students_list').innerHTML = 
            '<div class="text-center text-danger p-3">Error loading students</div>';
        });
    }

    function renderStudentsList(students) {
      const container = document.getElementById('students_list');
      
      if (students.length === 0) {
        container.innerHTML = '<div class="text-center text-muted p-3">No students found</div>';
        return;
      }

      const html = students.map(student => `
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" value="${student.student_id}" 
                 id="student_${student.student_id}" onchange="toggleStudent('${student.student_id}')">
          <label class="form-check-label" for="student_${student.student_id}">
            <strong>${student.name}</strong> (${student.student_id})
            <br><small class="text-muted">${student.email}</small>
            ${student.enrolled_programs.length > 0 ? 
              `<br><small class="text-info">Currently enrolled in: ${student.enrolled_programs.join(', ')}</small>` : ''}
          </label>
        </div>
      `).join('');

      container.innerHTML = html;
    }

    function filterStudents(searchTerm) {
      if (!searchTerm) {
        renderStudentsList(studentsData);
        return;
      }

      const filtered = studentsData.filter(student => 
        student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        student.student_id.toLowerCase().includes(searchTerm.toLowerCase()) ||
        student.email.toLowerCase().includes(searchTerm.toLowerCase())
      );

      renderStudentsList(filtered);
    }

    function toggleStudent(studentId) {
      const checkbox = document.getElementById(`student_${studentId}`);
      
      if (checkbox.checked) {
        if (!selectedStudents.includes(studentId)) {
          selectedStudents.push(studentId);
        }
      } else {
        selectedStudents = selectedStudents.filter(id => id !== studentId);
      }

      document.getElementById('selected_count').textContent = selectedStudents.length;
    }

    function batchEnrollStudents() {
      if (selectedStudents.length === 0) {
        alert('Please select at least one student to enroll.');
        return;
      }

      const formData = new FormData(document.getElementById('batchEnrollmentForm'));
      selectedStudents.forEach(studentId => {
        formData.append('student_ids[]', studentId);
      });

      const enrollBtn = document.getElementById('enrollStudentsBtn');
      enrollBtn.disabled = true;
      enrollBtn.textContent = 'Enrolling...';

      fetch('/admin/students/batch-enrollment/enroll', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(`Batch enrollment completed!\n\nSuccessful: ${data.summary.successful}\nFailed: ${data.summary.failed}\nDuplicates: ${data.summary.duplicates}`);
          document.getElementById('batchEnrollmentModal').querySelector('.btn-close').click();
          location.reload(); // Refresh to show updated data
        } else {
          alert('Batch enrollment failed: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during batch enrollment.');
      })
      .finally(() => {
        enrollBtn.disabled = false;
        enrollBtn.textContent = 'Enroll Selected Students';
      });
    }
  </script>
@endpush

<!-- Batch Enrollment Modal -->
<div class="modal fade" id="batchEnrollmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Batch Enroll Students</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="batchEnrollmentForm">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Program</label>
              <select name="program_id" id="batch_program_id" class="form-select" required>
                <option value="">Select Program</option>
                @foreach($programs as $program)
                  <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Package</label>
              <select name="package_id" id="batch_package_id" class="form-select" required>
                <option value="">Select Package</option>
              </select>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Enrollment Type</label>
              <select name="enrollment_type" class="form-select" required>
                <option value="full">Full</option>
                <option value="modular">Modular</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Learning Mode</label>
              <select name="learning_mode" class="form-select" required>
                <option value="online">Online</option>
                <option value="face-to-face">Face-to-Face</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Batch (Optional)</label>
              <select name="batch_id" id="batch_batch_id" class="form-select">
                <option value="">No Batch</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Search Students</label>
            <input type="text" id="student_search" class="form-control" placeholder="Search by name, ID, or email...">
          </div>

          <div class="mb-3">
            <label class="form-label">Select Students</label>
            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
              <div class="d-flex justify-content-between mb-2">
                <small class="text-muted">Select students to enroll:</small>
                <small class="text-muted">Selected: <span id="selected_count">0</span></small>
              </div>
              <div id="students_list">
                <div class="text-center text-muted p-3">
                  Loading students...
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="enrollStudentsBtn" class="btn btn-primary">Enroll Selected Students</button>
      </div>
    </div>
  </div>
</div>

