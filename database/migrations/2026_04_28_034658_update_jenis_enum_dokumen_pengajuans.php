<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (DB::getDriverName() !== 'sqlite') {
        DB::statement("ALTER TABLE dokumen_pengajuans 
            MODIFY COLUMN jenis 
            ENUM('sk_panitia','izin_penjualan','surat_penetapan_harga') 
            NOT NULL");
        }
    }

    public function down()
    {
        if (DB::getDriverName() !== 'sqlite') {
        DB::statement("ALTER TABLE dokumen_pengajuans 
            MODIFY COLUMN jenis 
            ENUM('sk_panitia','izin_penjualan') 
            NOT NULL");
        }
    }
};
