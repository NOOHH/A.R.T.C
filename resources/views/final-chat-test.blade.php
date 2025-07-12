<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Final Chat Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Final Chat System Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Professor Search Test</h3>
                    </div>
                    <div class="card-body">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search professors..." value="robert">
                        <button class="btn btn-primary mt-2" onclick="searchProfessors()">Search Professors</button>
                        <button class="btn btn-secondary mt-2" onclick="searchAll()">Search All Users</button>
                        <div id="searchResults" class="mt-3"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Message Test</h3>
                    </div>
                    <div class="card-body">
                        <input type="number" id="receiverId" class="form-control mb-2" placeholder="Receiver ID" value="1">
                        <input type="text" id="messageText" class="form-control mb-2" placeholder="Message" value="Hello from admin">
                        <button class="btn btn-success" onclick="sendMessage()">Send Message</button>
                        <div id="messageResults" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchProfessors() {
            const search = document.getElementById('searchInput').value;
            fetch(`/api/chat/session/search/professors?search=${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('searchResults').innerHTML = 
                        '<div class="alert alert-info"><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                })
                .catch(error => {
                    document.getElementById('searchResults').innerHTML = 
                        '<div class="alert alert-danger">Error: ' + error.message + '</div>';
                });
        }
        
        function searchAll() {
            const search = document.getElementById('searchInput').value;
            fetch(`/api/chat/session/users?type=all&q=${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('searchResults').innerHTML = 
                        '<div class="alert alert-info"><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                })
                .catch(error => {
                    document.getElementById('searchResults').innerHTML = 
                        '<div class="alert alert-danger">Error: ' + error.message + '</div>';
                });
        }
        
        function sendMessage() {
            const receiverId = document.getElementById('receiverId').value;
            const messageText = document.getElementById('messageText').value;
            
            fetch('/api/chat/session/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: messageText
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('messageResults').innerHTML = 
                    '<div class="alert alert-info"><pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
            })
            .catch(error => {
                document.getElementById('messageResults').innerHTML = 
                    '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            });
        }
        
        // Auto-test on page load
        document.addEventListener('DOMContentLoaded', function() {
            searchProfessors();
        });
    </script>
</body>
</html>
