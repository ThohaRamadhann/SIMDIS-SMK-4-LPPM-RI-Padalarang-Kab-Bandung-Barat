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

            // ADMIN
            [
                'id_pengguna' => 1,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'id_role' => 1,
                'password' => Hash::make('password')
            ],

            // GURU BK
            [
                'id_pengguna' => 2,
                'name' => 'Guru BK',
                'email' => 'bk@example.com',
                'username' => 'bk',
                'id_role' => 2,
                'password' => Hash::make('password')
            ],

            // WALI KELAS 1
            [
                'id_pengguna' => 3,
                'name' => 'Wali Kelas',
                'email' => 'walikelas1@example.com',
                'username' => 'walikelas1',
                'id_role' => 3,
                'password' => Hash::make('password')
            ],

            // WALI KELAS 2
            [
                'id_pengguna' => 4,
                'name' => 'Wali Kelas 2',
                'email' => 'walikelas2@example.com',
                'username' => 'walikelas2',
                'id_role' => 3,
                'password' => Hash::make('password')
            ],

            // ORANG TUA 1
            [
                'id_pengguna' => 5,
                'name' => 'Orang Tua Siswa 1',
                'email' => 'ortu1@example.com',
                'username' => 'ortu1',
                'id_role' => 4,
                'password' => Hash::make('password')
            ],

            // ORANG TUA 2
            [
                'id_pengguna' => 6,
                'name' => 'Orang Tua Siswa 2',
                'email' => 'ortu2@example.com',
                'username' => 'ortu2',
                'id_role' => 4,
                'password' => Hash::make('password')
            ],
        ];

        foreach ($users as $user) {
            Pengguna::updateOrCreate(
                ['id_pengguna' => $user['id_pengguna']],
                $user
            );
        }
    }
}