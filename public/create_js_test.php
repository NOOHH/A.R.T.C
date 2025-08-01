<?php
// Check the final JavaScript functions

$filePath = __DIR__ . '/../resources/views/student/quiz/results.blade.php';
$content = file_get_contents($filePath);

// Extract the JavaScript
preg_match('/<script>(.*?)<\/script>/s', $content, $matches);
$jsCode = $matches[1] ?? 'No JavaScript found';

// Create a simple test page
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Test Quiz Results JavaScript</title>
    <meta name="csrf-token" content="test-token">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .code { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
        button { padding: 10px 15px; margin: 10px 0; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Test Quiz Results JavaScript</h1>
    
    <h2>JavaScript Code from results.blade.php:</h2>
    <pre class="code">{$jsCode}</pre>
    
    <h2>Test Functions:</h2>
    <button id="retake-quiz-btn" onclick="testRetakeQuiz()">Test retakeQuiz(1)</button>
    <button onclick="testGoBack()">Test goBack()</button>
    
    <script>
        // Define the functions for testing
        {$jsCode}
        
        function testRetakeQuiz() {
            // Mock form submission
            const originalSubmit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = function() {
                alert('Form would be submitted to: ' + this.action + '\nWith method: ' + this.method);
                console.log('Form would be submitted', this);
                document.getElementById('submission-details').innerText = 
                    'Form action: ' + this.action + '\n' +
                    'Form method: ' + this.method + '\n' +
                    'CSRF token: ' + this.elements['_token'].value;
            };
            
            // Call the function
            retakeQuiz(1);
            
            // Restore original submit
            setTimeout(() => {
                HTMLFormElement.prototype.submit = originalSubmit;
            }, 100);
        }
        
        function testGoBack() {
            // Mock history.back
            const originalBack = window.history.back;
            window.history.back = function() {
                alert('history.back() would be called');
            };
            
            // Call the function
            goBack();
            
            // Restore original back
            setTimeout(() => {
                window.history.back = originalBack;
            }, 100);
        }
    </script>
    
    <h2>Form Submission Details:</h2>
    <pre id="submission-details" class="code">No form submitted yet</pre>
</body>
</html>
HTML;

// Save the test page
$testPath = __DIR__ . '/test_quiz_js.html';
file_put_contents($testPath, $html);

echo "Test page created at: test_quiz_js.html\n";
echo "URL: http://localhost/A.R.T.C/public/test_quiz_js.html";
