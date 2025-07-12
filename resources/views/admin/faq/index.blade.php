@extends('admin.admin-dashboard-layout')

@section('title', 'FAQ Management - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-primary">
                        <i class="bi bi-question-circle me-2"></i>FAQ Management
                    </h1>
                    <p class="text-muted mb-0">Manage frequently asked questions for the chat system</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                        <i class="bi bi-plus-circle me-1"></i>Add FAQ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-primary">
                        <i class="bi bi-question-circle display-4"></i>
                    </div>
                    <h5 class="card-title mt-2">Total FAQs</h5>
                    <h3 class="text-primary">{{ count($faqs) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-success">
                        <i class="bi bi-check-circle display-4"></i>
                    </div>
                    <h5 class="card-title mt-2">Active FAQs</h5>
                    <h3 class="text-success">{{ collect($faqs)->where('status', 'active')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-info">
                        <i class="bi bi-eye display-4"></i>
                    </div>
                    <h5 class="card-title mt-2">Total Views</h5>
                    <h3 class="text-info">{{ collect($faqs)->sum('views') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-warning">
                        <i class="bi bi-star display-4"></i>
                    </div>
                    <h5 class="card-title mt-2">Avg Rating</h5>
                    <h3 class="text-warning">4.5/5</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">FAQ Categories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categories as $category)
                        <div class="col-md-4 mb-3">
                            <div class="category-card p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $category['name'] }}</h6>
                                        <small class="text-muted">{{ $category['count'] }} FAQs</small>
                                    </div>
                                    <div class="category-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="filterByCategory('{{ $category['id'] }}')">
                                            View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">FAQ List</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" onchange="filterFAQs(this.value)">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                            <select class="form-select form-select-sm" onchange="filterByStatus(this.value)">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Question</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($faqs as $faq)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $faq['question'] }}</div>
                                        <small class="text-muted">{{ Str::limit($faq['answer'], 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $faq['category'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $faq['status'] === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($faq['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $faq['views'] }}</td>
                                    <td>{{ $faq['updated_at'] }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editFAQ({{ $faq['id'] }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="previewFAQ({{ $faq['id'] }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteFAQ({{ $faq['id'] }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addFaqForm">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" class="form-control" placeholder="Enter the question" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <textarea class="form-control" rows="5" placeholder="Enter the answer" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keywords (for better matching)</label>
                        <input type="text" class="form-control" placeholder="e.g., enrollment, payment, course">
                        <small class="form-text text-muted">Separate multiple keywords with commas</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveFAQ()">Save FAQ</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit FAQ Modal -->
<div class="modal fade" id="editFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editFaqForm">
                    <input type="hidden" id="editFaqId">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="editCategory" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <input type="text" class="form-control" id="editQuestion" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <textarea class="form-control" id="editAnswer" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keywords</label>
                        <input type="text" class="form-control" id="editKeywords">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editStatus">
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateFAQ()">Update FAQ</button>
            </div>
        </div>
    </div>
</div>

<script>
function filterByCategory(categoryId) {
    // Filter FAQs by category
    console.log('Filter by category:', categoryId);
}

function filterFAQs(categoryId) {
    // Filter table by category
    console.log('Filter FAQs:', categoryId);
}

function filterByStatus(status) {
    // Filter table by status
    console.log('Filter by status:', status);
}

function editFAQ(faqId) {
    // Load FAQ data and show edit modal
    const faqData = @json($faqs).find(faq => faq.id === faqId);
    if (faqData) {
        document.getElementById('editFaqId').value = faqData.id;
        document.getElementById('editCategory').value = faqData.category_id;
        document.getElementById('editQuestion').value = faqData.question;
        document.getElementById('editAnswer').value = faqData.answer;
        document.getElementById('editKeywords').value = faqData.keywords;
        document.getElementById('editStatus').checked = faqData.status === 'active';
        
        const editModal = new bootstrap.Modal(document.getElementById('editFaqModal'));
        editModal.show();
    }
}

function previewFAQ(faqId) {
    // Show preview of FAQ
    const faqData = @json($faqs).find(faq => faq.id === faqId);
    if (faqData) {
        alert(`Question: ${faqData.question}\n\nAnswer: ${faqData.answer}`);
    }
}

function deleteFAQ(faqId) {
    if (confirm('Are you sure you want to delete this FAQ?')) {
        // In a real application, this would send a DELETE request
        alert('FAQ deleted successfully!');
        location.reload();
    }
}

function saveFAQ() {
    // Save new FAQ
    alert('FAQ saved successfully!');
    const addModal = bootstrap.Modal.getInstance(document.getElementById('addFaqModal'));
    addModal.hide();
    location.reload();
}

function updateFAQ() {
    // Update existing FAQ
    alert('FAQ updated successfully!');
    const editModal = bootstrap.Modal.getInstance(document.getElementById('editFaqModal'));
    editModal.hide();
    location.reload();
}
</script>
@endsection
