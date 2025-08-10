// Admin Programs page specific JS extracted from inline script (image preview + page init registration)
(() => {
  if (window.__ADMIN_PROGRAMS_INIT__) return; // guard
  window.__ADMIN_PROGRAMS_INIT__ = true;

  function initImagePreview() {
    const imageInput = document.getElementById('program_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');
    if (!imageInput || !imagePreview || !previewImg || !removeImageBtn) return;

    imageInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) { imagePreview.style.display = 'none'; return; }
      if (!file.type.startsWith('image/')) { alert('Please select a valid image file.'); e.target.value=''; return; }
      if (file.size > 2 * 1024 * 1024) { alert('Image size must be < 2MB.'); e.target.value=''; return; }
      const reader = new FileReader();
      reader.onload = ev => { previewImg.src = ev.target.result; imagePreview.style.display = 'block'; };
      reader.readAsDataURL(file);
    });

    removeImageBtn.addEventListener('click', () => {
      imageInput.value = '';
      imagePreview.style.display = 'none';
      previewImg.src = '';
    });
  }

  function init() {
    initImagePreview();
    console.log('Admin Programs page initialized');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['admin-programs'] = () => { /* already initialized above */ };
