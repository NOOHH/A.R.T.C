<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SyncDatabaseStructure extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create activities table
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->bigInteger('activity_id')->nullable(false)->primary();
                $table->bigInteger('professor_id')->nullable(false);
                $table->bigInteger('program_id')->nullable(false);
                $table->string('255')('title')->nullable(false);
                $table->text('description');
                $table->text('instructions');
                $table->integer('max_points')->nullable(false)->default(100);
                $table->dateTime('due_date')->nullable(false);
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create admin_settings table
        if (!Schema::hasTable('admin_settings')) {
            Schema::create('admin_settings', function (Blueprint $table) {
                $table->bigInteger('setting_id')->nullable(false)->primary();
                $table->string('255')('setting_key')->nullable(false);
                $table->text('setting_value')->nullable(false);
                $table->text('setting_description');
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create admins table
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->integer('admin_id')->nullable(false)->primary();
                $table->string('100')('admin_name')->nullable(false);
                $table->string('100')('email')->nullable(false);
                $table->string('255')('password')->nullable(false);
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create announcements table
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->bigInteger('announcement_id')->nullable(false)->primary();
                $table->bigInteger('admin_id');
                $table->bigInteger('professor_id');
                $table->bigInteger('program_id');
                $table->string('255')('title')->nullable(false);
                $table->text('content')->nullable(false);
                $table->text('description');
                $table->timestamp('publish_date');
                $table->timestamp('expire_date');
                $table->boolean('is_published')->nullable(false)->default(1);
                $table->enum(''general','urgent','event','system','video','assignment','quiz'')('type')->default('general');
                $table->text('target_users');
                $table->text('target_programs');
                $table->text('target_batches');
                $table->text('target_plans');
                $table->enum(''all','specific'')('target_scope')->nullable(false)->default('all');
                $table->string('255')('video_link');
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create assignment_submissions table
        if (!Schema::hasTable('assignment_submissions')) {
            Schema::create('assignment_submissions', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('255')('student_id')->nullable(false);
                $table->bigInteger('module_id')->nullable(false);
                $table->bigInteger('content_id');
                $table->bigInteger('program_id')->nullable(false);
                $table->string('255')('file_path');
                $table->string('255')('original_filename');
                $table->text('files');
                $table->text('comments');
                $table->timestamp('submitted_at')->nullable(false)->default('current_timestamp()');
                $table->enum(''pending','draft','submitted','graded','returned','reviewed'')('status');
                $table->decimal('5', '2')('grade');
                $table->text('feedback');
                $table->timestamp('graded_at');
                $table->bigInteger('graded_by');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create assignments table
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->bigInteger('assignment_id')->nullable(false)->primary();
                $table->bigInteger('professor_id')->nullable(false);
                $table->bigInteger('program_id')->nullable(false);
                $table->string('255')('title')->nullable(false);
                $table->text('description');
                $table->text('instructions');
                $table->integer('max_points')->nullable(false)->default(100);
                $table->dateTime('due_date')->nullable(false);
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create attendance table
        if (!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('30')('student_id')->nullable(false);
                $table->integer('program_id')->nullable(false);
                $table->integer('professor_id')->nullable(false);
                $table->date('attendance_date')->nullable(false);
                $table->enum(''present','absent','late','excused'')('status')->nullable(false);
                $table->text('notes');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create batch_professors table
        if (!Schema::hasTable('batch_professors')) {
            Schema::create('batch_professors', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->integer('batch_id')->nullable(false);
                $table->integer('professor_id')->nullable(false);
                $table->timestamp('assigned_at')->nullable(false)->default('current_timestamp()');
                $table->integer('assigned_by');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create board_passers table
        if (!Schema::hasTable('board_passers')) {
            Schema::create('board_passers', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('255')('student_id');
                $table->string('255')('student_name')->nullable(false);
                $table->string('255')('program');
                $table->string('255')('board_exam')->nullable(false);
                $table->integer('exam_year')->nullable(false);
                $table->date('exam_date');
                $table->enum(''pass','fail','pending'')('result')->nullable(false)->default('PENDING');
                $table->decimal('5', '2')('rating');
                $table->text('notes');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create certificates table
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->bigInteger('certificate_id')->nullable(false)->primary();
                $table->string('255')('student_id')->nullable(false);
                $table->bigInteger('enrollment_id')->nullable(false);
                $table->bigInteger('program_id')->nullable(false);
                $table->string('255')('certificate_number')->nullable(false);
                $table->string('255')('student_name')->nullable(false);
                $table->string('255')('program_name')->nullable(false);
                $table->date('start_date')->nullable(false);
                $table->date('completion_date')->nullable(false);
                $table->decimal('5', '2')('final_score');
                $table->string('255')('certificate_type')->nullable(false)->default('completion');
                $table->string('255')('status')->nullable(false)->default('pending');
                $table->text('certificate_data');
                $table->string('255')('file_path');
                $table->string('255')('qr_code');
                $table->timestamp('issued_at');
                $table->bigInteger('issued_by');
                $table->text('rejection_reason');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create chats table
        if (!Schema::hasTable('chats')) {
            Schema::create('chats', function (Blueprint $table) {
                $table->bigInteger('chat_id')->nullable(false)->primary();
                $table->bigInteger('sender_id')->nullable(false);
                $table->bigInteger('receiver_id')->nullable(false);
                $table->text('body_cipher')->nullable(false);
                $table->boolean('is_read')->default(0);
                $table->boolean('is_encrypted')->nullable(false)->default(1);
                $table->timestamp('sent_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('read_at');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create class_meetings table
        if (!Schema::hasTable('class_meetings')) {
            Schema::create('class_meetings', function (Blueprint $table) {
                $table->bigInteger('meeting_id')->nullable(false)->primary();
                $table->bigInteger('batch_id')->nullable(false);
                $table->integer('professor_id')->nullable(false);
                $table->string('255')('title')->nullable(false);
                $table->text('description');
                $table->dateTime('meeting_date')->nullable(false);
                $table->integer('duration_minutes')->nullable(false)->default(60);
                $table->string('255')('meeting_url');
                $table->text('attached_files');
                $table->enum(''scheduled','ongoing','completed','cancelled'')('status')->nullable(false)->default('scheduled');
                $table->integer('created_by')->nullable(false);
                $table->boolean('url_visible_before_meeting')->nullable(false)->default(0);
                $table->integer('url_visibility_minutes_before')->nullable(false)->default(0);
                $table->dateTime('actual_start_time');
                $table->dateTime('actual_end_time');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create content_completions table
        if (!Schema::hasTable('content_completions')) {
            Schema::create('content_completions', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('30')('student_id')->nullable(false);
                $table->bigInteger('content_id')->nullable(false);
                $table->bigInteger('course_id');
                $table->integer('module_id');
                $table->timestamp('completed_at');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create content_items table
        if (!Schema::hasTable('content_items')) {
            Schema::create('content_items', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('255')('content_title')->nullable(false);
                $table->text('content_description');
                $table->bigInteger('lesson_id');
                $table->bigInteger('course_id');
                $table->enum(''assignment','quiz','test','link','video','document','lesson'')('content_type');
                $table->bigInteger('created_by_professor_id');
                $table->text('content_data');
                $table->string('255')('content_url');
                $table->string('255')('attachment_path');
                $table->decimal('8', '2')('max_points');
                $table->dateTime('due_date');
                $table->integer('time_limit');
                $table->integer('content_order')->nullable(false)->default(0);
                $table->integer('sort_order')->nullable(false)->default(0);
                $table->boolean('enable_submission')->nullable(false)->default(0);
                $table->string('255')('allowed_file_types');
                $table->integer('max_file_size');
                $table->text('submission_instructions');
                $table->boolean('allow_multiple_submissions')->nullable(false)->default(0);
                $table->integer('order')->nullable(false)->default(0);
                $table->boolean('is_required')->nullable(false)->default(1);
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->boolean('is_archived')->nullable(false)->default(0);
                $table->timestamp('archived_at');
                $table->bigInteger('archived_by_professor_id');
                $table->text('admin_override');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
                $table->boolean('is_locked')->nullable(false)->default(0);
                $table->boolean('requires_prerequisite')->nullable(false)->default(0);
                $table->bigInteger('prerequisite_content_id');
                $table->timestamp('release_date');
                $table->text('completion_criteria');
                $table->string('255')('lock_reason');
                $table->bigInteger('locked_by');
                $table->string('255')('file_path');
                $table->string('255')('file_name');
                $table->bigInteger('file_size');
                $table->string('255')('file_mime');
                $table->boolean('has_multiple_files')->nullable(false)->default(0);
            });
        }

        // Create course_completions table
        if (!Schema::hasTable('course_completions')) {
            Schema::create('course_completions', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('30')('student_id')->nullable(false);
                $table->bigInteger('course_id')->nullable(false);
                $table->integer('module_id');
                $table->bigInteger('content_id');
                $table->timestamp('completed_at');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create courses table
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->bigInteger('subject_id')->nullable(false)->primary();
                $table->string('255')('subject_name')->nullable(false);
                $table->text('subject_description');
                $table->bigInteger('module_id')->nullable(false);
                $table->decimal('10', '2')('subject_price')->nullable(false)->default(0.00);
                $table->integer('subject_order')->nullable(false)->default(0);
                $table->integer('course_order')->nullable(false)->default(0);
                $table->boolean('is_required')->nullable(false)->default(0);
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->boolean('is_archived')->nullable(false)->default(0);
                $table->text('admin_override');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
                $table->boolean('is_locked')->nullable(false)->default(0);
                $table->boolean('requires_prerequisite')->nullable(false)->default(0);
                $table->bigInteger('prerequisite_course_id');
                $table->timestamp('release_date');
                $table->text('completion_criteria');
                $table->string('255')('lock_reason');
                $table->bigInteger('locked_by');
            });
        }

        // Create deadlines table
        if (!Schema::hasTable('deadlines')) {
            Schema::create('deadlines', function (Blueprint $table) {
                $table->bigInteger('deadline_id')->nullable(false)->primary();
                $table->bigInteger('student_id')->nullable(false);
                $table->bigInteger('program_id')->nullable(false);
                $table->string('255')('title')->nullable(false);
                $table->text('description');
                $table->enum(''assignment','quiz','activity','exam'')('type')->nullable(false)->default('assignment');
                $table->bigInteger('reference_id');
                $table->dateTime('due_date')->nullable(false);
                $table->enum(''pending','completed','overdue'')('status')->nullable(false)->default('pending');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create director_program table
        if (!Schema::hasTable('director_program')) {
            Schema::create('director_program', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->integer('director_id')->nullable(false);
                $table->integer('program_id')->nullable(false);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create directors table
        if (!Schema::hasTable('directors')) {
            Schema::create('directors', function (Blueprint $table) {
                $table->integer('directors_id')->nullable(false)->primary();
                $table->integer('admin_id');
                $table->string('100')('directors_name')->nullable(false);
                $table->string('100')('directors_first_name')->nullable(false);
                $table->string('100')('directors_last_name')->nullable(false);
                $table->string('100')('directors_email')->nullable(false);
                $table->string('255')('directors_password')->nullable(false);
                $table->string('20')('referral_code');
                $table->boolean('directors_archived')->default(0);
                $table->boolean('has_all_program_access')->nullable(false)->default(1);
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create education_levels table
        if (!Schema::hasTable('education_levels')) {
            Schema::create('education_levels', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('255')('level_name')->nullable(false);
                $table->text('file_requirements');
                $table->boolean('available_for_general')->nullable(false)->default(1);
                $table->boolean('available_for_professional')->nullable(false)->default(1);
                $table->boolean('available_for_review')->nullable(false)->default(1);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->integer('level_order')->nullable(false)->default(0);
            });
        }

        // Create enrollment_courses table
        if (!Schema::hasTable('enrollment_courses')) {
            Schema::create('enrollment_courses', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('enrollment_id')->nullable(false);
                $table->integer('course_id')->nullable(false);
                $table->integer('module_id')->nullable(false);
                $table->enum(''module','course'')('enrollment_type')->nullable(false)->default('course');
                $table->decimal('10', '2')('course_price')->nullable(false)->default(0.00);
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create enrollments table
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->integer('enrollment_id')->nullable(false)->primary();
                $table->integer('registration_id');
                $table->string('30')('student_id');
                $table->bigInteger('user_id');
                $table->integer('program_id')->nullable(false);
                $table->integer('package_id')->nullable(false);
                $table->enum(''modular','full'')('enrollment_type')->nullable(false)->default('Modular');
                $table->enum(''synchronous','asynchronous'')('learning_mode')->nullable(false)->default('Synchronous');
                $table->bigInteger('batch_id');
                $table->dateTime('individual_start_date');
                $table->dateTime('individual_end_date');
                $table->enum(''pending','approved','rejected','completed'')('enrollment_status')->nullable(false)->default('pending');
                $table->decimal('5', '2')('progress_percentage')->nullable(false)->default(0.00);
                $table->timestamp('completion_date');
                $table->timestamp('last_activity');
                $table->integer('total_modules')->nullable(false)->default(0);
                $table->integer('completed_modules')->nullable(false)->default(0);
                $table->integer('total_courses')->nullable(false)->default(0);
                $table->integer('completed_courses')->nullable(false)->default(0);
                $table->boolean('certificate_eligible')->nullable(false)->default(0);
                $table->boolean('certificate_requested')->nullable(false)->default(0);
                $table->boolean('certificate_issued')->nullable(false)->default(0);
                $table->enum(''pending','paid','failed','cancelled'')('payment_status')->default('pending');
                $table->boolean('batch_access_granted')->nullable(false)->default(0);
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('start_date');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
                $table->string('255')('school_id');
                $table->string('255')('diploma');
                $table->string('255')('valid_school_identification');
                $table->string('255')('transcript_of_records');
                $table->string('255')('certificate_of_good_moral_character');
                $table->string('255')('psa_birth_certificate');
                $table->string('255')('photo_2x2');
                $table->string('255')('diploma_certificate');
                $table->string('255')('transcript_records');
                $table->string('255')('moral_certificate');
                $table->string('255')('birth_cert');
                $table->string('255')('id_photo');
                $table->string('255')('passport_photo');
                $table->string('255')('medical_certificate');
                $table->string('255')('barangay_clearance');
                $table->string('255')('police_clearance');
                $table->string('255')('nbi_clearance');
                $table->string('255')('tor');
                $table->string('255')('good_moral');
                $table->string('255')('psa');
            });
        }

        // Create form_requirements table
        if (!Schema::hasTable('form_requirements')) {
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

        // Create meeting_attendance_logs table
        if (!Schema::hasTable('meeting_attendance_logs')) {
            Schema::create('meeting_attendance_logs', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('meeting_id')->nullable(false);
                $table->bigInteger('student_id')->nullable(false);
                $table->enum(''present','absent','late'')('status')->nullable(false);
                $table->timestamp('joined_at');
                $table->timestamp('left_at');
                $table->integer('duration_minutes');
                $table->string('255')('ip_address');
                $table->text('notes');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create messages table
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('sender_id')->nullable(false);
                $table->string('50')('sender_type')->nullable(false);
                $table->bigInteger('receiver_id')->nullable(false);
                $table->string('50')('receiver_type')->nullable(false);
                $table->text('message')->nullable(false);
                $table->timestamp('sent_at')->nullable(false)->default('current_timestamp()');
                $table->boolean('is_read')->nullable(false)->default(0);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
                $table->text('content')->nullable(false);
                $table->timestamp('deleted_at');
            });
        }

        // Create module_completions table
        if (!Schema::hasTable('module_completions')) {
            Schema::create('module_completions', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('30')('student_id')->nullable(false);
                $table->integer('program_id');
                $table->integer('modules_id');
                $table->bigInteger('content_id');
                $table->timestamp('completed_at');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create modules table
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->integer('modules_id')->nullable(false)->primary();
                $table->string('255')('module_name')->nullable(false);
                $table->text('module_description');
                $table->integer('program_id')->nullable(false);
                $table->bigInteger('batch_id');
                $table->enum(''synchronous','asynchronous'')('learning_mode')->nullable(false)->default('Synchronous');
                $table->string('50')('content_type')->nullable(false)->default('');
                $table->text('content_data');
                $table->integer('plan_id');
                $table->string('255')('attachment');
                $table->string('255')('video_path');
                $table->text('content_url');
                $table->text('additional_content');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
                $table->boolean('is_archived')->default(0);
                $table->integer('order')->nullable(false)->default(0);
                $table->text('admin_override');
                $table->boolean('is_locked')->nullable(false)->default(0);
                $table->boolean('requires_prerequisite')->nullable(false)->default(0);
                $table->bigInteger('prerequisite_module_id');
                $table->timestamp('release_date');
                $table->text('completion_criteria');
                $table->string('255')('lock_reason');
                $table->bigInteger('locked_by');
                $table->integer('module_order')->nullable(false)->default(0);
            });
        }

        // Create package_courses table
        if (!Schema::hasTable('package_courses')) {
            Schema::create('package_courses', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->integer('package_id')->nullable(false);
                $table->integer('course_id')->nullable(false);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create package_modules table
        if (!Schema::hasTable('package_modules')) {
            Schema::create('package_modules', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('package_id')->nullable(false);
                $table->bigInteger('modules_id')->nullable(false);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create packages table
        if (!Schema::hasTable('packages')) {
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

        // Create payment_history table
        if (!Schema::hasTable('payment_history')) {
            Schema::create('payment_history', function (Blueprint $table) {
                $table->bigInteger('payment_history_id')->nullable(false)->primary();
                $table->integer('enrollment_id')->nullable(false);
                $table->integer('user_id');
                $table->string('255')('student_id');
                $table->integer('program_id')->nullable(false);
                $table->integer('package_id')->nullable(false);
                $table->decimal('10', '2')('amount');
                $table->enum(''pending','paid','failed','refunded','cancelled','processing'')('payment_status');
                $table->enum(''cash','card','bank_transfer','gcash','manual','other'')('payment_method');
                $table->text('payment_notes');
                $table->timestamp('payment_date');
                $table->integer('processed_by_admin_id');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create payment_method_fields table
        if (!Schema::hasTable('payment_method_fields')) {
            Schema::create('payment_method_fields', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('payment_method_id')->nullable(false);
                $table->string('255')('field_name')->nullable(false);
                $table->string('255')('field_label')->nullable(false);
                $table->text('field_type')->nullable(false);
                $table->text('field_options');
                $table->boolean('is_required')->nullable(false)->default(1);
                $table->integer('sort_order')->nullable(false)->default(0);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create payment_methods table
        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->bigInteger('payment_method_id')->nullable(false)->primary();
                $table->string('255')('method_name')->nullable(false);
                $table->enum(''credit_card','gcash','maya','bank_transfer','cash','other'')('method_type')->nullable(false);
                $table->text('description');
                $table->string('255')('qr_code_path');
                $table->text('instructions');
                $table->text('dynamic_fields');
                $table->boolean('is_enabled')->nullable(false)->default(1);
                $table->integer('sort_order')->nullable(false)->default(0);
                $table->bigInteger('created_by_admin_id')->nullable(false);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->bigInteger('payment_id')->nullable(false)->primary();
                $table->bigInteger('enrollment_id')->nullable(false);
                $table->string('30')('student_id')->nullable(false);
                $table->bigInteger('program_id')->nullable(false);
                $table->bigInteger('package_id')->nullable(false);
                $table->enum(''credit_card','gcash','bank_transfer','cash','admin_marked'')('payment_method')->nullable(false);
                $table->decimal('10', '2')('amount')->nullable(false);
                $table->enum(''pending','paid','failed','cancelled','rejected','resubmitted'')('payment_status')->default('pending');
                $table->text('rejection_reason');
                $table->bigInteger('rejected_by');
                $table->timestamp('rejected_at');
                $table->text('rejected_fields');
                $table->timestamp('resubmitted_at');
                $table->integer('resubmission_count')->default(0);
                $table->text('payment_details');
                $table->bigInteger('verified_by');
                $table->timestamp('verified_at');
                $table->string('255')('receipt_number');
                $table->string('255')('reference_number');
                $table->text('notes');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create plan table
        if (!Schema::hasTable('plan')) {
            Schema::create('plan', function (Blueprint $table) {
                $table->integer('plan_id')->nullable(false)->primary();
                $table->string('50')('plan_name')->nullable(false);
                $table->text('description');
                $table->boolean('enable_synchronous')->nullable(false)->default(1);
                $table->boolean('enable_asynchronous')->nullable(false)->default(1);
                $table->text('learning_mode_config');
            });
        }

        // Create professor_batch table
        if (!Schema::hasTable('professor_batch')) {
            Schema::create('professor_batch', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('batch_id')->nullable(false);
                $table->integer('professor_id')->nullable(false);
                $table->timestamp('assigned_at');
                $table->integer('assigned_by');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create professor_program table
        if (!Schema::hasTable('professor_program')) {
            Schema::create('professor_program', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('professor_id')->nullable(false);
                $table->bigInteger('program_id')->nullable(false);
                $table->string('255')('video_link');
                $table->text('video_description');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create professors table
        if (!Schema::hasTable('professors')) {
            Schema::create('professors', function (Blueprint $table) {
                $table->integer('professor_id')->nullable(false)->primary();
                $table->integer('admin_id')->nullable(false);
                $table->string('100')('professor_name')->nullable(false);
                $table->string('100')('professor_first_name')->nullable(false);
                $table->string('100')('professor_last_name')->nullable(false);
                $table->string('100')('professor_email')->nullable(false);
                $table->string('255')('professor_password')->nullable(false);
                $table->string('20')('referral_code');
                $table->string('255')('profile_photo');
                $table->text('dynamic_data');
                $table->boolean('professor_archived')->default(0);
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create programs table
        if (!Schema::hasTable('programs')) {
            Schema::create('programs', function (Blueprint $table) {
                $table->integer('program_id')->nullable(false)->primary();
                $table->string('100')('program_name')->nullable(false);
                $table->integer('created_by_admin_id')->nullable(false);
                $table->integer('director_id');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
                $table->integer('is_active')->nullable(false);
                $table->boolean('is_archived')->default(0);
                $table->text('program_description');
                $table->string('255')('program_image');
                $table->text('admin_override');
            });
        }

        // Create quiz_attempts table
        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->bigInteger('attempt_id')->nullable(false)->primary();
                $table->bigInteger('quiz_id')->nullable(false);
                $table->string('255')('student_id')->nullable(false);
                $table->text('answers')->nullable(false);
                $table->decimal('5', '2')('score');
                $table->integer('total_questions')->nullable(false);
                $table->integer('correct_answers')->nullable(false)->default(0);
                $table->timestamp('started_at');
                $table->timestamp('completed_at');
                $table->integer('time_taken');
                $table->enum(''in_progress','completed','abandoned'')('status')->nullable(false)->default('in_progress');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create quiz_options table
        if (!Schema::hasTable('quiz_options')) {
            Schema::create('quiz_options', function (Blueprint $table) {
                $table->bigInteger('option_id')->nullable(false)->primary();
                $table->bigInteger('question_id')->nullable(false);
                $table->text('option_text')->nullable(false);
                $table->boolean('is_correct')->nullable(false)->default(0);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create quiz_questions table
        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->bigInteger('quiz_id')->nullable(false);
                $table->string('255')('quiz_title');
                $table->integer('program_id');
                $table->text('question_text')->nullable(false);
                $table->enum(''multiple_choice','true_false','short_answer','essay'')('question_type')->nullable(false);
                $table->integer('question_order');
                $table->text('options');
                $table->text('correct_answer')->default('''');
                $table->text('explanation');
                $table->enum(''generated','manual','quizapi'')('question_source')->nullable(false)->default('generated');
                $table->text('question_metadata');
                $table->text('instructions');
                $table->integer('points')->nullable(false)->default(1);
                $table->string('255')('source_file');
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->integer('created_by_admin');
                $table->integer('created_by_professor');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create quizzes table
        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->bigInteger('quiz_id')->nullable(false)->primary();
                $table->bigInteger('professor_id');
                $table->integer('admin_id');
                $table->bigInteger('program_id')->nullable(false);
                $table->bigInteger('module_id');
                $table->bigInteger('course_id');
                $table->bigInteger('content_id');
                $table->string('255')('quiz_title')->nullable(false);
                $table->text('instructions');
                $table->text('quiz_description');
                $table->integer('total_questions')->nullable(false)->default(10);
                $table->integer('time_limit')->nullable(false)->default(60);
                $table->string('255')('document_path');
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->enum(''draft','published','archived'')('status')->nullable(false)->default('draft');
                $table->boolean('allow_retakes')->nullable(false)->default(0);
                $table->boolean('infinite_retakes')->default(0);
                $table->boolean('instant_feedback')->nullable(false)->default(0);
                $table->boolean('show_correct_answers')->nullable(false)->default(1);
                $table->integer('max_attempts');
                $table->boolean('has_deadline')->default(0);
                $table->dateTime('due_date');
                $table->boolean('is_draft')->nullable(false)->default(0);
                $table->boolean('randomize_order')->nullable(false)->default(0);
                $table->boolean('randomize_mc_options')->nullable(false)->default(0);
                $table->text('tags');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create referrals table
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->bigInteger('referral_id')->nullable(false)->primary();
                $table->string('20')('referral_code')->nullable(false);
                $table->enum(''director','professor'')('referrer_type')->nullable(false);
                $table->integer('referrer_id')->nullable(false);
                $table->string('30')('student_id')->nullable(false);
                $table->integer('registration_id')->nullable(false);
                $table->timestamp('used_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create registrations table
        if (!Schema::hasTable('registrations')) {
            Schema::create('registrations', function (Blueprint $table) {
                $table->integer('registration_id')->nullable(false)->primary();
                $table->integer('user_id')->nullable(false);
                $table->integer('package_id');
                $table->string('255')('package_name');
                $table->integer('plan_id');
                $table->string('255')('plan_name');
                $table->integer('program_id');
                $table->string('255')('program_name');
                $table->string('20')('enrollment_type');
                $table->string('50')('learning_mode');
                $table->string('255')('firstname');
                $table->string('50')('middlename');
                $table->string('255')('lastname');
                $table->string('50')('student_school');
                $table->string('50')('street_address');
                $table->string('50')('state_province');
                $table->string('50')('city');
                $table->string('20')('zipcode');
                $table->string('15')('contact_number');
                $table->string('255')('emergency_contact_number');
                $table->string('255')('good_moral');
                $table->string('255')('PSA');
                $table->string('255')('Course_Cert');
                $table->string('255')('TOR');
                $table->string('255')('Cert_of_Grad');
                $table->text('dynamic_fields');
                $table->string('255')('photo_2x2');
                $table->date('Start_Date');
                $table->enum(''pending','approved','rejected','resubmitted'')('status')->default('pending');
                $table->bigInteger('approved_by');
                $table->timestamp('approved_at');
                $table->text('undo_reason');
                $table->timestamp('undone_at');
                $table->bigInteger('undone_by');
                $table->text('rejection_reason');
                $table->text('rejected_fields');
                $table->bigInteger('rejected_by');
                $table->timestamp('rejected_at');
                $table->timestamp('resubmitted_at');
                $table->text('original_submission');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
                $table->string('20')('phone_number');
                $table->string('20')('telephone_number');
                $table->string('100')('religion');
                $table->string('100')('citizenship');
                $table->string('50')('civil_status');
                $table->date('birthdate');
                $table->string('20')('gender');
                $table->text('work_experience');
                $table->string('50')('preferred_schedule');
                $table->string('100')('emergency_contact_relationship');
                $table->text('health_conditions');
                $table->boolean('disability_support')->default(0);
                $table->string('255')('valid_id');
                $table->string('255')('birth_certificate');
                $table->string('255')('diploma_certificate');
                $table->string('255')('medical_certificate');
                $table->string('255')('passport_photo');
                $table->string('255')('parent_guardian_name');
                $table->string('20')('parent_guardian_contact');
                $table->string('255')('previous_school');
                $table->string('graduation_year');
                $table->string('255')('course_taken');
                $table->text('special_needs');
                $table->string('255')('scholarship_program');
                $table->string('100')('employment_status');
                $table->decimal('10', '2')('monthly_income');
                $table->string('255')('school_name');
                $table->text('selected_modules');
                $table->text('selected_courses');
                $table->string('255')('test_field_auto');
                $table->string('255')('testering');
                $table->string('255')('master');
                $table->string('255')('bagit');
                $table->string('255')('real');
                $table->string('255')('test_auto_column_1752439854');
                $table->string('255')('nyan');
                $table->string('50')('education_level');
                $table->enum(''sync','async'')('sync_async_mode')->default('sync');
                $table->string('255')('Test');
                $table->string('255')('last_name');
                $table->string('20')('referral_code');
                $table->string('255')('school_id');
                $table->string('255')('diploma');
                $table->string('255')('valid_school_identification');
                $table->string('255')('transcript_of_records');
                $table->string('255')('certificate_of_good_moral_character');
                $table->string('255')('psa_birth_certificate');
                $table->string('255')('transcript_records');
                $table->string('255')('moral_certificate');
                $table->string('255')('birth_cert');
                $table->string('255')('id_photo');
                $table->string('255')('barangay_clearance');
                $table->string('255')('police_clearance');
                $table->string('255')('nbi_clearance');
                $table->string('255')('form_137');
                $table->string('255')('birthday');
            });
        }

        // Create student_batches table
        if (!Schema::hasTable('student_batches')) {
            Schema::create('student_batches', function (Blueprint $table) {
                $table->bigInteger('batch_id')->nullable(false)->primary();
                $table->string('255')('batch_name')->nullable(false);
                $table->integer('program_id')->nullable(false);
                $table->integer('professor_id');
                $table->integer('max_capacity')->nullable(false);
                $table->integer('current_capacity')->nullable(false)->default(0);
                $table->boolean('is_active')->nullable(false)->default(1);
                $table->enum(''pending','available','ongoing','closed','completed'')('batch_status')->default('pending');
                $table->date('registration_deadline')->nullable(false);
                $table->date('start_date')->nullable(false);
                $table->date('end_date');
                $table->text('description');
                $table->integer('created_by');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create student_grades table
        if (!Schema::hasTable('student_grades')) {
            Schema::create('student_grades', function (Blueprint $table) {
                $table->bigInteger('grade_id')->nullable(false);
                $table->string('30')('student_id')->nullable(false);
                $table->integer('program_id')->nullable(false);
                $table->integer('professor_id')->nullable(false);
                $table->string('255')('assignment_name')->nullable(false);
                $table->decimal('5', '2')('grade')->nullable(false);
                $table->decimal('5', '2')('max_points')->nullable(false);
                $table->text('feedback');
                $table->timestamp('graded_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create student_progress table
        if (!Schema::hasTable('student_progress')) {
            Schema::create('student_progress', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('255')('student_id')->nullable(false);
                $table->bigInteger('content_id')->nullable(false);
                $table->bigInteger('course_id');
                $table->bigInteger('module_id');
                $table->boolean('is_completed')->nullable(false)->default(0);
                $table->timestamp('completed_at');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create students table
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->string('30')('student_id')->nullable(false)->primary();
                $table->integer('user_id')->nullable(false);
                $table->string('50')('firstname')->nullable(false);
                $table->string('50')('middlename');
                $table->string('50')('lastname')->nullable(false);
                $table->string('50')('student_school');
                $table->string('50')('street_address');
                $table->string('50')('state_province');
                $table->string('50')('city');
                $table->string('20')('zipcode');
                $table->string('15')('contact_number');
                $table->string('20')('emergency_contact_number');
                $table->string('255')('good_moral');
                $table->string('255')('PSA');
                $table->string('255')('Course_Cert');
                $table->string('255')('TOR');
                $table->string('255')('Cert_of_Grad');
                $table->string('255')('photo_2x2');
                $table->string('255')('profile_photo');
                $table->date('Start_Date');
                $table->timestamp('date_approved')->nullable(false)->default('current_timestamp()');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
                $table->string('100')('email')->nullable(false);
                $table->boolean('is_archived')->nullable(false)->default(0);
                $table->integer('package_id');
                $table->string('255')('package_name');
                $table->integer('plan_id');
                $table->string('255')('plan_name');
                $table->integer('program_id');
                $table->string('255')('education_level')->nullable(false)->default('');
                $table->string('255')('program_name');
                $table->string('20')('enrollment_type');
                $table->string('50')('learning_mode');
                $table->boolean('Undergraduate')->default(0);
                $table->boolean('Graduate')->default(0);
                $table->text('dynamic_fields');
                $table->enum(''pending','approved','rejected'')('status')->default('pending');
                $table->string('20')('phone_number');
                $table->string('20')('telephone_number');
                $table->string('100')('religion');
                $table->string('100')('citizenship');
                $table->string('50')('civil_status');
                $table->date('birthdate');
                $table->string('20')('gender');
                $table->text('work_experience');
                $table->string('50')('preferred_schedule');
                $table->string('100')('emergency_contact_relationship');
                $table->text('health_conditions');
                $table->boolean('disability_support')->default(0);
                $table->string('255')('valid_id');
                $table->string('255')('birth_certificate');
                $table->string('255')('diploma_certificate');
                $table->string('255')('medical_certificate');
                $table->string('255')('passport_photo');
                $table->string('255')('parent_guardian_name');
                $table->string('20')('parent_guardian_contact');
                $table->string('20')('referral_code');
                $table->string('255')('ama_namin');
                $table->string('255')('previous_school');
                $table->string('graduation_year');
                $table->string('255')('course_taken');
                $table->text('special_needs');
                $table->string('255')('scholarship_program');
                $table->string('100')('employment_status');
                $table->decimal('10', '2')('monthly_income');
                $table->string('255')('school_name');
                $table->text('selected_modules');
                $table->string('255')('test_field_auto');
                $table->string('255')('testering');
                $table->string('255')('master');
                $table->string('255')('bagit');
                $table->string('255')('real');
                $table->string('255')('test_auto_column_1752439854');
                $table->string('255')('nyan');
                $table->string('255')('Test');
                $table->string('255')('last_name');
                $table->string('255')('school_id');
                $table->string('255')('diploma');
                $table->string('255')('valid_school_identification');
                $table->string('255')('transcript_of_records');
                $table->string('255')('certificate_of_good_moral_character');
                $table->string('255')('psa_birth_certificate');
                $table->string('255')('transcript_records');
                $table->string('255')('moral_certificate');
                $table->string('255')('birth_cert');
                $table->string('255')('id_photo');
                $table->string('255')('barangay_clearance');
                $table->string('255')('police_clearance');
                $table->string('255')('nbi_clearance');
                $table->string('255')('birthday');
            });
        }

        // Create ui_settings table
        if (!Schema::hasTable('ui_settings')) {
            Schema::create('ui_settings', function (Blueprint $table) {
                $table->bigInteger('id')->nullable(false)->primary();
                $table->string('255')('section')->nullable(false);
                $table->string('255')('setting_key')->nullable(false);
                $table->text('setting_value')->nullable(false);
                $table->text('setting_type')->nullable(false)->default('text');
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

        // Create users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('user_id')->nullable(false)->primary();
                $table->bigInteger('admin_id');
                $table->bigInteger('directors_id');
                $table->boolean('is_online')->nullable(false)->default(0);
                $table->timestamp('last_seen');
                $table->string('100')('email')->nullable(false);
                $table->string('255')('user_firstname')->nullable(false);
                $table->string('255')('user_lastname')->nullable(false);
                $table->string('255')('password')->nullable(false);
                $table->string('32')('role')->default('unverified');
                $table->integer('enrollment_id');
                $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
                $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
            });
        }

        // Create websockets_statistics_entries table
        if (!Schema::hasTable('websockets_statistics_entries')) {
            Schema::create('websockets_statistics_entries', function (Blueprint $table) {
                $table->integer('id')->nullable(false)->primary();
                $table->string('255')('app_id')->nullable(false);
                $table->integer('peak_connection_count')->nullable(false);
                $table->integer('websocket_message_count')->nullable(false);
                $table->integer('api_message_count')->nullable(false);
                $table->timestamp('created_at');
                $table->timestamp('updated_at');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('websockets_statistics_entries');
        Schema::dropIfExists('users');
        Schema::dropIfExists('ui_settings');
        Schema::dropIfExists('students');
        Schema::dropIfExists('student_progress');
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('student_batches');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('professors');
        Schema::dropIfExists('professor_program');
        Schema::dropIfExists('professor_batch');
        Schema::dropIfExists('plan');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_method_fields');
        Schema::dropIfExists('payment_history');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('package_modules');
        Schema::dropIfExists('package_courses');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('module_completions');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('meeting_attendance_logs');
        Schema::dropIfExists('form_requirements');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('enrollment_courses');
        Schema::dropIfExists('education_levels');
        Schema::dropIfExists('directors');
        Schema::dropIfExists('director_program');
        Schema::dropIfExists('deadlines');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_completions');
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('content_completions');
        Schema::dropIfExists('class_meetings');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('board_passers');
        Schema::dropIfExists('batch_professors');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('activities');
    }
}
