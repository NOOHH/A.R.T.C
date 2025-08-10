<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->integer('package_id')->nullable(false)->primary();
            $table->string('100')('package_name')->nullable(false);
            $table->text('description');
            $table->decimal('10', '2')('amount')->nullable(false)->default(0.00);
            $table->integer('program_id');
            $table->integer('created_by_admin_id')->nullable(false);
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            $table->enum(''full','modular'')('package_type')->nullable(false)->default('full');
            $table->enum(''active','inactive'')('status')->nullable(false)->default('active');
            $table->enum(''module','course','both'')('selection_type')->nullable(false)->default('module');
            $table->enum(''modules','courses'')('selection_mode')->nullable(false)->default('modules');
            $table->integer('module_count')->default(0);
            $table->integer('course_count');
            $table->integer('min_courses');
            $table->integer('max_courses');
            $table->integer('allowed_modules')->nullable(false)->default(2);
            $table->text('allowed_courses');
            $table->decimal('10', '2')('extra_module_price');
            $table->decimal('10', '2')('price')->default(0.00);
            $table->integer('access_period_days');
            $table->integer('access_period_months');
            $table->integer('access_period_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
