{{-- Assignment Submission Form Partial --}}
<form id="assignmentSubmissionForm" enctype="multipart/form-data" class="mt-3">
    @csrf
    <input type="hidden" name="content_id" value="{{ $content->id }}">
    <input type="hidden" name="course_id" value="{{ $content->course_id }}">
    <input type="hidden" name="module_id" value="{{ $content->course->module_id ?? '' }}">
    @if(isset($draft))
        <input type="hidden" name="submission_id" value="{{ $draft->id }}">
    @endif
    
    <div class="mb-3">
        <label for="submissionFiles" class="form-label">
            Upload Files
            @if(isset($draft))
                <small class="text-muted">(Leave empty to keep existing files)</small>
            @endif
        </label>
        <input type="file" class="form-control" id="submissionFiles" name="files[]" multiple 
               @if(!isset($draft)) required @endif>
        <small class="form-text text-muted">
            @if($content->allowed_file_types)
                Allowed types: {{ $content->allowed_file_types }}
            @else
                Accepted formats: PDF, DOC, DOCX, ZIP, Images, Videos
            @endif
            @if($content->max_file_size)
                (Max: {{ $content->max_file_size }}MB each)
            @else
                (Max: 10MB each)
            @endif
        </small>
        
        @if(isset($draft) && !empty($draft->files))
            <div class="mt-2">
                <small class="text-info">
                    <strong>Current files in draft:</strong>
                    @php
                        $files = is_string($draft->files) ? json_decode($draft->files, true) : $draft->files;
                        $files = is_array($files) ? $files : [];
                    @endphp
                    @foreach($files as $file)
                        @php
                            $filePath = is_array($file) ? ($file['path'] ?? $file) : $file;
                            $fileName = is_array($file) ? ($file['original_filename'] ?? basename($filePath)) : basename($filePath);
                        @endphp
                        <span class="badge bg-secondary me-1">{{ $fileName }}</span>
                    @endforeach
                </small>
            </div>
        @endif
    </div>
    
    <div class="mb-3">
        <label for="submissionNotes" class="form-label">Notes (Optional)</label>
        <textarea class="form-control" id="submissionNotes" name="comments" rows="3" 
                  placeholder="Add any additional notes about your submission...">{{ isset($draft) ? $draft->comments : '' }}</textarea>
    </div>
    
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">
            <i class="bi bi-save"></i> Save Draft
        </button>
        @if(isset($draft))
            <button type="button" class="btn btn-primary" onclick="submitAssignment()">
                <i class="bi bi-upload"></i> Submit Assignment
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="removeDraft()">
                <i class="bi bi-trash"></i> Remove Draft
            </button>
        @else
            <button type="button" class="btn btn-primary" onclick="submitAssignment()">
                <i class="bi bi-upload"></i> Submit Assignment
            </button>
        @endif
    </div>
</form>

<div id="submissionStatus" class="mt-3"></div>
