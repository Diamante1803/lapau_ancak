<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satker;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SatkerSeeder extends Seeder
{
    public function run(): void
    {
        $satkers = [
            'Kejaksaan Negeri Padang',
            'Kejaksaan Negeri Padang Panjang',
            'Kejaksaan Negeri Pariaman',
            'Kejaksaan Negeri Bukittinggi',
            'Kejaksaan Negeri Payakumbuh',
            'Kejaksaan Negeri Solok',
            'Kejaksaan Negeri Pesisir Selatan',
            'Kejaksaan Negeri Pasaman',
            'Kejaksaan Negeri Pasaman Barat',
            'Kejaksaan Negeri Agam',
            'Kejaksaan Negeri Dharmasraya',
            'Kejaksaan Negeri Sijunjung',
            'Kejaksaan Negeri Solok Selatan',
            'Kejaksaan Negeri Sawahlunto',
            'Kejaksaan Negeri Tanah Datar',
            'Cabang Kejaksaan Negeri Payakumbuh di Pangkalan Kotobaru',
            'Cabang Kejaksaan Negeri Payakumbuh di Suliki',
            'Cabang Kejaksaan Negeri Solok di Alahan Panjang',
            'Cabang Kejaksaan Negeri Agam di Maninjau',
            'Cabang Kejaksaan Negeri Pasaman Barat di Air Bangis',
            'Cabang Kejaksaan Negeri Pesisir Selatan di Balai Selasa',
        ];

        foreach ($satkers as $index => $namaSatker) {

            // 1. Buat user admin untuk satker ini
            $adminUser = User::create([
                'name'     => $namaSatker,
                'email'    => 'admin.satker' . ($index + 1) . '@lelang.test',
                'password' => Hash::make('adminsatker'),
                'role'     => 'admin_satker', // sesuaikan dengan kolom role di tabel users
            ]);

            // 2. Buat satker dan hubungkan ke admin user
            Satker::create([
                'nama_satker'  => $namaSatker,
                'alamat'       => 'Jl. Contoh No. ' . (($index + 1) * 10) . ', Indonesia',
                'admin_user_id' => $adminUser->id,
            ]);
        }
    }
}