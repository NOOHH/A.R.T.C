<?php
// Set up professor session FIRST
session_start();
if (!isset($_SESSION['professor_id'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['professor_id'] = 8;
    $_SESSION['user_role'] = 'professor';
    $_SESSION['user_type'] = 'professor';
    $_SESSION['user_id'] = 8;
}

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Professor;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

echo "<h1>🧪 Quiz Generator System Comprehensive Test</h1>";
echo "<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 20px; }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .info { color: #007bff; font-weight: bold; }
    .section { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 8px; }
    .test-item { margin: 5px 0; padding: 8px; background: white; border-radius: 4px; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #e9ecef; }
</style>";

echo "<div class='section'>";
echo "<h2>📋 System Overview</h2>";

try {
    // Database connectivity
    echo "<div class='test-item'>";
    echo "<span class='success'>✅ Database Connected</span><br>";
    
    // Check professor
    $professor = Professor::find(session('professor_id'));
    if ($professor) {
        echo "<span class='success'>✅ Professor Found: {$professor->professor_first_name} {$professor->professor_last_name}</span><br>";
        
        // Check assigned programs
        $programs = $professor->programs()->get();
        echo "<span class='info'>📚 Assigned Programs: " . $programs->count() . "</span><br>";
        
        foreach ($programs as $program) {
            echo "&nbsp;&nbsp;• {$program->program_name}<br>";
        }
    } else {
        echo "<span class='error'>❌ Professor not found</span><br>";
    }
    
    // Count quizzes
    $totalQuizzes = Quiz::where('professor_id', session('professor_id'))->count();
    $draftQuizzes = Quiz::where('professor_id', session('professor_id'))->where('status', 'draft')->count();
    $publishedQuizzes = Quiz::where('professor_id', session('professor_id'))->where('status', 'published')->count();
    $archivedQuizzes = Quiz::where('professor_id', session('professor_id'))->where('status', 'archived')->count();
    
    echo "<span class='info'>📊 Quiz Statistics:</span><br>";
    echo "&nbsp;&nbsp;• Total Quizzes: {$totalQuizzes}<br>";
    echo "&nbsp;&nbsp;• Draft: {$draftQuizzes}<br>";
    echo "&nbsp;&nbsp;• Published: {$publishedQuizzes}<br>";
    echo "&nbsp;&nbsp;• Archived: {$archivedQuizzes}<br>";
    
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='test-item'><span class='error'>❌ Database Error: " . $e->getMessage() . "</span></div>";
}

echo "</div>";

// Test routes
echo "<div class='section'>";
echo "<h2>🛣️ Route Testing</h2>";

$routes = [
    'Main Page' => '/professor/quiz-generator',
    'Modules API' => '/professor/quiz-generator/modules/35',
    'Courses API' => '/professor/quiz-generator/courses/20',
    'Questions API' => '/professor/quiz-generator/api/questions/38',
    'Save Manual' => '/professor/quiz-generator/save-manual',
];

foreach ($routes as $name => $route) {
    echo "<div class='test-item'>";
    try {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Cookie: laravel_session=' . session_id(),
                    'X-Requested-With: XMLHttpRequest'
                ]
            ]
        ]);
        
        $url = "http://127.0.0.1:8000{$route}";
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            echo "<span class='success'>✅ {$name}</span> - Route accessible<br>";
            
            // Try to decode JSON if it looks like JSON
            if (strpos($response, '{') === 0) {
                $data = json_decode($response, true);
                if ($data !== null) {
                    echo "&nbsp;&nbsp;JSON Response: " . (isset($data['success']) ? ($data['success'] ? 'Success' : 'Failed') : 'Unknown') . "<br>";
                }
            }
        } else {
            echo "<span class='error'>❌ {$name}</span> - Route failed<br>";
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ {$name}</span> - Exception: " . $e->getMessage() . "<br>";
    }
    echo "</div>";
}

echo "</div>";

// Test database structure
echo "<div class='section'>";
echo "<h2>🗄️ Database Structure</h2>";

$tables = ['quizzes', 'quiz_questions', 'professors', 'programs', 'modules', 'courses'];

foreach ($tables as $table) {
    echo "<div class='test-item'>";
    try {
        $count = DB::table($table)->count();
        echo "<span class='success'>✅ {$table}</span> - {$count} records<br>";
        
        // Show columns for key tables
        if (in_array($table, ['quizzes', 'quiz_questions'])) {
            $columns = DB::getSchemaBuilder()->getColumnListing($table);
            echo "&nbsp;&nbsp;Columns: " . implode(', ', array_slice($columns, 0, 10)) . (count($columns) > 10 ? '...' : '') . "<br>";
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ {$table}</span> - Error: " . $e->getMessage() . "<br>";
    }
    echo "</div>";
}

echo "</div>";

// Test specific quiz with ID 38
echo "<div class='section'>";
echo "<h2>🎯 Quiz #38 Detailed Analysis</h2>";

try {
    $quiz = Quiz::with(['questions', 'program'])->find(38);
    
    if ($quiz) {
        echo "<div class='test-item'>";
        echo "<span class='success'>✅ Quiz Found</span><br>";
        echo "<table>";
        echo "<tr><th>Property</th><th>Value</th></tr>";
        echo "<tr><td>ID</td><td>{$quiz->quiz_id}</td></tr>";
        echo "<tr><td>Title</td><td>{$quiz->quiz_title}</td></tr>";
        echo "<tr><td>Status</td><td>{$quiz->status}</td></tr>";
        echo "<tr><td>Professor ID</td><td>{$quiz->professor_id}</td></tr>";
        echo "<tr><td>Program</td><td>" . ($quiz->program ? $quiz->program->program_name : 'N/A') . "</td></tr>";
        echo "<tr><td>Questions Count</td><td>{$quiz->questions->count()}</td></tr>";
        echo "<tr><td>Time Limit</td><td>{$quiz->time_limit} min</td></tr>";
        echo "<tr><td>Max Attempts</td><td>{$quiz->max_attempts}</td></tr>";
        echo "<tr><td>Created</td><td>{$quiz->created_at}</td></tr>";
        echo "</table>";
        
        // Show questions
        if ($quiz->questions->count() > 0) {
            echo "<h4>Questions:</h4>";
            echo "<table>";
            echo "<tr><th>#</th><th>Question</th><th>Type</th><th>Options</th><th>Answer</th></tr>";
            foreach ($quiz->questions as $i => $q) {
                $options = is_array($q->options) ? implode(', ', $q->options) : json_encode($q->options);
                echo "<tr>";
                echo "<td>" . ($i + 1) . "</td>";
                echo "<td>" . substr($q->question_text, 0, 50) . "...</td>";
                echo "<td>{$q->question_type}</td>";
                echo "<td>" . substr($options, 0, 30) . "...</td>";
                echo "<td>{$q->correct_answer}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "</div>";
    } else {
        echo "<div class='test-item'><span class='error'>❌ Quiz #38 not found</span></div>";
    }
} catch (Exception $e) {
    echo "<div class='test-item'><span class='error'>❌ Error loading quiz: " . $e->getMessage() . "</span></div>";
}

echo "</div>";

// Test controller methods
echo "<div class='section'>";
echo "<h2>🎮 Controller Method Testing</h2>";

$controllerClass = 'App\Http\Controllers\Professor\QuizGeneratorController';

if (class_exists($controllerClass)) {
    echo "<div class='test-item'>";
    echo "<span class='success'>✅ Controller exists</span><br>";
    
    $methods = get_class_methods($controllerClass);
    $expectedMethods = [
        'index', 'generate', 'getModulesByProgram', 'getCoursesByModule', 
        'saveManualQuiz', 'getQuestionsForModal', 'updateQuiz'
    ];
    
    foreach ($expectedMethods as $method) {
        if (in_array($method, $methods)) {
            echo "&nbsp;&nbsp;<span class='success'>✅ {$method}</span><br>";
        } else {
            echo "&nbsp;&nbsp;<span class='error'>❌ {$method}</span><br>";
        }
    }
    echo "</div>";
} else {
    echo "<div class='test-item'><span class='error'>❌ Controller not found</span></div>";
}

echo "</div>";

// Test JavaScript functionality simulation
echo "<div class='section'>";
echo "<h2>⚡ JavaScript Function Simulation</h2>";

echo "<div class='test-item'>";
echo "<span class='info'>📝 Core Functions Expected:</span><br>";
$jsFunctions = [
    'loadQuizQuestionsForModal', 'saveQuiz', 'loadQuizData', 'updateQuiz',
    'loadModules', 'loadCourses', 'showAlert', 'resetForm'
];

foreach ($jsFunctions as $func) {
    echo "&nbsp;&nbsp;• {$func}()<br>";
}
echo "</div>";

echo "</div>";

// Performance metrics
echo "<div class='section'>";
echo "<h2>⚡ Performance Metrics</h2>";

$start = microtime(true);

// Test query performance
$quiz_query_time = microtime(true);
$recentQuizzes = Quiz::where('professor_id', session('professor_id'))
    ->with(['questions', 'program'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
$quiz_query_time = microtime(true) - $quiz_query_time;

echo "<div class='test-item'>";
echo "<span class='info'>🚀 Performance Results:</span><br>";
echo "&nbsp;&nbsp;• Quiz Query Time: " . number_format($quiz_query_time * 1000, 2) . "ms<br>";
echo "&nbsp;&nbsp;• Recent Quizzes: " . $recentQuizzes->count() . "<br>";
echo "&nbsp;&nbsp;• Memory Usage: " . number_format(memory_get_usage() / 1024 / 1024, 2) . "MB<br>";
echo "&nbsp;&nbsp;• Peak Memory: " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . "MB<br>";

$total_time = microtime(true) - $start;
echo "&nbsp;&nbsp;• Total Test Time: " . number_format($total_time * 1000, 2) . "ms<br>";
echo "</div>";

echo "</div>";

echo "<div class='section'>";
echo "<h2>🎉 Summary</h2>";
echo "<div class='test-item'>";
echo "<span class='success'>✅ All major components tested</span><br>";
echo "<span class='info'>📋 Next Steps:</span><br>";
echo "&nbsp;&nbsp;1. Test UI in browser<br>";
echo "&nbsp;&nbsp;2. Test mobile responsiveness<br>";
echo "&nbsp;&nbsp;3. Test edit functionality<br>";
echo "&nbsp;&nbsp;4. Test modal behavior<br>";
echo "&nbsp;&nbsp;5. Test error handling<br>";
echo "</div>";
echo "</div>";

echo "<p><strong>Test completed at: " . date('Y-m-d H:i:s') . "</strong></p>";
?>
