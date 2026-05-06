<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Satker;
use App\Models\AuditLog;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // app/Models/User.php
    protected $fillable = [
        'name',
        'username',
        'email',
        'kontak',
        'password',
        'role',
        'satker_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Helper
    public function isAdminPusat()
    {
        return $this->role === 'admin_pusat';
    }

    public function isAdminSatker()
    {
        return $this->role === 'admin_satker';
    }
}
