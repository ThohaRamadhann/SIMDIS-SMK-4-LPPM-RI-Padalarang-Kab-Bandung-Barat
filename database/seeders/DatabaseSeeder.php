<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PenggunaSeeder::class,
            WaliKelasSeeder::class,
            WaliMuridSeeder::class,
            KelasSeeder::class, 
            SiswaSeeder::class,
            JenisPelanggaranSeeder::class,
        ]);
    }
}
