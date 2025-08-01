<?php
/**
 * Fix script to correct the answer comparison issue in quiz attempts
 * 
 * Problem: User selects "A" (letter format) but correct answer is stored as "0" (index format)
 * Solution: Add a conversion function in the quiz take page JavaScript
 */

// Locate the quiz take blade template
$takeBladeFile = __DIR__ . '/resources/views/student/quiz/take.blade.php';

// Check if the file exists
if (!file_exists($takeBladeFile)) {
    echo "Error: Could not find take.blade.php file.\n";
    exit(1);
}

// Read the file content
$content = file_get_contents($takeBladeFile);

// Find the fetch call where the answers are submitted
if (strpos($content, 'body: JSON.stringify({ answers: answers })') === false) {
    echo "Error: Could not find the answers submission code.\n";
    exit(1);
}

// Create a function to convert letter answers to index answers
$letterToIndexConverter = <<<'EOT'
    // Convert letter answers (A, B, C) to index answers (0, 1, 2)
    function convertLetterAnswersToIndex(answers) {
        const convertedAnswers = {};
        
        for (const questionId in answers) {
            const answer = answers[questionId];
            
            // If answer is a letter (A, B, C, etc.)
            if (typeof answer === 'string' && answer.match(/^[A-Z]$/)) {
                // Convert to index (A=0, B=1, C=2, etc.)
                const index = answer.charCodeAt(0) - 65; // ASCII 'A' is 65
                convertedAnswers[questionId] = index.toString();
            } else {
                convertedAnswers[questionId] = answer;
            }
        }
        
        console.log('Original answers:', answers);
        console.log('Converted answers:', convertedAnswers);
        return convertedAnswers;
    }
EOT;

// Modify the submission code to convert answers before sending
$originalSubmission = 'body: JSON.stringify({ answers: answers })';
$modifiedSubmission = 'body: JSON.stringify({ answers: convertLetterAnswersToIndex(answers) })';

// Add the converter function before the submission code
$pattern = '/function\s+submitQuiz\(\)\s+\{/';
$replacement = "function submitQuiz() {\n    $letterToIndexConverter\n";

$content = preg_replace($pattern, $replacement, $content);

// Update the submission line to use the converter
$content = str_replace($originalSubmission, $modifiedSubmission, $content);

// Write the modified content back to the file
file_put_contents($takeBladeFile, $content);

echo "Successfully updated the quiz take page with the letter-to-index converter function.\n";
echo "The quiz should now correctly process letter-based answers (A, B, C) by converting them to index-based answers (0, 1, 2).\n";
