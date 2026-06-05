<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\WaliKelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            'RPL'  => 'Rekayasa Perangkat Lunak',
            'TKJ'  => 'Teknik Komputer Jaringan',
            'TBSM' => 'Teknik Bisnis Sepeda Motor',
            'APH'  => 'Akomodasi Perhotelan'
        ];

        $tingkat = ['X','XI','XII'];

        $waliKelas = WaliKelas::all();

        $index = 0;

        foreach($tingkat as $t){

            foreach($jurusan as $kode => $namaJurusan){

                for($i=1;$i<=2;$i++){

                    Kelas::create([
                        'id_walikelas' => $waliKelas[$index]->id_walikelas,
                        'tingkat' => $t,
                        'nama_kelas' => "$t $kode $i",
                        'jurusan' => $namaJurusan,
                        'tahun_ajaran' => '2025/2026'
                    ]);

                    $index++;
                }
            }
        }
    }
}