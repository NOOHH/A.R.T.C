@extends('admin.admin-dashboard-layout')
@section('title', 'Student Registration')
@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin-student-registration.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4" style="color:#222;">{{ isset($history) && $history ? 'HISTORY' : 'PENDING' }}</h2>
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
                    @if(isset($history) && $history)
                        <th>STATUS</th>
                    @endif
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
                        @if(isset($history) && $history)
                            <td>
                                @php
                                    $status = 'APPROVED';
                                    if(isset($verifiedUsers) && isset($verifiedUsers[$registration->email])) {
                                        $role = $verifiedUsers[$registration->email]->role;
                                        if($role === 'verified') $status = 'VERIFIED';
                                        elseif($role === 'rejected') $status = 'REJECTED';
                                    }
                                @endphp
                                {{ $status }}
                            </td>
                        @endif
                        <td class="text-end">
                            @if(!isset($history) || !$history)
                                <button class="btn btn-primary btn-sm view-btn" style="background:#5c2f91;border:none;border-radius:16px;min-width:120px;" data-id="{{ $registration->registration_id }}">View Submission</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div id="registrationModal" class="modal" style="display:none;">
        <div class="modal-content landscape-modal">
            <span class="close">&times;</span>
            <h2>Registration Details</h2>
            <div id="modal-details" class="modal-details-structured landscape-details">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-actions">
                <form id="approveForm" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="approve-btn">Approve</button>
                </form>
                <form id="rejectForm" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="reject-btn">Reject</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('registrationModal');
    const modalDetails = document.getElementById('modal-details');
    const approveForm = document.getElementById('approveForm');
    const rejectForm = document.getElementById('rejectForm');
    const closeBtn = document.querySelector('.close');
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            fetch(`/admin/registration/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    function na(val) {
                        return (val === undefined || val === null || val === "") ? "N/A" : val;
                    }
                    // Landscape: split into left/right columns
                    let left = '', right = '';
                    left += `<div class='modal-section'><div class='modal-section-title'>Personal Information</div><div class='modal-row'><div><label>Last Name</label><div>${na(data.lastname)}</div></div><div><label>First Name</label><div>${na(data.firstname)}</div></div></div><div class='modal-row'><div><label>Middle Name</label><div>${na(data.middlename)}</div></div><div><label>Student School</label><div>${na(data.student_school)}</div></div></div></div>`;
                    left += `<div class='modal-section'><div class='modal-section-title'>Contact Information</div><div class='modal-row'><div style='flex:2'><label>Email</label><div>${na(data.email)}</div></div></div><div class='modal-row'><div><label>Contact Number</label><div>${na(data.contact_number)}</div></div><div><label>Emergency Contact</label><div>${na(data.emergency_contact_number)}</div></div></div></div>`;
                    left += `<div class='modal-section'><div class='modal-section-title'>Address</div><div class='modal-row'><div><label>Street Address</label><div>${na(data.street_address)}</div></div><div><label>City</label><div>${na(data.city)}</div></div></div><div class='modal-row'><div><label>State/Province</label><div>${na(data.state_province)}</div></div><div><label>Zipcode</label><div>${na(data.zipcode)}</div></div></div></div>`;
                    right += `<div class='modal-section'><div class='modal-section-title'>Documents</div><div class='modal-row'><div><label>Good Moral</label><div>${na(data.good_moral)}</div></div><div><label>PSA</label><div>${na(data.PSA)}</div></div></div><div class='modal-row'><div><label>Course Cert</label><div>${na(data.Course_Cert)}</div></div><div><label>TOR</label><div>${na(data.TOR)}</div></div></div><div class='modal-row'><div><label>Cert of Grad</label><div>${na(data.Cert_of_Grad)}</div></div><div><label>Photo 2x2</label><div>${na(data.photo_2x2)}</div></div></div></div>`;
                    right += `<div class='modal-section'><div class='modal-section-title'>Status</div><div class='modal-row'><div><label>Undergraduate</label><div>${na(data.Undergraduate)}</div></div><div><label>Graduate</label><div>${na(data.Graduate)}</div></div></div><div class='modal-row'><div><label>Start Date</label><div>${na(data.Start_Date)}</div></div><div><label>Status</label><div>${na(data.status)}</div></div></div></div>`;
                    modalDetails.innerHTML = `<div class='landscape-col'>${left}</div><div class='landscape-col'>${right}</div>`;
                    modal.style.display = 'flex';
                    approveForm.action = `/admin/registration/${id}/approve`;
                    rejectForm.action = `/admin/registration/${id}/reject`;
                })
                .catch(err => {
                    modalDetails.innerHTML = '<div style="color:red;">Failed to load details.</div>';
                    modal.style.display = 'flex';
                });
        });
    });
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
});
</script>
@endsection
