<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Registration;
use App\Models\EducationLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DynamicEnrollmentController extends Controller
{
    /**
     * Create a new enrollment with dynamic inheritance from registration
     */
    public function createEnrollment(Request $request)
    {
        try {
            $validated = $request->validate([
                'registration_id' => 'required|exists:registrations,registration_id',
                'education_level_id' => 'required|exists:education_levels,id',
                'program_id' => 'nullable|exists:programs,program_id',
                'package_id' => 'nullable|exists:packages,package_id',
                'enrollment_type' => 'required|string',
                'learning_mode' => 'required|string',
            ]);

            $registration = Registration::findOrFail($validated['registration_id']);
            
            // Check if user can enroll in this education level
            if (!$registration->canEnrollInLevel($validated['education_level_id'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'User is already enrolled in this education level'
                ], 422);
            }

            DB::beginTransaction();

            // Create enrollment
            $enrollment = new Enrollment([
                'user_id' => $registration->user_id,
                'registration_id' => $validated['registration_id'],
                'education_level_id' => $validated['education_level_id'],
                'program_id' => $validated['program_id'],
                'package_id' => $validated['package_id'],
                'enrollment_type' => $validated['enrollment_type'],
                'learning_mode' => $validated['learning_mode'],
                'enrollment_status' => 'pending',
                'progression_stage' => 'initial',
                'education_level_started_at' => now(),
            ]);

            $enrollment->save();

            // Inherit data from registration
            $enrollment->inheritFromRegistration();

            DB::commit();

            Log::info('Dynamic enrollment created', [
                'enrollment_id' => $enrollment->enrollment_id,
                'registration_id' => $validated['registration_id'],
                'education_level_id' => $validated['education_level_id'],
                'user_id' => $registration->user_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enrollment created successfully with inherited data',
                'enrollment' => $enrollment->load(['registration', 'educationLevel'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error creating dynamic enrollment', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create enrollment'
            ], 500);
        }
    }

    /**
     * Create a progression enrollment to a higher education level
     */
    public function createProgression(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_enrollment_id' => 'required|exists:enrollments,enrollment_id',
                'target_education_level_id' => 'required|exists:education_levels,id',
                'program_id' => 'nullable|exists:programs,program_id',
                'package_id' => 'nullable|exists:packages,package_id',
            ]);

            $currentEnrollment = Enrollment::findOrFail($validated['current_enrollment_id']);
            
            // Create progression enrollment
            $progressionEnrollment = $currentEnrollment->createProgression(
                $validated['target_education_level_id'],
                $validated['program_id'],
                $validated['package_id']
            );

            if (!$progressionEnrollment) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot create progression. Current enrollment must be completed.'
                ], 422);
            }

            Log::info('Progression enrollment created', [
                'new_enrollment_id' => $progressionEnrollment->enrollment_id,
                'previous_enrollment_id' => $currentEnrollment->enrollment_id,
                'target_education_level_id' => $validated['target_education_level_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Progression enrollment created successfully',
                'enrollment' => $progressionEnrollment->load(['registration', 'educationLevel'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating progression enrollment', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create progression enrollment'
            ], 500);
        }
    }

    /**
     * Get user's enrollment history with progression tracking
     */
    public function getEnrollmentHistory(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
            ]);

            $enrollments = Enrollment::where('user_id', $validated['user_id'])
                ->with(['registration', 'educationLevel', 'program', 'package'])
                ->orderBy('education_level_started_at', 'asc')
                ->get();

            // Group enrollments by education level for progression tracking
            $progressionData = $enrollments->groupBy('education_level_id')->map(function ($levelEnrollments) {
                return [
                    'education_level' => $levelEnrollments->first()->educationLevel,
                    'enrollments' => $levelEnrollments->map(function ($enrollment) {
                        return [
                            'enrollment_id' => $enrollment->enrollment_id,
                            'program' => $enrollment->program,
                            'package' => $enrollment->package,
                            'enrollment_status' => $enrollment->enrollment_status,
                            'progression_stage' => $enrollment->progression_stage,
                            'started_at' => $enrollment->education_level_started_at,
                            'completed_at' => $enrollment->education_level_completed_at,
                            'inherited_data_summary' => [
                                'fields_count' => count($enrollment->inherited_registration_data ?? []),
                                'inherited_at' => $enrollment->inheritance_metadata['inherited_at'] ?? null,
                            ]
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'user_id' => $validated['user_id'],
                'total_enrollments' => $enrollments->count(),
                'education_levels_enrolled' => $progressionData->count(),
                'progression_data' => $progressionData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting enrollment history', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to get enrollment history'
            ], 500);
        }
    }

    /**
     * Update inherited data when registration changes
     */
    public function syncInheritedData(Request $request)
    {
        try {
            $validated = $request->validate([
                'registration_id' => 'required|exists:registrations,registration_id',
            ]);

            $enrollments = Enrollment::where('registration_id', $validated['registration_id'])
                ->get();

            $updated = 0;
            foreach ($enrollments as $enrollment) {
                if ($enrollment->inheritFromRegistration()) {
                    $updated++;
                }
            }

            Log::info('Inherited data synchronized', [
                'registration_id' => $validated['registration_id'],
                'enrollments_updated' => $updated
            ]);

            return response()->json([
                'success' => true,
                'message' => "Synchronized inherited data for {$updated} enrollments",
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing inherited data', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to sync inherited data'
            ], 500);
        }
    }

    /**
     * Get combined data for an enrollment (enrollment + inherited registration data)
     */
    public function getEnrollmentData(Request $request, $enrollmentId)
    {
        try {
            $enrollment = Enrollment::with(['registration', 'educationLevel', 'program', 'package'])
                ->findOrFail($enrollmentId);

            $combinedData = $enrollment->getCombinedData();

            return response()->json([
                'success' => true,
                'enrollment_id' => $enrollmentId,
                'combined_data' => $combinedData,
                'inheritance_metadata' => $enrollment->inheritance_metadata,
                'relationships' => [
                    'registration' => $enrollment->registration,
                    'education_level' => $enrollment->educationLevel,
                    'program' => $enrollment->program,
                    'package' => $enrollment->package,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting enrollment data', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'enrollment_id' => $enrollmentId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to get enrollment data'
            ], 500);
        }
    }
}
