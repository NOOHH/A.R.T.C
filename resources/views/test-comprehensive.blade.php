<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database & Content Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .loading { background-color: #fff3cd; border-color: #ffeaa7; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Database & Content System Test</h1>

    <div id="db-test" class="test-section loading">
        <h3>Database Tables Test</h3>
        <p>Testing database tables and structure...</p>
        <div id="db-results"></div>
    </div>

    <div id="content-test" class="test-section loading">
        <h3>Content View Test</h3>
        <p>Testing content view functionality...</p>
        <div id="content-results"></div>
    </div>

    <div id="auth-test" class="test-section loading">
        <h3>Authentication & Session Test</h3>
        <p>Testing authentication and session handling...</p>
        <div id="auth-results"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Test database
            $.get('/test-db-comprehensive')
                .done(function(data) {
                    $('#db-test').removeClass('loading').addClass('success');
                    $('#db-results').html('<pre>' + JSON.stringify(data, null, 2) + '</pre>');
                })
                .fail(function(xhr) {
                    $('#db-test').removeClass('loading').addClass('error');
                    $('#db-results').html('<pre>Error: ' + xhr.responseText + '</pre>');
                });

            // Test content view
            $.get('/student/content/78')
                .done(function(data) {
                    $('#content-test').removeClass('loading').addClass('success');
                    $('#content-results').html('<pre>' + JSON.stringify(data, null, 2) + '</pre>');
                })
                .fail(function(xhr) {
                    $('#content-test').removeClass('loading').addClass('error');
                    $('#content-results').html('<pre>Error: ' + xhr.responseText + '</pre>');
                });

            // Test session/auth
            $.get('/debug-session')
                .done(function(data) {
                    $('#auth-test').removeClass('loading').addClass('success');
                    $('#auth-results').html('<pre>' + JSON.stringify(data, null, 2) + '</pre>');
                })
                .fail(function(xhr) {
                    $('#auth-test').removeClass('loading').addClass('error');
                    $('#auth-results').html('<pre>Error: ' + xhr.responseText + '</pre>');
                });
        });
    </script>
</body>
</html>
