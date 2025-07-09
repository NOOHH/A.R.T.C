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
        Schema::create('form_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('field_name'); // e.g., 'phone_number', 'date_of_birth', 'tor_document'
            $table->string('field_label'); // e.g., 'Phone Number', 'Date of Birth', 'Transcript of Records'
            $table->enum('field_type', ['text', 'email', 'tel', 'date', 'file', 'select', 'textarea', 'checkbox']);
            $table->enum('program_type', ['full', 'modular', 'both'])->default('both');
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('field_options')->nullable(); // For select options, file types, etc.
            $table->text('validation_rules')->nullable(); // Laravel validation rules
            $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('form_requirements');
    }
};
