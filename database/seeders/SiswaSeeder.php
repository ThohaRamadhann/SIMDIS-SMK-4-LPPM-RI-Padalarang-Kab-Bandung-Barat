<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\WaliMurid;
use App\Models\Kelas;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $wali = WaliMurid::all();
        $kelas = Kelas::all();

        if ($wali->count() < 2 || $kelas->count() < 2) {
            $this->command->warn('Wali murid atau kelas belum cukup untuk seeder siswa.');
            return;
        }

        $data = [
            [
                'nama' => 'Siswa A',
                'nis' => '1111',
                'id_walimurid' => $wali->get(0)->id_walimurid,
                'id_kelas' => $kelas->get(0)->id_kelas,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Siswa B',
                'nis' => '2222',
                'id_walimurid' => $wali->get(0)->id_walimurid,
                'id_kelas' => $kelas->get(0)->id_kelas,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Siswa C',
                'nis' => '3333',
                'id_walimurid' => $wali->get(1)->id_walimurid,
                'id_kelas' => $kelas->get(1)->id_kelas,
                'status' => 'aktif',
            ],
        ];

        foreach ($data as $i => $siswa) {
            Siswa::updateOrCreate(
                ['nis' => $siswa['nis']],
                $siswa
            );
        }
    }
}