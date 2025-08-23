@extends('professor.professor-layouts.professor-layout')

@section('title', 'Grades Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Grades Reports</h5>
                </div>
                <div class="card-body">
                    @if($programs->count() > 0)
                        <div class="row">
                            @foreach($programs as $program)
                                <div class="col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ $program->program_title }}</h6>
                                            <small class="text-muted">{{ $program->program_description ?? 'No description' }}</small>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Enrolled Students:</strong> {{ $program->enrollments->count() }}</p>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Email</th>
                                                            <th>Overall Grade</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($program->enrollments as $enrollment)
                                                            <tr>
                                                                <td>{{ $enrollment->student->student_firstname ?? 'N/A' }} {{ $enrollment->student->student_lastname ?? '' }}</td>
                                                                <td>{{ $enrollment->student->student_email ?? 'N/A' }}</td>
                                                                <td>
                                                                    <span class="badge bg-secondary">No grades yet</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-info">{{ ucfirst($enrollment->status ?? 'enrolled') }}</span>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        // Check if we're in tenant preview mode
                                                                        $tenantSlug = request()->route('tenant') ?? session('preview_tenant');
                                                                        $routePrefix = $tenantSlug ? 'tenant.draft.' : '';
                                                                        $routeParams = $tenantSlug ? ['tenant' => $tenantSlug, 'student' => $enrollment->student->student_id ?? '#'] : ['student' => $enrollment->student->student_id ?? '#'];
                                                                        $gradingRoute = $tenantSlug ? $routePrefix . 'professor.grading' : 'professor.grading.student-details';
                                                                    @endphp
                                                                    <a href="{{ route($gradingRoute, $routeParams) }}?program_id={{ $program->program_id }}" 
                                                                       class="btn btn-sm btn-outline-primary">
                                                                        View Details
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">No students enrolled</td>
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
                            <i class="bi bi-award fs-1 text-muted"></i>
                            <h5 class="mt-3">No Programs Assigned</h5>
                            <p class="text-muted">You don't have any programs assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
