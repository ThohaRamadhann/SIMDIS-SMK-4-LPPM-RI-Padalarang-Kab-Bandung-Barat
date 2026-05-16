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
                'tingkat' => 'X',
                'nama_kelas' => 'X RPL ' . ($index + 1),
                'jurusan' => 'RPL',
                'tahun_ajaran' => '2025/2026',
            ]);
        }
    }
}

