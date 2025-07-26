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
        Schema::table('student_grades', function (Blueprint $table) {
            // Add indexes for better performance
            if (!$this->hasIndex('student_grades', 'student_grades_student_id_program_id_index')) {
                $table->index(['student_id', 'program_id']);
            }
            
            if (!$this->hasIndex('student_grades', 'student_grades_grade_type_reference_id_index')) {
                $table->index(['grade_type', 'reference_id']);
            }
            
            if (!$this->hasIndex('student_grades', 'student_grades_graded_at_index')) {
                $table->index('graded_at');
            }
            
            // Add new fields for enhanced grading
            if (!Schema::hasColumn('student_grades', 'graded_by')) {
                $table->unsignedInteger('graded_by')->nullable()->after('feedback');
                $table->foreign('graded_by')->references('professor_id')->on('professors')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('student_grades', 'reference_name')) {
                $table->string('reference_name')->nullable()->after('assignment_title');
            }
            
            if (!Schema::hasColumn('student_grades', 'grade_type')) {
                $table->string('grade_type')->default('manual')->after('program_id');
            }
            
            if (!Schema::hasColumn('student_grades', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('grade_type');
            }
            
            if (!Schema::hasColumn('student_grades', 'max_points')) {
                $table->decimal('max_points', 8, 2)->nullable()->after('max_score');
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
        Schema::table('student_grades', function (Blueprint $table) {
            // Remove foreign key and column
            if (Schema::hasColumn('student_grades', 'graded_by')) {
                $table->dropForeign(['graded_by']);
                $table->dropColumn('graded_by');
            }
            
            // Remove added columns
            if (Schema::hasColumn('student_grades', 'reference_name')) {
                $table->dropColumn('reference_name');
            }
            
            if (Schema::hasColumn('student_grades', 'grade_type')) {
                $table->dropColumn('grade_type');
            }
            
            if (Schema::hasColumn('student_grades', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
            
            if (Schema::hasColumn('student_grades', 'max_points')) {
                $table->dropColumn('max_points');
            }
        });
    }
    
    /**
     * Check if an index exists on a table
     */
    private function hasIndex($table, $indexName)
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
        return array_key_exists($indexName, $indexes);
    }
};
