<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Student;
use App\Models\Deadline;
use App\Models\Announcement;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QuizGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('professor.auth');
    }

    public function index()
    {
        // Check if AI Quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        
        if (!$aiQuizEnabled) {
            return redirect()->route('professor.dashboard')->with('error', 'AI Quiz Generator is currently disabled.');
        }

        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        // Get professor's generated quizzes
        $quizzes = Quiz::where('professor_id', $professor->professor_id)
                      ->with(['program', 'questions'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return view('professor.quiz-generator', compact('assignedPrograms', 'quizzes'));
    }

    public function generate(Request $request)
    {
        // Check if AI Quiz feature is enabled
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        
        if (!$aiQuizEnabled) {
            return redirect()->route('professor.quiz-generator')->with('error', 'AI Quiz Generator is currently disabled.');
        }

        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'document' => 'required|file|mimes:pdf,doc,docx,csv,txt|max:10240', // 10MB max
            'num_questions' => 'required|integer|min:5|max:50',
            'difficulty' => 'required|in:easy,medium,hard',
            'quiz_type' => 'required|in:multiple_choice,true_false,mixed',
            'quiz_title' => 'required|string|max:255',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $professor = Professor::find(session('professor_id'));
        
        // Check if professor is assigned to this program
        if (!$professor->programs()->where('program_id', $request->program_id)->exists()) {
            return redirect()->back()->with('error', 'You are not assigned to this program.');
        }

        try {
            // Store the uploaded document
            $documentPath = $request->file('document')->store('quiz-documents', 'public');
            
            // Process the document and generate quiz
            $extractedText = $this->extractTextFromDocument($request->file('document'));
            $generatedQuestions = $this->generateQuestionsFromText(
                $extractedText,
                $request->num_questions,
                $request->difficulty,
                $request->quiz_type
            );

            // Create the quiz
            $quiz = Quiz::create([
                'professor_id' => $professor->professor_id,
                'program_id' => $request->program_id,
                'quiz_title' => $request->quiz_title,
                'instructions' => $request->instructions,
                'difficulty' => $request->difficulty,
                'total_questions' => count($generatedQuestions),
                'time_limit' => 60, // Default 60 minutes
                'document_path' => $documentPath,
                'is_active' => true,
                'created_at' => now(),
            ]);

            // Save questions
            foreach ($generatedQuestions as $questionData) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'],
                    'options' => json_encode($questionData['options']),
                    'correct_answer' => $questionData['correct_answer'],
                    'points' => $questionData['points'] ?? 1,
                ]);
            }

            // Sync with students - add to deadlines
            $this->syncQuizWithStudents($quiz);

            return redirect()->route('professor.quiz-generator')->with('success', 'Quiz generated successfully! It has been added to student deadlines.');

        } catch (\Exception $e) {
            Log::error('Quiz generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate quiz. Please try again.');
        }
    }

    private function extractTextFromDocument($file)
    {
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getPathname();
        
        switch (strtolower($extension)) {
            case 'txt':
                return file_get_contents($tempPath);
                
            case 'csv':
                $content = '';
                if (($handle = fopen($tempPath, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $content .= implode(' ', $data) . "\n";
                    }
                    fclose($handle);
                }
                return $content;
                
            case 'pdf':
                // For PDF, you might want to use a library like spatie/pdf-to-text
                // For now, return a placeholder
                return "PDF content extraction requires additional libraries. Please use TXT or CSV files for now.";
                
            case 'doc':
            case 'docx':
                // For Word documents, you might want to use phpoffice/phpword
                // For now, return a placeholder
                return "Word document extraction requires additional libraries. Please use TXT or CSV files for now.";
                
            default:
                throw new \Exception('Unsupported file format');
        }
    }

    private function generateQuestionsFromText($text, $numQuestions, $difficulty, $quizType)
    {
        // This is a simplified AI-like question generation
        // In a real implementation, you would integrate with OpenAI, Google AI, or similar services
        
        $sentences = array_filter(explode('.', $text));
        $questions = [];
        
        $difficultyMultiplier = [
            'easy' => 1,
            'medium' => 2,
            'hard' => 3
        ];
        
        for ($i = 0; $i < $numQuestions && $i < count($sentences); $i++) {
            $sentence = trim($sentences[$i]);
            if (strlen($sentence) < 10) continue;
            
            $questionType = $quizType === 'mixed' 
                ? (rand(0, 1) ? 'multiple_choice' : 'true_false')
                : $quizType;
            
            if ($questionType === 'multiple_choice') {
                $questions[] = $this->generateMultipleChoiceQuestion($sentence, $difficulty);
            } else {
                $questions[] = $this->generateTrueFalseQuestion($sentence, $difficulty);
            }
        }
        
        return $questions;
    }

    private function generateMultipleChoiceQuestion($text, $difficulty)
    {
        // Extract key words from the text
        $words = explode(' ', $text);
        $keyWord = $words[array_rand($words)];
        
        return [
            'question' => "Based on the content, what is mentioned about " . $keyWord . "?",
            'type' => 'multiple_choice',
            'options' => [
                'A' => $text,
                'B' => "Alternative interpretation of " . $keyWord,
                'C' => "Different context for " . $keyWord,
                'D' => "Unrelated information about " . $keyWord
            ],
            'correct_answer' => 'A',
            'points' => $difficulty === 'hard' ? 3 : ($difficulty === 'medium' ? 2 : 1)
        ];
    }

    private function generateTrueFalseQuestion($text, $difficulty)
    {
        $isTrue = rand(0, 1);
        
        return [
            'question' => $isTrue ? $text : str_replace(array_rand(explode(' ', $text)), "incorrect_information", $text),
            'type' => 'true_false',
            'options' => [
                'A' => 'True',
                'B' => 'False'
            ],
            'correct_answer' => $isTrue ? 'A' : 'B',
            'points' => $difficulty === 'hard' ? 3 : ($difficulty === 'medium' ? 2 : 1)
        ];
    }

    private function syncQuizWithStudents($quiz)
    {
        // Get all students enrolled in this program
        $students = Student::whereHas('enrollments', function ($query) use ($quiz) {
            $query->where('program_id', $quiz->program_id);
        })->get();

        // Add quiz deadline for each student
        foreach ($students as $student) {
            Deadline::create([
                'student_id' => $student->student_id,
                'program_id' => $quiz->program_id,
                'title' => 'Quiz: ' . $quiz->quiz_title,
                'description' => $quiz->instructions ?? 'Complete the assigned quiz',
                'type' => 'quiz',
                'reference_id' => $quiz->quiz_id,
                'due_date' => Carbon::now()->addDays(7), // 7 days to complete
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }
    }

    public function preview($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = Quiz::where('quiz_id', $quizId)
                   ->where('professor_id', $professor->professor_id)
                   ->with(['questions', 'program'])
                   ->firstOrFail();

        $html = view('professor.quiz-preview', compact('quiz'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function export($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = Quiz::where('quiz_id', $quizId)
                   ->where('professor_id', $professor->professor_id)
                   ->with(['questions', 'program'])
                   ->firstOrFail();

        // Generate PDF or downloadable format
        $content = view('professor.quiz-export', compact('quiz'))->render();
        
        return response($content)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="quiz-' . $quiz->quiz_title . '.html"');
    }

    public function delete($quizId)
    {
        $professor = Professor::find(session('professor_id'));
        $quiz = Quiz::where('quiz_id', $quizId)
                   ->where('professor_id', $professor->professor_id)
                   ->firstOrFail();

        // Delete associated deadlines
        Deadline::where('type', 'quiz')
                ->where('reference_id', $quiz->quiz_id)
                ->delete();

        // Delete quiz questions
        $quiz->questions()->delete();
        
        // Delete quiz
        $quiz->delete();

        return redirect()->route('professor.quiz-generator')->with('success', 'Quiz deleted successfully.');
    }
}
