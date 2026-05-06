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
        Schema::table('pengajuan_lelangs', function (Blueprint $table) {
            $table->json('catatan_revisi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_lelangs', function (Blueprint $table) {
            $table->text('catatan_revisi')->nullable()->change();
        });
    }
};
