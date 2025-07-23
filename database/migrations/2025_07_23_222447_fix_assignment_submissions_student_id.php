<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['student_id']);
            
            // Change student_id to string to match students table
            $table->string('student_id')->change();
            
            // Re-add foreign key constraint
            $table->foreign('student_id')->references('student_id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['student_id']);
            
            // Change back to bigint
            $table->unsignedBigInteger('student_id')->change();
            
            // Re-add foreign key constraint
            $table->foreign('student_id')->references('student_id')->on('students');
        });
    }
};
