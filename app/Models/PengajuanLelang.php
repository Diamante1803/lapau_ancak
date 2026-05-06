<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Satker;
use App\Models\DokumenPengajuan;
use App\Models\Perkara;

class PengajuanLelang extends Model
{
    use HasFactory;

    protected $fillable = [
        'satker_id',
        'judul_pengajuan',
        'status',
        'catatan_revisi',
        'tanggal_pengajuan'
    ];

    protected $casts = [
        'catatan_revisi'    => 'array',
        'tanggal_pengajuan' => 'datetime',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }

    public function dokumenPengajuan()
    {
        return $this->hasMany(DokumenPengajuan::class, 'pengajuan_lelang_id');
    }

    public function perkaras()
    {
        return $this->hasMany(Perkara::class, 'pengajuan_lelang_id');
    }

}
