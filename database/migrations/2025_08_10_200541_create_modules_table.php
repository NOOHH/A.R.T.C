<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->integer('modules_id')->nullable(false)->primary();
            $table->string('255')('module_name')->nullable(false);
            $table->text('module_description');
            $table->integer('program_id')->nullable(false);
            $table->bigInteger('batch_id');
            $table->enum(''synchronous','asynchronous'')('learning_mode')->nullable(false)->default('Synchronous');
            $table->string('50')('content_type')->nullable(false)->default('');
            $table->text('content_data');
            $table->integer('plan_id');
            $table->string('255')('attachment');
            $table->string('255')('video_path');
            $table->text('content_url');
            $table->text('additional_content');
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            $table->boolean('is_archived')->default(0);
            $table->integer('order')->nullable(false)->default(0);
            $table->text('admin_override');
            $table->boolean('is_locked')->nullable(false)->default(0);
            $table->boolean('requires_prerequisite')->nullable(false)->default(0);
            $table->bigInteger('prerequisite_module_id');
            $table->timestamp('release_date');
            $table->text('completion_criteria');
            $table->string('255')('lock_reason');
            $table->bigInteger('locked_by');
            $table->integer('module_order')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
