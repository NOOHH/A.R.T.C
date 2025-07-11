<!DOCTYPE html>
<html>
<head>
    <title>ARTC Registration Quick Test</title>
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
    <h1>ARTC Registration Quick Test</h1>
    
    <div class="test">
        <h3>Test 1: 500 Error Fix</h3>
        <input type="file" id="testFile" accept=".jpg,.png,.pdf">
        <button onclick="testFileUpload()">Test File Upload (Should Return JSON)</button>
        <div id="fileResult" class="response"></div>
    </div>
    
    <div class="test">
        <h3>Test 2: Session Data Check</h3>
        <button onclick="checkSession()">Check Session Variables</button>
        <div id="sessionResult" class="response"></div>
    </div>
    
    <div class="test">
        <h3>Test 3: User Prefill Endpoint</h3>
        <button onclick="testPrefill()">Test Prefill Endpoint</button>
        <div id="prefillResult" class="response"></div>
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
                
                let data;
                try {
                    data = await response.json();
                    resultDiv.textContent = `✅ SUCCESS - Got JSON response!\nStatus: ${response.status}\nResponse: ${JSON.stringify(data, null, 2)}`;
                    resultDiv.className = 'response success';
                } catch (jsonError) {
                    const rawText = await response.text();
                    resultDiv.textContent = `❌ FAILED - Got HTML instead of JSON!\nStatus: ${response.status}\nJSON Error: ${jsonError.message}\nRaw Response: ${rawText.substring(0, 500)}...`;
                    resultDiv.className = 'response error';
                }
                
            } catch (error) {
                resultDiv.textContent = `❌ Network Error: ${error.message}`;
                resultDiv.className = 'response error';
            }
        }
        
        function checkSession() {
            const resultDiv = document.getElementById('sessionResult');
            
            const sessionData = {
                user_id: '@if(session("user_id")){{ session("user_id") }}@endif',
                user_name: '@if(session("user_name")){{ session("user_name") }}@endif',
                user_firstname: '@if(session("user_firstname")){{ session("user_firstname") }}@endif',
                user_lastname: '@if(session("user_lastname")){{ session("user_lastname") }}@endif',
                user_email: '@if(session("user_email")){{ session("user_email") }}@endif',
                isLoggedIn: {{ session('user_id') ? 'true' : 'false' }}
            };
            
            let status = '';
            if (sessionData.isLoggedIn) {
                status += '✅ User is logged in\n';
                status += `👤 Name: ${sessionData.user_firstname} ${sessionData.user_lastname}\n`;
                status += `📧 Email: ${sessionData.user_email}\n`;
                
                if (!sessionData.user_firstname) status += '❌ Missing user_firstname\n';
                if (!sessionData.user_lastname) status += '❌ Missing user_lastname\n';
            } else {
                status += '❌ User is NOT logged in\n';
            }
            
            resultDiv.textContent = status + '\nFull Session Data:\n' + JSON.stringify(sessionData, null, 2);
            resultDiv.className = 'response';
        }
        
        async function testPrefill() {
            const resultDiv = document.getElementById('prefillResult');
            
            try {
                resultDiv.textContent = 'Testing prefill endpoint...';
                
                const response = await fetch('/registration/user-prefill', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                let status = `Status: ${response.status}\n`;
                if (data.success) {
                    status += '✅ SUCCESS - Got user data:\n';
                    if (data.data.firstname) status += `✅ Firstname: ${data.data.firstname}\n`;
                    if (data.data.lastname) status += `✅ Lastname: ${data.data.lastname}\n`;
                    if (!data.data.firstname) status += '❌ Missing firstname\n';
                    if (!data.data.lastname) status += '❌ Missing lastname\n';
                } else {
                    status += '❌ FAILED:\n';
                }
                
                resultDiv.textContent = status + '\nFull Response:\n' + JSON.stringify(data, null, 2);
                resultDiv.className = data.success ? 'response success' : 'response error';
                
            } catch (error) {
                resultDiv.textContent = `❌ Error: ${error.message}`;
                resultDiv.className = 'response error';
            }
        }
    </script>
</body>
</html>
