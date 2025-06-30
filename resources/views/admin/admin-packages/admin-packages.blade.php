@extends('admin.admin-dashboard-layout')

@section('title', 'Packages')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admin-packages.css') }}">
<style>
  /* 1) Let the main wrapper grow so cards never get clipped */
  .main-content-wrapper {
    align-items: flex-start !important;
  }

  /* 2) Make the white panel run under every card and add bottom breathing room */
  .packages-container {
    background: #fff;
    padding: 40px 20px 60px; /* extra bottom padding */
    margin: 40px 0 0 0;
    max-width: 1400px;
    width: 100%;
    box-sizing: border-box;
  }

  /* Header */
  .packages-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 0 10px;
  }
  .packages-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
  }
  .add-package-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  .add-package-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  }

  /* 3) Grid left-aligned, 2 columns when there's room */
  .package-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    justify-items: start;
  }

  /* 4) Cards: allow badge & shadow to overflow */
  .package-item {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    width: 100%;
    max-width: 380px;
    cursor: pointer;
    position: relative;
    overflow: visible;            /* ← key fix */
    border: 1px solid #e9ecef;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 20px;
  }
  .package-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  }

  /* Card header */
  .package-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    position: relative;
  }
  .package-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    text-align: center;
    hyphens: auto;
    word-wrap: break-word;
  }

  /* 5) Badge fully inside the card */
  .package-badge {
    position: absolute;
    top: 20px;         /* pulled in from corner */
    right: 15px;
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
    z-index: 2;
  }

  /* Card content */
  .package-content {
    padding: 25px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  .package-description {
    color: #6c757d;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0;
    hyphens: auto;
    word-wrap: break-word;
  }
  .package-price {
    font-size: 2rem;
    font-weight: 800;
    color: #2c3e50;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Action buttons */
  .package-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
  }
  .edit-package-btn,
  .delete-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s ease;
  }
  .edit-package-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
  }
  .edit-package-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
  }
  .delete-btn {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220,53,69,0.3);
  }
  .delete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220,53,69,0.4);
  }

  /* “No packages” message */
  .no-packages {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 20px;
    color: #6c757d;
    font-size: 1.2rem;
  }

  /* Modals (unchanged) */
  .modal-bg { /* … */ }
  .modal { /* … */ }
  .modal h3 { /* … */ }
  .modal input, .modal textarea { /* … */ }
  .modal-actions { /* … */ }
  .cancel-btn, .add-btn { /* … */ }

  /* Responsive */
  @media (max-width: 768px) {
    .packages-header {
      flex-direction: column;
      gap: 20px;
      text-align: center;
    }
    .packages-header h1 {
      font-size: 2rem;
    }
    .package-list {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .modal {
      padding: 30px 20px;
      margin: 20px;
    }
    .modal-actions {
      flex-direction: column;
    }
  }
  @media (max-width: 480px) {
    .package-list {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
<div class="main-content-wrapper" style="display: flex; flex-direction: column; align-items: center; width: 100%; min-width: 0;">
    <div class="packages-container">
        <div class="packages-header">
            <h1>Packages</h1>
            <button class="add-package-btn" id="showAddModal">
                <span style="font-size:1.3em;">&#43;</span> Add Package
            </button>
        </div>
        
        @if(session('success'))
            <div class="success-alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="error-alert">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="package-list">
            @forelse($packages as $package)
            <div class="package-item">
                <div class="package-header">
                    <h3 class="package-name">{{ $package->package_name }}</h3>
                    <div class="package-badge">Premium</div>
                </div>
                <div class="package-content">
                    <div class="package-description" title="{{ $package->description }}">
                        {{ $package->description }}
                    </div>
                    <div class="package-price">₱{{ number_format($package->amount ?? 0, 2) }}</div>
                    <div class="package-actions">
                        <button type="button" class="edit-package-btn" 
                                data-id="{{ $package->package_id }}" 
                                data-name="{{ $package->package_name }}" 
                                data-description="{{ $package->description }}" 
                                data-amount="{{ $package->amount }}">
                            Edit Package
                        </button>
                        <form action="{{ route('admin.packages.delete', $package->package_id) }}" method="POST" style="margin:0; flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this package?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
                <div class="no-packages">
                    <h3>No packages found</h3>
                    <p>Create your first package to get started!</p>
                </div>
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
    // Modal functionality
    document.getElementById('showAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.add('active');
    };
    document.getElementById('cancelAddModal').onclick = function () {
        document.getElementById('addModalBg').classList.remove('active');
    };
    document.getElementById('addModalBg').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };

    // Edit package functionality
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

    // Add smooth scrolling to top when modals close
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection
