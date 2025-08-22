@extends('layouts.app')
@section('content')
<div class="container py-5">
  <h2 class="mb-4">Professor Dashboard Preview</h2>
  <p class="text-muted">Tenant preview of professor dashboard (sample data).</p>
  <ul class="list-group">
    @forelse($courses as $c)
      <li class="list-group-item d-flex justify-content-between align-items-center">
        {{ $c->course_name ?? 'Course' }}
        <span class="badge bg-secondary">Preview</span>
      </li>
    @empty
      <li class="list-group-item">No courses yet.</li>
    @endforelse
  </ul>
</div>
@endsection
