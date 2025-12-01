<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_walikelas' => 1, 
                'tingkat' => '10',
                'nama_kelas' => '10 RPL 1',
                'jurusan' => 'RPL',
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'id_walikelas' => 2,
                'tingkat' => '10',
                'nama_kelas' => '10 TKJ 2',
                'jurusan' => 'RPL',
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'id_walikelas' => 3,
                'tingkat' => '11',
                'nama_kelas' => '11 Perhotelan 1',
                'jurusan' => 'DKV',
                'tahun_ajaran' => '2024/2025',
            ],
        ];

        foreach ($data as $row) {
            Kelas::create($row);
        }
    }
}
