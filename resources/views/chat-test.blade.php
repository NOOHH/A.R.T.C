<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Chat System Test</h3>
                    </div>
                    <div class="card-body">
                        <p>Click the chat button to test the chat functionality:</p>
                        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#chatOffcanvas">
                            <i class="bi bi-chat-dots"></i> Open Chat
                        </button>
                        
                        <div class="mt-3">
                            <h5>Test Instructions:</h5>
                            <ol>
                                <li>Click "Open Chat" button</li>
                                <li>Choose a user type (Students, Professors, etc.)</li>
                                <li>Use the search functionality to find users</li>
                                <li>Try chatting with different users</li>
                                <li>Test the FAQ Bot functionality</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the chat component -->
    @include('components.global-chat')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
