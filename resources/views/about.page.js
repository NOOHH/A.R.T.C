// About page script
(() => {
  if (window.__ABOUT_INIT__) return;
  window.__ABOUT_INIT__ = true;

  function initAboutPage() {
    console.log('About page initialized');
    // TODO: Extract about page interactions, team member modals, contact forms
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAboutPage);
  } else { initAboutPage(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['about'] = () => { /* already initialized above */ };
