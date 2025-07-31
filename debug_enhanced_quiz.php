<?php

require_once 'vendor/autoload.php';

// Simulate test data that mimics the problematic patterns
$testJsonData = [
    'multiple_choice_questions' => [
        [
            'question_number' => 1,
            'question_text' => 'What is MIT What?', // This should be caught and rejected
            'options' => [
                'A' => 'A software',
                'B' => 'A method',
                'C' => 'A type',
                'D' => 'A system'
            ],
            'correct_answer' => 'A',
            'explanation' => 'Short'
        ],
        [
            'question_number' => 2,
            'question_text' => 'According to the document, what is the primary purpose of the Shared Responsibility Model in cloud computing?',
            'options' => [
                'A' => 'A framework that defines security responsibilities between cloud providers and customers',
                'B' => 'A method for encrypting data in transit and at rest in cloud environments',
                'C' => 'A process for monitoring and logging all activities within cloud infrastructure',
                'D' => 'A standard for compliance and regulatory requirements in cloud services'
            ],
            'correct_answer' => 'A',
            'explanation' => 'The document clearly states that the Shared Responsibility Model divides security responsibilities between providers and customers.'
        ]
    ],
    'true_false_questions' => [
        [
            'question_number' => 3,
            'statement' => 'Short',
            'correct_answer' => 'True',
            'explanation' => 'Too short'
        ],
        [
            'question_number' => 4,
            'statement' => 'The cloud provider is responsible for securing the underlying infrastructure including physical security and network controls',
            'correct_answer' => 'True',
            'explanation' => 'According to the document, cloud providers handle infrastructure security including physical and network controls.'
        ]
    ]
];

echo "<h1>Enhanced Validation Test</h1>\n";

// Test the validation function
use App\Services\GeminiQuizService;
$service = new GeminiQuizService();

// Use reflection to access the private method
$reflectionClass = new ReflectionClass($service);
$method = $reflectionClass->getMethod('validateStructuredQuizData');
$method->setAccessible(true);

echo "<h2>Testing Validation...</h2>\n";

$isValid = $method->invoke($service, $testJsonData);

echo "<p><strong>Validation Result:</strong> " . ($isValid ? 'PASSED' : 'FAILED') . "</p>\n";

if (!$isValid) {
    echo "<p style='color: green;'>✅ Good! Validation correctly rejected poor quality questions.</p>\n";
} else {
    echo "<p style='color: red;'>❌ Problem! Validation should have rejected the poor quality questions.</p>\n";
}

echo "<h2>Test Questions Analysis:</h2>\n";

// Analyze each question manually
foreach ($testJsonData['multiple_choice_questions'] as $index => $mcq) {
    echo "<h3>MCQ " . ($index + 1) . ":</h3>\n";
    echo "<p><strong>Question:</strong> " . $mcq['question_text'] . "</p>\n";
    echo "<p><strong>Length:</strong> " . strlen($mcq['question_text']) . " characters</p>\n";
    
    $issues = [];
    
    // Check for known bad patterns
    if (preg_match('/^What is \w+ What\?/i', $mcq['question_text'])) {
        $issues[] = "Contains 'What is X What?' pattern";
    }
    
    if (strlen($mcq['question_text']) < 30) {
        $issues[] = "Question too short (< 30 chars)";
    }
    
    foreach ($mcq['options'] as $letter => $option) {
        if (strlen($option) < 15) {
            $issues[] = "Option {$letter} too short (< 15 chars)";
        }
        if (preg_match('/^A \w+$/i', $option)) {
            $issues[] = "Option {$letter} is a fragment";
        }
    }
    
    if (!empty($issues)) {
        echo "<p style='color: red;'><strong>Issues Found:</strong> " . implode(', ', $issues) . "</p>\n";
    } else {
        echo "<p style='color: green;'><strong>Quality Check: PASSED</strong></p>\n";
    }
}

echo "<h2>Expected Results:</h2>\n";
echo "<p>❌ MCQ 1 should be rejected (vague question, short options)</p>\n";
echo "<p>✅ MCQ 2 should be accepted (good quality)</p>\n";
echo "<p>❌ T/F 1 should be rejected (too short)</p>\n";
echo "<p>✅ T/F 2 should be accepted (good quality)</p>\n";

?>
