<?php
// debug_results_blade.php
// This file is used to diagnose and fix issues in the results.blade.php file

// Get the file contents
$filePath = __DIR__ . '/../resources/views/student/quiz/results.blade.php';
$content = file_get_contents($filePath);

echo "<h1>Debug Results.blade.php</h1>";

// Make a backup
$backupPath = __DIR__ . '/../resources/views/student/quiz/results.blade.php.bak';
file_put_contents($backupPath, $content);
echo "<p>Backup created at: $backupPath</p>";

// Find the script section and replace it
$scriptStart = strpos($content, "@push('scripts')");
$scriptEnd = strpos($content, "@endpush", $scriptStart);

if ($scriptStart !== false && $scriptEnd !== false) {
    echo "<p>Found script section from position $scriptStart to $scriptEnd</p>";
    
    // Extract the script section to display
    $scriptSection = substr($content, $scriptStart, $scriptEnd + 8 - $scriptStart);
    echo "<h2>Original Script Section:</h2>";
    echo "<pre>" . htmlspecialchars($scriptSection) . "</pre>";
    
    // Create new script section
    $newScriptSection = "@push('scripts')
<script>
    // Function to handle retaking the quiz with proper POST request
    function retakeQuiz(quizId) {
        const csrfToken = document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');
        
        // Show loading state
        const button = document.getElementById('retake-quiz-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class=\"bi bi-hourglass-split\"></i> Loading...';
        button.disabled = true;
        
        // Create a form to submit via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/student/quiz/${quizId}/start`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add to document and submit
        document.body.appendChild(form);
        form.submit();
    }
    
    // Function to handle back button correctly
    function goBack() {
        // Store current URL so we can check if we reload the same page
        const currentUrl = window.location.href;
        
        // Try to go back in history
        window.history.back();
        
        // Check if we stayed on the same page (history.back() didn't change location)
        setTimeout(function() {
            if (window.location.href === currentUrl) {
                // If still on same page, go to dashboard instead
                window.location.href = \"{{ route('student.dashboard') }}\";
            }
        }, 100);
    }
</script>
@endpush";
    
    // Replace the script section
    $newContent = substr($content, 0, $scriptStart) . $newScriptSection . substr($content, $scriptEnd + 8);
    
    // Write back to the file
    file_put_contents($filePath, $newContent);
    echo "<p>File updated with new script section</p>";
    echo "<h2>New Script Section:</h2>";
    echo "<pre>" . htmlspecialchars($newScriptSection) . "</pre>";
} else {
    echo "<p>Could not find script section in the file</p>";
}
