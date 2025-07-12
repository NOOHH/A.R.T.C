<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void 
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add soft deletes only if column doesn't exist
            if (!Schema::hasColumn('messages', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Add indexes for performance
            $table->index(['sender_id', 'receiver_id', 'created_at'], 'messages_sender_receiver_created_idx');
            $table->index(['receiver_id', 'is_read'], 'messages_receiver_read_idx');
            $table->index('created_at', 'messages_created_at_idx');
            $table->index('deleted_at', 'messages_deleted_at_idx');
        });
    }

    public function down(): void 
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            $table->dropIndex('messages_sender_receiver_created_idx');
            $table->dropIndex('messages_receiver_read_idx');
            $table->dropIndex('messages_created_at_idx');
            $table->dropIndex('messages_deleted_at_idx');
        });
    }
};
