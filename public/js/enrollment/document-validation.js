// Function to handle document upload and OCR validation
function handleDocumentUpload(inputElement, documentType) {
    const file = inputElement.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('document', file);
    formData.append('documentType', documentType);
    formData.append('firstName', document.querySelector('[name="firstname"]').value);
    formData.append('lastName', document.querySelector('[name="lastname"]').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    // Show loading spinner
    showSpinner(`${documentType}Loading`);

    fetch('/registration/validate-document', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideSpinner(`${documentType}Loading`);
        
        if (data.error) {
            showError(documentType, data.message);
            return;
        }

        if (data.success) {
            showSuccess(documentType);
            if (data.suggestions && data.suggestions.length > 0) {
                showProgramSuggestions(data.suggestions);
            }
        }
    })
    .catch(error => {
        hideSpinner(`${documentType}Loading`);
        showError(documentType, 'An error occurred while processing the document');
        console.error('Error:', error);
    });
}

// Function to show program suggestions
function showProgramSuggestions(suggestions) {
    const container = document.getElementById('programSuggestions');
    if (!container) return;

    container.innerHTML = `
        <div class="suggestions-header">
            <h4>Recommended Programs</h4>
            <p>Based on your uploaded documents</p>
        </div>
        <div class="suggestions-list">
            ${suggestions.map(suggestion => `
                <div class="program-suggestion" onclick="selectProgram('${suggestion.program.id}')">
                    <h5>${suggestion.program.name}</h5>
                    <p class="reason">${suggestion.reason}</p>
                    <div class="relevance-score">
                        Relevance: ${Array(Math.min(5, Math.ceil(suggestion.score))).fill('⭐').join('')}
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    container.style.display = 'block';
}

// Function to load and display available batches
function loadBatchesForProgram() {
    const programId = document.getElementById('programSelect').value;
    const learningMode = document.getElementById('learning_mode').value;
    
    if (!programId || learningMode !== 'synchronous') {
        return;
    }

    fetch(`/api/batches/${programId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('batchSelectionContainer');
            if (!container) return;

            container.innerHTML = `
                <h4>Available Batches</h4>
                <div class="batch-list">
                    ${data.map(batch => `
                        <div class="batch-option ${batch.batch_status === 'closed' ? 'closed' : ''}" 
                             onclick="${batch.batch_status !== 'closed' ? `selectBatch(${batch.id})` : ''}">
                            <div class="batch-header">
                                <h5>Batch ${batch.batch_name}</h5>
                                <span class="status-badge ${batch.batch_status}">
                                    ${batch.batch_status.charAt(0).toUpperCase() + batch.batch_status.slice(1)}
                                </span>
                            </div>
                            <div class="batch-details">
                                <p class="students-count">${batch.current_students}/${batch.max_students} students</p>
                                ${batch.enrollment_deadline ? 
                                    `<p class="deadline">Enrollment Deadline: ${new Date(batch.enrollment_deadline).toLocaleDateString()}</p>` 
                                    : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            container.style.display = 'block';
        })
        .catch(error => {
            console.error('Error loading batches:', error);
        });
}

// Function to select a batch
function selectBatch(batchId) {
    document.querySelectorAll('.batch-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    const selectedOption = document.querySelector(`.batch-option[onclick*="${batchId}"]`);
    if (selectedOption) {
        selectedOption.classList.add('selected');
    }
    
    document.getElementById('selectedBatchId').value = batchId;
    
    // Enable next button if batch is selected
    const nextBtn = document.getElementById('programNextBtn');
    if (nextBtn) {
        nextBtn.disabled = false;
        nextBtn.classList.add('enabled');
        nextBtn.classList.remove('disabled');
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.style.pointerEvents = 'auto';
    }
}

// Utility functions for showing/hiding loading spinners
function showSpinner(id) {
    const spinner = document.getElementById(id);
    if (spinner) spinner.style.display = 'block';
}

function hideSpinner(id) {
    const spinner = document.getElementById(id);
    if (spinner) spinner.style.display = 'none';
}

function showError(documentType, message) {
    const errorDiv = document.getElementById(`${documentType}Error`);
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

function showSuccess(documentType) {
    const successDiv = document.getElementById(`${documentType}Success`);
    if (successDiv) {
        successDiv.style.display = 'block';
    }
}
