<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigInteger('announcement_id')->nullable(false)->primary();
            $table->bigInteger('admin_id');
            $table->bigInteger('professor_id');
            $table->bigInteger('program_id');
            $table->string('255')('title')->nullable(false);
            $table->text('content')->nullable(false);
            $table->text('description');
            $table->timestamp('publish_date');
            $table->timestamp('expire_date');
            $table->boolean('is_published')->nullable(false)->default(1);
            $table->enum(''general','urgent','event','system','video','assignment','quiz'')('type')->default('general');
            $table->text('target_users');
            $table->text('target_programs');
            $table->text('target_batches');
            $table->text('target_plans');
            $table->enum(''all','specific'')('target_scope')->nullable(false)->default('all');
            $table->string('255')('video_link');
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}
