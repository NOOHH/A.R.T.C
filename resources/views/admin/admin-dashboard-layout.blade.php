<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    @php
        // Get user info for global variables
        $user = Auth::user();
        
        // Check if user is actually logged in via Laravel Auth or valid session
        $isLoggedIn = Auth::check() || session('logged_in') === true;
        
        // If Laravel Auth user is not available but session indicates logged in, fallback to session data
        if (!$user && $isLoggedIn) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Only use session data if logged_in is explicitly true
            if (session('logged_in') === true || $_SESSION['logged_in'] ?? false) {
                $sessionUser = (object) [
                    'id' => $_SESSION['user_id'] ?? session('user_id'),
                    'name' => $_SESSION['user_name'] ?? session('user_name') ?? 'Guest',
                    'role' => $_SESSION['user_type'] ?? session('user_role') ?? 'guest'
                ];
                
                // Only use session user if we have valid session data
                if ($sessionUser->id) {
                    $user = $sessionUser;
                }
            }
        }
        
        // If not logged in, clear user data
        if (!$isLoggedIn) {
            $user = null;
        }
    @endphp

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        window.myId = @json($isLoggedIn && $user ? $user->id : null);
        window.myName = @json($isLoggedIn && $user ? $user->name : 'Guest');
        window.isAuthenticated = @json($isLoggedIn && (bool) $user);
        window.userRole = @json($isLoggedIn && $user ? $user->role : 'guest');
        window.csrfToken = @json(csrf_token());
        
        // Global chat state
        window.currentChatType = null;
        window.currentChatUser = null;
        
        // Make variables available without window prefix
        var myId = window.myId;
        var myName = window.myName;
        var isAuthenticated = window.isAuthenticated;
        var userRole = window.userRole;
        var csrfToken = window.csrfToken;
        var currentChatType = window.currentChatType;
        var currentChatUser = window.currentChatUser;
        
        console.log('Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>

    @php
        // Ensure we can reliably check admin status
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $isAdmin = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin')
                 || (session('user_type') === 'admin');
    @endphp
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Your Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">

    {{-- Global UI Styles (e.g. from your helper) --}}
    {!! App\Helpers\UIHelper::getNavbarStyles() !!}

    {{-- Chat CSS + any overrides --}}
    @stack('styles')
</head>
<body>
<div class="admin-container">
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <!-- Hamburger Menu Button - Always visible -->
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
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
            <!-- Sidebar Overlay -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <!-- Modern Sliding Sidebar -->
            <aside class="modern-sidebar" id="modernSidebar">
                <div class="sidebar-content">
                    <nav class="sidebar-nav">
                        <!-- Dashboard -->
                        <div class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link @if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <!-- Certificate Management -->
                        <div class="nav-item">
                            <a href="{{ route('admin.certificates') }}" class="nav-link @if(Route::currentRouteName() === 'admin.certificates') active @endif">
                                <i class="bi bi-award"></i>
                                <span>Certificate Management</span>
                            </a>
                        </div>

                        <!-- Registration Management -->
                        <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) active @endif">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#registrationMenu">
                                <i class="bi bi-person-plus"></i>
                                <span>Registration</span>
                                <i class="bi bi-chevron-down dropdown-arrow"></i>
                            </a>
                            <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'admin.student.registration')) show @endif" id="registrationMenu">
                                <div class="submenu">
                                    <a href="{{ route('admin.student.registration.pending') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.pending') active @endif">
                                        <i class="bi bi-clock"></i>
                                        <span>Pending</span>
                                    </a>
                                    <a href="{{ route('admin.student.registration.history') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.history') active @endif">
                                        <i class="bi bi-archive"></i>
                                        <span>History</span>
                                    </a>
                                    <a href="{{ route('admin.student.registration.payment.pending') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.payment.pending') active @endif">
                                        <i class="bi bi-credit-card"></i>
                                        <span>Payment Pending</span>
                                    </a>
                                    <a href="{{ route('admin.student.registration.payment.history') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.student.registration.payment.history') active @endif">
                                        <i class="bi bi-receipt"></i>
                                        <span>Payment History</span>
                                    </a>
                                    <a href="{{ route('admin.batches.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.batches')) active @endif">
                                        <i class="bi bi-people"></i>
                                        <span>Batch Enroll</span>
                                    </a>
                                    <a href="{{ route('admin.enrollments.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.enrollments.index') active @endif">
                                        <i class="bi bi-book"></i>
                                        <span>Assign Course to Student</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Account Management -->
                        <div class="nav-item dropdown-nav">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#accountsMenu">
                                <i class="bi bi-people"></i>
                                <span>Accounts</span>
                                <i class="bi bi-chevron-down dropdown-arrow"></i>
                            </a>
                            <div class="collapse" id="accountsMenu">
                                <div class="submenu">
                                    <a href="{{ route('admin.students.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.students.index') active @endif">
                                        <i class="bi bi-person"></i>
                                        <span>Students</span>
                                    </a>
                                    @if($isAdmin)
                                    <a href="{{ route('admin.directors.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.directors')) active @endif">
                                        <i class="bi bi-person-badge"></i>
                                        <span>Directors</span>
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.professors.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.professors')) active @endif">
                                        <i class="bi bi-person-workspace"></i>
                                        <span>Professors</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Programs & Packages -->
                        <div class="nav-item dropdown-nav @if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index') active @endif">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#programsMenu">
                                <i class="bi bi-mortarboard"></i>
                                <span>Programs</span>
                                <i class="bi bi-chevron-down dropdown-arrow"></i>
                            </a>
                            <div class="collapse @if(str_starts_with(Route::currentRouteName(), 'admin.programs') || str_starts_with(Route::currentRouteName(), 'admin.modules') || Route::currentRouteName() === 'admin.packages.index') show @endif" id="programsMenu">
                                <div class="submenu">
                                    <a href="{{ route('admin.programs.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.programs.index') active @endif">
                                        <i class="bi bi-collection"></i>
                                        <span>Manage Programs</span>
                                    </a>
                                    <a href="{{ route('admin.modules.index') }}" class="submenu-link @if(str_starts_with(Route::currentRouteName(), 'admin.modules')) active @endif">
                                        <i class="bi bi-puzzle"></i>
                                        <span>Manage Modules</span>
                                    </a>
                                    @if($isAdmin)
                                    <a href="{{ route('admin.packages.index') }}" class="submenu-link @if(Route::currentRouteName() === 'admin.packages.index') active @endif">
                                        <i class="bi bi-box-seam"></i>
                                        <span>Packages</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Analytics -->
                        @if($isAdmin || session('user_type') === 'director')
                        <div class="nav-item">
                            <a href="{{ route('admin.analytics.index') }}" class="nav-link @if(Route::currentRouteName() === 'admin.analytics.index') active @endif">
                                <i class="bi bi-graph-up"></i>
                                <span>Analytics</span>
                            </a>
                        </div>
                        @endif
                        
                        <!-- Chat Management -->
                        <div class="nav-item">
                            <a href="{{ route('admin.chat.index', ['default' => 'true']) }}" class="nav-link @if(Route::currentRouteName() === 'admin.chat.index') active @endif">
                                <i class="bi bi-chat-dots"></i>
                                <span>Chat Logs</span>
                            </a>
                        </div>
                        
                        <!-- FAQ Management -->
                        <div class="nav-item">
                            <a href="{{ route('admin.faq.index') }}" class="nav-link @if(Route::currentRouteName() === 'admin.faq.index') active @endif">
                                <i class="bi bi-question-circle"></i>
                                <span>FAQ Management</span>
                            </a>
                        </div>

                        <!-- Announcements -->
                        <div class="nav-item">
                            <a href="{{ route('admin.announcements.index') }}" class="nav-link @if(str_starts_with(Route::currentRouteName(), 'admin.announcements')) active @endif">
                                <i class="bi bi-megaphone"></i>
                                <span>Announcements</span>
                            </a>
                        </div>

                        <!-- Settings -->
                        @if($isAdmin)
                        <div class="nav-item">
                            <a href="{{ route('admin.settings.index') }}" class="nav-link @if(Route::currentRouteName() === 'admin.settings.index') active @endif">
                                <i class="bi bi-gear"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                        @endif
                    </nav>

                    <!-- Sidebar Footer (Bottom Section) -->
                    <div class="sidebar-footer">
                        <div class="nav-item">
                            <a href="#" class="nav-link" onclick="handleAdminLogout();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
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
    // Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const modernSidebar = document.getElementById('modernSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const contentWrapper = document.querySelector('.content-wrapper');

    // Toggle sidebar function
    function toggleSidebar() {
        if (window.innerWidth >= 768) {
            // Desktop: Toggle collapsed state
            modernSidebar.classList.toggle('collapsed');    
            
    if (modernSidebar.classList.contains('collapsed')) {
        contentWrapper.style.marginLeft = '50px';
    } else {
        contentWrapper.style.marginLeft = '50px';
    }
        } else {
            // Mobile: Toggle sidebar visibility
            if (modernSidebar) {
                modernSidebar.classList.toggle('active');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
            }
            document.body.style.overflow = modernSidebar && modernSidebar.classList.contains('active') ? 'hidden' : '';
        }
    }

    // Close sidebar function (mobile only)
    function closeSidebar() {
        if (window.innerWidth < 768) {
            if (modernSidebar) {
                modernSidebar.classList.remove('active');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
    }

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Handle window resize
    window.addEventListener('resize', function() {
           if (window.innerWidth >= 768 && contentWrapper) {
        contentWrapper.style.marginLeft = modernSidebar.classList.contains('collapsed')
            ? '70px'
            : '50px';
    }
    });

    // Sidebar dropdowns now use Bootstrap's native collapse behavior; no custom toggle logic needed
    // const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    // Bootstrap collapse via data-bs-toggle handles opening and closing

    // Auto-expand active dropdowns
    document.querySelectorAll('.dropdown-nav.active .collapse').forEach(collapse => {
        const bsCollapse = new bootstrap.Collapse(collapse, {
            toggle: false
        });
        bsCollapse.show();
        
        const arrow = collapse.previousElementSibling.querySelector('.dropdown-arrow');
        if (arrow) {
            arrow.style.transform = 'rotate(180deg)';
        }
    });

    // Handle nav link clicks
    document.querySelectorAll('.nav-link, .submenu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default for dropdown toggles
            if (this.classList.contains('dropdown-toggle')) {
                return;
            }
            
            // Close sidebar on mobile when clicking nav links
            if (window.innerWidth < 768) {
                setTimeout(closeSidebar, 100);
            }
        });
    });

    // Initialize proper margins on page load
    if (window.innerWidth >= 168) {
        if (contentWrapper) {
            contentWrapper.style.marginLeft = '50px';
        }
    }
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
    
    // Use GET request to our SearchController endpoint
    const params = new URLSearchParams({
        query: searchQuery,
        type: currentSearchType || 'all',
        limit: 10
    });
    
    fetch(`/search?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
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
    if (suggestionsContainer) {
        suggestionsContainer.innerHTML = '';
    }
    
    // Check if we got the new format from SearchController
    const results = data.results || data;
    
    // Show results
    if (results && results.length > 0) {
        resultsContainer.innerHTML = results.map(result => {
            if (result.type === 'program') {
                return `
                    <div class="result-item" onclick="selectResult('program', '${result.id}')">
                        <div class="result-icon">
                            <i class="bi bi-collection text-primary"></i>
                        </div>
                        <div class="result-details">
                            <div class="result-title">${result.name}</div>
                            <div class="result-subtitle">${result.description || 'Program'}</div>
                            <small class="text-muted">${result.modules_count || 0} modules • ${result.courses_count || 0} courses</small>
                        </div>
                        <div class="result-type">
                            <span class="badge bg-info">Program</span>
                        </div>
                    </div>
                `;
            } else {
                // User result
                const roleClass = getRoleClass(result.role);
                return `
                    <div class="result-item" onclick="selectResult('${result.type}', '${result.id}')">
                        <div class="result-icon">
                            <img src="${result.avatar || '/images/default-avatar.png'}" alt="${result.name}" class="result-avatar">
                        </div>
                        <div class="result-details">
                            <div class="result-title">${result.name}</div>
                            <div class="result-subtitle">${result.email}</div>
                            ${result.programs && result.programs.length > 0 ? 
                                `<small class="text-muted">Programs: ${result.programs.join(', ')}</small>` : ''}
                        </div>
                        <div class="result-type">
                            <span class="badge bg-${roleClass}">${result.role}</span>
                            <br><small class="text-muted">${result.status}</small>
                        </div>
                    </div>
                `;
            }
        }).join('');
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

// Get role class for badge styling
function getRoleClass(role) {
    switch(role ? role.toLowerCase() : '') {
        case 'student': return 'primary';
        case 'professor': return 'success';
        case 'admin': return 'warning';
        case 'director': return 'danger';
        default: return 'secondary';
    }
}

function selectResult(type, id) {
    // For our new search system, show profile modal instead of navigating
    hideSearchDropdown();
    
    if (type === 'program') {
        showProgramModal(id);
    } else {
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
                alert('Unable to load user profile. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error loading user profile:', error);
            alert('Error loading user profile. Please try again.');
        });
}

// Show program details modal
function showProgramModal(programId) {
    fetch(`/search/profile?user_id=${programId}&type=program`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProgramModal(data.program);
            } else {
                // Fallback to navigation
                window.location.href = `/admin/programs/${programId}`;
            }
        })
        .catch(error => {
            console.error('Error loading program details:', error);
            // Fallback to navigation
            window.location.href = `/admin/programs/${programId}`;
        });
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

.result-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
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
{{-- 1) Include the chat HTML offcanvas --}}
    @include('components.global-chat')

    <!-- Core JS: Bootstrap bundle + jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Your Admin‐page JS (logout handler, search, sidebar toggles…) --}}
    <script>
    function handleAdminLogout() {
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('admin-logout-form').submit();
        }
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
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>${enrollment.program}</strong>
                                                            <br><small class="text-muted">Enrolled: ${new Date(enrollment.enrolled_at).toLocaleDateString()}</small>
                                                        </div>
                                                        <span class="badge bg-success">${enrollment.status || 'Active'}</span>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                    
                                    ${profile.role === 'Professor' && profile.professor_id ? `
                                        <h6 class="mt-4">Professor Information</h6>
                                        <p><strong>Professor ID:</strong> ${profile.professor_id}</p>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            ${profile.role === 'Student' && profile.student_id ? `
                                <button type="button" class="btn btn-primary" onclick="window.open('/admin/students/${profile.student_id}', '_blank')">
                                    <i class="bi bi-eye me-2"></i>View Full Student Profile
                                </button>
                                <button type="button" class="btn btn-success" onclick="window.open('/profile/user/${profile.id}', '_blank')">
                                    <i class="bi bi-user me-2"></i>Public Profile
                                </button>
                            ` : profile.role === 'Professor' && profile.professor_id ? `
                                <button type="button" class="btn btn-success" onclick="window.open('/admin/professors/${profile.id}', '_blank')">
                                    <i class="bi bi-eye me-2"></i>View Full Professor Profile
                                </button>
                                <button type="button" class="btn btn-primary" onclick="window.open('/profile/user/${profile.id}', '_blank')">
                                    <i class="bi bi-user me-2"></i>Public Profile
                                </button>
                            ` : `
                                <button type="button" class="btn btn-primary" onclick="window.open('/profile/user/${profile.id}', '_blank')">
                                    <i class="bi bi-user me-2"></i>View Profile
                                </button>
                            `}
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

    // Display program modal
    function displayProgramModal(program) {
        const modalContent = `
            <div class="modal fade" id="programModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-collection me-2"></i>${program.name}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>Program Description</h6>
                                    <p>${program.description || 'No description available'}</p>
                                    
                                    <h6 class="mt-4">Modules & Courses</h6>
                                    <div class="accordion" id="modulesAccordion">
                                        ${program.modules.map((module, index) => `
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading${index}">
                                                    <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#collapse${index}">
                                                        ${module.name}
                                                        <span class="badge bg-secondary ms-2">${module.courses.length} courses</span>
                                                    </button>
                                                </h2>
                                                <div id="collapse${index}" 
                                                     class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                                                     data-bs-parent="#modulesAccordion">
                                                    <div class="accordion-body">
                                                        <p class="text-muted">${module.description || 'No description available'}</p>
                                                        <div class="row">
                                                            ${module.courses.map(course => `
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="card">
                                                                        <div class="card-body p-3">
                                                                            <h6 class="card-title">${course.name}</h6>
                                                                            <p class="card-text small text-muted">${course.description || 'No description'}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            `).join('')}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Program Statistics</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total Modules:</span>
                                                <span class="badge bg-primary">${program.total_modules}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total Courses:</span>
                                                <span class="badge bg-info">${program.total_courses}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Enrolled Students:</span>
                                                <span class="badge bg-success">${program.total_students}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Created:</span>
                                                <small class="text-muted">${new Date(program.created_at).toLocaleDateString()}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="window.open('/admin/programs/${program.id}', '_blank')">
                                <i class="bi bi-eye me-2"></i>View Full Program
                            </button>
                            <button type="button" class="btn btn-success" onclick="window.open('/profile/program/${program.id}', '_blank')">
                                <i class="bi bi-collection me-2"></i>Public Profile
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('programModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add new modal to body
        document.body.insertAdjacentHTML('beforeend', modalContent);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('programModal'));
        modal.show();
    }
    
    // … your search and sidebar toggle scripts …
    </script>

    {{-- 2) Finally, dump the chat component’s JS (and any other @push('scripts')) --}}
    @stack('scripts')
</body>
</html>
