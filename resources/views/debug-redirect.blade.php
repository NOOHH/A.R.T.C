<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Redirect</title>
    <script>
    function testRedirect() {
        const contentId = document.getElementById('contentId').value;
        console.log('Testing redirect to content ID:', contentId);
        
        try {
            window.location.href = '/student/content/' + contentId + '/view';
        } catch (e) {
            console.error('Error during redirect:', e);
            document.getElementById('error').innerText = e.message;
        }
    }
    </script>
</head>
<body>
    <h1>Debug Redirect Test</h1>
    <div>
        <input type="text" id="contentId" value="80" placeholder="Content ID">
        <button onclick="testRedirect()">Test Redirect</button>
    </div>
    <div id="error" style="color: red;"></div>
    
    <h2>Session Debug</h2>
    <pre>{{ json_encode(session()->all(), JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Authentication Check</h2>
    <ul>
        <li>user_id: {{ session('user_id') ?? 'Not set' }}</li>
        <li>user_role: {{ session('user_role') ?? 'Not set' }}</li>
        <li>role: {{ session('role') ?? 'Not set' }}</li>
        <li>logged_in: {{ session('logged_in') ? 'Yes' : 'No' }}</li>
    </ul>
</body>
</html>
