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
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\AdminPreviewCustomization;

class BatchEnrollmentController extends Controller
{
    use AdminPreviewCustomization;
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
            'enrollment_deadline' => 'nullable|date',
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
            'registration_deadline' => $request->enrollment_deadline ? \Carbon\Carbon::parse($request->enrollment_deadline) : null,
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

    public function completeBatch($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        if (!in_array($batch->batch_status, ['ongoing', 'available'])) {
            return response()->json(['error' => 'Only ongoing or available batches can be completed'], 400);
        }

        $batch->update(['batch_status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Batch completed successfully',
            'new_status' => 'completed'
        ]);
    }

    public function reopenBatch($id)
    {
        // Authentication is handled by middleware

        $batch = StudentBatch::find($id);
        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        if (!in_array($batch->batch_status, ['completed', 'closed'])) {
            return response()->json(['error' => 'Only completed or closed batches can be reopened'], 400);
        }

        $batch->update(['batch_status' => 'ongoing']);

        return response()->json([
            'success' => true,
            'message' => 'Batch reopened successfully',
            'new_status' => 'ongoing'
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
            'professor_ids' => 'nullable|array',
            'professor_ids.*' => 'exists:professors,professor_id',
            'max_capacity' => 'required|integer|min:1',
            'batch_status' => 'required|in:pending,available,ongoing,completed,closed',
            'enrollment_deadline' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'batch_description' => 'nullable|string'
        ]);

        $batch = StudentBatch::findOrFail($id);
        
        // Check if max_capacity is not less than current enrollment
        if ($request->max_capacity < $batch->current_capacity) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum capacity cannot be less than current enrollment count (' . $batch->current_capacity . ')'
            ], 400);
        }
        
        $batch->update([
            'batch_name' => $request->batch_name,
            'program_id' => $request->program_id,
            'max_capacity' => $request->max_capacity,
            'batch_status' => $request->batch_status,
            'registration_deadline' => $request->enrollment_deadline ? \Carbon\Carbon::parse($request->enrollment_deadline) : null,
            'start_date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null,
            'end_date' => $request->end_date ? \Carbon\Carbon::parse($request->end_date) : null,
            'description' => $request->batch_description
        ]);

        // Update professor assignments if provided
        if (isset($request->professor_ids)) {
            // Remove existing assignments
            $batch->professors()->detach();
            
            // Add new assignments
            if (count($request->professor_ids) > 0) {
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
        }

        return response()->json([
            'success' => true,
            'message' => 'Batch updated successfully',
            'batch' => $batch->fresh()->load('program', 'professors')
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
        
        try {
            $batch = StudentBatch::with(['program', 'enrollments.user', 'enrollments.student', 'enrollments.program', 'enrollments.package'])->findOrFail($id);
            
            // Log for debugging
            Log::info('Exporting batch enrollments', [
                'batch_id' => $id,
                'batch_name' => $batch->batch_name,
                'enrollment_count' => $batch->enrollments->count()
            ]);
            
            $enrollments = $batch->enrollments->map(function ($enrollment) {
                $user = $enrollment->user;
                $student = $enrollment->student;
                
                // Get student name from user or student relationship
                $studentName = 'N/A';
                if ($user) {
                    $studentName = trim(($user->user_firstname ?? '') . ' ' . ($user->user_lastname ?? ''));
                } elseif ($student) {
                    $studentName = trim(($student->firstname ?? '') . ' ' . ($student->lastname ?? ''));
                }
                
                // Get email from user or student relationship
                $email = 'N/A';
                if ($user) {
                    $email = $user->email ?? 'N/A';
                } elseif ($student) {
                    $email = $student->email ?? 'N/A';
                }
                
                return [
                    'Student Name' => $studentName ?: 'N/A',
                    'Email' => $email,
                    'Program' => $enrollment->program->program_name ?? 'N/A',
                    'Package' => $enrollment->package->package_name ?? 'N/A',
                    'Enrollment Date' => $enrollment->created_at ? $enrollment->created_at->format('Y-m-d H:i:s') : 'N/A',
                    'Enrollment Status' => $enrollment->enrollment_status ?? 'N/A',
                    'Payment Status' => $enrollment->payment_status ?? 'N/A',
                    'Batch Access' => $enrollment->batch_access_granted ? 'Granted' : 'Pending',
                    'Amount' => $enrollment->package->price ?? 0
                ];
            });

            $filename = 'batch_' . $batch->batch_id . '_enrollments_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function() use ($enrollments) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8 encoding
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Add CSV headers
                if ($enrollments->isNotEmpty()) {
                    fputcsv($file, array_keys($enrollments->first()));
                    
                    // Add data rows
                    foreach ($enrollments as $enrollment) {
                        fputcsv($file, $enrollment);
                    }
                } else {
                    // If no enrollments, still create a file with headers
                    fputcsv($file, ['Student Name', 'Email', 'Program', 'Package', 'Enrollment Date', 'Enrollment Status', 'Payment Status', 'Batch Access', 'Amount']);
                    fputcsv($file, ['No enrollments found for this batch']);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Export error: ' . $e->getMessage(), [
                'batch_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Try to return JSON as fallback
            try {
                $batch = StudentBatch::with(['program', 'enrollments.user', 'enrollments.student', 'enrollments.program', 'enrollments.package'])->findOrFail($id);
                
                $enrollments = $batch->enrollments->map(function ($enrollment) {
                    $user = $enrollment->user;
                    $student = $enrollment->student;
                    
                    $studentName = 'N/A';
                    if ($user) {
                        $studentName = trim(($user->user_firstname ?? '') . ' ' . ($user->user_lastname ?? ''));
                    } elseif ($student) {
                        $studentName = trim(($student->firstname ?? '') . ' ' . ($student->lastname ?? ''));
                    }
                    
                    $email = 'N/A';
                    if ($user) {
                        $email = $user->email ?? 'N/A';
                    } elseif ($student) {
                        $email = $student->email ?? 'N/A';
                    }
                    
                    return [
                        'student_name' => $studentName ?: 'N/A',
                        'email' => $email,
                        'program' => $enrollment->program->program_name ?? 'N/A',
                        'package' => $enrollment->package->package_name ?? 'N/A',
                        'enrollment_date' => $enrollment->created_at ? $enrollment->created_at->format('Y-m-d H:i:s') : 'N/A',
                        'enrollment_status' => $enrollment->enrollment_status ?? 'N/A',
                        'payment_status' => $enrollment->payment_status ?? 'N/A',
                        'batch_access' => $enrollment->batch_access_granted ? 'Granted' : 'Pending',
                        'amount' => $enrollment->package->price ?? 0
                    ];
                });
                
                return response()->json([
                    'success' => true,
                    'message' => 'Export completed (JSON format due to CSV error)',
                    'batch' => [
                        'id' => $batch->batch_id,
                        'name' => $batch->batch_name,
                        'program' => $batch->program->program_name ?? 'N/A'
                    ],
                    'enrollments' => $enrollments,
                    'total_enrollments' => $enrollments->count()
                ]);
                
            } catch (\Exception $fallbackError) {
                Log::error('Fallback export also failed: ' . $fallbackError->getMessage());
                
                return response()->json([
                    'error' => 'Failed to export batch enrollments. Please try again later.',
                    'details' => $e->getMessage()
                ], 500);
            }
        }
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
    
    /**
     * Preview mode for tenant preview system
     */
    public function previewIndex($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Mock batches data
            $batchesCollection = collect([
                (object)[
                    'batch_id' => 1,
                    'batch_name' => 'Nursing Review Batch 2025-A',
                    'batch_code' => 'NUR2025A',
                    'description' => 'Comprehensive nursing review batch for 2025 board exam preparation', // Add missing property
                    'program_id' => 1,
                    'start_date' => now(), // Keep as Carbon object
                    'end_date' => now()->addMonths(6), // Keep as Carbon object
                    'registration_deadline' => now()->addDays(14), // Keep as Carbon object
                    'batch_status' => 'available',
                    'max_students' => 50,
                    'max_capacity' => 50,
                    'current_capacity' => 25,
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(10),
                    'program' => (object)[
                        'program_id' => 1,
                        'program_name' => 'Nursing Review'
                    ],
                    'professors' => collect([
                        (object)[
                            'professor_id' => 1,
                            'firstname' => 'Dr. Maria',
                            'lastname' => 'Santos',
                            'professor_name' => 'Dr. Maria Santos',
                            'professor_email' => 'maria.santos@example.com'
                        ]
                    ]),
                    'assignedProfessor' => (object)[
                        'professor_id' => 1,
                        'firstname' => 'Dr. Maria',
                        'lastname' => 'Santos',
                        'professor_first_name' => 'Dr. Maria',
                        'professor_last_name' => 'Santos',
                        'professor_name' => 'Dr. Maria Santos', // Add missing property
                        'name' => 'Dr. Maria Santos'
                    ]
                ],
                (object)[
                    'batch_id' => 2,
                    'batch_name' => 'Medical Technology Review Batch 2025-A',
                    'batch_code' => 'MEDTECH2025A',
                    'description' => 'Medical technology certification review program for board exam prep', // Add missing property
                    'program_id' => 2,
                    'start_date' => now()->addDays(30), // Keep as Carbon object
                    'end_date' => now()->addMonths(6)->addDays(30), // Keep as Carbon object
                    'registration_deadline' => now()->addDays(20), // Keep as Carbon object
                    'batch_status' => 'ongoing',
                    'max_students' => 40,
                    'max_capacity' => 40,
                    'current_capacity' => 35,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                    'program' => (object)[
                        'program_id' => 2,
                        'program_name' => 'Medical Technology Review'
                    ],
                    'professors' => collect([
                        (object)[
                            'professor_id' => 2,
                            'firstname' => 'Dr. Juan',
                            'lastname' => 'Garcia',
                            'professor_name' => 'Dr. Juan Garcia',
                            'professor_email' => 'juan.garcia@example.com'
                        ]
                    ]),
                    'assignedProfessor' => (object)[
                        'professor_id' => 2,
                        'firstname' => 'Dr. Juan',
                        'lastname' => 'Garcia',
                        'professor_first_name' => 'Dr. Juan',
                        'professor_last_name' => 'Garcia',
                        'professor_name' => 'Dr. Juan Garcia', // Add missing property
                        'name' => 'Dr. Juan Garcia'
                    ]
                ]
            ]);

            // Mock programs
            $programs = collect([
                (object)['program_id' => 1, 'program_name' => 'Nursing Review'],
                (object)['program_id' => 2, 'program_name' => 'Medical Technology Review']
            ]);

            // Mock professors
            $professors = collect([
                (object)[
                    'professor_id' => 1, 
                    'firstname' => 'Dr. Maria', 
                    'lastname' => 'Santos',
                    'professor_name' => 'Dr. Maria Santos',
                    'professor_email' => 'maria.santos@example.com'
                ],
                (object)[
                    'professor_id' => 2, 
                    'firstname' => 'Dr. Juan', 
                    'lastname' => 'Garcia',
                    'professor_name' => 'Dr. Juan Garcia',
                    'professor_email' => 'juan.garcia@example.com'
                ]
            ]);

            $html = view('admin.admin-student-enrollment.batch-enroll', [
                'batches' => $batchesCollection,
                'programs' => $programs,
                'professors' => $professors,
                'isPreview' => true
            ])->render();

            
            
            // Generate mock data for batch enrollment
            $students = $this->generateMockData('students');
            $programs = $this->generateMockData('programs');
            view()->share('students', $students);
            view()->share('programs', $programs);
            view()->share('isPreviewMode', true);
            
            return response($html);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin batches preview error: ' . $e->getMessage());
            // Fallback to simple HTML on error
            return response('
                <html>
                    <head><title>Admin Batches Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Admin Batches Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        } finally {
            // Clear session after render
            session()->forget(['user_name', 'user_role', 'logged_in', 'preview_mode']);
        }
    }
}
