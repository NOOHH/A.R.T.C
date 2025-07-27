
<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar       = document.getElementById('modernSidebar');
  const toggleBtn     = document.getElementById('sidebarToggle');
  const closeBtn      = document.getElementById('sidebarClose');
  const reopenBtn     = document.getElementById('sidebarReopenBtn');
  
  // Create overlay for mobile
  const overlay = document.createElement('div');
  overlay.className = 'sidebar-overlay';
  document.body.appendChild(overlay);

  // Helper to check desktop vs mobile break
  const isDesktop = () => window.innerWidth >= 992;

  // Toggle action
  function openOrCollapse() {
    if (isDesktop()) {
      // collapse/expand on desktop
      sidebar.classList.toggle('collapsed');
      reopenBtn.style.display = sidebar.classList.contains('collapsed') ? 'block' : 'none';
    } else {
      // slide‐in on mobile
      sidebar.classList.add('active');
      overlay.classList.add('active');
    }
  }

  // Close action
  function closeSidebar() {
    if (isDesktop()) {
      sidebar.classList.toggle('collapsed');
      reopenBtn.style.display = sidebar.classList.contains('collapsed') ? 'block' : 'none';
    } else {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    }
  }

  toggleBtn.addEventListener('click', openOrCollapse);
  closeBtn.addEventListener('click', closeSidebar);
  
  // Reopen button only for desktop collapsed state
  reopenBtn.addEventListener('click', () => {
    sidebar.classList.remove('collapsed');
    reopenBtn.style.display = 'none';
  });

  // Clicking overlay hides mobile sidebar
  overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
  });

  // On window resize, ensure overlay/sidebar states reset
  window.addEventListener('resize', () => {
    if (isDesktop()) {
      overlay.classList.remove('active');
      sidebar.classList.remove('active');
    } else {
      // optionally collapse desktop state when switching to mobile
      sidebar.classList.remove('collapsed');
      reopenBtn.style.display = 'none';
    }
  });
});
</script>
