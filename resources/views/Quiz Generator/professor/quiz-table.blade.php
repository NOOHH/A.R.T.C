<!-- Quiz Table Component -->
<div class="table-responsive">
    @if($quizzes->count() > 0)
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Quiz Title</th>
                    <th>Program</th>
                    <th>Questions</th>
                    <th>Settings</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                <tr>
                    <td>
                        <strong>{{ $quiz->quiz_title }}</strong>
                        @if($quiz->tags && is_array($quiz->tags))
                            <br>
                            @foreach($quiz->tags as $tag)
                                <span class="badge bg-info text-dark me-1">{{ $tag }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $quiz->program->program_name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-primary">
                            @if(property_exists($quiz, 'questions') && $quiz->questions)
                                {{ $quiz->questions->count() ?? 0 }} Questions
                            @elseif(isset($quiz->total_questions))
                                {{ $quiz->total_questions }} Questions
                            @else
                                0 Questions
                            @endif
                        </span>
                        @if($quiz->time_limit)
                            <br><small class="text-muted"><i class="bi bi-clock"></i> {{ $quiz->time_limit }}min</small>
                        @endif
                    </td>
                    <td>
                        @if($quiz->allow_retakes)
                            <span class="badge bg-info text-dark">Retakes</span>
                        @endif
                        @if($quiz->instant_feedback)
                            <span class="badge bg-warning text-dark">Instant Feedback</span>
                        @endif
                        @if($quiz->randomize_order)
                            <span class="badge bg-secondary">Random Order</span>
                        @endif
                        @if(isset($quiz->randomize_mc_options) && $quiz->randomize_mc_options)
                            <span class="badge bg-secondary">Random Options</span>
                        @endif
                        @if($quiz->max_attempts > 1)
                            <span class="badge bg-info">Max: {{ $quiz->max_attempts }}</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ $quiz->created_at->format('M j, Y') }}</small>
                    </td>
                    <td>
                        <div class="btn-group-vertical btn-group-sm" role="group">
                            <!-- Preview Quiz (available for all statuses) -->
                            <button class="btn btn-outline-primary btn-sm preview-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">
                                <i class="bi bi-eye"></i> Preview
                            </button>
                            <!-- Edit Quiz (available for all statuses) -->
                            <button class="btn btn-outline-secondary btn-sm edit-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">
                                <i class="bi bi-pencil"></i> Edit Quiz
                            </button>
                            <!-- Status-specific actions -->
                            @if($status === 'draft')
                                <button class="btn btn-success btn-sm" onclick="publishQuiz('{{ $quiz->quiz_id }}')">
                                    <i class="bi bi-check-circle"></i> Publish
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="archiveQuiz('{{ $quiz->quiz_id }}')">
                                    <i class="bi bi-archive"></i> Archive
                                </button>
                            @elseif($status === 'published')
                                <button class="btn btn-warning btn-sm" onclick="archiveQuiz('{{ $quiz->quiz_id }}')">
                                    <i class="bi bi-archive"></i> Archive
                                </button>
                            @elseif($status === 'archived')
                                <button class="btn btn-info btn-sm" onclick="restoreQuiz('{{ $quiz->quiz_id }}')">
                                    <i class="bi bi-arrow-clockwise"></i> Unarchive
                                </button>
                            @endif
                            <!-- Delete (available for all statuses) -->
                            <button class="btn btn-danger btn-sm delete-quiz-btn" data-quiz-id="{{ $quiz->quiz_id }}">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-4">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="text-muted mt-2">
                @if($status === 'draft')
                    No draft quizzes yet. Create your first quiz above!
                @elseif($status === 'published')
                    No published quizzes yet. Publish a draft quiz to see it here.
                @else
                    No archived quizzes yet.
                @endif
            </p>
        </div>
    @endif
</div>
