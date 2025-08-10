<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationlevelsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('education_levels', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('level_name')->nullable(false);
            $table->text('file_requirements');
            $table->boolean('available_for_general')->nullable(false)->default(1);
            $table->boolean('available_for_professional')->nullable(false)->default(1);
            $table->boolean('available_for_review')->nullable(false)->default(1);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->integer('level_order')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('education_levels');
    }
}
