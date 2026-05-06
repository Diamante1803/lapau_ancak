<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Users;
use App\Models\PengajuanLelang;

class Satker extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_satker',
        'alamat',
        'admin_user_id'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function pengajuans()
    {
        return $this->hasMany(PengajuanLelang::class);
    }
}
