<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns to enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'learning_mode')) {
                $table->enum('learning_mode', ['synchronous', 'asynchronous'])->nullable()->after('enrollment_type');
            }
            if (!Schema::hasColumn('enrollments', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('learning_mode');
            }
            if (!Schema::hasColumn('enrollments', 'batch_access_granted')) {
                $table->boolean('batch_access_granted')->default(false)->after('batch_id');
            }
            if (!Schema::hasColumn('enrollments', 'enrollment_status')) {
                $table->enum('enrollment_status', ['pending', 'approved', 'rejected'])->default('pending')->after('batch_access_granted');
            }
            if (!Schema::hasColumn('enrollments', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('enrollment_status');
            }
            if (!Schema::hasColumn('enrollments', 'start_date')) {
                $table->date('start_date')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('enrollments', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('enrollments', 'student_id')) {
                $table->string('student_id', 30)->nullable()->after('enrollment_id');
            }
            if (!Schema::hasColumn('enrollments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('enrollments', 'registration_id')) {
                $table->unsignedBigInteger('registration_id')->nullable()->after('user_id');
            }
        });

        // Add missing columns to student_batches table if it exists
        if (Schema::hasTable('student_batches')) {
            Schema::table('student_batches', function (Blueprint $table) {
                if (!Schema::hasColumn('student_batches', 'end_date')) {
                    $table->datetime('end_date')->nullable()->after('start_date');
                }
            });
        }

        // Create student_batches table if it doesn't exist
        if (!Schema::hasTable('student_batches')) {
            Schema::create('student_batches', function (Blueprint $table) {
                $table->id('batch_id');
                $table->string('batch_name');
                $table->unsignedBigInteger('program_id');
                $table->integer('max_capacity')->default(10);
                $table->integer('current_capacity')->default(0);
                $table->enum('batch_status', ['available', 'ongoing', 'closed', 'completed', 'pending', 'not_verified'])->default('available');
                $table->datetime('registration_deadline')->nullable();
                $table->datetime('start_date')->nullable();
                $table->datetime('end_date')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('professor_id')->nullable();
                $table->timestamp('professor_assigned_at')->nullable();
                $table->unsignedBigInteger('professor_assigned_by')->nullable();
                $table->timestamps();

                $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
                $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('set null');
            });
        }

        // Add missing columns to registrations table
        Schema::table('registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('registrations', 'learning_mode')) {
                $table->enum('learning_mode', ['synchronous', 'asynchronous'])->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('registrations', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('learning_mode');
            }
            if (!Schema::hasColumn('registrations', 'end_date')) {
                $table->date('end_date')->nullable()->after('Start_Date');
            }
        });

        // Add missing columns to students table
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'learning_mode')) {
                $table->enum('learning_mode', ['synchronous', 'asynchronous'])->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('students', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('learning_mode');
            }
            if (!Schema::hasColumn('students', 'end_date')) {
                $table->date('end_date')->nullable()->after('Start_Date');
            }
            if (!Schema::hasColumn('students', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('program_name');
            }
        });

        // Add batch_status to batches table if it doesn't have pending/not_verified
        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                if (!Schema::hasColumn('batches', 'end_date')) {
                    $table->datetime('end_date')->nullable()->after('start_date');
                }
            });
            
            // Update batch_status enum to include pending and not_verified
            DB::statement("ALTER TABLE batches MODIFY COLUMN batch_status ENUM('available', 'ongoing', 'closed', 'completed', 'pending', 'not_verified') DEFAULT 'available'");
        }

        // Add foreign keys for batch_id if not exists
        if (Schema::hasTable('student_batches')) {
            Schema::table('enrollments', function (Blueprint $table) {
                if (!Schema::hasColumn('enrollments', 'batch_id_foreign')) {
                    $table->foreign('batch_id')->references('batch_id')->on('student_batches')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
        });

        // Drop columns from enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['learning_mode', 'batch_id', 'batch_access_granted', 'enrollment_status', 'payment_status', 'start_date', 'end_date', 'student_id', 'user_id', 'registration_id']);
        });

        // Drop columns from registrations
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['learning_mode', 'batch_id', 'end_date']);
        });

        // Drop columns from students
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['learning_mode', 'batch_id', 'end_date', 'is_archived']);
        });

        // Drop columns from batches
        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                $table->dropColumn(['end_date']);
            });
        }

        // Drop columns from student_batches
        if (Schema::hasTable('student_batches')) {
            Schema::table('student_batches', function (Blueprint $table) {
                $table->dropColumn(['end_date']);
            });
        }
    }
};
