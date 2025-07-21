<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminOverride extends Model
{
    /**
     * Since we're not using a separate admin_overrides table,
     * this model provides static methods to work with override
     * columns in existing tables (modules, courses, content_items)
     */
    public $timestamps = false;

    /**
     * Check if an item is accessible to a student
     */
    public static function isItemAccessible($itemType, $itemId, $studentId = null)
    {
        try {
            $table = self::getTableName($itemType);
            $primaryKey = self::getPrimaryKey($itemType);
            
            $item = DB::table($table)->where($primaryKey, $itemId)->first();
            
            if (!$item) {
                return false;
            }
            
            // Check if item is locked
            if (isset($item->is_locked) && $item->is_locked) {
                return false;
            }
            
            // Check release date
            if (isset($item->release_date) && $item->release_date && now()->lt($item->release_date)) {
                return false;
            }
            
            // Check prerequisites
            if (isset($item->requires_prerequisite) && $item->requires_prerequisite && $studentId) {
                $prerequisiteColumn = self::getPrerequisiteColumn($itemType);
                if (isset($item->$prerequisiteColumn) && $item->$prerequisiteColumn) {
                    if (!self::isPrerequisiteCompleted($itemType, $item->$prerequisiteColumn, $studentId)) {
                        return false;
                    }
                }
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error checking item accessibility: ' . $e->getMessage());
            return true; // Default to accessible if error
        }
    }

    /**
     * Get lock reason for a student
     */
    public static function getLockReasonForStudent($itemType, $itemId, $studentId = null)
    {
        try {
            $table = self::getTableName($itemType);
            $primaryKey = self::getPrimaryKey($itemType);
            
            $item = DB::table($table)->where($primaryKey, $itemId)->first();
            
            if (!$item) {
                return 'Item not found';
            }
            
            // Check if manually locked
            if (isset($item->is_locked) && $item->is_locked) {
                return $item->lock_reason ?? 'This item is currently locked';
            }
            
            // Check release date
            if (isset($item->release_date) && $item->release_date && now()->lt($item->release_date)) {
                return 'Available on ' . date('M j, Y \a\t g:i A', strtotime($item->release_date));
            }
            
            // Check prerequisites
            if (isset($item->requires_prerequisite) && $item->requires_prerequisite && $studentId) {
                $prerequisiteColumn = self::getPrerequisiteColumn($itemType);
                if (isset($item->$prerequisiteColumn) && $item->$prerequisiteColumn) {
                    if (!self::isPrerequisiteCompleted($itemType, $item->$prerequisiteColumn, $studentId)) {
                        $prerequisiteName = self::getPrerequisiteName($itemType, $item->$prerequisiteColumn);
                        return "Complete {$prerequisiteName} first";
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting lock reason: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Toggle lock status for an item
     */
    public static function toggleLock($itemType, $itemId, $adminId, $reason = null)
    {
        try {
            $table = self::getTableName($itemType);
            $primaryKey = self::getPrimaryKey($itemType);
            
            $item = DB::table($table)->where($primaryKey, $itemId)->first();
            
            if (!$item) {
                return false;
            }
            
            $newLockStatus = !($item->is_locked ?? false);
            
            DB::table($table)
                ->where($primaryKey, $itemId)
                ->update([
                    'is_locked' => $newLockStatus,
                    'lock_reason' => $newLockStatus ? $reason : null,
                    'locked_by' => $newLockStatus ? $adminId : null,
                    'updated_at' => now()
                ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error toggling lock: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set prerequisite for an item
     */
    public static function setPrerequisite($itemType, $itemId, $prerequisiteId = null)
    {
        try {
            $table = self::getTableName($itemType);
            $primaryKey = self::getPrimaryKey($itemType);
            $prerequisiteColumn = self::getPrerequisiteColumn($itemType);
            
            DB::table($table)
                ->where($primaryKey, $itemId)
                ->update([
                    'requires_prerequisite' => $prerequisiteId ? true : false,
                    $prerequisiteColumn => $prerequisiteId,
                    'updated_at' => now()
                ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error setting prerequisite: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set release date for an item
     */
    public static function setSchedule($itemType, $itemId, $releaseDate = null)
    {
        try {
            $table = self::getTableName($itemType);
            $primaryKey = self::getPrimaryKey($itemType);
            
            DB::table($table)
                ->where($primaryKey, $itemId)
                ->update([
                    'release_date' => $releaseDate,
                    'updated_at' => now()
                ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error setting schedule: ' . $e->getMessage());
            return false;
        }
    }

    // Helper methods
    private static function getTableName($itemType)
    {
        switch ($itemType) {
            case 'module':
                return 'modules';
            case 'course':
                return 'courses';
            case 'content':
                return 'content_items';
            default:
                throw new \InvalidArgumentException("Invalid item type: {$itemType}");
        }
    }

    private static function getPrimaryKey($itemType)
    {
        switch ($itemType) {
            case 'module':
                return 'modules_id';
            case 'course':
                return 'subject_id';
            case 'content':
                return 'id';
            default:
                throw new \InvalidArgumentException("Invalid item type: {$itemType}");
        }
    }

    private static function getPrerequisiteColumn($itemType)
    {
        switch ($itemType) {
            case 'module':
                return 'prerequisite_module_id';
            case 'course':
                return 'prerequisite_course_id';
            case 'content':
                return 'prerequisite_content_id';
            default:
                throw new \InvalidArgumentException("Invalid item type: {$itemType}");
        }
    }

    private static function isPrerequisiteCompleted($itemType, $prerequisiteId, $studentId)
    {
        // Check if the prerequisite item is completed
        // This would check against progress/completion tables
        // For now, return true as a placeholder
        return true;
    }

    private static function getPrerequisiteName($itemType, $prerequisiteId)
    {
        try {
            $table = self::getTableName($itemType);
            $primaryKey = self::getPrimaryKey($itemType);
            
            $item = DB::table($table)->where($primaryKey, $prerequisiteId)->first();
            
            switch ($itemType) {
                case 'module':
                    return $item->module_name ?? "Module {$prerequisiteId}";
                case 'course':
                    return $item->subject_name ?? "Course {$prerequisiteId}";
                case 'content':
                    return $item->content_title ?? "Content {$prerequisiteId}";
                default:
                    return "Item {$prerequisiteId}";
            }
        } catch (\Exception $e) {
            return "Item {$prerequisiteId}";
        }
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminOverride extends Model
{
    use HasFactory;
}
