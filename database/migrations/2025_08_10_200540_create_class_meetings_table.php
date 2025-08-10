<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassmeetingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('class_meetings', function (Blueprint $table) {
            $table->bigInteger('meeting_id')->nullable(false)->primary();
            $table->bigInteger('batch_id')->nullable(false);
            $table->integer('professor_id')->nullable(false);
            $table->string('255')('title')->nullable(false);
            $table->text('description');
            $table->dateTime('meeting_date')->nullable(false);
            $table->integer('duration_minutes')->nullable(false)->default(60);
            $table->string('255')('meeting_url');
            $table->text('attached_files');
            $table->enum(''scheduled','ongoing','completed','cancelled'')('status')->nullable(false)->default('scheduled');
            $table->integer('created_by')->nullable(false);
            $table->boolean('url_visible_before_meeting')->nullable(false)->default(0);
            $table->integer('url_visibility_minutes_before')->nullable(false)->default(0);
            $table->dateTime('actual_start_time');
            $table->dateTime('actual_end_time');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('class_meetings');
    }
}
