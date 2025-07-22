<!DOCTYPE html>
<html>
<head>
    <title>Test File Upload</title>
</head>
<body>
    <h1>Test File Upload</h1>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2>Upload Result:</h2>
        <pre>
POST Data: <?php print_r($_POST); ?>
FILES Data: <?php print_r($_FILES); ?>
        </pre>
        
        <?php if (isset($_FILES['test_file'])): ?>
            <h3>File Details:</h3>
            <ul>
                <li>Name: <?php echo $_FILES['test_file']['name']; ?></li>
                <li>Size: <?php echo $_FILES['test_file']['size']; ?> bytes</li>
                <li>Type: <?php echo $_FILES['test_file']['type']; ?></li>
                <li>Error: <?php echo $_FILES['test_file']['error']; ?></li>
                <li>Temp Name: <?php echo $_FILES['test_file']['tmp_name']; ?></li>
                <li>File Exists: <?php echo file_exists($_FILES['test_file']['tmp_name']) ? 'Yes' : 'No'; ?></li>
            </ul>
            
            <?php if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK): ?>
                <p style="color: green;">✅ File uploaded successfully!</p>
                
                <?php
                // Try to move the file
                $destination = 'test_uploads/' . time() . '_' . $_FILES['test_file']['name'];
                if (!is_dir('test_uploads')) {
                    mkdir('test_uploads', 0755, true);
                }
                
                if (move_uploaded_file($_FILES['test_file']['tmp_name'], $destination)) {
                    echo "<p style='color: green;'>✅ File moved to: $destination</p>";
                    echo "<p>File size on disk: " . filesize($destination) . " bytes</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to move file</p>";
                }
                ?>
            <?php else: ?>
                <p style="color: red;">❌ Upload Error: <?php echo $_FILES['test_file']['error']; ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p style="color: red;">❌ No file found in $_FILES</p>
        <?php endif; ?>
        
        <hr>
    <?php endif; ?>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <p>
            <label for="test_file">Select file:</label><br>
            <input type="file" id="test_file" name="test_file" required>
        </p>
        <p>
            <label for="test_text">Test text field:</label><br>
            <input type="text" id="test_text" name="test_text" value="test value">
        </p>
        <p>
            <button type="submit">Upload Test</button>
        </p>
    </form>
</body>
</html>
