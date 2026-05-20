<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Satker;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Pusat
        |--------------------------------------------------------------------------
        */
        User::updateOrCreate(
            ['email' => 'adminpusat@lapauancak.id'],
            [
                'name'      => 'Administrator Pusat',
                'username'  => 'adminpusat',
                'email'     => 'adminpusat@lapauancak.id',
                'kontak'    => '081234567890',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin_pusat',
                'satker_id' => null,
            ]
        );
    }
}