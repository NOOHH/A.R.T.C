@extends('professor.layout')

@section('title', 'My Programs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-mortarboard"></i> My Programs</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Only use $assignedPrograms as provided by the controller. Do not fetch or display any other programs. --}}
            @if($assignedPrograms->count() > 0)
                <div class="row">
                    @foreach($assignedPrograms as $program)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">{{ $program->program_name }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>Students Enrolled:</strong> {{ $program->students->count() }}<br>
                                        <strong>Modules:</strong> {{ $program->modules->count() }}<br>
                                        @if($program->program_description)
                                            <small class="text-muted">{{ Str::limit($program->program_description, 100) }}</small>
                                        @endif
                                    </p>
                                    
                                    @if($program->pivot->video_link)
                                        <div class="alert alert-info small">
                                            <i class="bi bi-camera-video"></i> Video link uploaded
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group w-100">
                                        <a href="{{ route('professor.program.details', $program->program_id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#videoModal{{ $program->program_id }}">
                                            <i class="bi bi-camera-video"></i> Video
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Modal -->
                        <div class="modal fade" id="videoModal{{ $program->program_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Video Link - {{ $program->program_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('professor.program.update-video', $program->program_id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="video_link{{ $program->program_id }}" class="form-label">Video Link (Zoom, YouTube, etc.)</label>
                                                <input type="url" class="form-control" 
                                                       id="video_link{{ $program->program_id }}" 
                                                       name="video_link" 
                                                       value="{{ $program->pivot->video_link ?? '' }}" 
                                                       placeholder="https://zoom.us/j/..." required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="video_description{{ $program->program_id }}" class="form-label">Description (Optional)</label>
                                                <textarea class="form-control" 
                                                          id="video_description{{ $program->program_id }}" 
                                                          name="video_description" 
                                                          rows="3" 
                                                          placeholder="Brief description of the video content">{{ $program->pivot->video_description ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Video Link</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <h4><i class="bi bi-info-circle"></i> No Programs Assigned</h4>
                    <p>You haven't been assigned to any programs yet. Please contact your administrator.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
