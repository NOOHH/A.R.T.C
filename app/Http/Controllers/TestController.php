<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Program;
use App\Models\Module;

class TestController extends Controller
{
    public function seedPrograms()
    {
        try {
            $programs = [
                [
                    'program_name' => 'Nursing Board Review',
                    'program_description' => 'Comprehensive review program for nursing board examination covering all essential topics and practical applications.',
                    'is_archived' => false,
                ],
                [
                    'program_name' => 'Medical Technology Review',
                    'program_description' => 'Complete preparation for medical technology board exam with laboratory simulations and practice tests.',
                    'is_archived' => false,
                ],
                [
                    'program_name' => 'Physical Therapy Review',
                    'program_description' => 'Intensive review for physical therapy board examination including anatomy, physiology, and rehabilitation techniques.',
                    'is_archived' => false,
                ],
                [
                    'program_name' => 'Pharmacy Review',
                    'program_description' => 'Board review program for pharmacy graduates covering pharmacology, pharmaceutical calculations, and drug interactions.',
                    'is_archived' => false,
                ],
                [
                    'program_name' => 'Medical Laboratory Science',
                    'program_description' => 'Specialized review for medical laboratory science professionals with hands-on laboratory experience.',
                    'is_archived' => false,
                ],
                [
                    'program_name' => 'Radiologic Technology Review',
                    'program_description' => 'Comprehensive review for radiologic technology board examination covering imaging techniques and radiation safety.',
                    'is_archived' => false,
                ]
            ];

            $created = [];
            $existingCount = 0;
            
            foreach ($programs as $programData) {
                $existing = Program::where('program_name', $programData['program_name'])->first();
                if (!$existing) {
                    $program = Program::create($programData);
                    $created[] = $program->program_name;
                } else {
                    $existingCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'created_programs' => $created,
                'existing_programs_count' => $existingCount,
                'total_programs' => Program::count(),
                'message' => count($created) . ' new programs created, ' . $existingCount . ' already existed.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function testDatabaseConnection()
    {
        try {
            $programsCount = Program::count();
            $tablesExist = DB::select('SHOW TABLES');
            
            return response()->json([
                'status' => 'success',
                'database_connected' => true,
                'programs_count' => $programsCount,
                'tables_count' => count($tablesExist),
                'message' => 'Database connection successful!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'database_connected' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
