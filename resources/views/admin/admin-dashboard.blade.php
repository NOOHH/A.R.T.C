@extends('admin.admin-dashboard-layout')
@section('title', 'Admin Dashboard')
@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
<style>
.pending-panel {
    border: none;
    border-radius: 18px;
    background: #f8f8fc;
    box-shadow: 0 4px 24px rgba(92,47,145,0.07);
    padding: 32px 32px 32px 32px;
    max-width: 430px;
    margin: 0 auto;
}
.panel-title {
    font-size: 1.25em;
    font-weight: bold;
    margin-bottom: 18px;
    color: #222;
    border-bottom: 2px solid #222;
    padding-bottom: 8px;
}
.pending-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: none;
    margin-top: 18px;
}
.pending-table th {
    background: #ede6f7;
    color: #5c2f91;
    font-weight: 700;
    border: none;
    border-radius: 16px 16px 0 0;
    font-size: 1.08em;
    padding: 16px 18px;
}
.pending-table td {
    background: #fff;
    color: #222;
    font-weight: 500;
    border: none;
    font-size: 1.08em;
    padding: 16px 18px;
}
.pending-table tr {
    border-radius: 0 0 16px 16px;
    box-shadow: none;
}
.pending-table tr:not(:last-child) td {
    border-bottom: 1px solid #e0e0e0;
}
.pending-table th, .pending-table td {
    text-align: left;
}
</style>
@endsection
@section('content')
@if(isset($dbError) && $dbError)
    <div style="background:#ffeaea;color:#b91c1c;padding:14px 18px;border-radius:8px;margin-bottom:18px;font-weight:600;border:1.5px solid #fca5a5;">
        <span style="font-size:1.2em;vertical-align:middle;">&#9888;&#65039;</span> {{ $dbError }}<br>
        <span style="font-weight:400;font-size:0.98em;">Some dashboard features are unavailable until the database is restored.</span>
    </div>
@endif
<div class="action-btns">
    <button><span>&#10133;</span></button>
    <button><span>&#9998;</span></button>
    <button><span>&#128465;</span></button>
    <button><span>&#128465;</span></button>
</div>
<div class="content-row">
    <!-- Course Cards -->
    <div class="course-list">
        <div class="course-card">
            <div class="title">Fundamentals of Engineering</div>
            <div class="progress">0% complete</div>
        </div>
        <div class="course-card">
            <div class="title">Fundamentals of Engineering</div>
            <div class="progress">0% complete</div>
        </div>
        <div class="course-card">
            <div class="title">Fundamentals of Engineering</div>
            <div class="progress">0% complete</div>
        </div>
    </div>
    <!-- Pending Student Registration -->
    <div class="pending-panel">
        <div class="panel-title">Pending Student Registration</div>
        @if(isset($dbError) && $dbError)
            <div style="color:#b91c1c;padding:12px 0;">Cannot load registrations. Database unavailable.</div>
        @else
            <table class="pending-table">
                <thead>
                    <tr>
                        <th>Last<br>name</th>
                        <th>First<br>name</th>
                        <th>Middle<br>name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $registration)
                    <tr>
                        <td>{{ $registration->lastname }}</td>
                        <td>{{ $registration->firstname }}</td>
                        <td>{{ $registration->middlename }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <!-- Modal -->
    <div id="registrationModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Registration Details</h2>
            <div id="modal-details" class="modal-details-structured">
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
                    // Group fields for better structure
                    function na(val) {
                        return (val === undefined || val === null || val === "") ? "N/A" : val;
                    }
                    let html = '';
                    html += `<div class='modal-section'><div class='modal-section-title'>Personal Information</div><div class='modal-row'><div><label>Last Name</label><div>${na(data.lastname)}</div></div><div><label>First Name</label><div>${na(data.firstname)}</div></div></div><div class='modal-row'><div><label>Middle Name</label><div>${na(data.middlename)}</div></div><div><label>Student School</label><div>${na(data.student_school)}</div></div></div></div>`;
                    html += `<div class='modal-section'><div class='modal-section-title'>Contact Information</div><div class='modal-row'><div><label>Email</label><div>${na(data.email)}</div></div><div><label>Contact Number</label><div>${na(data.contact_number)}</div></div></div><div class='modal-row'><div><label>Emergency Contact</label><div>${na(data.emergency_contact_number)}</div></div></div></div>`;
                    html += `<div class='modal-section'><div class='modal-section-title'>Address</div><div class='modal-row'><div><label>Street Address</label><div>${na(data.street_address)}</div></div><div><label>City</label><div>${na(data.city)}</div></div></div><div class='modal-row'><div><label>State/Province</label><div>${na(data.state_province)}</div></div><div><label>Zipcode</label><div>${na(data.zipcode)}</div></div></div></div>`;
                    html += `<div class='modal-section'><div class='modal-section-title'>Documents</div><div class='modal-row'><div><label>Good Moral</label><div>${na(data.good_moral)}</div></div><div><label>PSA</label><div>${na(data.PSA)}</div></div></div><div class='modal-row'><div><label>Course Cert</label><div>${na(data.Course_Cert)}</div></div><div><label>TOR</label><div>${na(data.TOR)}</div></div></div><div class='modal-row'><div><label>Cert of Grad</label><div>${na(data.Cert_of_Grad)}</div></div><div><label>Photo 2x2</label><div>${na(data.photo_2x2)}</div></div></div></div>`;
                    html += `<div class='modal-section'><div class='modal-section-title'>Status</div><div class='modal-row'><div><label>Undergraduate</label><div>${na(data.Undergraduate)}</div></div><div><label>Graduate</label><div>${na(data.Graduate)}</div></div></div><div class='modal-row'><div><label>Start Date</label><div>${na(data.Start_Date)}</div></div><div><label>Status</label><div>${na(data.status)}</div></div></div></div>`;
                    modalDetails.innerHTML = html;
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
