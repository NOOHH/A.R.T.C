@extends('layouts.app')
@section('content')
<div class="container py-5">
  <h2 class="mb-4">Admin Dashboard Preview</h2>
  <p class="text-muted">Tenant preview of admin dashboard (summary only).</p>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card shadow-sm h-100"><div class="card-body text-center">
        <h5 class="card-title mb-1">Programs</h5>
        <div class="display-6">{{ $stats['programs'] }}</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm h-100"><div class="card-body text-center">
        <h5 class="card-title mb-1">Courses</h5>
        <div class="display-6">{{ $stats['courses'] }}</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm h-100"><div class="card-body text-center">
        <h5 class="card-title mb-1">Students</h5>
        <div class="display-6">{{ $stats['students'] }}</div>
      </div></div>
    </div>
  </div>
</div>
@endsection
