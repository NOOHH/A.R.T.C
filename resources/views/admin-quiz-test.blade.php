<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Quiz Management Test</title>
    <meta name="csrf-token" content="{{ $csrfToken }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Admin Quiz Management Test</h1>
        
        <div class="alert alert-info">
            <h5>Testing Admin Quiz Interface</h5>
            <p>This page tests the admin quiz management functionality directly.</p>
            <p><strong>Admin ID:</strong> {{ session('user_id', 'Not set') }}</p>
            <p><strong>CSRF Token:</strong> {{ substr($csrfToken, 0, 20) }}...</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Test Status Change Functions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Test with Admin Quiz (ID: 46)</h6>
                        <button class="btn btn-success btn-sm me-2" onclick="testStatusChange(46, 'published')">
                            <i class="bi bi-check-circle"></i> Publish
                        </button>
                        <button class="btn btn-secondary btn-sm me-2" onclick="testStatusChange(46, 'archived')">
                            <i class="bi bi-archive"></i> Archive
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="testStatusChange(46, 'draft')">
                            <i class="bi bi-file-text"></i> Draft
                        </button>
                    </div>
                    <div class="col-md-6">
                        <h6>Test with Professor Quiz (ID: 48)</h6>
                        <button class="btn btn-success btn-sm me-2" onclick="testStatusChange(48, 'published')">
                            <i class="bi bi-check-circle"></i> Publish
                        </button>
                        <button class="btn btn-secondary btn-sm me-2" onclick="testStatusChange(48, 'archived')">
                            <i class="bi bi-archive"></i> Archive
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="testStatusChange(48, 'draft')">
                            <i class="bi bi-file-text"></i> Draft
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Test Results:</h6>
                    <div id="testResults" class="border p-3 bg-light" style="min-height: 200px; font-family: monospace; white-space: pre-wrap;"></div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-secondary btn-sm" onclick="clearResults()">
                        <i class="bi bi-trash"></i> Clear Results
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Direct Quiz Manager Access</h5>
            </div>
            <div class="card-body">
                <a href="/admin/quiz-generator" class="btn btn-primary" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Open Admin Quiz Generator
                </a>
                <a href="/professor/quiz-generator" class="btn btn-info" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Open Professor Quiz Generator (for comparison)
                </a>
            </div>
        </div>
    </div>

    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function log(message) {
            const results = document.getElementById('testResults');
            results.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '\n';
            results.scrollTop = results.scrollHeight;
        }

        function clearResults() {
            document.getElementById('testResults').innerHTML = '';
        }

        async function testStatusChange(quizId, newStatus) {
            log(`Testing status change for Quiz ${quizId} to '${newStatus}'...`);
            
            const endpoints = {
                'published': `/admin/quiz-generator/${quizId}/publish`,
                'archived': `/admin/quiz-generator/${quizId}/archive`,
                'draft': `/admin/quiz-generator/${quizId}/draft`
            };
            
            const endpoint = endpoints[newStatus];
            if (!endpoint) {
                log(`ERROR: Unknown status '${newStatus}'`);
                return;
            }
            
            try {
                log(`Making request to: ${endpoint}`);
                
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                log(`Response status: ${response.status} ${response.statusText}`);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    log(`ERROR Response: ${errorText.substring(0, 500)}...`);
                    return;
                }
                
                const data = await response.json();
                log(`SUCCESS: ${JSON.stringify(data)}`);
                
            } catch (error) {
                log(`FETCH ERROR: ${error.message}`);
                console.error('Full error:', error);
            }
        }

        // Test authentication on page load
        window.addEventListener('load', function() {
            log('Page loaded - Admin Quiz Management Test initialized');
            log('CSRF Token: ' + csrfToken.substring(0, 20) + '...');
            log('Ready to test quiz status changes');
            log('');
        });
    </script>
</body>
</html>
