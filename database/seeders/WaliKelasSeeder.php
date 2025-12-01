<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaliKelas;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        WaliKelas::updateOrCreate(
            ['id_pengguna' => 3],
            ['nuptk' => '1234567890', 'jabatan' => 'Wali Kelas']
        );
    }
}
