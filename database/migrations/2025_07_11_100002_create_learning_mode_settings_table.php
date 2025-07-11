<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_mode_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('plan_type', ['Full Plan', 'Modular Plan'])->unique();
            $table->boolean('enable_synchronous')->default(true);
            $table->boolean('enable_asynchronous')->default(true);
            $table->json('additional_config')->nullable();
            $table->timestamps();
        });

        // Insert default settings for both plan types
        DB::table('learning_mode_settings')->insert([
            [
                'plan_type' => 'Full Plan',
                'enable_synchronous' => true,
                'enable_asynchronous' => true,
                'additional_config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_type' => 'Modular Plan',
                'enable_synchronous' => true,
                'enable_asynchronous' => true,
                'additional_config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_mode_settings');
    }
};
