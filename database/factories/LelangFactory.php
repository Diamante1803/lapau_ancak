<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Lelang;
use App\Models\PengajuanLelang;
use App\Models\Perkara;
use App\Models\Satker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lelang>
 */
class LelangFactory extends Factory
{
    protected $model = Lelang::class;

    public function definition(): array
    {
        return [
            'barang_id' => function () {
                $satker = Satker::create([
                    'nama_satker' => fake()->company(),
                    'alamat' => fake()->address(),
                ]);

                $pengajuan = PengajuanLelang::create([
                    'satker_id' => $satker->id,
                    'judul_pengajuan' => fake()->sentence(4),
                    'status' => 'approved',
                    'tanggal_pengajuan' => now(),
                ]);

                $perkara = Perkara::create([
                    'pengajuan_lelang_id' => $pengajuan->id,
                    'nomor_perkara' => fake()->bothify('###/Pid.B/####/PN.???'),
                    'nama_tersangka' => fake()->name(),
                    'tanggal_putusan' => now()->subMonth()->toDateString(),
                ]);

                return Barang::create([
                    'perkara_id' => $perkara->id,
                    'nama_barang' => fake()->words(3, true),
                    'deskripsi' => fake()->sentence(),
                    'harga_awal' => 500000,
                    'status' => 'in_auction',
                ])->id;
            },
            'tanggal_mulai' => now()->subHour(),
            'tanggal_selesai' => now()->addHour(),
            'status' => 'scheduled',
            'harga_awal' => 500000,
            'harga_tertinggi' => null,
            'pemenang_id' => null,
        ];
    }
}
