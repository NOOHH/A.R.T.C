<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (!Schema::hasTable('board_passers')) {
        Schema::create('board_passers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('board_exam', 50);
            $table->integer('exam_year');
            $table->date('exam_date')->nullable();
            $table->enum('result', ['PASS', 'FAIL']);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['student_id', 'board_exam', 'exam_year']);
            $table->index('result');
            $table->index('exam_year');
        });
        echo "Board passers table created successfully!\n";
    } else {
        echo "Board passers table already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
