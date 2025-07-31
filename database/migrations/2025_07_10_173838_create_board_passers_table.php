<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('board_passers')) {
            Schema::create('board_passers', function (Blueprint $table) {
                $table->id();
                $table->string('student_id')->nullable();
                $table->string('student_name');
                $table->string('program')->nullable();
                $table->string('board_exam');
                $table->integer('exam_year');
                $table->date('exam_date')->nullable();
                $table->enum('result', ['PASS', 'FAIL', 'PENDING'])->default('PENDING');
                $table->decimal('rating', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['student_id', 'board_exam', 'exam_year']);
                $table->index(['result', 'exam_year']);
                $table->index('program');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('board_passers');
    }
};
