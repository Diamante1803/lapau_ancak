<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Barang;

class FotoBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'file_path'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
