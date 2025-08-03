<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Program;
use App\Models\Package;
use App\Models\EducationLevel;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Http\Controllers\ModularRegistrationController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModularRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected $modularController;
    protected $testProgram;
    protected $testPackage;
    protected $testEducationLevel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test program
        $this->testProgram = Program::create([
            'program_name' => 'Test Modular Program',
            'program_description' => 'Test program for modular enrollment',
            'archived' => false
        ]);

        // Create a test package
        $this->testPackage = Package::create([
            'package_name' => 'Test Modular Package',
            'description' => 'Test package for modular enrollment',
            'package_price' => 1000,
            'package_type' => 'modular',
            'archived' => false
        ]);

        // Create a test education level
        $this->testEducationLevel = EducationLevel::create([
            'level_name' => 'Undergraduate',
            'description' => 'Undergraduate level',
            'is_active' => true,
            'file_requirements' => json_encode([
                [
                    'field_name' => 'diploma_certificate',
                    'document_type' => 'Diploma Certificate',
                    'available_modular_plan' => true,
                    'is_required' => false
                ]
            ])
        ]);

        $this->modularController = new ModularRegistrationController(app()->make(\App\Services\OcrService::class));
    }

    /** @test */
    public function test_modular_enrollment_form_displays_correctly()
    {
        $response = $this->get('/enrollment/modular');
        
        $response->assertStatus(200);
        $response->assertViewIs('registration.Modular_enrollment');
        $response->assertViewHas('programs');
        $response->assertViewHas('packages');
    }

    /** @test */
    public function test_modular_enrollment_validation_fails_with_missing_data()
    {
        $response = $this->postJson('/enrollment/modular/submit', []);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors'
        ]);
    }

    /** @test */
    public function test_modular_enrollment_creates_user_and_registration()
    {
        $enrollmentData = [
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'learning_mode' => 'asynchronous',
            'selected_modules' => json_encode([
                ['id' => 1, 'name' => 'Test Module']
            ]),
            'education_level' => 'Undergraduate',
            'Start_Date' => now()->addDays(30)->format('Y-m-d'),
            'enrollment_type' => 'Modular',
            'user_firstname' => 'John',
            'user_lastname' => 'Doe',
            'email' => 'john.doe@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/enrollment/modular/submit', $enrollmentData);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@test.com',
            'user_firstname' => 'John',
            'user_lastname' => 'Doe',
            'role' => 'student'
        ]);

        // Verify registration was created
        $this->assertDatabaseHas('registrations', [
            'email' => 'john.doe@test.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'enrollment_type' => 'Modular',
            'status' => 'pending'
        ]);

        // Verify enrollment was created
        $this->assertDatabaseHas('enrollments', [
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'enrollment_type' => 'Modular',
            'enrollment_status' => 'pending'
        ]);
    }

    /** @test */
    public function test_modular_enrollment_with_logged_in_user()
    {
        // Create and login a user
        $user = User::create([
            'user_firstname' => 'Jane',
            'user_lastname' => 'Smith',
            'email' => 'jane.smith@test.com',
            'password' => bcrypt('password123'),
            'role' => 'student'
        ]);

        $this->actingAs($user);

        $enrollmentData = [
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'learning_mode' => 'synchronous',
            'selected_modules' => json_encode([
                ['id' => 1, 'name' => 'Test Module']
            ]),
            'education_level' => 'Undergraduate',
            'Start_Date' => now()->addDays(30)->format('Y-m-d'),
            'enrollment_type' => 'Modular'
        ];

        $response = $this->postJson('/enrollment/modular/submit', $enrollmentData);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify registration was created for existing user
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->user_id,
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'program_id' => $this->testProgram->program_id,
            'enrollment_type' => 'Modular'
        ]);
    }

    /** @test */
    public function test_modular_enrollment_step_validation()
    {
        $stepData = [
            'selected_modules' => json_encode([['id' => 1]]),
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'learning_mode' => 'asynchronous',
            'education_level' => 'Undergraduate',
            'Start_Date' => now()->addDays(30)->format('Y-m-d')
        ];

        $response = $this->postJson('/enrollment/modular/validate', $stepData);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Modular enrollment validation passed'
        ]);
    }

    /** @test */
    public function test_modular_batch_retrieval()
    {
        $response = $this->getJson("/api/modular/batches/{$this->testProgram->program_id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'batches',
            'auto_create'
        ]);
    }

    /** @test */
    public function test_modular_user_prefill()
    {
        // Create and login a user
        $user = User::create([
            'user_firstname' => 'Test',
            'user_lastname' => 'User',
            'email' => 'test.user@test.com',
            'password' => bcrypt('password123'),
            'role' => 'student'
        ]);

        session(['user_id' => $user->user_id]);

        $response = $this->getJson('/modular/registration/user-prefill');
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'firstname' => 'Test',
                'lastname' => 'User',
                'email' => 'test.user@test.com'
            ]
        ]);
    }

    /** @test */
    public function test_duplicate_modular_enrollment_prevention()
    {
        // Create first enrollment
        $enrollmentData = [
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'learning_mode' => 'asynchronous',
            'selected_modules' => json_encode([
                ['id' => 1, 'name' => 'Test Module']
            ]),
            'education_level' => 'Undergraduate',
            'Start_Date' => now()->addDays(30)->format('Y-m-d'),
            'enrollment_type' => 'Modular',
            'user_firstname' => 'John',
            'user_lastname' => 'Doe',
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // First enrollment
        $response1 = $this->postJson('/enrollment/modular/submit', $enrollmentData);
        $response1->assertStatus(200);

        // Attempt duplicate enrollment within 5 minutes
        $response2 = $this->postJson('/enrollment/modular/submit', $enrollmentData);
        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'message' => 'Registration already completed successfully!'
        ]);
    }

    /** @test */
    public function test_modular_enrollment_with_file_upload()
    {
        // Create a test file
        $testFile = \Illuminate\Http\UploadedFile::fake()->create('diploma.pdf', 1024, 'application/pdf');

        $enrollmentData = [
            'program_id' => $this->testProgram->program_id,
            'package_id' => $this->testPackage->package_id,
            'learning_mode' => 'asynchronous',
            'selected_modules' => json_encode([
                ['id' => 1, 'name' => 'Test Module']
            ]),
            'education_level' => 'Undergraduate',
            'Start_Date' => now()->addDays(30)->format('Y-m-d'),
            'enrollment_type' => 'Modular',
            'user_firstname' => 'John',
            'user_lastname' => 'Doe',
            'email' => 'file.test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'diploma_certificate' => $testFile
        ];

        $response = $this->postJson('/enrollment/modular/submit', $enrollmentData);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify file was processed
        $registration = Registration::where('email', 'file.test@test.com')->first();
        $this->assertNotNull($registration);
        $this->assertNotNull($registration->diploma_certificate);
    }

    protected function tearDown(): void
    {
        // Clean up test data
        DB::table('registrations')->where('email', 'like', '%@test.com')->delete();
        DB::table('enrollments')->whereIn('user_id', User::where('email', 'like', '%@test.com')->pluck('user_id'))->delete();
        DB::table('users')->where('email', 'like', '%@test.com')->delete();
        
        parent::tearDown();
    }
}
