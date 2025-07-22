<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_method_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_method_id');
            $table->string('field_name');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'email', 'tel', 'file', 'number']);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('field_options')->nullable();
            $table->timestamps();
            $table->foreign('payment_method_id')->references('payment_method_id')->on('payment_methods')->onDelete('cascade');
        });

        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->text('terms_html');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_method_fields');
        Schema::dropIfExists('payment_terms');
    }
}; 