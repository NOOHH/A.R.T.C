<?php

namespace App\Services;

use App\Models\StudentBatch;
use App\Models\Program;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BatchCreationService
{
    /**
     * Create a new batch for a program when no suitable batch exists
     * 
     * @param int $programId
     * @return StudentBatch
     */
    public function createPendingBatch(int $programId): StudentBatch
    {
        try {
            // Get the program
            $program = Program::findOrFail($programId);
            
            // Count existing batches for this program to generate unique name
            $existingBatchCount = StudentBatch::where('program_id', $programId)->count();
            $batchNumber = $existingBatchCount + 1;
            
            // Generate batch name
            $batchName = $program->program_name . ' ' . $batchNumber;
            
            // Calculate dates
            $registrationDeadline = Carbon::now()->addMonth(); // 1 month from creation
            $startDate = Carbon::now()->addWeeks(3); // 3 weeks from creation
            $endDate = $startDate->copy()->addMonths(8); // 8 months from start date
            
            // Create the batch
            $batch = StudentBatch::create([
                'batch_name' => $batchName,
                'program_id' => $programId,
                'max_capacity' => 30, // Default capacity
                'current_capacity' => 0, // Will be incremented when students enroll
                'batch_status' => 'pending',
                'registration_deadline' => $registrationDeadline,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'description' => "Auto-created batch for {$program->program_name}. Created due to student enrollment when no available batch existed.",
                'created_by' => 1, // Default admin ID - should be updated if we know the creating admin
                'professor_id' => null, // Will be assigned manually by admin
            ]);
            
            Log::info('Auto-created new batch for program', [
                'batch_id' => $batch->batch_id,
                'batch_name' => $batchName,
                'program_id' => $programId,
                'program_name' => $program->program_name,
                'capacity' => 30,
                'registration_deadline' => $registrationDeadline->toDateString(),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);
            
            return $batch;
            
        } catch (\Exception $e) {
            Log::error('Failed to create automatic batch', [
                'program_id' => $programId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Find or create a suitable batch for a program
     * 
     * @param int $programId
     * @param string $learningMode
     * @return StudentBatch|null
     */
    public function findOrCreateBatch(int $programId, string $learningMode = 'synchronous'): ?StudentBatch
    {
        // Only create batches for synchronous learning mode
        if (strtolower($learningMode) !== 'synchronous') {
            return null;
        }
        
        try {
            // First, try to find an existing batch with available capacity
            $availableBatch = StudentBatch::where('program_id', $programId)
                ->where('batch_status', 'pending')
                ->whereColumn('current_capacity', '<', 'max_capacity')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($availableBatch) {
                Log::info('Found available batch for program', [
                    'batch_id' => $availableBatch->batch_id,
                    'batch_name' => $availableBatch->batch_name,
                    'current_capacity' => $availableBatch->current_capacity,
                    'max_capacity' => $availableBatch->max_capacity
                ]);
                return $availableBatch;
            }
            
            // No available batch found, create a new one
            Log::info('No available batch found, creating new batch for program', [
                'program_id' => $programId,
                'learning_mode' => $learningMode
            ]);
            
            return $this->createPendingBatch($programId);
            
        } catch (\Exception $e) {
            Log::error('Error in findOrCreateBatch', [
                'program_id' => $programId,
                'learning_mode' => $learningMode,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Check if a batch is at capacity and create a new one if needed
     * 
     * @param int $batchId
     * @return StudentBatch|null
     */
    public function checkAndCreateNewBatchIfFull(int $batchId): ?StudentBatch
    {
        try {
            $batch = StudentBatch::findOrFail($batchId);
            
            // If batch is at capacity, create a new one
            if ($batch->current_capacity >= $batch->max_capacity) {
                Log::info('Batch is at capacity, creating new batch', [
                    'full_batch_id' => $batchId,
                    'full_batch_name' => $batch->batch_name,
                    'capacity' => $batch->current_capacity . '/' . $batch->max_capacity
                ]);
                
                return $this->createPendingBatch($batch->program_id);
            }
            
            return $batch;
            
        } catch (\Exception $e) {
            Log::error('Error checking batch capacity', [
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
