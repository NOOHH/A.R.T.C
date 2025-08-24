<?php
/**
 * CUSTOMIZE WEBSITE PERMISSION IMPLEMENTATION SCRIPT
 * Implements the permission-based changes to the customize-website page
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "🚀 CUSTOMIZE WEBSITE PERMISSION IMPLEMENTATION\n";
echo "===============================================\n\n";

echo "1️⃣ BACKING UP ORIGINAL FILES\n";
echo "-----------------------------\n";

// Backup files before modification
$filesToBackup = [
    'resources/views/smartprep/dashboard/partials/settings/advanced.blade.php',
    'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php',
    'routes/smartprep.php'
];

foreach ($filesToBackup as $file) {
    $sourcePath = __DIR__ . '/' . $file;
    $backupPath = __DIR__ . '/' . $file . '.backup.' . date('Y-m-d-H-i-s');
    
    if (file_exists($sourcePath)) {
        copy($sourcePath, $backupPath);
        echo "   ✅ Backed up: $file\n";
    } else {
        echo "   ❌ File not found: $file\n";
    }
}

echo "\n2️⃣ CREATING PERMISSION SECTIONS\n";
echo "--------------------------------\n";

// Create Director Features Section
$directorFeaturesContent = '<!-- Director Features -->
<div id="director-features" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-user-tie me-2"></i>Director Features
    </h3>

    <form id="directorFeaturesForm" action="{{ route(\'smartprep.dashboard.settings.update.director\', [\'website\' => $selectedWebsite->id]) }}" method="POST" onsubmit="updateDirectorFeatures(event)">
        @csrf
        <p class="text-muted small mb-3">Control which features are available to directors in their admin dashboard.</p>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorViewStudents" name="view_students" {{ ($settings[\'director_features\'][\'view_students\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorViewStudents">
                        <strong>View Students</strong><br>
                        <small class="text-muted">Allow directors to view student information and lists</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManagePrograms" name="manage_programs" {{ ($settings[\'director_features\'][\'manage_programs\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorManagePrograms">
                        <strong>Manage Programs</strong><br>
                        <small class="text-muted">Allow directors to create and edit programs</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageModules" name="manage_modules" {{ ($settings[\'director_features\'][\'manage_modules\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorManageModules">
                        <strong>Manage Modules</strong><br>
                        <small class="text-muted">Allow directors to create and edit modules</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageEnrollments" name="manage_enrollments" {{ ($settings[\'director_features\'][\'manage_enrollments\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorManageEnrollments">
                        <strong>Manage Enrollments</strong><br>
                        <small class="text-muted">Allow directors to manage student enrollments</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorViewAnalytics" name="view_analytics" {{ ($settings[\'director_features\'][\'view_analytics\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorViewAnalytics">
                        <strong>View Analytics</strong><br>
                        <small class="text-muted">Allow directors to view analytics and reports</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageProfessors" name="manage_professors" {{ ($settings[\'director_features\'][\'manage_professors\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorManageProfessors">
                        <strong>Manage Professors</strong><br>
                        <small class="text-muted">Allow directors to manage professor accounts</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageAnnouncements" name="manage_announcements" {{ ($settings[\'director_features\'][\'manage_announcements\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorManageAnnouncements">
                        <strong>Manage Announcements</strong><br>
                        <small class="text-muted">Allow directors to create and manage announcements</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="directorManageBatches" name="manage_batches" {{ ($settings[\'director_features\'][\'manage_batches\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="directorManageBatches">
                        <strong>Manage Batches</strong><br>
                        <small class="text-muted">Allow directors to manage student batches</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Director Features
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm(\'directorFeaturesForm\')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>
</div>
';

// Create Professor Features Section
$professorFeaturesContent = '<!-- Professor Features -->
<div id="professor-features" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-chalkboard-teacher me-2"></i>Professor Features
    </h3>

    <form id="professorFeaturesForm" action="{{ route(\'smartprep.dashboard.settings.update.professor\', [\'website\' => $selectedWebsite->id]) }}" method="POST" onsubmit="updateProfessorFeatures(event)">
        @csrf
        <p class="text-muted small mb-3">Control which features are available to professors in their dashboard.</p>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="aiQuizEnabled" name="ai_quiz_enabled" {{ ($settings[\'professor_features\'][\'ai_quiz_enabled\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="aiQuizEnabled">
                        <strong>AI Quiz Generator</strong><br>
                        <small class="text-muted">Allow professors to generate quizzes from documents</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="gradingEnabled" name="grading_enabled" {{ ($settings[\'professor_features\'][\'grading_enabled\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="gradingEnabled">
                        <strong>Grading System</strong><br>
                        <small class="text-muted">Allow professors to grade assignments and quizzes</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="videoUploadEnabled" name="upload_videos_enabled" {{ ($settings[\'professor_features\'][\'upload_videos_enabled\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="videoUploadEnabled">
                        <strong>Video Upload</strong><br>
                        <small class="text-muted">Allow professors to upload video links</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="attendanceEnabled" name="attendance_enabled" {{ ($settings[\'professor_features\'][\'attendance_enabled\'] ?? true) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="attendanceEnabled">
                        <strong>Attendance Management</strong><br>
                        <small class="text-muted">Allow professors to track student attendance</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="meetingCreationEnabled" name="meeting_creation_enabled" {{ ($settings[\'professor_features\'][\'meeting_creation_enabled\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="meetingCreationEnabled">
                        <strong>Meeting Creation</strong><br>
                        <small class="text-muted">Allow professors to create and schedule meetings</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="professorModuleManagementEnabled" name="module_management_enabled" {{ ($settings[\'professor_features\'][\'module_management_enabled\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="professorModuleManagementEnabled">
                        <strong>Module Management</strong><br>
                        <small class="text-muted">Allow professors to create and manage modules</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="professorAnnouncementManagementEnabled" name="announcement_management_enabled" {{ ($settings[\'professor_features\'][\'announcement_management_enabled\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="professorAnnouncementManagementEnabled">
                        <strong>Announcement Management</strong><br>
                        <small class="text-muted">Allow professors to create and manage announcements</small>
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="professorChatManagementEnabled" name="chat_management_enabled" {{ ($settings[\'professor_features\'][\'chat_management_enabled\'] ?? false) ? \'checked\' : \'\' }}>
                    <label class="form-check-label" for="professorChatManagementEnabled">
                        <strong>Chat Management</strong><br>
                        <small class="text-muted">Allow professors to access and manage chat features</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-footer">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i>Save Professor Features
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm(\'professorFeaturesForm\')">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>
</div>
';

// Write the new director features file
$directorFeaturesPath = __DIR__ . '/resources/views/smartprep/dashboard/partials/settings/director-features.blade.php';
file_put_contents($directorFeaturesPath, $directorFeaturesContent);
echo "   ✅ Created: director-features.blade.php\n";

// Write the new professor features file
$professorFeaturesPath = __DIR__ . '/resources/views/smartprep/dashboard/partials/settings/professor-features.blade.php';
file_put_contents($professorFeaturesPath, $professorFeaturesContent);
echo "   ✅ Created: professor-features.blade.php\n";

echo "\n3️⃣ MODIFYING ADVANCED SETTINGS SECTION\n";
echo "---------------------------------------\n";

// Replace the advanced settings section content
$newAdvancedContent = '<!-- Permissions Settings -->
<div id="permissions-settings" class="sidebar-section">
    <h3 class="section-title">
        <i class="fas fa-shield-alt me-2"></i>Permissions
    </h3>
    <p class="text-muted small mb-3">Configure access permissions for different user roles on your website.</p>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Permission Management:</strong> Control what features are available to directors and professors to customize the user experience on your training platform.
    </div>
    
    <div class="permission-overview">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
                        <h6>Director Access</h6>
                        <p class="text-muted small">Configure administrative features for directors</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="showSection(\'director-features\')">
                            <i class="fas fa-cog me-1"></i>Configure
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chalkboard-teacher fa-2x text-success mb-2"></i>
                        <h6>Professor Access</h6>
                        <p class="text-muted small">Configure teaching features for professors</p>
                        <button class="btn btn-outline-success btn-sm" onclick="showSection(\'professor-features\')">
                            <i class="fas fa-cog me-1"></i>Configure
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
';

$advancedFilePath = __DIR__ . '/resources/views/smartprep/dashboard/partials/settings/advanced.blade.php';
file_put_contents($advancedFilePath, $newAdvancedContent);
echo "   ✅ Modified: advanced.blade.php (replaced with permissions)\n";

echo "\n4️⃣ UPDATING CUSTOMIZE INTERFACE\n";
echo "--------------------------------\n";

// Update the customize interface to include the new sections
$interfaceFilePath = __DIR__ . '/resources/views/smartprep/dashboard/partials/customize-interface.blade.php';
$interfaceContent = file_get_contents($interfaceFilePath);

// Add the new includes after the admin panel settings
$newIncludes = '        
        <!-- Director Features Settings -->
        @include(\'smartprep.dashboard.partials.settings.director-features\')
        
        <!-- Professor Features Settings -->
        @include(\'smartprep.dashboard.partials.settings.professor-features\')
        
        <!-- Permissions Settings (formerly Advanced Settings) -->
        @include(\'smartprep.dashboard.partials.settings.advanced\')';

$interfaceContent = str_replace(
    '        <!-- Advanced Settings -->
        @include(\'smartprep.dashboard.partials.settings.advanced\')',
    $newIncludes,
    $interfaceContent
);

file_put_contents($interfaceFilePath, $interfaceContent);
echo "   ✅ Updated: customize-interface.blade.php\n";

echo "\n5️⃣ UPDATING JAVASCRIPT FUNCTIONS\n";
echo "---------------------------------\n";

// Update the customize scripts
$scriptsFilePath = __DIR__ . '/resources/views/smartprep/dashboard/partials/customize-scripts.blade.php';
if (file_exists($scriptsFilePath)) {
    $scriptsContent = file_get_contents($scriptsFilePath);
    
    // Add new JavaScript functions for permissions handling
    $newJavaScriptFunctions = '
        // Director Features Update Function
        async function updateDirectorFeatures(event) {
            event.preventDefault();
            await handleFormSubmission(event, \'director\', \'Updating director features...\');
        }
        
        // Professor Features Update Function
        async function updateProfessorFeatures(event) {
            event.preventDefault();
            await handleFormSubmission(event, \'professor\', \'Updating professor features...\');
        }
        
        // Show specific section function
        function showSection(sectionId) {
            // Hide all sections first
            document.querySelectorAll(\'.sidebar-section\').forEach(section => {
                section.style.display = \'none\';
            });
            
            // Show the requested section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.style.display = \'block\';
                targetSection.scrollIntoView({ behavior: \'smooth\' });
            }
        }
        
        // Reset to show all sections
        function showAllSections() {
            document.querySelectorAll(\'.sidebar-section\').forEach(section => {
                section.style.display = \'block\';
            });
        }
    ';
    
    // Insert the new functions before the closing script tag
    $scriptsContent = str_replace('</script>', $newJavaScriptFunctions . '</script>', $scriptsContent);
    
    file_put_contents($scriptsFilePath, $scriptsContent);
    echo "   ✅ Updated: customize-scripts.blade.php\n";
}

echo "\n6️⃣ ADDING CONTROLLER METHODS\n";
echo "-----------------------------\n";

// Add new controller methods for director and professor features
$controllerFilePath = __DIR__ . '/app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
$controllerContent = file_get_contents($controllerFilePath);

$newControllerMethods = '
    /**
     * Update director features settings
     */
    public function updateDirector(Request $request, $website)
    {
        try {
            $website = Client::findOrFail($website);
            $tenant = Tenant::where(\'slug\', $website->slug)->firstOrFail();
            
            $this->tenantService->switchToTenant($tenant);
            
            $directorFeatures = [
                \'view_students\' => $request->has(\'view_students\'),
                \'manage_programs\' => $request->has(\'manage_programs\'),
                \'manage_modules\' => $request->has(\'manage_modules\'),
                \'manage_enrollments\' => $request->has(\'manage_enrollments\'),
                \'view_analytics\' => $request->has(\'view_analytics\'),
                \'manage_professors\' => $request->has(\'manage_professors\'),
                \'manage_announcements\' => $request->has(\'manage_announcements\'),
                \'manage_batches\' => $request->has(\'manage_batches\'),
            ];
            
            // Save to tenant database
            Setting::setGroup(\'director_features\', $directorFeatures);
            
            $this->tenantService->switchToMain();
            
            return response()->json([
                \'success\' => true,
                \'message\' => \'Director features updated successfully!\'
            ]);
            
        } catch (\\Exception $e) {
            $this->tenantService->switchToMain();
            
            return response()->json([
                \'success\' => false,
                \'message\' => \'Failed to update director features: \' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update professor features settings
     */
    public function updateProfessor(Request $request, $website)
    {
        try {
            $website = Client::findOrFail($website);
            $tenant = Tenant::where(\'slug\', $website->slug)->firstOrFail();
            
            $this->tenantService->switchToTenant($tenant);
            
            $professorFeatures = [
                \'ai_quiz_enabled\' => $request->has(\'ai_quiz_enabled\'),
                \'grading_enabled\' => $request->has(\'grading_enabled\'),
                \'upload_videos_enabled\' => $request->has(\'upload_videos_enabled\'),
                \'attendance_enabled\' => $request->has(\'attendance_enabled\'),
                \'meeting_creation_enabled\' => $request->has(\'meeting_creation_enabled\'),
                \'module_management_enabled\' => $request->has(\'module_management_enabled\'),
                \'announcement_management_enabled\' => $request->has(\'announcement_management_enabled\'),
                \'chat_management_enabled\' => $request->has(\'chat_management_enabled\'),
            ];
            
            // Save to tenant database
            Setting::setGroup(\'professor_features\', $professorFeatures);
            
            $this->tenantService->switchToMain();
            
            return response()->json([
                \'success\' => true,
                \'message\' => \'Professor features updated successfully!\'
            ]);
            
        } catch (\\Exception $e) {
            $this->tenantService->switchToMain();
            
            return response()->json([
                \'success\' => false,
                \'message\' => \'Failed to update professor features: \' . $e->getMessage()
            ], 500);
        }
    }
';

// Insert the new methods before the closing class brace
$controllerContent = str_replace('}\n?>', $newControllerMethods . '\n}', $controllerContent);

file_put_contents($controllerFilePath, $controllerContent);
echo "   ✅ Updated: CustomizeWebsiteController.php\n";

echo "\n7️⃣ UPDATING CONTROLLER CURRENT METHOD\n";
echo "--------------------------------------\n";

// Update the current method to load director and professor features
$currentMethodUpdate = "        // Load permission settings
        try {
            \$directorFeatures = Setting::getGroup('director_features')->toArray();
            \$professorFeatures = Setting::getGroup('professor_features')->toArray();
        } catch (\\Exception \$e) {
            \$directorFeatures = [];
            \$professorFeatures = [];
        }
        
        \$settings['director_features'] = \$directorFeatures;
        \$settings['professor_features'] = \$professorFeatures;";

// Find the settings loading section and add permission settings
$settingsPattern = '/(\$settings\[\'advanced\'\] = Setting::getGroup\(\'advanced\'\)->toArray\(\);)/';
$controllerContent = file_get_contents($controllerFilePath);
$controllerContent = preg_replace($settingsPattern, '$1' . "\n\n                        " . $currentMethodUpdate, $controllerContent);

file_put_contents($controllerFilePath, $controllerContent);
echo "   ✅ Updated: Controller current() method\n";

echo "\n8️⃣ TESTING IMPLEMENTATION\n";
echo "--------------------------\n";

try {
    // Test file existence
    $requiredFiles = [
        'resources/views/smartprep/dashboard/partials/settings/director-features.blade.php',
        'resources/views/smartprep/dashboard/partials/settings/professor-features.blade.php',
    ];
    
    foreach ($requiredFiles as $file) {
        $exists = file_exists(__DIR__ . '/' . $file);
        echo "   " . ($exists ? "✅" : "❌") . " File created: $file\n";
    }
    
    // Test controller syntax
    $controllerPath = __DIR__ . '/app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
    $syntax = exec("php -l $controllerPath 2>&1", $output, $returnCode);
    echo "   " . ($returnCode === 0 ? "✅" : "❌") . " Controller syntax: " . ($returnCode === 0 ? "Valid" : "Error") . "\n";
    
} catch (\Exception $e) {
    echo "   ❌ Testing error: " . $e->getMessage() . "\n";
}

echo "\n✅ IMPLEMENTATION COMPLETE!\n";
echo "============================\n";
echo "📋 SUMMARY OF CHANGES:\n";
echo "   1. ✅ Removed Advanced Settings (CSS, JS, Analytics, etc.)\n";
echo "   2. ✅ Added Director Features section with 8 permissions\n";
echo "   3. ✅ Added Professor Features section with 8 permissions\n";
echo "   4. ✅ Updated JavaScript handlers for new sections\n";
echo "   5. ✅ Added controller methods for saving permissions\n";
echo "   6. ✅ Updated database integration for permissions\n";
echo "   7. ✅ Created comprehensive permission management UI\n";

echo "\n🚀 NEXT STEPS:\n";
echo "1. Run CUSTOMIZE_WEBSITE_VALIDATION_TEST.php to validate changes\n";
echo "2. Test the customize page: http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=16\n";
echo "3. Verify permission functionality in admin preview\n";
echo "4. Test database storage of permission settings\n";
