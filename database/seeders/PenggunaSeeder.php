<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['id_pengguna' => 1, 'name' => 'Admin', 'email' => 'admin@example.com', 'username' => 'admin', 'id_role' => 1, 'password' => Hash::make('password')],
            ['id_pengguna' => 2, 'name' => 'Guru BK', 'email' => 'bk@example.com', 'username' => 'bk', 'id_role' => 2, 'password' => Hash::make('password')],
            ['id_pengguna' => 3, 'name' => 'Wali Kelas', 'email' => 'walikelas@example.com', 'username' => 'walikelas', 'id_role' => 3, 'password' => Hash::make('password')],
            ['id_pengguna' => 4, 'name' => 'Orang Tua', 'email' => 'ortu@example.com', 'username' => 'ortu', 'id_role' => 4, 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            Pengguna::updateOrCreate(['id_pengguna' => $user['id_pengguna']], $user);
        }
    }
}
