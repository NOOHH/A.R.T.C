@extends('admin.admin-dashboard-layout')

@section('title', 'Programs')

@push('styles')
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

  /* Header */
  .programs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 10px;
  }
  .programs-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  /* Programs grid */
  .programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }

  /* Program card */
  .program-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
    position: relative;
    overflow: hidden;
  }
  .program-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
  .program-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .program-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .program-title::before {
    content: '🎓';
    font-size: 1.2rem;
  }

  .program-stats {
    background: rgba(102, 126, 234, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .enrollment-count {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    color: #667eea;
    font-weight: 600;
  }
  .enrollment-count::before {
    content: '👥';
    font-size: 1.1rem;
  }

  .program-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
  }

  .view-enrollees-btn, .delete-program-btn {
    padding: 10px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .view-enrollees-btn {
    background: #17a2b8;
    color: white;
  }
  .view-enrollees-btn:hover {
    background: #138496;
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

  /* Add button */
  .add-program-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 100;
  }
  .add-program-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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
    max-width: 500px;
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

  .modal input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
  }

  .modal input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
  }

  .cancel-btn, .add-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
  }

  .cancel-btn {
    background: #6c757d;
    color: white;
  }
  .cancel-btn:hover {
    background: #5a6268;
  }

  .add-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  .add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  /* Empty state */
  .no-programs {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-size: 1.1rem;
  }
  .no-programs::before {
    content: '🎓';
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

  /* Enrollments modal specific styles */
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
</style>
@endpush

@section('content')
<!-- Display messages -->
@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="error-message">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="error-message">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="programs-container">
    <div class="programs-header">
        <h1>Programs</h1>
    </div>

    <div class="programs-grid">
        @forelse($programs as $program)
            <div class="program-card">
                <div class="program-title">{{ $program->program_name }}</div>
                
                <div class="program-stats">
                    <div class="enrollment-count">
                        Enrolled Students: {{ $program->enrollments->count() }}
                    </div>
                </div>

                <div class="program-actions">
                    <button type="button" class="view-enrollees-btn" data-program-id="{{ $program->program_id }}">
                        👥 View Enrollees
                    </button>
                    <form action="{{ route('admin.programs.delete', $program->program_id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-program-btn" onclick="return confirm('Are you sure you want to delete this program?')">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="no-programs">
                No programs found.<br>
                <small>Click "Add Program" to create your first program.</small>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Program Button -->
<button class="add-program-btn" id="showAddModal">
    <span style="font-size:1.3em;">&#43;</span> Add Program
</button>

<!-- Add Program Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Create New Program</h3>
        <form action="{{ route('admin.programs.store') }}" method="POST">
            @csrf
            <input type="text" name="program_name" placeholder="Program Name" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Program</button>
            </div>
        </form>
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
    // Add Program Modal
    const showAddModal = document.getElementById('showAddModal');
    const addModalBg = document.getElementById('addModalBg');
    const cancelAddModal = document.getElementById('cancelAddModal');

    showAddModal.addEventListener('click', function() {
        addModalBg.classList.add('active');
    });

    cancelAddModal.addEventListener('click', function() {
        addModalBg.classList.remove('active');
    });

    addModalBg.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

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
                    console.log('Enrollment data received:', data); // Debug log
                    loadingMessage.style.display = 'none';
                    enrollmentsList.style.display = 'block';
                    
                    if (data.enrollments && data.enrollments.length > 0) {
                        // Update modal title with program name and count
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
        // Reset modal content
        enrollmentsModal.querySelector('h3').textContent = 'Enrolled Students';
        enrollmentsList.innerHTML = '';
        loadingMessage.textContent = 'Loading enrollments...';
    });

    enrollmentsModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            // Reset modal content
            enrollmentsModal.querySelector('h3').textContent = 'Enrolled Students';
            enrollmentsList.innerHTML = '';
            loadingMessage.textContent = 'Loading enrollments...';
        }
    });
});
</script>
@endpush
