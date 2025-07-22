<?php
/*
 * FORM SUBMISSION DIAGNOSTIC TEST
 * This will help us understand what's happening with the file upload
 */

echo "<h1>Form Submission Diagnostic Test</h1>";

// Test 1: Check if Laravel server is receiving the request properly
echo "<h2>Test 1: Check PHP $_FILES and $_POST</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    echo "<h3>Server Info:</h3>";
    echo "Content Length: " . $_SERVER['CONTENT_LENGTH'] ?? 'Not set' . "<br>";
    echo "Content Type: " . $_SERVER['CONTENT_TYPE'] ?? 'Not set' . "<br>";
    echo "HTTP Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
    
    if (isset($_FILES['attachment'])) {
        $file = $_FILES['attachment'];
        echo "<h3>File Analysis:</h3>";
        echo "Name: " . $file['name'] . "<br>";
        echo "Type: " . $file['type'] . "<br>";
        echo "Size: " . $file['size'] . "<br>";
        echo "Error: " . $file['error'] . "<br>";
        echo "Tmp Name: " . $file['tmp_name'] . "<br>";
        echo "File exists in temp: " . (file_exists($file['tmp_name']) ? 'YES' : 'NO') . "<br>";
    } else {
        echo "<strong style='color: red;'>❌ NO FILE UPLOADED - \$_FILES['attachment'] is not set!</strong><br>";
    }
} else {
    echo "<p>No POST request received yet.</p>";
}

?>

<h2>Test Form</h2>
<form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div style="margin: 10px 0;">
        <label for="test_title">Title:</label><br>
        <input type="text" id="test_title" name="title" value="Test Upload" required>
    </div>
    
    <div style="margin: 10px 0;">
        <label for="test_file">File:</label><br>
        <input type="file" id="test_file" name="attachment" required>
    </div>
    
    <div style="margin: 10px 0;">
        <button type="submit">Test Upload</button>
    </div>
</form>

<h2>Laravel Route Test</h2>
<form method="POST" enctype="multipart/form-data" action="/admin/modules/course-content-store">
    <?php 
    // We need CSRF token for Laravel
    echo '<input type="hidden" name="_token" value="test-token-placeholder">';
    ?>
    
    <div style="margin: 10px 0;">
        <label>Program ID:</label><br>
        <input type="number" name="program_id" value="1" required>
    </div>
    
    <div style="margin: 10px 0;">
        <label>Module ID:</label><br>
        <input type="number" name="module_id" value="1" required>
    </div>
    
    <div style="margin: 10px 0;">
        <label>Content Type:</label><br>
        <select name="content_type" required>
            <option value="Lesson">Lesson</option>
            <option value="Quiz">Quiz</option>
        </select>
    </div>
    
    <div style="margin: 10px 0;">
        <label>Title:</label><br>
        <input type="text" name="title" value="Laravel Test Upload" required>
    </div>
    
    <div style="margin: 10px 0;">
        <label>File:</label><br>
        <input type="file" name="attachment" required>
    </div>
    
    <div style="margin: 10px 0;">
        <button type="submit">Test Laravel Route</button>
    </div>
</form>

<script>
console.log('Form diagnostic script loaded');

// Monitor all form submissions
document.addEventListener('submit', function(e) {
    console.log('Form submission detected:', e.target);
    console.log('Action:', e.target.action);
    console.log('Method:', e.target.method);
    console.log('Enctype:', e.target.enctype);
    
    const formData = new FormData(e.target);
    console.log('Form data entries:');
    for (let [key, value] of formData.entries()) {
        if (value instanceof File) {
            console.log(`${key}:`, {
                name: value.name,
                size: value.size,
                type: value.type
            });
        } else {
            console.log(`${key}:`, value);
        }
    }
});
</script>
