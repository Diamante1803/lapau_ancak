<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Penawaran;

class Pembeli extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email', 
        'no_hp',
        'verified_at',
        'magic_token',
        'token_expired_at',
    ];

    protected $casts = [
        'verified_at'      => 'datetime',
        'token_expired_at' => 'datetime',
    ];

    public function penawarans()
    {
        return $this->hasMany(Penawaran::class);
    }

    // Helper: cek verified hari ini
    public function isVerifiedToday()
    {
        return $this->verified_at && $this->verified_at->isToday();
    }
}
