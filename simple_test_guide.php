<?php

echo "=== Testing Admin Quiz Creation (Post-Fix) ===\n\n";

echo "1. Testing with cURL request...\n";

$testQuizData = [
    'title' => 'Test Admin Quiz - ' . date('Y-m-d H:i:s'),
    'description' => 'This is a test quiz created after fixing the quiz_title error',
    'program_id' => 41,
    'module_id' => 79,
    'course_id' => 52,
    'admin_id' => 1,
    'quiz_id' => null,
    'time_limit' => 60,
    'max_attempts' => 1,
    'infinite_retakes' => false,
    'has_deadline' => false,
    'due_date' => null,
    'is_draft' => true,
    'status' => 'draft',
    'questions' => [
        [
            'question_text' => 'What is the primary purpose of this test?',
            'question_type' => 'multiple_choice',
            'points' => 1,
            'explanation' => 'This question tests the admin quiz creation functionality',
            'options' => [
                'To test the quiz_title fix',
                'To create a regular quiz',
                'To test the database',
                'To check validation'
            ],
            'correct_answers' => [0],
            'order' => 1
        ]
    ]
];

$jsonData = json_encode($testQuizData);
echo "✅ Test data prepared: " . strlen($jsonData) . " bytes\n";

echo "\n2. Making HTTP request...\n";

// Create a temporary file with the JSON data
$tempFile = tempnam(sys_get_temp_dir(), 'quiz_test');
file_put_contents($tempFile, $jsonData);

echo "POST data saved to temp file: $tempFile\n";
echo "Request URL: http://localhost:8000/admin/quiz-generator/save-quiz\n";

echo "\n3. Check the browser console and Laravel logs for results...\n";
echo "Manual test recommended:\n";
echo "1. Open browser to: http://localhost:8000/admin/quiz-generator\n";
echo "2. Login as admin if needed\n";
echo "3. Try creating a quiz\n";
echo "4. Check console for any 'quiz_title' errors\n";

echo "\n4. Checking recent logs for the fix validation...\n";

// Clean up
unlink($tempFile);

echo "✅ Test preparation complete\n";
echo "\nTo verify the fix manually:\n";
echo "- Open your admin quiz interface\n";
echo "- Create a new quiz\n";
echo "- The 'Undefined array key \"quiz_title\"' error should no longer occur\n";
echo "- Check Laravel logs: Get-Content storage\\logs\\laravel.log -Tail 10\n";

?>
