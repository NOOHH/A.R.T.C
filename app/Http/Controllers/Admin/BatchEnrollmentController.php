<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentBatch;
use App\Models\Program;
use App\Models\Enrollment;
use Carbon\Carbon;

class BatchEnrollmentController extends Controller
{
    public function index()
    {
        // Authentication is handled by middleware

        $batches = StudentBatch::with(['program', 'assignedProfessor'])->orderBy('created_at', 'desc')->get();
        $programs = Program::where('is_archived', 0)->get();

        return view('admin.admin-student-enrollment.batch-enroll', [
            'batches' => $batches,
            'programs' => $programs
        ]);
    }

    public function store(Request $request)
    {
        // Authentication is handled by middleware

        $request->validate([
            'batch_name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,program_id',
            'max_capacity' => 'required|integer|min:1',
            'registration_deadline' => 'required|date|after:today',
            'start_date' => 'required|date|after:registration_deadline',
            'description' => 'nullable|string'
        ]);

        $batch = StudentBatch::create([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'current_capacity' => 0,
            'batch_status' => 'available',
            'registration_deadline' => Carbon::parse($request->registration_deadline),
            'start_date' => Carbon::parse($request->start_date),
            'description' => $request->description,
            'created_by' => session('admin_id') // Track who created the batch
        ]);

        return redirect()->back()->with('success', 'Batch "' . $batch->batch_name . '" created successfully!');
    }

    public function show($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        return response()->json($batch);
    }

    public function update(Request $request, $id)
    {
        // Authentication is handled by middleware

        $request->validate([
            'batch_name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,program_id',
            'max_capacity' => 'required|integer|min:1',
            'registration_deadline' => 'required|date',
            'start_date' => 'required|date|after:registration_deadline',
            'description' => 'nullable|string'
        ]);

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        // Don't allow reducing max_capacity below current_capacity
        if ($request->max_capacity < $batch->current_capacity) {
            return response()->json([
                'error' => 'Cannot reduce maximum capacity below current enrollment'
            ], 400);
        }

        $batch->update([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'registration_deadline' => Carbon::parse($request->registration_deadline),
            'start_date' => Carbon::parse($request->start_date),
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Batch updated successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        $newStatus = $batch->batch_status === 'closed' ? 'available' : 'closed';
        $batch->update(['batch_status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Batch status updated successfully',
            'new_status' => $newStatus
        ]);
    }

    public function students($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        $students = $batch->enrollments()
            ->with(['user', 'student'])
            ->get()
            ->map(function ($enrollment) {
                return [
                    'firstname' => $enrollment->user->user_firstname ?? $enrollment->student->first_name ?? 'N/A',
                    'lastname' => $enrollment->user->user_lastname ?? $enrollment->student->last_name ?? 'N/A',
                    'email' => $enrollment->user->email ?? 'N/A',
                    'enrollment_date' => $enrollment->created_at,
                    'status' => $enrollment->enrollment_status
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Get batches by program for AJAX requests (used in registration forms)
     */
    public function getBatchesByProgram(Request $request)
    {
        // Authentication is handled by middleware
        
        $programId = $request->get('program_id');
        
        if (!$programId) {
            return response()->json([]);
        }

        $batches = StudentBatch::where('program_id', $programId)
            ->where('batch_status', '!=', 'closed')
            ->where('registration_deadline', '>=', now())
            ->with('program')
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($batch) {
                return [
                    'batch_id' => $batch->batch_id,
                    'batch_name' => $batch->batch_name,
                    'program_name' => $batch->program->program_name ?? 'N/A',
                    'max_capacity' => $batch->max_capacity,
                    'current_capacity' => $batch->current_capacity,
                    'batch_status' => $batch->batch_status,
                    'registration_deadline' => $batch->registration_deadline->format('M d, Y'),
                    'start_date' => $batch->start_date->format('M d, Y'),
                    'description' => $batch->description,
                    'status' => $batch->batch_status === 'available' ? 'active' : 'inactive',
                    'schedule' => 'Live Classes - ' . $batch->start_date->format('M d, Y')
                ];
            });

        return response()->json($batches);
    }

    /**
     * Update batch details
     */
    public function updateBatch(Request $request, $id)
    {
        // Authentication is handled by middleware
        
        $request->validate([
            'batch_name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,program_id',
            'max_capacity' => 'required|integer|min:1',
            'registration_deadline' => 'required|date',
            'start_date' => 'required|date|after:registration_deadline',
            'description' => 'nullable|string'
        ]);

        $batch = StudentBatch::findOrFail($id);
        
        $batch->update([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'registration_deadline' => Carbon::parse($request->registration_deadline),
            'start_date' => Carbon::parse($request->start_date),
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Batch updated successfully',
            'batch' => $batch->load('program')
        ]);
    }

    /**
     * Delete a batch
     */
    public function deleteBatch($id)
    {
        // Authentication is handled by middleware
        
        $batch = StudentBatch::findOrFail($id);
        
        // Check if batch has enrolled students
        if ($batch->current_capacity > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete batch with enrolled students'
            ], 400);
        }
        
        $batchName = $batch->batch_name;
        $batch->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Batch '{$batchName}' deleted successfully"
        ]);
    }

    /**
     * Add students to batch
     */
    public function addStudentsToBatch(Request $request, $id)
    {
        // Authentication is handled by middleware
        
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,user_id'
        ]);

        $batch = StudentBatch::findOrFail($id);
        $studentIds = $request->student_ids;
        
        // Check capacity
        $newEnrollmentCount = count($studentIds);
        if ($batch->current_capacity + $newEnrollmentCount > $batch->max_capacity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough capacity in batch. Available slots: ' . ($batch->max_capacity - $batch->current_capacity)
            ], 400);
        }

        $enrolled = 0;
        foreach ($studentIds as $studentId) {
            // Check if student is already enrolled in this batch
            $existingEnrollment = Enrollment::where('user_id', $studentId)
                ->where('batch_id', $id)
                ->first();
                
            if (!$existingEnrollment) {
                Enrollment::create([
                    'user_id' => $studentId,
                    'program_id' => $batch->program_id,
                    'batch_id' => $id,
                    'enrollment_status' => 'active',
                    'enrollment_date' => now()
                ]);
                $enrolled++;
            }
        }

        // Update batch capacity
        $batch->increment('current_capacity', $enrolled);

        return response()->json([
            'success' => true,
            'message' => "{$enrolled} students enrolled successfully",
            'enrolled_count' => $enrolled
        ]);
    }

    /**
     * Remove student from batch
     */
    public function removeStudentFromBatch($batchId, $studentId)
    {
        // Authentication is handled by middleware
        
        $enrollment = Enrollment::where('batch_id', $batchId)
            ->where('user_id', $studentId)
            ->first();
            
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found in this batch'
            ], 404);
        }

        $enrollment->delete();
        
        // Update batch capacity
        $batch = StudentBatch::findOrFail($batchId);
        $batch->decrement('current_capacity');

        return response()->json([
            'success' => true,
            'message' => 'Student removed from batch successfully'
        ]);
    }

    /**
     * Export batch enrollment details
     */
    public function exportBatchEnrollments($id)
    {
        // Authentication is handled by middleware
        
        $batch = StudentBatch::with(['program', 'enrollments.user'])->findOrFail($id);
        
        $enrollments = $batch->enrollments->map(function ($enrollment) {
            return [
                'Student Name' => $enrollment->user->user_firstname . ' ' . $enrollment->user->user_lastname,
                'Email' => $enrollment->user->email,
                'Enrollment Date' => $enrollment->enrollment_date->format('Y-m-d'),
                'Status' => $enrollment->enrollment_status
            ];
        });

        $filename = 'batch_' . $batch->batch_id . '_enrollments_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            if ($enrollments->isNotEmpty()) {
                fputcsv($file, array_keys($enrollments->first()));
                
                // Add data rows
                foreach ($enrollments as $enrollment) {
                    fputcsv($file, $enrollment);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
