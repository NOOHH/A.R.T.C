<!-- Student Search Component -->
<div class="position-relative" id="studentSearchContainer">
    <div class="input-group">
        <span class="input-group-text bg-primary text-white">
            <i class="fas fa-search"></i>
        </span>
        <input 
            type="text" 
            class="form-control" 
            id="studentSearchInput" 
            placeholder="Search programs, modules, courses, and your professors..."
            autocomplete="off"
        >
    </div>
    
    <!-- Search Results Dropdown -->
    <div class="search-dropdown position-absolute w-100 mt-1 bg-white border border-secondary rounded shadow-lg" 
         id="studentSearchResults" 
         style="display: none; z-index: 1050; max-height: 400px; overflow-y: auto;">
        
        <!-- Results will be populated here -->
        <div id="studentSearchResultsList"></div>
        
        <!-- No Results Message -->
        <div id="studentNoResults" class="p-3 text-center text-muted" style="display: none;">
            <i class="fas fa-info-circle mb-2"></i>
            <p class="mb-0">No results found.</p>
            <small>Search for any programs, modules, courses, or your enrolled professors.</small>
        </div>
    </div>
</div>

<!-- Professor Details Modal -->
<div class="modal fade" id="studentProfessorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Professor Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentProfessorModalBody">
                <!-- Professor details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
.search-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    background: white;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.search-result-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.search-result-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}

.program-icon {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.professor-icon {
    background: linear-gradient(45deg, #28a745, #1e7e34);
}

.badge-count {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearchInput');
    const searchResults = document.getElementById('studentSearchResults');
    const resultsList = document.getElementById('studentSearchResultsList');
    const noResults = document.getElementById('studentNoResults');
    let searchTimeout;

    // Search input handler
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            hideSearchResults();
            return;
        }

        searchTimeout = setTimeout(() => {
            performStudentSearch(query);
        }, 300);
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!document.getElementById('studentSearchContainer').contains(e.target)) {
            hideSearchResults();
        }
    });

    // Perform search
    function performStudentSearch(query) {
        console.log('Student search: Starting search for query:', query);
        
        fetch(`/search?query=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin' // Include cookies for session authentication
        })
        .then(response => {
            console.log('Student search: Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Student search: Response data:', data);
            
            if (data.success) {
                console.log('Student search: Success - found', data.results.length, 'results');
                displayStudentSearchResults(data.results);
            } else {
                console.log('Student search: Failed - showing no results');
                showNoResults();
            }
        })
        .catch(error => {
            console.error('Student search: Error occurred:', error);
            showNoResults();
        });
    }

    // Display search results
    function displayStudentSearchResults(results) {
        console.log('Student search: Displaying results:', results);
        resultsList.innerHTML = '';
        
        if (results.length === 0) {
            console.log('Student search: No results to display');
            showNoResults();
            return;
        }

        console.log('Student search: Creating result items for', results.length, 'results');
        results.forEach((result, index) => {
            console.log('Student search: Creating item', index, 'for result:', result);
            const item = createStudentResultItem(result);
            resultsList.appendChild(item);
        });

        noResults.style.display = 'none';
        searchResults.style.display = 'block';
        console.log('Student search: Results displayed successfully');
    }

    // Create result item
    function createStudentResultItem(result) {
        const item = document.createElement('div');
        item.className = 'search-result-item d-flex align-items-center';
        
        if (result.type === 'program') {
            const statusClass = result.is_enrolled ? 'success' : 'primary';
            const statusIcon = result.is_enrolled ? 'check-circle' : 'plus-circle';
            
            item.innerHTML = `
                <div class="search-result-icon program-icon me-3">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-primary d-flex align-items-center">
                        ${result.name}
                        <span class="badge bg-${statusClass} ms-2">
                            <i class="fas fa-${statusIcon} me-1"></i>${result.role}
                        </span>
                    </div>
                    <div class="text-muted small">${result.description || 'No description available'}</div>
                    <div class="d-flex gap-2 mt-1">
                        <span class="badge bg-info badge-count">
                            <i class="fas fa-cube me-1"></i>${result.modules_count || 0} Modules
                        </span>
                        <span class="badge bg-secondary badge-count">
                            <i class="fas fa-book me-1"></i>${result.courses_count || 0} Courses
                        </span>
                    </div>
                </div>
                <div class="text-muted">
                    <i class="fas fa-info-circle"></i>
                </div>
            `;
            item.addEventListener('click', () => showStudentProgramDetails(result));
        } else if (result.type === 'professor') {
            item.innerHTML = `
                <div class="search-result-icon professor-icon me-3">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-success">${result.name}</div>
                    <div class="text-muted small">${result.role}</div>
                    <div class="text-muted small">${result.description || result.email}</div>
                </div>
                <div class="text-muted">
                    <i class="fas fa-user-circle"></i>
                </div>
            `;
            item.addEventListener('click', () => showStudentProfessorDetails(result));
        }

        return item;
    }

    // Show program details
    function showStudentProgramDetails(program) {
        window.location.href = `/profile/program/${program.id}`;
    }

    // Show professor details
    function showStudentProfessorDetails(professor) {
        hideSearchResults();
        
        const modalBody = document.getElementById('studentProfessorModalBody');
        modalBody.innerHTML = `
            <div class="text-center mb-4">
                <div class="professor-icon d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px; font-size: 32px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h4 class="text-success">${professor.name}</h4>
                <p class="text-muted">${professor.email}</p>
                <span class="badge ${professor.status === 'Online' ? 'bg-success' : 'bg-secondary'}">
                    ${professor.status}
                </span>
            </div>

            ${professor.programs && professor.programs.length > 0 ? `
                <div class="mb-3">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Teaching Your Programs
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        ${professor.programs.map(prog => `
                            <span class="badge bg-primary">${prog}</span>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            <div class="alert alert-success">
                <i class="fas fa-info-circle me-2"></i>
                This professor teaches in your enrolled programs. You can contact them through the messaging system.
            </div>
            
            <div class="text-center">
                <a href="/profile/user/${professor.id}" class="btn btn-success" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>View Full Professor Profile
                </a>
            </div>
        `;

        new bootstrap.Modal(document.getElementById('studentProfessorModal')).show();
    }

    // Utility functions
    function showNoResults() {
        resultsList.innerHTML = '';
        noResults.style.display = 'block';
        searchResults.style.display = 'block';
    }

    function hideSearchResults() {
        searchResults.style.display = 'none';
    }
});
</script>
