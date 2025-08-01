<?php
/**
 * Find quiz submission routes and controllers
 */

echo "=== SEARCHING FOR QUIZ SUBMISSION HANDLERS ===\n\n";

// Search in all PHP files for quiz submission related code
$searchDirectories = [
    __DIR__ . '/app',
    __DIR__ . '/routes'
];

$patterns = [
    'student.quiz.submit',
    'quiz/submit',
    'quiz.*submit',
    'submitQuiz',
    'Route::post.*quiz',
    'function.*submit'
];

foreach ($searchDirectories as $dir) {
    echo "Searching in: $dir\n";
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            foreach ($patterns as $pattern) {
                if (stripos($content, str_replace('.*', '', $pattern)) !== false) {
                    echo "Found in: " . $file->getPathname() . "\n";
                    
                    // Show relevant lines
                    $lines = explode("\n", $content);
                    foreach ($lines as $lineNum => $line) {
                        if (stripos($line, str_replace('.*', '', $pattern)) !== false) {
                            echo "  Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
                        }
                    }
                    echo "\n";
                    break;
                }
            }
        }
    }
}

// Also check if there are any AJAX endpoints defined in JavaScript files
echo "\n=== CHECKING JAVASCRIPT FILES FOR QUIZ ENDPOINTS ===\n";
$jsFiles = glob(__DIR__ . '/resources/views/**/*.blade.php');
foreach ($jsFiles as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'student.quiz.submit') !== false || 
        strpos($content, 'quiz/submit') !== false ||
        strpos($content, 'submitQuiz') !== false) {
        echo "Found quiz submission code in: $file\n";
        
        // Extract the relevant lines
        $lines = explode("\n", $content);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, 'student.quiz.submit') !== false || 
                strpos($line, 'quiz/submit') !== false ||
                strpos($line, 'fetch(') !== false && strpos($line, 'quiz') !== false) {
                echo "  Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
            }
        }
        echo "\n";
    }
}

echo "=== SEARCH COMPLETED ===\n";
