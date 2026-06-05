<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\WaliMurid;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::all();
        $wali = WaliMurid::all();

        for($i=1;$i<=50;$i++){

            Siswa::create([
                'id_walimurid' => $wali[$i-1]->id_walimurid,
                'id_kelas' => $kelas[($i-1) % $kelas->count()]->id_kelas,
                'nama' => "Siswa $i",
                'nis' => '2025'.str_pad($i,4,'0',STR_PAD_LEFT),
                'status' => 'aktif'
            ]);
        }
    }
}