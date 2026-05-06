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
        Schema::create('pengajuan_lelangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satker_id')->constrained()->cascadeOnDelete();
            $table->string('judul_pengajuan');
            $table->enum('status', ['draft', 'submitted', 'revision', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_revisi')->nullable();
            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamps();
            
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_lelangs');
    }
};
