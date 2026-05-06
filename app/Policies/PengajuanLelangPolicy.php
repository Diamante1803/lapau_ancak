<?php

namespace App\Policies;

use App\Models\PengajuanLelang;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengajuanLelangPolicy
{
    public function view(User $user, PengajuanLelang $pengajuan)
    {
        // ADMIN PUSAT
        if ($user->role === 'admin_pusat') {
            return in_array($pengajuan->status, ['submitted', 'approved', 'revision']);
        }

        // ADMIN SATKER
        if ($user->role === 'admin_satker') {
            return $pengajuan->satker_id === $user->satker_id;
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->isAdminSatker();
    }

    public function update(User $user, PengajuanLelang $pengajuan)
    {
        if ($user->isAdminPusat()) return true;

        return $user->satker_id === $pengajuan->satker_id
            && in_array($pengajuan->status, ['draft', 'revision']);
    }

    public function revisi(User $user, PengajuanLelang $pengajuan)
    {
        return $user->isAdminPusat() 
            && $pengajuan->status === 'submitted';
    }

    public function submit(User $user, PengajuanLelang $pengajuan)
    {
        return $user->satker_id === $pengajuan->satker_id;
    }

    public function approve(User $user, PengajuanLelang $pengajuan)
{
    return $user->isAdminPusat() 
        && $pengajuan->status === 'submitted';
}
}
