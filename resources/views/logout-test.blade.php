<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logout Test - A.R.T.C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">
                            <i class="bi bi-shield-check"></i>
                            Logout Test
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Test the logout functionality with proper CSRF protection.</p>
                        
                        <div class="alert alert-info">
                            <strong>Current Session Info:</strong><br>
                            <small>
                                CSRF Token: {{ csrf_token() }}<br>
                                Session ID: {{ session()->getId() }}<br>
                                User Type: {{ session('user_type', 'Not logged in') }}<br>
                                User ID: {{ session('user_id', 'N/A') }}
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <!-- Standard Logout Form -->
                            <form action="{{ route('student.logout') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Student Logout Route
                                </button>
                            </form>
                            
                            <!-- General Logout Route -->
                            <form action="{{ route('logout') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-door-open"></i>
                                    General Logout Route
                                </button>
                            </form>
                            
                            <!-- Professor Logout Route -->
                            @if(Route::has('professor.logout'))
                            <form action="{{ route('professor.logout') }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-person-badge"></i>
                                    Professor Logout Route
                                </button>
                            </form>
                            @endif
                            
                            <!-- AJAX Logout Test -->
                            <button type="button" class="btn btn-info w-100" onclick="ajaxLogout()">
                                <i class="bi bi-wifi"></i>
                                AJAX Logout Test
                            </button>
                        </div>
                        
                        <div id="logoutResult" class="mt-3"></div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Session Debug Info</h5>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded">
<strong>Session Data:</strong>
@foreach(session()->all() as $key => $value)
{{ $key }}: {{ is_string($value) ? $value : json_encode($value) }}
@endforeach

<strong>Environment:</strong>
APP_ENV: {{ env('APP_ENV') }}
APP_DEBUG: {{ env('APP_DEBUG') ? 'true' : 'false' }}
SESSION_LIFETIME: {{ env('SESSION_LIFETIME', 120) }} minutes
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function ajaxLogout() {
            const resultDiv = document.getElementById('logoutResult');
            resultDiv.innerHTML = '<div class="alert alert-info">Testing AJAX logout...</div>';
            
            try {
                // Create FormData instead of JSON for CSRF token
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const response = await fetch('{{ route("student.logout") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ AJAX logout successful! Redirecting...</div>';
                    setTimeout(() => {
                        window.location.href = data.redirect || '/';
                    }, 1500);
                } else {
                    const errorData = await response.text();
                    resultDiv.innerHTML = `<div class="alert alert-danger">❌ AJAX logout failed: ${response.status} ${response.statusText}<br><small>${errorData}</small></div>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger">❌ AJAX logout error: ${error.message}</div>`;
            }
        }
        
        // Show any flash messages
        @if(session('success'))
            alert('Success: {{ session("success") }}');
        @endif
        
        @if(session('error'))
            alert('Error: {{ session("error") }}');
        @endif
        
        @if(session('info'))
            alert('Info: {{ session("info") }}');
        @endif
    </script>
</body>
</html>
