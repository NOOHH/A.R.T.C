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