<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\WaliSiswa;

class WaliSiswaSeeder extends Seeder
{
    public function run(): void
    {
        $ortu = Pengguna::where('id_role', 4)->get();

        foreach ($ortu as $item) {
            WaliSiswa::create([
                'id_pengguna' => $item->id_pengguna,
                'hubungan'    => rand(0, 1) ? 'Ayah' : 'Ibu',
            ]);
        }
    }
}