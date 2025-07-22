<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Registration;
use App\Models\Enrollment;

class SimpleRegistrationController extends Controller
{
    /**
     * Handle student registration - Simplified approach based on profile.php logic
     */
    public function store(Request $request)
    {
        Log::info('========== SIMPLE REGISTRATION ATTEMPT STARTED ==========');
        Log::info('All request data: ', $request->all());
        Log::info('File uploads: ', $request->files->all());
        
        try {
            DB::beginTransaction();
            
            // Basic validation
            $validatedData = $request->validate([
                'user_firstname' => 'required|string|max:255',
                'user_lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
                'program_id' => 'required|integer|exists:programs,program_id',
                'package_id' => 'required|integer|exists:packages,package_id',
                'enrollment_type' => 'required|in:Full,Modular',
                'learning_mode' => 'required|in:synchronous,asynchronous',
                'education_level' => 'required|in:Undergraduate,Graduate',
                'Start_Date' => 'required|date',
            ]);
            
            Log::info('Validation passed. Creating user...');
            
            // Create user (similar to profile.php user creation)
            $user = new User();
            $user->user_firstname = $validatedData['user_firstname'];
            $user->user_lastname = $validatedData['user_lastname'];
            $user->email = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->role = 'student';
            $user->admin_id = 1; // Default admin_id
            $user->directors_id = 1; // Default directors_id
            
            if (!$user->save()) {
                throw new \Exception('Failed to create user account');
            }
            
            Log::info('User created successfully', ['user_id' => $user->user_id]);
            
            // Create registration record with basic data
            $registration = new Registration();
            $registration->user_id = $user->user_id;
            $registration->program_id = $validatedData['program_id'];
            $registration->package_id = $validatedData['package_id'];
            $registration->enrollment_type = $validatedData['enrollment_type'];
            $registration->learning_mode = strtolower($validatedData['learning_mode']);
            $registration->education_level = $validatedData['education_level'];
            $registration->Start_Date = $validatedData['Start_Date'];
            $registration->status = 'pending';
            
            Log::info('About to handle file uploads...');
            
            // Handle file uploads using profile.php approach
            $this->handleFileUploads($request, $registration);
            
            Log::info('About to save registration...');
            Log::info('Registration data before save: ', $registration->toArray());
            
            // Save registration
            if (!$registration->save()) {
                throw new \Exception('Failed to create registration record');
            }
            
            Log::info('Registration created successfully', ['registration_id' => $registration->registration_id]);
            
            // Create enrollment record
            $enrollment = new Enrollment();
            $enrollment->user_id = $user->user_id;
            $enrollment->registration_id = $registration->registration_id;
            $enrollment->program_id = $validatedData['program_id'];
            $enrollment->package_id = $validatedData['package_id'];
            $enrollment->learning_mode = $validatedData['learning_mode'] === 'synchronous' ? 'Synchronous' : 'Asynchronous';
            $enrollment->enrollment_type = $validatedData['enrollment_type'];
            $enrollment->enrollment_status = 'pending';
            $enrollment->payment_status = 'pending';
            
            if (!$enrollment->save()) {
                throw new \Exception('Failed to create enrollment record');
            }
            
            Log::info('Enrollment created successfully', ['enrollment_id' => $enrollment->enrollment_id]);
            
            DB::commit();
            
            Log::info('Registration completed successfully', [
                'user_id' => $user->user_id,
                'registration_id' => $registration->registration_id,
                'enrollment_id' => $enrollment->enrollment_id
            ]);
            
            return redirect()->back()->with('success', 'Registration completed successfully! User ID: ' . $user->user_id . ', Registration ID: ' . $registration->registration_id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle file uploads using profile.php approach
     */
    private function handleFileUploads(Request $request, Registration $registration)
    {
        // Define file fields that can be uploaded
        $fileFields = [
            'valid_id' => 'valid_id',
            'birth_certificate' => 'birth_certificate',
            'good_moral' => 'good_moral',
            'diploma' => 'diploma',
            'tor' => 'tor',
            'school_id' => 'school_id'
        ];
        
        foreach ($fileFields as $fieldName => $dbColumn) {
            // Check if file was uploaded (Laravel way)
            if ($request->hasFile($fieldName)) {
                $uploadedFile = $request->file($fieldName);
                
                // Validate file (similar to profile.php)
                if ($uploadedFile->isValid()) {
                    $allowedMimes = ['jpg', 'jpeg', 'png', 'pdf'];
                    $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
                    
                    if (in_array($fileExtension, $allowedMimes)) {
                        // Create upload directory if it doesn't exist (same as profile.php)
                        $uploadDir = public_path('uploads/registrations/');
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        // Generate unique filename (same approach as profile.php)
                        $fileName = time() . '_' . uniqid() . '_' . $uploadedFile->getClientOriginalName();
                        $relativePath = 'uploads/registrations/' . $fileName;
                        
                        // Move uploaded file (Laravel equivalent of move_uploaded_file)
                        if ($uploadedFile->move($uploadDir, $fileName)) {
                            // Store relative path in database (same as profile.php)
                            $registration->{$dbColumn} = $relativePath;
                            
                            Log::info("File uploaded successfully", [
                                'field' => $fieldName,
                                'path' => $relativePath,
                                'size' => $uploadedFile->getSize()
                            ]);
                        } else {
                            Log::error("Failed to upload file for field: " . $fieldName);
                        }
                    } else {
                        Log::error("Invalid file type for field: " . $fieldName . " - " . $fileExtension);
                    }
                } else {
                    Log::error("Invalid file upload for field: " . $fieldName);
                }
            }
        }
    }
}
