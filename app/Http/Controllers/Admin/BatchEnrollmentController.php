<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentBatch;
use App\Models\Program;
use App\Models\Enrollment;
use App\Models\Professor;
use App\Models\User;
use Carbon\Carbon;

class BatchEnrollmentController extends Controller
{
    public function index()
    {
        // Authentication is handled by middleware

        $batches = StudentBatch::with(['program', 'professors'])->orderBy('created_at', 'desc')->get();
        $programs = Program::where('is_archived', 0)->get();
        $professors = Professor::where('professor_archived', 0)->get();

        return view('admin.admin-student-enrollment.batch-enroll', [
            'batches' => $batches,
            'programs' => $programs,
            'professors' => $professors
        ]);
    }

    public function create()
    {
        // Authentication is handled by middleware
        $programs = Program::where('is_archived', 0)->get();
        $professors = Professor::where('professor_archived', 0)->get();

        return view('admin.admin-student-enrollment.batch-create', [
            'programs' => $programs,
            'professors' => $professors
        ]);
    }

    public function store(Request $request)
    {
        // Authentication is handled by middleware

        $request->validate([
            'batch_name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,program_id',
            'professor_ids' => 'nullable|array',
            'professor_ids.*' => 'exists:professors,professor_id',
            'max_capacity' => 'required|integer|min:1',
            'registration_deadline' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'batch_description' => 'nullable|string',
            'batch_status' => 'required|in:available,ongoing,closed,completed'
        ]);

        // Auto-determine status based on start date if provided
        $status = $request->batch_status;
        if ($request->start_date) {
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $today = \Carbon\Carbon::today();
            $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null;
            
            if ($startDate->equalTo($today) || $startDate->lessThan($today)) {
                if ($endDate && $today->greaterThan($endDate)) {
                    $status = 'completed';
                } else {
                    $status = 'ongoing';
                }
            } else {
                $status = 'available';
            }
        }

        $batch = StudentBatch::create([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'current_capacity' => 0,
            'batch_status' => $status,
            'registration_deadline' => $request->registration_deadline ? \Carbon\Carbon::parse($request->registration_deadline) : null,
            'start_date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            'end_date' => $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null,
            'description' => $request->batch_description,
            'created_by' => session('admin_id'), // Track who created the batch
            'professor_id' => $request->professor_ids ? $request->professor_ids[0] : null // Keep for backward compatibility
        ]);

        // Assign multiple professors if provided
        if ($request->professor_ids && count($request->professor_ids) > 0) {
            $professorData = [];
            foreach ($request->professor_ids as $professorId) {
                $professorData[$professorId] = [
                    'assigned_at' => now(),
                    'assigned_by' => session('admin_id'),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            $batch->professors()->attach($professorData);
        }

        $professorCount = $request->professor_ids ? count($request->professor_ids) : 0;
        $message = 'Batch "' . $batch->batch_name . '" created successfully with status: ' . $status;
        if ($professorCount > 0) {
            $message .= ' and ' . $professorCount . ' professor(s) assigned.';
        }

        return redirect()->back()->with('success', $message);
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
            'professor_id' => 'nullable|integer', // Changed from exists check
            'max_capacity' => 'required|integer|min:1',
            'registration_deadline' => 'required|date',
            'start_date' => 'required|date', // Removed after:registration_deadline for flexibility
            'end_date' => 'nullable|date|after:start_date',
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

        $updateData = [
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'professor_id' => $request->professor_id,
            'max_capacity' => $request->max_capacity,
            'registration_deadline' => Carbon::parse($request->registration_deadline),
            'start_date' => Carbon::parse($request->start_date),
            'description' => $request->description
        ];
        
        // Handle end_date
        if ($request->end_date) {
            $updateData['end_date'] = Carbon::parse($request->end_date);
        } else {
            $updateData['end_date'] = null;
        }

        $batch->update($updateData);
        
        // Update batch status based on new dates
        $batch->updateStatusBasedOnDates();

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

    public function approveBatch($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        if ($batch->batch_status !== 'pending') {
            return response()->json(['error' => 'Only pending batches can be approved'], 400);
        }

        $batch->update(['batch_status' => 'available']);

        return response()->json([
            'success' => true,
            'message' => 'Batch approved successfully',
            'new_status' => 'available'
        ]);
    }

    public function students($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        $enrollments = $batch->enrollments()
            ->with(['user', 'student', 'program', 'package'])
            ->get()
            ->map(function ($enrollment) {
                $studentName = '';
                $studentEmail = '';
                
                // Get student name and email from user or student relationship
                if ($enrollment->user) {
                    $studentName = trim(($enrollment->user->user_firstname ?? '') . ' ' . ($enrollment->user->user_lastname ?? ''));
                    $studentEmail = $enrollment->user->email ?? '';
                } elseif ($enrollment->student) {
                    $studentName = trim(($enrollment->student->firstname ?? '') . ' ' . ($enrollment->student->lastname ?? ''));
                    $studentEmail = $enrollment->student->email ?? '';
                }
                
                // Determine if student is pending or current based on batch access
                $isPending = !$enrollment->batch_access_granted;
                $isCurrent = $enrollment->batch_access_granted;

                return [
                    'user_id' => $enrollment->user_id,
                    'enrollment_id' => $enrollment->enrollment_id,
                    'student_id' => $enrollment->student_id,
                    'name' => $studentName ?: 'N/A',
                    'email' => $studentEmail ?: 'N/A',
                    'enrollment_date' => $enrollment->created_at,
                    'enrollment_status' => $enrollment->enrollment_status,
                    'payment_status' => $enrollment->payment_status,
                    'program_name' => $enrollment->program->program_name ?? 'N/A',
                    'package_name' => $enrollment->package->package_name ?? 'N/A',
                    'amount' => $enrollment->package->price ?? 0,
                    'is_pending' => $isPending,
                    'is_current' => $isCurrent
                ];
            });

        // Separate pending and current students
        $pendingStudents = $enrollments->filter(function ($student) {
            return $student['is_pending'];
        })->values();

        $currentStudents = $enrollments->filter(function ($student) {
            return $student['is_current'];
        })->values();

        // Get available students (those who have enrollments but not in this batch or have no enrollment)
        $enrolledUserIds = $enrollments->pluck('user_id')->filter()->toArray();
        $enrolledStudentIds = $enrollments->pluck('student_id')->filter()->toArray();
        
        // Get students who could potentially be added to this batch
        $availableStudents = Enrollment::with(['user', 'student'])
            ->where('program_id', $batch->program_id)
            ->where(function($query) use ($id) {
                $query->where('batch_id', '!=', $id)
                      ->orWhereNull('batch_id');
            })
            ->get()
            ->map(function ($enrollment) {
                $studentName = '';
                $studentEmail = '';
                
                if ($enrollment->user) {
                    $studentName = trim(($enrollment->user->user_firstname ?? '') . ' ' . ($enrollment->user->user_lastname ?? ''));
                    $studentEmail = $enrollment->user->email ?? '';
                } elseif ($enrollment->student) {
                    $studentName = trim(($enrollment->student->firstname ?? '') . ' ' . ($enrollment->student->lastname ?? ''));
                    $studentEmail = $enrollment->student->email ?? '';
                }

                return [
                    'user_id' => $enrollment->user_id,
                    'enrollment_id' => $enrollment->enrollment_id,
                    'student_id' => $enrollment->student_id,
                    'name' => $studentName ?: 'N/A',
                    'email' => $studentEmail ?: 'N/A',
                    'enrollment_status' => $enrollment->enrollment_status,
                    'payment_status' => $enrollment->payment_status,
                ];
            })
            ->filter(function ($student) use ($enrolledUserIds, $enrolledStudentIds) {
                // Exclude students already in this batch
                return !in_array($student['user_id'], $enrolledUserIds) && 
                       !in_array($student['student_id'], $enrolledStudentIds);
            })
            ->values();

        return response()->json([
            'success' => true,
            'current_students' => $currentStudents,
            'pending_students' => $pendingStudents,
            'available_students' => $availableStudents,
            'total_current' => $currentStudents->count(),
            'total_pending' => $pendingStudents->count(),
            'total_available' => $availableStudents->count()
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

        // Update batch statuses first
        $this->updateBatchStatuses();

        $batches = StudentBatch::where('program_id', $programId)
            ->whereIn('batch_status', ['available', 'ongoing']) // Include ongoing batches
            ->where(function($query) {
                // Allow registration if deadline hasn't passed OR if batch is ongoing but still accepting
                $query->where('registration_deadline', '>=', now())
                      ->orWhere('batch_status', 'ongoing');
            })
            ->with('program')
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($batch) {
                $isOngoing = $batch->batch_status === 'ongoing';
                $daysStarted = $isOngoing ? now()->diffInDays($batch->start_date) : 0;
                
                return [
                    'batch_id' => $batch->batch_id,
                    'batch_name' => $batch->batch_name,
                    'program_name' => $batch->program->program_name ?? 'N/A',
                    'max_capacity' => $batch->max_capacity,
                    'current_capacity' => $batch->current_capacity,
                    'batch_status' => $batch->batch_status,
                    'registration_deadline' => $batch->registration_deadline ? $batch->registration_deadline->format('M d, Y') : 'Open',
                    'start_date' => $batch->start_date->format('M d, Y'),
                    'end_date' => $batch->end_date ? $batch->end_date->format('M d, Y') : null,
                    'description' => $batch->description,
                    'status' => $batch->batch_status === 'available' ? 'active' : ($isOngoing ? 'ongoing' : 'inactive'),
                    'schedule' => $isOngoing ? 'Ongoing - Started ' . $batch->start_date->format('M d, Y') : 'Live Classes - ' . $batch->start_date->format('M d, Y'),
                    'is_ongoing' => $isOngoing,
                    'days_started' => $daysStarted,
                    'has_available_slots' => $batch->hasAvailableSlots(),
                    'available_slots' => $batch->available_slots
                ];
            })
            ->filter(function($batch) {
                // Only show batches that have available slots
                return $batch['has_available_slots'];
            })
            ->values();

        return response()->json($batches);
    }

    /**
     * Update batch statuses based on current date
     */
    private function updateBatchStatuses()
    {
        $batches = StudentBatch::whereIn('batch_status', ['pending', 'available', 'ongoing'])
            ->get();
        
        foreach ($batches as $batch) {
            $batch->updateStatusBasedOnDates();
        }
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
            'start_date' => 'required|date', // Removed after:registration_deadline for flexibility
            'description' => 'nullable|string',
            'batch_status' => 'nullable|in:pending,available,ongoing,completed,closed',
            'end_date' => 'nullable|date|after:start_date',
            'professor_ids' => 'nullable|array',
            'professor_ids.*' => 'exists:professors,professor_id'
        ]);

        $batch = StudentBatch::findOrFail($id);
        
        $batch->update([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'registration_deadline' => Carbon::parse($request->registration_deadline),
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => $request->end_date ? Carbon::parse($request->end_date) : null,
            'description' => $request->description,
            'batch_status' => $request->batch_status ?? $batch->batch_status
        ]);

        // Handle professor assignments
        if ($request->has('professor_ids')) {
            $batch->professors()->sync($request->professor_ids ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => 'Batch updated successfully',
            'batch' => $batch->load('program', 'professors')
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
            $user = $enrollment->user;
            return [
                'Student Name' => $user ? $user->user_firstname . ' ' . $user->user_lastname : 'N/A',
                'Email' => $user ? $user->email : 'N/A',
                'Enrollment Date' => $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : 'N/A',
                'Status' => $enrollment->enrollment_status ?? 'N/A'
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

    /**
     * Get available students for batch assignment
     */
    public function getAvailableStudents($batchId)
    {
        // Authentication is handled by middleware
        
        $batch = StudentBatch::findOrFail($batchId);
        
        // Get students who are not already enrolled in this batch
        $enrolledStudentIds = Enrollment::where('batch_id', $batchId)->pluck('user_id');
        
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('user_id', $enrolledStudentIds)
            ->select('user_id', 'user_firstname', 'user_lastname', 'email')
            ->orderBy('user_firstname')
            ->get();

        return response()->json([
            'success' => true,
            'students' => $availableStudents
        ]);
    }



    /**
     * Add student from available list to batch
     */
    public function addStudentToBatch(Request $request, $batchId, $enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        $batch = StudentBatch::find($batchId);
        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found'
            ], 404);
        }

        // Check if already in this batch
        if ($enrollment->batch_id == $batchId) {
            return response()->json([
                'success' => false,
                'message' => 'Student already in this batch'
            ], 400);
        }

        // Get target type from request (current or pending)
        $requestData = $request->json()->all();
        $targetType = $requestData['target_type'] ?? 'pending';

        // Set status based on target type
        if ($targetType === 'current') {
            // Check capacity first
            $currentCount = Enrollment::where('batch_id', $batchId)
                ->where('batch_access_granted', true)
                ->count();

            if ($currentCount >= $batch->max_capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch is at maximum capacity'
                ], 400);
            }

            // Add to batch as current student (grant batch access)
            $enrollment->update([
                'batch_id' => $batchId,
                'batch_access_granted' => true
            ]);

            // Update batch capacity
            $newCurrentCount = Enrollment::where('batch_id', $batchId)
                ->where('batch_access_granted', true)
                ->count();
            $batch->update(['current_capacity' => $newCurrentCount]);

            $message = 'Student added to batch as current student (granted dashboard access)';
        } else {
            // Add to batch as pending student
            $enrollment->update([
                'batch_id' => $batchId,
                'batch_access_granted' => false
            ]);

            $message = 'Student added to batch as pending student';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Move student from pending to current (grants dashboard access without changing enrollment/payment status)
     */
    public function moveStudentToCurrent(Request $request, $batchId, $enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        $batch = StudentBatch::find($batchId);
        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found'
            ], 404);
        }

        // Check capacity first - count only current students with batch access
        $currentCount = Enrollment::where('batch_id', $batchId)
            ->where('batch_access_granted', true)
            ->count();

        if ($currentCount >= $batch->max_capacity) {
            return response()->json([
                'success' => false,
                'message' => 'Batch is at maximum capacity'
            ], 400);
        }

        // Grant batch access without changing enrollment/payment status
        $enrollment->update([
            'batch_id' => $batchId,
            'batch_access_granted' => true
        ]);

        // Update batch capacity based on batch access
        $newCurrentCount = Enrollment::where('batch_id', $batchId)
            ->where('batch_access_granted', true)
            ->count();
        $batch->update(['current_capacity' => $newCurrentCount]);

        return response()->json([
            'success' => true,
            'message' => 'Student moved to current (granted dashboard access with status notification)'
        ]);
    }

    /**
     * Move student from current to pending (removes dashboard access but keeps in batch)
     */
    public function moveStudentToPending(Request $request, $batchId, $enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        // Remove batch access without changing enrollment/payment status
        $enrollment->update([
            'batch_access_granted' => false
        ]);

        // Update batch capacity
        $batch = StudentBatch::find($batchId);
        if ($batch) {
            $newCurrentCount = Enrollment::where('batch_id', $batchId)
                ->where('batch_access_granted', true)
                ->count();
            $batch->update(['current_capacity' => $newCurrentCount]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Student moved to pending (dashboard access removed)'
        ]);
    }

    /**
     * Remove student from batch completely (moves back to available)
     */
    public function removeStudentFromBatchCompletely(Request $request, $batchId, $enrollmentId)
    {
        $enrollment = Enrollment::find($enrollmentId);
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        // Remove from batch by setting batch_id to null
        $enrollment->update([
            'batch_id' => null
        ]);

        // Update batch capacity
        $batch = StudentBatch::find($batchId);
        if ($batch) {
            $newCurrentCount = Enrollment::where('batch_id', $batchId)
                ->where('enrollment_status', 'approved')
                ->where('payment_status', 'paid')
                ->count();
            $batch->update(['current_capacity' => $newCurrentCount]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Student removed from batch and moved to available students'
        ]);
    }
}
