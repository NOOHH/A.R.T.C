<!DOCTYPE html>
<html>
<head>
    <title>Test Admin Quiz Generator Link</title>
</head>
<body>
    <h1>Testing Admin Quiz Generator Access</h1>
    
    <p>Route URL: <code>{{ route('admin.quiz-generator') }}</code></p>
    
    <p>
        <a href="{{ route('admin.quiz-generator') }}" target="_blank" style="
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        ">
            <i class="bi bi-robot"></i> Test AI Quiz Generator Link
        </a>
    </p>
    
    <p>If the link above doesn't work, try this direct URL:</p>
    <p><a href="http://127.0.0.1:8000/admin/quiz-generator" target="_blank">http://127.0.0.1:8000/admin/quiz-generator</a></p>
    
    <script>
        // Test if route exists via JavaScript
        console.log('Testing route:', '{{ route("admin.quiz-generator") }}');
        
        // Test if we can access the route
        fetch('{{ route("admin.quiz-generator") }}', {
            method: 'HEAD'
        }).then(response => {
            console.log('Route status:', response.status);
            if (response.status === 200) {
                console.log('✓ Route is accessible');
            } else {
                console.log('✗ Route returned status:', response.status);
            }
        }).catch(error => {
            console.error('✗ Route test failed:', error);
        });
    </script>
</body>
</html>
