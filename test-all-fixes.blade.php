<!DOCTYPE html>
<html>
<head>
    <title>Test All Fixes</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Testing All Fixes</h2>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Global Variables Test</h3>
                <div id="globalVarsTest">
                    <p>Testing global variables accessibility...</p>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Chat Test</h3>
                <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#chatOffcanvas">
                    Open Chat
                </button>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3>Referral Analytics Test</h3>
                <button class="btn btn-success" onclick="testReferralAPI()">Test Referral API</button>
                <div id="referralTestResult" class="mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Include the updated admin layout scripts for global variables -->
    @php
        // Get user info for global variables
        $user = Auth::user();
        
        // If Laravel Auth user is not available, fallback to session data
        if (!$user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Create a fake user object from session data for consistency
            $sessionUser = (object) [
                'id' => $_SESSION['user_id'] ?? session('user_id'),
                'name' => $_SESSION['user_name'] ?? session('user_name') ?? 'Guest',
                'role' => $_SESSION['user_type'] ?? session('user_role') ?? 'guest'
            ];
            
            // Only use session user if we have valid session data
            if ($sessionUser->id) {
                $user = $sessionUser;
            }
        }
    @endphp

    <!-- Global Variables for JavaScript - Must be loaded first -->
    <script>
        // Global variables accessible throughout the page
        window.myId = @json(optional($user)->id);
        window.myName = @json(optional($user)->name ?? 'Guest');
        window.isAuthenticated = @json((bool) $user);
        window.userRole = @json(optional($user)->role ?? 'guest');
        window.csrfToken = @json(csrf_token());
        
        // Global chat state
        window.currentChatType = null;
        window.currentChatUser = null;
        
        // Make variables available without window prefix
        var myId = window.myId;
        var myName = window.myName;
        var isAuthenticated = window.isAuthenticated;
        var userRole = window.userRole;
        var csrfToken = window.csrfToken;
        var currentChatType = window.currentChatType;
        var currentChatUser = window.currentChatUser;
        
        console.log('Global variables initialized:', { myId, myName, isAuthenticated, userRole });
    </script>

    <!-- Include global chat component -->
    @include('components.global-chat')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Test global variables
        const globalVarsDiv = document.getElementById('globalVarsTest');
        
        if (typeof myId !== 'undefined') {
            globalVarsDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>✅ Global variables working!</strong><br>
                    myId: ${myId}<br>
                    myName: ${myName}<br>
                    isAuthenticated: ${isAuthenticated}<br>
                    userRole: ${userRole}
                </div>
            `;
        } else {
            globalVarsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ Global variables not working!</strong><br>
                    myId is undefined
                </div>
            `;
        }
    });

    function testReferralAPI() {
        const resultDiv = document.getElementById('referralTestResult');
        resultDiv.innerHTML = '<div class="alert alert-info">Testing referral API...</div>';
        
        fetch('/api/referral/analytics', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <strong>✅ Referral API working!</strong><br>
                        Total Referrals: ${data.total_referrals || 0}<br>
                        Top Referrers: ${data.top_referrers ? data.top_referrers.length : 0}
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <strong>⚠️ API Response:</strong><br>
                        ${JSON.stringify(data)}
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ API Error:</strong><br>
                    ${error.message}
                </div>
            `;
        });
    }
    </script>
</body>
</html>
