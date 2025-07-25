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
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('modules_id');
            $table->string('module_name');
            $table->text('module_description')->nullable();
            $table->integer('program_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->enum('learning_mode', ['Synchronous', 'Asynchronous'])->default('Synchronous');
            $table->string('content_type', 50)->default('');
            $table->text('content_data')->nullable();
            $table->integer('plan_id')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->string('video_path', 255)->nullable();
            $table->longText('additional_content')->nullable();
            $table->timestamps();
            $table->boolean('is_archived')->default(false);
            $table->integer('order')->default(0);
            $table->longText('admin_override')->nullable()->comment('overrides set by admins (stored as JSON)');
            $table->unsignedInteger('module_order')->default(0);
            $table->index('program_id', 'idx_modules_program');
            $table->index('plan_id', 'idx_modules_plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
};
