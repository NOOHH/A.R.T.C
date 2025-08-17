<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\WebsiteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ClientWebsiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin' && auth()->user()->email !== 'admin@smartprep.com') {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    public function createWebsiteFromRequest(WebsiteRequest $websiteRequest)
    {
        try {
            // Generate unique slug
            $slug = Str::slug($websiteRequest->business_name);
            $originalSlug = $slug;
            $counter = 1;

            while (Client::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $dbName = 'smartprep_' . str_replace('-', '_', $slug);

            // Create the client website by replicating A.R.T.C structure
            $websitePath = $this->replicateARTCStructure($slug, $websiteRequest->business_name, $dbName);

            // Create client record
            $client = Client::create([
                'name' => $websiteRequest->business_name,
                'slug' => $slug,
                'db_name' => $dbName,
                'db_host' => env('DB_HOST', '127.0.0.1'),
                'db_port' => (int) env('DB_PORT', 3306),
                'db_username' => env('DB_USERNAME', 'root'),
                'db_password' => env('DB_PASSWORD', ''),
                'user_id' => $websiteRequest->user_id,
                'status' => 'active'
            ]);

            // Update website request
            $websiteRequest->update([
                'status' => 'completed',
                'client_id' => $client->id,
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Website created successfully! Access at /t/{$slug}",
                'data' => [
                    'client' => $client,
                    'url' => "/t/{$slug}",
                    'admin_url' => "/t/{$slug}/admin/dashboard"
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating website: ' . $e->getMessage()
            ], 500);
        }
    }

    private function replicateARTCStructure($slug, $businessName, $dbName)
    {
        $artcPath = 'C:/xampp/htdocs/A.R.T.C';
        $clientPath = "C:/xampp/htdocs/SmartPrep/storage/client-websites/{$slug}";

        // Create client website directory
        if (!File::exists(dirname($clientPath))) {
            File::makeDirectory(dirname($clientPath), 0755, true);
        }

        // Copy essential A.R.T.C structure
        $this->copyARTCEssentials($artcPath, $clientPath, $businessName, $dbName);

        // Create database using A.R.T.C structure
        $this->createDatabaseFromARTC($dbName, $artcPath);

        return $clientPath;
    }

    private function copyARTCEssentials($sourcePath, $targetPath, $businessName, $dbName)
    {
        // Create basic directory structure
        File::makeDirectory($targetPath, 0755, true);
        File::makeDirectory($targetPath . '/public', 0755, true);
        File::makeDirectory($targetPath . '/resources/views', 0755, true);
        File::makeDirectory($targetPath . '/config', 0755, true);

        // Copy key A.R.T.C files that define the structure
        $filesToCopy = [
            '/composer.json',
            '/artisan',
            '/.env.example'
        ];

        foreach ($filesToCopy as $file) {
            if (File::exists($sourcePath . $file)) {
                File::copy($sourcePath . $file, $targetPath . $file);
            }
        }

        // Create custom .env for this client
        $envContent = File::get($sourcePath . '/.env.example');
        $envContent = str_replace([
            'DB_DATABASE=laravel',
            'APP_NAME=Laravel'
        ], [
            "DB_DATABASE={$dbName}",
            "APP_NAME=\"{$businessName}\""
        ], $envContent);

        File::put($targetPath . '/.env', $envContent);

        // Create a simple index.php that points to SmartPrep's tenant system
        $indexContent = "<?php
// This website is powered by SmartPrep
// Redirect to tenant system
header('Location: " . url('/t/' . basename($targetPath)) . "');
exit;
";
        File::put($targetPath . '/public/index.php', $indexContent);
    }

    private function createDatabaseFromARTC($dbName, $artcPath)
    {
        try {
            // Create database
            $conn = new \PDO("mysql:host=127.0.0.1", 'root', '');
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $conn->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn = null;

            // Temporarily switch database connection
            $originalDb = config('database.connections.mysql.database');
            config(['database.connections.mysql.database' => $dbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Run A.R.T.C migrations (using our ARTC-compatible migrations)
            Artisan::call('migrate', [
                '--database' => 'mysql',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            // Seed with A.R.T.C structure data
            $this->seedARTCStructure($dbName);

            // Restore original database connection
            config(['database.connections.mysql.database' => $originalDb]);
            DB::purge('mysql');
            DB::reconnect('mysql');

        } catch (\Exception $e) {
            // Restore original database connection on error
            if (isset($originalDb)) {
                config(['database.connections.mysql.database' => $originalDb]);
                DB::purge('mysql');
                DB::reconnect('mysql');
            }
            throw $e;
        }
    }

    private function seedARTCStructure($dbName)
    {
        // Switch to client database
        $originalDb = config('database.connections.mysql.database');
        config(['database.connections.mysql.database' => $dbName]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        try {
            // Create default A.R.T.C-style data

            // 1. Create default director (admin)
            $directorId = DB::table('directors')->insertGetId([
                'first_name' => 'Admin',
                'last_name' => 'Director',
                'email' => 'admin@' . strtolower(str_replace('sp_', '', $dbName)) . '.com',
                'password' => bcrypt('admin123'),
                'all_program_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Create programs (A.R.T.C style)
            $programIds = [];
            $programs = [
                [
                    'program_name' => 'Licensure Examination Review',
                    'program_description' => 'Comprehensive review program for professional licensure examinations',
                    'director_id' => $directorId
                ],
                [
                    'program_name' => 'Civil Service Examination Review',
                    'program_description' => 'Preparation course for civil service examinations',
                    'director_id' => $directorId
                ],
                [
                    'program_name' => 'Professional Development Program',
                    'program_description' => 'Continuing education and professional development courses',
                    'director_id' => $directorId
                ]
            ];

            foreach ($programs as $program) {
                $programIds[] = DB::table('programs')->insertGetId(array_merge($program, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }

            // 3. Create courses for each program
            $courseIds = [];
            foreach ($programIds as $index => $programId) {
                $courses = [
                    [
                        'course_name' => 'Foundation Course',
                        'course_description' => 'Basic foundation and review of fundamental concepts',
                        'program_id' => $programId,
                        'course_order' => 1
                    ],
                    [
                        'course_name' => 'Advanced Review',
                        'course_description' => 'Advanced topics and comprehensive review',
                        'program_id' => $programId,
                        'course_order' => 2
                    ],
                    [
                        'course_name' => 'Mock Examinations',
                        'course_description' => 'Practice tests and mock examinations',
                        'program_id' => $programId,
                        'course_order' => 3
                    ]
                ];

                foreach ($courses as $course) {
                    $courseIds[] = DB::table('courses')->insertGetId(array_merge($course, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                }
            }

            // 4. Create modules for courses
            foreach ($courseIds as $courseId) {
                $modules = [
                    [
                        'module_name' => 'Introduction and Overview',
                        'module_description' => 'Course introduction and overview of topics',
                        'course_id' => $courseId,
                        'module_order' => 1,
                        'content' => 'Welcome to this course module. This section provides an overview of what you will learn.'
                    ],
                    [
                        'module_name' => 'Core Concepts',
                        'module_description' => 'Essential concepts and principles',
                        'course_id' => $courseId,
                        'module_order' => 2,
                        'content' => 'This module covers the core concepts that form the foundation of the subject matter.'
                    ],
                    [
                        'module_name' => 'Practice and Application',
                        'module_description' => 'Practical exercises and real-world applications',
                        'course_id' => $courseId,
                        'module_order' => 3,
                        'content' => 'Apply what you have learned through practical exercises and case studies.'
                    ]
                ];

                foreach ($modules as $module) {
                    DB::table('modules')->insert(array_merge($module, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                }
            }

            // 5. Create default professors
            $professorIds = [];
            for ($i = 1; $i <= 3; $i++) {
                $professorIds[] = DB::table('professors')->insertGetId([
                    'first_name' => "Professor{$i}",
                    'last_name' => 'Smith',
                    'email' => "professor{$i}@" . strtolower(str_replace('sp_', '', $dbName)) . '.com',
                    'password' => bcrypt('professor123'),
                    'department' => 'Academic Department',
                    'bio' => "Experienced professor with expertise in various academic subjects.",
                    'referral_code' => "PROF{$i}" . strtoupper(substr($dbName, -3)),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 6. Create sample students
            for ($i = 1; $i <= 5; $i++) {
                DB::table('students')->insert([
                    'student_id' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'first_name' => "Student{$i}",
                    'last_name' => 'Doe',
                    'email' => "student{$i}@" . strtolower(str_replace('sp_', '', $dbName)) . '.com',
                    'password' => bcrypt('student123'),
                    'phone' => "+1234567890{$i}",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 7. Create welcome announcements
            foreach ($programIds as $programId) {
                DB::table('announcements')->insert([
                    'title' => 'Welcome to Our Training Center!',
                    'content' => 'We are excited to welcome you to our comprehensive training program. Our courses are designed to help you achieve your professional goals and excel in your chosen field.',
                    'author_id' => $directorId,
                    'author_type' => 'director',
                    'program_id' => $programId,
                    'is_active' => true,
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 8. Create sample batches
            foreach ($programIds as $index => $programId) {
                DB::table('batches')->insert([
                    'batch_name' => 'Batch ' . chr(65 + $index) . ' - ' . date('Y'),
                    'batch_description' => 'Regular batch for the academic year ' . date('Y'),
                    'program_id' => $programId,
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->addMonths(6)->format('Y-m-d'),
                    'status' => 'active',
                    'max_students' => 50,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

        } finally {
            // Restore original database connection
            config(['database.connections.mysql.database' => $originalDb]);
            DB::purge('mysql');
            DB::reconnect('mysql');
        }
    }
}
