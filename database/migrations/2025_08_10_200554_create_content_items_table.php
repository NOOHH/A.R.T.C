<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentitemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('content_items', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('content_title')->nullable(false);
            $table->text('content_description');
            $table->bigInteger('lesson_id');
            $table->bigInteger('course_id');
            $table->enum(''assignment','quiz','test','link','video','document','lesson'')('content_type');
            $table->bigInteger('created_by_professor_id');
            $table->text('content_data');
            $table->string('255')('content_url');
            $table->string('255')('attachment_path');
            $table->decimal('8', '2')('max_points');
            $table->dateTime('due_date');
            $table->integer('time_limit');
            $table->integer('content_order')->nullable(false)->default(0);
            $table->integer('sort_order')->nullable(false)->default(0);
            $table->boolean('enable_submission')->nullable(false)->default(0);
            $table->string('255')('allowed_file_types');
            $table->integer('max_file_size');
            $table->text('submission_instructions');
            $table->boolean('allow_multiple_submissions')->nullable(false)->default(0);
            $table->integer('order')->nullable(false)->default(0);
            $table->boolean('is_required')->nullable(false)->default(1);
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->boolean('is_archived')->nullable(false)->default(0);
            $table->timestamp('archived_at');
            $table->bigInteger('archived_by_professor_id');
            $table->text('admin_override');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->boolean('is_locked')->nullable(false)->default(0);
            $table->boolean('requires_prerequisite')->nullable(false)->default(0);
            $table->bigInteger('prerequisite_content_id');
            $table->timestamp('release_date');
            $table->text('completion_criteria');
            $table->string('255')('lock_reason');
            $table->bigInteger('locked_by');
            $table->string('255')('file_path');
            $table->string('255')('file_name');
            $table->bigInteger('file_size');
            $table->string('255')('file_mime');
            $table->boolean('has_multiple_files')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('content_items');
    }
}
