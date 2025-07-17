@extends('professor.layout')

@section('title', 'My Batches')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">My Assigned Batches</h5>
                </div>
                <div class="card-body">
                    @if($batches->count() > 0)
                        <div class="row">
                            @foreach($batches as $batch)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $batch->batch_name }}</h6>
                                            <span class="badge bg-primary">{{ $batch->students->count() }} Students</span>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Program:</strong> {{ $batch->program->program_title ?? 'Unknown Program' }}</p>
                                            <p><strong>Start Date:</strong> {{ $batch->start_date ? \Carbon\Carbon::parse($batch->start_date)->format('M d, Y') : 'Not set' }}</p>
                                            <p><strong>End Date:</strong> {{ $batch->end_date ? \Carbon\Carbon::parse($batch->end_date)->format('M d, Y') : 'Not set' }}</p>
                                            
                                            @if($batch->students->count() > 0)
                                                <div class="mt-3">
                                                    <h6>Students:</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Email</th>
                                                                    <th>Status</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($batch->students as $student)
                                                                    <tr>
                                                                        <td>{{ $student->student_firstname }} {{ $student->student_lastname }}</td>
                                                                        <td>{{ $student->student_email }}</td>
                                                                        <td>
                                                                            <span class="badge bg-success">Active</span>
                                                                        </td>
                                                                        <td>
                                                                            <a href="{{ route('professor.grading.student-details', $student->student_id) }}?program_id={{ $batch->program_id }}" 
                                                                               class="btn btn-sm btn-outline-primary">
                                                                                <i class="bi bi-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-3">
                                                    <small class="text-muted">No students enrolled in this batch</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-collection fs-1 text-muted"></i>
                            <h5 class="mt-3">No Batches Assigned</h5>
                            <p class="text-muted">You don't have any batches assigned to you yet.</p>
                            <p class="text-muted">Contact your administrator to get assigned to batches.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
