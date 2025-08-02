{{-- resources/views/admin/admin-layouts/admin-header.blade.php --}}
<header class="main-header">
    <div class="header-left">
        <!-- Brand Logo and Text -->
        <div class="brand-container d-flex align-items-center gap-3">
            <img src="{{ asset('images/ARTC_logo.png') }}" alt="A.R.T.C" class="brand-logo">
            <div class="brand-text-area d-flex flex-column justify-content-center">
                <span class="brand-text fw-bold">Ascendo Review &amp; Training Center</span>
                <span class="brand-subtext text-muted">Admin Portal</span>
            </div>
        </div>
    </div>

    <div class="header-center">
        <!-- Universal Search -->
        <div class="search-container">
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
    </div>

    <div class="header-right">
        <!-- Chat Icon Button -->
        <button class="btn btn-link p-0 ms-2" id="chatTriggerBtn" title="Open Chat" style="font-size: 1.5rem; color: #764ba2;">
            <i class="bi bi-chat-dots"></i>
        </button>
        
        <!-- Mobile Profile Icon -->
        <div class="profile-icon">👤</div>
    </div>
</header>
