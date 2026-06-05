<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\WaliKelas;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        $waliKelas = Pengguna::where('id_role',3)->get();

        foreach($waliKelas as $wk){

            WaliKelas::create([
                'id_pengguna' => $wk->id_pengguna,
                'nuptk' => 'NUPTK'.str_pad($wk->id_pengguna,6,'0',STR_PAD_LEFT),
                'jabatan' => 'Wali Kelas'
            ]);
        }
    }
}