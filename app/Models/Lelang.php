<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Barang;
use App\Models\Penawaran;
use App\Models\Pembeli;
use App\Models\BeritaAcaraSerahTerima;

class Lelang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'harga_awal',
        'harga_tertinggi',
        'pemenang_id'
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'harga_awal' => 'decimal:2',
        'harga_tertinggi' => 'decimal:2'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function penawarans()
    {
        return $this->hasMany(Penawaran::class);
    }

    public function pemenang()
    {
        return $this->belongsTo(Pembeli::class, 'pemenang_id');
    }

    // Helper: ambil penawaran tertinggi
    public function penawaranTertinggi()
    {
        return $this->penawarans()->orderByDesc('nilai_penawaran')->first();
    }

    public function laporan()
    {
        return $this->hasOne(LaporanLelang::class);
    }   
}
