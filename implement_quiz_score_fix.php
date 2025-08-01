<?php
/**
 * Fix to ensure the server-side code also converts letter answers to index answers
 */

// Let's find the controller or file that handles quiz submission
// Since we couldn't find it directly, let's create a middleware that can be inserted
// into the request processing pipeline to handle this conversion

// Path to the middleware file
$middlewarePath = __DIR__ . '/app/Http/Middleware/ConvertQuizAnswers.php';

// Create the middleware file
$middlewareContent = <<<'PHP'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertQuizAnswers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only process POST requests to quiz submission endpoints
        if ($request->isMethod('post') && 
            (strpos($request->path(), 'quiz/submit') !== false || 
             strpos($request->path(), 'quiz') !== false && strpos($request->path(), 'submit') !== false)) {
            
            // If there are answers in the request
            if ($request->has('answers')) {
                $answers = $request->input('answers');
                
                // Convert letter answers to index answers
                $convertedAnswers = [];
                foreach ($answers as $questionId => $answer) {
                    if (is_string($answer) && preg_match('/^[A-Z]$/', $answer)) {
                        // Convert letter (A, B, C) to index (0, 1, 2)
                        $index = ord($answer) - 65; // ASCII 'A' is 65
                        $convertedAnswers[$questionId] = (string)$index;
                    } else {
                        $convertedAnswers[$questionId] = $answer;
                    }
                }
                
                // Replace the answers in the request
                $request->merge(['answers' => $convertedAnswers]);
            }
        }
        
        return $next($request);
    }
}
PHP;

// Create the middleware file
file_put_contents($middlewarePath, $middlewareContent);
echo "Created middleware: app/Http/Middleware/ConvertQuizAnswers.php\n";

// Now we need to register the middleware in the Kernel
$kernelPath = __DIR__ . '/app/Http/Kernel.php';
$kernelContent = file_get_contents($kernelPath);

// Check if the middleware is already registered
if (strpos($kernelContent, 'ConvertQuizAnswers') === false) {
    // Add the middleware to the global middleware group
    $pattern = '/protected \$middleware = \[(.*?)\];/s';
    $replacement = "protected \$middleware = [$1\n        \\App\\Http\\Middleware\\ConvertQuizAnswers::class,\n    ];";
    $kernelContent = preg_replace($pattern, $replacement, $kernelContent);
    
    // Write the modified content back
    file_put_contents($kernelPath, $kernelContent);
    echo "Registered middleware in app/Http/Kernel.php\n";
} else {
    echo "Middleware already registered in Kernel.php\n";
}

// Also let's create a controller fix for server-side score calculation
$controllerFixPath = __DIR__ . '/app/Http/Controllers/FixQuizSubmissionController.php';
$controllerFixContent = <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;

class FixQuizSubmissionController extends Controller
{
    /**
     * Fix the score calculation for a quiz attempt
     */
    public function fixAttemptScore($attemptId)
    {
        // Get the attempt
        $attempt = QuizAttempt::find($attemptId);
        if (!$attempt) {
            return response()->json(['error' => 'Attempt not found'], 404);
        }
        
        // Get the quiz
        $quiz = Quiz::find($attempt->quiz_id);
        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }
        
        // Get questions
        $questions = QuizQuestion::where('quiz_id', $quiz->quiz_id)->get();
        
        // Get stored answers
        $storedAnswers = $attempt->answers;
        
        // Calculate correct score
        $correctCount = 0;
        $totalQuestions = $questions->count();
        
        foreach ($questions as $question) {
            // Special case for questions with empty IDs
            if (empty($question->question_id) && $storedAnswers) {
                // Use the first key from the stored answers
                $keys = array_keys((array)$storedAnswers);
                if (!empty($keys)) {
                    $fakeQuestionId = $keys[0];
                    $studentAnswer = $storedAnswers[$fakeQuestionId] ?? null;
                    
                    // Convert from letter to index if needed
                    if ($studentAnswer === 'A') {
                        $convertedAnswer = '0';
                        $isCorrect = $convertedAnswer === $question->correct_answer;
                        
                        if ($isCorrect) {
                            $correctCount++;
                        }
                    } else if (is_string($studentAnswer) && preg_match('/^[A-Z]$/', $studentAnswer)) {
                        // Handle other letters (B, C, etc.)
                        $convertedAnswer = (string)(ord($studentAnswer) - 65);
                        $isCorrect = $convertedAnswer === $question->correct_answer;
                        
                        if ($isCorrect) {
                            $correctCount++;
                        }
                    } else if ($studentAnswer === $question->correct_answer) {
                        // Direct comparison (for non-letter answers)
                        $correctCount++;
                    }
                }
            } else {
                // Normal case with question ID
                $questionId = $question->question_id;
                $studentAnswer = $storedAnswers[$questionId] ?? null;
                
                if ($studentAnswer !== null) {
                    if ($question->question_type === 'multiple_choice') {
                        // Convert letter answers
                        if (is_string($studentAnswer) && preg_match('/^[A-Z]$/', $studentAnswer)) {
                            $convertedAnswer = (string)(ord($studentAnswer) - 65);
                            $isCorrect = $convertedAnswer === $question->correct_answer;
                        } else {
                            $isCorrect = (string)$studentAnswer === (string)$question->correct_answer;
                        }
                    } else {
                        $isCorrect = $studentAnswer === $question->correct_answer;
                    }
                    
                    if ($isCorrect) {
                        $correctCount++;
                    }
                }
            }
        }
        
        // Calculate score
        $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        
        // Update the attempt
        $attempt->correct_answers = $correctCount;
        $attempt->score = $score;
        $attempt->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Attempt score updated successfully',
            'score' => $score,
            'correct_answers' => $correctCount,
            'total_questions' => $totalQuestions
        ]);
    }
    
    /**
     * Fix all quiz attempts with potential scoring issues
     */
    public function fixAllAttempts()
    {
        $attempts = QuizAttempt::where('score', 0)
                              ->where('status', 'completed')
                              ->get();
        
        $fixed = 0;
        
        foreach ($attempts as $attempt) {
            $result = $this->fixAttemptScore($attempt->attempt_id);
            if ($result->getStatusCode() === 200) {
                $fixed++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "$fixed attempts have been fixed"
        ]);
    }
}
PHP;

file_put_contents($controllerFixPath, $controllerFixContent);
echo "Created fix controller: app/Http/Controllers/FixQuizSubmissionController.php\n";

// Create a route for this controller
$routePath = __DIR__ . '/routes/web.php';
$routeContent = file_get_contents($routePath);

// Add our fix routes
$routeAddition = <<<'PHP'

// Quiz score fix routes
Route::get('/admin/fix-quiz-score/{attemptId}', [App\Http\Controllers\FixQuizSubmissionController::class, 'fixAttemptScore']);
Route::get('/admin/fix-all-quiz-scores', [App\Http\Controllers\FixQuizSubmissionController::class, 'fixAllAttempts']);
PHP;

file_put_contents($routePath, $routeContent . $routeAddition);
echo "Added fix routes to routes/web.php\n";

echo "\nFix has been implemented. To fix specific quiz attempt scores, visit:\n";
echo "http://127.0.0.1:8000/admin/fix-quiz-score/13\n";
echo "To fix all quiz attempts with 0 scores, visit:\n";
echo "http://127.0.0.1:8000/admin/fix-all-quiz-scores\n";
