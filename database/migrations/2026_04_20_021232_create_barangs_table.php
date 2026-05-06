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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perkara_id')->constrained()->cascadeOnDelete();
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_awal', 15, 2);
            $table->enum('status', ['available', 'in_auction', 'sold', 'unsold'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
