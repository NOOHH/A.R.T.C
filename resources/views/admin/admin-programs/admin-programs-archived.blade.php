@extends('admin.admin-dashboard-layout')

@section('title', 'Archived Programs')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-programs.css') }}">
<style>
  /* Main wrapper */
  .main-content-wrapper {
    align-items: flex-start !important;
  }

  /* Container */
  .programs-container {
    background: #fff;
    padding: 40px 20px 60px;
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  /* Header with Bootstrap approach */
  .programs-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #6c757d;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .back-to-programs-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .back-to-programs-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
  }

  /* Programs grid */
  .programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }

  /* Program card - archived styling */
  .program-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    position: relative;
    opacity: 0.7;
    border-left: 5px solid #6c757d;
  }

  .program-card:hover {
    opacity: 1;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  .program-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.3;
  }

  .program-stats {
    margin-bottom: 20px;
  }

  .enrollment-count {
    color: #6c757d;
    font-size: 0.95rem;
    background: rgba(108, 117, 125, 0.1);
    padding: 8px 12px;
    border-radius: 20px;
    display: inline-block;
  }

  .program-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .view-enrollees-btn, .unarchive-btn, .delete-program-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .view-enrollees-btn {
    background: #17a2b8;
    color: white;
  }
  .view-enrollees-btn:hover {
    background: #138496;
    transform: scale(1.05);
  }

  .unarchive-btn {
    background: #28a745;
    color: white;
  }
  .unarchive-btn:hover {
    background: #218838;
    transform: scale(1.05);
  }

  .delete-program-btn {
    background: #dc3545;
    color: white;
  }
  .delete-program-btn:hover {
    background: #c82333;
    transform: scale(1.05);
  }

  /* Empty state */
  .no-programs {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }
  .no-programs::before {
    content: '📁';
    display: block;
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }

  /* Messages */
  .success-message, .error-message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
  }

  .success-message {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
  }

  .error-message {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
  }

  .error-message ul {
    margin: 0;
    padding-left: 20px;
  }

  /* Modal styles */
  .modal-bg {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  .modal-bg.active {
    display: flex;
  }

  .modal {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 600px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .modal h3 {
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-size: 1.5rem;
    text-align: center;
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
  }

  .cancel-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
    background: #6c757d;
    color: white;
  }
  .cancel-btn:hover {
    background: #5a6268;
  }

  #enrollmentsModal .modal {
    max-width: 600px;
  }

  #enrollmentsList {
    max-height: 400px;
    overflow-y: auto;
    padding: 0;
    margin: 15px 0;
    list-style: none;
  }

  #enrollmentsList li {
    padding: 12px 15px;
    background: #f8f9fa;
    margin-bottom: 8px;
    border-radius: 8px;
    border-left: 3px solid #667eea;
  }

  .loading {
    text-align: center;
    color: #6c757d;
    font-style: italic;
  }

  .archived-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #6c757d;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  /* Batch actions and selection styles */
  .batch-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #6c757d;
  }

  .select-all-container {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
  }

  .batch-delete-btn {
    display: none;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .batch-delete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
  }

  .program-checkbox {
    margin-right: 10px;
    transform: scale(1.2);
  }
</style>
@endpush

@section('content')
<!-- Display messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="programs-container">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h1 class="display-4 fw-bold text-uppercase text-muted mb-0" style="letter-spacing: 2px;">Archived Programs</h1>
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('admin.programs.index') }}" class="btn btn-lg text-white fw-semibold px-4 py-2 rounded-pill shadow back-to-programs-btn">
                <i class="fas fa-arrow-left me-2"></i>Back to Programs
            </a>
        </div>
    </div>

    <!-- Batch Actions -->
    @if($archivedPrograms->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllPrograms" onchange="toggleSelectAll()">
            <label class="form-check-label fw-semibold" for="selectAllPrograms">
                Select All Programs
            </label>
        </div>
        <button type="button" class="btn btn-danger fw-semibold" id="batchDeleteBtn" onclick="batchDeletePrograms()">
            <i class="fas fa-trash me-2"></i>Delete Selected
        </button>
    </div>
    @endif

    <div class="programs-grid">
        @forelse($archivedPrograms as $program)
            <div class="program-card">
                <div class="archived-badge">ARCHIVED</div>
                <div class="d-flex align-items-start gap-3">
                    <input type="checkbox" 
                           class="form-check-input program-checkbox mt-1" 
                           data-program-id="{{ $program->program_id }}"
                           onchange="toggleProgramSelection({{ $program->program_id }}, this)">
                    <div class="flex-grow-1">
                        <div class="program-title">{{ $program->program_name }}</div>
                        
                        <div class="program-stats">
                            <div class="enrollment-count">
                                <i class="fas fa-user-graduate me-2"></i>Enrolled Students: {{ $program->enrollments_count ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap mt-3">
                    <button type="button" class="btn btn-info btn-sm fw-semibold view-enrollees-btn" data-program-id="{{ $program->program_id }}">
                        <i class="fas fa-users me-1"></i>View Enrollees
                    </button>
                    <button type="button" class="btn btn-success btn-sm fw-semibold unarchive-btn" data-program-id="{{ $program->program_id }}">
                        <i class="fas fa-folder-open me-1"></i>Unarchive
                    </button>
                    <form action="{{ route('admin.programs.delete', $program->program_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm fw-semibold" onclick="return confirm('Are you sure you want to permanently delete this program? This action cannot be undone.')">
                            <i class="fas fa-trash me-1"></i>Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light border border-2 border-dashed rounded-4 p-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <div class="text-muted h5 mb-2">No archived programs found.</div>
                    <small class="text-muted">
                        <a href="{{ route('admin.programs.index') }}" class="text-primary text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Go back to active programs
                        </a>
                    </small>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Enrollments Modal -->
<div class="modal-bg" id="enrollmentsModal">
    <div class="modal">
        <h3>Enrolled Students</h3>
        <div class="loading" id="loadingMessage">Loading enrollments...</div>
        <ul id="enrollmentsList" style="display: none;"></ul>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeEnrollmentsModal">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF token setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Enrollments Modal
    const enrollmentsModal = document.getElementById('enrollmentsModal');
    const closeEnrollmentsModal = document.getElementById('closeEnrollmentsModal');
    const enrollmentsList = document.getElementById('enrollmentsList');
    const loadingMessage = document.getElementById('loadingMessage');

    // View Enrollees functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-enrollees-btn')) {
            const programId = e.target.dataset.programId;
            
            // Show modal and loading
            enrollmentsModal.classList.add('active');
            loadingMessage.style.display = 'block';
            enrollmentsList.style.display = 'none';
            enrollmentsList.innerHTML = '';

            // Fetch enrollments
            fetch(`/admin/programs/${programId}/enrollments`)
                .then(response => response.json())
                .then(data => {
                    console.log('Enrollment data received:', data);
                    loadingMessage.style.display = 'none';
                    enrollmentsList.style.display = 'block';
                    
                    if (data.enrollments && data.enrollments.length > 0) {
                        enrollmentsModal.querySelector('h3').textContent = 
                            `${data.program_name} - ${data.total_enrollments} Enrolled Students`;
                        
                        data.enrollments.forEach(enrollment => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <div style="font-weight: bold; font-size: 1.1em; color: #2c3e50; margin-bottom: 5px;">
                                    👤 ${enrollment.student_name || 'Unknown Student'}
                                </div>
                                <div style="font-size: 0.9em; color: #667eea; margin-bottom: 3px;">
                                    📧 ${enrollment.email || 'No email available'}
                                </div>
                                <div style="font-size: 0.85em; color: #6c757d;">
                                    📅 Enrolled: ${enrollment.enrolled_at || 'Unknown date'}
                                </div>
                            `;
                            enrollmentsList.appendChild(li);
                        });
                    } else {
                        enrollmentsModal.querySelector('h3').textContent = 'No Students Enrolled';
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <div style="text-align: center; font-style: italic; color: #6c757d; padding: 20px;">
                                No students enrolled in this program yet.
                            </div>
                        `;
                        enrollmentsList.appendChild(li);
                    }
                })
                .catch(error => {
                    console.error('Error fetching enrollments:', error);
                    loadingMessage.textContent = 'Error loading enrollments.';
                });
        }
    });

    closeEnrollmentsModal.addEventListener('click', function() {
        enrollmentsModal.classList.remove('active');
        enrollmentsModal.querySelector('h3').textContent = 'Enrolled Students';
        enrollmentsList.innerHTML = '';
        loadingMessage.textContent = 'Loading enrollments...';
    });

    enrollmentsModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            enrollmentsModal.querySelector('h3').textContent = 'Enrolled Students';
            enrollmentsList.innerHTML = '';
            loadingMessage.textContent = 'Loading enrollments...';
        }
    });

    // Unarchive functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('unarchive-btn')) {
            const programId = e.target.dataset.programId;
            
            if (confirm('Are you sure you want to unarchive this program?')) {
                fetch(`/admin/programs/${programId}/toggle-archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Refresh the page to update the UI
                    } else {
                        alert('Error: ' + (data.message || 'Something went wrong'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while unarchiving the program.');
                });
            }
        }
    });
});

// Batch delete functionality
let selectedPrograms = new Set();

function toggleProgramSelection(programId, checkbox) {
    if (checkbox.checked) {
        selectedPrograms.add(programId);
    } else {
        selectedPrograms.delete(programId);
    }
    updateBatchDeleteButton();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllPrograms');
    const programCheckboxes = document.querySelectorAll('.program-checkbox');
    
    programCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        const programId = parseInt(checkbox.dataset.programId);
        if (selectAllCheckbox.checked) {
            selectedPrograms.add(programId);
        } else {
            selectedPrograms.delete(programId);
        }
    });
    
    updateBatchDeleteButton();
}

function updateBatchDeleteButton() {
    const batchDeleteBtn = document.getElementById('batchDeleteBtn');
    const selectedCount = selectedPrograms.size;
    
    if (selectedCount > 0) {
        batchDeleteBtn.style.display = 'inline-block';
        batchDeleteBtn.textContent = `Delete Selected (${selectedCount})`;
    } else {
        batchDeleteBtn.style.display = 'none';
    }
}

function batchDeletePrograms() {
    if (selectedPrograms.size === 0) return;
    
    if (confirm(`Are you sure you want to permanently delete ${selectedPrograms.size} selected program(s)? This action cannot be undone.`)) {
        fetch('/admin/programs/batch-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ program_ids: Array.from(selectedPrograms) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting programs.');
        });
    }
}
</script>
@endpush
