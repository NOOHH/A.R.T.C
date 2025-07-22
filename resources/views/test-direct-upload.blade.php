<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Direct File Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .debug { background: #e2e3e5; border: 1px solid #d1d1d3; color: #6c757d; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Direct File Upload Test</h1>
    <p>This form will test file upload functionality directly to help debug the issue.</p>
    
    <form id="uploadForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="program_id">Program ID:</label>
            <input type="text" id="program_id" name="program_id" value="32" required>
        </div>
        
        <div class="form-group">
            <label for="module_id">Module ID:</label>
            <input type="text" id="module_id" name="module_id" value="68" required>
        </div>
        
        <div class="form-group">
            <label for="course_id">Course ID:</label>
            <input type="text" id="course_id" name="course_id" value="36" required>
        </div>
        
        <div class="form-group">
            <label for="content_type">Content Type:</label>
            <select id="content_type" name="content_type" required>
                <option value="lesson">Lesson</option>
                <option value="assignment">Assignment</option>
                <option value="pdf">PDF</option>
                <option value="document">Document</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="content_title">Content Title:</label>
            <input type="text" id="content_title" name="content_title" value="Test Upload" required>
        </div>
        
        <div class="form-group">
            <label for="content_description">Content Description:</label>
            <textarea id="content_description" name="content_description">Test file upload description</textarea>
        </div>
        
        <div class="form-group">
            <label for="attachment">Select File to Upload:</label>
            <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.txt,.jpg,.png">
            <small>Choose a small test file (PDF, DOC, TXT, or image)</small>
        </div>
        
        <button type="submit">Upload Test File</button>
    </form>
    
    <div id="result"></div>
    
    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const resultDiv = document.getElementById('result');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
            
            // Create FormData
            const formData = new FormData(form);
            
            // Add CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('_token', token);
            
            // Debug FormData
            console.log('=== FORM DATA DEBUG ===');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`${key}: File - Name: ${value.name}, Size: ${value.size}, Type: ${value.type}`);
                } else {
                    console.log(`${key}: ${value}`);
                }
            }
            
            try {
                const response = await fetch('/admin/modules/course-content-store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const responseText = await response.text();
                console.log('Raw Response:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch {
                    result = { success: false, message: 'Invalid JSON response', rawResponse: responseText };
                }
                
                if (result.success) {
                    resultDiv.innerHTML = `
                        <div class="result success">
                            <h3>✅ Upload Successful!</h3>
                            <p><strong>Message:</strong> ${result.message}</p>
                            <div class="debug">
                                <strong>Debug Info:</strong>
                                ${JSON.stringify(result, null, 2)}
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <h3>❌ Upload Failed</h3>
                            <p><strong>Message:</strong> ${result.message || 'Unknown error'}</p>
                            <div class="debug">
                                <strong>Response:</strong>
                                ${JSON.stringify(result, null, 2)}
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="result error">
                        <h3>❌ Network Error</h3>
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Upload Test File';
            }
        });
        
        // File input change handler
        document.getElementById('attachment').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                console.log('File selected:', {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    lastModified: new Date(file.lastModified)
                });
            }
        });
    </script>
</body>
</html>
