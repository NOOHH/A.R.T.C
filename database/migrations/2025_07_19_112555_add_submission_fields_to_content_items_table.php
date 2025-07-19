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
        Schema::table('content_items', function (Blueprint $table) {
            $table->boolean('enable_submission')->default(false)->after('is_active');
            $table->string('allowed_file_types')->nullable()->after('enable_submission');
            $table->integer('max_file_size')->default(10240)->after('allowed_file_types'); // Size in KB, default 10MB
            $table->text('submission_instructions')->nullable()->after('max_file_size');
            $table->boolean('allow_multiple_submissions')->default(false)->after('submission_instructions');
            $table->string('content_url')->nullable()->after('attachment_path'); // For link type content
            $table->integer('sort_order')->default(1)->after('content_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropColumn([
                'enable_submission',
                'allowed_file_types',
                'max_file_size',
                'submission_instructions',
                'allow_multiple_submissions',
                'content_url',
                'sort_order'
            ]);
        });
    }
};
