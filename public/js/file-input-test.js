/**
 * Temporary JavaScript file to test file input persistence solution
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Testing file input persistence');
    
    // Create test container
    const testContainer = document.createElement('div');
    testContainer.style.border = '1px solid #ccc';
    testContainer.style.padding = '20px';
    testContainer.style.margin = '20px';
    testContainer.innerHTML = `
        <h3>File Upload Test</h3>
        <div class="form-group mb-3">
            <label>File Upload Test</label>
            <input type="file" name="test_file" id="test_file" class="form-control">
            <div id="file-status"></div>
        </div>
        <button type="button" id="simulate-upload" class="btn btn-primary">Simulate Upload</button>
    `;
    
    // Append to body
    document.body.appendChild(testContainer);
    
    // Add event handlers
    document.getElementById('simulate-upload').addEventListener('click', function() {
        const fileInput = document.getElementById('test_file');
        const fileStatus = document.getElementById('file-status');
        
        if (!fileInput.files || fileInput.files.length === 0) {
            fileStatus.innerHTML = '<div class="alert alert-warning">Please select a file first</div>';
            return;
        }
        
        // Simulate successful upload
        const fileName = fileInput.files[0].name;
        
        // Create visual indicator
        const fileContainer = fileInput.parentElement;
        
        // Create a visual replacement
        const visualReplacement = document.createElement('div');
        visualReplacement.className = 'file-visual-replacement';
        visualReplacement.style.marginTop = '10px';
        visualReplacement.style.padding = '8px';
        visualReplacement.style.border = '1px solid #28a745';
        visualReplacement.style.borderRadius = '4px';
        visualReplacement.style.backgroundColor = '#d4edda';
        visualReplacement.innerHTML = `
            <div style="display:flex; align-items:center; justify-content:space-between">
                <div>
                    <i class="bi bi-file-earmark-check"></i>
                    <strong>Selected file:</strong> ${fileName}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-x"></i> Remove
                </button>
            </div>
        `;
        
        // Add hidden field for the file path
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'test_file_path';
        hiddenInput.value = 'documents/simulated_file_path_' + fileName;
        
        // Add to container
        fileContainer.appendChild(visualReplacement);
        fileContainer.appendChild(hiddenInput);
        
        // Set data attributes on file input
        fileInput.dataset.hasUploadedFile = 'true';
        fileInput.dataset.uploadedFilePath = 'documents/simulated_file_path_' + fileName;
        
        // Optionally hide the original file input
        // fileInput.style.display = 'none';
        
        // Show success message
        fileStatus.innerHTML = '<div class="alert alert-success">File successfully "uploaded"</div>';
    });
});
