<?php
echo "🔍 DEEP ERROR ANALYSIS FOR MODELNOTFOUNDEXCEPTION\n";
echo "=" . str_repeat("=", 50) . "\n\n";

/**
 * Extract detailed error information from the responses
 */

$testUrls = [
    'http://127.0.0.1:8000/t/draft/test1/admin/students/archived?website=15' => 'Students Archived',
    'http://127.0.0.1:8000/t/draft/test1/admin/professors/archived?website=15' => 'Professors Archived'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'ignore_errors' => true,
        'header' => "User-Agent: PHP Debug Client\r\n"
    ]
]);

foreach ($testUrls as $url => $description) {
    echo "🔍 Deep analysis for $description:\n";
    echo "   URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        // Extract the full error message
        if (preg_match('/<title>([^<]+)<\/title>/', $response, $titleMatches)) {
            echo "   📄 PAGE TITLE: " . trim($titleMatches[1]) . "\n";
        }
        
        // Look for model name
        if (preg_match('/No query results for model \[([^\]]+)\]/', $response, $modelMatches)) {
            echo "   🏷️  MODEL: " . $modelMatches[1] . "\n";
        }
        
        // Look for file and line information
        if (preg_match('/vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Database\\\\Eloquent\\\\Builder\.php["\s]*line["\s]*(\d+)/', $response, $builderMatches)) {
            echo "   📍 BUILDER LINE: " . $builderMatches[1] . "\n";
        }
        
        // Look for the actual method that failed
        if (preg_match('/at ([^\\\\]+\\\\[^\\\\]+\\\\[^\\\\]+)\->([^\(]+)\(/', $response, $methodMatches)) {
            echo "   🎯 FAILED METHOD: " . $methodMatches[1] . "->" . $methodMatches[2] . "()\n";
        }
        
        // Look for stack trace to find where it's called from
        if (preg_match('/vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Routing\\\\([^"]+)"[^:]*line["\s]*(\d+)/', $response, $routeMatches)) {
            echo "   🛣️  ROUTING FILE: " . $routeMatches[1] . " (line " . $routeMatches[2] . ")\n";
        }
        
        // Look for view compilation errors
        if (preg_match('/resources\\\\views\\\\([^"]+\.blade\.php)/', $response, $viewMatches)) {
            echo "   📋 VIEW FILE: " . $viewMatches[1] . "\n";
        }
        
        // Look for specific Laravel files that might give us clues
        preg_match_all('/at ([^\\\\]+(?:\\\\[^\\\\]+)*)\->([^\(]+)\([^)]*\)[^"]*"[^:]*line["\s]*(\d+)/', $response, $allMatches, PREG_SET_ORDER);
        
        if (!empty($allMatches)) {
            echo "   📋 CALL STACK:\n";
            foreach (array_slice($allMatches, 0, 5) as $match) {
                echo "      - " . basename($match[1]) . "->" . $match[2] . "() (line " . $match[3] . ")\n";
            }
        }
        
        // Check if it's related to pagination
        if (strpos($response, 'pagination') !== false || strpos($response, 'paginate') !== false) {
            echo "   📄 PAGINATION: Pagination-related issue detected\n";
        }
        
        // Check if it's related to relationships
        if (strpos($response, 'relationship') !== false || strpos($response, 'with(') !== false) {
            echo "   🔗 RELATIONSHIPS: Model relationship issue detected\n";
        }
        
        // Extract the first few lines of the error HTML to see what's really happening
        preg_match('/<div class="exception-message"[^>]*>([^<]+)</', $response, $exceptionMatches);
        if (isset($exceptionMatches[1])) {
            echo "   💬 EXCEPTION: " . trim($exceptionMatches[1]) . "\n";
        }
        
    } else {
        echo "   ❌ NO RESPONSE: Cannot reach URL\n";
    }
    echo "\n";
}

echo "🔍 ADDITIONAL CHECKS:\n";
echo "=" . str_repeat("-", 30) . "\n";

// Let's also check what routes are actually registered
echo "📋 Route verification:\n";
$routesList = shell_exec('cd c:\\xampp\\htdocs\\A.R.T.C && php artisan route:list --path=draft');
if ($routesList) {
    $lines = explode("\n", $routesList);
    foreach ($lines as $line) {
        if (strpos($line, 'archived') !== false) {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "   ❌ Could not retrieve route list\n";
}

echo "\n💡 INVESTIGATION SUMMARY:\n";
echo "=" . str_repeat("-", 30) . "\n";
echo "Looking for clues about what's causing the ModelNotFoundException\n";
echo "Checking if it's in view rendering, model relationships, or routing\n";
?>
