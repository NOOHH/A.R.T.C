
<header class="main-header">

        <style>
        /* ==== Search box styles ==== */
        .search-container {
            position: relative;
            max-width: 500px;
            width: 100%;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 0.6rem 1rem;
            padding-right: 3.5rem; /* space for button */
            border-radius: 999px;
            border: 1px solid #d1d5db;
            font-size: 0.95rem;
            height: 42px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: border-color .2s, box-shadow .2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.2);
        }

.search-btn {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    padding: 8px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    height: 34px;
    min-width: 34px;
    color: white;
    font-size: 16px; /* make the emoji/icon visible */
    gap: 4px;
    transition: filter .15s, transform .15s;
}


        .search-btn:hover {
            filter: brightness(1.05);
        }

        .search-btn:active {
            transform: translateY(-50%) scale(.97);
        }

        .search-btn .spinner-border {
            width: 16px;
            height: 16px;
            border-width: 2px;
        }

        .search-results-wrapper {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 16px 40px rgba(0,0,0,0.08);
            z-index: 1100;
            overflow: hidden;
            font-size: 0.9rem;
        }

        .search-dropdown {
            display: none;
        }

        .search-dropdown.active {
            display: block;
        }

        .search-dropdown-content {
            padding: 10px;
        }

        .search-suggestion-item,
        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-suggestion-item:hover,
        .search-result-item:hover {
            background: #f1f5fe;
        }

        .search-loading {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            color: #555;
            font-size: 0.85rem;
        }

        .visually-hidden {
            position: absolute !important;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            white-space: nowrap;
            border: 0;
        }

        /* Mobile Header Optimizations */
        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .header-left,
            .header-center,
            .header-right {
                width: 100%;
                justify-content: center;
            }
            
            .brand-container {
                justify-content: center;
            }
            
            .brand-text {
                font-size: 1.1rem;
            }
            
            .brand-subtext {
                font-size: 0.85rem;
            }
            
            .search-container {
                max-width: 100%;
            }
            
            .search-input {
                font-size: 1rem;
                height: 48px;
                padding: 0.8rem 1.2rem;
                padding-right: 4rem;
            }
            
            .search-btn {
                height: 40px;
                min-width: 40px;
                right: 4px;
                z-index: 99
            }
            
            #chatTriggerBtn {
                font-size: 1.8rem !important;
                padding: 0.5rem;
                min-height: 44px;
                min-width: 44px;
            }
        }

        @media (max-width: 480px) {
            .main-header {
                padding: 0.8rem;
                gap: 0.8rem;
            }
            
            .brand-text {
                font-size: 1rem;
                text-align: center;
            }
            
            .brand-subtext {
                font-size: 0.8rem;
                text-align: center;
            }
            
            .search-input {
                height: 44px;
                padding: 0.7rem 1rem;
                padding-right: 3.5rem;
                font-size: 0.95rem;
            }
            
            .search-btn {
                height: 36px;
                min-width: 36px;
            }
        }

        @media (max-width: 360px) {
            .brand-container {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .brand-logo {
                width: 40px;
                height: 40px;
            }
            
            .header-right {
                flex-direction: row;
                gap: 1rem;
                justify-content: center;
            }
        }
    </style>
    <div class="header-left">
        <!-- Brand Logo and Text -->
        <div class="brand-container d-flex align-items-center gap-3">
            <?php
                // Get brand logo from NavbarComposer data
                $brandLogo = $navbar['brand_logo'] ?? null;
                $defaultLogo = asset('images/ARTC_logo.png');
            ?>
            
            <?php if($brandLogo): ?>
                <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($brandLogo)); ?>" 
                     alt="Brand Logo" 
                     class="brand-logo"
                     onerror="this.src='<?php echo e($defaultLogo); ?>'">
            <?php else: ?>
                <img src="<?php echo e($defaultLogo); ?>" alt="A.R.T.C" class="brand-logo">
            <?php endif; ?>
            
            <div class="brand-text-area d-flex flex-column justify-content-center">
                <span class="brand-text fw-bold"><?php echo e($navbar['brand_name'] ?? 'Ascendo Review & Training Center'); ?></span>
                <span class="brand-subtext text-muted">Admin Portal</span>
            </div>
        </div>
    </div>

    <div class="header-center">
        <!-- Universal Search -->
        <div class="search-container">
            <div class="search-box">
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
        <button class="btn btn-link p-0" id="chatTriggerBtn" title="Open Chat" style="font-size: 1.5rem; color: #764ba2;">
            <i class="bi bi-chat-dots"></i>
        </button>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\admin\admin-layouts\admin-header.blade.php ENDPATH**/ ?>