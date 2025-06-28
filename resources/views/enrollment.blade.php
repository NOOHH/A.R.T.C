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
        max-height: 80vh;
        overflow-y: auto;
    }
.modal-content h3 {
    font-size: 2.5rem;
    font-weight: bold;
}
    .packages-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 36px;
        justify-content: center;
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 8px;
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
        margin: 0 16px 0 0;
    }

    .package-card:hover {
        transform: translateY(-5px);
    }

    .package-card h4 {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        margin: 10px 0;
    }

    .package-card p.description {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
        transition: all 0.3s;
        cursor: pointer;
        text-align: center;
        position: relative;
    }

    .package-card p.description .desc-toggle {
        color: #007bff;
        font-weight: bold;
        cursor: pointer;
        display: none;
        margin-left: 4px;
    }

    .package-card p.description.expanded .desc-toggle {
        display: inline;
    }

    .package-card p.description.show-toggle .desc-toggle {
        display: inline;
    }

    .package-card p.description.expanded {
        -webkit-line-clamp: unset;
        overflow: visible;
    }

    .select-btn {
        margin-top: 10px;
        padding: 8px 20px;
        border: none;
        border-radius: 8px;
        background-color: #1c2951;
        color: white;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .select-btn:hover {
        background-color: #344160;
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

    .package-card:last-child {
        margin-right: 0;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh;">
    <div style="display: flex; gap: 60px; margin-bottom: 40px;">
<<<<<<< HEAD
        {{-- Complete Plan --}}
=======
>>>>>>> main
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Complete Plan</h3>
            <button id="enrollCompleteBtn" onclick="enroll('Complete')" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                Enroll
            </button>
        </div>
<<<<<<< HEAD

        {{-- Modular Plan --}}
=======
>>>>>>> main
        <div class="program-card" style="width: 350px; height: 260px; border-radius: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 24px;">Modular Plan</h3>
            <button id="enrollModularBtn" onclick="enroll('Modular')" class="enroll-btn" style="background-color: #1c2951; color: white; border-radius: 20px; padding: 10px 40px; font-size: 1rem; border: none; opacity: 0.5; cursor: not-allowed;" disabled>
                Enroll
            </button>
        </div>
    </div>

    <button id="openModalBtn" style="padding: 12px 30px; border-radius: 20px; font-size: 1rem; border: none; background-color: #1c2951; color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer;">
        Select Package
    </button>

    <div id="selectedPackageText" class="selected-package-badge" style="display: none;">
        <div class="selected-package-icon">✓</div>
        <span id="selectedPackageName"></span>
    </div>
</div>

<div id="packageModal" class="modal">
    <div class="modal-content">
        <h3 style="text-align:center; margin-bottom: 20px;">Select Your Package</h3>
        <div class="packages-grid">
            @foreach($packages as $package)
                <div class="package-card">
                    <h4>{{ $package->package_name }}</h4>
                    <p class="description" onclick="toggleDescription(this, event)">
                        {{ $package->description ?? 'No description available.' }}
                        <span class="desc-toggle"> Show more</span>
                    </p>
                    <p><strong>₱{{ number_format($package->amount ?? 0, 2) }}</strong></p>
                    <button type="button" class="select-btn" onclick="selectPackage('{{ $package->package_id }}', '{{ $package->package_name }}', event)">Select</button>
                </div>
            @endforeach
        </div>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
</div>

<script>
function toggleDescription(el, e) {
    e.stopPropagation();
    el.classList.toggle('expanded');
    let toggleSpan = el.querySelector('.desc-toggle');
    if (el.classList.contains('expanded')) {
        toggleSpan.textContent = ' Show less';
    } else {
        toggleSpan.textContent = ' Show more';
    }
}

function selectPackage(packageId, packageName, e) {
    e.stopPropagation();
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

function enroll(type) {
    if (!selectedPackageId) {
        alert('Please select your package first.');
        return;
    }

    let programId = (type === 'Complete') ? 1 : (type === 'Modular') ? 2 : null;
    if (!programId) {
        alert('Invalid enrollment type');
        return;
    }

    let url = (type === 'Complete')
        ? `/enrollment/full?enrollment_type=full&package_id=${selectedPackageId}&program_id=${programId}`
        : `/enrollment/modular?enrollment_type=modular&package_id=${selectedPackageId}&program_id=${programId}`;

    window.location.href = url;
}

// After the DOM is loaded, check each description and only show the toggle if the text is long enough

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.package-card p.description').forEach(function(desc) {
        // Get the text content (excluding the toggle span)
        var text = desc.childNodes[0].nodeValue ? desc.childNodes[0].nodeValue.trim() : '';
        var charLimit = 60; // Set your character limit for when to show the toggle
        var toggle = desc.querySelector('.desc-toggle');
        if (text.length > charLimit) {
            desc.classList.add('show-toggle');
            if (toggle) toggle.style.display = 'inline';
        } else {
            if (toggle) toggle.style.display = 'none';
        }
    });
<<<<<<< HEAD

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
=======
});
>>>>>>> main
</script>
@endsection
