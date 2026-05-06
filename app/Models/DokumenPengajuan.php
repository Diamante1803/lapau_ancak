<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PengajuanLelang;

class DokumenPengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_lelang_id',
        'jenis',
        'file_path'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanLelang::class, 'pengajuan_lelang_id');
    }
}
