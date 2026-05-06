<?php
// app/Models/LaporanLelang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanLelang extends Model
{
    protected $table = 'laporan_lelangs';

    protected $fillable = [
        'lelang_id',
        'satker_id',
        'nomor_bast',
        'nomor_billing',
        'file_bast',
        'file_bukti_bayar',
        'tanggal_bast',
        'tanggal_bayar',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bast'  => 'date',
        'tanggal_bayar' => 'date',
    ];

    public function lelang()
    {
        return $this->belongsTo(Lelang::class);
    }

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }

    // Helper: cek apakah laporan sudah lengkap
    public function isLengkap(): bool
    {
        return $this->file_bast && $this->file_bukti_bayar;
    }
}