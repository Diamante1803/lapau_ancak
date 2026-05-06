<?php

namespace App\Policies;

use App\Models\Perkara;
use App\Models\User;
use App\Models\PengajuanLelang;

use Illuminate\Auth\Access\Response;

class PerkaraPolicy
{
    public function view(User $user, Perkara $perkara)
    {
        if ($user->isAdminPusat()) return true;

        return $user->satker_id === $perkara->pengajuan->satker_id;
    }

    public function create(User $user, PengajuanLelang $pengajuan)
    {
        return $user->satker_id === $pengajuan->satker_id;
    }
}
