<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AdminOverride;

class AdminOverrideController extends Controller
{
    /**
     * Get current override status for an item
     */
    public function getStatus($type, $id)
    {
        try {
            $table = $this->getTableForType($type);
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }
            
            $item = DB::table($table)->where($this->getIdField($type), $id)->first();
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found']);
            }
            
            $status = [
                'is_locked' => isset($item->is_locked) ? $item->is_locked : false,
                'lock_reason' => isset($item->lock_reason) ? $item->lock_reason : null,
                'release_date' => isset($item->release_date) ? $item->release_date : null,
                'prerequisite_name' => null
            ];
            
            // Get prerequisite name if exists
            if (isset($item->requires_prerequisite) && $item->requires_prerequisite) {
                $prereqTable = null;
                $prereqId = null;
                
                if (isset($item->prerequisite_module_id) && $item->prerequisite_module_id) {
                    $prereqTable = 'modules';
                    $prereqId = $item->prerequisite_module_id;
                } elseif (isset($item->prerequisite_course_id) && $item->prerequisite_course_id) {
                    $prereqTable = 'courses';
                    $prereqId = $item->prerequisite_course_id;
                } elseif (isset($item->prerequisite_content_id) && $item->prerequisite_content_id) {
                    $prereqTable = 'content_items';
                    $prereqId = $item->prerequisite_content_id;
                }
                
                if ($prereqTable && $prereqId) {
                    $prereq = DB::table($prereqTable)->find($prereqId);
                    if ($prereq) {
                        $status['prerequisite_name'] = $this->getItemName($prereq, $prereqTable);
                    }
                }
            }
            
            return response()->json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Get list of available prerequisites
     */
    public function getPrerequisites()
    {
        try {
            $prerequisites = [];
            
            // Get modules
            $modules = DB::table('modules')->select('modules_id as id', 'module_name as name')->get();
            foreach ($modules as $module) {
                $prerequisites[] = [
                    'type' => 'module',
                    'id' => $module->id,
                    'name' => $module->name
                ];
            }
            
            // Get courses
            $courses = DB::table('courses')->select('subject_id as id', 'subject_name as name')->get();
            foreach ($courses as $course) {
                $prerequisites[] = [
                    'type' => 'course',
                    'id' => $course->id,
                    'name' => $course->name
                ];
            }
            
            // Get content items
            $content = DB::table('content_items')->select('id', 'content_title as name')->get();
            foreach ($content as $item) {
                $prerequisites[] = [
                    'type' => 'content',
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            
            return response()->json(['success' => true, 'prerequisites' => $prerequisites]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Clear release schedule for an item
     */
    public function clearSchedule(Request $request)
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');
            
            $table = $this->getTableForType($type);
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }
            
            DB::table($table)
                ->where($this->getIdField($type), $id)
                ->update(['release_date' => null]);
            
            return response()->json(['success' => true, 'message' => 'Release schedule cleared']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Toggle lock status for an item
     */
    public function toggleLock(Request $request)
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');
            $lockReason = $request->input('lock_reason');
            
            $table = $this->getTableForType($type);
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }
            
            $item = DB::table($table)->where($this->getIdField($type), $id)->first();
            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found']);
            }
            
            $newLockStatus = !($item->is_locked ?? false);
            
            DB::table($table)
                ->where($this->getIdField($type), $id)
                ->update([
                    'is_locked' => $newLockStatus,
                    'lock_reason' => $newLockStatus ? $lockReason : null,
                    'locked_by' => $newLockStatus ? auth()->id() : null
                ]);
            
            $message = $newLockStatus ? 'Item locked successfully' : 'Item unlocked successfully';
            return response()->json(['success' => true, 'message' => $message, 'is_locked' => $newLockStatus]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Set release schedule for an item
     */
    public function setSchedule(Request $request)
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');
            $releaseDate = $request->input('release_date');
            
            $table = $this->getTableForType($type);
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }
            
            DB::table($table)
                ->where($this->getIdField($type), $id)
                ->update(['release_date' => $releaseDate]);
            
            return response()->json(['success' => true, 'message' => 'Release schedule set successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Set prerequisite for an item
     */
    public function setPrerequisite(Request $request)
    {
        try {
            $type = $request->input('type');
            $id = $request->input('id');
            $prerequisite = $request->input('prerequisite');
            
            $table = $this->getTableForType($type);
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid type']);
            }
            
            $updateData = [
                'requires_prerequisite' => !empty($prerequisite),
                'prerequisite_module_id' => null,
                'prerequisite_course_id' => null,
                'prerequisite_content_id' => null
            ];
            
            if ($prerequisite) {
                [$prereqType, $prereqId] = explode('_', $prerequisite, 2);
                
                switch ($prereqType) {
                    case 'module':
                        $updateData['prerequisite_module_id'] = $prereqId;
                        break;
                    case 'course':
                        $updateData['prerequisite_course_id'] = $prereqId;
                        break;
                    case 'content':
                        $updateData['prerequisite_content_id'] = $prereqId;
                        break;
                }
            }
            
            DB::table($table)
                ->where($this->getIdField($type), $id)
                ->update($updateData);
            
            return response()->json(['success' => true, 'message' => 'Prerequisite updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Bulk lock items
     */
    public function bulkLock(Request $request)
    {
        try {
            $items = $request->input('items', []);
            $lockReason = $request->input('lock_reason');
            
            foreach ($items as $item) {
                $table = $this->getTableForType($item['type']);
                if ($table) {
                    DB::table($table)
                        ->where($this->getIdField($item['type']), $item['id'])
                        ->update([
                            'is_locked' => true,
                            'lock_reason' => $lockReason,
                            'locked_by' => auth()->id()
                        ]);
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Items locked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Bulk unlock items
     */
    public function bulkUnlock(Request $request)
    {
        try {
            $items = $request->input('items', []);
            
            foreach ($items as $item) {
                $table = $this->getTableForType($item['type']);
                if ($table) {
                    DB::table($table)
                        ->where($this->getIdField($item['type']), $item['id'])
                        ->update([
                            'is_locked' => false,
                            'lock_reason' => null,
                            'locked_by' => null
                        ]);
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Items unlocked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get overrides for specific program
     */
    public function getItemOverrides($programId)
    {
        try {
            $overrides = [];
            
            // Get module overrides
            $modules = DB::table('modules')
                ->where('program_id', $programId)
                ->whereNotNull('is_locked')
                ->select('modules_id as id', 'module_name as name', 'is_locked', 'lock_reason')
                ->get();
            
            foreach ($modules as $module) {
                $overrides[] = [
                    'type' => 'module',
                    'id' => $module->id,
                    'name' => $module->name,
                    'is_locked' => $module->is_locked,
                    'lock_reason' => $module->lock_reason
                ];
            }
            
            // Get course overrides
            $courses = DB::table('courses')
                ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
                ->where('modules.program_id', $programId)
                ->whereNotNull('courses.is_locked')
                ->select('courses.subject_id as id', 'courses.subject_name as name', 'courses.is_locked', 'courses.lock_reason')
                ->get();
            
            foreach ($courses as $course) {
                $overrides[] = [
                    'type' => 'course',
                    'id' => $course->id,
                    'name' => $course->name,
                    'is_locked' => $course->is_locked,
                    'lock_reason' => $course->lock_reason
                ];
            }
            
            // Get content overrides
            $content = DB::table('content_items')
                ->join('courses', 'content_items.course_id', '=', 'courses.subject_id')
                ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
                ->where('modules.program_id', $programId)
                ->whereNotNull('content_items.is_locked')
                ->select('content_items.id as id', 'content_items.content_title as name', 'content_items.is_locked', 'content_items.lock_reason')
                ->get();
            
            foreach ($content as $item) {
                $overrides[] = [
                    'type' => 'content',
                    'id' => $item->id,
                    'name' => $item->name,
                    'is_locked' => $item->is_locked,
                    'lock_reason' => $item->lock_reason
                ];
            }
            
            return response()->json(['success' => true, 'overrides' => $overrides]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Helper method to get table name for type
     */
    private function getTableForType($type)
    {
        switch ($type) {
            case 'module':
                return 'modules';
            case 'course':
                return 'courses';
            case 'content':
                return 'content_items';
            default:
                return null;
        }
    }

    /**
     * Helper method to get ID field for type
     */
    private function getIdField($type)
    {
        switch ($type) {
            case 'module':
                return 'modules_id'; // Fixed: modules table uses modules_id
            case 'course':
                return 'subject_id'; // Fixed: courses table uses subject_id
            case 'content':
                return 'id'; // Fixed: content_items table uses id
            default:
                return 'id';
        }
    }
    
    /**
     * Helper method to get item name
     */
    private function getItemName($item, $table)
    {
        switch ($table) {
            case 'modules':
                return $item->module_name;
            case 'courses':
                return $item->subject_name; // Fixed: courses table uses subject_name
            case 'content_items':
                return $item->content_title; // Fixed: content_items table uses content_title
            default:
                return 'Unknown';
        }
    }
}
