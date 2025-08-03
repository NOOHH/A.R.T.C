// Admin Sidebar Management
(function() {
    'use strict';
    
    // Constants
    const SIDEBAR_STORAGE_KEY = 'admin_sidebar_state';
    const SIDEBAR_COLLAPSED_CLASS = 'collapsed';
    
    // DOM Elements
    let sidebar = null;
    let toggleButton = null;
    let mainContent = null;
    
    // Initialize sidebar on page load
    function initSidebar() {
        sidebar = document.getElementById('modernSidebar') || document.querySelector('.modern-sidebar');
        toggleButton = document.querySelector('.sidebar-toggle');
        mainContent = document.querySelector('.main-content');
        
        if (!sidebar || !toggleButton) {
            console.warn('Sidebar elements not found');
            return;
        }
        
        // Restore sidebar state from localStorage
        restoreSidebarState();
        
        // Add event listeners
        toggleButton.addEventListener('click', toggleSidebar);
        
        // Handle resize events
        window.addEventListener('resize', handleResize);
        
        // Handle clicks outside sidebar on mobile
        document.addEventListener('click', handleOutsideClick);
        
        // Apply consistent styling
        applySidebarStyling();
    }
    
    // Toggle sidebar function
    function toggleSidebar() {
        if (!sidebar) return;
        
        const isCollapsed = sidebar.classList.contains(SIDEBAR_COLLAPSED_CLASS);
        
        if (isCollapsed) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
        
        // Save state to localStorage
        saveSidebarState();
    }
    
    // Expand sidebar
    function expandSidebar() {
        if (!sidebar) return;
        
        sidebar.classList.remove(SIDEBAR_COLLAPSED_CLASS);
        
        // Update main content margin if exists
        if (mainContent) {
            mainContent.style.marginLeft = '280px';
        }
        
        // Update toggle button icon
        updateToggleButtonIcon(false);
    }
    
    // Collapse sidebar
    function collapseSidebar() {
        if (!sidebar) return;
        
        sidebar.classList.add(SIDEBAR_COLLAPSED_CLASS);
        
        // Update main content margin if exists
        if (mainContent) {
            mainContent.style.marginLeft = '80px';
        }
        
        // Update toggle button icon
        updateToggleButtonIcon(true);
    }
    
    // Update toggle button icon
    function updateToggleButtonIcon(isCollapsed) {
        if (!toggleButton) return;
        
        const icon = toggleButton.querySelector('i');
        if (icon) {
            icon.className = isCollapsed ? 'bi bi-list' : 'bi bi-x-lg';
        }
    }
    
    // Save sidebar state to localStorage
    function saveSidebarState() {
        if (!sidebar) return;
        
        const isCollapsed = sidebar.classList.contains(SIDEBAR_COLLAPSED_CLASS);
        localStorage.setItem(SIDEBAR_STORAGE_KEY, isCollapsed ? 'collapsed' : 'expanded');
    }
    
    // Restore sidebar state from localStorage
    function restoreSidebarState() {
        if (!sidebar) return;
        
        const savedState = localStorage.getItem(SIDEBAR_STORAGE_KEY);
        
        if (savedState === 'collapsed') {
            collapseSidebar();
        } else {
            expandSidebar();
        }
    }
    
    // Handle window resize
    function handleResize() {
        if (!sidebar) return;
        
        const width = window.innerWidth;
        
        // Auto-collapse on small screens
        if (width < 768) {
            sidebar.classList.add('mobile-mode');
            collapseSidebar();
        } else {
            sidebar.classList.remove('mobile-mode');
            restoreSidebarState();
        }
    }
    
    // Handle clicks outside sidebar on mobile
    function handleOutsideClick(event) {
        if (!sidebar || window.innerWidth >= 768) return;
        
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isToggleButton = toggleButton && toggleButton.contains(event.target);
        
        if (!isClickInsideSidebar && !isToggleButton) {
            collapseSidebar();
        }
    }
    
    // Apply consistent sidebar styling
    function applySidebarStyling() {
        if (!sidebar) return;
        
        // Ensure consistent classes
        sidebar.classList.add('modern-sidebar');
        
        // Add hover effects for menu items
        const menuItems = sidebar.querySelectorAll('.nav-link, .submenu-link');
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
            });
            
            item.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.backgroundColor = '';
                }
            });
        });
        
        // Handle dropdown menus
        const dropdownToggles = sidebar.querySelectorAll('.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                if (submenu) {
                    const isExpanded = submenu.style.display === 'block';
                    submenu.style.display = isExpanded ? 'none' : 'block';
                    
                    // Update arrow icon
                    const arrow = this.querySelector('.dropdown-arrow');
                    if (arrow) {
                        arrow.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(90deg)';
                    }
                }
            });
        });
    }
    
    // Public API
    window.AdminSidebar = {
        init: initSidebar,
        toggle: toggleSidebar,
        expand: expandSidebar,
        collapse: collapseSidebar
    };
    
    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
    
})();
