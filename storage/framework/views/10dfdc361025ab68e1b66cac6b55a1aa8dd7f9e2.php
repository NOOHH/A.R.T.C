<!-- Simple Create Meeting Modal - Guaranteed to appear -->
<div id="simpleMeetingModal" style="display: none !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.9) !important; z-index: 999999 !important; overflow: auto !important;">
    <div style="position: relative !important; width: 90% !important; max-width: 600px !important; margin: 50px auto !important; background: white !important; border-radius: 8px !important; box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;">
        
        <!-- Header -->
        <div style="padding: 20px !important; border-bottom: 1px solid #eee !important; display: flex !important; justify-content: space-between !important; align-items: center !important; background: #f8f9fa !important;">
            <h4 style="margin: 0 !important; color: #333 !important; font-size: 18px !important;">
                <i class="bi bi-calendar-plus"></i> 
                <span id="modalTitle">Create Meeting</span>
            </h4>
            <button type="button" onclick="hideSimpleModal()" style="background: none !important; border: none !important; font-size: 24px !important; cursor: pointer !important; color: #666 !important; padding: 0 !important; width: 30px !important; height: 30px !important;">
                ×
            </button>
        </div>

        <!-- Form -->
        <form id="simpleMeetingForm" method="POST" style="margin: 0;">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="simpleProfessorId" name="professor_id">
            
            <div style="padding: 20px;">
                
                <!-- Meeting Title -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Meeting Title *</label>
                    <input type="text" id="simpleMeetingTitle" name="meeting_title" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <!-- Meeting Date -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Date & Time *</label>
                    <input type="datetime-local" id="simpleMeetingDate" name="meeting_date" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                </div>

                <!-- Programs -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Programs *</label>
                    <div id="simpleProgramsContainer" style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" id="simpleSelectAllPrograms" style="margin-right: 8px;">
                                <strong>Select All Programs</strong>
                            </label>
                        </div>
                        <hr style="margin: 10px 0;">
                        <!-- Programs will be loaded here -->
                    </div>
                </div>

                <!-- Batches -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Batches *</label>
                    <div id="simpleBatchesContainer" style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" id="simpleSelectAllBatches" style="margin-right: 8px;">
                                <strong>Select All Batches</strong>
                            </label>
                        </div>
                        <hr style="margin: 10px 0;">
                        <!-- Batches will be loaded here -->
                    </div>
                    <small style="color: #666; font-size: 12px;">Select programs first to see available batches</small>
                </div>

                <!-- Meeting Link -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Meeting Link</label>
                    <input type="url" id="simpleMeetingLink" name="meeting_link" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    <small style="color: #666; font-size: 12px;">Will be auto-filled from professor's program meeting link</small>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Description</label>
                    <textarea id="simpleMeetingDescription" name="description" rows="3" 
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"></textarea>
                </div>

            </div>

            <!-- Footer -->
            <div style="padding: 20px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px; background: #f8f9fa;">
                <button type="button" onclick="hideSimpleModal()" 
                        style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" 
                        style="padding: 8px 16px; background: #198754; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="bi bi-calendar-plus"></i> Create Meeting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Simple Modal Functions
function showSimpleModal(professorId, professorName) {
    console.log('Showing simple modal for professor:', professorId, professorName);
    
    // Find the modal
    const modal = document.getElementById('simpleMeetingModal');
    if (!modal) {
        console.error('Modal element not found!');
        alert('Modal not found! Please refresh the page.');
        return;
    }
    
    console.log('Modal found, attempting to show...');
    
    // Set modal content
    document.getElementById('simpleProfessorId').value = professorId;
    document.getElementById('modalTitle').textContent = 'Create Meeting for ' + professorName;
    document.getElementById('simpleMeetingForm').action = `/admin/professors/${professorId}/meetings`;
    
    // Show modal with extremely aggressive styling
    modal.style.cssText = `
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(0,0,0,0.9) !important;
        z-index: 2147483647 !important;
        overflow: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        transform: none !important;
        visibility: visible !important;
        opacity: 1 !important;
    `;
    
    // Also try to append to body in case it's nested too deep
    if (modal.parentElement !== document.body) {
        console.log('Moving modal to body...');
        document.body.appendChild(modal);
    }
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    
    // Set minimum date
    const now = new Date();
    const minDateTime = now.toISOString().slice(0, 16);
    document.getElementById('simpleMeetingDate').min = minDateTime;
    
    console.log('Modal should now be visible:', modal.style.display);
    console.log('Modal position:', modal.getBoundingClientRect());
    
    // Fetch professor's programs and batches
    fetch(`/admin/professors/${professorId}/batches`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateSimplePrograms(data.programs);
                populateSimpleBatches(data.batches);
                initializeSimpleHandlers();
            } else {
                console.error('Failed to fetch professor data:', data);
            }
        })
        .catch(error => {
            console.error('Error fetching professor data:', error);
        });
}

function hideSimpleModal() {
    console.log('Hiding simple modal');
    document.getElementById('simpleMeetingModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Debug function to test modal visibility
function debugModal() {
    console.log('=== Modal Debug ===');
    const modal = document.getElementById('simpleMeetingModal');
    if (modal) {
        console.log('Modal element found:', modal);
        console.log('Current styles:', {
            display: modal.style.display,
            position: modal.style.position,
            zIndex: modal.style.zIndex,
            visibility: modal.style.visibility,
            opacity: modal.style.opacity
        });
        console.log('Computed styles:', window.getComputedStyle(modal));
        console.log('Modal bounding rect:', modal.getBoundingClientRect());
        console.log('Modal parent:', modal.parentElement);
        console.log('Modal innerHTML length:', modal.innerHTML.length);
        
        // Check if modal content exists
        const modalContent = modal.querySelector('div');
        if (modalContent) {
            console.log('Modal content found:', modalContent);
            console.log('Content bounding rect:', modalContent.getBoundingClientRect());
        } else {
            console.log('No modal content found!');
        }
        
        // Force show with extreme styles and red background
        modal.style.cssText = `
            display: block !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: red !important;
            z-index: 999999 !important;
            overflow: auto !important;
            margin: 0 !important;
            padding: 0 !important;
        `;
        console.log('Modal should be red and visible now');
        console.log('After style change - bounding rect:', modal.getBoundingClientRect());
        
        // Add white text to make it more visible
        modal.innerHTML = '<div style="color: white; font-size: 48px; text-align: center; padding: 100px;">MODAL IS HERE!</div>';
        
        setTimeout(() => {
            modal.style.background = 'rgba(0,0,0,0.9) !important';
            console.log('Background changed back to dark');
            // Restore original content (you'll need to reload page)
            location.reload();
        }, 3000);
    } else {
        console.error('Modal element NOT found!');
        console.log('All elements with simpleMeetingModal:', document.querySelectorAll('#simpleMeetingModal'));
        console.log('All modal-related elements:', document.querySelectorAll('[id*="modal"], [class*="modal"]'));
    }
}

function populateSimplePrograms(programs) {
    const container = document.getElementById('simpleProgramsContainer');
    
    // Clear existing programs (keep select all)
    const existingPrograms = container.querySelectorAll('.simple-program-item');
    existingPrograms.forEach(item => item.remove());
    
    programs.forEach(program => {
        const div = document.createElement('div');
        div.className = 'simple-program-item';
        div.style.marginBottom = '8px';
        div.innerHTML = `
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" class="simple-program-checkbox" name="program_ids[]" 
                       value="${program.program_id}" style="margin-right: 8px;">
                ${program.program_name}
            </label>
        `;
        container.appendChild(div);
    });
}

function populateSimpleBatches(batches) {
    const container = document.getElementById('simpleBatchesContainer');
    
    // Clear existing batches (keep select all)
    const existingBatches = container.querySelectorAll('.simple-batch-item');
    existingBatches.forEach(item => item.remove());
    
    batches.forEach(batch => {
        const div = document.createElement('div');
        div.className = 'simple-batch-item';
        div.setAttribute('data-program-id', batch.program_id);
        div.style.display = 'none';
        div.style.marginBottom = '8px';
        div.innerHTML = `
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" class="simple-batch-checkbox" name="batch_ids[]" 
                       value="${batch.batch_id}" style="margin-right: 8px;">
                <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-right: 8px;">${batch.program_name}</span>
                ${batch.batch_name}
            </label>
        `;
        container.appendChild(div);
    });
}

function initializeSimpleHandlers() {
    // Select all programs
    const selectAllPrograms = document.getElementById('simpleSelectAllPrograms');
    const programCheckboxes = document.querySelectorAll('.simple-program-checkbox');
    
    selectAllPrograms.addEventListener('change', function() {
        programCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSimpleBatchVisibility();
    });
    
    // Individual program checkboxes
    programCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSimpleBatchVisibility();
            
            // Update select all state
            const allChecked = Array.from(programCheckboxes).every(cb => cb.checked);
            const noneChecked = Array.from(programCheckboxes).every(cb => !cb.checked);
            selectAllPrograms.checked = allChecked;
            selectAllPrograms.indeterminate = !allChecked && !noneChecked;
        });
    });
    
    // Select all batches
    const selectAllBatches = document.getElementById('simpleSelectAllBatches');
    const batchCheckboxes = document.querySelectorAll('.simple-batch-checkbox');
    
    selectAllBatches.addEventListener('change', function() {
        const visibleBatchCheckboxes = document.querySelectorAll('.simple-batch-item:not([style*="display: none"]) .simple-batch-checkbox');
        visibleBatchCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Individual batch checkboxes
    batchCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all batches state
            const visibleBatches = document.querySelectorAll('.simple-batch-item:not([style*="display: none"]) .simple-batch-checkbox');
            const allChecked = Array.from(visibleBatches).every(cb => cb.checked);
            const noneChecked = Array.from(visibleBatches).every(cb => !cb.checked);
            selectAllBatches.checked = allChecked;
            selectAllBatches.indeterminate = !allChecked && !noneChecked;
        });
    });
}

function updateSimpleBatchVisibility() {
    const selectedPrograms = Array.from(document.querySelectorAll('.simple-program-checkbox:checked')).map(cb => cb.value);
    const batchItems = document.querySelectorAll('.simple-batch-item');
    
    batchItems.forEach(item => {
        const programId = item.getAttribute('data-program-id');
        if (selectedPrograms.includes(programId)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
            const checkbox = item.querySelector('.simple-batch-checkbox');
            if (checkbox) checkbox.checked = false;
        }
    });
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('simpleMeetingModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideSimpleModal();
            }
        });
    }
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('simpleMeetingForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedPrograms = document.querySelectorAll('.simple-program-checkbox:checked');
            const selectedBatches = document.querySelectorAll('.simple-batch-checkbox:checked');
            
            if (selectedPrograms.length === 0) {
                e.preventDefault();
                alert('Please select at least one program.');
                return;
            }
            
            if (selectedBatches.length === 0) {
                e.preventDefault();
                alert('Please select at least one batch.');
                return;
            }
            
            // Disable unchecked checkboxes before submission
            document.querySelectorAll('.simple-program-checkbox:not(:checked)').forEach(cb => {
                cb.disabled = true;
            });
            document.querySelectorAll('.simple-batch-checkbox:not(:checked)').forEach(cb => {
                cb.disabled = true;
            });
        });
    }
});
</script>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/admin/professors/partials/create-meeting-modal.blade.php ENDPATH**/ ?>