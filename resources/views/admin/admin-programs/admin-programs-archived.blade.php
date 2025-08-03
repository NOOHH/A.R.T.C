@extends('admin.admin-dashboard-layout')

@section('title', 'Archived Programs')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-programs.css') }}">
<style>
  /* Professional Design Overhaul for Archived Programs */
  body {
    background: #f8fafc !important;
    overflow-x: hidden;
  }
  
  .main-content-wrapper {
    align-items: flex-start !important;
    overflow-x: hidden;
    width: 100%;
    max-width: 100vw;
    display: flex;
    justify-content: center;
    padding: 20px;
  }

  /* Modern Professional Container */
  .programs-container {
    background: #ffffff;
    padding: 32px;
    margin: 20px auto;
    max-width: 1200px;
    width: calc(100% - 40px);
    box-sizing: border-box;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    overflow: visible;
  }

  /* Professional Header */
  .programs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 2px solid #f1f5f9;
  }

  .programs-header h1 {
    font-size: 2.2rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .programs-header h1::before {
    content: '📁';
    font-size: 2rem;
    opacity: 0.8;
  }

  .back-to-programs-btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.15);
  }

  .back-to-programs-btn:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.25);
    text-decoration: none;
    color: white;
  }

  /* Stats Summary Cards */
  .stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    text-align: center;
    transition: all 0.2s ease;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 8px;
  }

  .stat-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Modern Grid Layout */
  .programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
  }

  /* Professional Program Cards */
  .program-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }

  .program-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: #64748b;
  }

  .program-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #c7d2fe;
  }

  .program-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .program-title::after {
    content: '📋';
    opacity: 0.6;
  }

  .program-description {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .program-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .stat-item {
    background: #f1f5f9;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .enrollment-count {
    background: #fef3c7;
    color: #92400e;
  }

  .modules-count {
    background: #dbeafe;
    color: #1e40af;
  }

  /* Professional Action Buttons */
  .program-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: auto;
  }

  .action-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 36px;
  }

  .view-btn {
    background: #0ea5e9;
    color: white;
  }

  .view-btn:hover {
    background: #0284c7;
    color: white;
    text-decoration: none;
  }

  .restore-btn {
    background: #10b981;
    color: white;
  }

  .restore-btn:hover {
    background: #059669;
    color: white;
    text-decoration: none;
  }

  .delete-btn {
    background: #ef4444;
    color: white;
  }

  .delete-btn:hover {
    background: #dc2626;
    color: white;
    text-decoration: none;
  }

  /* Empty State Design */
  .no-programs {
    text-align: center;
    padding: 80px 20px;
    color: #64748b;
  }

  .empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
  }

  .empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
  }

  .empty-message {
    font-size: 1rem;
    line-height: 1.5;
  }

  /* Alert Messages */
  .alert-modern {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    border: 1px solid;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .alert-success {
    background: #f0fdf4;
    color: #166534;
    border-color: #bbf7d0;
  }

  .alert-error {
    background: #fef2f2;
    color: #991b1b;
    border-color: #fecaca;
  }

  /* Mobile Responsiveness */
  @media (max-width: 768px) {
    .programs-container {
      padding: 20px;
      margin: 10px;
      width: calc(100% - 20px);
    }

    .programs-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
      text-align: left;
    }

    .programs-header h1 {
      font-size: 1.8rem;
    }

    .programs-grid {
      grid-template-columns: 1fr;
      gap: 16px;
    }

    .stats-summary {
      grid-template-columns: 1fr;
      gap: 12px;
    }

    .program-stats {
      flex-direction: column;
      gap: 8px;
    }

    .program-actions {
      flex-direction: column;
    }

    .action-btn {
      justify-content: center;
      width: 100%;
    }
  }

  @media (max-width: 480px) {
    .programs-container {
      padding: 16px;
      border-radius: 8px;
    }

    .programs-header h1 {
      font-size: 1.5rem;
    }

    .program-card {
      padding: 16px;
    }
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
    <!-- Professional Header -->
    <div class="programs-header">
        <div>
            <h1>Archived Programs</h1>
            <p class="header-subtitle">Manage and review archived programs</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.programs.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Programs</span>
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    @if($archivedPrograms->count() > 0)
    <div class="stats-summary">
        <div class="stat-item">
            <div class="stat-value">{{ $archivedPrograms->count() }}</div>
            <div class="stat-label">Archived Programs</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $archivedPrograms->sum('enrollments_count') ?? 0 }}</div>
            <div class="stat-label">Total Past Enrollments</div>
        </div>
    </div>

    <!-- Batch Actions -->
    <div class="batch-actions">
        <div class="batch-selection">
            <input type="checkbox" id="selectAllPrograms" onchange="toggleSelectAll()">
            <label for="selectAllPrograms">Select All Programs</label>
        </div>
        <button type="button" class="action-btn action-btn-danger" id="batchDeleteBtn" onclick="batchDeletePrograms()">
            <i class="fas fa-trash"></i>
            <span>Delete Selected</span>
        </button>
    </div>
    @endif

    <div class="programs-grid">
        @forelse($archivedPrograms as $program)
            <div class="program-card">
                <div class="card-header">
                    <div class="card-selection">
                        <input type="checkbox" 
                               class="program-checkbox" 
                               data-program-id="{{ $program->program_id }}"
                               onchange="toggleProgramSelection({{ $program->program_id }}, this)">
                    </div>
                    <div class="card-status">ARCHIVED</div>
                </div>
                
                <div class="card-content">
                    <div class="program-title">{{ $program->program_name }}</div>
                    
                    <div class="program-stats">
                        <div class="stat-row">
                            <i class="fas fa-user-graduate"></i>
                            <span>{{ $program->enrollments_count ?? 0 }} past enrollments</span>
                        </div>
                    </div>
                </div>

                <div class="program-actions">
                    <form action="{{ route('admin.programs.toggle-archive', $program->program_id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="action-btn action-btn-success" onclick="return confirm('Are you sure you want to unarchive this program?')">
                            <i class="fas fa-folder-open"></i>
                            <span>Unarchive</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.programs.delete', $program->program_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn action-btn-danger" onclick="return confirm('Are you sure you want to permanently delete this program? This action cannot be undone.')">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="empty-title">No archived programs found</div>
                <div class="empty-description">
                    <a href="{{ route('admin.programs.index') }}" class="empty-link">
                        <i class="fas fa-arrow-left"></i>
                        Go back to active programs
                    </a>
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
                        // Simply reload the page without showing success message
                        location.reload();
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
