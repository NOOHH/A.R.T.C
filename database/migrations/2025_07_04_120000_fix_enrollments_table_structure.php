<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, check if we need to rename the table from 'enrollment' to 'enrollments'
        if (Schema::hasTable('enrollment') && !Schema::hasTable('enrollments')) {
            DB::statement('RENAME TABLE enrollment TO enrollments');
        }
        
        // If enrollments table doesn't exist, create it
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->increments('enrollment_id');
                $table->string('student_id')->nullable();
                $table->unsignedInteger('program_id')->nullable();
                $table->unsignedInteger('package_id')->nullable();
                $table->string('enrollment_type')->default('full');
                $table->string('Modular_enrollment', 50)->nullable();
                $table->string('Complete_Program', 50)->nullable();
                $table->timestamps();
            });
        } else {
            // Add missing columns to existing enrollments table
            if (!Schema::hasColumn('enrollments', 'student_id')) {
                Schema::table('enrollments', function (Blueprint $table) {
                    $table->string('student_id')->nullable()->after('enrollment_id');
                });
            }
            if (!Schema::hasColumn('enrollments', 'program_id')) {
                Schema::table('enrollments', function (Blueprint $table) {
                    $table->unsignedInteger('program_id')->nullable();
                });
            }
            if (!Schema::hasColumn('enrollments', 'enrollment_type')) {
                Schema::table('enrollments', function (Blueprint $table) {
                    $table->string('enrollment_type')->default('full');
                });
            }
            if (!Schema::hasColumn('enrollments', 'created_at')) {
                Schema::table('enrollments', function (Blueprint $table) {
                    $table->timestamps();
                });
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};
