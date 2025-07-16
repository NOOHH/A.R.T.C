<!DOCTYPE html>
<html>
<head>
    <title>Session Fix Test</title>
</head>
<body>
    <h2>Testing Session Variables in Global Chat Context</h2>
    
    @php
        // Start PHP session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Simulate logged in admin for testing
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Test Admin';
        $_SESSION['user_type'] = 'admin';
        $_SESSION['logged_in'] = true;
        
        // Use PHP session as primary, Laravel session as fallback
        $currentUserId = $_SESSION['user_id'] ?? session('user_id');
        $currentUserName = $_SESSION['user_name'] ?? session('user_name') ?? 'Guest';
        $currentUserAuth = $_SESSION['logged_in'] ?? session('logged_in') ?? false;
        $currentUserRole = $_SESSION['user_type'] ?? session('user_role') ?? 'guest';
    @endphp

    <script>
    // Global variables (same as in chat component)
    let myId = @json($currentUserId);
    let myName = @json($currentUserName);
    const isAuthenticated = @json($currentUserAuth);
    const userRole = @json($currentUserRole);

    console.log('Session Test Results:', {
        myId: myId,
        myName: myName,
        isAuthenticated: isAuthenticated,
        userRole: userRole
    });

    // Display on page
    document.addEventListener('DOMContentLoaded', function() {
        document.body.innerHTML += '<h3>JavaScript Variables:</h3>';
        document.body.innerHTML += '<p>myId: ' + myId + '</p>';
        document.body.innerHTML += '<p>myName: ' + myName + '</p>';
        document.body.innerHTML += '<p>isAuthenticated: ' + isAuthenticated + '</p>';
        document.body.innerHTML += '<p>userRole: ' + userRole + '</p>';
        
        if (myId && isAuthenticated) {
            document.body.innerHTML += '<div style="color: green; font-weight: bold;">✅ Session Fix Working!</div>';
        } else {
            document.body.innerHTML += '<div style="color: red; font-weight: bold;">❌ Session Fix Not Working</div>';
        }
    });
    </script>

    <h3>PHP Session Variables:</h3>
    <p>user_id: {{ $_SESSION['user_id'] ?? 'Not set' }}</p>
    <p>user_name: {{ $_SESSION['user_name'] ?? 'Not set' }}</p>
    <p>user_type: {{ $_SESSION['user_type'] ?? 'Not set' }}</p>
    <p>logged_in: {{ $_SESSION['logged_in'] ?? 'Not set' }}</p>
</body>
</html>
