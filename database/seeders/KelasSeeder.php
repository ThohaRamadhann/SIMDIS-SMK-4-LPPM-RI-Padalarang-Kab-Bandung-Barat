<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\WaliKelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $waliKelas = WaliKelas::all();

        // Pastikan minimal ada 2 wali kelas
        if ($waliKelas->count() < 2) {
            $this->command->warn('Minimal harus ada 2 data wali kelas.');
            return;
        }

        $dataKelas = [
            [
                'id_walikelas' => $waliKelas[0]->id_walikelas,
                'tingkat' => 'X',
                'nama_kelas' => 'X RPL 1',
                'jurusan' => 'RPL',
                'tahun_ajaran' => '2025/2026',
            ],
            [
                'id_walikelas' => $waliKelas[1]->id_walikelas,
                'tingkat' => 'XI',
                'nama_kelas' => 'XI TKJ 1',
                'jurusan' => 'TKJ',
                'tahun_ajaran' => '2025/2026',
            ],
        ];

        foreach ($dataKelas as $kelas) {
            Kelas::updateOrCreate(
                ['nama_kelas' => $kelas['nama_kelas']],
                $kelas
            );
        }
    }
}