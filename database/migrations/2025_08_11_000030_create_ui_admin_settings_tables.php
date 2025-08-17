<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ui_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->string('setting_key');
            $table->text('setting_value')->nullable();
            $table->enum('setting_type', ['color', 'file', 'text', 'boolean', 'json'])->default('text');
            $table->timestamps();
            $table->unique(['section', 'setting_key']);
        });

        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('ui_settings');
    }
};



