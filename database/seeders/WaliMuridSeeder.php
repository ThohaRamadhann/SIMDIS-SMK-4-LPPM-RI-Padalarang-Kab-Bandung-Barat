<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaliMurid;

class WaliMuridSeeder extends Seeder
{
    public function run(): void
    {
        WaliMurid::updateOrCreate(
            ['id_walimurid' => 1],
            [
                'id_pengguna' => 4,  
                'hubungan' => 'orang tua'
            ]
        );
    }
}
