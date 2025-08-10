// Admin Settings page script  
(() => {
  if (window.__ADMIN_SETTINGS_INIT__) return;
  window.__ADMIN_SETTINGS_INIT__ = true;

  function initAdminSettings() {
    console.log('Admin settings page initialized');
    // TODO: Extract settings form handling, sortable lists, validation
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminSettings);
  } else { initAdminSettings(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['admin-settings'] = () => { /* already initialized above */ };
