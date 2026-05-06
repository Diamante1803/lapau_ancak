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
        Schema::create('lelangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained()->cascadeOnDelete();
            $table->timestamp('tanggal_mulai');
            $table->timestamp('tanggal_selesai')->nullable();
            $table->enum('status', ['scheduled', 'active', 'closed', 'cancelled'])->default('scheduled');
            $table->decimal('harga_awal', 15, 2);
            $table->decimal('harga_tertinggi', 15, 2)->nullable();
            $table->foreignId('pemenang_id')->nullable()->constrained('pembelis')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lelangs');
    }
};
