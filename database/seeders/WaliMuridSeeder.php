<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\WaliMurid;

class WaliMuridSeeder extends Seeder
{
    public function run(): void
    {
        $ortu = Pengguna::where('id_role',4)->get();

        foreach($ortu as $item){

            WaliMurid::create([
                'id_pengguna' => $item->id_pengguna,
                'hubungan' => rand(0,1) ? 'Ayah' : 'Ibu'
            ]);
        }
    }
}