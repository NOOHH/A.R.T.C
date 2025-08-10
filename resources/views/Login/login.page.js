// Login page script
(() => {
  if (window.__LOGIN_INIT__) return;
  window.__LOGIN_INIT__ = true;

  function initLogin() {
    console.log('Login page initialized');
    // TODO: Extract login form validation, authentication logic
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLogin);
  } else { initLogin(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['login'] = () => { /* already initialized above */ };
