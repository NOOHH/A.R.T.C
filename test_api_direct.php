<?php
// Simple test to verify the API endpoint works
$url = "http://127.0.0.1:8000/get-module-courses?module_id=76&user_id=174";

echo "Testing URL: $url\n\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Accept: application/json',
            'Content-Type: application/json'
        ]
    ]
]);

$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo "Failed to fetch data from API\n";
    echo "Response headers: " . implode("\n", $http_response_header ?? []) . "\n";
} else {
    echo "API Response:\n";
    $data = json_decode($result, true);
    
    if ($data) {
        echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        
        if (isset($data['courses'])) {
            echo "Courses found: " . count($data['courses']) . "\n\n";
            
            foreach ($data['courses'] as $course) {
                echo "Course: {$course['course_name']} (ID: {$course['course_id']})\n";
                echo "Already Enrolled: " . ($course['already_enrolled'] ? 'YES' : 'NO') . "\n";
                echo "Should show 'Already Enrolled' badge: " . ($course['already_enrolled'] ? 'YES' : 'NO') . "\n\n";
            }
        }
    } else {
        echo "Raw response: $result\n";
    }
}
?>
