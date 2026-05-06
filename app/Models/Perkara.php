<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PengajuanLelang;
use App\Models\DokumenPerkara;
use App\Models\Barang;

class Perkara extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_lelang_id',
        'nomor_perkara',
        'nama_tersangka',
        'tanggal_putusan'
    ];

    protected $casts = [
        'tanggal_putusan' => 'date'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanLelang::class, 'pengajuan_lelang_id');
    }

    public function dokumenPerkara()
    {
        return $this->hasMany(DokumenPerkara::class, 'perkara_id');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'perkara_id'); 
    }
}
