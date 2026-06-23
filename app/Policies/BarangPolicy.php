<?php

namespace App\Policies;

use App\Models\Barang;
use App\Models\User;
use App\Models\Perkara;
use Illuminate\Auth\Access\Response;

class BarangPolicy
{
    public function view(User $user, Barang $barang)
    {
        if ($user->isAdminPusat()) return true;

        return $user->satker_id === $barang->perkara->pengajuan->satker_id;
    }

    public function create(User $user, Perkara $perkara)
    {
        return $user->satker_id === $perkara->pengajuan->satker_id
            && in_array($perkara->pengajuan->status, ['draft', 'revision']);
    }

    public function update(User $user, Barang $barang)
    {
        if ($user->isAdminPusat()) return true;

        return $user->satker_id === $barang->perkara->pengajuan->satker_id
            && in_array($barang->perkara->pengajuan->status, ['draft', 'revision']);
    }
}
