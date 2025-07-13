<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Rename message column to body_cipher for encrypted content
            $table->renameColumn('message', 'body_cipher');
            
            // Add is_read column if it doesn't exist
            if (!Schema::hasColumn('chats', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('body_cipher');
            }
            
            // Add additional indexes for better performance
            $table->index(['sender_id', 'receiver_id', 'sent_at'], 'chats_conversation_index');
            $table->index(['receiver_id', 'is_read'], 'chats_unread_index');
            $table->index('sent_at', 'chats_sent_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Rename back to message
            $table->renameColumn('body_cipher', 'message');
            
            // Drop indexes
            $table->dropIndex('chats_conversation_index');
            $table->dropIndex('chats_unread_index');
            $table->dropIndex('chats_sent_at_index');
            
            // Drop is_read column if it exists
            if (Schema::hasColumn('chats', 'is_read')) {
                $table->dropColumn('is_read');
            }
        });
    }
};
