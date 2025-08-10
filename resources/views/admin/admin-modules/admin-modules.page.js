// Admin Modules page script
(() => {
  if (window.__ADMIN_MODULES_INIT__) return;
  window.__ADMIN_MODULES_INIT__ = true;

  function initAdminModules() {
    console.log('Admin modules page initialized');
    // TODO: Extract module management, sortable functionality, CRUD operations
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminModules);
  } else { initAdminModules(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['admin-modules'] = () => { /* already initialized above */ };
