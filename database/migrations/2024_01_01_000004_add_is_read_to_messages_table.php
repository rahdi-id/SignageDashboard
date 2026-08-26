<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add is_read column to messages table.
     *
     * is_read = false  → pesan Guest belum dibaca Admin
     * is_read = true   → Admin sudah membuka conversation ini
     *
     * Hanya pesan sender_type='guest' yang relevan untuk unread tracking.
     * Pesan sender_type='admin' selalu dianggap sudah dibaca.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('attachment');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};
