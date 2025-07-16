<!DOCTYPE html>
<html>
<head>
    <title>Test Global Variables</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Testing Global Chat Variables</h2>
    
    <!-- Include the chat component -->
    @include('components.global-chat')
    
    <script>
    // Test script to verify global variables are accessible
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Testing global variables accessibility:');
        console.log('myId:', typeof myId !== 'undefined' ? myId : 'UNDEFINED');
        console.log('myName:', typeof myName !== 'undefined' ? myName : 'UNDEFINED');
        console.log('isAuthenticated:', typeof isAuthenticated !== 'undefined' ? isAuthenticated : 'UNDEFINED');
        console.log('userRole:', typeof userRole !== 'undefined' ? userRole : 'UNDEFINED');
        console.log('csrfToken:', typeof csrfToken !== 'undefined' ? csrfToken : 'UNDEFINED');
        console.log('currentChatType:', typeof currentChatType !== 'undefined' ? currentChatType : 'UNDEFINED');
        console.log('currentChatUser:', typeof currentChatUser !== 'undefined' ? currentChatUser : 'UNDEFINED');
        
        // Display results on page
        document.body.innerHTML += '<h3>Test Results:</h3>';
        document.body.innerHTML += '<p>myId: ' + (typeof myId !== 'undefined' ? myId : 'UNDEFINED') + '</p>';
        document.body.innerHTML += '<p>myName: ' + (typeof myName !== 'undefined' ? myName : 'UNDEFINED') + '</p>';
        document.body.innerHTML += '<p>isAuthenticated: ' + (typeof isAuthenticated !== 'undefined' ? isAuthenticated : 'UNDEFINED') + '</p>';
        document.body.innerHTML += '<p>userRole: ' + (typeof userRole !== 'undefined' ? userRole : 'UNDEFINED') + '</p>';
        
        if (typeof myId !== 'undefined' && typeof myName !== 'undefined' && typeof isAuthenticated !== 'undefined') {
            document.body.innerHTML += '<div style="color: green; font-weight: bold;">✅ Global variables are accessible!</div>';
        } else {
            document.body.innerHTML += '<div style="color: red; font-weight: bold;">❌ Global variables are NOT accessible</div>';
        }
    });
    </script>
</body>
</html>
