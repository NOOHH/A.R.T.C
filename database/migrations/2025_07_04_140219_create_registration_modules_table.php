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
        Schema::create('registration_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id');
            $table->unsignedBigInteger('module_id');
            $table->timestamps();
            
            $table->unique(['registration_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_modules');
    }
};
