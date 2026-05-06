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
        Schema::table('lelangs', function (Blueprint $table) {
            $table->tinyInteger('pemenang_urutan')->default(1)->after('pemenang_id');
            $table->text('catatan_pemenang')->nullable()->after('pemenang_urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lelangs', function (Blueprint $table) {
            //
        });
    }
};
