<?php
$pdo = new PDO('mysql:host=localhost;dbname=artc;charset=utf8', 'root', '');

echo "=== Module 45 Analysis ===\n";

// Check module
$stmt = $pdo->query('SELECT * FROM modules WHERE modules_id = 45');
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if ($module) {
    echo "Module: {$module['module_name']} (ID: {$module['modules_id']})\n";
    echo "Program ID: {$module['program_id']}\n";
    
    // Check courses
    $stmt2 = $pdo->query('SELECT * FROM courses WHERE module_id = 45');
    $courses = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "Courses in this module: " . count($courses) . "\n\n";
    
    foreach ($courses as $course) {
        echo "📋 Course: {$course['subject_name']} (ID: {$course['subject_id']})\n";
        
        // Check lessons
        $stmt3 = $pdo->query("SELECT * FROM lessons WHERE course_id = {$course['subject_id']}");
        $lessons = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($lessons as $lesson) {
            echo "  📝 Lesson: {$lesson['lesson_name']} (ID: {$lesson['lesson_id']})\n";
            
            // Check content items
            $stmt4 = $pdo->query("SELECT * FROM content_items WHERE lesson_id = {$lesson['lesson_id']}");
            $contentItems = $stmt4->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($contentItems as $item) {
                echo "    📎 Content: {$item['content_title']} ({$item['content_type']})";
                if ($item['attachment_path']) {
                    echo " - File: {$item['attachment_path']}";
                }
                echo "\n";
            }
            
            if (empty($contentItems)) {
                echo "    (No content items)\n";
            }
        }
        
        if (empty($lessons)) {
            echo "  (No lessons)\n";
        }
        echo "\n";
    }
} else {
    echo "❌ Module 45 not found\n";
}
?>
