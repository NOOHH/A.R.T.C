@extends('admin.admin-dashboard-layout')
@section('title', 'View Student Registration')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color:#222;">Student Registration Details</h2>
    <div class="card p-4 shadow-sm">
        <div class="row mb-2">
            <div class="col-md-4"><strong>Last Name:</strong></div>
            <div class="col-md-8">{{ $registration->lastname }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>First Name:</strong></div>
            <div class="col-md-8">{{ $registration->firstname }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Middle Name:</strong></div>
            <div class="col-md-8">{{ $registration->middlename }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Email:</strong></div>
            <div class="col-md-8">{{ $registration->email }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Category:</strong></div>
            <div class="col-md-8">{{ $registration->category ?? '' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Program:</strong></div>
            <div class="col-md-8">{{ $registration->program ?? '' }}</div>
        </div>
        <!-- Add more fields as needed -->
        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
