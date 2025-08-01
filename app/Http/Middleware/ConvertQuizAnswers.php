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