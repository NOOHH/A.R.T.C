<!DOCTYPE html>
<html>
<head>
    <title>Session Debug Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Session Debug Test</h1>
    
    <h2>Session Data:</h2>
    <pre>
User ID: {{ session('user_id') ?? 'null' }}
User Name: {{ session('user_name') ?? 'null' }}
User Firstname: {{ session('user_firstname') ?? 'null' }}
User Lastname: {{ session('user_lastname') ?? 'null' }}
User Email: {{ session('user_email') ?? 'null' }}
User Role: {{ session('user_role') ?? 'null' }}
Logged In: {{ session('logged_in') ?? 'null' }}
    </pre>
    
    <h2>JavaScript Session Variables:</h2>
    <script>
        const isUserLoggedIn = @if(session('user_id')) true @else false @endif;
        const loggedInUserId = '@if(session("user_id")){{ session("user_id") }}@endif';
        const loggedInUserName = '@if(session("user_name")){{ session("user_name") }}@endif';
        const loggedInUserFirstname = '@if(session("user_firstname")){{ session("user_firstname") }}@endif';
        const loggedInUserLastname = '@if(session("user_lastname")){{ session("user_lastname") }}@endif';
        const loggedInUserEmail = '@if(session("user_email")){{ session("user_email") }}@endif';
        
        console.log('Session check:', {
            isUserLoggedIn,
            loggedInUserId,
            loggedInUserName,
            loggedInUserFirstname,
            loggedInUserLastname,
            loggedInUserEmail
        });
        
        document.write('<pre>');
        document.write('Is User Logged In: ' + isUserLoggedIn + '\n');
        document.write('User ID: ' + loggedInUserId + '\n');
        document.write('User Name: ' + loggedInUserName + '\n');
        document.write('User Firstname: ' + loggedInUserFirstname + '\n');
        document.write('User Lastname: ' + loggedInUserLastname + '\n');
        document.write('User Email: ' + loggedInUserEmail + '\n');
        document.write('</pre>');
    </script>
    
    <h2>Test File Upload</h2>
    <form id="testForm" enctype="multipart/form-data">
        <input type="file" id="testFile" name="file" accept=".jpg,.jpeg,.png,.pdf">
        <button type="button" onclick="testFileUpload()">Test Upload</button>
    </form>
    
    <div id="result"></div>
    
    <script>
        function testFileUpload() {
            const fileInput = document.getElementById('testFile');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Please select a file first');
                return;
            }
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('field_name', 'test_field');
            formData.append('first_name', 'Test');
            formData.append('last_name', 'User');
            
            fetch('/registration/validate-file', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response:', data);
                document.getElementById('result').innerHTML = '<pre>' + data + '</pre>';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('result').innerHTML = '<pre>Error: ' + error + '</pre>';
            });
        }
    </script>
</body>
</html>
