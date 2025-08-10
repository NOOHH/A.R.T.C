// Professor Modules Index page script
(() => {
  if (window.__PROFESSOR_MODULES_INDEX_INIT__) return;
  window.__PROFESSOR_MODULES_INDEX_INIT__ = true;

  function initModulesIndex() {
    console.log('Professor modules index initialized');
    // TODO: Extract sortable JS, module management, CRUD operations
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModulesIndex);
  } else { initModulesIndex(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['professor-modules-index'] = () => { /* already initialized above */ };
