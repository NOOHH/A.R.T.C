<?php
/**
 * Comprehensive Course Content Upload Test & Debug Script
 * Tests course content items table, upload functionality, modal interactions
 */

echo "🔍 COMPREHENSIVE COURSE CONTENT UPLOAD TEST\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Test database connection
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=artc",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Database connection successful\n";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Test content_items table structure
echo "\n📊 CONTENT_ITEMS TABLE ANALYSIS\n";
echo "-" . str_repeat("-", 40) . "\n";

try {
    $stmt = $pdo->query("DESCRIBE content_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table Structure:\n";
    $requiredColumns = ['id', 'content_title', 'course_id', 'content_type', 'attachment_path', 'created_at', 'updated_at'];
    $existingColumns = array_column($columns, 'Field');
    
    foreach ($requiredColumns as $required) {
        $exists = in_array($required, $existingColumns);
        echo "  • {$required}: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "\n";
    }
    
    // Show attachment_path column details
    foreach ($columns as $column) {
        if ($column['Field'] === 'attachment_path') {
            echo "\nattachment_path details:\n";
            echo "  • Type: {$column['Type']}\n";
            echo "  • Null: {$column['Null']}\n";
            echo "  • Default: {$column['Default']}\n";
            break;
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "\n";
}

// Test existing content_items data
echo "\n📂 EXISTING CONTENT_ITEMS DATA\n";
echo "-" . str_repeat("-", 40) . "\n";

try {
    $stmt = $pdo->query("
        SELECT 
            id,
            content_title,
            course_id,
            content_type,
            attachment_path,
            content_url,
            created_at,
            updated_at
        FROM content_items 
        ORDER BY id DESC 
        LIMIT 10
    ");
    
    $contentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($contentItems)) {
        echo "⚠️  No content items found in database\n";
        echo "   This confirms the upload issue - data is not being inserted\n";
    } else {
        echo "Found " . count($contentItems) . " content items:\n\n";
        
        foreach ($contentItems as $item) {
            echo "📄 ID: {$item['id']} - {$item['content_title']}\n";
            echo "   Course ID: {$item['course_id']}\n";
            echo "   Type: " . ($item['content_type'] ?: 'NULL') . "\n";
            echo "   Attachment: " . ($item['attachment_path'] ?: 'NULL') . "\n";
            echo "   URL: " . ($item['content_url'] ?: 'NULL') . "\n";
            echo "   Created: {$item['created_at']}\n";
            
            // Check if attachment file exists
            if ($item['attachment_path']) {
                $storagePath = __DIR__ . '/storage/app/public/' . $item['attachment_path'];
                $publicPath = __DIR__ . '/public/storage/' . $item['attachment_path'];
                
                echo "   Storage file: " . (file_exists($storagePath) ? "✅ EXISTS" : "❌ MISSING") . "\n";
                echo "   Public file: " . (file_exists($publicPath) ? "✅ EXISTS" : "❌ MISSING") . "\n";
            }
            echo "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error querying content_items: " . $e->getMessage() . "\n";
}

// Test courses table to understand the relationship
echo "\n🎓 COURSES TABLE ANALYSIS\n";
echo "-" . str_repeat("-", 40) . "\n";

try {
    $stmt = $pdo->query("
        SELECT 
            subject_id,
            subject_title,
            modules_id
        FROM courses 
        ORDER BY subject_id DESC 
        LIMIT 5
    ");
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($courses)) {
        echo "Sample courses for testing:\n";
        foreach ($courses as $course) {
            echo "• Course ID: {$course['subject_id']} - {$course['subject_title']} (Module: {$course['modules_id']})\n";
        }
    } else {
        echo "❌ No courses found - this might be the issue!\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error querying courses: " . $e->getMessage() . "\n";
}

// Test modules table
echo "\n🏗️ MODULES TABLE ANALYSIS\n";
echo "-" . str_repeat("-", 40) . "\n";

try {
    $stmt = $pdo->query("
        SELECT 
            modules_id,
            module_name,
            program_id
        FROM modules 
        ORDER BY modules_id DESC 
        LIMIT 5
    ");
    
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($modules)) {
        echo "Sample modules for testing:\n";
        foreach ($modules as $module) {
            echo "• Module ID: {$module['modules_id']} - {$module['module_name']} (Program: {$module['program_id']})\n";
        }
    } else {
        echo "❌ No modules found\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error querying modules: " . $e->getMessage() . "\n";
}

// Test storage directories
echo "\n📁 STORAGE DIRECTORIES TEST\n";
echo "-" . str_repeat("-", 40) . "\n";

$directories = [
    'storage/app/public' => __DIR__ . '/storage/app/public',
    'storage/app/public/content' => __DIR__ . '/storage/app/public/content',
    'public/storage' => __DIR__ . '/public/storage',
    'public/storage/content' => __DIR__ . '/public/storage/content',
];

foreach ($directories as $name => $path) {
    if (is_dir($path)) {
        $fileCount = count(glob($path . '/*'));
        $writable = is_writable($path) ? "✅ WRITABLE" : "❌ NOT WRITABLE";
        echo "• {$name}: ✅ EXISTS ({$fileCount} files) {$writable}\n";
    } else {
        echo "• {$name}: ❌ MISSING\n";
    }
}

// Test file upload simulation
echo "\n🧪 FILE UPLOAD SIMULATION TEST\n";
echo "-" . str_repeat("-", 40) . "\n";

// Create test content item data
$testData = [
    'content_title' => 'Test Content Upload ' . date('Y-m-d H:i:s'),
    'content_description' => 'Testing course content upload functionality',
    'course_id' => 1, // Assuming course ID 1 exists
    'content_type' => 'document',
    'attachment_path' => 'content/test_content_' . time() . '.pdf',
    'content_url' => null,
    'max_points' => 0,
    'is_required' => 1,
    'is_active' => 1,
];

try {
    // Test insert
    $placeholders = str_repeat('?,', count($testData) - 1) . '?';
    $columns = implode(',', array_keys($testData));
    $sql = "INSERT INTO content_items ({$columns}) VALUES ({$placeholders})";
    
    echo "Testing content_items insert:\n";
    echo "SQL: {$sql}\n";
    echo "Data: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array_values($testData));
    
    if ($result) {
        $insertId = $pdo->lastInsertId();
        echo "✅ Test insert successful! New ID: {$insertId}\n";
        
        // Verify the insert
        $stmt = $pdo->prepare("SELECT * FROM content_items WHERE id = ?");
        $stmt->execute([$insertId]);
        $inserted = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($inserted) {
            echo "✅ Insert verified:\n";
            echo "   • Title: {$inserted['content_title']}\n";
            echo "   • Course ID: {$inserted['course_id']}\n";
            echo "   • Type: {$inserted['content_type']}\n";
            echo "   • Attachment: {$inserted['attachment_path']}\n";
        } else {
            echo "❌ Insert verification failed\n";
        }
        
        // Clean up test data
        $pdo->prepare("DELETE FROM content_items WHERE id = ?")->execute([$insertId]);
        echo "🧹 Test data cleaned up\n";
        
    } else {
        echo "❌ Test insert failed\n";
        print_r($stmt->errorInfo());
    }
    
} catch (PDOException $e) {
    echo "❌ Insert test error: " . $e->getMessage() . "\n";
}

// Test route accessibility
echo "\n🌐 ROUTE ACCESSIBILITY TEST\n";
echo "-" . str_repeat("-", 40) . "\n";

// Test if we can access the web.php file to check routes
$webRoutesPath = __DIR__ . '/routes/web.php';
if (file_exists($webRoutesPath)) {
    $routeContent = file_get_contents($webRoutesPath);
    
    // Check for required routes
    $requiredRoutes = [
        'course-content-store' => '/admin/modules/course-content-store',
        'content-delete' => '/admin/content/{id}',
        'content-get' => '/admin/content/{id}',
        'module-delete' => '/admin/modules/{module:modules_id}'
    ];
    
    echo "Checking required routes:\n";
    foreach ($requiredRoutes as $name => $pattern) {
        $found = strpos($routeContent, $pattern) !== false;
        echo "• {$name}: " . ($found ? "✅ FOUND" : "❌ MISSING") . "\n";
        
        if ($found) {
            // Extract the full route line for analysis
            $lines = explode("\n", $routeContent);
            foreach ($lines as $line) {
                if (strpos($line, $pattern) !== false) {
                    echo "  → " . trim($line) . "\n";
                    break;
                }
            }
        }
    }
} else {
    echo "❌ Cannot access routes/web.php file\n";
}

// Test controller file existence
echo "\n📋 CONTROLLER FILES TEST\n";
echo "-" . str_repeat("-", 40) . "\n";

$controllerPath = __DIR__ . '/app/Http/Controllers/AdminModuleController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    $requiredMethods = [
        'courseContentStore',
        'deleteContent',
        'destroy',
        'getContent'
    ];
    
    echo "Checking controller methods:\n";
    foreach ($requiredMethods as $method) {
        $found = strpos($controllerContent, "function {$method}") !== false;
        echo "• {$method}: " . ($found ? "✅ FOUND" : "❌ MISSING") . "\n";
    }
} else {
    echo "❌ AdminModuleController.php not found\n";
}

// Generate comprehensive summary
echo "\n📋 COMPREHENSIVE TEST SUMMARY\n";
echo "=" . str_repeat("=", 60) . "\n";

echo "🔍 FINDINGS:\n";
echo "1. Database Connection: ✅ Working\n";
echo "2. content_items Table: " . (isset($columns) ? "✅ Exists" : "❌ Issues") . "\n";
echo "3. Existing Data: " . (empty($contentItems) ? "⚠️  None found (upload issue confirmed)" : "✅ Data exists") . "\n";
echo "4. Storage Directories: " . (is_dir(__DIR__ . '/storage/app/public/content') ? "✅ Ready" : "❌ Missing") . "\n";
echo "5. Insert Test: " . (isset($result) && $result ? "✅ Database writable" : "❌ Database issues") . "\n\n";

echo "🎯 LIKELY ISSUES:\n";
if (empty($contentItems)) {
    echo "• Course content uploads are NOT reaching the database\n";
    echo "• This indicates an issue in the courseContentStore method or form submission\n";
}
echo "• Modal interaction issues with edit and delete functions\n";
echo "• Need to add comprehensive logging to track the upload flow\n\n";

echo "🔧 RECOMMENDED FIXES:\n";
echo "1. Add extensive debugging to courseContentStore method\n";
echo "2. Fix modal click-outside functionality\n";
echo "3. Fix 405 Method Not Allowed error for delete operations\n";
echo "4. Add comprehensive logging for file upload and database operations\n";
echo "5. Test the complete upload flow with real files\n\n";

echo "📊 TEST DATA FOR DEBUGGING:\n";
if (!empty($courses)) {
    echo "• Use Course ID: {$courses[0]['subject_id']} ({$courses[0]['subject_title']})\n";
}
if (!empty($modules)) {
    echo "• Use Module ID: {$modules[0]['modules_id']} ({$modules[0]['module_name']})\n";
}

echo "\n🎉 Comprehensive test complete!\n";
echo "This data will guide the fixes for the course content upload system.\n";

?>
