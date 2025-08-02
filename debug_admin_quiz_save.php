<?php
// File to debug issues with saving quizzes in admin panel

require_once __DIR__ . '/vendor/autoload.php';

// Set error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<style>
    body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
    h1, h2, h3 { color: #333; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; white-space: pre-wrap; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
</style>";

echo "<h1>Admin Quiz Save Debug Tool</h1>";

try {
    $db = new PDO("mysql:host=localhost;dbname=artc", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check quizzes table structure
    $stmt = $db->query("DESCRIBE quizzes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Quiz Table Columns</h2>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $hasInfiniteRetakes = false;
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            if ($key == 'Field' && $value == 'infinite_retakes') {
                $hasInfiniteRetakes = true;
            }
        }
        echo "</tr>";
    }
    echo "</table>";
    
    if (!$hasInfiniteRetakes) {
        echo "<p class='error'>WARNING: 'infinite_retakes' column is missing from the quizzes table!</p>";
        echo "<p>This is likely causing the 500 error when saving quizzes.</p>";
        
        echo "<h3>SQL to Add the Missing Column</h3>";
        echo "<pre>ALTER TABLE quizzes ADD COLUMN infinite_retakes TINYINT(1) DEFAULT 0 AFTER max_attempts;</pre>";
        
        // Add the column
        echo "<form method='post'>";
        echo "<input type='hidden' name='action' value='add_column'>";
        echo "<button type='submit' style='background: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer;'>Add 'infinite_retakes' Column</button>";
        echo "</form>";
    }
    
    // 2. Check recent quiz saves
    $stmt = $db->query("SELECT * FROM quizzes ORDER BY created_at DESC LIMIT 5");
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Recent Quizzes</h2>";
    
    if (count($quizzes) > 0) {
        echo "<table>";
        echo "<tr>";
        foreach (array_keys($quizzes[0]) as $key) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";
        
        foreach ($quizzes as $quiz) {
            echo "<tr>";
            foreach ($quiz as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p class='warning'>No quizzes found in database.</p>";
    }
    
    // 3. Handle adding the column if requested
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_column') {
        if (!$hasInfiniteRetakes) {
            try {
                $db->exec("ALTER TABLE quizzes ADD COLUMN infinite_retakes TINYINT(1) DEFAULT 0 AFTER max_attempts");
                echo "<p class='success'>Column 'infinite_retakes' added successfully!</p>";
                echo "<script>setTimeout(() => window.location.reload(), 2000);</script>";
            } catch (Exception $e) {
                echo "<p class='error'>Error adding column: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    // 4. Display saveQuizWithQuestions implementation details
    echo "<h2>Handling Quiz Saves in Controller</h2>";
    echo "<p>The Admin QuizGeneratorController likely needs to adapt to the database structure.</p>";
    
    echo "<h3>Key Points to Check</h3>";
    echo "<ul>";
    echo "<li>Does the validation match the actual column names in the database?</li>";
    echo "<li>Are required columns being populated?</li>";
    echo "<li>Are quiz questions correctly being associated with the quiz?</li>";
    echo "</ul>";
    
    echo "<h3>Test Save Payload</h3>";
    echo "<p>You can use this tool to test a simple quiz save:</p>";
    
    echo "<form id='testForm'>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Quiz Title</label>";
    echo "<input type='text' name='title' value='Test Quiz' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Description</label>";
    echo "<textarea name='description' style='width: 100%; padding: 8px;'>Test Description</textarea>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Program ID</label>";
    echo "<input type='number' name='program_id' value='41' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Module ID</label>";
    echo "<input type='number' name='module_id' value='79' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px;'>Course ID</label>";
    echo "<input type='number' name='course_id' value='52' style='width: 100%; padding: 8px;'>";
    echo "</div>";
    echo "<button type='button' onclick='testSaveQuiz()' style='padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer;'>Test Save Quiz</button>";
    echo "</form>";
    
    echo "<div id='result' style='margin-top: 20px;'></div>";
    
    // JavaScript for testing
    echo "<script>
    function testSaveQuiz() {
        const form = document.getElementById('testForm');
        
        const testData = {
            quiz_title: form.querySelector('input[name=\"title\"]').value,
            title: form.querySelector('input[name=\"title\"]').value,
            description: form.querySelector('textarea[name=\"description\"]').value,
            program_id: form.querySelector('input[name=\"program_id\"]').value,
            module_id: form.querySelector('input[name=\"module_id\"]').value,
            course_id: form.querySelector('input[name=\"course_id\"]').value,
            is_draft: true,
            admin_id: 1,
            questions: [
                {
                    question: 'Test question?',
                    question_type: 'multiple_choice',
                    options: ['Option A', 'Option B', 'Option C', 'Option D'],
                    correct_answer: '0',
                    explanation: 'Test explanation',
                    points: 1
                }
            ]
        };
        
        document.getElementById('result').innerHTML = '<p>Sending test save request...</p>';
        
        fetch('/admin/quiz-generator/save-quiz', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(testData)
        })
        .then(function(response) {
            if (!response.ok) {
                return response.text().then(function(text) {
                    throw new Error('Server returned ' + response.status + ': ' + text);
                });
            }
            return response.json();
        })
        .then(function(data) {
            document.getElementById('result').innerHTML = 
                '<pre style=\"background: #eef; padding: 15px;\">' + JSON.stringify(data, null, 2) + '</pre>';
            
            if (data.success) {
                document.getElementById('result').innerHTML += 
                    '<p class=\"success\">Save successful! Quiz ID: ' + data.quiz_id + '</p>';
            }
        })
        .catch(function(error) {
            document.getElementById('result').innerHTML = 
                '<p class=\"error\">Error: ' + error.message + '</p>';
        });
    }
    </script>";
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

// Add CSRF meta tag for testing
echo "<meta name='csrf-token' content='{{ csrf_token() }}' />";
?>
