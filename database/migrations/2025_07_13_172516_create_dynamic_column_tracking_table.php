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
        Schema::create('dynamic_column_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('column_name')->unique();
            $table->string('column_type')->default('varchar(255)');
            $table->boolean('is_active')->default(true);
            $table->boolean('has_data_in_registrations')->default(false);
            $table->boolean('has_data_in_students')->default(false);
            $table->timestamp('first_created_at')->nullable();
            $table->timestamp('last_removed_at')->nullable();
            $table->json('historical_data')->nullable(); // Store backup data when column is removed
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
        Schema::dropIfExists('dynamic_column_tracking');
    }
};
