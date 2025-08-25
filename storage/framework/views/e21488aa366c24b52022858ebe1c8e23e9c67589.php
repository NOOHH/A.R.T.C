<!-- Professional Student Search Component -->
<div class="search-container" id="studentSearchContainer">
    <div class="search-input-wrapper">
        <div class="search-icon">
            <i class="bi bi-search"></i>
        </div>
        <input 
            type="text" 
            class="search-input" 
            id="studentSearchInput" 
            placeholder="Search programs, modules, courses..."
            autocomplete="off"
        >
        <div class="search-actions">
            <button class="search-clear-btn" id="searchClearBtn" style="display: none;">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>
    
    <!-- Search Results Dropdown -->
    <div class="search-results-dropdown" 
         id="studentSearchResults" 
         style="display: none;">
        
        <!-- Results will be populated here -->
        <div id="studentSearchResultsList"></div>
        
        <!-- No Results Message -->
        <div id="studentNoResults" class="no-results-message" style="display: none;">
            <div class="no-results-icon">
                <i class="bi bi-search"></i>
            </div>
            <div class="no-results-content">
                <h6>No results found</h6>
                <p>Try searching for programs, modules, courses, or professors</p>
            </div>
        </div>
    </div>
</div>

<style>
.search-container {
    position: relative;
    width: 100%;
    max-width: 500px;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.search-input-wrapper:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-1px);
}

.search-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    color: #9ca3af;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.95rem;
    color: #374151;
    font-weight: 500;
    min-width: 0;
}

.search-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.search-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border: none;
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.search-clear-btn:hover {
    background: #e5e7eb;
    color: #374151;
}

/* Search Results Dropdown */
.search-results-dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1050;
    backdrop-filter: blur(10px);
}

.search-result-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f9fafb;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}

.search-result-item:hover {
    background: #f8fafc;
    transform: translateX(2px);
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.program-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.professor-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.search-result-content {
    flex: 1;
    min-width: 0;
}

.search-result-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-result-subtitle {
    color: #6b7280;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.search-result-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.search-result-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #f3f4f6;
    color: #374151;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.search-result-badge.primary {
    background: #dbeafe;
    color: #1d4ed8;
}

.search-result-badge.success {
    background: #d1fae5;
    color: #059669;
}

.search-result-badge.info {
    background: #e0f2fe;
    color: #0284c7;
}

.search-result-badge.secondary {
    background: #f3f4f6;
    color: #6b7280;
}

.search-result-arrow {
    color: #9ca3af;
    font-size: 1.1rem;
    margin-left: 0.5rem;
    flex-shrink: 0;
}

/* No Results Message */
.no-results-message {
    padding: 2rem 1.5rem;
    text-align: center;
}

.no-results-icon {
    width: 64px;
    height: 64px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: #9ca3af;
    font-size: 1.5rem;
}

.no-results-content h6 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.no-results-content p {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
}

/* Scrollbar Styling */
.search-results-dropdown::-webkit-scrollbar {
    width: 6px;
}

.search-results-dropdown::-webkit-scrollbar-track {
    background: #f9fafb;
    border-radius: 3px;
}

.search-results-dropdown::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.search-results-dropdown::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-container {
        max-width: 100%;
    }
    
    .search-input-wrapper {
        padding: 0.625rem 0.875rem;
    }
    
    .search-result-item {
        padding: 0.875rem;
    }
    
    .search-result-icon {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearchInput');
    const searchResults = document.getElementById('studentSearchResults');
    const resultsList = document.getElementById('studentSearchResultsList');
    const noResults = document.getElementById('studentNoResults');
    const clearBtn = document.getElementById('searchClearBtn');
    let searchTimeout;

    // Search input handler
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        // Show/hide clear button
        clearBtn.style.display = query.length > 0 ? 'flex' : 'none';
        
        if (query.length < 2) {
            hideSearchResults();
            return;
        }

        searchTimeout = setTimeout(() => {
            performStudentSearch(query);
        }, 300);
    });

    // Clear search
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
        clearBtn.style.display = 'none';
        hideSearchResults();
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
        
        fetch(`/student/search?query=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
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
        item.className = 'search-result-item';
        
        if (result.type === 'program') {
            const statusClass = result.is_enrolled ? 'success' : 'primary';
            const statusIcon = result.is_enrolled ? 'check-circle' : 'plus-circle';
            
            item.innerHTML = `
                <div class="search-result-icon program-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div class="search-result-content">
                    <div class="search-result-title">
                        ${result.name}
                        <span class="search-result-badge ${statusClass}">
                            <i class="bi bi-${statusIcon}"></i>
                            ${result.role}
                        </span>
                    </div>
                    <div class="search-result-subtitle">${result.description || 'No description available'}</div>
                    <div class="search-result-badges">
                        <span class="search-result-badge info">
                            <i class="bi bi-collection"></i>
                            ${result.modules_count || 0} Modules
                        </span>
                        <span class="search-result-badge secondary">
                            <i class="bi bi-book"></i>
                            ${result.courses_count || 0} Courses
                        </span>
                    </div>
                </div>
                <div class="search-result-arrow">
                    <i class="bi bi-arrow-right"></i>
                </div>
            `;
            item.addEventListener('click', () => showStudentProgramDetails(result));
        } else if (result.type === 'professor') {
            item.innerHTML = `
                <div class="search-result-icon professor-icon">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="search-result-content">
                    <div class="search-result-title">${result.name}</div>
                    <div class="search-result-subtitle">${result.role}</div>
                    <div class="search-result-subtitle">${result.description || result.email}</div>
                </div>
                <div class="search-result-arrow">
                    <i class="bi bi-arrow-right"></i>
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
        window.location.href = `/profile/professor/${professor.id}`;
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
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/components/student-search.blade.php ENDPATH**/ ?>