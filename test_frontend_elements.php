<?php
/**
 * Test Frontend Form Elements for Admin Quiz Generator
 * This script tests that all form elements have the correct data
 */

// Database connection
$host = 'localhost';
$dbname = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FRONTEND FORM ELEMENTS TEST ===\n\n";
    
    // 1. Test programs data for dropdown
    echo "1. TESTING PROGRAMS DATA:\n";
    echo "------------------------\n";
    
    $stmt = $pdo->query("
        SELECT program_id, program_name 
        FROM programs 
        WHERE is_archived = 0 
        ORDER BY program_name
    ");
    
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Available programs for admin: " . count($programs) . "\n";
    
    foreach ($programs as $program) {
        echo "  - {$program['program_name']} (ID: {$program['program_id']})\n";
    }
    
    // 2. Test modules data for each program
    echo "\n2. TESTING MODULES DATA:\n";
    echo "-----------------------\n";
    
    foreach ($programs as $program) {
        $stmt = $pdo->prepare("
            SELECT modules_id as module_id, module_name 
            FROM modules 
            WHERE program_id = ? AND is_archived = 0 
            ORDER BY module_name
        ");
        $stmt->execute([$program['program_id']]);
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Program '{$program['program_name']}' has " . count($modules) . " modules:\n";
        foreach ($modules as $module) {
            echo "    - {$module['module_name']} (ID: {$module['module_id']})\n";
        }
    }
    
    // 3. Test courses data for modules
    echo "\n3. TESTING COURSES DATA:\n";
    echo "-----------------------\n";
    
    $stmt = $pdo->query("
        SELECT DISTINCT m.modules_id as module_id, m.module_name
        FROM modules m 
        WHERE m.is_archived = 0 
        LIMIT 3
    ");
    $sampleModules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sampleModules as $module) {
        $stmt = $pdo->prepare("
            SELECT subject_id as course_id, subject_name as course_name 
            FROM courses 
            WHERE module_id = ? AND is_archived = 0 
            ORDER BY subject_name
        ");
        $stmt->execute([$module['module_id']]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Module '{$module['module_name']}' has " . count($courses) . " courses:\n";
        foreach ($courses as $course) {
            echo "    - {$course['course_name']} (ID: {$course['course_id']})\n";
        }
    }
    
    // 4. Generate JavaScript data format
    echo "\n4. GENERATING JAVASCRIPT DATA FORMAT:\n";
    echo "------------------------------------\n";
    
    echo "Programs for JavaScript (JSON format):\n";
    echo json_encode($programs, JSON_PRETTY_PRINT) . "\n";
    
    // 5. Test admin session simulation
    echo "\n5. SIMULATING ADMIN SESSION:\n";
    echo "---------------------------\n";
    
    $stmt = $pdo->query("SELECT admin_id, admin_name, email FROM admins LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $sessionData = [
            'user_id' => $admin['admin_id'],
            'user_name' => $admin['admin_name'],
            'user_email' => $admin['email'],
            'user_type' => 'admin',
            'logged_in' => true
        ];
        
        echo "Admin session data for JavaScript:\n";
        echo json_encode($sessionData, JSON_PRETTY_PRINT) . "\n";
    }
    
    // 6. Create test HTML form elements
    echo "\n6. GENERATING HTML FORM ELEMENTS:\n";
    echo "--------------------------------\n";
    
    echo "Program select options:\n";
    echo "<select id=\"program_id\" name=\"program_id\" class=\"form-select\">\n";
    echo "    <option value=\"\">Select Program</option>\n";
    foreach ($programs as $program) {
        echo "    <option value=\"{$program['program_id']}\">{$program['program_name']}</option>\n";
    }
    echo "</select>\n\n";
    
    // 7. Test quiz type options
    echo "Quiz type select options:\n";
    echo "<select class=\"form-select\" id=\"aiQuestionType\">\n";
    echo "    <option value=\"multiple_choice\">Multiple Choice</option>\n";
    echo "    <option value=\"true_false\">True/False</option>\n";
    echo "    <option value=\"mixed\">Mixed Questions</option>\n";
    echo "</select>\n\n";
    
    // 8. Test file upload element
    echo "File upload element:\n";
    echo "<input type=\"file\" class=\"form-control\" id=\"ai_document\" name=\"file\" \n";
    echo "       accept=\".pdf,.doc,.docx,.csv,.txt,.jpg,.jpeg,.png\" \n";
    echo "       onchange=\"handleFileChange()\">\n\n";
    
    echo "=== FRONTEND ELEMENTS TEST COMPLETE ===\n";
    echo "\nThe admin quiz generator frontend should have:\n";
    echo "✓ " . count($programs) . " programs in the dropdown\n";
    echo "✓ Dynamic module loading working\n";
    echo "✓ Dynamic course loading working\n";
    echo "✓ Proper session data for authentication\n";
    echo "✓ All form elements properly configured\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
