@extends('layouts.navbar')

@section('title', 'Home')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
        {{-- Complete Program --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Program</h3>
            <button id="enrollCompleteBtn" onclick="enroll('Complete')" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                Enroll
            </button>
        </div>

        {{-- Modular Enrollment --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Enrollment</h3>
            <button id="enrollModularBtn" onclick="enroll('Modular')" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                Enroll
            </button>
        </div>
    </div>

    
    <div class="select-container" style="margin-top: 0;">
        <select id="packageSelect" style="padding: 12px 30px; border-radius: 20px; font-size: 1rem; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <option selected disabled value="">Please select your package</option>
            @foreach($packages as $package)
                <option value="{{ $package->package_id }}">{{ $package->package_name }}</option>
            @endforeach
        </select>
    </div>
</div>

<script>
// Enable enroll buttons only when a package is selected
const packageSelect = document.getElementById('packageSelect');
const enrollCompleteBtn = document.getElementById('enrollCompleteBtn');
const enrollModularBtn = document.getElementById('enrollModularBtn');

packageSelect.addEventListener('change', function() {
    const selected = packageSelect.value !== '';
    enrollCompleteBtn.disabled = !selected;
    enrollModularBtn.disabled = !selected;
    enrollCompleteBtn.style.opacity = selected ? '1' : '0.5';
    enrollModularBtn.style.opacity = selected ? '1' : '0.5';
    enrollCompleteBtn.style.cursor = selected ? 'pointer' : 'not-allowed';
    enrollModularBtn.style.cursor = selected ? 'pointer' : 'not-allowed';
});

function enroll(type) {
    const packageId = document.getElementById('packageSelect').value;
    if (!packageId) {
        alert('Please select your package first.');
        return;
    }
    if (type === 'Modular') {
        window.location.href = `/enrollment/modular?enrollment_type=${type}&package_id=${packageId}`;
    } else {
        window.location.href = `/enrollment/full?enrollment_type=${type}&package_id=${packageId}`;
    }
}
</script>
@endsection
