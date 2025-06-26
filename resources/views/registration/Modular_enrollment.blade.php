@extends('layouts.navbar') {{-- Uses your navbar layout --}}

@section('title', 'Student Registration')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
@endpush

@section('content')
<form action="{{ route('student.register') }}" method="POST" class="registration-form" enctype="multipart/form-data">
    @csrf
    <h2>STUDENT MODULAR <br> REGISTRATION FORM</h2>

    <h3>Student Information</h3>
    <div class="input-row">
        <input type="text" name="first_name" placeholder="First name" required>
        <input type="text" name="middle_name" placeholder="Middle name">
        <input type="text" name="last_name" placeholder="Last name" required>
    </div>
    <input type="text" name="school" placeholder="Student's school" class="input-full" required>

    <h3>Address</h3>
    <div class="input-row">
        <input type="text" name="street_address" placeholder="Street Address" required>
        <input type="text" name="state" placeholder="State/Province" required>
    </div>
    <div class="input-row">
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="zip" placeholder="Zip Code" required>
    </div>

    <h3>Contact Information</h3>
    <div class="input-row">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
    </div>
    <input type="text" name="emergency_contact" placeholder="Emergency Contact Number" class="input-full" required>

    <h3>Verification/Document Upload</h3>
    <div class="document-buttons">
        <label>Good Moral <input type="file" name="good_moral" hidden></label>
        <label>PSA Birth Cert. <input type="file" name="birth_cert" hidden></label>
        <label>Course Cert. <input type="file" name="course_cert" hidden></label>
        <label>ToR <input type="file" name="tor" hidden></label>
        <label>Cert. of Graduation <input type="file" name="grad_cert" hidden></label>
        <label>1x1 Photo <input type="file" name="photo" hidden></label>
    </div>

    <div class="input-row">
        <label><input type="radio" name="education" value="Undergraduate" checked> Undergraduate</label>
        <label><input type="radio" name="education" value="Graduate"> Graduate</label>
    </div>

    <h3>Courses</h3>
    <div class="input-row">
        <select name="course_1" required>
            <option value="">Course 1</option>
            <option value="Nursing">Nursing</option>
            <option value="Engineering">Engineering</option>
        </select>
    </div>

    <h3>Start Date</h3>
    <div class="course-box">
        <label for="start_date">
            <input type="date" name="start_date" id="start_date">
        </label>
    </div>

    <div class="terms">
        <a href="javascript:void(0)" onclick="openModal()" style="text-decoration: underline; color: #1c2951;">
            Read Terms and Conditions
        </a>
    </div>

    <!-- Modal -->
    <div id="termsModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999;">
        <div style="background-color: #fff; width: 80%; max-width: 600px; margin: 100px auto; padding: 20px; border-radius: 10px;">
            <h2>Terms and Conditions</h2>
            <p>
                By registering, you agree that all information provided is accurate.
                Uploaded documents are for verification only.
            </p>
            <div style="margin-top: 20px;">
                <label><input type="checkbox" id="agreeInModal"> I agree to the Terms and Conditions</label>
            </div>
            <button onclick="closeModal()" id="agreeBtn" disabled style="margin-top: 20px;">Agree and Close</button>
        </div>
    </div>

    <button type="submit" class="enroll-btn">Enroll</button>
</form>

<script>
    const modal = document.getElementById('termsModal');
    const agreeInModal = document.getElementById('agreeInModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');

    submitBtn.disabled = true;

    function openModal() {
        modal.style.display = 'block';
        agreeInModal.checked = false;
        agreeBtn.disabled = true;
    }

    function closeModal() {
        modal.style.display = 'none';
        if (agreeInModal.checked) {
            submitBtn.disabled = false;
        }
    }

    agreeInModal.addEventListener('change', () => {
        agreeBtn.disabled = !agreeInModal.checked;
    });

    window.onclick = function (event) {
        if (event.target === modal) {
            closeModal();
        }
    };

    form.addEventListener('submit', function (e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            alert('Please read and agree to the terms before submitting.');
        }
    });
</script>
@endsection
