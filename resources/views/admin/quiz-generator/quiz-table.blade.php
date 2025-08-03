<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Quiz Title</th>
                <th>Program</th>
                <th>Module</th>
                <th>Course</th>
                <th>Questions</th>
                <th>Time Limit</th>
                <th>Attempts</th>
                <th>Deadline</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quizzes as $quiz)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $quiz->quiz_title }}</h6>
                                @if($quiz->quiz_description)
                                    <small class="text-muted">{{ Str::limit($quiz->quiz_description, 50) }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $quiz->program->program_name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @if($quiz->module)
                            <span class="badge bg-info">{{ $quiz->module->module_name }}</span>
                        @else
                            <span class="text-muted">All Modules</span>
                        @endif
                    </td>
                    <td>
                        @if($quiz->course)
                            <span class="badge bg-secondary">{{ $quiz->course->subject_name }}</span>
                        @else
                            <span class="text-muted">All Courses</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-dark">{{ $quiz->questions->count() }} questions</span>
                    </td>
                    <td>
                        @if($quiz->time_limit)
                            <i class="bi bi-clock"></i> {{ $quiz->time_limit }} mins
                        @else
                            <span class="text-muted">No limit</span>
                        @endif
                    </td>
                    <td>
                        @if($quiz->max_attempts && $quiz->max_attempts > 0)
                            <i class="bi bi-arrow-repeat"></i> {{ $quiz->max_attempts }}
                        @else
                            <span class="text-success"><i class="bi bi-infinity"></i> Unlimited</span>
                        @endif
                    </td>
                    <td>
                        @if($quiz->due_date)
                            @php
                                $dueDate = \Carbon\Carbon::parse($quiz->due_date);
                                $isOverdue = $dueDate->isPast();
                            @endphp
                            <div class="small {{ $isOverdue ? 'text-danger' : 'text-warning' }}">
                                <i class="bi bi-calendar-event"></i>
                                {{ $dueDate->format('M d, Y') }}<br>
                                <small>{{ $dueDate->format('g:i A') }}</small>
                                @if($isOverdue)
                                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Overdue</small>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">No deadline</span>
                        @endif
                    </td>
                    <td>
                        <div class="small text-muted">
                            {{ $quiz->created_at->format('M d, Y') }}<br>
                            <small>{{ $quiz->created_at->format('g:i A') }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            @if($status !== 'archived')
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="editQuiz({{ $quiz->quiz_id }})" title="Edit Quiz">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endif
                            
                            @if($status === 'draft')
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="changeQuizStatus({{ $quiz->quiz_id }}, 'published')" title="Publish Quiz">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuizStatus({{ $quiz->quiz_id }}, 'archived')" title="Archive Quiz">
                                    <i class="bi bi-archive"></i>
                                </button>
                            @elseif($status === 'published')
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="changeQuizStatus({{ $quiz->quiz_id }}, 'draft')" title="Move to Draft">
                                    <i class="bi bi-file-text"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeQuizStatus({{ $quiz->quiz_id }}, 'archived')" title="Archive Quiz">
                                    <i class="bi bi-archive"></i>
                                </button>
                            @elseif($status === 'archived')
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="changeQuizStatus({{ $quiz->quiz_id }}, 'draft')" title="Restore to Draft">
                                    <i class="bi bi-file-text"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="changeQuizStatus({{ $quiz->quiz_id }}, 'published')" title="Publish Quiz">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteQuiz({{ $quiz->quiz_id }})" title="Delete Quiz">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($quizzes->isEmpty())
        <div class="text-center py-4">
            <p class="text-muted">No {{ $status }} quizzes found</p>
        </div>
    @endif
</div>
