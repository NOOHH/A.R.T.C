Route::get('/debug-quiz-system', function() {
    echo "<h1>Quiz System Debug</h1>";
    
    try {
        // 1. Check current session
        echo "<h2>1. Session Check</h2>";
        echo "<p>User ID: " . (session('user_id') ?? 'Not set') . "</p>";
        echo "<p>User Role: " . (session('user_role') ?? 'Not set') . "</p>";
        echo "<p>Logged in: " . (session('logged_in') ? 'Yes' : 'No') . "</p>";
        
        // 2. Check quiz routes
        echo "<h2>2. Quiz Routes Check</h2>";
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $quizRoutes = [];
        
        foreach ($routes as $route) {
            $uri = $route->uri();
            if (strpos($uri, 'quiz') !== false && strpos($uri, 'student') !== false) {
                $methods = implode('|', $route->methods());
                $name = $route->getName() ?? 'No name';
                $action = $route->getActionName();
                $quizRoutes[] = "[$methods] $uri → $action (name: $name)";
            }
        }
        
        if (empty($quizRoutes)) {
            echo "<p style='color: red;'>❌ No student quiz routes found</p>";
        } else {
            echo "<p>✅ Found " . count($quizRoutes) . " quiz routes:</p>";
            echo "<ul>";
            foreach ($quizRoutes as $route) {
                echo "<li>$route</li>";
            }
            echo "</ul>";
        }
        
        // 3. Check database tables
        echo "<h2>3. Database Tables Check</h2>";
        
        $tables = ['quizzes', 'quiz_questions', 'quiz_attempts', 'students', 'users'];
        foreach ($tables as $table) {
            try {
                $count = \Illuminate\Support\Facades\DB::table($table)->count();
                echo "<p>✅ Table '$table': $count records</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Table '$table': Error - " . $e->getMessage() . "</p>";
            }
        }
        
        // 4. Check quiz data
        echo "<h2>4. Quiz Data Check</h2>";
        
        try {
            $quizCount = \Illuminate\Support\Facades\DB::table('quizzes')->count();
            echo "<p>Total quizzes: $quizCount</p>";
            
            if ($quizCount > 0) {
                $sampleQuiz = \Illuminate\Support\Facades\DB::table('quizzes')->first();
                echo "<h3>Sample Quiz:</h3>";
                echo "<ul>";
                foreach ($sampleQuiz as $key => $value) {
                    echo "<li><strong>$key:</strong> $value</li>";
                }
                echo "</ul>";
                
                // Check questions for this quiz
                $questionCount = \Illuminate\Support\Facades\DB::table('quiz_questions')
                    ->where('quiz_id', $sampleQuiz->quiz_id)
                    ->count();
                echo "<p>Questions for this quiz: $questionCount</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error checking quiz data: " . $e->getMessage() . "</p>";
        }
        
        // 5. Check StudentDashboardController methods
        echo "<h2>5. Controller Methods Check</h2>";
        
        $controllerClass = new ReflectionClass(\App\Http\Controllers\StudentDashboardController::class);
        $methods = $controllerClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $quizMethods = [];
        
        foreach ($methods as $method) {
            if (strpos(strtolower($method->getName()), 'quiz') !== false) {
                $params = [];
                foreach ($method->getParameters() as $param) {
                    $paramStr = $param->getName();
                    if ($param->hasType()) {
                        $paramStr = $param->getType() . ' ' . $paramStr;
                    }
                    if ($param->isDefaultValueAvailable()) {
                        $paramStr .= ' = ' . var_export($param->getDefaultValue(), true);
                    }
                    $params[] = $paramStr;
                }
                $quizMethods[] = $method->getName() . '(' . implode(', ', $params) . ')';
            }
        }
        
        if (empty($quizMethods)) {
            echo "<p style='color: red;'>❌ No quiz methods found in StudentDashboardController</p>";
        } else {
            echo "<p>✅ Found quiz methods:</p>";
            echo "<ul>";
            foreach ($quizMethods as $method) {
                echo "<li>$method</li>";
            }
            echo "</ul>";
        }
        
        // 6. Check student authentication
        echo "<h2>6. Student Authentication Check</h2>";
        
        try {
            $student = \App\Models\Student::where('user_id', 1)->first();
            if ($student) {
                echo "<p>✅ Student found: ID {$student->student_id}</p>";
            } else {
                echo "<p style='color: red;'>❌ No student found for user_id 1</p>";
                
                // Try to find any student
                $anyStudent = \App\Models\Student::first();
                if ($anyStudent) {
                    echo "<p>Found sample student: ID {$anyStudent->student_id}, user_id: {$anyStudent->user_id}</p>";
                }
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error checking student: " . $e->getMessage() . "</p>";
        }
        
        // 7. Test route generation
        echo "<h2>7. Route Generation Test</h2>";
        
        try {
            $startRoute = route('student.quiz.start', ['quizId' => 1]);
            echo "<p>✅ Start route: $startRoute</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Start route error: " . $e->getMessage() . "</p>";
        }
        
        try {
            $takeRoute = route('student.quiz.take', ['attemptId' => 1]);
            echo "<p>✅ Take route: $takeRoute</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Take route error: " . $e->getMessage() . "</p>";
        }
        
        try {
            $submitRoute = route('student.quiz.submit', ['attemptId' => 1]);
            echo "<p>Submit route: $submitRoute</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Submit route error: " . $e->getMessage() . "</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Critical Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
    echo "<h2>8. Test Forms</h2>";
    echo "<h3>Test Quiz Start Route</h3>";
    echo "<form method='POST' action='/student/quiz/1/start'>";
    echo "<input type='hidden' name='_token' value='" . csrf_token() . "'>";
    echo "<button type='submit'>Test Quiz Start (POST)</button>";
    echo "</form>";
    
    echo "<h3>Test Quiz Take Route</h3>";
    echo "<p><a href='/student/quiz/take/1'>Test Quiz Take (GET)</a></p>";
    
    echo "<h2>9. Recommendations</h2>";
    echo "<ol>";
    echo "<li>Check if routes are properly defined for quiz submission with attemptId</li>";
    echo "<li>Verify controller method names match route definitions</li>";
    echo "<li>Ensure middleware allows student access to quiz routes</li>";
    echo "<li>Check if session authentication is working correctly</li>";
    echo "<li>Verify database relationships between users, students, quizzes, and attempts</li>";
    echo "</ol>";
});
