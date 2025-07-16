@extends('admin.admin-dashboard-layout')

@section('title', 'Student Registration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Student Registration Pending</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.student.registration.payment.pending') }}" class="btn btn-outline-warning">
                        <i class="bi bi-credit-card"></i> Payment Pending
                    </a>
                    <a href="{{ route('admin.student.registration.history') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history"></i> Registration History
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Students with Pending Registration</h6>
                </div>
                <div class="card-body">
                    @if($registrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Program</th>
                                        <th>Package</th>
                                        <th>Plan Type</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                    <tr>
                                        <td>
                                            {{ ($registration->firstname ?? ($registration->student->firstname ?? '')) }} 
                                            {{ ($registration->middlename ?? ($registration->student->middlename ?? '')) }}
                                            {{ ($registration->lastname ?? ($registration->student->lastname ?? '')) }}
                                        </td>
                                        <td>
                                            @if(isset($registration->user) && $registration->user)
                                                {{ $registration->user->email ?? 'N/A' }}
                                            @elseif(isset($registration->email))
                                                {{ $registration->email }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A') }}</td>
                                        <td>{{ $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A') }}</td>
                                        <td>{{ $registration->plan_name ?? ($registration->enrollment_type ? ucfirst($registration->enrollment_type) : 'N/A') }}</td>
                                        <td>{{ $registration->created_at ? $registration->created_at->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ ucfirst($registration->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="approveRegistration('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewRegistrationDetails('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="rejectRegistration('{{ $registration->registration_id }}')">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-plus" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No Pending Registrations</h5>
                            <p class="text-muted">All student registrations have been processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal           = document.getElementById('registrationModal');
    const modalDetails    = document.getElementById('modal-details');
    const closeBtn        = document.querySelector('.close');
    const viewBtns        = document.querySelectorAll('.view-submission-btn');
    const approveForm     = document.getElementById('approveForm');
    const rejectForm      = document.getElementById('rejectForm');
    const baseUrl         = window.location.origin;

    const na = (value) => value || 'N/A';

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!id) return;

            // Fetch registration details
            fetch(`${baseUrl}/admin/registration/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    // Basic Information
                    let left = `<div class='modal-section'>
                                  <div class='modal-section-title'>Basic Information</div>
                                  <div class='modal-row'>
                                    <div><label>Name</label><div>${na(data.firstname)} ${na(data.middlename)} ${na(data.lastname)}</div></div>
                                    <div><label>Email</label><div>${na(data.email)}</div></div>
                                  </div>
                                  <div class='modal-row'>
                                    <div><label>Phone</label><div>${na(data.mobile_number)}</div></div>
                                    <div><label>Gender</label><div>${na(data.gender)}</div></div>
                                  </div>
                                  <div class='modal-row'>
                                    <div><label>Birthday</label><div>${na(data.birthday)}</div></div>
                                    <div><label>Age</label><div>${na(data.age)}</div></div>
                                  </div>
                                </div>`;

                    // Address Information
                    left += `<div class='modal-section'>
                               <div class='modal-section-title'>Address</div>
                               <div class='modal-row'>
                                 <div><label>Address</label><div>${na(data.address)}</div></div>
                                 <div><label>City</label><div>${na(data.city)}</div></div>
                               </div>
                               <div class='modal-row'>
                                 <div><label>Province</label><div>${na(data.province)}</div></div>
                                 <div><label>ZIP Code</label><div>${na(data.zipcode)}</div></div>
                               </div>
                             </div>`;

                    // Educational Background
                    let right = `<div class='modal-section'>
                                   <div class='modal-section-title'>Educational Background</div>
                                   <div class='modal-row'>
                                     <div><label>Degree</label><div>${na(data.degree)}</div></div>
                                     <div><label>School</label><div>${na(data.school)}</div></div>
                                   </div>
                                   <div class='modal-row'>
                                     <div><label>Year Graduated</label><div>${na(data.year_graduated)}</div></div>
                                   </div>
                                 </div>`;

                    // Program Information
                    right += `<div class='modal-section'>
                                <div class='modal-section-title'>Program Details</div>
                                <div class='modal-row'>
                                  <div><label>Program</label><div>${na(data.program_name)}</div></div>
                                  <div><label>Package</label><div>${na(data.package_name)}</div></div>
                                </div>
                                <div class='modal-row'>
                                  <div><label>Plan Type</label><div>${na(data.plan_type)}</div></div>
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

<script>
// Global functions for onclick handlers
function viewRegistrationDetails(registrationId) {
    const modal = document.getElementById('registrationModal');
    const modalDetails = document.getElementById('modal-details');
    const baseUrl = window.location.origin;
    
    if (!modal || !modalDetails) {
        console.error('Modal elements not found');
        return;
    }
    
    const na = (value) => value || 'N/A';
    
    // Fetch registration details
    fetch(`${baseUrl}/admin/registration/${registrationId}/details`)
        .then(response => response.json())
        .then(data => {
            // Basic Information
            let left = `<div class='modal-section'>
                          <div class='modal-section-title'>Basic Information</div>
                          <div class='modal-row'>
                            <div><label>Name</label><div>${na(data.firstname)} ${na(data.middlename)} ${na(data.lastname)}</div></div>
                            <div><label>Email</label><div>${na(data.email)}</div></div>
                          </div>
                          <div class='modal-row'>
                            <div><label>Phone</label><div>${na(data.mobile_number)}</div></div>
                            <div><label>Gender</label><div>${na(data.gender)}</div></div>
                          </div>
                          <div class='modal-row'>
                            <div><label>Birthdate</label><div>${na(data.birthdate)}</div></div>
                            <div><label>Age</label><div>${na(data.age)}</div></div>
                          </div>
                        </div>`;

            // Address Section
            left += `<div class='modal-section'>
                       <div class='modal-section-title'>Address</div>
                       <div class='modal-row'>
                         <div><label>Address</label><div>${na(data.address)}</div></div>
                         <div><label>City</label><div>${na(data.city)}</div></div>
                       </div>
                       <div class='modal-row'>
                         <div><label>State/Province</label><div>${na(data.state_province)}</div></div>
                         <div><label>Zip Code</label><div>${na(data.zipcode)}</div></div>
                       </div>
                     </div>`;

            // Program Information
            let right = `<div class='modal-section'>
                           <div class='modal-section-title'>Program Information</div>
                           <div class='modal-row'>
                             <div><label>Program</label><div>${na(data.program_name)}</div></div>
                             <div><label>Package</label><div>${na(data.package_name)}</div></div>
                           </div>
                           <div class='modal-row'>
                             <div><label>Plan</label><div>${na(data.plan_name)}</div></div>
                             <div><label>Learning Mode</label><div>${na(data.learning_mode)}</div></div>
                           </div>
                           <div class='modal-row'>
                             <div><label>Start Date</label><div>${na(data.Start_Date)}</div></div>
                             <div><label>Status</label><div>${na(data.status)}</div></div>
                           </div>
                         </div>`;

            // Documents Section
            right += `<div class='modal-section'>
                        <div class='modal-section-title'>Documents</div>`;
            
            const documents = ['PSA', 'TOR', 'Course_Cert', 'good_moral', 'photo_2x2'];
            documents.forEach(doc => {
                if (data[doc]) {
                    right += `<div class='modal-row'>
                                <div><label>${doc.replace('_', ' ')}</label><div>✅ Uploaded</div></div>
                              </div>`;
                }
            });
            right += `</div>`;

            modalDetails.innerHTML = `
                <div class='modal-columns'>
                    <div class='modal-left'>${left}</div>
                    <div class='modal-right'>${right}</div>
                </div>
            `;

            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching registration details:', error);
            alert('Error loading registration details. Please try again.');
        });
}

function approveRegistration(registrationId) {
    if (confirm('Are you sure you want to approve this registration?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${window.location.origin}/admin/registration/${registrationId}/approve`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectRegistration(registrationId) {
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason !== null) { // User didn't cancel
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${window.location.origin}/admin/registration/${registrationId}/reject`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        if (reason.trim()) {
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);
        }
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
