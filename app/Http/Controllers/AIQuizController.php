<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use App\Models\AdminSetting;
use App\Models\Professor;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIQuizController extends Controller
{
    /**
     * Show the quiz generation form for professors
     */
    public function professorIndex()
    {
        // Check if AI quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        if (!$aiQuizEnabled) {
            abort(404); // Hide feature if disabled
        }

        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        $quizzes = QuizQuestion::where('created_by_professor', session('professor_id'))
            ->with('program')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('quiz_title')
            ->map(function ($questions) {
                $firstQuestion = $questions->first();
                return (object) [
                    'quiz_id' => $firstQuestion->quiz_id,
                    'quiz_title' => $firstQuestion->quiz_title,
                    'program' => $firstQuestion->program,
                    'questions' => $questions,
                    'difficulty' => $firstQuestion->difficulty ?? 'medium',
                    'created_at' => $firstQuestion->created_at
                ];
            })->values();

        return view('professor.quiz-generator', compact('quizzes', 'assignedPrograms'));
    }

    /**
     * Show the quiz generation form for admins
     */
    public function adminIndex()
    {
        $quizzes = QuizQuestion::select('quiz_title')
            ->distinct()
            ->get()
            ->pluck('quiz_title');

        return view('admin.ai-quiz.index', compact('quizzes'));
    }

    /**
     * Generate quiz questions from uploaded file
     */
    public function generate(Request $request)
    {
        // Check if AI quiz feature is enabled for professor access
        if (session('professor_role') === 'professor' && !AdminSetting::isEnabled('ai_quiz_generation_enabled')) {
            return response()->json(['error' => 'AI Quiz feature is not available'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,csv,txt|max:10240', // 10MB max
            'quiz_title' => 'required|string|max:255',
            'question_count' => 'required|integer|min:1|max:50',
            'question_types' => 'required|array',
            'question_types.*' => 'in:multiple_choice,true_false,short_answer,essay'
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('quiz_uploads', $fileName, 'public');

            // Extract text content from file
            $content = $this->extractContent($file);
            
            if (!$content) {
                return response()->json(['error' => 'Could not extract content from file'], 400);
            }

            // Generate questions using AI-like logic (simulated for now)
            $questions = $this->generateQuestions(
                $content,
                $request->quiz_title,
                $request->question_count,
                $request->question_types
            );

            // Save questions to database
            $savedQuestions = [];
            foreach ($questions as $questionData) {
                $question = QuizQuestion::create([
                    'quiz_title' => $request->quiz_title,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'],
                    'options' => $questionData['options'] ?? null,
                    'correct_answer' => $questionData['answer'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'points' => 1,
                    'source_file' => $fileName,
                    'created_by_admin' => session('user_role') === 'admin' ? session('user_id') : null,
                    'created_by_professor' => session('professor_role') === 'professor' ? session('professor_id') : null,
                ]);

                $savedQuestions[] = $question;
            }

            return response()->json([
                'success' => true,
                'message' => count($savedQuestions) . ' questions generated successfully!',
                'questions' => $savedQuestions
            ]);

        } catch (\Exception $e) {
            Log::error('AI Quiz Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate quiz questions'], 500);
        }
    }

    /**
     * Extract content from uploaded file
     */
    private function extractContent($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = '';

        try {
            switch ($extension) {
                case 'txt':
                    $content = file_get_contents($file->getRealPath());
                    break;
                    
                case 'csv':
                    $content = $this->extractFromCsv($file);
                    break;
                    
                case 'pdf':
                    $content = $this->extractFromPdf($file);
                    break;
                    
                case 'doc':
                case 'docx':
                    $content = $this->extractFromWord($file);
                    break;
                    
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('File extraction error: ' . $e->getMessage());
            return null;
        }

        return $content;
    }

    /**
     * Extract content from CSV file
     */
    private function extractFromCsv($file)
    {
        $content = '';
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $content .= implode(' ', $data) . "\n";
            }
            fclose($handle);
        }
        return $content;
    }

    /**
     * Extract content from PDF (basic implementation)
     */
    private function extractFromPdf($file)
    {
        // Note: This is a simplified implementation
        // In production, you'd use libraries like Smalot\PdfParser or similar
        return "PDF content extraction requires additional libraries. Please use TXT or CSV files for now.";
    }

    /**
     * Extract content from Word document
     */
    private function extractFromWord($file)
    {
        // Note: This is a simplified implementation
        // In production, you'd use libraries like PhpOffice\PhpWord or similar
        return "Word document extraction requires additional libraries. Please use TXT or CSV files for now.";
    }

    /**
     * Generate questions from content using AI-like logic
     */
    private function generateQuestions($content, $quizTitle, $questionCount, $questionTypes)
    {
        $questions = [];
        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $sentences = array_filter(array_map('trim', $sentences));

        for ($i = 0; $i < $questionCount && $i < count($sentences); $i++) {
            $sentence = $sentences[$i];
            if (strlen($sentence) < 20) continue; // Skip very short sentences

            $questionType = $questionTypes[array_rand($questionTypes)];
            $question = $this->generateQuestionFromSentence($sentence, $questionType);
            
            if ($question) {
                $questions[] = $question;
            }
        }

        return $questions;
    }

    /**
     * Generate a single question from a sentence
     */
    private function generateQuestionFromSentence($sentence, $type)
    {
        switch ($type) {
            case 'multiple_choice':
                return $this->generateMultipleChoice($sentence);
                
            case 'true_false':
                return $this->generateTrueFalse($sentence);
                
            case 'short_answer':
                return $this->generateShortAnswer($sentence);
                
            case 'essay':
                return $this->generateEssayQuestion($sentence);
                
            default:
                return null;
        }
    }

    /**
     * Generate multiple choice question
     */
    private function generateMultipleChoice($sentence)
    {
        $words = explode(' ', $sentence);
        if (count($words) < 5) return null;

        // Find a key word to make a question about
        $keyWord = $words[rand(2, count($words) - 2)];
        $question = str_replace($keyWord, '______', $sentence) . '?';

        $options = [
            $keyWord, // Correct answer
            $this->generateWrongOption($keyWord),
            $this->generateWrongOption($keyWord),
            $this->generateWrongOption($keyWord)
        ];
        shuffle($options);

        return [
            'type' => 'multiple_choice',
            'question' => "Fill in the blank: " . $question,
            'options' => $options,
            'answer' => $keyWord,
            'explanation' => "The correct answer is based on the original text."
        ];
    }

    /**
     * Generate true/false question
     */
    private function generateTrueFalse($sentence)
    {
        $isTrue = rand(0, 1);
        
        if ($isTrue) {
            $question = "True or False: " . $sentence;
            $answer = "True";
        } else {
            // Modify the sentence to make it false
            $words = explode(' ', $sentence);
            if (count($words) > 3) {
                $words[rand(1, count($words) - 2)] = "NOT";
                $modifiedSentence = implode(' ', $words);
                $question = "True or False: " . $modifiedSentence;
                $answer = "False";
            } else {
                $question = "True or False: " . $sentence;
                $answer = "True";
            }
        }

        return [
            'type' => 'true_false',
            'question' => $question,
            'options' => ['True', 'False'],
            'answer' => $answer,
            'explanation' => "Based on the provided content."
        ];
    }

    /**
     * Generate short answer question
     */
    private function generateShortAnswer($sentence)
    {
        $words = explode(' ', $sentence);
        if (count($words) < 4) return null;

        $keyWord = $words[rand(1, count($words) - 2)];
        $question = str_replace($keyWord, '______', $sentence) . '?';

        return [
            'type' => 'short_answer',
            'question' => "Complete the following: " . $question,
            'options' => null,
            'answer' => $keyWord,
            'explanation' => "The answer should be based on the provided content."
        ];
    }

    /**
     * Generate essay question
     */
    private function generateEssayQuestion($sentence)
    {
        $topics = [
            "Explain the significance of: ",
            "Discuss the importance of: ",
            "Analyze the following statement: ",
            "Provide your thoughts on: "
        ];

        $topic = $topics[array_rand($topics)];
        $question = $topic . $sentence;

        return [
            'type' => 'essay',
            'question' => $question,
            'options' => null,
            'answer' => "This is an essay question requiring detailed analysis.",
            'explanation' => "Essay answers should demonstrate understanding of the topic."
        ];
    }

    /**
     * Generate wrong options for multiple choice
     */
    private function generateWrongOption($correctAnswer)
    {
        $wrongOptions = [
            'incorrect_option_1',
            'wrong_answer',
            'false_choice',
            'not_correct'
        ];

        return $wrongOptions[array_rand($wrongOptions)] . '_' . rand(1, 100);
    }

    /**
     * Get quiz questions for a specific quiz
     */
    public function getQuizQuestions($quizTitle)
    {
        $questions = QuizQuestion::forQuiz($quizTitle)->active()->get();
        return response()->json($questions);
    }

    /**
     * Delete a quiz and all its questions (admin version)
     */
    public function deleteQuizAdmin(Request $request)
    {
        $request->validate([
            'quiz_title' => 'required|string'
        ]);

        $deleted = QuizQuestion::where('quiz_title', $request->quiz_title)->delete();

        return response()->json([
            'success' => true,
            'message' => "Quiz '{$request->quiz_title}' and {$deleted} questions deleted successfully."
        ]);
    }

    /**
     * Generate quiz questions from uploaded file (professor version)
     */
    public function generateQuiz(Request $request)
    {
        // Check if AI quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        if (!$aiQuizEnabled) {
            return back()->withErrors(['error' => 'AI Quiz feature is not available']);
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,csv,txt|max:10240', // 10MB max
            'program_id' => 'required|exists:programs,program_id',
            'quiz_title' => 'required|string|max:255',
            'num_questions' => 'required|integer|min:1|max:20',
            'difficulty' => 'required|in:easy,medium,hard',
            'quiz_type' => 'required|in:multiple_choice,true_false,mixed',
            'instructions' => 'nullable|string|max:1000'
        ]);

        try {
            $professor = Professor::find(session('professor_id'));
            
            // Verify professor has access to this program
            if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
                return back()->withErrors(['error' => 'You do not have access to this program.']);
            }

            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('quiz_uploads', $fileName, 'public');

            // Extract text content from file
            $content = $this->extractContent($file);
            
            if (!$content) {
                return back()->withErrors(['error' => 'Could not extract content from file']);
            }

            // Generate questions using AI-like logic (simulated for now)
            $questions = $this->generateQuestions(
                $content,
                $request->quiz_title,
                $request->num_questions,
                [$request->quiz_type],
                $request->difficulty
            );

            // Save questions to database
            foreach ($questions as $questionData) {
                QuizQuestion::create([
                    'quiz_title' => $request->quiz_title,
                    'program_id' => $request->program_id,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'],
                    'options' => $questionData['options'] ?? null,
                    'correct_answer' => $questionData['answer'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'difficulty' => $request->difficulty,
                    'points' => 1,
                    'source_file' => $fileName,
                    'instructions' => $request->instructions,
                    'created_by_professor' => $professor->professor_id,
                ]);
            }

            return back()->with('success', "Quiz '{$request->quiz_title}' generated successfully with {$request->num_questions} questions!");

        } catch (\Exception $e) {
            Log::error('Quiz generation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to generate quiz. Please try again.']);
        }
    }

    /**
     * Preview a quiz
     */
    public function previewQuiz($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = QuizQuestion::where('quiz_id', $quizId)
            ->where('created_by_professor', $professor->professor_id)
            ->first();
            
        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        $questions = QuizQuestion::where('quiz_title', $quiz->quiz_title)
            ->where('created_by_professor', $professor->professor_id)
            ->get();

        $html = view('professor.quiz-preview', compact('quiz', 'questions'))->render();
        
        return response()->json(['html' => $html]);
    }

    /**
     * Export quiz as PDF or text
     */
    public function exportQuiz($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = QuizQuestion::where('quiz_id', $quizId)
            ->where('created_by_professor', $professor->professor_id)
            ->first();
            
        if (!$quiz) {
            abort(404);
        }

        $questions = QuizQuestion::where('quiz_title', $quiz->quiz_title)
            ->where('created_by_professor', $professor->professor_id)
            ->get();

        $content = "Quiz: {$quiz->quiz_title}\n";
        $content .= "Difficulty: " . ucfirst($quiz->difficulty ?? 'medium') . "\n";
        $content .= "Total Questions: " . $questions->count() . "\n\n";

        if ($quiz->instructions) {
            $content .= "Instructions: {$quiz->instructions}\n\n";
        }

        foreach ($questions as $index => $question) {
            $content .= ($index + 1) . ". {$question->question_text}\n";
            
            if ($question->question_type === 'multiple_choice' && $question->options) {
                $options = json_decode($question->options, true);
                foreach ($options as $optionIndex => $option) {
                    $letter = chr(65 + $optionIndex); // A, B, C, D
                    $content .= "   {$letter}. {$option}\n";
                }
            }
            
            $content .= "   Answer: {$question->correct_answer}\n";
            
            if ($question->explanation) {
                $content .= "   Explanation: {$question->explanation}\n";
            }
            
            $content .= "\n";
        }

        $fileName = str_replace(' ', '_', $quiz->quiz_title) . '_quiz.txt';
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Delete a quiz
     */
    public function deleteQuiz($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = QuizQuestion::where('quiz_id', $quizId)
            ->where('created_by_professor', $professor->professor_id)
            ->first();
            
        if (!$quiz) {
            return back()->withErrors(['error' => 'Quiz not found']);
        }

        // Delete all questions with the same quiz title
        QuizQuestion::where('quiz_title', $quiz->quiz_title)
            ->where('created_by_professor', $professor->professor_id)
            ->delete();

        return back()->with('success', 'Quiz deleted successfully!');
    }
}
