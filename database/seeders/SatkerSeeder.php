<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('satkers')->insert([
            ['name' => 'Kejaksaan Jakarta', 'code' => 'JKT01'],
            ['name' => 'Kejaksaan Bandung', 'code' => 'BDG01'],
        ]);
    }
    
}