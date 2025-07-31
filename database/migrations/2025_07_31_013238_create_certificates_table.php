<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id('certificate_id');
            $table->string('student_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('program_id');
            $table->string('certificate_number')->unique();
            $table->string('student_name');
            $table->string('program_name');
            $table->date('start_date');
            $table->date('completion_date');
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('certificate_type')->default('completion'); // completion, achievement, participation
            $table->string('status')->default('pending'); // pending, approved, issued, rejected
            $table->text('certificate_data')->nullable(); // JSON data for certificate generation
            $table->string('file_path')->nullable(); // Path to generated certificate file
            $table->string('qr_code')->nullable(); // QR code for verification
            $table->timestamp('issued_at')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable(); // Admin who issued the certificate
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['student_id']);
            $table->index(['enrollment_id']);
            $table->index(['program_id']);
            $table->index(['status']);
            $table->index(['certificate_number']);
            
            // Note: Foreign keys will be added separately if tables exist
        });
        
        // Add foreign keys after table creation if referenced tables exist
        if (Schema::hasTable('students')) {
            Schema::table('certificates', function (Blueprint $table) {
                try {
                    $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
                } catch (Exception $e) {
                    // If foreign key fails, continue without it
                    Log::warning('Failed to add student_id foreign key to certificates table: ' . $e->getMessage());
                }
            });
        }
        
        if (Schema::hasTable('enrollments')) {
            Schema::table('certificates', function (Blueprint $table) {
                try {
                    $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
                } catch (Exception $e) {
                    // If foreign key fails, continue without it
                    Log::warning('Failed to add enrollment_id foreign key to certificates table: ' . $e->getMessage());
                }
            });
        }
        
        if (Schema::hasTable('programs')) {
            Schema::table('certificates', function (Blueprint $table) {
                try {
                    $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
                } catch (Exception $e) {
                    // If foreign key fails, continue without it
                    Log::warning('Failed to add program_id foreign key to certificates table: ' . $e->getMessage());
                }
            });
        }
        
        if (Schema::hasTable('admins')) {
            Schema::table('certificates', function (Blueprint $table) {
                try {
                    $table->foreign('issued_by')->references('admin_id')->on('admins')->onDelete('set null');
                } catch (Exception $e) {
                    // If foreign key fails, continue without it
                    Log::warning('Failed to add issued_by foreign key to certificates table: ' . $e->getMessage());
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
