<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\AdminSetting;
use App\Models\Module;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Models\Course;
use App\Models\ContentItem;
use App\Services\GeminiQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuizGeneratorController extends Controller
{
    protected $geminiQuizService;

    public function __construct(GeminiQuizService $geminiQuizService)
    {
        $this->middleware('professor.auth');
        $this->geminiQuizService = $geminiQuizService;
    }

    public function index()
    {
        $aiQuizEnabled = AdminSetting::where('setting_key', 'ai_quiz_enabled')->value('setting_value') == 'true';
        if (!$aiQuizEnabled) {
            return redirect()->route('professor.dashboard')->with('error', 'AI Quiz Generator is currently disabled.');
        }

        $professor = Professor::find(session('professor_id'));
        $assignedPrograms = $professor->programs()->get();
        
        $allQuizzes = Quiz::where('professor_id', $professor->professor_id)
                          ->with(['program', 'questions'])
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        $draftQuizzes = $allQuizzes->where('status', 'draft');
        $publishedQuizzes = $allQuizzes->where('status', 'published');
        $archivedQuizzes = $allQuizzes->where('status', 'archived');
        
        return view('Quiz Generator.professor.quiz-generator', compact('assignedPrograms', 'draftQuizzes', 'publishedQuizzes', 'archivedQuizzes'));
    }

    public function getModulesByProgram($programId)
    {
        $modules = Module::where('program_id', $programId)
            ->where('is_archived', false)
            ->orderBy('module_name')
            ->get(['modules_id as module_id', 'module_name']);

        return response()->json(['success' => true, 'modules' => $modules]);
    }

    public function getCoursesByModule($moduleId)
    {
        $courses = Course::where('module_id', $moduleId)
            ->where('is_archived', false)
            ->orderBy('subject_name')
            ->get(['subject_id as course_id', 'subject_name as course_name']);
        return response()->json(['success' => true, 'courses' => $courses]);
    }

    public function getContentsByCourse($courseId)
    {
        $contents = ContentItem::where('course_id', $courseId)
                       ->active()
                       ->ordered()
                       ->get(['id as content_id','content_title']);
        return response()->json(['success' => true, 'contents' => $contents]);
    }

    public function generateAIQuestions(Request $request)
    {
        Log::info('=== AI Quiz Generation Request ===', [
            'professor_id' => session('professor_id'),
            'request_data' => $request->except(['file', '_token']),
            'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'No file'
        ]);

        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,csv,txt,jpg,jpeg,png|max:51200', // 50MB limit
                'num_questions' => 'required|integer|min:1|max:50',
                'question_type' => 'required|in:multiple_choice,true_false,mixed',
            ]);

            if (empty(env('GEMINI_API_KEY')) || env('GEMINI_API_KEY') === 'your_gemini_api_key_here') {
                Log::error('Gemini API key is not configured.');
                return response()->json(['success' => false, 'message' => 'AI Service is not configured. Please contact administrator.'], 500);
            }

            $file = $request->file('file');
            $numQuestions = (int) $request->input('num_questions');
            $quizType = $request->input('question_type');

            if ($quizType === 'mixed') {
                $minMcq = (int) ceil($numQuestions / 2);
                $minTf = (int) floor($numQuestions / 2);
            } else {
                $minMcq = ($quizType === 'multiple_choice') ? $numQuestions : 0;
                $minTf = ($quizType === 'true_false') ? $numQuestions : 0;
            }
            
            $documentPath = $file->store('quiz-documents', 'public');
            $documentResult = $this->geminiQuizService->extractDocumentContent('public/' . $documentPath);

            if (empty($documentResult['content'])) {
                Log::warning('Failed to extract document content', ['error' => $documentResult['error'] ?? 'Unknown error']);
                return response()->json(['success' => false, 'message' => 'Failed to extract content from document.'], 400);
            }

            $quizData = $this->geminiQuizService->generateQuiz($documentResult['content'], $minMcq, $minTf);

            // Convert old format to new format for compatibility
            $generatedQuestions = [];
            
            // If Gemini returns an error or no questions, try the simple approach
            if (!empty($quizData['error']) || empty($quizData['mcqs']) && empty($quizData['true_false'])) {
                Log::warning('Standard Gemini service failed, trying simple approach', [
                    'error' => $quizData['error'] ?? 'No questions generated'
                ]);
                
                // Try simple service as fallback
                $simpleService = new \App\Services\SimpleGeminiQuizService();
                $simpleResult = $simpleService->generateQuiz($documentResult['content'], $numQuestions, 0);
                
                if ($simpleResult['success'] && !empty($simpleResult['questions'])) {
                    $generatedQuestions = $simpleResult['questions'];
                    Log::info('✓ Simple Gemini service succeeded as fallback', ['count' => count($generatedQuestions)]);
                } else {
                    $userMessage = 'AI quiz generation failed. The AI could not generate questions from the provided document. Try a different document, a smaller file, or fewer questions.';
                    return response()->json(['success' => false, 'message' => $userMessage], 500);
                }
            } else {
                // Convert standard format to frontend format
                if (!empty($quizData['mcqs'])) {
                    foreach ($quizData['mcqs'] as $mcq) {
                        $generatedQuestions[] = [
                            'question' => $mcq['text'],
                            'type' => 'multiple_choice',
                            'options' => $mcq['options'],
                            'correct_answer' => $mcq['correct_answer'] ?? 'A',
                            'explanation' => $mcq['explanation'] ?? 'Based on the document content.'
                        ];
                    }
                }
                
                if (!empty($quizData['true_false'])) {
                    foreach ($quizData['true_false'] as $tf) {
                        $generatedQuestions[] = [
                            'question' => $tf['statement'],
                            'type' => 'true_false',
                            'options' => ['True', 'False'],
                            'correct_answer' => $tf['correct_answer'] ?? 'True',
                            'explanation' => $tf['explanation'] ?? 'Based on the document content.'
                        ];
                    }
                }
            }

            // If the AI returns no questions, log and suggest next steps
            if (empty($generatedQuestions)) {
                Log::warning('AI service returned no questions after all attempts');
                return response()->json([
                    'success' => false,
                    'message' => 'The AI could not generate questions from the provided document. Try a different document, a smaller file, or fewer questions.'
                ], 400);
            }

            Log::info('✓ AI questions generated successfully via service', ['count' => count($generatedQuestions)]);

            return response()->json([
                'success' => true,
                'questions' => $generatedQuestions
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed during AI question generation', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Invalid input.', 'errors' => $e->errors()], 422);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('API connection error during AI question generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Connection to AI service timed out. Please try with a smaller document or fewer questions.'
            ], 504); // Gateway Timeout
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('API request error during AI question generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error communicating with AI service: ' . $e->getMessage()
            ], 502); // Bad Gateway
        } catch (\Exception $e) {
            // Check for timeout error specifically
            if (strpos($e->getMessage(), 'time') !== false && strpos($e->getMessage(), 'exceeded') !== false) {
                Log::error('Timeout error in AI question generation', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'AI quiz generation timed out. Please try with a smaller document or fewer questions.'
                ], 504);
            }
            
            Log::error('An unexpected error occurred in generateAIQuestions', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'error' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'AI quiz generation failed: ' . $e->getMessage(),
                'error' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function saveQuizWithQuestions(Request $request)
    {
        Log::info('=== Saving AI-Generated Quiz ===', [
            'professor_id' => session('professor_id'),
            'request_data' => $request->except(['questions', '_token'])
        ]);

        try {
            $validatedData = $request->validate([
                'quiz_title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'required|exists:modules,modules_id',
                'course_id' => 'required|exists:courses,subject_id',
                'questions' => 'required|array|min:1',
                'questions.*.question' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answer' => 'required|string',
                'questions.*.explanation' => 'nullable|string',
            ]);

            $professor = Professor::find(session('professor_id'));
            if (!$professor) {
                Log::error('Professor not found for saving quiz', ['session_id' => session('professor_id')]);
                return response()->json(['success' => false, 'message' => 'Professor session not found.'], 401);
            }

            DB::beginTransaction();

            $quiz = Quiz::create([
                'professor_id' => $professor->professor_id,
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'quiz_title' => $validatedData['quiz_title'],
                'quiz_description' => 'AI-generated quiz.',
                'instructions' => 'Please answer the following questions.',
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false,
                'total_questions' => count($validatedData['questions']),
                'created_at' => now(),
            ]);

            $contentItem = ContentItem::create([
                'content_title' => $validatedData['quiz_title'],
                'content_description' => 'AI Generated Quiz',
                'course_id' => $validatedData['course_id'],
                'content_type' => 'quiz',
                'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                'is_active' => true,
            ]);

            $quiz->update(['content_id' => $contentItem->id]);

            foreach ($validatedData['questions'] as $questionData) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['question_type'],
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'generated',
                    'points' => 1,
                    'is_active' => true,
                    'created_by_professor' => $professor->professor_id,
                ]);
            }

            DB::commit();

            Log::info('✓ AI-generated quiz saved successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz saved successfully as a draft.',
                'quiz_id' => $quiz->quiz_id,
                'redirect_url' => route('professor.quiz-generator')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during quiz save', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An unexpected error occurred while saving the quiz', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected server error occurred while saving the quiz.'], 500);
        }
    }

    /**
     * Save manually created quiz
     */
    public function saveManualQuiz(Request $request)
    {
        Log::info('=== Saving Manual Quiz ===', [
            'professor_id' => session('professor_id'),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'raw_content' => substr($request->getContent(), 0, 2000),
            'request_all' => $request->all(),
            'json_all' => $request->isJson() ? $request->json()->all() : null,
        ]);

        try {
            // Handle both form data and JSON requests
            $inputData = $request->isJson() ? $request->json()->all() : $request->all();
            
            Log::info('Input data extracted:', [
                'data_type' => $request->isJson() ? 'JSON' : 'Form',
                'input_keys' => array_keys($inputData),
                'title_value' => $inputData['title'] ?? 'NOT SET',
                'questions_count' => isset($inputData['questions']) ? count($inputData['questions']) : 0,
                'questions_sample' => isset($inputData['questions'][0]) ? $inputData['questions'][0] : 'NO QUESTIONS'
            ]);

            // Create new request with the input data for validation
            $request->merge($inputData);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'is_draft' => 'nullable|boolean',
                'time_limit' => 'nullable|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'infinite_retakes' => 'nullable|boolean',
                'has_deadline' => 'nullable|boolean',
                'due_date' => 'nullable|date',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answers' => 'nullable|array',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric|min:0',
                'questions.*.order' => 'nullable|integer|min:1',
            ]);

            $professor = Professor::find(session('professor_id'));
            if (!$professor) {
                Log::error('Professor not found for saving manual quiz', ['session_id' => session('professor_id')]);
                return response()->json(['success' => false, 'message' => 'Professor session not found.'], 401);
            }

            DB::beginTransaction();

            // Determine status based on is_draft flag
            $isDraft = $validatedData['is_draft'] ?? true;
            $status = $isDraft ? 'draft' : 'published';

            $quiz = Quiz::create([
                'professor_id' => $professor->professor_id,
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $validatedData['description'] ?? 'Manually created quiz.',
                'instructions' => $validatedData['instructions'] ?? 'Please answer the following questions.',
                'status' => $status,
                'is_draft' => $isDraft,
                'is_active' => !$isDraft,
                'time_limit' => $validatedData['time_limit'] ?? 60,
                'max_attempts' => $validatedData['infinite_retakes'] ? 999 : ($validatedData['max_attempts'] ?? 1),
                'infinite_retakes' => $validatedData['infinite_retakes'] ?? false,
                'has_deadline' => $validatedData['has_deadline'] ?? false,
                'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                'total_questions' => count($validatedData['questions']),
                'created_at' => now(),
            ]);

            // Create content item if course is specified
            if (!empty($validatedData['course_id'])) {
                $contentItem = ContentItem::create([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Manually Created Quiz',
                    'course_id' => $validatedData['course_id'],
                    'content_type' => 'quiz',
                    'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);

                $quiz->update(['content_id' => $contentItem->id]);
            }

            foreach ($validatedData['questions'] as $index => $questionData) {
                // Handle correct answers properly - convert letters to indices
                $correctAnswer = '';
                if (!empty($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    $correctAnswer = $questionData['correct_answers'][0] ?? '';
                } else {
                    $correctAnswer = $questionData['correct_answers'] ?? '';
                }
                
                // Convert letter answers (A, B, C, D) to index format (0, 1, 2, 3)
                if (is_string($correctAnswer) && preg_match('/^[A-Z]$/', $correctAnswer)) {
                    $correctAnswer = (string)(ord($correctAnswer) - 65);
                }

                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $questionData['order'] ?? ($index + 1),
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $correctAnswer,
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => $professor->professor_id,
                ]);
            }

            DB::commit();

            Log::info('✓ Manual quiz saved successfully', [
                'quiz_id' => $quiz->quiz_id, 
                'status' => $status,
                'questions_saved' => count($validatedData['questions'])
            ]);

            $message = $isDraft 
                ? 'Quiz saved successfully as a draft.' 
                : 'Quiz published successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'quiz_id' => $quiz->quiz_id,
                'status' => $status,
                'redirect_url' => route('professor.quiz-generator')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during manual quiz save', [
                'errors' => $e->errors(),
                'input_data' => $request->all()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Invalid data provided.', 
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An unexpected error occurred while saving manual quiz', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_data' => $request->except(['questions'])
            ]);
            return response()->json(['success' => false, 'message' => 'An unexpected server error occurred while saving the quiz.'], 500);
        }
    }

    /**
     * General save method (can handle both manual and AI-generated quizzes)
     */
    public function save(Request $request)
    {
        Log::info('=== General Quiz Save ===', [
            'professor_id' => session('professor_id'),
            'quiz_type' => $request->input('quiz_type', 'unknown')
        ]);

        // Determine if this is a manual or AI-generated quiz
        $quizType = $request->input('quiz_type', 'manual');
        
        if ($quizType === 'ai_generated') {
            return $this->saveQuizWithQuestions($request);
        } else {
            return $this->saveManualQuiz($request);
        }
    }

    /**
     * Preview a quiz
     */
    public function preview(Quiz $quiz)
    {
        $professor = Professor::find(session('professor_id'));
        
        if (!$professor || $quiz->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.quiz-generator')
                ->with('error', 'Quiz not found or access denied.');
        }

        $questions = $quiz->questions()->orderBy('question_order')->get();
        
        return view('Quiz Generator.professor.quiz-preview', compact('quiz', 'questions'));
    }

    /**
     * Preview a quiz (alternative method name)
     */
    public function previewQuiz(Quiz $quiz)
    {
        return $this->preview($quiz);
    }

    /**
     * Publish a quiz
     */
    public function publish(Quiz $quiz)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            $quiz->update([
                'status' => 'published',
                'is_draft' => false,
                'is_active' => true
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update(['is_active' => true]);
            }

            Log::info('Quiz published successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz published successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error publishing quiz', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error publishing quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish a quiz (alternative method name)
     */
    public function publishQuiz(Quiz $quiz)
    {
        return $this->publish($quiz);
    }

    /**
     * Archive a quiz
     */
    public function archive(Quiz $quiz)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            $quiz->update([
                'status' => 'archived',
                'is_active' => false
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update(['is_active' => false]);
            }

            Log::info('Quiz archived successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz archived successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error archiving quiz', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error archiving quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive a quiz (alternative method name)
     */
    public function archiveQuiz(Quiz $quiz)
    {
        return $this->archive($quiz);
    }

    /**
     * Delete a quiz
     */
    public function delete(Quiz $quiz)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            // Delete associated content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->delete();
            }

            // Delete quiz questions
            $quiz->questions()->delete();

            // Delete quiz
            $quiz->delete();

            Log::info('Quiz deleted successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting quiz', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a quiz (alternative method name)
     */
    public function deleteQuiz(Quiz $quiz)
    {
        return $this->delete($quiz);
    }

    /**
     * Delete a specific question from a quiz
     */
    public function deleteQuestion(Quiz $quiz, QuizQuestion $question)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            if ($question->quiz_id !== $quiz->quiz_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question does not belong to this quiz.'
                ], 403);
            }

            $question->delete();

            // Update total questions count
            $remainingQuestions = $quiz->questions()->count();
            $quiz->update(['total_questions' => $remainingQuestions]);

            Log::info('Quiz question deleted successfully', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully.',
                'remaining_questions' => $remainingQuestions
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting quiz question', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View questions for a quiz
     */
    public function viewQuestions(Quiz $quiz)
    {
        $professor = Professor::find(session('professor_id'));
        
        if (!$professor || $quiz->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.quiz-generator')
                ->with('error', 'Quiz not found or access denied.');
        }

        $questions = $quiz->questions()->orderBy('question_order')->get();
        
        return view('Quiz Generator.professor.quiz-questions', compact('quiz', 'questions'));
    }

    /**
     * Edit questions for a quiz
     */
    public function editQuestions(Quiz $quiz)
    {
        $professor = Professor::find(session('professor_id'));
        
        if (!$professor || $quiz->professor_id !== $professor->professor_id) {
            return redirect()->route('professor.quiz-generator')
                ->with('error', 'Quiz not found or access denied.');
        }

        $questions = $quiz->questions()->orderBy('question_order')->get();
        $programs = $professor->programs()->get();
        
        return view('Quiz Generator.professor.quiz-questions-edit', compact('quiz', 'questions', 'programs'));
    }

    /**
     * Update a specific question
     */
    public function updateQuestion(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            if ($question->quiz_id !== $quiz->quiz_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question does not belong to this quiz.'
                ], 403);
            }

            $validatedData = $request->validate([
                'question_text' => 'required|string',
                'question_type' => 'required|string|in:multiple_choice,true_false',
                'options' => 'nullable|array',
                'correct_answer' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'nullable|numeric|min:0',
            ]);

            $question->update([
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
                'options' => $validatedData['options'] ?? [],
                'correct_answer' => $validatedData['correct_answer'],
                'explanation' => $validatedData['explanation'] ?? '',
                'points' => $validatedData['points'] ?? 1,
            ]);

            Log::info('Quiz question updated successfully', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating quiz question', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a new question to a quiz
     */
    public function addQuestion(Request $request, Quiz $quiz)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            $validatedData = $request->validate([
                'question_text' => 'required|string',
                'question_type' => 'required|string|in:multiple_choice,true_false',
                'options' => 'nullable|array',
                'correct_answer' => 'required|string',
                'explanation' => 'nullable|string',
                'points' => 'nullable|numeric|min:0',
            ]);

            // Get next question order
            $nextOrder = $quiz->questions()->max('question_order') + 1;

            $question = QuizQuestion::create([
                'quiz_id' => $quiz->quiz_id,
                'quiz_title' => $quiz->quiz_title,
                'program_id' => $quiz->program_id,
                'question_text' => $validatedData['question_text'],
                'question_type' => $validatedData['question_type'],
                'question_order' => $nextOrder,
                'options' => $validatedData['options'] ?? [],
                'correct_answer' => $validatedData['correct_answer'],
                'explanation' => $validatedData['explanation'] ?? '',
                'question_source' => 'manual',
                'points' => $validatedData['points'] ?? 1,
                'is_active' => true,
                'created_by_professor' => $professor->professor_id,
            ]);

            // Update total questions count
            $totalQuestions = $quiz->questions()->count();
            $quiz->update(['total_questions' => $totalQuestions]);

            Log::info('Quiz question added successfully', [
                'quiz_id' => $quiz->quiz_id,
                'question_id' => $question->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Question added successfully.',
                'question_id' => $question->id,
                'total_questions' => $totalQuestions
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error adding quiz question', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit quiz (alternative method name)
     */
    public function editQuiz(Quiz $quiz)
    {
        return $this->editQuestions($quiz);
    }

    /**
     * Get quiz data for editing (alternative method name)
     */
    public function getQuizForEdit(Quiz $quiz)
    {
        return $this->editQuestions($quiz);
    }

    /**
     * Restore an archived quiz
     */
    public function restoreQuiz(Quiz $quiz)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            $quiz->update([
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update(['is_active' => false]);
            }

            Log::info('Quiz restored successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz restored to drafts successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error restoring quiz', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error restoring quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move quiz to draft status
     */
    public function moveToDraft(Quiz $quiz)
    {
        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            $quiz->update([
                'status' => 'draft',
                'is_draft' => true,
                'is_active' => false
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update(['is_active' => false]);
            }

            Log::info('Quiz moved to draft successfully', ['quiz_id' => $quiz->quiz_id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz moved to drafts successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error moving quiz to draft', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error moving quiz to draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing quiz with questions
     */
    public function updateQuizWithQuestions(Request $request, Quiz $quiz)
    {
        Log::info('=== Updating Quiz ===', [
            'quiz_id' => $quiz->quiz_id,
            'professor_id' => session('professor_id'),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_json' => $request->isJson(),
            'request_all' => $request->all(),
        ]);

        try {
            $professor = Professor::find(session('professor_id'));
            
            if (!$professor || $quiz->professor_id !== $professor->professor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found or access denied.'
                ], 403);
            }

            // Handle both form data and JSON requests
            $inputData = $request->isJson() ? $request->json()->all() : $request->all();
            $request->merge($inputData);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'module_id' => 'nullable|exists:modules,modules_id',
                'course_id' => 'nullable|exists:courses,subject_id',
                'description' => 'nullable|string',
                'instructions' => 'nullable|string',
                'is_draft' => 'nullable|boolean',
                'time_limit' => 'nullable|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'infinite_retakes' => 'nullable|boolean',
                'has_deadline' => 'nullable|boolean',
                'due_date' => 'nullable|date',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|string|in:multiple_choice,true_false',
                'questions.*.options' => 'nullable|array',
                'questions.*.correct_answers' => 'nullable|array',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.points' => 'nullable|numeric|min:0',
                'questions.*.order' => 'nullable|integer|min:1',
            ]);

            DB::beginTransaction();

            // Determine status based on is_draft flag
            $isDraft = $validatedData['is_draft'] ?? true;
            $status = $isDraft ? 'draft' : 'published';

            // Update quiz
            $quiz->update([
                'quiz_title' => $validatedData['title'],
                'quiz_description' => $validatedData['description'] ?? 'Manually created quiz.',
                'instructions' => $validatedData['instructions'] ?? 'Please answer the following questions.',
                'program_id' => $validatedData['program_id'],
                'module_id' => $validatedData['module_id'],
                'course_id' => $validatedData['course_id'],
                'status' => $status,
                'is_draft' => $isDraft,
                'is_active' => !$isDraft,
                'time_limit' => $validatedData['time_limit'] ?? 60,
                'max_attempts' => $validatedData['infinite_retakes'] ? 999 : ($validatedData['max_attempts'] ?? 1),
                'infinite_retakes' => $validatedData['infinite_retakes'] ?? false,
                'has_deadline' => $validatedData['has_deadline'] ?? false,
                'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                'total_questions' => count($validatedData['questions']),
                'updated_at' => now(),
            ]);

            // Update content item if exists
            if ($quiz->content_id) {
                ContentItem::where('id', $quiz->content_id)->update([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Updated Quiz',
                    'course_id' => $validatedData['course_id'],
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);
            } elseif (!empty($validatedData['course_id'])) {
                // Create content item if doesn't exist but course is specified
                $contentItem = ContentItem::create([
                    'content_title' => $validatedData['title'],
                    'content_description' => 'Updated Quiz',
                    'course_id' => $validatedData['course_id'],
                    'content_type' => 'quiz',
                    'content_data' => json_encode(['quiz_id' => $quiz->quiz_id]),
                    'due_date' => ($validatedData['has_deadline'] && $validatedData['due_date']) ? $validatedData['due_date'] : null,
                    'is_active' => !$isDraft,
                ]);

                $quiz->update(['content_id' => $contentItem->id]);
            }

            // Delete existing questions
            QuizQuestion::where('quiz_id', $quiz->quiz_id)->delete();

            // Create new questions
            foreach ($validatedData['questions'] as $index => $questionData) {
                $correctAnswer = '';
                if (!empty($questionData['correct_answers']) && is_array($questionData['correct_answers'])) {
                    $correctAnswer = $questionData['correct_answers'][0] ?? '';
                } else {
                    $correctAnswer = $questionData['correct_answers'] ?? '';
                }
                
                // Convert letter answers (A, B, C, D) to index format (0, 1, 2, 3)
                if (is_string($correctAnswer) && preg_match('/^[A-Z]$/', $correctAnswer)) {
                    $correctAnswer = (string)(ord($correctAnswer) - 65);
                }

                QuizQuestion::create([
                    'quiz_id' => $quiz->quiz_id,
                    'quiz_title' => $quiz->quiz_title,
                    'program_id' => $quiz->program_id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'question_order' => $questionData['order'] ?? ($index + 1),
                    'options' => $questionData['options'] ?? [],
                    'correct_answer' => $correctAnswer,
                    'explanation' => $questionData['explanation'] ?? '',
                    'question_source' => 'manual',
                    'points' => $questionData['points'] ?? 1,
                    'is_active' => true,
                    'created_by_professor' => $professor->professor_id,
                ]);
            }

            DB::commit();

            Log::info('✓ Quiz updated successfully', [
                'quiz_id' => $quiz->quiz_id, 
                'status' => $status,
                'questions_updated' => count($validatedData['questions'])
            ]);

            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Quiz draft updated successfully!' : 'Quiz updated and published successfully!',
                'quiz_id' => $quiz->quiz_id,
                'is_draft' => $isDraft
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed for quiz update', [
                'quiz_id' => $quiz->quiz_id,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quiz', [
                'quiz_id' => $quiz->quiz_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating quiz: ' . $e->getMessage()
            ], 500);
        }
    }
}
