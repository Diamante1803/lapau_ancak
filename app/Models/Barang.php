<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Perkara;
use App\Models\FotoBarang;
use App\Models\Lelang;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'perkara_id',
        'nama_barang',
        'deskripsi',
        'catatan_internal',
        'harga_awal',
        'status'
    ];

    protected $casts = [
        'harga_awal' => 'decimal:2'
    ];

    public function perkara()
    {
        return $this->belongsTo(Perkara::class);
    }

    public function fotoBarang()
    {
        return $this->hasMany(FotoBarang::class);
    }

    public function lelang()
    {
        return $this->hasOne(Lelang::class);
    }
}
