@extends('professor.layout')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-robot"></i> AI Quiz Generator</h2>
    <form id="quiz-generator-form" enctype="multipart/form-data" method="POST" action="{{ route('professor.quiz-generator.generate') }}">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="program_id" class="form-label">Program</label>
                <select id="program_id" name="program_id" class="form-select" required>
                    <option value="">Select Program</option>
                    @foreach($assignedPrograms as $program)
                        <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="module_id" class="form-label">Module</label>
                <select id="module_id" name="module_id" class="form-select" required disabled>
                    <option value="">Select Module</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="course_id" class="form-label">Course</label>
                <select id="course_id" name="course_id" class="form-select" required disabled>
                    <option value="">Select Course</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="content_id" class="form-label">Course Content</label>
                <select id="content_id" name="content_id" class="form-select" required disabled>
                    <option value="">Select Content</option>
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
    $('#content_id').prop('disabled', true).html('<option value="">Select Content</option>');
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
    $('#content_id').prop('disabled', true).html('<option value="">Select Content</option>');
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
$('#course_id').on('change', function() {
    let courseId = $(this).val();
    $('#content_id').prop('disabled', true).html('<option value="">Select Content</option>');
    if (courseId) {
        $.get('/professor/quiz-generator/contents/' + courseId, function(res) {
            if (res.success) {
                let options = '<option value="">Select Content</option>';
                res.contents.forEach(c => options += `<option value="${c.content_id}">${c.content_title}</option>`);
                $('#content_id').html(options).prop('disabled', false);
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

// --- Simple Tag Input: Convert comma-separated to array on submit ---
$('#quiz-generator-form').on('submit', function() {
    let tags = $('#tags').val();
    if (tags) {
        let tagArr = tags.split(',').map(t => t.trim()).filter(t => t.length > 0);
        $('<input>').attr({type: 'hidden', name: 'tags[]'}).val(tagArr).appendTo(this);
        $('#tags').prop('disabled', true); // prevent double submit
    }
});
</script>
@endpush
