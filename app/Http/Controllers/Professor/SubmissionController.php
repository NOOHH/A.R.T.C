<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Program;
use App\Models\Module;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Check if professor is properly authenticated
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('SubmissionController: Not authenticated as professor via session', [
                    'logged_in' => session('logged_in'),
                    'professor_id' => session('professor_id'),
                    'user_role' => session('user_role'),
                    'session_data' => session()->all()
                ]);
                return redirect()->route('login')->with('error', 'Please log in as a professor to access submissions.');
            }

            Log::info('SubmissionController: Professor authentication successful', [
                'professor_id' => session('professor_id'),
                'user_role' => session('user_role')
            ]);

            // Get current professor
            $professor = Professor::where('professor_id', session('professor_id'))->first();
            
            if (!$professor) {
                return redirect()->route('professor.dashboard')->with('error', 'Professor not found.');
            }

            // Get only programs assigned to this professor
            $assignedPrograms = $professor->assignedPrograms()->get();
            $assignedProgramIds = $assignedPrograms->pluck('program_id')->toArray();

            // Get filter parameters
            $programId = $request->get('program_id');
            $moduleId = $request->get('module_id');
            $status = $request->get('status');

            // Build query for submissions with proper relationships
            // Only show submissions for professor's assigned programs
            $query = AssignmentSubmission::with([
                'student' => function($q) {
                    $q->with('user');
                }, 
                'program', 
                'module'
            ])->whereIn('program_id', $assignedProgramIds)
              ->orderBy('submitted_at', 'desc');

            // Apply filters
            if ($programId && in_array($programId, $assignedProgramIds)) {
                $query->where('program_id', $programId);
            }

            if ($moduleId) {
                $query->where('module_id', $moduleId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            // Get submissions with pagination
            $submissions = $query->paginate(10);

            // Process files data for each submission
            foreach ($submissions as $submission) {
                $processedFiles = [];
                
                if (is_string($submission->files)) {
                    $decoded = json_decode($submission->files, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $processedFiles = $decoded;
                    } else {
                        // If JSON decode fails, create a basic structure from other fields
                        if ($submission->original_filename && $submission->file_path) {
                            $processedFiles = [[
                                'name' => $submission->original_filename,
                                'original_filename' => $submission->original_filename,
                                'path' => $submission->file_path,
                                'file_path' => $submission->file_path,
                                'size' => 'Unknown'
                            ]];
                        }
                    }
                } elseif (is_array($submission->files)) {
                    $processedFiles = $submission->files;
                } else {
                    // If files is not an array or string, create basic structure
                    if ($submission->original_filename && $submission->file_path) {
                        $processedFiles = [[
                            'name' => $submission->original_filename,
                            'original_filename' => $submission->original_filename,
                            'path' => $submission->file_path,
                            'file_path' => $submission->file_path,
                            'size' => 'Unknown'
                        ]];
                    }
                }
                
                // Ensure each file in the array has the necessary keys
                if (is_array($processedFiles)) {
                    foreach ($processedFiles as &$file) {
                        if (!isset($file['name']) && isset($file['original_filename'])) {
                            $file['name'] = $file['original_filename'];
                        }
                        if (!isset($file['path']) && isset($file['file_path'])) {
                            $file['path'] = $file['file_path'];
                        }
                        if (!isset($file['size'])) {
                            $file['size'] = 'Unknown';
                        }
                    }
                }
                
                // Set the processed files using setAttribute to avoid the indirect modification error
                $submission->setAttribute('processed_files', $processedFiles);
            }

            // Get modules for the assigned programs for filter dropdowns
            $modules = Module::whereIn('program_id', $assignedProgramIds)
                ->where('is_archived', false)
                ->orderBy('module_name')
                ->get();

            return view('professor.submissions.index', compact(
                'submissions',
                'assignedPrograms',
                'modules',
                'programId',
                'moduleId',
                'status'
            ));

        } catch (\Exception $e) {
            Log::error('Professor submissions error: ' . $e->getMessage());
            return redirect()->route('professor.dashboard')
                ->with('error', 'Error loading submissions: ' . $e->getMessage());
        }
    }

    public function gradeSubmission(Request $request, $id)
    {
        try {
            // Check if professor is properly authenticated
            if (!session('logged_in') || !session('professor_id') || session('user_role') !== 'professor') {
                Log::error('SubmissionController gradeSubmission: Not authenticated as professor via session');
                return response()->json(['success' => false, 'message' => 'Authentication required.'], 401);
            }

            $request->validate([
                'grade' => 'required|numeric|min:0|max:100',
                'feedback' => 'nullable|string|max:2000',
                'status' => 'required|in:graded,reviewed'
            ]);

            // Get current professor
            $professor = Professor::where('professor_id', session('professor_id'))->first();
            
            if (!$professor) {
                return response()->json(['success' => false, 'message' => 'Professor not found.'], 403);
            }

            $submission = AssignmentSubmission::findOrFail($id);
            
            // Check if this submission belongs to professor's assigned programs
            $assignedProgramIds = $professor->assignedPrograms()->pluck('program_id')->toArray();
            
            if (!in_array($submission->program_id, $assignedProgramIds)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }

            // Update submission with grade and feedback
            $submission->update([
                'grade' => $request->grade,
                'feedback' => $request->feedback,
                'status' => $request->status,
                'graded_at' => now(),
                'graded_by' => $professor->professor_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assignment graded successfully!',
                'submission' => $submission
            ]);

        } catch (\Exception $e) {
            Log::error('Professor grade submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error grading submission: ' . $e->getMessage()
            ], 500);
        }
    }
}
