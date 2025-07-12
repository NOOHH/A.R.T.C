<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">

    {{-- Global UI Styles --}}
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}
    
    @yield('head')
    @stack('styles')
</head>
<body>
<div class="admin-container">
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <a href="{{ route('home') }}" class="brand-link">
                <img src="{{ App\Helpers\UIHelper::getGlobalLogo() }}" alt="Logo">
                <div class="brand-text">Ascendo Review<br>and Training Center</div>
            </a>
        </div>
        
        <!-- Search Bar in Header -->
        <div class="header-search">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" 
                       id="universalSearchInput" 
                       class="form-control search-input" 
                       placeholder="Search students, professors, programs..." 
                       autocomplete="off"
                       onkeyup="handleSearchInput()"
                       onfocus="showSearchDropdown()"
                       onblur="hideSearchDropdown()">
                <button class="search-btn" type="button" onclick="performSearch()">🔍</button>
            </div>
            
            <!-- Search Results Dropdown -->
            <div id="searchResultsDropdown" class="search-dropdown" style="display: none;">
                <div class="search-dropdown-content">
                    <!-- Search suggestions -->
                    <div id="searchSuggestions" class="search-suggestions">
                        <!-- Dynamic suggestions -->
                    </div>
                    
                    <!-- Search results -->
                    <div id="searchResults" class="search-results">
                        <!-- Dynamic results -->
                    </div>
                    
                    <!-- Loading indicator -->
                    <div id="searchLoading" class="search-loading d-none">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2">Searching...</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <span class="notification-icon chat-trigger" data-bs-toggle="offcanvas" data-bs-target="#chatOffcanvas" aria-label="Open chat">
                <i class="bi bi-chat-dots"></i>
            </span>
            <span class="profile-icon">👤</span>
        </div>
    </header>

            <!-- Main Content -->
            <div class="main-content">
                

    <div class="main-wrapper">
        <div class="content-below-search">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav>
                    <ul>
                        {{-- Dashboard --}}
                        <li class="@if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                                <span class="icon">📊</span> Dashboard
                            </a>
                        </li>

                        {{-- Student Registration --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">👥</span> Student Registration
                                <span class="chevron">▼</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li class="@if(Route::currentRouteName() === 'admin.student.registration.pending') active @endif">
                                    <a href="{{ route('admin.student.registration.pending') }}">Pending</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.student.registration.history') active @endif">
                                    <a href="{{ route('admin.student.registration.history') }}">History</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.student.registration.payment.pending') active @endif">
                                    <a href="{{ route('admin.student.registration.payment.pending') }}">Payment Pending</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.student.registration.payment.history') active @endif">
                                    <a href="{{ route('admin.student.registration.payment.history') }}">Payment History</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Students List --}}
                        <li class="@if(Route::currentRouteName() === 'admin.students.index') active @endif">
                            <a href="{{ route('admin.students.index') }}" class="sidebar-link">
                                <span class="icon">📋</span> List of Students
                            </a>
                        </li>

                        {{-- Student Enroll Dropdown --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.batches') || Route::currentRouteName() === 'admin.enrollments.index') active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">📝</span> Student Enroll
                                <span class="chevron">▼</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.batches')) active @endif">
                                    <a href="{{ route('admin.batches.index') }}">Batch Enroll</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.enrollments.index') active @endif">
                                    <a href="{{ route('admin.enrollments.index') }}">Assign Course to Student</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Directors --}}
                        <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.directors')) active @endif">
                            <a href="{{ route('admin.directors.index') }}" class="sidebar-link">
                                <span class="icon">👨‍💼</span> Directors
                            </a>
                        </li>

                        {{-- Programs & Packages Dropdown --}}
                        <li class="dropdown-sidebar @if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index') active @endif">
                            <a href="#" class="sidebar-link">
                                <span class="icon">🎓</span> Programs
                                <span class="chevron">▼</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li class="@if(Route::currentRouteName() === 'admin.programs.index') active @endif">
                                    <a href="{{ route('admin.programs.index') }}">Manage Programs</a>
                                </li>
                                <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.modules')) active @endif">
                                    <a href="{{ route('admin.modules.index') }}">Manage Modules</a>
                                </li>
                                <li class="@if(Route::currentRouteName() === 'admin.packages.index') active @endif">
                                    <a href="{{ route('admin.packages.index') }}">Packages</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Professors --}}
                        <li class="@if(str_starts_with(Route::currentRouteName(), 'admin.professors')) active @endif">
                            <a href="{{ route('admin.professors.index') }}" class="sidebar-link">
                                <span class="icon">👨‍🏫</span> Professors
                            </a>
                        </li>

                        {{-- Analytics --}}
                        <li class="@if(Route::currentRouteName() === 'admin.analytics.index') active @endif">
                            <a href="{{ route('admin.analytics.index') }}" class="sidebar-link">
                                <span class="icon">📈</span> Analytics
                            </a>
                        </li>
                        
                        {{-- Chat Management --}}
                        <li class="@if(Route::currentRouteName() === 'admin.chat.index') active @endif">
                            <a href="{{ route('admin.chat.index', ['default' => 'true']) }}" class="sidebar-link">
                                <span class="icon">💬</span> Chat Logs
                            </a>
                        </li>
                        
                        {{-- FAQ Management --}}
                        <li class="@if(Route::currentRouteName() === 'admin.faq.index') active @endif">
                            <a href="{{ route('admin.faq.index') }}" class="sidebar-link">
                                <span class="icon">❓</span> FAQ Management
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Bottom section -->
                <div class="sidebar-footer">
                    <ul class="bottom-links">
                        <li class="help-link"><span class="icon">❓</span> Help</li>
                        <li class="settings-link">
                            <a href="{{ route('admin.settings.index') }}" class="sidebar-link">
                                <span class="icon">⚙️</span> Settings
                            </a>
                        </li>
                        <li class="logout" onclick="handleAdminLogout();" style="cursor: pointer;">
                            <span class="icon">🚪</span> Logout
                        </li>
                    </ul>
                </div>
            </aside>




















                <div class="content-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="admin-logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@yield('scripts')
<script>
function handleAdminLogout() {
    if (confirm('Are you sure you want to logout?')) {
        // Submit the form to properly log out and clear session
        document.getElementById('admin-logout-form').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-open dropdowns that are marked as active
    document.querySelectorAll('.dropdown-sidebar.active').forEach(dropdown => {
        dropdown.classList.add('active');
    });

    // Toggle dropdowns
    document.querySelectorAll('.dropdown-sidebar > a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            link.parentElement.classList.toggle('active');
        });
    });

    // Hover/click animations
    function addAnimatedEvents(el, hoverBg, hoverColor) {
        if (!el) return;
        el.addEventListener('mouseenter', () => {
            el.style.background = hoverBg;
            el.style.color = hoverColor;
            el.style.transform = 'scale(1.05)';
        });
        el.addEventListener('mouseleave', () => {
            el.style.background = '';
            el.style.color = '';
            el.style.transform = 'scale(1)';
        });
        el.addEventListener('mousedown', () => el.style.transform = 'scale(0.95)');
        el.addEventListener('mouseup',   () => el.style.transform = 'scale(1.05)');
    }
    addAnimatedEvents(document.querySelector('.help-link'),    '#f1c40f', '#fff');
    addAnimatedEvents(document.querySelector('.settings-link'),'#8e44ad', '#fff');
    addAnimatedEvents(document.querySelector('.logout'),       '#e74c3c', '#fff');

    // Settings navigation
    document.querySelector('.settings-link')?.addEventListener('click', () => {
        window.location.href = '{{ route("admin.settings.index") }}';
    });

    // Logout is handled by handleAdminLogout() function, no additional handler needed here
});
</script>

<!-- Enhanced Search JavaScript -->
<script>
// Search functionality
let searchTimeout;
let currentSearchType = 'all';

function handleSearchInput() {
    const searchInput = document.getElementById('universalSearchInput');
    const query = searchInput.value.trim();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // If query is empty, hide dropdown
    if (query.length === 0) {
        hideSearchDropdown();
        return;
    }
    
    // Show loading and perform search after delay
    showSearchLoading(true);
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
}

function performSearch(query = null) {
    const searchInput = document.getElementById('universalSearchInput');
    const searchQuery = query || searchInput.value.trim();
    
    if (searchQuery.length === 0) {
        hideSearchDropdown();
        return;
    }
    
    showSearchLoading(true);
    
    // Make API call to search
    fetch('/api/admin/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            query: searchQuery,
            type: currentSearchType
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Search request failed');
        }
        return response.json();
    })
    .then(data => {
        displaySearchResults(data);
        showSearchLoading(false);
    })
    .catch(error => {
        console.error('Search error:', error);
        showSearchLoading(false);
        // Display error message
        const resultsContainer = document.getElementById('searchResults');
        if (resultsContainer) {
            resultsContainer.innerHTML = `
                <div class="no-results">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Search temporarily unavailable
                </div>
            `;
        }
        showSearchDropdown();
    });
}

function displaySearchResults(data) {
    const resultsContainer = document.getElementById('searchResults');
    const suggestionsContainer = document.getElementById('searchSuggestions');
    
    // Clear previous results
    resultsContainer.innerHTML = '';
    suggestionsContainer.innerHTML = '';
    
    // Show suggestions
    if (data.suggestions && data.suggestions.length > 0) {
        suggestionsContainer.innerHTML = `
            <div class="suggestions-header">Suggestions</div>
            ${data.suggestions.map(suggestion => `
                <div class="suggestion-item" onclick="selectSuggestion('${suggestion}')">
                    <i class="bi bi-search me-2"></i>${suggestion}
                </div>
            `).join('')}
        `;
    }
    
    // Show results
    if (data.results && data.results.length > 0) {
        resultsContainer.innerHTML = `
            <div class="results-header">Results (${data.results.length})</div>
            ${data.results.map(result => `
                <div class="result-item" onclick="selectResult('${result.type}', '${result.id}')">
                    <div class="result-icon">
                        <i class="bi bi-${getResultIcon(result.type)}"></i>
                    </div>
                    <div class="result-details">
                        <div class="result-title">${result.name}</div>
                        <div class="result-subtitle">${result.subtitle}</div>
                    </div>
                    <div class="result-type">${result.type}</div>
                </div>
            `).join('')}
        `;
    } else {
        resultsContainer.innerHTML = `
            <div class="no-results">
                <i class="bi bi-search me-2"></i>
                No results found
            </div>
        `;
    }
    
    showSearchDropdown();
}

function getResultIcon(type) {
    switch(type) {
        case 'student': return 'person-circle';
        case 'professor': return 'person-badge';
        case 'program': return 'book';
        case 'batch': return 'people';
        case 'admin': return 'shield-check';
        default: return 'search';
    }
}

function selectSuggestion(suggestion) {
    const searchInput = document.getElementById('universalSearchInput');
    searchInput.value = suggestion;
    performSearch(suggestion);
}

function selectResult(type, id) {
    // Navigate to appropriate page based on result type
    switch(type) {
        case 'student':
            window.location.href = `/admin/students/${id}`;
            break;
        case 'professor':
            window.location.href = `/admin/professors/${id}`;
            break;
        case 'program':
            window.location.href = `/admin/programs/${id}`;
            break;
        case 'batch':
            window.location.href = `/admin/batches/${id}`;
            break;
        default:
            console.log('Unknown result type:', type);
    }
}

function showSearchDropdown() {
    const dropdown = document.getElementById('searchResultsDropdown');
    if (dropdown) {
        dropdown.style.display = 'block';
    }
}

function hideSearchDropdown() {
    // Add a small delay to allow for click events on results
    setTimeout(() => {
        const dropdown = document.getElementById('searchResultsDropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }, 200);
}

function showSearchLoading(show) {
    const loading = document.getElementById('searchLoading');
    if (loading) {
        if (show) {
            loading.classList.remove('d-none');
        } else {
            loading.classList.add('d-none');
        }
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const searchContainer = document.querySelector('.header-search');
    const dropdown = document.getElementById('searchResultsDropdown');
    
    if (searchContainer && !searchContainer.contains(event.target)) {
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }
});

// Handle Enter key in search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('universalSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    }
});
</script>

<style>
.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 4px;
}

.search-dropdown-content {
    padding: 8px;
}

.suggestions-header, .results-header {
    font-weight: 600;
    color: #666;
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
    font-size: 0.9rem;
}

.suggestion-item, .result-item {
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    margin-bottom: 2px;
}

.suggestion-item:hover, .result-item:hover {
    background-color: #f8f9fa;
}

.result-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.result-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
}

.result-details {
    flex: 1;
}

.result-title {
    font-weight: 500;
    color: #333;
}

.result-subtitle {
    font-size: 0.85rem;
    color: #666;
}

.result-type {
    font-size: 0.8rem;
    color: #999;
    text-transform: capitalize;
}

.no-results {
    text-align: center;
    padding: 20px;
    color: #666;
}

.search-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    color: #666;
}

.header-search {
    position: relative;
}

.header-search .search-box {
    position: relative;
}
</style>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@stack('scripts')

<!-- Include Global Chat Component -->
@include('components.global-chat')

</body>
</html>
