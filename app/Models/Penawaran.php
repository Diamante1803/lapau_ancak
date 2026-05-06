<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Lelang;
use App\Models\Pembeli;

class Penawaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'lelang_id',
        'pembeli_id',
        'nilai_penawaran'
    ];

    protected $casts = [
        'nilai_penawaran' => 'decimal:2'
    ];

    public function lelang()
    {
        return $this->belongsTo(Lelang::class);
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class);
    }
}
