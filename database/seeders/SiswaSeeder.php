<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::updateOrCreate(
            ['id_siswa' => 1],
            [
                'nama' => 'Siswa A',
                'nis' => '1111',
                'id_walimurid' => 1, 
                'id_kelas' => 1,
                'status' => 'aktif'
            ]
        );

        Siswa::updateOrCreate(
            ['id_siswa' => 2],
            [
                'nama' => 'Siswa B',
                'nis' => '2222',
                'id_walimurid' => 1,
                'id_kelas' => 1,
                'status' => 'aktif'
            ]
        );
    }
}
