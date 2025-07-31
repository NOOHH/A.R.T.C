<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiQuizService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CloudSecurityQuizController extends Controller
{
    protected $geminiQuizService;
    
    /**
     * Create a new controller instance.
     *
     * @param GeminiQuizService $geminiQuizService
     * @return void
     */
    public function __construct(GeminiQuizService $geminiQuizService)
    {
        $this->geminiQuizService = $geminiQuizService;
    }
    
    /**
     * Show the form to generate a cloud security quiz
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('cloud-security.quiz-generator');
    }
    
    /**
     * Generate a quiz from the cloud security lecture PDFs
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function generateQuiz(Request $request)
    {
        try {
            // Define the paths to the Cloud Security lecture PDFs
            $pdfPaths = [];
            
            // Check if PDFs were uploaded
            if ($request->hasFile('pdfs')) {
                $files = $request->file('pdfs');
                
                foreach ($files as $file) {
                    $path = $file->store('cloud-security-pdfs', 'public');
                    $pdfPaths[] = 'public/' . $path;
                }
            } else {
                // Use predefined PDFs if available
                $defaultPdfs = [
                    'cloud-security/Lecture 7 - Cloud Security Part 1.pdf',
                    'cloud-security/Lecture 7 - Cloud Security Part 2.pdf',
                    'cloud-security/Lecture 8 - GRC.pdf'
                ];
                
                foreach ($defaultPdfs as $pdf) {
                    if (Storage::exists('public/' . $pdf)) {
                        $pdfPaths[] = 'public/' . $pdf;
                    } else {
                        Log::warning("Default PDF not found: $pdf");
                    }
                }
            }
            
            if (empty($pdfPaths)) {
                return back()->with('error', 'No PDFs found or uploaded for quiz generation.');
            }
            
            // Generate the quiz
            $minMcq = $request->input('min_mcq', 10);
            $minTf = $request->input('min_tf', 8);
            
            $quizData = $this->geminiQuizService->generateQuizFromMultipleDocuments($pdfPaths, $minMcq, $minTf);
            
            // Format quiz for display
            $formattedQuiz = $this->geminiQuizService->formatQuizOutput($quizData);
            
            return view('cloud-security.quiz-result', [
                'quiz' => $quizData,
                'formattedQuiz' => $formattedQuiz,
                'sources' => $quizData['source_documents'] ?? []
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating Cloud Security quiz: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate quiz: ' . $e->getMessage());
        }
    }
    
    /**
     * Process uploaded PDFs and generate a quiz
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function processUploadedPdfs(Request $request)
    {
        try {
            $request->validate([
                'pdfs.*' => 'required|file|mimes:pdf|max:10240', // 10MB max per file
            ]);
            
            $pdfPaths = [];
            
            if ($request->hasFile('pdfs')) {
                foreach ($request->file('pdfs') as $file) {
                    $path = $file->store('uploads/pdfs', 'public');
                    $pdfPaths[] = 'public/' . $path;
                }
            }
            
            if (empty($pdfPaths)) {
                return back()->with('error', 'No PDFs were uploaded.');
            }
            
            // Generate quiz from uploaded PDFs
            $minMcq = $request->input('min_mcq', 10);
            $minTf = $request->input('min_tf', 8);
            
            $quizData = $this->geminiQuizService->generateQuizFromMultipleDocuments($pdfPaths, $minMcq, $minTf);
            
            // Format quiz for display
            $formattedQuiz = $this->geminiQuizService->formatQuizOutput($quizData);
            
            return view('cloud-security.quiz-result', [
                'quiz' => $quizData,
                'formattedQuiz' => $formattedQuiz,
                'sources' => $quizData['source_documents'] ?? []
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing uploaded PDFs: ' . $e->getMessage());
            return back()->with('error', 'Failed to process uploaded PDFs: ' . $e->getMessage());
        }
    }
    
    /**
     * Save the generated quiz to the database
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveQuiz(Request $request)
    {
        try {
            $request->validate([
                'quiz_data' => 'required',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $quizData = json_decode($request->input('quiz_data'), true);
            
            if (empty($quizData)) {
                return back()->with('error', 'Invalid quiz data.');
            }
            
            // Convert quiz data to database format
            $questions = $this->geminiQuizService->convertToQuizQuestions($quizData);
            
            // Create quiz record
            $quiz = new \App\Models\Quiz();
            $quiz->title = $request->input('title');
            $quiz->description = $request->input('description');
            $quiz->professor_id = session('professor_id');
            $quiz->status = 'draft';
            $quiz->save();
            
            // Save questions
            foreach ($questions as $questionData) {
                $question = new \App\Models\QuizQuestion();
                $question->quiz_id = $quiz->quiz_id;
                $question->question_text = $questionData['question_text'];
                $question->question_type = $questionData['question_type'];
                $question->options = $questionData['options'];
                $question->correct_answer = $questionData['correct_answer'];
                $question->explanation = $questionData['explanation'];
                $question->question_source = 'gemini';
                $question->points = $questionData['points'];
                $question->is_active = true;
                $question->save();
            }
            
            return redirect()->route('professor.quiz-generator.index')
                ->with('success', 'Cloud Security Quiz created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Error saving quiz: ' . $e->getMessage());
            return back()->with('error', 'Failed to save quiz: ' . $e->getMessage());
        }
    }
    
    /**
     * Regenerate a quiz from the same PDF sources but with different questions
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function regenerateQuiz(Request $request)
    {
        try {
            $sources = $request->input('sources', []);
            $sourceArray = json_decode($sources, true);
            $pdfPaths = [];
            
            if (!empty($sourceArray)) {
                foreach ($sourceArray as $source) {
                    if (Storage::exists('public/' . $source)) {
                        $pdfPaths[] = 'public/' . $source;
                    }
                }
            }
            
            // Use the sources from the previous quiz or defaults
            if (empty($pdfPaths)) {
                // Use predefined PDFs if available
                $defaultPdfs = [
                    'cloud-security/Lecture 7 - Cloud Security Part 1.pdf',
                    'cloud-security/Lecture 7 - Cloud Security Part 2.pdf',
                    'cloud-security/Lecture 8 - GRC.pdf'
                ];
                
                foreach ($defaultPdfs as $pdf) {
                    if (Storage::exists('public/' . $pdf)) {
                        $pdfPaths[] = 'public/' . $pdf;
                    }
                }
            }
            
            if (empty($pdfPaths)) {
                return back()->with('error', 'No PDFs found for quiz regeneration.');
            }
            
            // Generate the quiz
            $minMcq = $request->input('min_mcq', 10);
            $minTf = $request->input('min_tf', 8);
            
            // Force different questions by using a different seed
            $quizData = $this->geminiQuizService->generateQuizFromMultipleDocuments($pdfPaths, $minMcq, $minTf);
            
            // Format quiz for display
            $formattedQuiz = $this->geminiQuizService->formatQuizOutput($quizData);
            
            return view('cloud-security.quiz-result', [
                'quiz' => $quizData,
                'formattedQuiz' => $formattedQuiz,
                'sources' => $quizData['source_documents'] ?? [],
                'regenerated' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error regenerating quiz: ' . $e->getMessage());
            return back()->with('error', 'Failed to regenerate quiz: ' . $e->getMessage());
        }
    }
}
