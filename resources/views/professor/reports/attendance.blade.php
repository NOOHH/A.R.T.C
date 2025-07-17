@extends('professor.layout')

@section('title', 'Attendance Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Reports</h5>
                </div>
                <div class="card-body">
                    @if($batches->count() > 0)
                        <div class="row">
                            @foreach($batches as $batch)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ $batch->batch_name }}</h6>
                                            <small class="text-muted">{{ $batch->program->program_title ?? 'Unknown Program' }}</small>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Students:</strong> {{ $batch->students->count() }}</p>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Last Attendance</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($batch->students as $student)
                                                            <tr>
                                                                <td>{{ $student->student_firstname }} {{ $student->student_lastname }}</td>
                                                                <td>
                                                                    <small class="text-muted">No records yet</small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-secondary">Unknown</span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">No students enrolled</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <h5 class="mt-3">No Batches Assigned</h5>
                            <p class="text-muted">You don't have any batches assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
