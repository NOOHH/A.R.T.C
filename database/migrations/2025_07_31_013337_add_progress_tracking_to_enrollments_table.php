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
        Schema::table('enrollments', function (Blueprint $table) {
            // Add progress tracking columns if they don't exist
            if (!Schema::hasColumn('enrollments', 'progress_percentage')) {
                $table->decimal('progress_percentage', 5, 2)->default(0)->after('enrollment_status');
            }
            if (!Schema::hasColumn('enrollments', 'completion_date')) {
                $table->timestamp('completion_date')->nullable()->after('progress_percentage');
            }
            if (!Schema::hasColumn('enrollments', 'start_date')) {
                $table->timestamp('start_date')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('enrollments', 'last_activity')) {
                $table->timestamp('last_activity')->nullable()->after('completion_date');
            }
            if (!Schema::hasColumn('enrollments', 'total_modules')) {
                $table->integer('total_modules')->default(0)->after('last_activity');
            }
            if (!Schema::hasColumn('enrollments', 'completed_modules')) {
                $table->integer('completed_modules')->default(0)->after('total_modules');
            }
            if (!Schema::hasColumn('enrollments', 'total_courses')) {
                $table->integer('total_courses')->default(0)->after('completed_modules');
            }
            if (!Schema::hasColumn('enrollments', 'completed_courses')) {
                $table->integer('completed_courses')->default(0)->after('total_courses');
            }
            if (!Schema::hasColumn('enrollments', 'certificate_eligible')) {
                $table->boolean('certificate_eligible')->default(false)->after('completed_courses');
            }
            if (!Schema::hasColumn('enrollments', 'certificate_requested')) {
                $table->boolean('certificate_requested')->default(false)->after('certificate_eligible');
            }
            if (!Schema::hasColumn('enrollments', 'certificate_issued')) {
                $table->boolean('certificate_issued')->default(false)->after('certificate_requested');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'progress_percentage',
                'completion_date', 
                'start_date',
                'last_activity',
                'total_modules',
                'completed_modules',
                'total_courses',
                'completed_courses',
                'certificate_eligible',
                'certificate_requested',
                'certificate_issued'
            ]);
        });
    }
};
