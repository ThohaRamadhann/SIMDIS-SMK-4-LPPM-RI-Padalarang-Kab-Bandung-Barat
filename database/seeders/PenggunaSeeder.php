<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::create([
            'id_role' => 1,
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);

        for ($i=1;$i<=2;$i++) {
            Pengguna::create([
                'id_role' => 2,
                'name' => "Guru BK $i",
                'username' => "bk$i",
                'email' => "bk$i@example.com",
                'password' => Hash::make('password')
            ]);
        }

        for ($i=1;$i<=38;$i++) {
            Pengguna::create([
                'id_role' => 3,
                'name' => "Wali Kelas $i",
                'username' => "walikelas$i",
                'email' => "walikelas$i@example.com",
                'password' => Hash::make('password')
            ]);
        }

        for ($i=1;$i<=50;$i++) {
            Pengguna::create([
                'id_role' => 4,
                'name' => "Orang Tua $i",
                'username' => "ortu$i",
                'email' => "ortu$i@example.com",
                'password' => Hash::make('password')
            ]);
        }
    }
}