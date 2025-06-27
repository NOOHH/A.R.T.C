@extends('admin.admin-dashboard-layout')
@section('title', 'Student Registration')
@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .students-table thead th {
        background: #ede6f7;
        color: #5c2f91;
        font-weight: 700;
        border-bottom: 2px solid #5c2f91;
    }
    .students-table tbody td {
        vertical-align: middle;
        font-size: 1.08rem;
    }
    .students-table tr {
        border-bottom: 1px solid #e0e0e0;
    }
    .students-table .form-check-input:checked {
        background-color: #5c2f91;
        border-color: #5c2f91;
    }
    .enroll-header {
        font-size: 0.95rem;
        font-weight: 600;
        color: #888;
        background: transparent;
        border: none;
        margin-right: 8px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4" style="color:#222;">PENDING</h2>
    <div class="table-responsive">
        <table class="table students-table align-middle bg-white rounded-3 shadow-sm" style="min-width:900px;">
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>Middle Name</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Program</th>
                    <th class="text-end"> </th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $registration)
                    <tr>
                        <td class="fw-bold">{{ strtoupper($registration->lastname) }}</td>
                        <td>{{ $registration->middlename }}</td>
                        <td>{{ $registration->firstname }}</td>
                        <td>{{ $registration->email }}</td>
                        <td class="text-uppercase">{{ $registration->category ?? '' }}</td>
                        <td>{{ $registration->program ?? '' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.student.registration.view', ['id' => $registration->id ?? $registration->registration_id]) }}" class="btn btn-primary btn-sm" style="background:#5c2f91;border:none;border-radius:16px;min-width:120px;">View Submission</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
