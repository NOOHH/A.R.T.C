


<div class="search-container">
    <div class="search-wrapper">
        <div class="input-group">
            <span class="input-group-text search-icon">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                   id="universalSearchInput" 
                   class="form-control search-input" 
                   placeholder="<?php if(auth()->check()): ?><?php if(auth()->user()->role === 'admin' || auth()->user()->role === 'director'): ?>Search students and professors...<?php elseif(auth()->user()->role === 'professor'): ?>Search students...<?php else: ?> Search professors...<?php endif; ?> <?php else: ?> Search... <?php endif; ?>"
                   autocomplete="off"
                   onkeyup="handleSearchInput()"
                   onfocus="showSearchDropdown()"
                   onblur="hideSearchDropdown()">
            
            <!-- Search Type Selector (Admin/Director only) -->
            <?php if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'director')): ?>
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
            <?php elseif(auth()->check() && auth()->user()->role === 'professor'): ?>
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
            <?php endif; ?>
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
    // Set up outside click handler
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            hideSearchDropdown();
        }
    });
    
    // Set up event delegation for search result clicks
    const searchResults = document.getElementById('searchResults');
    if (searchResults) {
        searchResults.addEventListener('click', function(e) {
            const resultItem = e.target.closest('.search-result-item');
            if (resultItem) {
                e.preventDefault();
                e.stopPropagation();
                
                const url = resultItem.dataset.url;
                const id = resultItem.dataset.id;
                const type = resultItem.dataset.type;
                
                hideSearchDropdown();
                
                if (url) {
                    // Direct URL navigation
                    console.log('Navigating to:', url);
                    window.location.href = url;
                } else if (id && type) {
                    // Fallback using selectSearchResult function
                    console.log('Using selectSearchResult for:', id, type);
                    selectSearchResult(id, type);
                }
            }
        });
    }
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
    
    fetch(`/search-now?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
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
                <div class="search-result-item" data-url="${result.url || '/profile/program/' + result.id}" style="cursor: pointer;">
                    <div class="search-result-icon">
                        <i class="bi bi-collection text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="search-result-info">
                        <div class="search-result-name">${result.name || 'Unknown Program'}</div>
                        <div class="search-result-email">${result.description || ''}</div>
                        <small class="text-muted">Program</small>
                    </div>
                    <div class="search-result-role">
                        <span class="badge bg-info">Program</span>
                    </div>
                </div>
            `;
        } else if (result.type === 'student') {
            return `
                <div class="search-result-item" data-url="${result.url || '/profile/user/' + result.id}" style="cursor: pointer;">
                    <img src="${result.avatar || '/images/default-avatar.png'}" alt="${result.name || 'Student'}" class="search-result-avatar">
                    <div class="search-result-info">
                        <div class="search-result-name">${result.name || 'Unknown Student'}</div>
                        <div class="search-result-email">${result.email || ''}</div>
                        <small class="text-muted">Student</small>
                    </div>
                    <div class="search-result-role">
                        <span class="badge bg-success">Student</span>
                    </div>
                </div>
            `;
        } else if (result.type === 'professor') {
            return `
                <div class="search-result-item" data-url="${result.url || '/profile/professor/' + result.id}" style="cursor: pointer;">
                    <img src="${result.avatar || '/images/default-avatar.png'}" alt="${result.name || 'Professor'}" class="search-result-avatar">
                    <div class="search-result-info">
                        <div class="search-result-name">${result.name || 'Unknown Professor'}</div>
                        <div class="search-result-email">${result.email || ''}</div>
                        <small class="text-muted">Professor</small>
                    </div>
                    <div class="search-result-role">
                        <span class="badge bg-warning">Professor</span>
                    </div>
                </div>
            `;
        } else {
            // Fallback for other types
            return `
                <div class="search-result-item" data-id="${result.id}" data-type="${result.type}" style="cursor: pointer;">
                    <img src="${result.avatar || '/images/default-avatar.png'}" alt="${result.name || 'User'}" class="search-result-avatar">
                    <div class="search-result-info">
                        <div class="search-result-name">${result.name || 'Unknown User'}</div>
                        <div class="search-result-email">${result.email || ''}</div>
                        <small class="text-muted">${result.type || 'User'}</small>
                    </div>
                    <div class="search-result-role">
                        <span class="badge bg-secondary">${result.type || 'User'}</span>
                    </div>
                </div>
            `;
        }
    }).join('');
    
    showSearchDropdown();
}

// Generate action buttons based on user role
function generateActionButtons(result) {
    // Try multiple ways to get current user info:
    // 1. Laravel auth user
    // 2. Session data
    // 3. Global JavaScript variables
    const currentUser = <?php echo json_encode(auth()->user(), 15, 512) ?> || {
        id: <?php echo json_encode(session('user_id') ?: session('professor_id'), 15, 512) ?> || (typeof window !== 'undefined' && window.myId),
        role: <?php echo json_encode(session('user_role') ?: session('user_type'), 15, 512) ?> || (typeof window !== 'undefined' && window.userRole),
        name: <?php echo json_encode(session('user_name'), 15, 512) ?> || (typeof window !== 'undefined' && window.myName)
    };
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

// Show result modal
function showResultModal(result) {
    const modalHtml = `
        <div class="modal fade" id="searchResultModal" tabindex="-1" aria-labelledby="searchResultModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchResultModalLabel">
                            <i class="bi bi-${getTypeIcon(result.type)} me-2"></i>${result.name || 'Unknown'}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="result-icon mb-3">
                                    <i class="bi bi-${getTypeIcon(result.type)}" style="font-size: 4rem; color: ${getTypeColor(result.type)};"></i>
                                </div>
                                <span class="badge bg-${getTypeBadgeColor(result.type)} fs-6">${result.type || 'User'}</span>
                            </div>
                            <div class="col-md-9">
                                <h4 class="mb-3">${result.name || 'Unknown'}</h4>
                                ${result.email ? `<p><strong>Email:</strong> ${result.email}</p>` : ''}
                                ${result.description ? `<p><strong>Description:</strong> ${result.description}</p>` : ''}
                                <p><strong>ID:</strong> ${result.id || 'N/A'}</p>
                                
                                ${getResultActions(result)}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        ${getModalFooterActions(result)}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('searchResultModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('searchResultModal'));
    modal.show();
    
    // Remove modal from DOM when hidden
    document.getElementById('searchResultModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Get type icon
function getTypeIcon(type) {
    switch(type) {
        case 'student': return 'person-fill';
        case 'professor': return 'person-badge-fill';
        case 'program': return 'collection-fill';
        case 'admin': return 'shield-fill';
        case 'director': return 'star-fill';
        default: return 'person-fill';
    }
}

// Get type color
function getTypeColor(type) {
    switch(type) {
        case 'student': return '#28a745';
        case 'professor': return '#fd7e14';
        case 'program': return '#007bff';
        case 'admin': return '#ffc107';
        case 'director': return '#dc3545';
        default: return '#6c757d';
    }
}

// Get type badge color
function getTypeBadgeColor(type) {
    switch(type) {
        case 'student': return 'success';
        case 'professor': return 'warning';
        case 'program': return 'info';
        case 'admin': return 'primary';
        case 'director': return 'danger';
        default: return 'secondary';
    }
}

// Get result actions
function getResultActions(result) {
    let actions = [];
    
    if (result.type === 'student' || result.type === 'professor') {
        actions.push(`
            <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-3">
                <button class="btn btn-outline-primary btn-sm" onclick="startChat('${result.id}')">
                    <i class="bi bi-chat-dots me-1"></i>Start Chat
                </button>
                <button class="btn btn-outline-info btn-sm" onclick="viewDetails('${result.id}', '${result.type}')">
                    <i class="bi bi-info-circle me-1"></i>View Details
                </button>
            </div>
        `);
    } else if (result.type === 'program') {
        actions.push(`
            <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-3">
                <button class="btn btn-outline-info btn-sm" onclick="viewProgramModules('${result.id}')">
                    <i class="bi bi-list-ul me-1"></i>View Modules
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="viewProgramStudents('${result.id}')">
                    <i class="bi bi-people me-1"></i>View Students
                </button>
            </div>
        `);
    }
    
    return actions.join('');
}

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




</script>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\components\universal-search.blade.php ENDPATH**/ ?>