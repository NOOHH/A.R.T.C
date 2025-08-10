// Student Calendar page script
(() => {
  if (window.__STUDENT_CALENDAR_INIT__) return;
  window.__STUDENT_CALENDAR_INIT__ = true;

  function initCalendar() {
    console.log('Student calendar initialized');
    // TODO: Extract calendar functionality, event handling
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalendar);
  } else { initCalendar(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['student-calendar'] = () => { /* already initialized above */ };
