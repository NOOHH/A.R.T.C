@extends('admin.admin-dashboard-layout')

@section('title', 'Packages')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admin-packages.css') }}">
<style>
.package-description.collapsible {
    display: block;
    max-height: 3.6em; /* about 2 lines */
    overflow: hidden;
    position: relative;
    cursor: pointer;
    transition: max-height 0.3s;
    color: #888;
    white-space: pre-line;
}
.package-description.collapsible .desc-toggle {
    display: block;
    color: #007bff;
    font-weight: bold;
    cursor: pointer;
    margin-top: 0.2em;
}
.package-description.collapsible.expanded {
    max-height: 1000px;
    overflow: visible;
}
</style>
@endpush

@section('content')
<div class="main-content-wrapper" style="display: flex; flex-direction: column; align-items: center; width: 100%; min-width: 0;">
    <div class="packages-container" style="margin: 40px 0 0 0; max-width: 750px; min-width: 350px; width: 100%; box-sizing: border-box;">
        <div class="packages-header">
            PACKAGES
            <button class="add-package-btn" id="showAddModal">
                <span style="font-size:1.3em;">&#43;</span> Add Package
            </button>
        </div>
        @if(session('success'))
            <div class="success-alert" style="margin: 0 auto; text-align: center; max-width: 500px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-alert" style="margin: 0 auto; text-align: center; max-width: 500px;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="error-message" style="margin: 0 auto; text-align: center; max-width: 500px;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="package-list" style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: center;">
            @forelse($packages as $package)
            <div class="package-item" style="flex: 0 1 320px; min-width: 280px; max-width: 350px;">
                <span><strong>{{ $package->package_name }}</strong></span>
                <span class="status-bar">
                    <span class="package-description">{{ $package->description }}</span>
                </span>
                <span class="package-price">₱{{ number_format($package->amount ?? 0, 2) }}</span>
                <div class="package-actions">
                    <button type="button" class="edit-package-btn" data-id="{{ $package->package_id }}" data-name="{{ $package->package_name }}" data-description="{{ $package->description }}" data-amount="{{ $package->amount }}">Edit</button>
                    <form action="{{ route('admin.packages.delete', $package->package_id) }}" method="POST" style="margin:0;display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </div>
            </div>
            @empty
                <div class="no-packages">No packages found.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-bg" id="addModalBg">
    <div class="modal">
        <h3>Create Package</h3>
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf
            <input type="text" name="package_name" placeholder="Package Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="amount" placeholder="Amount (₱)" min="0" step="0.01" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelAddModal">Cancel</button>
                <button type="submit" class="add-btn">Add Package</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-bg" id="editModalBg">
    <div class="modal">
        <h3>Edit Package</h3>
        <form id="editPackageForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="package_name" id="editPackageName" placeholder="Package Name" required>
            <textarea name="description" id="editPackageDescription" placeholder="Description" required></textarea>
            <input type="number" name="amount" id="editPackageAmount" placeholder="Amount (₱)" min="0" step="0.01" required>
            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelEditModal">Cancel</button>
                <button type="submit" class="add-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('showAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.add('active');
    };
    document.getElementById('cancelAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.remove('active');
    };
    document.getElementById('addModalBg').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };

    document.querySelectorAll('.edit-package-btn').forEach(function(btn) {
        btn.onclick = function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            var desc = this.getAttribute('data-description');
            var amount = this.getAttribute('data-amount');
            document.getElementById('editPackageName').value = name;
            document.getElementById('editPackageDescription').value = desc;
            document.getElementById('editPackageAmount').value = amount;
            var form = document.getElementById('editPackageForm');
            form.action = '/admin/packages/' + id;
            document.getElementById('editModalBg').classList.add('active');
        };
    });
    document.getElementById('cancelEditModal').onclick = function () {
        document.getElementById('editModalBg').classList.remove('active');
    };
    document.getElementById('editModalBg').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };

    // Expand/collapse package description with preview and toggle text
    document.querySelectorAll('.package-description').forEach(function(desc) {
        // Only add toggle if text is long
        if (desc.textContent.length > 10) {
            desc.classList.add('collapsible');
            var toggle = document.createElement('span');
            toggle.className = 'desc-toggle';
            toggle.textContent = ' Show more';
            desc.appendChild(toggle);
            desc.onclick = function(e) {
                // Prevent toggle click from bubbling up
                if (e.target.classList.contains('desc-toggle') || e.target === desc) {
                    desc.classList.toggle('expanded');
                    toggle.textContent = desc.classList.contains('expanded') ? ' Show less' : ' Show more';
                }
            };
        }
    });
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-packages.js') }}"></script>
@endpush
