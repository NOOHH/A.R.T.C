@extends('professor.layout')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-robot"></i> AI Quiz Generator</h2>
    
    <!-- Error/Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Debug Info (will be populated by JavaScript on errors) -->
    <div id="debug-alert" class="alert alert-info" style="display: none;">
        <h6>Debug Information:</h6>
        <div id="debug-content"></div>
    </div>
    
    <form id="quiz-generator-form" enctype="multipart/form-data" method="POST" action="{{ route('professor.quiz-generator.generate') }}">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="program_id" class="form-label">Program</label>
                <select id="program_id" name="program_id" class="form-select" required>
                    <option value="">Select Program</option>
                    @foreach($assignedPrograms as $program)
                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="module_id" class="form-label">Module</label>
                <select id="module_id" name="module_id" class="form-select" required disabled>
                    <option value="">Select Module</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="course_id" class="form-label">Course</label>
                <select id="course_id" name="course_id" class="form-select" required disabled>
                    <option value="">Select Course</option>
                </select>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="document" class="form-label">Upload Document</label>
                <input type="file" class="form-control" id="document" name="document" accept=".pdf,.doc,.docx,.csv,.txt" required>
                <small class="text-muted">Supported: PDF, Word, CSV, TXT. Max: 10MB.</small>
            </div>
            <div class="col-md-2">
                <label for="num_questions" class="form-label"># Questions</label>
                <input type="number" class="form-control" id="num_questions" name="num_questions" min="5" max="50" value="10" required>
            </div>
            <div class="col-md-3">
                <label for="quiz_type" class="form-label">Question Type</label>
                <select id="quiz_type" name="quiz_type" class="form-select" required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="flashcard">Flashcard</option>
                    <option value="mixed">Mixed (MCQ & T/F)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="randomize_order" name="randomize_order">
                    <label class="form-check-label" for="randomize_order">Randomize Order</label>
                </div>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="quiz_title" class="form-label">Quiz Title</label>
                <input type="text" class="form-control" id="quiz_title" name="quiz_title" required>
            </div>
            <div class="col-md-6">
                <label for="tags" class="form-label">Tags <small class="text-muted">(comma separated)</small></label>
                <input type="text" class="form-control" id="tags" name="tags" placeholder="e.g. algebra, calculus, midterm">
            </div>
        </div>
        <div class="mb-3">
            <label for="instructions" class="form-label">Instructions (Optional)</label>
            <textarea class="form-control" id="instructions" name="instructions" rows="2" placeholder="Any special instructions for students..."></textarea>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-magic"></i> Generate Quiz</button>
        </div>
    </form>

    <hr class="my-4">
    <h4 class="mb-3">Your Draft & Published Quizzes</h4>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Tags</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                <tr>
                    <td>{{ $quiz->quiz_title }}</td>
                    <td>{{ $quiz->program->program_name ?? '-' }}</td>
                    <td>
                        @if($quiz->is_draft)
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-success">Published</span>
                        @endif
                    </td>
                    <td>
                        @if($quiz->tags && is_array($quiz->tags))
                            @foreach($quiz->tags as $tag)
                                <span class="badge bg-info text-dark">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline-secondary btn-sm view-questions-btn" data-quiz-id="{{ $quiz->quiz_id }}">View/Edit Questions</button>
                        <button class="btn btn-outline-primary btn-sm preview-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">Preview</button>
                        @if($quiz->jotform_url)
                            <a href="{{ $quiz->jotform_url }}" target="_blank" class="btn btn-outline-success btn-sm">Open JotForm</a>
                        @endif
                        @if($quiz->is_draft)
                            <form action="{{ route('professor.quiz-generator.publish', $quiz->quiz_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Publish</button>
                            </form>
                        @endif
                        <form action="{{ route('professor.quiz-generator.delete', $quiz->quiz_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this quiz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No quizzes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal for Viewing/Editing Questions -->
    <div class="modal fade" id="questionsModal" tabindex="-1" aria-labelledby="questionsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="questionsModalLabel">Quiz Questions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="questions-modal-body">
            <!-- Questions will be loaded here via AJAX -->
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Previewing Quiz as Student -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="previewModalLabel">Quiz Preview (Student View)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="preview-modal-body">
            <!-- Preview will be loaded here via AJAX -->
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// --- Dynamic Dropdowns ---
$('#program_id').on('change', function() {
    let programId = $(this).val();
    $('#module_id').prop('disabled', true).html('<option value="">Select Module</option>');
    $('#course_id').prop('disabled', true).html('<option value="">Select Course</option>');
    
    if (programId) {
        $.get('/professor/quiz-generator/modules/' + programId, function(res) {
            if (res.success) {
                let options = '<option value="">Select Module</option>';
                res.modules.forEach(m => options += `<option value="${m.module_id}">${m.module_name}</option>`);
                $('#module_id').html(options).prop('disabled', false);
            }
        });
    }
});

$('#module_id').on('change', function() {
    let moduleId = $(this).val();
    $('#course_id').prop('disabled', true).html('<option value="">Select Course</option>');
    
    if (moduleId) {
        $.get('/professor/quiz-generator/courses/' + moduleId, function(res) {
            if (res.success) {
                let options = '<option value="">Select Course</option>';
                res.courses.forEach(c => options += `<option value="${c.course_id}">${c.course_name}</option>`);
                $('#course_id').html(options).prop('disabled', false);
            }
        });
    }
});

// --- View/Edit Questions Modal ---
$('.view-questions-btn').on('click', function() {
    let quizId = $(this).data('quiz-id');
    $('#questions-modal-body').html('<div class="text-center py-4"><div class="spinner-border"></div></div>');
    $('#questionsModal').modal('show');
    $.get('/professor/quiz-generator/questions/' + quizId, function(res) {
        $('#questions-modal-body').html(res.html);
    });
});

// --- Preview Quiz Modal ---
$('.preview-quiz-btn').on('click', function() {
    let quizId = $(this).data('quiz-id');
    $('#preview-modal-body').html('<div class="text-center py-4"><div class="spinner-border"></div></div>');
    $('#previewModal').modal('show');
    $.get('/professor/quiz-generator/preview/' + quizId, function(res) {
        $('#preview-modal-body').html(res.html);
    });
});

// --- Enhanced Form Submission with Error Handling ---
$('#quiz-generator-form').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    console.log('Form submission started');
    
    // Validate required fields
    const requiredFields = {
        'program_id': $('#program_id').val(),
        'module_id': $('#module_id').val(), 
        'course_id': $('#course_id').val(),
        'quiz_title': $('#quiz_title').val(),
        'document': $('#document')[0].files.length > 0 ? $('#document')[0].files[0] : null
    };
    
    console.log('Required fields check:', requiredFields);
    
    // Check for missing required fields
    let missingFields = [];
    if (!requiredFields.program_id) missingFields.push('Program');
    if (!requiredFields.module_id) missingFields.push('Module');
    if (!requiredFields.course_id) missingFields.push('Course');
    if (!requiredFields.quiz_title) missingFields.push('Quiz Title');
    if (!requiredFields.document) missingFields.push('Document');
    
    if (missingFields.length > 0) {
        alert('Please fill in all required fields: ' + missingFields.join(', '));
        return false;
    }
    
    // Process tags
    let tags = $('#tags').val();
    if (tags) {
        let tagArr = tags.split(',').map(t => t.trim()).filter(t => t.length > 0);
        console.log('Tags processed:', tagArr);
        
        // Remove existing tag inputs
        $(this).find('input[name="tags[]"]').remove();
        
        // Add new tag inputs
        tagArr.forEach(tag => {
            $('<input>').attr({
                type: 'hidden', 
                name: 'tags[]'
            }).val(tag).appendTo(this);
        });
    }
    
    // Add loading state to submit button
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');
    submitBtn.prop('disabled', true);
    
    // Create FormData for file upload
    const formData = new FormData(this);
    
    // Fix the randomize_order checkbox value - convert "on" to boolean
    if (formData.has('randomize_order')) {
        formData.set('randomize_order', '1'); // Laravel expects '1' for true
    } else {
        formData.set('randomize_order', '0'); // Laravel expects '0' for false
    }
    
    console.log('Form data being submitted:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value instanceof File ? `File: ${value.name}` : value);
    }
    
    // Submit via AJAX for better error handling
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Success response:', response);
            if (response.success) {
                let message = 'Quiz generated successfully!';
                if (response.data.jotform && response.data.jotform.form_url) {
                    message += `\n\nJotForm created: ${response.data.jotform.form_url}`;
                }
                alert(message);
                location.reload(); // Refresh to show new quiz
            } else {
                alert('Error: ' + (response.message || 'Unknown error occurred'));
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error Details:');
            console.log('Status:', status);
            console.log('Error:', error);
            console.log('Response Text:', xhr.responseText);
            console.log('Status Code:', xhr.status);
            
            let errorMessage = 'An error occurred while generating the quiz.';
            let debugInfo = '';
            
            if (xhr.status === 422) {
                // Validation errors
                try {
                    const errors = JSON.parse(xhr.responseText);
                    if (errors.errors) {
                        errorMessage = 'Validation errors:\n';
                        Object.keys(errors.errors).forEach(field => {
                            errorMessage += `- ${field}: ${errors.errors[field].join(', ')}\n`;
                        });
                    } else if (errors.message) {
                        errorMessage = errors.message;
                    }
                } catch (e) {
                    errorMessage = 'Validation error: ' + xhr.responseText;
                }
            } else if (xhr.status === 500) {
                try {
                    const serverError = JSON.parse(xhr.responseText);
                    if (serverError.message) {
                        errorMessage = 'Server error: ' + serverError.message;
                        if (serverError.debug_info) {
                            debugInfo = `Error at line ${serverError.debug_info.error_line} in ${serverError.debug_info.error_file}`;
                        }
                    }
                } catch (e) {
                    errorMessage = 'Server error. Please check the server logs.';
                }
            } else if (xhr.status === 419) {
                errorMessage = 'CSRF token mismatch. Please refresh the page and try again.';
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your internet connection.';
            }
            
            alert(errorMessage);
            
            // Show debug info if available
            if (debugInfo || xhr.responseText) {
                $('#debug-content').html(`
                    <strong>Status Code:</strong> ${xhr.status}<br>
                    <strong>Status:</strong> ${status}<br>
                    <strong>Error:</strong> ${error}<br>
                    ${debugInfo ? `<strong>Debug:</strong> ${debugInfo}<br>` : ''}
                    <strong>Response:</strong> <pre style="white-space: pre-wrap; font-size: 12px;">${xhr.responseText}</pre>
                `);
                $('#debug-alert').show();
            }
        },
        complete: function() {
            // Restore button state
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
            $('#tags').prop('disabled', false);
        }
    });
    
    return false;
});
</script>
@endpush
