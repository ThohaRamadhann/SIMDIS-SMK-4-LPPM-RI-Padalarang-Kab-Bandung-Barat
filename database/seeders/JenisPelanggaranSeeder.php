<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPelanggaran;

class JenisPelanggaranSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            ['id_jenispelanggaran' => 1, 'nama_pelanggaran' => 'Terlambat',              'tingkat_pelanggaran' => 'Ringan'],
            ['id_jenispelanggaran' => 2, 'nama_pelanggaran' => 'Bolos',                  'tingkat_pelanggaran' => 'Sedang'],
            ['id_jenispelanggaran' => 3, 'nama_pelanggaran' => 'Membawa senjata tajam',  'tingkat_pelanggaran' => 'Berat'],
        ];

        foreach ($jenis as $j) {
            JenisPelanggaran::updateOrCreate(
                ['id_jenispelanggaran' => $j['id_jenispelanggaran']],
                $j
            );
        }
    }
}