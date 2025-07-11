<!DOCTYPE html>
<html>
<head>
    <title>ARTC Registration Debug Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; margin: 20px; }
        .test { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        button { margin: 5px; padding: 8px 15px; }
        .response { background: #f8f9fa; padding: 10px; margin: 10px 0; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>ARTC Registration System Debug Test</h1>
    
    <div class="test">
        <h3>Test 1: File Upload Endpoint</h3>
        <input type="file" id="testFile" accept=".jpg,.png,.pdf">
        <button onclick="testFileUpload()">Test File Upload</button>
        <div id="fileResult" class="response"></div>
    </div>
    
    <div class="test">
        <h3>Test 2: User Prefill Endpoint</h3>
        <button onclick="testUserPrefill()">Test User Prefill</button>
        <div id="prefillResult" class="response"></div>
    </div>
    
    <div class="test">
        <h3>Test 3: Session Check</h3>
        <button onclick="testSession()">Check Session</button>
        <div id="sessionResult" class="response"></div>
    </div>

    <script>
        async function testFileUpload() {
            const fileInput = document.getElementById('testFile');
            const resultDiv = document.getElementById('fileResult');
            
            if (!fileInput.files[0]) {
                resultDiv.textContent = 'Please select a file first';
                return;
            }
            
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('field_name', 'test_field');
            formData.append('first_name', 'Test');
            formData.append('last_name', 'User');
            
            try {
                resultDiv.textContent = 'Testing...';
                
                const response = await fetch('/registration/validate-file', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                resultDiv.textContent = `Status: ${response.status}\nResponse: ${JSON.stringify(data, null, 2)}`;
                resultDiv.className = data.success ? 'response success' : 'response error';
                
            } catch (error) {
                resultDiv.textContent = `Error: ${error.message}`;
                resultDiv.className = 'response error';
            }
        }
        
        async function testUserPrefill() {
            const resultDiv = document.getElementById('prefillResult');
            
            try {
                resultDiv.textContent = 'Testing...';
                
                const response = await fetch('/registration/user-prefill', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                resultDiv.textContent = `Status: ${response.status}\nResponse: ${JSON.stringify(data, null, 2)}`;
                resultDiv.className = data.success ? 'response success' : 'response error';
                
            } catch (error) {
                resultDiv.textContent = `Error: ${error.message}`;
                resultDiv.className = 'response error';
            }
        }
        
        function testSession() {
            const resultDiv = document.getElementById('sessionResult');
            
            const sessionData = {
                user_id: '@if(session("user_id")){{ session("user_id") }}@endif',
                user_name: '@if(session("user_name")){{ session("user_name") }}@endif',
                user_firstname: '@if(session("user_firstname")){{ session("user_firstname") }}@endif',
                user_lastname: '@if(session("user_lastname")){{ session("user_lastname") }}@endif',
                user_email: '@if(session("user_email")){{ session("user_email") }}@endif',
                isLoggedIn: {{ session('user_id') ? 'true' : 'false' }}
            };
            
            resultDiv.textContent = JSON.stringify(sessionData, null, 2);
            resultDiv.className = 'response';
        }
    </script>
</body>
</html>
