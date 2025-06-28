@extends('layouts.navbar')

@section('title', 'Home')

@push('styles')
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 999;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: auto;
        padding: 30px;
        border: 1px solid #888;
        width: 90%;
        max-width: 800px;
        border-radius: 20px;
    }

    .packages-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .package-card {
        background-color: #f9f9f9;
        border-radius: 15px;
        padding: 20px;
        width: 220px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.2s;
        cursor: pointer;
    }

    .package-card:hover {
        transform: translateY(-5px);
    }

    .package-card h4 {
        margin: 10px 0;
    }

    .package-card p {
        font-size: 0.9rem;
        color: #555;
        text-align: center;
    }

    .close-btn {
        background-color: #ff4b5c;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 25px;
        margin: 20px auto 0;
        display: block;
        cursor: pointer;
        transition: background 0.2s;
    }

    .close-btn:hover {
        background-color: #d93c4b;
    }

    .selected-package-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #e0f3ff;
        color: #1c2951;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-top: 25px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .selected-package-badge:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .selected-package-icon {
        background-color: #1c2951;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
        {{-- Complete Plan --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Plan</h3>
            <button id="enrollCompleteBtn" onclick="enroll('Complete')" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                Enroll
            </button>
        </div>

        {{-- Modular Plan --}}
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Plan</h3>
            <button id="enrollModularBtn" onclick="enroll('Modular')" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                Enroll
            </button>
        </div>
    </div>

    {{-- Package Select Button --}}
    <button id="openModalBtn" style="padding: 12px 30px; border-radius: 20px; font-size: 1rem; border: none; background-color: #1c2951; color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer;">
        Select Package
    </button>

    {{-- Display selected package as badge --}}
    <div id="selectedPackageText" class="selected-package-badge" style="display: none;">
        <div class="selected-package-icon">✓</div>
        <span id="selectedPackageName"></span>
    </div>
</div>

{{-- Modal --}}
<div id="packageModal" class="modal">
    <div class="modal-content">
        <h3 style="text-align:center; margin-bottom: 20px;">Select Your Package</h3>
        <div class="packages-grid">
            @foreach($packages as $package)
                <div class="package-card" onclick="selectPackage('{{ $package->package_id }}', '{{ $package->package_name }}')">
                    <h4>{{ $package->package_name }}</h4>
                    <p>{{ $package->description ?? 'No description available.' }}</p>
                    <p><strong>₱{{ number_format($package->price, 2) }}</strong></p>
                </div>
            @endforeach
        </div>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
</div>

<script>
    const enrollCompleteBtn = document.getElementById('enrollCompleteBtn');
    const enrollModularBtn = document.getElementById('enrollModularBtn');
    const packageModal = document.getElementById('packageModal');
    const selectedPackageText = document.getElementById('selectedPackageText');
    const selectedPackageName = document.getElementById('selectedPackageName');
    const openModalBtn = document.getElementById('openModalBtn');

    let selectedPackageId = null;

    openModalBtn.addEventListener('click', () => {
        packageModal.style.display = "block";
    });

    function closeModal() {
        packageModal.style.display = "none";
    }

    function selectPackage(packageId, packageName) {
        selectedPackageId = packageId;
        selectedPackageName.textContent = packageName;
        selectedPackageText.style.display = 'inline-flex';

        enrollCompleteBtn.disabled = false;
        enrollModularBtn.disabled = false;
        enrollCompleteBtn.style.opacity = '1';
        enrollModularBtn.style.opacity = '1';
        enrollCompleteBtn.style.cursor = 'pointer';
        enrollModularBtn.style.cursor = 'pointer';

        closeModal();
    }

    function enroll(type) {
        if (!selectedPackageId) {
            alert('Please select your package first.');
            return;
        }

        let programId, planId;
        if (type === 'Complete') {
            programId = 1; // adjust as needed
            planId = 1;    // set your plan id for Complete
        } else if (type === 'Modular') {
            programId = 2; // adjust as needed
            planId = 2;    // set your plan id for Modular
        } else {
            alert('Invalid enrollment type');
            return;
        }

        let enrollmentType = type.toLowerCase();
        let url;
        if (enrollmentType === 'complete') {
            url = `/enrollment/full?enrollment_type=full&package_id=${selectedPackageId}&program_id=${programId}&plan_id=${planId}`;
        } else if (enrollmentType === 'modular') {
            url = `/enrollment/modular?enrollment_type=modular&package_id=${selectedPackageId}&program_id=${programId}&plan_id=${planId}`;
        } else {
            alert('Invalid enrollment type');
            return;
        }
        window.location.href = url;
    }
</script>
@endsection
