<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';
    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'item_type',
        'item_id',
        'is_completed',
        'completed_at',
        'progress_percentage',
        'completion_data'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'completion_data' => 'array'
    ];

    const ITEM_TYPES = [
        'module' => 'module',
        'course' => 'course',
        'content' => 'content'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    // Helper methods
    public function markCompleted($completionData = null)
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'progress_percentage' => 100,
            'completion_data' => $completionData
        ]);
    }

    public function updateProgress($percentage, $completionData = null)
    {
        $this->update([
            'progress_percentage' => $percentage,
            'completion_data' => $completionData,
            'is_completed' => $percentage >= 100,
            'completed_at' => $percentage >= 100 ? now() : null
        ]);
    }

    // Static helper methods
    public static function getProgress($studentId, $itemType, $itemId)
    {
        return static::where('student_id', $studentId)
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->first();
    }

    public static function isCompleted($studentId, $itemType, $itemId)
    {
        return static::where('student_id', $studentId)
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->where('is_completed', true)
            ->exists();
    }

    public static function markItemCompleted($studentId, $itemType, $itemId, $completionData = null)
    {
        return static::updateOrCreate(
            [
                'student_id' => $studentId,
                'item_type' => $itemType,
                'item_id' => $itemId
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'progress_percentage' => 100,
                'completion_data' => $completionData
            ]
        );
    }

    public static function updateItemProgress($studentId, $itemType, $itemId, $percentage, $completionData = null)
    {
        return static::updateOrCreate(
            [
                'student_id' => $studentId,
                'item_type' => $itemType,
                'item_id' => $itemId
            ],
            [
                'progress_percentage' => $percentage,
                'completion_data' => $completionData,
                'is_completed' => $percentage >= 100,
                'completed_at' => $percentage >= 100 ? now() : null
            ]
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;
}
