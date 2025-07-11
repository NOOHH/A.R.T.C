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
        Schema::table('plan', function (Blueprint $table) {
            // Add learning mode configuration fields for Full Plan vs Modular Plan
            $table->boolean('enable_synchronous')->default(true)->after('description');
            $table->boolean('enable_asynchronous')->default(true)->after('enable_synchronous');
            $table->json('learning_mode_config')->nullable()->after('enable_asynchronous');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->dropColumn(['enable_synchronous', 'enable_asynchronous', 'learning_mode_config']);
        });
    }
};
