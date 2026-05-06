<?php

namespace App\Policies;

use App\Models\Lelang;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LelangPolicy
{
    public function view(User $user, Lelang $lelang)
    {
        if ($user->isAdminPusat()) return true;

        return $user->satker_id === $lelang->barang->perkara->pengajuan->satker_id;
    }

    public function create(User $user)
    {
        return $user->isAdminPusat();
    }

    public function update(User $user)
    {
        return $user->isAdminPusat();
    }

    public function close(User $user)
    {
        return $user->isAdminPusat();
    }
}
