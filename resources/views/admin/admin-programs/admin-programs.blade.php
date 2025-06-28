@extends('admin.admin-dashboard-layout')

@section('title', 'Programs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admin-programs.css') }}">
@endpush

@section('content')
<div class="programs-container">
    <div class="programs-header">PROGRAMS</div>

    <div class="program-list">
        @forelse($programs as $program)
            <div class="program-item">
                <span>{{ $program->program_name }}</span>
                <span class="status-bar">Enrolled: {{ $program->enrollments->count() }}</span>
                <button type="button" class="show-enrollments-btn" data-program-id="{{ $program->program_id }}">View Enrollees</button>
                <form action="{{ route('admin.programs.delete', $program->program_id) }}" method="POST" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </div>
        @empty
            <div class="no-programs">No programs found.</div>
        @endforelse
    </div>

    <button class="add-program-btn" id="showAddModal">
        <span style="font-size:1.3em;">&#43;</span> Add Program
    </button>
</div>

<!-- Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Create Program</h3>
        <form action="{{ route('admin.programs.store') }}" method="POST">
            @csrf
            <input type="text" name="program_name" placeholder="Program Name" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Program</button>
            </div>
        </form>
    </div>
</div>

<!-- Enrollments Modal -->
<div class="modal-bg" id="enrollmentsModal">
    <div class="modal">
        <h3>Enrolled Students</h3>
        <ul id="enrollmentsList"></ul>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" id="closeEnrollmentsModal">Close</button>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="error-message">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="error-message">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    document.getElementById('showAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.add('active');
    };
    document.getElementById('cancelAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.remove('active');
    };
    document.getElementById('addModalBg').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-programs.js') }}"></script>
@endpush
