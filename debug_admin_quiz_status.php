<?php

// Debug script to test admin quiz status change functionality
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Set up logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Admin Quiz Status Debug Tool</h1>";
echo "<p>This tool helps diagnose issues with quiz status changes in the admin interface</p>";

// Check if quiz ID is provided
$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    // If no quiz_id, list all quizzes with links to test them
    $quizzes = Quiz::orderBy('created_at', 'desc')->limit(10)->get();
    
    echo "<h2>Select a quiz to debug:</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Created By</th><th>Actions</th></tr>";
    
    foreach ($quizzes as $quiz) {
        echo "<tr>";
        echo "<td>{$quiz->quiz_id}</td>";
        echo "<td>{$quiz->quiz_title}</td>";
        echo "<td>{$quiz->status}</td>";
        echo "<td>" . ($quiz->professor_id ? "Professor #{$quiz->professor_id}" : "Admin #{$quiz->admin_id}") . "</td>";
        echo "<td>
            <a href='?quiz_id={$quiz->quiz_id}&action=info'>View Info</a> | 
            <a href='?quiz_id={$quiz->quiz_id}&action=publish'>Publish</a> | 
            <a href='?quiz_id={$quiz->quiz_id}&action=draft'>Draft</a> | 
            <a href='?quiz_id={$quiz->quiz_id}&action=archive'>Archive</a>
        </td>";
        echo "</tr>";
    }
    
    echo "</table>";
    exit;
}

// Get the specified quiz
$quiz = Quiz::find($quiz_id);

if (!$quiz) {
    die("Quiz with ID {$quiz_id} not found");
}

// Display current quiz information
echo "<h2>Quiz Information (ID: {$quiz->quiz_id})</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Field</th><th>Value</th></tr>";
echo "<tr><td>Title</td><td>{$quiz->quiz_title}</td></tr>";
echo "<tr><td>Status</td><td>{$quiz->status}</td></tr>";
echo "<tr><td>is_draft</td><td>" . ($quiz->is_draft ? 'Yes' : 'No') . "</td></tr>";
echo "<tr><td>is_active</td><td>" . ($quiz->is_active ? 'Yes' : 'No') . "</td></tr>";
echo "<tr><td>Created By</td><td>" . ($quiz->professor_id ? "Professor #{$quiz->professor_id}" : "Admin #{$quiz->admin_id}") . "</td></tr>";
echo "<tr><td>Content ID</td><td>" . ($quiz->content_id ?? 'None') . "</td></tr>";

// Check content item if it exists
if ($quiz->content_id) {
    $contentItem = DB::table('content_items')->find($quiz->content_id);
    if ($contentItem) {
        echo "<tr><td>Content Status</td><td>" . ($contentItem->is_active ? 'Active' : 'Inactive') . "</td></tr>";
    } else {
        echo "<tr><td>Content Status</td><td>Content item does not exist</td></tr>";
    }
}

echo "</table>";

// Check route parameter structure
echo "<h2>API Routes Check</h2>";
echo "<ul>";
echo "<li>Admin publish route: /admin/quiz-generator/{$quiz_id}/publish</li>";
echo "<li>Admin archive route: /admin/quiz-generator/{$quiz_id}/archive</li>";
echo "<li>Admin draft route: /admin/quiz-generator/{$quiz_id}/draft</li>";
echo "</ul>";

// Process action if specified
$action = $_GET['action'] ?? null;

if ($action && in_array($action, ['publish', 'draft', 'archive'])) {
    echo "<h2>Processing Action: {$action}</h2>";
    
    try {
        switch ($action) {
            case 'publish':
                $quiz->status = 'published';
                $quiz->is_draft = false;
                $quiz->is_active = true;
                break;
            case 'draft':
                $quiz->status = 'draft';
                $quiz->is_draft = true;
                $quiz->is_active = false;
                break;
            case 'archive':
                $quiz->status = 'archived';
                $quiz->is_draft = false;
                $quiz->is_active = false;
                break;
        }
        
        $quiz->save();
        
        // Update content item if exists
        if ($quiz->content_id) {
            DB::table('content_items')
                ->where('id', $quiz->content_id)
                ->update(['is_active' => $action === 'publish']);
        }
        
        echo "<div style='color: green; font-weight: bold;'>Quiz status successfully changed to {$action}</div>";
        echo "<a href='?quiz_id={$quiz_id}'>Refresh to see changes</a>";
        
    } catch (Exception $e) {
        echo "<div style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</div>";
    }
}

echo "<hr>";
echo "<p><a href='javascript:history.back()'>Back</a> | <a href='?'>List All Quizzes</a></p>";
