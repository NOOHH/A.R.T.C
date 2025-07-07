
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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Whether visible to non-admins
            $table->timestamps();
        });

        // Insert default AI quiz feature setting
        DB::table('admin_settings')->insert([
            'setting_key' => 'ai_quiz_generation_enabled',
            'setting_value' => 'true',
            'description' => 'Enable or disable AI-powered quiz generation feature for professors',
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_settings');
    }
};
