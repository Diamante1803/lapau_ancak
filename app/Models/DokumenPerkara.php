<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Perkara;

class DokumenPerkara extends Model
{
    use HasFactory;

    protected $fillable = [
        'perkara_id',
        'nama_dokumen',
        'file_path'
    ];

    public function perkara()
    {
        return $this->belongsTo(Perkara::class);
    }
}
