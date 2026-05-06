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
        Schema::create('laporan_lelangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lelang_id')->constrained()->onDelete('cascade');
            $table->foreignId('satker_id')->constrained('satkers')->onDelete('cascade');

            // Nomor dokumen
            $table->string('nomor_bast')->nullable();
            $table->string('nomor_billing')->nullable();

            // File upload
            $table->string('file_bast')->nullable();       // PDF BAST
            $table->string('file_bukti_bayar')->nullable(); // PDF/JPG bukti bayar

            // Tanggal
            $table->date('tanggal_bast')->nullable();
            $table->date('tanggal_bayar')->nullable();

            // Status kelengkapan
            $table->enum('status', ['belum_lengkap', 'lengkap'])
                ->default('belum_lengkap');

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_lelangs');
    }
};
