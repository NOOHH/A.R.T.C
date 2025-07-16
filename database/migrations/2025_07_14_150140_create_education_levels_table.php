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
        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('level_name');
            $table->text('level_description')->nullable();
            $table->integer('level_order')->default(1);
            $table->boolean('available_full_plan')->default(true);
            $table->boolean('available_modular_plan')->default(true);
            $table->json('file_requirements')->nullable(); // Store file requirements as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('education_levels');
    }
};
