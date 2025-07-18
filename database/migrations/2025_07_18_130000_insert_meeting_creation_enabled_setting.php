<?php

use Illuminate\Database\Migrations\Migration;
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
        // Only insert if not exists
        if (!DB::table('admin_settings')->where('setting_key', 'meeting_creation_enabled')->exists()) {
            DB::table('admin_settings')->insert([
                'setting_key' => 'meeting_creation_enabled',
                'setting_value' => '1',
                'setting_description' => 'Enable or disable meeting creation for professors',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('admin_settings')->where('setting_key', 'meeting_creation_enabled')->delete();
    }
};
