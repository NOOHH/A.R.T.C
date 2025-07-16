<!DOCTYPE html>
<html>
<head>
    <title>Chat API Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        // Set global variables for testing
        window.myId = 8; // Test with user ID 8
        window.myName = 'Test User';
        window.isAuthenticated = true;
        window.userRole = 'professor';
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        var myId = window.myId;
        var myName = window.myName;
        var isAuthenticated = window.isAuthenticated;
        var userRole = window.userRole;
        var csrfToken = window.csrfToken;
        
        async function testChatAPI() {
            console.log('Testing Chat API...');
            console.log('Global vars:', { myId, myName, isAuthenticated, userRole });
            
            try {
                // Test fetching students
                console.log('1. Fetching students...');
                const studentResponse = await fetch('/api/chat/session/users?type=student', {
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'include'
                });
                const studentData = await studentResponse.json();
                console.log('Students response:', studentData);
                
                // Test fetching professors
                console.log('2. Fetching professors...');
                const professorResponse = await fetch('/api/chat/session/users?type=professor', {
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'include'
                });
                const professorData = await professorResponse.json();
                console.log('Professors response:', professorData);
                
                // Test fetching admins
                console.log('3. Fetching admins...');
                const adminResponse = await fetch('/api/chat/session/users?type=admin', {
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'include'
                });
                const adminData = await adminResponse.json();
                console.log('Admins response:', adminData);
                
            } catch (error) {
                console.error('Error testing chat API:', error);
            }
        }
        
        // Run test when page loads
        document.addEventListener('DOMContentLoaded', testChatAPI);
    </script>
</head>
<body>
    <h1>Chat API Test</h1>
    <p>Check the browser console for test results.</p>
    
    <div>
        <h3>Manual Test Buttons:</h3>
        <button onclick="testChatAPI()">Test Chat API</button>
        <button onclick="fetch('/api/chat/session/users?type=student', {headers: {'X-CSRF-TOKEN': csrfToken}, credentials: 'include'}).then(r => r.json()).then(d => console.log('Students:', d))">Get Students</button>
        <button onclick="fetch('/api/chat/session/users?type=professor', {headers: {'X-CSRF-TOKEN': csrfToken}, credentials: 'include'}).then(r => r.json()).then(d => console.log('Professors:', d))">Get Professors</button>
        <button onclick="fetch('/api/chat/session/users?type=admin', {headers: {'X-CSRF-TOKEN': csrfToken}, credentials: 'include'}).then(r => r.json()).then(d => console.log('Admins:', d))">Get Admins</button>
    </div>
    
    <!-- Include the chat component for testing -->
    @include('components.global-chat')
    
    <!-- Chat Trigger Button -->
    <button type="button" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1050;" data-bs-toggle="offcanvas" data-bs-target="#chatOffcanvas">
        <i class="bi bi-chat-dots"></i> Test Chat
    </button>
    
    <!-- Include Bootstrap for the offcanvas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
