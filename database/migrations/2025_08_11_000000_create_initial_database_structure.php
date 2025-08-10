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
        // This migration creates the essential database structure
        // that was previously loaded from mysql-schema.sql
        
        // Create students table if it doesn't exist
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->string('student_id', 30)->primary();
                $table->integer('user_id');
                $table->string('firstname', 50);
                $table->string('middlename', 50)->nullable();
                $table->string('lastname', 50);
                $table->string('student_school', 50)->nullable();
                $table->string('street_address', 50)->nullable();
                $table->string('state_province', 50)->nullable();
                $table->string('city', 50)->nullable();
                $table->string('zipcode', 20)->nullable();
                $table->string('contact_number', 15)->nullable();
                $table->string('emergency_contact_number', 20)->nullable();
                $table->string('good_moral', 255)->nullable();
                $table->string('PSA', 255)->nullable();
                $table->string('Course_Cert', 255)->nullable();
                $table->string('TOR', 255)->nullable();
                $table->string('Cert_of_Grad', 255)->nullable();
                $table->string('photo_2x2', 255)->nullable();
                $table->date('Start_Date')->nullable();
                $table->timestamp('date_approved')->useCurrent();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrentOnUpdate();
                $table->string('email', 100);
                $table->boolean('is_archived')->default(false);
                $table->unsignedInteger('package_id')->nullable();
                $table->string('package_name', 255)->nullable();
                $table->unsignedInteger('plan_id')->nullable();
                $table->string('plan_name', 255)->nullable();
                $table->unsignedInteger('program_id')->nullable();
                $table->string('program_name', 255)->nullable();
                $table->string('enrollment_type', 20)->nullable();
                $table->string('learning_mode', 50)->nullable();
            });
        }

        // Create other essential tables if they don't exist
        $essentialTables = [
            'admins' => function (Blueprint $table) {
                $table->increments('admin_id');
                $table->string('admin_name', 100);
                $table->string('email', 100)->unique();
                $table->string('password', 255);
                $table->timestamps();
            },
            'programs' => function (Blueprint $table) {
                $table->increments('program_id');
                $table->string('program_name', 255);
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            },
            'packages' => function (Blueprint $table) {
                $table->increments('package_id');
                $table->string('package_name', 100);
                $table->text('description')->nullable();
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->unsignedInteger('program_id')->nullable();
                $table->unsignedInteger('created_by_admin_id');
                $table->enum('package_type', ['full', 'modular'])->default('full');
                $table->integer('module_count')->default(0);
                $table->integer('allowed_modules')->default(2);
                $table->decimal('extra_module_price', 10, 2)->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->timestamps();
            },
            'plan' => function (Blueprint $table) {
                $table->increments('plan_id');
                $table->string('plan_name', 255);
                $table->text('description')->nullable();
                $table->boolean('enable_synchronous')->default(true);
                $table->boolean('enable_asynchronous')->default(true);
                $table->json('learning_mode_config')->nullable();
            }
        ];

        foreach ($essentialTables as $tableName => $callback) {
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, $callback);
            }
        }

        // Insert default data if tables are empty
        if (DB::table('programs')->count() === 0) {
            DB::table('programs')->insert([
                [
                    'program_id' => 1,
                    'program_name' => 'Default Program',
                    'description' => 'Default program for ARTC',
                    'price' => 0.00,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        if (DB::table('plan')->count() === 0) {
            DB::table('plan')->insert([
                [
                    'plan_id' => 1,
                    'plan_name' => 'Full Plan',
                    'description' => 'Complete program access',
                    'enable_synchronous' => true,
                    'enable_asynchronous' => true,
                ],
                [
                    'plan_id' => 2,
                    'plan_name' => 'Modular Plan',
                    'description' => 'Modular program access',
                    'enable_synchronous' => true,
                    'enable_asynchronous' => true,
                ]
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
        // Drop tables in reverse order
        Schema::dropIfExists('students');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('plan');
        Schema::dropIfExists('admins');
    }
};
