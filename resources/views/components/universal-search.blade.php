{{-- Enhanced Universal Search Component --}}
{{-- This component provides role-based search functionality --}}

<div class="search-container">
    <div class="search-wrapper">
        <div class="input-group">
            <span class="input-group-text search-icon">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                   id="universalSearchInput" 
                   class="form-control search-input" 
                   placeholder="@if(auth()->check())@if(auth()->user()->role === 'admin' || auth()->user()->role === 'director')Search students and professors...@elseif(auth()->user()->role === 'professor')Search students...@else Search professors...@endif @else Search... @endif"
                   autocomplete="off"
                   onkeyup="handleSearchInput()"
                   onfocus="showSearchDropdown()"
                   onblur="hideSearchDropdown()">
            
            <!-- Search Type Selector (Admin/Director only) -->
            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'director'))
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="searchTypeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span id="searchTypeLabel">All</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('all')">All</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('students')">Students Only</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('professors')">Professors Only</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('programs')">Programs Only</a></li>
                </ul>
            </div>
            @elseif(auth()->check() && auth()->user()->role === 'professor')
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="searchTypeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span id="searchTypeLabel">All</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('all')">All</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('students')">Students Only</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setSearchType('programs')">Programs Only</a></li>
                </ul>
            </div>
            @endif
            
            <!-- Advanced Search Button -->
            <button class="btn btn-outline-primary" type="button" onclick="showAdvancedSearch()">
                <i class="bi bi-funnel"></i>
            </button>
        </div>
        
        <!-- Search Results Dropdown -->
        <div id="searchResultsDropdown" class="search-dropdown" style="display: none;">
            <div class="search-dropdown-content">
                <!-- Search suggestions will be populated here -->
                <div id="searchSuggestions" class="search-suggestions">
                    <!-- Dynamic suggestions -->
                </div>
                
                <!-- Search results -->
                <div id="searchResults" class="search-results">
                    <!-- Dynamic results -->
                </div>
                
                <!-- No results message -->
                <div id="noResults" class="no-results text-center py-3" style="display: none;">
                    <i class="bi bi-search text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0">No results found</p>
                </div>
                
                <!-- Loading indicator -->
                <div id="searchLoading" class="search-loading text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <p class="text-muted mb-0 mt-2">Searching...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Search Modal -->
<div class="modal fade" id="advancedSearchModal" tabindex="-1" aria-labelledby="advancedSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advancedSearchModalLabel">
                    <i class="bi bi-funnel me-2"></i>Advanced Search
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="advancedSearchForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Search Query</label>
                                <input type="text" class="form-control" id="advancedSearchQuery" placeholder="Enter name, email, or ID">
                            </div>
                        </div>
                        
                        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'director'))
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" id="advancedSearchRole">
                                    <option value="">All Roles</option>
                                    <option value="student">Students</option>
                                    <option value="professor">Professors</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="advancedSearchStatus">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'director' || auth()->user()->role === 'professor'))
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Program</label>
                                <select class="form-select" id="advancedSearchProgram">
                                    <option value="">All Programs</option>
                                    <!-- Programs will be populated dynamically -->
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="performAdvancedSearch()">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Search Results Modal -->
<div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchResultsModalLabel">
                    <i class="bi bi-search me-2"></i>Search Results
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="searchResultsContainer">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Search Styles -->
<style>
.search-container {
    position: relative;
    max-width: 500px;
    margin: 0 auto;
}

.search-wrapper {
    position: relative;
}

.search-input {
    border-radius: 25px;
    padding-left: 50px;
    padding-right: 15px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: none;
    border: none;
    color: #6c757d;
}

.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
    margin-top: 5px;
}

.search-dropdown-content {
    padding: 10px 0;
}

.search-suggestions {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.search-suggestion-item,
.search-result-item {
    padding: 10px 15px;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-suggestion-item:hover,
.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-suggestion-item:last-child,
.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item {
    display: flex;
    align-items: center;
}

.search-result-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 15px;
    object-fit: cover;
}

.search-result-icon {
    width: 40px;
    height: 40px;
    margin-right: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
}

.search-result-info {
    flex: 1;
}

.search-result-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
}

.search-result-email {
    color: #6c757d;
    font-size: 0.9rem;
}

.search-result-role {
    background: #e9ecef;
    color: #495057;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    text-transform: capitalize;
}

.search-result-actions {
    display: flex;
    gap: 5px;
}

.search-result-actions .btn {
    padding: 5px 10px;
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .search-container {
        max-width: 100%;
    }
    
    .search-dropdown {
        left: -10px;
        right: -10px;
    }
}
</style>

<!-- Enhanced Search JavaScript -->
<script>
let searchTimeout;
let currentSearchType = 'all';
let isSearchFocused = false;

// Initialize search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load programs for advanced search
    loadPrograms();
    
    // Set up outside click handler
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            hideSearchDropdown();
        }
    });
});

// Handle search input
function handleSearchInput() {
    const query = document.getElementById('universalSearchInput').value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        hideSearchDropdown();
        return;
    }
    
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
}

// Perform search
function performSearch(query) {
    if (!query) return;
    
    showSearchLoading();
    
    const params = new URLSearchParams({
        query: query,
        type: currentSearchType,
        limit: 10
    });
    
    fetch(`/search?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            hideSearchLoading();
            if (data.success) {
                displaySearchResults(data.results);
            } else {
                showNoResults();
            }
        })
        .catch(error => {
            hideSearchLoading();
            console.error('Search error:', error);
            showNoResults();
        });
}

// Display search results
function displaySearchResults(results) {
    const container = document.getElementById('searchResults');
    
    if (results.length === 0) {
        showNoResults();
        return;
    }
    
    container.innerHTML = results.map(result => {
        if (result.type === 'program') {
            return `
                <div class="search-result-item" onclick="selectSearchResult('${result.id}', 'program')">
                    <div class="search-result-icon">
                        <i class="bi bi-collection text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="search-result-info">
                        <div class="search-result-name">${result.name}</div>
                        <div class="search-result-email">${result.description}</div>
                        <small class="text-muted">
                            ${result.modules_count} modules • ${result.courses_count} courses
                        </small>
                    </div>
                    <div class="search-result-role">
                        <span class="badge bg-info">Program</span>
                    </div>
                    <div class="search-result-actions">
                        ${generateProgramActionButtons(result)}
                    </div>
                </div>
            `;
        } else {
            const roleClass = getRoleClass(result.role);
            const actions = generateActionButtons(result);
            
            return `
                <div class="search-result-item" onclick="selectSearchResult('${result.id}', '${result.type}')">
                    <img src="${result.avatar || '/images/default-avatar.png'}" alt="${result.name}" class="search-result-avatar">
                    <div class="search-result-info">
                        <div class="search-result-name">${result.name}</div>
                        <div class="search-result-email">${result.email}</div>
                        ${result.programs && result.programs.length > 0 ? 
                            `<small class="text-muted">Programs: ${result.programs.join(', ')}</small>` : ''}
                    </div>
                    <div class="search-result-role">
                        <span class="badge bg-${roleClass}">${result.role}</span>
                        <br><small class="text-muted">${result.status}</small>
                    </div>
                    <div class="search-result-actions">
                        ${actions}
                    </div>
                </div>
            `;
        }
    }).join('');
    
    showSearchDropdown();
}

// Generate action buttons based on user role
function generateActionButtons(result) {
    const currentUser = @json(auth()->user());
    let actions = [];
    
    // View profile action
    actions.push(`<button class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); viewProfile('${result.id}', '${result.type}')">
        <i class="bi bi-person"></i>
    </button>`);
    
    // Chat action
    actions.push(`<button class="btn btn-outline-success btn-sm" onclick="event.stopPropagation(); startChat('${result.id}')">
        <i class="bi bi-chat"></i>
    </button>`);
    
    // Admin actions
    if (currentUser && (currentUser.role === 'admin' || currentUser.role === 'director')) {
        actions.push(`<button class="btn btn-outline-info btn-sm" onclick="event.stopPropagation(); viewDetails('${result.id}', '${result.type}')">
            <i class="bi bi-info-circle"></i>
        </button>`);
    }
    
    return actions.join('');
}

// Generate action buttons for programs
function generateProgramActionButtons(result) {
    let actions = [];
    
    // View program details
    actions.push(`<button class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); viewProgram('${result.id}')">
        <i class="bi bi-eye"></i>
    </button>`);
    
    // View modules/courses (redirect to program profile)
    actions.push(`<button class="btn btn-outline-info btn-sm" onclick="event.stopPropagation(); window.location.href='/profile/program/${result.id}'">
        <i class="bi bi-list-ul"></i>
    </button>`);
    
    return actions.join('');
}

// Get role class for badge styling
function getRoleClass(role) {
    switch(role.toLowerCase()) {
        case 'student': return 'primary';
        case 'professor': return 'success';
        case 'admin': return 'warning';
        case 'director': return 'danger';
        default: return 'secondary';
    }
}

// Select search result
function selectSearchResult(id, type) {
    hideSearchDropdown();
    
    if (type === 'program') {
        window.location.href = `/profile/program/${id}`;
    } else if (type === 'student') {
        window.location.href = `/profile/user/${id}`;
    } else if (type === 'professor') {
        window.location.href = `/profile/professor/${id}`;
    } else {
        // For other user types (admin, director), use the existing modal
        showUserModal(id);
    }
}

// View profile
function viewProfile(id, type) {
    if (type === 'program') {
        window.location.href = `/profile/program/${id}`;
    } else if (type === 'student') {
        window.location.href = `/profile/user/${id}`;
    } else if (type === 'professor') {
        window.location.href = `/profile/professor/${id}`;
    } else {
        // For other user types (admin, director), use the existing modal
        showUserModal(id);
    }
}

// Show user profile modal
function showUserModal(userId) {
    fetch(`/search/profile?user_id=${userId}&type=user`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUserProfileModal(data.profile);
            } else {
                alert('Failed to load user profile');
            }
        })
        .catch(error => {
            console.error('Error loading user profile:', error);
            alert('Failed to load user profile');
        });
}

// Redirect to program profile page
function showProgramModal(programId) {
    window.location.href = `/profile/program/${programId}`;
}

// Display user profile modal
function displayUserProfileModal(profile) {
    const modalContent = `
        <div class="modal fade" id="userProfileModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-person-circle me-2"></i>User Profile
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="${profile.avatar || '/images/default-avatar.png'}" 
                                     alt="${profile.name}" 
                                     class="rounded-circle mb-3" 
                                     width="120" height="120">
                                <h5>${profile.name}</h5>
                                <span class="badge bg-${getRoleClass(profile.role)} mb-2">${profile.role}</span>
                                <p class="text-muted">${profile.status}</p>
                            </div>
                            <div class="col-md-8">
                                <h6>Contact Information</h6>
                                <p><strong>Email:</strong> ${profile.email}</p>
                                <p><strong>Joined:</strong> ${new Date(profile.created_at).toLocaleDateString()}</p>
                                
                                ${profile.enrollments ? `
                                    <h6 class="mt-4">Program Enrollments</h6>
                                    <div class="list-group">
                                        ${profile.enrollments.map(enrollment => `
                                            <div class="list-group-item">
                                                <strong>${enrollment.program}</strong>
                                                <br><small class="text-muted">Enrolled: ${new Date(enrollment.enrolled_at).toLocaleDateString()}</small>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="startChat('${profile.id}')">
                            <i class="bi bi-chat me-2"></i>Start Chat
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('userProfileModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal to body
    document.body.insertAdjacentHTML('beforeend', modalContent);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('userProfileModal'));
    modal.show();
}

// View program (redirect to program page)
function viewProgram(programId) {
    window.location.href = `/profile/program/${programId}`;
}

// Start chat
function startChat(userId) {
    // Implementation depends on your chat system
    if (typeof selectUser === 'function') {
        selectUser(userId);
    } else {
        // Fallback: open chat in new window
        window.open(`/chat/${userId}`, '_blank');
    }
}

// View details
function viewDetails(userId) {
    window.open(`/admin/users/${userId}`, '_blank');
}

// Set search type
function setSearchType(type) {
    currentSearchType = type;
    const labels = {
        'all': 'All',
        'students': 'Students',
        'professors': 'Professors',
        'programs': 'Programs'
    };
    
    document.getElementById('searchTypeLabel').textContent = labels[type] || 'All';
    
    // Re-perform search if there's a query
    const query = document.getElementById('universalSearchInput').value.trim();
    if (query.length >= 2) {
        performSearch(query);
    }
}

// Show/hide search dropdown
function showSearchDropdown() {
    isSearchFocused = true;
    document.getElementById('searchResultsDropdown').style.display = 'block';
}

function hideSearchDropdown() {
    setTimeout(() => {
        if (!isSearchFocused) {
            document.getElementById('searchResultsDropdown').style.display = 'none';
        }
        isSearchFocused = false;
    }, 200);
}

// Show loading
function showSearchLoading() {
    document.getElementById('searchLoading').style.display = 'block';
    document.getElementById('searchResults').innerHTML = '';
    document.getElementById('noResults').style.display = 'none';
}

// Hide loading
function hideSearchLoading() {
    document.getElementById('searchLoading').style.display = 'none';
}

// Show no results
function showNoResults() {
    document.getElementById('noResults').style.display = 'block';
    document.getElementById('searchResults').innerHTML = '';
    showSearchDropdown();
}

// Advanced search
function showAdvancedSearch() {
    $('#advancedSearchModal').modal('show');
}

function performAdvancedSearch() {
    const query = document.getElementById('advancedSearchQuery').value.trim();
    const role = document.getElementById('advancedSearchRole').value;
    const status = document.getElementById('advancedSearchStatus').value;
    const program = document.getElementById('advancedSearchProgram').value;
    
    const params = new URLSearchParams({
        query: query,
        role: role,
        status: status,
        program: program,
        limit: 50
    });
    
    fetch(`/search/advanced?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAdvancedResults(data.results);
                $('#advancedSearchModal').modal('hide');
                $('#searchResultsModal').modal('show');
            } else {
                alert('Search failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Advanced search error:', error);
            alert('Search failed. Please try again.');
        });
}

// Display advanced search results
function displayAdvancedResults(results) {
    const container = document.getElementById('searchResultsContainer');
    
    if (results.length === 0) {
        container.innerHTML = '<div class="text-center py-4"><p class="text-muted">No results found.</p></div>';
        return;
    }
    
    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Additional Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${results.map(result => `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${result.avatar}" alt="${result.name}" class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <div class="fw-bold">${result.name}</div>
                                        <small class="text-muted">${result.email}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-${result.role === 'student' ? 'primary' : 'success'}">${result.role}</span>
                            </td>
                            <td>
                                <span class="badge bg-${result.status === 'active' ? 'success' : 'secondary'}">${result.status}</span>
                            </td>
                            <td>
                                ${result.enrollment_info ? result.enrollment_info.map(e => `<small class="d-block">${e.program} (${e.batch})</small>`).join('') : ''}
                                ${result.programs ? result.programs.map(p => `<small class="d-block">${p.name}</small>`).join('') : ''}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewProfile(${result.id}, '${result.role}')">
                                        <i class="bi bi-person"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="startChat(${result.id})">
                                        <i class="bi bi-chat"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Load programs for advanced search
function loadPrograms() {
    fetch('/api/programs')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('advancedSearchProgram');
                data.programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.id;
                    option.textContent = program.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading programs:', error);
        });
}
</script>
