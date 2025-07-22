<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 300px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .result { margin-top: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 4px; background: #f9f9f9; }
        .success { border-color: #4caf50; background: #f1f8e9; }
        .error { border-color: #f44336; background: #fdf2f2; }
    </style>
</head>
<body>
    <h1>File Upload Test</h1>
    
    <form id="testUploadForm" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label>Program:</label>
            <select name="program_id" required>
                <option value="">-- Select Program --</option>
                @foreach(DB::table('programs')->get() as $program)
                    <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Module:</label>
            <select name="module_id" required>
                <option value="">-- Select Module --</option>
                @foreach(DB::table('modules')->get() as $module)
                    <option value="{{ $module->modules_id }}">{{ $module->module_name }} (Program: {{ $module->program_id }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Course:</label>
            <select name="course_id" required>
                <option value="">-- Select Course --</option>
                @foreach(DB::table('courses')->get() as $course)
                    <option value="{{ $course->subject_id }}">{{ $course->subject_name }} (Module: {{ $course->module_id }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Content Type:</label>
            <select name="content_type" required>
                <option value="">-- Select Type --</option>
                <option value="lesson">Lesson</option>
                <option value="document">Document</option>
                <option value="assignment">Assignment</option>
            </select>
        </div>

        <div class="form-group">
            <label>Content Title:</label>
            <input type="text" name="content_title" required>
        </div>

        <div class="form-group">
            <label>Content Description:</label>
            <textarea name="content_description" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label>File Attachment:</label>
            <input type="file" name="attachment" accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg">
        </div>

        <button type="submit">Upload Content</button>
    </form>

    <div id="result"></div>

    <script>
        document.getElementById('testUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const resultDiv = document.getElementById('result');
            
            // Show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
            resultDiv.innerHTML = '<p>Uploading file...</p>';
            
            // Debug form data
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                if (key === 'attachment' && value instanceof File) {
                    console.log(`${key}:`, {
                        name: value.name,
                        size: value.size,
                        type: value.type
                    });
                } else {
                    console.log(`${key}:`, value);
                }
            }
            
            try {
                const response = await fetch('/admin/modules/course-content-store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const responseText = await response.text();
                console.log('Response status:', response.status);
                console.log('Response text:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error(`Invalid JSON response: ${responseText.substring(0, 200)}`);
                }
                
                if (data.success) {
                    resultDiv.className = 'result success';
                    resultDiv.innerHTML = `
                        <h3>Success!</h3>
                        <p><strong>Message:</strong> ${data.message}</p>
                        <p><strong>Content ID:</strong> ${data.content_item?.id || 'N/A'}</p>
                        <p><strong>Attachment Path:</strong> ${data.debug_info?.attachment_path || 'None'}</p>
                        <p><strong>Public URL:</strong> ${data.debug_info?.public_url || 'None'}</p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <h3>Error</h3>
                        <p><strong>Message:</strong> ${data.message}</p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                }
                
            } catch (error) {
                console.error('Upload error:', error);
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `
                    <h3>Upload Error</h3>
                    <p>${error.message}</p>
                `;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Upload Content';
            }
        });
    </script>
</body>
</html>
