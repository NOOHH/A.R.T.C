<?php
// Create test data for course system
use Illuminate\Support\Facades\DB;

// Connect to database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully\n";
    
    // Check if we have modules
    $stmt = $pdo->query("SELECT * FROM modules LIMIT 1");
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$module) {
        echo "No modules found, creating a test module...\n";
        $stmt = $pdo->prepare("INSERT INTO modules (module_name, module_description, program_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Test Module', 'Test module description', 1]);
        $moduleId = $pdo->lastInsertId();
        echo "Created module with ID: $moduleId\n";
    } else {
        $moduleId = $module['modules_id'];
        echo "Using existing module: {$module['module_name']} (ID: $moduleId)\n";
    }
    
    // Check if courses table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'courses'");
    if ($stmt->rowCount() == 0) {
        echo "Courses table doesn't exist, creating it...\n";
        $pdo->exec("
            CREATE TABLE courses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                module_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                is_required BOOLEAN DEFAULT FALSE,
                is_active BOOLEAN DEFAULT TRUE,
                `order` INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (module_id) REFERENCES modules(modules_id) ON DELETE CASCADE
            )
        ");
        echo "Created courses table\n";
    }
    
    // Check if lessons table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'lessons'");
    if ($stmt->rowCount() == 0) {
        echo "Lessons table doesn't exist, creating it...\n";
        $pdo->exec("
            CREATE TABLE lessons (
                id INT AUTO_INCREMENT PRIMARY KEY,
                course_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                `order` INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
            )
        ");
        echo "Created lessons table\n";
    }
    
    // Check if content_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'content_items'");
    if ($stmt->rowCount() == 0) {
        echo "Content_items table doesn't exist, creating it...\n";
        $pdo->exec("
            CREATE TABLE content_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lesson_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                type ENUM('assignment', 'quiz', 'test', 'link') NOT NULL,
                `order` INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
            )
        ");
        echo "Created content_items table\n";
    }
    
    // Create test course
    $stmt = $pdo->prepare("INSERT INTO courses (module_id, subject_name, subject_description, subject_price, is_required, subject_order) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$moduleId, 'Introduction to Programming', 'Basic programming concepts and fundamentals', 199.99, true, 1]);
    $courseId = $pdo->lastInsertId();
    echo "Created course with ID: $courseId\n";
    
    // Create test lessons
    $lessons = [
        ['Variables and Data Types', 'Learn about variables and different data types'],
        ['Control Structures', 'Understanding if statements, loops, and conditions'],
        ['Functions and Methods', 'Creating and using functions in programming']
    ];
    
    $lessonIds = [];
    foreach ($lessons as $index => $lesson) {
        $stmt = $pdo->prepare("INSERT INTO lessons (course_id, lesson_name, lesson_description, lesson_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courseId, $lesson[0], $lesson[1], $index + 1]);
        $lessonIds[] = $pdo->lastInsertId();
        echo "Created lesson: {$lesson[0]} (ID: {$pdo->lastInsertId()})\n";
    }
    
    // Create test content items
    $contentItems = [
        ['Assignment: Variable Practice', 'Practice creating and using variables', 'assignment'],
        ['Quiz: Data Types', 'Test your knowledge of data types', 'quiz'],
        ['Test: Programming Basics', 'Comprehensive test on basic programming', 'test']
    ];
    
    foreach ($lessonIds as $lessonIndex => $lessonId) {
        foreach ($contentItems as $contentIndex => $item) {
            $stmt = $pdo->prepare("INSERT INTO content_items (lesson_id, content_title, content_description, content_type, content_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$lessonId, $item[0] . " - Lesson " . ($lessonIndex + 1), $item[1], $item[2], $contentIndex + 1]);
            echo "Created content item: {$item[0]} - Lesson " . ($lessonIndex + 1) . "\n";
        }
    }
    
    echo "\nTest data created successfully!\n";
    echo "Module ID: $moduleId\n";
    echo "Course ID: $courseId\n";
    echo "Lessons: " . count($lessonIds) . "\n";
    echo "Content Items: " . (count($lessonIds) * count($contentItems)) . "\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
