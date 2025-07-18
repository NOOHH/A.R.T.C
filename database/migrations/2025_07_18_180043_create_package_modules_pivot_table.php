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
        Schema::create('package_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('modules_id'); // Use the actual module primary key name
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate associations
            $table->unique(['package_id', 'modules_id']);
            
            // Add indexes for better performance
            $table->index('package_id');
            $table->index('modules_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_modules');
    }
};
