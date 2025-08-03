<!-- Create Meeting Modal -->
<div class="modal fade" id="createMeetingModal" tabindex="-1" aria-labelledby="createMeetingModalLabel" aria-hidden="true" style="z-index: 9999 !important;">
    <div class="modal-dialog modal-lg" style="z-index: 10000 !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createMeetingModalLabel">
                    <i class="bi bi-calendar-plus me-2"></i>Create Meeting
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createMeetingForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="meeting_professor_id" name="professor_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="meeting_title" class="form-label">Meeting Title *</label>
                            <input type="text" class="form-control" id="meeting_title" name="meeting_title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="meeting_date" class="form-label">Date & Time *</label>
                            <input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Programs *</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="programDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="programSelectionText">Select Programs</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="programDropdown" id="programs-container">
                                    <li>
                                        <div class="form-check px-3 py-2">
                                            <input class="form-check-input" type="checkbox" id="selectAllPrograms">
                                            <label class="form-check-label fw-bold" for="selectAllPrograms">
                                                Select All Programs
                                            </label>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <!-- Programs will be loaded dynamically -->
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Batches *</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="batchDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="batchSelectionText">Select Programs First</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="batchDropdown" id="batches-container">
                                    <li>
                                        <div class="form-check px-3 py-2">
                                            <input class="form-check-input" type="checkbox" id="selectAllBatches">
                                            <label class="form-check-label fw-bold" for="selectAllBatches">
                                                Select All Batches
                                            </label>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <!-- Batches will be loaded dynamically -->
                                </ul>
                            </div>
                            <div class="form-text">Batches are grouped by program. Select programs first to see available batches.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="meeting_link" class="form-label">Meeting Link</label>
                        <input type="url" class="form-control" id="meeting_link" name="meeting_link">
                        <div class="form-text">This will be auto-filled from the professor's program meeting link</div>
                    </div>

                    <div class="mb-3">
                        <label for="meeting_description" class="form-label">Description</label>
                        <textarea class="form-control" id="meeting_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-plus me-2"></i>Create Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Admin Create Meeting Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin modal script loaded');
    const createMeetingModal = document.getElementById('createMeetingModal');
    
    if (createMeetingModal) {
        console.log('Admin modal element found');
        
        // Add additional event listeners for debugging
        createMeetingModal.addEventListener('shown.bs.modal', function(event) {
            console.log('Admin modal shown event triggered');
            console.log('Modal element:', createMeetingModal);
            console.log('Modal display style:', createMeetingModal.style.display);
            console.log('Modal z-index:', createMeetingModal.style.zIndex);
        });
        
        createMeetingModal.addEventListener('show.bs.modal', function(event) {
            console.log('Admin modal show event triggered');
            const button = event.relatedTarget;
            const professorId = button.getAttribute('data-professor-id');
            const professorName = button.getAttribute('data-professor-name');
            
            console.log('Professor ID:', professorId);
            console.log('Professor Name:', professorName);
            
            document.getElementById('meeting_professor_id').value = professorId;
            document.getElementById('createMeetingModalLabel').innerHTML = 
                '<i class="bi bi-calendar-plus me-2"></i>Create Meeting for ' + professorName;
            
            // Set form action
            document.getElementById('createMeetingForm').action = `/admin/professors/${professorId}/meetings`;
            
            // Fetch professor's programs and batches
            fetch(`/admin/professors/${professorId}/batches`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populatePrograms(data.programs);
                        populateBatches(data.batches);
                        initializeDropdownHandlers();
                    } else {
                        console.error('Failed to fetch professor data:', data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching professor data:', error);
                });
        });
    } else {
        console.error('Admin modal element not found!');
    }
    
    function populatePrograms(programs) {
        const container = document.getElementById('programs-container');
        
        // Clear existing programs (keep select all)
        const existingPrograms = container.querySelectorAll('.program-checkbox-item');
        existingPrograms.forEach(item => item.remove());
        
        programs.forEach(program => {
            const li = document.createElement('li');
            li.className = 'program-checkbox-item';
            li.innerHTML = `
                <div class="form-check px-3 py-2">
                    <input class="form-check-input program-checkbox" type="checkbox" 
                           name="program_ids[]" value="${program.program_id}" 
                           id="admin_program_${program.program_id}">
                    <label class="form-check-label" for="admin_program_${program.program_id}">
                        ${program.program_name}
                    </label>
                </div>
            `;
            container.appendChild(li);
        });
    }
    
    function populateBatches(batches) {
        const container = document.getElementById('batches-container');
        
        // Clear existing batches (keep select all)
        const existingBatches = container.querySelectorAll('.batch-checkbox-item');
        existingBatches.forEach(item => item.remove());
        
        batches.forEach(batch => {
            const li = document.createElement('li');
            li.className = 'batch-checkbox-item';
            li.setAttribute('data-program-id', batch.program_id);
            li.style.display = 'none';
            li.innerHTML = `
                <div class="form-check px-3 py-2">
                    <input class="form-check-input batch-checkbox" type="checkbox" 
                           name="batch_ids[]" value="${batch.batch_id}" 
                           id="admin_batch_${batch.batch_id}">
                    <label class="form-check-label" for="admin_batch_${batch.batch_id}">
                        <span class="badge bg-primary me-2">${batch.program_name}</span>
                        ${batch.batch_name}
                    </label>
                </div>
            `;
            container.appendChild(li);
        });
    }
    
    function initializeDropdownHandlers() {
        // Prevent dropdown from closing when clicking checkboxes
        document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        
        const selectAllPrograms = document.getElementById('selectAllPrograms');
        const programCheckboxes = document.querySelectorAll('.program-checkbox');
        const programSelectionText = document.getElementById('programSelectionText');
        
        selectAllPrograms.addEventListener('change', function() {
            programCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBatchVisibility();
            updateSelectionTexts();
        });
        
        programCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBatchVisibility();
                updateSelectionTexts();
                
                // Update select all state
                const allChecked = Array.from(programCheckboxes).every(cb => cb.checked);
                const noneChecked = Array.from(programCheckboxes).every(cb => !cb.checked);
                selectAllPrograms.checked = allChecked;
                selectAllPrograms.indeterminate = !allChecked && !noneChecked;
            });
        });
        
        const selectAllBatches = document.getElementById('selectAllBatches');
        const batchCheckboxes = document.querySelectorAll('.batch-checkbox');
        
        selectAllBatches.addEventListener('change', function() {
            const visibleBatchCheckboxes = document.querySelectorAll('.batch-checkbox-item:not([style*="display: none"]) .batch-checkbox');
            visibleBatchCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionTexts();
        });
        
        batchCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectionTexts();
                
                // Update select all batches state
                const visibleBatches = document.querySelectorAll('.batch-checkbox-item:not([style*="display: none"]) .batch-checkbox');
                const allChecked = Array.from(visibleBatches).every(cb => cb.checked);
                const noneChecked = Array.from(visibleBatches).every(cb => !cb.checked);
                selectAllBatches.checked = allChecked;
                selectAllBatches.indeterminate = !allChecked && !noneChecked;
            });
        });
    }
    
    function updateBatchVisibility() {
        const selectedPrograms = Array.from(document.querySelectorAll('.program-checkbox:checked')).map(cb => cb.value);
        const batchItems = document.querySelectorAll('.batch-checkbox-item');
        
        batchItems.forEach(item => {
            const programId = item.getAttribute('data-program-id');
            if (selectedPrograms.includes(programId)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                const checkbox = item.querySelector('.batch-checkbox');
                if (checkbox) checkbox.checked = false;
            }
        });
        
        // Show batches for selected programs
        selectedPrograms.forEach(programId => {
            const programBatches = document.querySelectorAll(`.batch-checkbox-item[data-program-id="${programId}"]`);
            programBatches.forEach(item => {
                item.style.display = 'block';
            });
        });
    }
    
    function updateSelectionTexts() {
        const selectedPrograms = Array.from(document.querySelectorAll('.program-checkbox:checked'));
        const selectedBatches = Array.from(document.querySelectorAll('.batch-checkbox:checked'));
        const programSelectionText = document.getElementById('programSelectionText');
        const batchSelectionText = document.getElementById('batchSelectionText');
        
        // Update program selection text
        if (selectedPrograms.length === 0) {
            programSelectionText.textContent = 'Select Programs';
        } else if (selectedPrograms.length === 1) {
            programSelectionText.textContent = selectedPrograms[0].nextElementSibling.textContent;
        } else {
            programSelectionText.textContent = `${selectedPrograms.length} Programs Selected`;
        }
        
        // Update batch selection text
        if (selectedBatches.length === 0) {
            batchSelectionText.textContent = selectedPrograms.length > 0 ? 'Select Batches' : 'Select Programs First';
        } else if (selectedBatches.length === 1) {
            const batchLabel = selectedBatches[0].nextElementSibling.textContent.replace(/^\s*\w+\s*/, ''); // Remove badge text
            batchSelectionText.textContent = batchLabel.trim();
        } else {
            batchSelectionText.textContent = `${selectedBatches.length} Batches Selected`;
        }
    }
    
    // Set minimum date to today for datetime input
    document.addEventListener('DOMContentLoaded', function() {
        const meetingDateInput = document.getElementById('meeting_date');
        if (meetingDateInput) {
            const now = new Date();
            const minDateTime = now.toISOString().slice(0, 16);
            meetingDateInput.min = minDateTime;
        }
    });
});
</script>

<style>
/* Custom modal z-index to ensure it appears above all other elements */
#createMeetingModal {
    z-index: 99999 !important;
    position: fixed !important;
}

#createMeetingModal .modal-dialog {
    z-index: 100000 !important;
    position: relative !important;
}

/* Ensure modal backdrop has correct z-index */
.modal-backdrop.show {
    z-index: 99998 !important;
}

/* Additional styles to ensure modal visibility */
.modal.show {
    display: block !important;
    z-index: 99999 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
}

.modal.show .modal-dialog {
    z-index: 100000 !important;
    position: relative !important;
}

/* Debug styles - add a border to see if modal is visible */
#createMeetingModal {
    border: 5px solid red !important;
    background-color: rgba(255, 0, 0, 0.1) !important;
}

#createMeetingModal .modal-content {
    border: 5px solid blue !important;
    background-color: white !important;
}

/* Force modal to be visible */
.modal.fade.show {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Override any conflicting styles */
body.modal-open {
    overflow: hidden !important;
}

.modal-backdrop {
    z-index: 99998 !important;
}
</style>
