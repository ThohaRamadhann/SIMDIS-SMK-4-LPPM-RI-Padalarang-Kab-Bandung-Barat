<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaliKelas;
use App\Models\Pengguna;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        $waliKelasUsers = Pengguna::where('id_role', 3)->get();

        foreach ($waliKelasUsers as $user) {
            WaliKelas::updateOrCreate(
                ['id_pengguna' => $user->id_pengguna],
                [
                    'nuptk' => 'NUPTK-' . $user->id_pengguna,
                    'jabatan' => 'Wali Kelas',
                ]
            );
        }
    }
}
