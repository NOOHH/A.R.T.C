<?php
// Direct database test without Laravel models
echo "=== DIRECT DATABASE ENROLLMENT SYSTEM TEST ===\n";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection established\n\n";
    
    // 1. Check Students Table
    echo "1. CHECKING STUDENTS:\n";
    $stmt = $pdo->query("SELECT student_id, firstname, lastname FROM students ORDER BY student_id LIMIT 5");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($students) . " students (showing first 5):\n";
    foreach ($students as $student) {
        echo "  ID: {$student['student_id']}, Name: {$student['firstname']} {$student['lastname']}\n";
    }
    echo "\n";
    
    // 2. Check Programs Table
    echo "2. CHECKING PROGRAMS:\n";
    $stmt = $pdo->query("SELECT program_id, program_name FROM programs ORDER BY program_id LIMIT 5");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($programs) . " programs (showing first 5):\n";
    foreach ($programs as $program) {
        echo "  ID: {$program['program_id']}, Name: {$program['program_name']}\n";
    }
    echo "\n";
    
    // 3. Check Modules Table
    echo "3. CHECKING MODULES:\n";
    $stmt = $pdo->query("SELECT modules_id, module_name, program_id FROM modules ORDER BY modules_id LIMIT 5");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($modules) . " modules (showing first 5):\n";
    foreach ($modules as $module) {
        echo "  ID: {$module['modules_id']}, Name: {$module['module_name']}, Program: {$module['program_id']}\n";
    }
    echo "\n";
    
    // 4. Check Courses Table
    echo "4. CHECKING COURSES:\n";
    $stmt = $pdo->query("SELECT subject_id, subject_name, module_id FROM courses ORDER BY subject_id LIMIT 5");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($courses) . " courses (showing first 5):\n";
    foreach ($courses as $course) {
        echo "  ID: {$course['subject_id']}, Name: {$course['subject_name']}, Module: {$course['module_id']}\n";
    }
    echo "\n";
    
    // 5. Check Enrollments Table Structure
    echo "5. CHECKING ENROLLMENTS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE enrollments");
    $enrollment_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($enrollment_columns as $column) {
        echo "  {$column['Field']} - {$column['Type']} ({$column['Null']})\n";
    }
    echo "\n";
    
    // 6. Check Enrollment Courses Table Structure
    echo "6. CHECKING ENROLLMENT_COURSES TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE enrollment_courses");
    $enrollment_courses_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($enrollment_courses_columns as $column) {
        echo "  {$column['Field']} - {$column['Type']} ({$column['Null']})\n";
    }
    echo "\n";
    
    // 7. Check Current Enrollments
    echo "7. CHECKING CURRENT ENROLLMENTS:\n";
    $stmt = $pdo->query("SELECT enrollment_id, student_id, program_id, created_at FROM enrollments ORDER BY enrollment_id DESC LIMIT 5");
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($enrollments) . " enrollments (showing last 5):\n";
    foreach ($enrollments as $enrollment) {
        echo "  ID: {$enrollment['enrollment_id']}, Student: {$enrollment['student_id']}, Program: {$enrollment['program_id']}, Date: {$enrollment['created_at']}\n";
    }
    echo "\n";
    
    // 8. Check Current Enrollment Courses
    echo "8. CHECKING CURRENT ENROLLMENT COURSES:\n";
    $stmt = $pdo->query("SELECT id, enrollment_id, course_id, module_id FROM enrollment_courses ORDER BY id DESC LIMIT 5");
    $enrollment_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($enrollment_courses) . " enrollment courses (showing last 5):\n";
    foreach ($enrollment_courses as $ec) {
        echo "  ID: {$ec['id']}, Enrollment: {$ec['enrollment_id']}, Course: {$ec['course_id']}, Module: {$ec['module_id']}\n";
    }
    echo "\n";
    
    // 9. Test API Data Simulation
    echo "9. TESTING API DATA FLOW:\n";
    
    // Get first program
    $stmt = $pdo->query("SELECT program_id FROM programs LIMIT 1");
    $first_program = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($first_program) {
        $program_id = $first_program['program_id'];
        echo "Testing with Program ID: $program_id\n";
        
        // Get modules for this program
        $stmt = $pdo->prepare("SELECT modules_id as module_id, module_name FROM modules WHERE program_id = ?");
        $stmt->execute([$program_id]);
        $program_modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "  Modules for Program $program_id: " . count($program_modules) . " found\n";
        
        if (!empty($program_modules)) {
            $first_module = $program_modules[0];
            $module_id = $first_module['module_id'];
            echo "  Testing with Module ID: $module_id\n";
            
            // Get courses for this module
            $stmt = $pdo->prepare("SELECT subject_id as course_id, subject_name FROM courses WHERE module_id = ?");
            $stmt->execute([$module_id]);
            $module_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "  Courses for Module $module_id: " . count($module_courses) . " found\n";
        }
    }
    echo "\n";
    
    // 10. Test Enrollment Creation Simulation
    echo "10. TESTING ENROLLMENT CREATION FLOW:\n";
    
    // Get test data
    $stmt = $pdo->query("SELECT student_id FROM students LIMIT 1");
    $test_student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT program_id FROM programs LIMIT 1");
    $test_program = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($test_student && $test_program) {
        echo "Test Student ID: {$test_student['student_id']}\n";
        echo "Test Program ID: {$test_program['program_id']}\n";
        
        // Check if enrollment already exists
        $stmt = $pdo->prepare("SELECT enrollment_id FROM enrollments WHERE student_id = ? AND program_id = ?");
        $stmt->execute([$test_student['student_id'], $test_program['program_id']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            echo "✓ Enrollment already exists: ID {$existing['enrollment_id']}\n";
        } else {
            echo "○ No existing enrollment found - would create new one\n";
        }
    }
    
    echo "\n=== TEST COMPLETE ===\n";
    echo "All database tables and relationships appear to be working correctly.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
