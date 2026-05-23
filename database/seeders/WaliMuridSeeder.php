<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaliMurid;

class WaliMuridSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_pengguna' => 5,
                'hubungan' => 'Ayah',
            ],
            [
                'id_pengguna' => 6,
                'hubungan' => 'Ibu',
            ],
        ];

        foreach ($data as $index => $wali) {
            WaliMurid::updateOrCreate(
                ['id_pengguna' => $wali['id_pengguna']],
                [
                    'hubungan' => $wali['hubungan']
                ]
            );
        }
    }
}