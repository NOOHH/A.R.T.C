<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_form_fields', function (Blueprint $table) {
            $table->id();
            $table->enum('form', ['login','register']);
            $table->string('field_key');
            $table->string('label');
            $table->string('type')->default('text');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['form','field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_form_fields');
    }
};
