<?php

namespace App\Http\Controllers\Traits;

trait AdminPreviewCustomization
{
    /**
     * Load tenant customization settings for admin preview
     */
    protected function loadAdminPreviewCustomization()
    {
        $websiteId = request()->get('website');
        
        if ($websiteId) {
            try {
                $client = \App\Models\Client::find($websiteId);
                if ($client) {
                    $tenantObj = \App\Models\Tenant::where('slug', $client->slug)->first();
                    if ($tenantObj) {
                        $tenantService = app(\App\Services\TenantService::class);
                        $tenantService->switchToTenant($tenantObj);
                        
                        // Load tenant-specific settings
                        $navbarSettings = \App\Models\Setting::getGroup('navbar');
                        $adminSettings = \App\Models\Setting::getGroup('admin_panel');
                        
                        $settings = [
                            'navbar' => [
                                'brand_name' => $navbarSettings ? $navbarSettings->get('brand_name', 'Ascendo Review and Training Center') : 'Ascendo Review and Training Center',
                                'brand_logo' => $navbarSettings ? $navbarSettings->get('brand_logo', null) : null,
                                'admin_subtext' => $navbarSettings ? $navbarSettings->get('admin_subtext', 'Admin Portal') : 'Admin Portal',
                            ],
                            'admin_panel' => [
                                'brand_name' => $adminSettings ? $adminSettings->get('brand_name', 'Ascendo Review and Training Center') : 'Ascendo Review and Training Center',
                                'brand_logo' => $adminSettings ? $adminSettings->get('brand_logo', null) : null,
                            ],
                        ];
                        
                        // Load admin settings from tenant database
                        $adminSettings = [];
                        try {
                            $adminSettings['director_view_students'] = \App\Models\AdminSetting::getValue('director_view_students', 'true');
                            $adminSettings['director_manage_programs'] = \App\Models\AdminSetting::getValue('director_manage_programs', 'false');
                            $adminSettings['director_manage_modules'] = \App\Models\AdminSetting::getValue('director_manage_modules', 'false');
                            $adminSettings['director_manage_professors'] = \App\Models\AdminSetting::getValue('director_manage_professors', 'false');
                            $adminSettings['director_manage_batches'] = \App\Models\AdminSetting::getValue('director_manage_batches', 'false');
                            $adminSettings['director_view_analytics'] = \App\Models\AdminSetting::getValue('director_view_analytics', 'false');
                            $adminSettings['director_manage_enrollments'] = \App\Models\AdminSetting::getValue('director_manage_enrollments', 'true');
                        } catch (\Exception $e) {
                            $adminSettings['director_view_students'] = 'true';
                            $adminSettings['director_manage_programs'] = 'false';
                            $adminSettings['director_manage_modules'] = 'false';
                            $adminSettings['director_manage_professors'] = 'false';
                            $adminSettings['director_manage_batches'] = 'false';
                            $adminSettings['director_view_analytics'] = 'false';
                            $adminSettings['director_manage_enrollments'] = 'true';
                        }
                        
                        $tenantService->switchToMain();
                        
                        // Share settings with the view
                        view()->share('settings', $settings);
                        view()->share('navbar', $settings['navbar'] ?? []);
                        view()->share('adminSettings', $adminSettings);
                        
                        return $settings;
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to load tenant customization for admin preview', [
                    'website_id' => $websiteId,
                    'controller' => get_class($this),
                    'error' => $e->getMessage()
                ]);
                // Continue with default settings
            }
        }
        
        return [];
    }

    /**
     * Generate mock data for admin preview pages
     */
    protected function generateMockData($type)
    {
        switch ($type) {
            case 'students':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'student_id' => 1,
                        'name' => 'John Doe',
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'john.doe@example.com',
                        'student_number' => 'STU001',
                        'created_at' => now(),
                        'status' => 'active',
                        'phone' => '09123456789'
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'student_id' => 2,
                        'name' => 'Jane Smith',
                        'first_name' => 'Jane',
                        'last_name' => 'Smith',
                        'email' => 'jane.smith@example.com',
                        'student_number' => 'STU002',
                        'created_at' => now()->subDays(5),
                        'status' => 'active',
                        'phone' => '09123456790'
                    ]),
                    $this->createMockObject([
                        'id' => 3,
                        'student_id' => 3,
                        'name' => 'Mike Johnson',
                        'first_name' => 'Mike',
                        'last_name' => 'Johnson',
                        'email' => 'mike.johnson@example.com',
                        'student_number' => 'STU003',
                        'created_at' => now()->subDays(10),
                        'status' => 'inactive',
                        'phone' => '09123456791'
                    ])
                ]);

            case 'professors':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'professor_id' => 1,
                        'name' => 'Dr. Sarah Wilson',
                        'first_name' => 'Sarah',
                        'last_name' => 'Wilson',
                        'email' => 'sarah.wilson@example.com',
                        'professor_number' => 'PROF001',
                        'department' => 'Computer Science',
                        'created_at' => now(),
                        'professor_archived' => 0,
                        'programs' => collect([
                            $this->createMockObject([
                                'program_id' => 1,
                                'program_name' => 'Computer Science Fundamentals'
                            ])
                        ])
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'professor_id' => 2,
                        'name' => 'Prof. Robert Chen',
                        'first_name' => 'Robert',
                        'last_name' => 'Chen',
                        'email' => 'robert.chen@example.com',
                        'professor_number' => 'PROF002',
                        'department' => 'Mathematics',
                        'created_at' => now()->subDays(30),
                        'professor_archived' => 0,
                        'programs' => collect([
                            $this->createMockObject([
                                'program_id' => 2,
                                'program_name' => 'Data Structures and Algorithms'
                            ])
                        ])
                    ])
                ]);

            case 'programs':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'program_id' => 1,
                        'name' => 'Computer Science Fundamentals',
                        'program_name' => 'Computer Science Fundamentals',
                        'description' => 'Introduction to programming and computer science concepts',
                        'duration' => '12 weeks',
                        'status' => 'active',
                        'created_at' => now()
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'program_id' => 2,
                        'name' => 'Data Structures and Algorithms',
                        'program_name' => 'Data Structures and Algorithms',
                        'description' => 'Advanced programming concepts and problem solving',
                        'duration' => '16 weeks',
                        'status' => 'active',
                        'created_at' => now()->subDays(15)
                    ])
                ]);

            case 'modules':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'modules_id' => 1,
                        'title' => 'Introduction to Programming',
                        'module_name' => 'Introduction to Programming',
                        'module_description' => 'Basic programming concepts and syntax',
                        'description' => 'Basic programming concepts and syntax',
                        'status' => 'published',
                        'order' => 1,
                        'created_at' => now()
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'modules_id' => 2,
                        'title' => 'Variables and Data Types',
                        'module_name' => 'Variables and Data Types',
                        'module_description' => 'Understanding different data types and variable usage',
                        'description' => 'Understanding different data types and variable usage',
                        'status' => 'published',
                        'order' => 2,
                        'created_at' => now()->subDays(5)
                    ])
                ]);

            case 'packages':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'package_id' => 1,
                        'name' => 'Basic Package',
                        'package_name' => 'Basic Package',
                        'description' => 'Entry-level learning package',
                        'package_description' => 'Entry-level learning package',
                        'price' => 99.99,
                        'package_price' => 99.99,
                        'package_type' => 'basic',
                        'selection_mode' => 'automatic',
                        'duration' => '3 months',
                        'status' => 'active',
                        'is_active' => true,
                        'created_at' => now(),
                        'enrollments_count' => 45
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'package_id' => 2,
                        'name' => 'Premium Package',
                        'package_name' => 'Premium Package',
                        'description' => 'Comprehensive learning package with extra features',
                        'package_description' => 'Comprehensive learning package with extra features',
                        'price' => 199.99,
                        'package_price' => 199.99,
                        'package_type' => 'premium',
                        'selection_mode' => 'manual',
                        'duration' => '6 months',
                        'status' => 'active',
                        'is_active' => true,
                        'created_at' => now()->subDays(10),
                        'enrollments_count' => 32
                    ])
                ]);

            case 'announcements':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'title' => 'Welcome to the New Semester',
                        'content' => 'We are excited to welcome all students to the new academic semester.',
                        'created_at' => now(),
                        'getCreator' => function() {
                            return $this->createMockObject(['name' => 'Admin User']);
                        }
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'title' => 'System Maintenance Notice',
                        'content' => 'The system will undergo maintenance on Saturday from 2-4 AM.',
                        'created_at' => now()->subDays(3),
                        'getCreator' => function() {
                            return $this->createMockObject(['name' => 'Technical Team']);
                        }
                    ])
                ]);

            case 'analytics':
                return [
                    'total_students' => 150,
                    'total_professors' => 25,
                    'total_programs' => 12,
                    'total_modules' => 48,
                    'active_enrollments' => 89,
                    'completion_rate' => 76.5,
                    'monthly_revenue' => 12450.00
                ];

            case 'directors':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'director_id' => 1,
                        'name' => 'Dr. Michael Thompson',
                        'full_name' => 'Dr. Michael Thompson',
                        'first_name' => 'Michael',
                        'last_name' => 'Thompson',
                        'email' => 'michael.thompson@example.com',
                        'position' => 'Academic Director',
                        'department' => 'Administration',
                        'created_at' => now()
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'director_id' => 2,
                        'name' => 'Ms. Linda Rodriguez',
                        'full_name' => 'Ms. Linda Rodriguez',
                        'first_name' => 'Linda',
                        'last_name' => 'Rodriguez',
                        'email' => 'linda.rodriguez@example.com',
                        'position' => 'Program Director',
                        'department' => 'Curriculum',
                        'created_at' => now()->subDays(20)
                    ])
                ]);

            case 'payments':
                return collect([
                    $this->createMockObject([
                        'id' => 1,
                        'payment_id' => 1,
                        'student_name' => 'John Doe',
                        'amount' => 199.99,
                        'payment_method' => 'Credit Card',
                        'status' => 'completed',
                        'payment_status' => 'completed',
                        'transaction_id' => 'TXN001',
                        'reference_number' => 'REF001',
                        'created_at' => now()
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'payment_id' => 2,
                        'student_name' => 'Jane Smith',
                        'amount' => 99.99,
                        'payment_method' => 'PayPal',
                        'status' => 'pending',
                        'payment_status' => 'pending',
                        'transaction_id' => 'TXN002',
                        'reference_number' => 'REF002',
                        'created_at' => now()->subHours(6)
                    ])
                ]);

            default:
                return collect();
        }
    }

    /**
     * Create a mock object with dynamic method support
     */
    protected function createMockObject($attributes)
    {
        $obj = new \stdClass();
        foreach ($attributes as $key => $value) {
            if (is_callable($value)) {
                $obj->$key = $value();
            } else {
                $obj->$key = $value;
            }
        }
        
        // Add common methods that might be called on the object
        $obj->getCreator = function() {
            return $this->createMockObject(['name' => 'System Admin']);
        };
        
        return $obj;
    }
}
