@extends('admin.admin-dashboard-layout')

@section('title', 'Student Registration')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url"    content="{{ url('') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-student-registration.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h2 class="fw-bold mb-4" style="color:#222;">
        {{ (isset($history) && $history) ? 'HISTORY' : 'PENDING' }}
    </h2>
    <div class="table-responsive">
        <table class="table students-table align-middle bg-white rounded-3 shadow-sm" style="min-width:900px;">
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>Middle Name</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Plan</th>
                    <th>Program</th>
                    @if(isset($history) && $history)
                        <th>Status</th>
                    @endif
                    <th class="text-end"> </th>
                </tr>
            </thead>
            <tbody>
                @if($registrations->isEmpty())
                    <tr>
                        <td colspan="@if(isset($history) && $history) 8 @else 7 @endif"
                            class="text-center text-muted py-4">
                            {{ isset($history) && $history ? 'No history records found.' : 'No pending registrations found.' }}
                        </td>
                    </tr>
                @else
                    @foreach($registrations as $registration)
                        <tr>
                            <td class="fw-bold">
                                {{ strtoupper($registration->lastname ?? ($registration->student->lastname ?? '')) }}
                            </td>
                            <td>{{ $registration->middlename ?? ($registration->student->middlename ?? '') }}</td>
                            <td>{{ $registration->firstname ?? ($registration->student->firstname ?? '') }}</td>
                            <td>
                                @if(isset($registration->user) && $registration->user)
                                    {{ $registration->user->email ?? 'N/A' }}
                                @elseif(isset($registration->email))
                                    {{ $registration->email }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-uppercase">
                                @if(isset($history) && $history)
                                    {{-- For history (students), get from enrollments --}}
                                    @if($registration->enrollments && $registration->enrollments->count() > 0)
                                        @foreach($registration->enrollments as $index => $enrollment)
                                            @if($index > 0)<br>@endif
                                            {{ $enrollment->package->package_name ?? 'N/A' }}
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                @else
                                    {{-- For pending (registrations), get from registration --}}
                                    {{ $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A') }}
                                @endif
                            </td>
                            <td>
                                @if(isset($history) && $history)
                                    {{-- For history (students), get from enrollments --}}
                                    @if($registration->enrollments && $registration->enrollments->count() > 0)
                                        @foreach($registration->enrollments as $index => $enrollment)
                                            @if($index > 0)<br>@endif
                                            {{ $enrollment->enrollment_type ?? 'N/A' }}
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                @else
                                    {{-- For pending (registrations), get from registration --}}
                                    {{ $registration->plan_name ?? ($registration->enrollment_type ? ucfirst($registration->enrollment_type) : 'N/A') }}
                                @endif
                            </td>
                            <td>
                                @if(isset($history) && $history)
                                    {{-- For history (students), get from enrollments --}}
                                    @if($registration->enrollments && $registration->enrollments->count() > 0)
                                        @foreach($registration->enrollments as $index => $enrollment)
                                            @if($index > 0)<br>@endif
                                            {{ $enrollment->program->program_name ?? 'N/A' }}
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                @else
                                    {{-- For pending (registrations), get from registration --}}
                                    {{ $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A') }}
                                @endif
                            </td>
                            @if(isset($history) && $history)
                                <td>
                                    @php
                                        $status     = 'Unverified';
                                        $badgeClass = 'bg-danger';
                                        if(isset($registration->user) && $registration->user && !empty($registration->user->role)){
                                            $roleLower = strtolower($registration->user->role);
                                            $status    = ucfirst($roleLower);
                                            $badgeClass = $roleLower === 'student' ? 'bg-success' : 'bg-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}"
                                          style="font-size:1em; text-transform:capitalize;">
                                        {{ $status }}
                                    </span>
                                </td>
                            @endif
                            <td class="text-end">
                                <button
                                    class="btn btn-primary btn-sm view-submission-btn"
                                    data-id="{{ $registration->registration_id ?? $registration->id ?? '' }}">
                                    View Submission
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="registrationModal" class="modal">
        <div class="modal-content landscape-modal">
            <span class="close">&times;</span>
            <h2>Registration Details</h2>
            <div id="modal-details" class="modal-details-structured landscape-details"></div>
            <div class="modal-actions">
                <form id="approveForm" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="approve-btn btn btn-success btn-sm">Approve</button>
                </form>
                <form id="rejectForm" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="reject-btn btn btn-danger btn-sm">Reject</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal        = document.getElementById('registrationModal');
    const modalDetails = document.getElementById('modal-details');
    const approveForm  = document.getElementById('approveForm');
    const rejectForm   = document.getElementById('rejectForm');
    const closeBtn     = document.querySelector('.close');
    const baseUrl      = document.querySelector('meta[name="base-url"]').content;
    const token        = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.view-submission-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;

            fetch(`${baseUrl}/admin/registration/${id}`)
                .then(res => res.ok ? res.json() : Promise.reject(res.statusText))
                .then(data => {
                    function na(val) {
                        return (val === undefined || val === null || val === "") ? "N/A" : val;
                    }
                    let left = '', right = '';

                    // Personal Info
                    left += `<div class='modal-section'>
                                <div class='modal-section-title'>Personal Information</div>
                                <div class='modal-row'>
                                  <div><label>Last Name</label><div>${na(data.lastname)}</div></div>
                                  <div><label>First Name</label><div>${na(data.firstname)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>Middle Name</label><div>${na(data.middlename)}</div></div>
                                  <div><label>Student School</label><div>${na(data.student_school)}</div></div>
                                </div>
                              </div>`;

                    // Contact Info
                    left += `<div class='modal-section'>
                                <div class='modal-section-title'>Contact Information</div>
                                <div class='modal-row'>
                                  <div style='flex:2'><label>Email</label><div>${na(data.email)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>Contact Number</label><div>${na(data.contact_number)}</div></div>
                                  <div><label>Emergency Contact</label><div>${na(data.emergency_contact_number)}</div></div>
                                </div>
                              </div>`;

                    // Address
                    left += `<div class='modal-section'>
                                <div class='modal-section-title'>Address</div>
                                <div class='modal-row'>
                                  <div><label>Street Address</label><div>${na(data.street_address)}</div></div>
                                  <div><label>City</label><div>${na(data.city)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>State/Province</label><div>${na(data.state_province)}</div></div>
                                  <div><label>Zipcode</label><div>${na(data.zipcode)}</div></div>
                                </div>
                              </div>`;

                    // Documents
                    right += `<div class='modal-section'>
                                <div class='modal-section-title'>Documents</div>
                                <div class='modal-row'>
                                  <div><label>Good Moral</label><div>${na(data.good_moral)}</div></div>
                                  <div><label>PSA</label><div>${na(data.PSA)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>Course Cert</label><div>${na(data.Course_Cert)}</div></div>
                                  <div><label>TOR</label><div>${na(data.TOR)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>Cert of Grad</label><div>${na(data.Cert_of_Grad)}</div></div>
                                  <div><label>Photo 2x2</label><div>${na(data.photo_2x2)}</div></div>
                                </div>
                              </div>`;

                    // Status & Dates
                    right += `<div class='modal-section'>
                                <div class='modal-section-title'>Status</div>
                                <div class='modal-row'>
                                  <div><label>Undergraduate</label><div>${na(data.Undergraduate)}</div></div>
                                  <div><label>Graduate</label><div>${na(data.Graduate)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>Start Date</label><div>${na(data.Start_Date)}</div></div>
                                  <div><label>Status</label><div>${na(data.status)}</div></div>
                                </div>
                              </div>`;

                    modalDetails.innerHTML = `<div class='landscape-col'>${left}</div>
                                              <div class='landscape-col'>${right}</div>`;
                    modal.style.display = 'flex';

                    approveForm.action = `${baseUrl}/admin/registration/${id}/approve`;
                    rejectForm.action  = `${baseUrl}/admin/registration/${id}/reject`;
                })
                .catch(() => {
                    modalDetails.innerHTML = '<div class="text-danger">Failed to load details.</div>';
                    modal.style.display    = 'flex';
                });
        });
    });

    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick   = e => { if (e.target === modal) modal.style.display = 'none'; };
});
</script>
@endsection
