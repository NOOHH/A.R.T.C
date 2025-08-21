<?php

namespace App\Helpers;

use App\Models\Student;
use App\Models\Professor;
use App\Models\Tenant;
use App\Models\Client;
use App\Models\Enrollment;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantContextHelper
{
    /**
     * Detect tenant context for a student based on their enrollments
     */
    public static function detectStudentTenant($userId)
    {
        try {
            // Find the student
            $student = Student::where('user_id', $userId)->first();
            if (!$student) {
                return null;
            }

            // Get the student's enrollments
            $enrollments = Enrollment::where('student_id', $student->student_id)
                ->with('program')
                ->get();

            if ($enrollments->isEmpty()) {
                return null;
            }

            // For now, use the first enrollment's program to determine tenant
            // In the future, this could be enhanced to support multiple tenants per student
            $firstEnrollment = $enrollments->first();
            $program = $firstEnrollment->program;

            if (!$program) {
                return null;
            }

            // Try to find a tenant associated with this program
            // This is a simplified approach - you might want to add a tenant_id field to programs
            return self::findTenantByProgram($program);

        } catch (\Exception $e) {
            Log::error('Error detecting student tenant context: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Detect tenant context for a professor
     */
    public static function detectProfessorTenant($userId)
    {
        try {
            // Find the professor
            $professor = Professor::where('user_id', $userId)->first();
            if (!$professor) {
                return null;
            }

            // For now, we'll use a default tenant or the first available tenant
            // In the future, you might want to add a tenant_id field to professors
            return self::getDefaultTenant();

        } catch (\Exception $e) {
            Log::error('Error detecting professor tenant context: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find tenant by program (simplified approach)
     */
    private static function findTenantByProgram($program)
    {
        // This is a simplified approach - you might want to add a tenant_id field to programs
        // For now, we'll try to find a tenant by looking at the program name or other attributes
        
        // Try to find a tenant that matches the program's characteristics
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Check if the tenant's name or slug appears in the program name
            if (stripos($program->program_name, $tenant->name) !== false ||
                stripos($program->program_name, $tenant->slug) !== false) {
                return $tenant;
            }
        }

        // If no match found, return the first available tenant
        return $tenants->first();
    }

    /**
     * Get default tenant
     */
    private static function getDefaultTenant()
    {
        // Return the first available tenant or a specific default tenant
        return Tenant::first();
    }

    /**
     * Load tenant settings for a given tenant
     */
    public static function loadTenantSettings($tenant)
    {
        if (!$tenant) {
            return [];
        }

        try {
            // Switch to tenant database
            $tenantService = app(\App\Services\TenantService::class);
            $tenantService->switchToTenant($tenant);

            // Load settings from tenant database
            $settings = [];
            
            // Load navbar settings
            $settings['navbar'] = [
                'brand_name' => \App\Models\Setting::get('navbar', 'brand_name', 'Ascendo Review & Training Center'),
                'brand_logo' => \App\Models\Setting::get('navbar', 'brand_logo', null),
                'navbar_brand_name' => \App\Models\Setting::get('navbar', 'navbar_brand_name', 'Ascendo Review & Training Center'),
                'navbar_brand_logo' => \App\Models\Setting::get('navbar', 'navbar_brand_logo', null),
                'navbar_brand_image' => \App\Models\Setting::get('navbar', 'navbar_brand_image', null),
                'navbar_style' => \App\Models\Setting::get('navbar', 'navbar_style', 'fixed-top'),
                'navbar_menu_items' => \App\Models\Setting::get('navbar', 'navbar_menu_items', ''),
                'show_login_button' => \App\Models\Setting::get('navbar', 'show_login_button', true),
            ];

            // Load student portal settings
            $settings['student_portal'] = [
                'brand_name' => \App\Models\Setting::get('student_portal', 'brand_name', 'Ascendo Review & Training Center'),
                'brand_logo' => \App\Models\Setting::get('student_portal', 'brand_logo', null),
            ];

            // Load professor panel settings
            $settings['professor_panel'] = [
                'brand_name' => \App\Models\Setting::get('professor_panel', 'brand_name', 'Ascendo Review & Training Center'),
                'brand_logo' => \App\Models\Setting::get('professor_panel', 'brand_logo', null),
            ];

            // Load sidebar settings
            $settings['student_sidebar'] = [
                'primary_color' => \App\Models\Setting::get('student_sidebar', 'primary_color', '#3f4d69'),
                'secondary_color' => \App\Models\Setting::get('student_sidebar', 'secondary_color', '#2d2d2d'),
                'accent_color' => \App\Models\Setting::get('student_sidebar', 'accent_color', '#4f757d'),
                'text_color' => \App\Models\Setting::get('student_sidebar', 'text_color', '#e0e0e0'),
                'hover_color' => \App\Models\Setting::get('student_sidebar', 'hover_color', '#374151'),
                'background_color' => \App\Models\Setting::get('student_sidebar', 'background_color', '#f8f9fa'),
            ];

            $settings['professor_sidebar'] = [
                'primary_color' => \App\Models\Setting::get('professor_sidebar', 'primary_color', '#007bff'),
                'secondary_color' => \App\Models\Setting::get('professor_sidebar', 'secondary_color', '#6c757d'),
                'accent_color' => \App\Models\Setting::get('professor_sidebar', 'accent_color', '#28a745'),
                'text_color' => \App\Models\Setting::get('professor_sidebar', 'text_color', '#ffffff'),
                'hover_color' => \App\Models\Setting::get('professor_sidebar', 'hover_color', '#0056b3'),
                'background_color' => \App\Models\Setting::get('professor_sidebar', 'background_color', '#f8f9fa'),
            ];

            // Switch back to main database
            $tenantService->switchToMain();

            return $settings;

        } catch (\Exception $e) {
            Log::error('Error loading tenant settings: ' . $e->getMessage());
            
            // Switch back to main database in case of error
            try {
                $tenantService = app(\App\Services\TenantService::class);
                $tenantService->switchToMain();
            } catch (\Exception $switchError) {
                Log::error('Error switching back to main database: ' . $switchError->getMessage());
            }
            
            return [];
        }
    }

    /**
     * Get tenant context for current user
     */
    public static function getCurrentTenantContext()
    {
        $userId = session('user_id');
        $userRole = session('user_role');

        if (!$userId || !$userRole) {
            return null;
        }

        $tenant = null;

        if ($userRole === 'student') {
            $tenant = self::detectStudentTenant($userId);
        } elseif ($userRole === 'professor') {
            $tenant = self::detectProfessorTenant($userId);
        }

        if (!$tenant) {
            return null;
        }

        return [
            'tenant' => $tenant,
            'settings' => self::loadTenantSettings($tenant)
        ];
    }
} 