<!DOCTYPE html>
<html>
<head>
    <title>Direct Course Content Upload Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Direct Course Content Upload Test</h1>
    
    <h2>Test 1: Simple Form (No JavaScript)</h2>
    <form action="{{ route('admin.modules.course-content-store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div>
            <label>Program ID:</label>
            <input type="number" name="program_id" value="33" required>
        </div>
        
        <div>
            <label>Module ID:</label>
            <input type="number" name="module_id" value="74" required>
        </div>
        
        <div>
            <label>Course ID:</label>
            <input type="number" name="course_id" value="39" required>
        </div>
        
        <div>
            <label>Content Type:</label>
            <select name="content_type" required>
                <option value="lesson">Lesson</option>
                <option value="quiz">Quiz</option>
            </select>
        </div>
        
        <div>
            <label>Content Title:</label>
            <input type="text" name="content_title" value="Direct Test Upload" required>
        </div>
        
        <div>
            <label>File:</label>
            <input type="file" name="attachment" required>
            <small>Select any file to test upload</small>
        </div>
        
        <div>
            <button type="submit">Submit Direct Upload</button>
        </div>
    </form>

    <h2>Debug Info</h2>
    <p><strong>CSRF Token:</strong> {{ csrf_token() }}</p>
    <p><strong>Route URL:</strong> {{ route('admin.modules.course-content-store') }}</p>
    
    <script>
        // Simple form monitoring
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Direct form submission detected');
            console.log('Action:', this.action);
            console.log('Method:', this.method);
            console.log('Enctype:', this.enctype);
            
            const formData = new FormData(this);
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`${key}:`, {
                        name: value.name,
                        size: value.size,
                        type: value.type
                    });
                } else {
                    console.log(`${key}:`, value);
                }
            }
            
            // Let the form submit normally - no preventDefault()
        });
    </script>
    
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        div { margin: 10px 0; }
        label { display: block; font-weight: bold; }
        input, select { width: 300px; padding: 5px; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer; }
    </style>
</body>
</html>
