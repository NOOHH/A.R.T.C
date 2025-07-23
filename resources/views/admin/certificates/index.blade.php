@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="admin-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 mb-1">
                                <i class="bi bi-award me-2"></i>
                                Certificates Management
                            </h1>
                            <p class="mb-0 opacity-75">Generate and manage student certificates</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Completed Enrollments</h5>
                </div>
                <div class="card-body">
                    @if($completedEnrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Program</th>
                                        <th>Completion Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedEnrollments as $enrollment)
                                        <tr>
                                            <td>
                                                @if($enrollment->student && $enrollment->student->user)
                                                    {{ $enrollment->student->user->user_firstname }} {{ $enrollment->student->user->user_lastname }}
                                                @else
                                                    Unknown Student
                                                @endif
                                            </td>
                                            <td>{{ $enrollment->program->program_name ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->completed_at ? $enrollment->completed_at->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('certificate.show', ['user_id' => $enrollment->student->user_id ?? 0, 'enrollment_id' => $enrollment->enrollment_id]) }}" 
                                                   class="btn btn-primary btn-sm" target="_blank">
                                                    <i class="bi bi-eye me-1"></i>View Certificate
                                                </a>
                                                <a href="{{ route('certificate.download', ['user_id' => $enrollment->student->user_id ?? 0, 'enrollment_id' => $enrollment->enrollment_id]) }}" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $completedEnrollments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-award" style="font-size: 4rem; color: #dee2e6;"></i>
                            <h4 class="mt-3 text-muted">No Certificates Available</h4>
                            <p class="text-muted">There are no completed enrollments to generate certificates for.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-header {
    background: linear-gradient(135deg, #0d6efd, #4c84ff);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 1rem 1rem;
}
</style>
@endsection
