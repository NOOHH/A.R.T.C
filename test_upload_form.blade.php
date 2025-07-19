<!DOCTYPE html>
<html>
<head>
    <title>Upload Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Simple Upload Test</h1>
    
    <form action="/admin/modules/course-content-store" method="POST" enctype="multipart/form-data" id="testForm">
        @csrf
        
        <p>Program: 
            <select name="program_id" required>
                <option value="32">Engineer</option>
            </select>
        </p>
        
        <p>Module: 
            <select name="module_id" required>
                <option value="40">Modules 1</option>
            </select>
        </p>
        
        <p>Course: 
            <select name="course_id" required>
                <option value="1">Test Course</option>
            </select>
        </p>
        
        <p>Content Type: 
            <select name="content_type" required>
                <option value="lesson">Lesson</option>
                <option value="assignment">Assignment</option>
            </select>
        </p>
        
        <p>Title: <input type="text" name="content_title" value="Test Content" required></p>
        
        <p>Description: <textarea name="content_description">Test description</textarea></p>
        
        <p>File: <input type="file" name="attachment"></p>
        
        <p><button type="submit">Submit Test</button></p>
    </form>
    
    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            console.log('Submitting form...');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text();
            })
            .then(data => {
                console.log('Response data:', data);
                try {
                    const jsonData = JSON.parse(data);
                    console.log('Parsed JSON:', jsonData);
                    if (jsonData.success) {
                        alert('Success: ' + jsonData.message);
                    } else {
                        alert('Error: ' + jsonData.message);
                    }
                } catch (e) {
                    console.error('Response is not JSON:', data);
                    alert('Response is not JSON. Check console.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Fetch error: ' + error.message);
            });
        });
    </script>
</body>
</html>
