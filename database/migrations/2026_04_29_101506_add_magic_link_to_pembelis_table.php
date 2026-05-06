<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelis', function (Blueprint $table) {
            $table->string('magic_token', 64)->nullable()->after('no_hp');
            $table->timestamp('token_expired_at')->nullable()->after('magic_token');
        });
    }

    public function down(): void
    {
        Schema::table('pembelis', function (Blueprint $table) {
            $table->dropColumn(['magic_token', 'token_expired_at']);
        });
    }
};