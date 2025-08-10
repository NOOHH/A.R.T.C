// Signup page script
(() => {
  if (window.__SIGNUP_INIT__) return;
  window.__SIGNUP_INIT__ = true;

  function initSignup() {
    console.log('Signup page initialized');
    // TODO: Extract signup form validation, recaptcha, registration logic
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSignup);
  } else { initSignup(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['signup'] = () => { /* already initialized above */ };
