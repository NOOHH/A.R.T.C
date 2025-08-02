<?php

// Simple debug script for admin quiz status change
echo "<h1>Admin Quiz Status Change Debugger</h1>";

// Function to simulate an AJAX request to change quiz status
function testAdminQuizStatusChange($quizId, $action) {
    $url = "http://127.0.0.1:8000/admin/quiz-generator/{$quizId}/{$action}";
    
    echo "<h3>Testing URL: {$url}</h3>";
    
    $headers = [
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{}'); // Empty JSON object
    
    echo "<p>Sending POST request...</p>";
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<p>HTTP Status Code: {$httpCode}</p>";
    
    if ($error) {
        echo "<p style='color: red'>Error: {$error}</p>";
    } else {
        echo "<p>Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Display a form to test the functionality
?>

<form method="post" action="">
    <div>
        <label for="quiz_id">Quiz ID:</label>
        <input type="number" name="quiz_id" id="quiz_id" value="<?= $_POST['quiz_id'] ?? '' ?>" required>
    </div>
    <div>
        <label for="action">Action:</label>
        <select name="action" id="action">
            <option value="publish" <?= ($_POST['action'] ?? '') == 'publish' ? 'selected' : '' ?>>Publish</option>
            <option value="archive" <?= ($_POST['action'] ?? '') == 'archive' ? 'selected' : '' ?>>Archive</option>
            <option value="draft" <?= ($_POST['action'] ?? '') == 'draft' ? 'selected' : '' ?>>Move to Draft</option>
        </select>
    </div>
    <div>
        <button type="submit" name="test">Test Status Change</button>
    </div>
</form>

<?php
// Process the form
if (isset($_POST['test']) && isset($_POST['quiz_id']) && isset($_POST['action'])) {
    $quizId = (int) $_POST['quiz_id'];
    $action = $_POST['action'];
    
    if ($quizId > 0 && in_array($action, ['publish', 'archive', 'draft'])) {
        testAdminQuizStatusChange($quizId, $action);
    }
}
?>
