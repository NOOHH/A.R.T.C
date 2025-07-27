
<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('studentSidebar');
  const toggleBtn = document.getElementById('sidebarToggleBtn');
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
    }
  }

  // Close sidebar on mobile
  function closeSidebar() {
    if (!isDesktop()) {
      sidebar.classList.remove('mobile-open');
      if (backdrop) {
        backdrop.classList.remove('active');
      }
    }
  }

  // Event listeners
  if (toggleBtn) {
    toggleBtn.addEventListener('click', toggleSidebar);
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
    } else {
      // Keep collapsed state when switching to mobile
      sidebar.classList.remove('mobile-open');
      if (backdrop) {
        backdrop.classList.remove('active');
      }
    }
  });

  // Initialize sidebar state based on screen size
  if (isDesktop()) {
    sidebar.classList.remove('mobile-open');
    if (backdrop) {
      backdrop.classList.remove('active');
    }
  }
});
</script>
