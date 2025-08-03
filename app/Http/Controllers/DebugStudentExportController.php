<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebugStudentExportController extends Controller
{
    public function debugExport(Request $request)
    {
        try {
            Log::info('Debug CSV export started', ['filters' => $request->all()]);
            
            // Check authentication
            $user = session('user_type');
            if (!$user) {
                return response()->json(['error' => 'Not authenticated', 'user_session' => session()->all()], 401);
            }
            
            // Get a small sample of students
            $students = Student::with(['user', 'program', 'enrollment.batch', 'enrollments.program', 'enrollments.package'])
                ->where('is_archived', false)
                ->limit(5)
                ->get();
                
            $exportData = [];
            foreach ($students as $student) {
                $enrollment = $student->enrollments->first();
                $exportData[] = [
                    'student_id' => $student->student_id ?? '',
                    'firstname' => $student->firstname ?? '',
                    'lastname' => $student->lastname ?? '',
                    'email' => $student->email ?? ($student->user->email ?? ''),
                    'program' => $student->program->program_name ?? 'N/A',
                    'status' => $student->date_approved ? 'Approved' : 'Pending',
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Debug export successful',
                'student_count' => $students->count(),
                'total_students' => Student::where('is_archived', false)->count(),
                'sample_data' => $exportData,
                'filters' => $request->all(),
                'user_session' => [
                    'user_type' => session('user_type'),
                    'user_id' => session('user_id'),
                    'user_name' => session('user_name'),
                ],
                'headers_test' => [
                    'content_type' => 'text/csv',
                    'content_disposition' => 'attachment; filename="test.csv"',
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Debug CSV export failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Debug export failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function testCsvDownload(Request $request)
    {
        try {
            $testData = [
                ['Student ID', 'Name', 'Email', 'Status'],
                ['001', 'John Doe', 'john@example.com', 'Active'],
                ['002', 'Jane Smith', 'jane@example.com', 'Pending'],
                ['003', 'Bob Johnson', 'bob@example.com', 'Active']
            ];
            
            $filename = 'test_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function() use ($testData) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                foreach ($testData as $row) {
                    fputcsv($file, $row);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Test CSV failed: ' . $e->getMessage()], 500);
        }
    }
}
