<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->integer('program_id')->nullable(false)->primary();
            $table->string('100')('program_name')->nullable(false);
            $table->integer('created_by_admin_id')->nullable(false);
            $table->integer('director_id');
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            $table->integer('is_active')->nullable(false);
            $table->boolean('is_archived')->default(0);
            $table->text('program_description');
            $table->string('255')('program_image');
            $table->text('admin_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
}
