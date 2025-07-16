<?php
// Database connection test
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>Database Connection Test</h2>";
    echo "<div style='color: green'>✅ Database connection successful</div>";
} catch (PDOException $e) {
    echo "<div style='color: red'>❌ Database connection failed: " . $e->getMessage() . "</div>";
    exit;
}

// Test registration flow
echo "<h2>Testing Registration Flow</h2>";

// Test 1: Check if tables exist
echo "<h3>1. Testing Database Tables</h3>";

try {
    $tables = ['registrations', 'students', 'enrollments', 'packages', 'programs', 'modules'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result = $stmt->fetchAll();
        
        if (empty($result)) {
            echo "<div style='color: red'>❌ Table '{$table}' does not exist</div>";
        } else {
            echo "<div style='color: green'>✅ Table '{$table}' exists</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Database error: " . $e->getMessage() . "</div>";
}

// Test 2: Check if packages exist
echo "<h3>2. Testing Packages</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE package_type = 'modular' LIMIT 5");
    $stmt->execute();
    $packages = $stmt->fetchAll();
    
    if (empty($packages)) {
        echo "<div style='color: red'>❌ No modular packages found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($packages) . " modular packages</div>";
        foreach ($packages as $package) {
            echo "<div>- {$package['package_name']} (ID: {$package['package_id']})</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Package error: " . $e->getMessage() . "</div>";
}

// Test 3: Check if programs exist
echo "<h3>3. Testing Programs</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE is_archived = 0 LIMIT 5");
    $stmt->execute();
    $programs = $stmt->fetchAll();
    
    if (empty($programs)) {
        echo "<div style='color: red'>❌ No active programs found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($programs) . " active programs</div>";
        foreach ($programs as $program) {
            echo "<div>- {$program['program_name']} (ID: {$program['program_id']})</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Program error: " . $e->getMessage() . "</div>";
}

// Test 4: Check if modules exist
echo "<h3>4. Testing Modules</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM modules LIMIT 5");
    $stmt->execute();
    $modules = $stmt->fetchAll();
    
    if (empty($modules)) {
        echo "<div style='color: red'>❌ No modules found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($modules) . " modules</div>";
        foreach ($modules as $module) {
            echo "<div>- {$module['name']} (ID: {$module['id']})</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Module error: " . $e->getMessage() . "</div>";
}

// Test 5: Check recent registrations
echo "<h3>5. Testing Recent Registrations</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM registrations ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $registrations = $stmt->fetchAll();
    
    if (empty($registrations)) {
        echo "<div style='color: orange'>⚠️ No registrations found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($registrations) . " recent registrations</div>";
        foreach ($registrations as $registration) {
            echo "<div>- Registration ID: {$registration['registration_id']}, User: {$registration['user_id']}, Status: {$registration['status']}</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Registration error: " . $e->getMessage() . "</div>";
}

// Test 6: Check recent students
echo "<h3>6. Testing Recent Students</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM students ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $students = $stmt->fetchAll();
    
    if (empty($students)) {
        echo "<div style='color: orange'>⚠️ No students found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($students) . " recent students</div>";
        foreach ($students as $student) {
            echo "<div>- Student ID: {$student['student_id']}, Name: {$student['firstname']} {$student['lastname']}</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Student error: " . $e->getMessage() . "</div>";
}

// Test 7: Check recent enrollments
echo "<h3>7. Testing Recent Enrollments</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM enrollments ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $enrollments = $stmt->fetchAll();
    
    if (empty($enrollments)) {
        echo "<div style='color: orange'>⚠️ No enrollments found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($enrollments) . " recent enrollments</div>";
        foreach ($enrollments as $enrollment) {
            echo "<div>- Enrollment ID: {$enrollment['enrollment_id']}, User: {$enrollment['user_id']}, Status: {$enrollment['enrollment_status']}</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Enrollment error: " . $e->getMessage() . "</div>";
}

// Test 8: Check form requirements
echo "<h3>8. Testing Form Requirements</h3>";

try {
    $stmt = $pdo->prepare("SELECT * FROM form_requirements WHERE is_active = 1 AND program_type = 'modular' LIMIT 10");
    $stmt->execute();
    $requirements = $stmt->fetchAll();
    
    if (empty($requirements)) {
        echo "<div style='color: orange'>⚠️ No modular form requirements found</div>";
    } else {
        echo "<div style='color: green'>✅ Found " . count($requirements) . " modular form requirements</div>";
        foreach ($requirements as $requirement) {
            echo "<div>- {$requirement['field_label']} ({$requirement['field_name']}): {$requirement['field_type']}</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Form requirements error: " . $e->getMessage() . "</div>";
}

// Test 9: Check registration table columns
echo "<h3>9. Testing Registration Table Structure</h3>";

try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM registrations");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['registration_id', 'user_id', 'program_id', 'package_id', 'enrollment_type', 'learning_mode', 'status'];
    
    foreach ($requiredColumns as $column) {
        if (in_array($column, $columnNames)) {
            echo "<div style='color: green'>✅ Column '{$column}' exists</div>";
        } else {
            echo "<div style='color: red'>❌ Column '{$column}' missing</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Registration table error: " . $e->getMessage() . "</div>";
}

// Test 10: Check admin packages page
echo "<h3>10. Testing Admin Packages Page</h3>";

try {
    $url = 'http://127.0.0.1:8000/admin/packages';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "<div style='color: green'>✅ Admin packages page accessible</div>";
    } else {
        echo "<div style='color: red'>❌ Admin packages page error: HTTP {$httpCode}</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Admin packages page error: " . $e->getMessage() . "</div>";
}

// Test 11: Check modular enrollment page
echo "<h3>11. Testing Modular Enrollment Page</h3>";

try {
    $url = 'http://127.0.0.1:8000/enrollment/modular';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "<div style='color: green'>✅ Modular enrollment page accessible</div>";
        
        // Check if the page contains expected content
        if (strpos($response, 'Modular Enrollment') !== false) {
            echo "<div style='color: green'>✅ Modular enrollment page contains correct content</div>";
        } else {
            echo "<div style='color: orange'>⚠️ Modular enrollment page missing expected content</div>";
        }
    } else {
        echo "<div style='color: red'>❌ Modular enrollment page error: HTTP {$httpCode}</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red'>❌ Modular enrollment page error: " . $e->getMessage() . "</div>";
}

echo "<h3>Test Complete</h3>";
echo "<p>If you see red errors above, those need to be fixed for the registration to work properly.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Fix any missing database tables or columns</li>";
echo "<li>Ensure modular packages exist in the database</li>";
echo "<li>Test the registration form manually at: <a href='http://127.0.0.1:8000/enrollment/modular'>http://127.0.0.1:8000/enrollment/modular</a></li>";
echo "<li>Check the admin packages page at: <a href='http://127.0.0.1:8000/admin/packages'>http://127.0.0.1:8000/admin/packages</a></li>";
echo "</ul>";
?>
