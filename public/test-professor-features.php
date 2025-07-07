<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Features Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Professor Features Implementation Test</h1>
        
        <div class="alert alert-info">
            <h5>Professor Dashboard Features Test</h5>
            <p>This page tests all the newly implemented professor features including attendance tracking, grading, AI quiz generation, and dynamic profile forms.</p>
        </div>

        <?php
        // Database connection test
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=artc", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<div class="alert alert-success">✅ Database connection successful</div>';
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">❌ Database connection failed: ' . $e->getMessage() . '</div>';
            exit;
        }

        // Test table existence
        $tables = ['attendance', 'student_grades', 'quiz_questions', 'admin_settings'];
        echo '<h3>Database Tables</h3>';
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo '<div class="alert alert-success">✅ Table `' . $table . '` exists</div>';
                } else {
                    echo '<div class="alert alert-danger">❌ Table `' . $table . '` missing</div>';
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">❌ Error checking table `' . $table . '`: ' . $e->getMessage() . '</div>';
            }
        }

        // Test AI Quiz setting
        echo '<h3>Admin Settings</h3>';
        try {
            $stmt = $pdo->query("SELECT * FROM admin_settings WHERE setting_key = 'ai_quiz_enabled'");
            $setting = $stmt->fetch();
            if ($setting) {
                echo '<div class="alert alert-success">✅ AI Quiz setting exists: ' . ($setting['setting_value'] == 'true' ? 'Enabled' : 'Disabled') . '</div>';
            } else {
                echo '<div class="alert alert-warning">⚠️ AI Quiz setting not found</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">❌ Error checking AI Quiz setting: ' . $e->getMessage() . '</div>';
        }

        // Test professors table dynamic_data column
        echo '<h3>Professor Dynamic Data</h3>';
        try {
            $stmt = $pdo->query("DESCRIBE professors");
            $columns = $stmt->fetchAll();
            $hasDynamicData = false;
            foreach ($columns as $column) {
                if ($column['Field'] == 'dynamic_data') {
                    $hasDynamicData = true;
                    break;
                }
            }
            if ($hasDynamicData) {
                echo '<div class="alert alert-success">✅ Professors table has dynamic_data column</div>';
            } else {
                echo '<div class="alert alert-warning">⚠️ Professors table missing dynamic_data column</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">❌ Error checking professors table: ' . $e->getMessage() . '</div>';
        }

        // File structure test
        echo '<h3>File Structure</h3>';
        $files = [
            '../app/Http/Controllers/ProfessorAttendanceController.php' => 'Attendance Controller',
            '../app/Http/Controllers/ProfessorGradingController.php' => 'Grading Controller',
            '../app/Http/Controllers/AIQuizController.php' => 'AI Quiz Controller',
            '../resources/views/professor/attendance/index.blade.php' => 'Attendance View',
            '../resources/views/professor/attendance/reports.blade.php' => 'Attendance Reports View',
            '../resources/views/professor/grading/index.blade.php' => 'Grading View',
            '../resources/views/professor/grading/student-details.blade.php' => 'Student Details View',
            '../resources/views/professor/quiz-generator.blade.php' => 'AI Quiz Generator View',
            '../resources/views/professor/quiz-preview.blade.php' => 'Quiz Preview View',
        ];

        foreach ($files as $file => $description) {
            if (file_exists($file)) {
                echo '<div class="alert alert-success">✅ ' . $description . ' exists</div>';
            } else {
                echo '<div class="alert alert-danger">❌ ' . $description . ' missing: ' . $file . '</div>';
            }
        }

        // Test sample data insertion (if tables exist)
        if ($pdo->query("SHOW TABLES LIKE 'attendance'")->rowCount() > 0) {
            echo '<h3>Sample Data Test</h3>';
            try {
                // Check if we have professors and students to work with
                $profCount = $pdo->query("SELECT COUNT(*) FROM professors")->fetchColumn();
                $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
                $programCount = $pdo->query("SELECT COUNT(*) FROM programs")->fetchColumn();
                
                echo '<div class="alert alert-info">Available Data: ' . $profCount . ' professors, ' . $studentCount . ' students, ' . $programCount . ' programs</div>';
                
                if ($profCount > 0 && $studentCount > 0 && $programCount > 0) {
                    // Get sample IDs
                    $professor = $pdo->query("SELECT professor_id FROM professors LIMIT 1")->fetch();
                    $student = $pdo->query("SELECT student_id FROM students LIMIT 1")->fetch();
                    $program = $pdo->query("SELECT program_id FROM programs LIMIT 1")->fetch();
                    
                    if ($professor && $student && $program) {
                        // Test attendance record
                        $stmt = $pdo->prepare("INSERT IGNORE INTO attendance (student_id, program_id, professor_id, attendance_date, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$student['student_id'], $program['program_id'], $professor['professor_id'], date('Y-m-d'), 'present', 'Test attendance record']);
                        
                        // Test grade record
                        $stmt = $pdo->prepare("INSERT IGNORE INTO student_grades (student_id, program_id, professor_id, assignment_name, grade, max_points, feedback, graded_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$student['student_id'], $program['program_id'], $professor['professor_id'], 'Test Assignment', 85.5, 100, 'Good work!', date('Y-m-d H:i:s')]);
                        
                        // Test quiz question
                        $stmt = $pdo->prepare("INSERT IGNORE INTO quiz_questions (quiz_title, program_id, question_text, question_type, correct_answer, created_by_professor) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute(['Sample Quiz', $program['program_id'], 'What is 2+2?', 'multiple_choice', '4', $professor['professor_id']]);
                        
                        echo '<div class="alert alert-success">✅ Sample data inserted successfully</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-warning">⚠️ Sample data insertion: ' . $e->getMessage() . '</div>';
            }
        }

        echo '<h3>Next Steps</h3>';
        echo '<div class="alert alert-primary">';
        echo '<h5>To test the professor features:</h5>';
        echo '<ol>';
        echo '<li>Log in as a professor at <a href="../professor/login">/professor/login</a></li>';
        echo '<li>Navigate to the dashboard to see the new feature cards</li>';
        echo '<li>Test attendance management at <a href="../professor/attendance">/professor/attendance</a></li>';
        echo '<li>Test grading at <a href="../professor/grading">/professor/grading</a></li>';
        echo '<li>Test AI quiz generator at <a href="../professor/quiz-generator">/professor/quiz-generator</a> (if enabled)</li>';
        echo '<li>Test dynamic profile form at <a href="../professor/profile">/professor/profile</a></li>';
        echo '</ol>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
