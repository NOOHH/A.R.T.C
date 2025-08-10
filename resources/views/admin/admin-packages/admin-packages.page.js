// Admin Packages page script extracted from Blade inline <script>
// Uses global fetch and DOM APIs. Wrapped in IIFE to avoid leaking vars.
(() => {
  if (window.__ADMIN_PACKAGES_INIT__) return; 
  window.__ADMIN_PACKAGES_INIT__ = true;

  let selectedModules = [];
  let editSelectedModules = [];
  let selectedCourses = [];
  let editSelectedCourses = [];

  const $ = (id) => document.getElementById(id);
  const show = (el) => el && (el.style.display = 'block');
  const hide = (el) => el && (el.style.display = 'none');
  const flex = (el) => el && (el.style.display = 'flex');

  function showLoading(id){ flex($(id)); }
  function hideLoading(id){ hide($(id)); }
  function showElement(id){ show($(id)); }
  function hideElement(id){ hide($(id)); }

  function resetAddForm(){
    const form = $('addPackageForm');
    if(form) form.reset();
    selectedModules = []; selectedCourses = [];
    updateSelectedModulesDisplay();
    updateSelectedCoursesDisplay();
    ['selectionTypeGroup','selectionModeGroup','countLimitsGroup','moduleSelection','courseSelection']
      .forEach(hideElement);
  }

  window.showAddModal = function(){
    const modal = $('addPackageModal');
    modal && modal.classList.add('active');
    resetAddForm();
  };
  window.closeAddModal = function(){ const m=$('addPackageModal'); m&&m.classList.remove('active'); };
  window.showEditModal = function(){ const m=$('editPackageModal'); m&&m.classList.add('active'); };
  window.closeEditModal = function(){
    const m=$('editPackageModal'); m&&m.classList.remove('active');
    editSelectedModules=[]; editSelectedCourses=[];
    updateEditSelectedModulesDisplay();
    updateEditSelectedCoursesDisplay();
  };

  window.handlePackageTypeChange = function(){
    const type = $('package_type')?.value;
    if(type === 'modular'){
      ['selectionTypeGroup','selectionModeGroup','countLimitsGroup'].forEach(showElement);
      $('selection_type')?.setAttribute('required','required');
      handleSelectionTypeChange();
      handleSelectionModeChange();
    } else {
      ['selectionTypeGroup','selectionModeGroup','countLimitsGroup','moduleSelection','courseSelection'].forEach(hideElement);
      $('selection_type')?.removeAttribute('required');
    }
  };

  window.handleSelectionModeChange = function(){}; // reserved

  window.handleSelectionTypeChange = function(){
    const selectionType = $('selection_type')?.value;
    const programId = $('program_id')?.value;
    ['moduleSelection','courseSelection'].forEach(hideElement);
    if(!programId) return;
    if(selectionType === 'module'){ showElement('moduleSelection'); loadModulesForProgram(programId,'add'); }
    else if(selectionType === 'course'){ showElement('courseSelection'); loadCoursesForProgram(programId,'add'); }
    else if(selectionType === 'both'){ ['moduleSelection','courseSelection'].forEach(showElement); loadModulesForProgram(programId,'add'); loadCoursesForProgram(programId,'add'); }
  };

  window.handleEditPackageTypeChange = function(){
    const type = $('edit_package_type')?.value;
    if(type === 'modular'){
      ['editSelectionTypeGroup','editSelectionModeGroup'].forEach(showElement);
      $('edit_selection_type')?.setAttribute('required','required');
      handleEditSelectionTypeChange();
      handleEditSelectionModeChange();
    } else {
      ['editSelectionTypeGroup','editSelectionModeGroup','editModuleSelection','editCourseSelection'].forEach(hideElement);
      $('edit_selection_type')?.removeAttribute('required');
    }
  };

  window.handleEditSelectionModeChange = function(){};

  window.handleEditSelectionTypeChange = function(){
    const selectionType = $('edit_selection_type')?.value;
    const programId = $('edit_program_id')?.value;
    ['editModuleSelection','editCourseSelection'].forEach(hideElement);
    if(!programId) return;
    if(selectionType === 'module'){ showElement('editModuleSelection'); loadModulesForProgram(programId,'edit'); }
    else if(selectionType === 'course'){ showElement('editCourseSelection'); loadCoursesForProgram(programId,'edit'); }
    else if(selectionType === 'both'){ ['editModuleSelection','editCourseSelection'].forEach(showElement); loadModulesForProgram(programId,'edit'); loadCoursesForProgram(programId,'edit'); }
  };

  window.loadProgramData = function(){
    const programId = $('program_id')?.value;
    const selectionType = $('selection_type')?.value;
    if(!programId) return;
    if(['module','both'].includes(selectionType)) loadModulesForProgram(programId,'add');
    if(['course','both'].includes(selectionType)) loadCoursesForProgram(programId,'add');
  };
  window.loadEditProgramData = function(){
    const programId = $('edit_program_id')?.value;
    const selectionType = $('edit_selection_type')?.value;
    if(!programId) return;
    if(['module','both'].includes(selectionType)) loadModulesForProgram(programId,'edit');
    if(['course','both'].includes(selectionType)) loadCoursesForProgram(programId,'edit');
  };

  function fetchJson(url){ return fetch(url).then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); }); }

  function loadModulesForProgram(programId,mode){
    const loadingId = mode === 'add' ? 'moduleLoading':'editModuleLoading';
    const containerId = mode === 'add' ? 'moduleCheckboxes':'editModuleCheckboxes';
    const loadingEl = $(loadingId); const containerEl = $(containerId);
    if(!loadingEl || !containerEl){ console.error('Missing module elements'); return; }
    showLoading(loadingId);
    fetchJson(`/admin/get-program-modules?program_id=${programId}`)
      .then(data=>{ hideLoading(loadingId); containerEl.innerHTML=''; if(data.success && data.modules){
        data.modules.forEach(module=>{
          const courseCount = module.courses ? module.courses.length : 0;
          const div = document.createElement('div');
          div.className='checkbox-item';
          div.innerHTML=`<input type="checkbox" id="${mode}_module_${module.modules_id}" value="${module.modules_id}" onchange="handleModuleSelection(this,'${mode}')"><label for="${mode}_module_${module.modules_id}">${module.module_name}<span class='course-count'>${courseCount} courses</span></label>`;
          containerEl.appendChild(div);
        });
      } else { containerEl.innerHTML='<p class="text-muted">No modules found for this program.</p>'; } })
      .catch(err=>{ hideLoading(loadingId); console.error('Error loading modules',err); containerEl.innerHTML='<p class="text-danger">Error loading modules.</p>'; });
  }

  function loadCoursesForProgram(programId,mode){
    const loadingId = mode === 'add' ? 'courseLoading':'editCourseLoading';
    const containerId = mode === 'add' ? 'courseCheckboxes':'editCourseCheckboxes';
    const loadingEl = $(loadingId); const containerEl = $(containerId);
    if(!loadingEl || !containerEl){ console.error('Missing course elements'); return; }
    showLoading(loadingId);
    fetchJson(`/admin/get-program-modules?program_id=${programId}`)
      .then(data=>{ hideLoading(loadingId); containerEl.innerHTML=''; if(data.success && data.modules){
        let total=0; data.modules.forEach(module=>{ if(module.courses && module.courses.length){
          const header=document.createElement('div'); header.className='module-header'; header.innerHTML=`<h6 class='text-primary mb-2'><i class='fas fa-layer-group me-1'></i>${module.module_name}</h6>`; containerEl.appendChild(header);
          module.courses.forEach(course=>{ total++; const div=document.createElement('div'); div.className='checkbox-item'; div.innerHTML=`<input type='checkbox' id='${mode}_course_${course.subject_id}' value='${course.subject_id}' data-module-id='${module.modules_id}' onchange="handleCourseSelection(this,'${mode}')"><label for='${mode}_course_${course.subject_id}'>${course.subject_name}</label>`; containerEl.appendChild(div); });
        }}); if(!total) containerEl.innerHTML='<p class="text-muted">No courses found for this program.</p>'; }
        else containerEl.innerHTML='<p class="text-muted">No courses found for this program.</p>'; })
      .catch(err=>{ hideLoading(loadingId); console.error('Error loading courses',err); containerEl.innerHTML='<p class="text-danger">Error loading courses.</p>'; });
  }

  window.handleModuleSelection = function(cb,mode){
    const id=cb.value; const name=cb.nextElementSibling.textContent.trim();
    if(mode==='add') { if(cb.checked) selectedModules.push({id,name}); else selectedModules=selectedModules.filter(m=>m.id!==id); updateSelectedModulesDisplay(); }
    else { if(cb.checked) editSelectedModules.push({id,name}); else editSelectedModules=editSelectedModules.filter(m=>m.id!==id); updateEditSelectedModulesDisplay(); }
  };
  window.handleCourseSelection = function(cb,mode){
    const id=cb.value; const moduleId=cb.getAttribute('data-module-id'); const name=cb.nextElementSibling.textContent.trim();
    if(mode==='add'){ if(cb.checked) selectedCourses.push({id,name,module_id:moduleId}); else selectedCourses=selectedCourses.filter(c=>c.id!==id); updateSelectedCoursesDisplay(); }
    else { if(cb.checked) editSelectedCourses.push({id,name,module_id:moduleId}); else editSelectedCourses=editSelectedCourses.filter(c=>c.id!==id); updateEditSelectedCoursesDisplay(); }
  };

  function renderBadges(items){ return items.map(i=>`<span class="selected-badge">${i.name}</span>`).join(''); }
  function updateSelectedModulesDisplay(){ const d=$('selectedModulesDisplay'),c=$('selectedModulesCount'),l=$('selectedModulesList'); if(d&&c&&l){ c.textContent=selectedModules.length; l.innerHTML=renderBadges(selectedModules); d.style.display=selectedModules.length?'block':'none'; } }
  function updateEditSelectedModulesDisplay(){ const d=$('editSelectedModulesDisplay'),c=$('editSelectedModulesCount'),l=$('editSelectedModulesList'); if(d&&c&&l){ c.textContent=editSelectedModules.length; l.innerHTML=renderBadges(editSelectedModules); d.style.display=editSelectedModules.length?'block':'none'; } }
  function updateSelectedCoursesDisplay(){ const d=$('selectedCoursesDisplay'),c=$('selectedCoursesCount'),l=$('selectedCoursesList'); if(d&&c&&l){ c.textContent=selectedCourses.length; l.innerHTML=renderBadges(selectedCourses); d.style.display=selectedCourses.length?'block':'none'; } }
  function updateEditSelectedCoursesDisplay(){ const d=$('editSelectedCoursesDisplay'),c=$('editSelectedCoursesCount'),l=$('editSelectedCoursesList'); if(d&&c&&l){ c.textContent=editSelectedCourses.length; l.innerHTML=renderBadges(editSelectedCourses); d.style.display=editSelectedCourses.length?'block':'none'; } }

  window.editPackage = function(packageId){
    fetchJson(`/admin/packages/${packageId}`)
      .then(data=>{ if(!data.success) throw new Error(data.message||'Load error'); const p=data.package; 
        if($('edit_package_name')) $('edit_package_name').value = p.package_name||'';
        if($('edit_description')) $('edit_description').value = p.description||'';
        if($('edit_package_type')) $('edit_package_type').value = p.package_type||'full';
        if($('edit_selection_type')) $('edit_selection_type').value = p.selection_type||'module';
        if($('edit_amount')) $('edit_amount').value = p.amount||''; if($('edit_program_id')) $('edit_program_id').value = p.program_id||'';
        if($('edit_access_period_days')) $('edit_access_period_days').value = p.access_period_days||'';
        if($('edit_access_period_months')) $('edit_access_period_months').value = p.access_period_months||'';
        if($('edit_access_period_years')) $('edit_access_period_years').value = p.access_period_years||'';
        const modeCourses=$('edit_selection_mode_courses'); const modeModules=$('edit_selection_mode_modules');
        (p.selection_mode||'modules')==='courses' ? modeCourses && (modeCourses.checked=true) : modeModules && (modeModules.checked=true);
        const form=$('editPackageForm'); if(form) form.action = `/admin/packages/${packageId}`;
        handleEditPackageTypeChange(); if(p.program_id) loadEditProgramData();
        editSelectedModules = (p.modules||[]).map(m=>({id:m.modules_id,name:m.module_name}));
        editSelectedCourses = (p.courses||[]).map(c=>({id:c.subject_id,name:c.subject_name,module_id:c.module_id}));
        updateEditSelectedModulesDisplay(); updateEditSelectedCoursesDisplay(); showEditModal();
      })
      .catch(err=>{ console.error('Error loading package',err); alert('Error loading package.'); });
  };

  window.deletePackage = function(packageId){
    if(!confirm('Are you sure you want to delete this package? This action cannot be undone.')) return;
    fetch(`/admin/packages/${packageId}`,{ method:'DELETE', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type':'application/json' }})
      .then(r=>{ if(r.status===400) return r.json().then(d=>{ throw new Error(d.message||'Bad Request'); }); if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
      .then(d=>{ if(d.success){ alert('Package deleted successfully!'); location.reload(); } else alert('Error deleting package: '+(d.message||'Unknown error')); })
      .catch(e=>{ console.error('Delete error',e); alert('Error deleting package: '+e.message); });
  };

  function submitHandlerFactory(formId, selectedArrGetter, courseArrGetter, creating){
    const form=$(formId); if(!form) return; form.addEventListener('submit', e=>{ e.preventDefault(); const fd=new FormData(form); selectedArrGetter().forEach(m=>fd.append('selected_modules[]', m.id)); courseArrGetter().forEach(c=>fd.append('selected_courses[]', c.id)); const btn=form.querySelector('button[type="submit"]'); const original=btn.innerHTML; btn.disabled=true; btn.innerHTML=`<i class='fas fa-spinner fa-spin me-1'></i>${creating?'Creating...':'Updating...'}`; fetch(form.action,{method:'POST',body:fd,headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}}).then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); }).then(d=>{ if(d.success) location.reload(); else { alert('Error: '+(d.message||'Unknown')); btn.disabled=false; btn.innerHTML=original; } }).catch(err=>{ console.error('Submit error',err); alert('Error. Please try again.'); btn.disabled=false; btn.innerHTML=original; }); }); }
  submitHandlerFactory('addPackageForm', ()=>selectedModules, ()=>selectedCourses, true);
  submitHandlerFactory('editPackageForm', ()=>editSelectedModules, ()=>editSelectedCourses, false);

  const addModal=$('addPackageModal'); addModal && addModal.addEventListener('click', e=>{ if(e.target===addModal) closeAddModal(); });
  const editModal=$('editPackageModal'); editModal && editModal.addEventListener('click', e=>{ if(e.target===editModal) closeEditModal(); });
  document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ closeAddModal(); closeEditModal(); } });

  document.addEventListener('DOMContentLoaded', ()=> { console.log('Admin Packages page initialized'); });
})();

window.__PageInits = window.__PageInits || {}; 
window.__PageInits['admin-packages'] = () => { /* already auto-ran above */ };
