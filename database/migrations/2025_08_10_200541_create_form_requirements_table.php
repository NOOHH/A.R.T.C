<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormrequirementsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('form_requirements', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('field_name')->nullable(false);
            $table->string('255')('field_label')->nullable(false);
            $table->text('field_type');
            $table->enum(''student','professor','admin'')('entity_type')->nullable(false)->default('student');
            $table->enum(''full','modular','both','all'')('program_type')->nullable(false)->default('both');
            $table->boolean('is_required')->nullable(false)->default(1);
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->boolean('is_bold')->nullable(false)->default(0);
            $table->text('field_options');
            $table->text('validation_rules');
            $table->integer('sort_order')->nullable(false)->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->string('255')('section_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('form_requirements');
    }
}
