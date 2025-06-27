<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
</head>
<body>
<div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-row">
            <img src='{{ asset('images/logo.png') }}' alt='Logo'>
            <div class="brand-text">Ascendo Review<br>and Training Center</div>
        </div>
        <nav>
            <ul>
                <li class="active"><span>&#128200;</span> Dashboard</li>
                <li><span>&#128100;</span> Student Registration</li>
                <li><span>&#128221;</span> Enrollment</li>
                <li><span>&#128451;</span> Programs</li>
                <li><span>&#128101;</span> Professors</li>
            </ul>
        </nav>
        <div style="flex: 1;"></div>
        <ul class="bottom-links">
            <li><span>&#10067;</span> Help</li>
            <li><span>&#9881;&#65039;</span> Settings</li>
            <li class="logout"><span>&#8634;</span> Logout</li>
        </ul>
    </aside>
    <!-- Main Content -->
    <div class="main">
        @if(isset($dbError) && $dbError)
            <div style="background:#ffeaea;color:#b91c1c;padding:14px 18px;border-radius:8px;margin-bottom:18px;font-weight:600;border:1.5px solid #fca5a5;">
                <span style="font-size:1.2em;vertical-align:middle;">&#9888;&#65039;</span> {{ $dbError }}<br>
                <span style="font-weight:400;font-size:0.98em;">Some dashboard features are unavailable until the database is restored.</span>
            </div>
        @endif
        <!-- Top Bar -->
        <div class="topbar">
            <div class="searchbar">
                <span style="font-size: 1.3em; margin-right: 10px;">&#9776;</span>
                <input type="text" placeholder="Search">
                <span style="font-size: 1.2em; color: #888; margin-left: 8px;">&#128269;</span>
            </div>
            <div style="flex: 1;"></div>
            <span class="icon">&#128172;</span>
            <span class="icon">&#128100;</span>
        </div>
        <!-- Action Buttons -->
        <div class="action-btns">
            <button><span>&#10133;</span></button>
            <button><span>&#9998;</span></button>
            <button><span>&#128465;</span></button>
            <button><span>&#128465;</span></button>
        </div>
        <!-- Course Cards and Pending Registration -->
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
                                <th style="text-align:center; min-width:110px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                            <tr>
                                <td>{{ $registration->lastname }}</td>
                                <td>{{ $registration->firstname }}</td>
                                <td>{{ $registration->middlename }}</td>
                                <td style="text-align:center;">
                                    <button class="view-btn" data-id="{{ $registration->registration_id }}">View</button>
                                </td>
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
                                let html = '';
                                html += `<div class='modal-section'><div class='modal-section-title'>Personal Information</div><div class='modal-row'><div><label>Last Name</label><div>${data.lastname || ''}</div></div><div><label>First Name</label><div>${data.firstname || ''}</div></div></div><div class='modal-row'><div><label>Middle Name</label><div>${data.middlename || ''}</div></div><div><label>Student School</label><div>${data.student_school || ''}</div></div></div></div>`;
                                html += `<div class='modal-section'><div class='modal-section-title'>Contact Information</div><div class='modal-row'><div><label>Email</label><div>${data.email || ''}</div></div><div><label>Contact Number</label><div>${data.contact_number || ''}</div></div></div><div class='modal-row'><div><label>Emergency Contact</label><div>${data.emergency_contact_number || ''}</div></div></div></div>`;
                                html += `<div class='modal-section'><div class='modal-section-title'>Address</div><div class='modal-row'><div><label>Street Address</label><div>${data.street_address || ''}</div></div><div><label>City</label><div>${data.city || ''}</div></div></div><div class='modal-row'><div><label>State/Province</label><div>${data.state_province || ''}</div></div><div><label>Zipcode</label><div>${data.zipcode || ''}</div></div></div></div>`;
                                html += `<div class='modal-section'><div class='modal-section-title'>Documents</div><div class='modal-row'><div><label>Good Moral</label><div>${data.good_moral || ''}</div></div><div><label>PSA</label><div>${data.PSA || ''}</div></div></div><div class='modal-row'><div><label>Course Cert</label><div>${data.Course_Cert || ''}</div></div><div><label>TOR</label><div>${data.TOR || ''}</div></div></div><div class='modal-row'><div><label>Cert of Grad</label><div>${data.Cert_of_Grad || ''}</div></div><div><label>Photo 2x2</label><div>${data.photo_2x2 || ''}</div></div></div></div>`;
                                html += `<div class='modal-section'><div class='modal-section-title'>Status</div><div class='modal-row'><div><label>Undergraduate</label><div>${data.Undergraduate || ''}</div></div><div><label>Graduate</label><div>${data.Graduate || ''}</div></div></div><div class='modal-row'><div><label>Start Date</label><div>${data.Start_Date || ''}</div></div><div><label>Status</label><div>${data.status || ''}</div></div></div></div>`;
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
        </div>
    </div>
</div>
</body>
</html>
