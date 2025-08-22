@extends('layouts.app')
@section('content')
<div class="container py-5">
  <h2 class="mb-4">Student Dashboard Preview</h2>
  <p class="text-muted">This is a tenant preview of the student dashboard (read-only sample data).</p>
  <div class="row">
    @forelse($programs as $p)
      <div class="col-md-3 mb-3">
        <div class="card h-100"><div class="card-body">
          <h6 class="card-title">{{ $p->program_name ?? 'Program' }}</h6>
          <p class="small text-muted mb-0">Preview item</p>
        </div></div>
      </div>
    @empty
      <p>No programs yet.</p>
    @endforelse
  </div>
</div>
@endsection
