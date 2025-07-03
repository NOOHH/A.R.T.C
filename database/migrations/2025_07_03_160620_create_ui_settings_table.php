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
        Schema::create('ui_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section'); // 'navbar', 'student_portal', 'professor_portal', 'admin_portal'
            $table->string('setting_key'); // 'primary_color', 'background_color', 'logo_url', etc.
            $table->text('setting_value'); // The actual value
            $table->enum('setting_type', ['color', 'file', 'text', 'boolean', 'json'])->default('text');
            $table->timestamps();
            
            $table->unique(['section', 'setting_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ui_settings');
    }
};
