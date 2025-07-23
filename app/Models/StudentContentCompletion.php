<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentContentCompletion extends Model
{
    use HasFactory;

    protected $table = 'student_content_completions';
    protected $fillable = [
        'student_id',
        'content_id',
        'completed_at',
    ];
    public $timestamps = true;
} 