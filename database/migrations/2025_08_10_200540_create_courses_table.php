<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigInteger('subject_id')->nullable(false)->primary();
            $table->string('255')('subject_name')->nullable(false);
            $table->text('subject_description');
            $table->bigInteger('module_id')->nullable(false);
            $table->decimal('10', '2')('subject_price')->nullable(false)->default(0.00);
            $table->integer('subject_order')->nullable(false)->default(0);
            $table->integer('course_order')->nullable(false)->default(0);
            $table->boolean('is_required')->nullable(false)->default(0);
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->boolean('is_archived')->nullable(false)->default(0);
            $table->text('admin_override');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->boolean('is_locked')->nullable(false)->default(0);
            $table->boolean('requires_prerequisite')->nullable(false)->default(0);
            $table->bigInteger('prerequisite_course_id');
            $table->timestamp('release_date');
            $table->text('completion_criteria');
            $table->string('255')('lock_reason');
            $table->bigInteger('locked_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
