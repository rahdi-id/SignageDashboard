<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add last seen timestamps to conversations table.
     *
     * guest_last_seen_at — diupdate setiap kali guest hit polling endpoint
     * admin_last_seen_at — diupdate setiap kali admin hit polling endpoint
     *
     * NULL berarti belum pernah aktif sejak kolom ini dibuat.
     * <= 15 detik yang lalu → dianggap Online
     * > 15 detik yang lalu → Last seen X minutes/hours ago
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->timestamp('guest_last_seen_at')->nullable()->after('priority');
            $table->timestamp('admin_last_seen_at')->nullable()->after('guest_last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['guest_last_seen_at', 'admin_last_seen_at']);
        });
    }
};
