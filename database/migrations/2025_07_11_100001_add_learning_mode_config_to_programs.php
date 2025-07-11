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
        Schema::table('programs', function (Blueprint $table) {
            // Add learning mode configuration fields
            $table->boolean('enable_synchronous')->default(true)->after('program_description');
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
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['enable_synchronous', 'enable_asynchronous', 'learning_mode_config']);
        });
    }
};
