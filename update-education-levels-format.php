<?php

/**
 * Update Education Levels to New Document Format
 * 
 * This script converts the old format education levels to the new 
 * document type format that uses proper document_type fields
 * instead of hardcoded file upload names.
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 Updating Education Levels to New Document Format...\n\n";

try {
    // Get current education levels
    $educationLevels = DB::table('education_levels')->get();
    
    if ($educationLevels->isEmpty()) {
        echo "❌ No education levels found in database.\n";
        exit(1);
    }
    
    foreach ($educationLevels as $level) {
        echo "📝 Processing: {$level->level_name}\n";
        
        $oldRequirements = json_decode($level->file_requirements, true) ?? [];
        $newRequirements = [];
        
        foreach ($oldRequirements as $fieldName => $config) {
            // Map old field names to new document types
            $documentType = 'custom';
            $customName = null;
            
            // Convert old hardcoded names to new document types
            if (stripos($fieldName, 'School ID') !== false) {
                $documentType = 'school_id';
            } elseif (stripos($fieldName, 'PSA') !== false || stripos($fieldName, 'birth certificate') !== false) {
                $documentType = 'PSA';
            } elseif (stripos($fieldName, 'TOR') !== false || stripos($fieldName, 'transcript') !== false) {
                $documentType = 'TOR';
            } elseif (stripos($fieldName, 'Good Moral') !== false || stripos($fieldName, 'moral') !== false) {
                $documentType = 'good_moral';
            } elseif (stripos($fieldName, 'Diploma') !== false) {
                $documentType = 'diploma';
            } elseif (stripos($fieldName, 'Certificate of Graduation') !== false || stripos($fieldName, 'graduation') !== false) {
                $documentType = 'Cert_of_Grad';
            } elseif (stripos($fieldName, 'Course Cert') !== false || stripos($fieldName, 'course certificate') !== false) {
                $documentType = 'Course_Cert';
            } elseif (stripos($fieldName, 'Photo') !== false || stripos($fieldName, '2x2') !== false) {
                $documentType = 'photo_2x2';
            } else {
                // Keep as custom with original name
                $documentType = 'custom';
                $customName = $fieldName;
            }
            
            // Convert file type
            $fileType = 'any';
            if (isset($config['type'])) {
                switch ($config['type']) {
                    case 'image':
                        $fileType = 'image';
                        break;
                    case 'pdf':
                        $fileType = 'pdf';
                        break;
                    case 'document':
                        $fileType = 'document';
                        break;
                    default:
                        $fileType = 'any';
                }
            }
            
            // Create new requirement format
            $newRequirement = [
                'field_name' => $documentType === 'custom' ? $customName : $documentType,
                'document_type' => $documentType,
                'file_type' => $fileType,
                'custom_name' => $customName,
                'is_required' => $config['required'] ?? true,
                'available_full_plan' => true,
                'available_modular_plan' => true,
                'description' => $config['description'] ?? ''
            ];
            
            $newRequirements[] = $newRequirement;
            
            echo "  ✅ Converted: '{$fieldName}' → '{$documentType}' ({$fileType})\n";
        }
        
        // Update the education level with new format
        DB::table('education_levels')
            ->where('id', $level->id)
            ->update([
                'file_requirements' => json_encode($newRequirements),
                'updated_at' => now()
            ]);
        
        echo "  🎉 Updated {$level->level_name} with " . count($newRequirements) . " requirements\n\n";
    }
    
    echo "✅ All education levels updated successfully!\n\n";
    
    // Show the updated education levels
    echo "📋 Updated Education Levels:\n";
    echo "=" . str_repeat("=", 50) . "\n";
    
    $updatedLevels = DB::table('education_levels')->get();
    foreach ($updatedLevels as $level) {
        echo "\n🎓 {$level->level_name}:\n";
        $requirements = json_decode($level->file_requirements, true) ?? [];
        
        foreach ($requirements as $req) {
            $displayName = $req['custom_name'] ?: $req['document_type'];
            $required = $req['is_required'] ? '[REQUIRED]' : '[OPTIONAL]';
            $fileType = strtoupper($req['file_type']);
            echo "  • {$displayName} ({$fileType}) {$required}\n";
        }
    }
    
    echo "\n🎉 Education levels are now using the modern document type format!\n";
    echo "   The admin interface will now show clean document type badges.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
