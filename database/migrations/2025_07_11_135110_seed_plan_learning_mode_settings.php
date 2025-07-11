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
        // Update existing plan records with initial learning mode settings
        DB::table('plan')->where('plan_id', 1)->update([
            'enable_synchronous' => true,
            'enable_asynchronous' => true,
            'learning_mode_config' => json_encode([
                'synchronous_auto_start_days' => 14,
                'asynchronous_manual_start' => true
            ])
        ]);

        DB::table('plan')->where('plan_id', 2)->update([
            'enable_synchronous' => true,
            'enable_asynchronous' => true,
            'learning_mode_config' => json_encode([
                'synchronous_auto_start_days' => 14,
                'asynchronous_manual_start' => true
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reset learning mode settings
        DB::table('plan')->whereIn('plan_id', [1, 2])->update([
            'enable_synchronous' => true,
            'enable_asynchronous' => true,
            'learning_mode_config' => null
        ]);
    }
};
