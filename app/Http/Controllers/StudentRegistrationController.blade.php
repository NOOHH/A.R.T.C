@extends('layouts.navbar')

@section('title', 'Student Registration')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ENROLLMENT/Full_Enrollment.css') }}">
@endpush

@section('content')
<form action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" class="registration-form">
    @csrf
    <h2>STUDENT REGISTRATION FORM</h2>

    {{-- Show validation error --}}
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    {{-- Form fields --}}
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
        <label>Good Moral <input type="file" name="good_moral" hidden required></label>
        <label>PSA Birth Cert. <input type="file" name="birth_cert" hidden required></label>
        <label>Course Cert. <input type="file" name="course_cert" hidden required></label>
        <label>ToR <input type="file" name="tor" hidden required></label>
        <label>Cert. of Graduation <input type="file" name="grad_cert" hidden required></label>
        <label>1x1 Photo <input type="file" name="photo" hidden required></label>
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
            <input type="date" name="start_date" id="start_date" required>
        </label>
    </div>

    <button type="submit" class="enroll-btn">Enroll</button>
</form>
@endsection


    <button type="submit" class="enroll-btn">Enroll</button>
</form>
<script>
    const modal = document.getElementById('termsModal');
    const agreeInModal = document.getElementById('agreeInModal');
    const agreeBtn = document.getElementById('agreeBtn');
    const form = document.querySelector('.registration-form');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Disable submit on page load
    submitBtn.disabled = true;

    function openModal() {
        modal.style.display = 'block';
        agreeInModal.checked = false;
        agreeBtn.disabled = true;
    }

    function closeModal() {
        if (agreeInModal.checked) {
            submitBtn.disabled = false; // Enable submit button
        }
        modal.style.display = 'none';
    }

    agreeInModal.addEventListener('change', () => {
        agreeBtn.disabled = !agreeInModal.checked;
    });

    // Clicking outside the modal closes it
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Prevent form submission if user didn't agree
    form.addEventListener('submit', function (e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            alert('Please agree to the terms before submitting.');
        }
    });
</script>

@endsection
