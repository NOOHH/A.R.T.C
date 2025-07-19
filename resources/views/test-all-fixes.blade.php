<!DOCTYPE html>
<html>
<head>
    <title>A.R.T.C Modular Enrollment Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .test-section { margin: 20px 0; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>A.R.T.C System Test Results</h1>
    
    <div class="test-section">
        <h2>1. Upload Directory Test</h2>
        <div id="uploadTest">
            <?php
            $uploadDir = storage_path('app/public/uploads/education_requirements');
            if (file_exists($uploadDir)) {
                echo '<span class="success">✅ Upload directory exists: ' . $uploadDir . '</span>';
            } else {
                echo '<span class="error">❌ Upload directory missing: ' . $uploadDir . '</span>';
            }
            ?>
        </div>
    </div>

    <div class="test-section">
        <h2>2. Route Tests</h2>
        <div id="routeTests">
            <?php
            $routes = [
                'enrollment.modular.submit' => '/enrollment/modular/submit',
                'registration.validateFile' => '/registration/validate-file',
                'get.module.courses' => '/get-module-courses'
            ];
            
            foreach ($routes as $name => $url) {
                try {
                    $actualUrl = route($name);
                    echo '<span class="success">✅ Route "' . $name . '": ' . $actualUrl . '</span><br>';
                } catch (Exception $e) {
                    echo '<span class="error">❌ Route "' . $name . '" not found</span><br>';
                }
            }
            ?>
        </div>
    </div>

    <div class="test-section">
        <h2>3. Database Test</h2>
        <div id="dbTests">
            <?php
            try {
                // Check database connection
                $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
                echo '<span class="success">✅ Database connected</span><br>';
                
                // Check recent registrations with course data
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM registrations WHERE selected_courses IS NOT NULL");
                $result = $stmt->fetch();
                echo '<span class="success">✅ Registrations with course data: ' . $result['count'] . '</span><br>';
                
                // Check enrollment courses
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM enrollment_courses");
                $result = $stmt->fetch();
                echo '<span class="success">✅ Enrollment course records: ' . $result['count'] . '</span><br>';
                
                // Check batch capacity
                $stmt = $pdo->query("
                    SELECT b.batch_id, b.batch_name, b.current_capacity, 
                           COUNT(e.enrollment_id) as actual_capacity
                    FROM student_batches b
                    LEFT JOIN enrollments e ON b.batch_id = e.batch_id 
                           AND e.enrollment_status = 'approved' 
                           AND e.payment_status = 'paid'
                    WHERE b.batch_status = 'available'
                    GROUP BY b.batch_id, b.batch_name, b.current_capacity
                    LIMIT 5
                ");
                
                while ($row = $stmt->fetch()) {
                    if ($row['current_capacity'] == $row['actual_capacity']) {
                        echo '<span class="success">✅ Batch ' . $row['batch_id'] . ': ' . $row['current_capacity'] . '/' . $row['actual_capacity'] . ' (correct)</span><br>';
                    } else {
                        echo '<span class="warning">⚠️  Batch ' . $row['batch_id'] . ': ' . $row['current_capacity'] . '/' . $row['actual_capacity'] . ' (mismatch)</span><br>';
                    }
                }
                
            } catch (Exception $e) {
                echo '<span class="error">❌ Database error: ' . $e->getMessage() . '</span>';
            }
            ?>
        </div>
    </div>

    <div class="test-section">
        <h2>4. Modular Enrollment Test Form</h2>
        <p>Test the modular enrollment with course selection:</p>
        <button onclick="window.open('/enrollment/modular', '_blank')">Open Modular Enrollment</button>
    </div>

    <div class="test-section">
        <h2>5. Full Enrollment Test Form</h2>
        <p>Test the full enrollment with file upload:</p>
        <button onclick="window.open('/enrollment/full', '_blank')">Open Full Enrollment</button>
    </div>

    <div class="test-section">
        <h2>6. File Upload Test</h2>
        <form id="fileTestForm" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" name="test_file" id="testFile" accept=".pdf,.jpg,.jpeg,.png">
            <input type="hidden" name="field_name" value="test_document">
            <input type="hidden" name="first_name" value="Test">
            <input type="hidden" name="last_name" value="User">
            <button type="button" onclick="testFileUpload()">Test File Upload & Validation</button>
        </form>
        <div id="fileTestResult"></div>
    </div>

    <script>
        function testFileUpload() {
            const form = document.getElementById('fileTestForm');
            const file = document.getElementById('testFile').files[0];
            const resultDiv = document.getElementById('fileTestResult');
            
            if (!file) {
                resultDiv.innerHTML = '<span class="error">Please select a file first</span>';
                return;
            }
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('field_name', 'test_document');
            formData.append('first_name', 'Test');
            formData.append('last_name', 'User');
            
            resultDiv.innerHTML = 'Testing file upload...';
            
            fetch('/registration/validate-file', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<span class="success">✅ File upload successful! Path: ' + data.file_path + '</span>';
                } else {
                    resultDiv.innerHTML = '<span class="error">❌ File upload failed: ' + data.message + '</span>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<span class="error">❌ Network error: ' + error.message + '</span>';
            });
        }
    </script>
</body>
</html>
