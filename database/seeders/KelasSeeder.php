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

        foreach ($waliKelas as $index => $wali) {
            Kelas::create([
                'id_walikelas' => $wali->id_walikelas,
                'tingkat' => '10',
                'nama_kelas' => '10 Kelas ' . ($index + 1),
                'jurusan' => 'RPL',
                'tahun_ajaran' => '2024/2025',
            ]);
        }
    }
}

