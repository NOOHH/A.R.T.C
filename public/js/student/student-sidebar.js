document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('studentSidebar');
  const toggleBtn = document.getElementById('sidebarToggleBtn');
  const mobileToggleBtn = document.getElementById('mobileSidebarToggle');
  const backdrop = document.getElementById('sidebarBackdrop');
  
  // Helper to check desktop vs mobile break
  const isDesktop = () => window.innerWidth >= 769;

  // Toggle action
  function toggleSidebar() {
    if (isDesktop()) {
      // collapse/expand on desktop
      sidebar.classList.toggle('collapsed');
    } else {
      // slide-in on mobile
      sidebar.classList.toggle('mobile-open');
      if (backdrop) {
        backdrop.classList.toggle('active');
      }
      // Update mobile toggle button state
      if (mobileToggleBtn) {
        mobileToggleBtn.classList.toggle('active');
        mobileToggleBtn.classList.toggle('sidebar-open');
      }
    }
  }

  // Close sidebar on mobile
  function closeSidebar() {
    if (!isDesktop()) {
      sidebar.classList.remove('mobile-open');
      if (backdrop) {
        backdrop.classList.remove('active');
      }
      // Reset mobile toggle button state
      if (mobileToggleBtn) {
        mobileToggleBtn.classList.remove('active');
        mobileToggleBtn.classList.remove('sidebar-open');
      }
    }
  }

  // Event listeners
  if (toggleBtn) {
    toggleBtn.addEventListener('click', toggleSidebar);
  }
  
  // Mobile toggle button event listener
  if (mobileToggleBtn) {
    mobileToggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      toggleSidebar();
    });
  }
  
  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }

  // On window resize, ensure proper state
  window.addEventListener('resize', () => {
    if (isDesktop()) {
      if (backdrop) {
        backdrop.classList.remove('active');
      }
      sidebar.classList.remove('mobile-open');
      // Reset mobile toggle button state on desktop
      if (mobileToggleBtn) {
        mobileToggleBtn.classList.remove('active');
        mobileToggleBtn.classList.remove('sidebar-open');
      }
    } else {
      // Keep collapsed state when switching to mobile
      sidebar.classList.remove('mobile-open');
      if (backdrop) {
        backdrop.classList.remove('active');
      }
      // Reset mobile toggle button state
      if (mobileToggleBtn) {
        mobileToggleBtn.classList.remove('active');
        mobileToggleBtn.classList.remove('sidebar-open');
      }
    }
  });

  // Initialize sidebar state based on screen size
  if (isDesktop()) {
    sidebar.classList.remove('mobile-open');
    if (backdrop) {
      backdrop.classList.remove('active');
    }
    if (mobileToggleBtn) {
      mobileToggleBtn.classList.remove('active');
      mobileToggleBtn.classList.remove('sidebar-open');
    }
  }
});
