<?php

namespace App\Imports;

use App\Models\Pengguna;
use App\Models\Role;
use App\Models\WaliMurid;
use App\Models\WaliKelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class PenggunaImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    public array $errors   = [];
    public int   $imported = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // Normalisasi role
            $roleName = strtolower(trim($row['role']));
            $roleName = str_replace(' ', '_', $roleName);

            // Cari role
            $role = Role::where('nama_role', $roleName)->first();
            if (!$role) {
                $this->errors[] = "Baris " . ($index + 2) . ": Role '{$row['role']}' tidak ditemukan.";
                continue;
            }

            // Cek username duplikat
            if (Pengguna::where('username', $row['username'])->exists()) {
                $this->errors[] = "Baris " . ($index + 2) . ": Username '{$row['username']}' sudah digunakan.";
                continue;
            }

            // Normalisasi nomor telepon
            $phone = isset($row['no_telpon'])
                ? preg_replace('/[^0-9]/', '', (string) $row['no_telpon'])
                : null;

            if ($phone) {
                if (substr($phone, 0, 1) === '0') {
                    $phone = '62' . substr($phone, 1);
                }
                if (!str_starts_with($phone, '62')) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Nomor telepon harus diawali 62 atau 0.";
                    continue;
                }
                if (strlen($phone) < 10 || strlen($phone) > 15) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Nomor telepon tidak valid.";
                    continue;
                }
            }

            // Simpan pengguna
            $user = Pengguna::create([
                'id_role'   => $role->id_role,
                'name'      => $row['name'],       // ← sesuai kolom DB
                'username'  => $row['username'],
                'email'     => $row['email'] ?? null,
                'no_telpon' => $phone,
                'password'  => Hash::make($row['password']),
            ]);

            // Role: orang_tua
            if ($role->nama_role === 'orang_tua') {
                if (empty($row['hubungan'])) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Hubungan wajib diisi untuk role orang tua.";
                    $user->delete();
                    continue;
                }
                WaliMurid::create([
                    'id_pengguna' => $user->id_pengguna,
                    'hubungan'    => trim($row['hubungan']),
                ]);
            }

            // Role: guru_bk / wali_kelas
            if (in_array($role->nama_role, ['guru_bk', 'wali_kelas'])) {
                WaliKelas::create([
                    'id_pengguna' => $user->id_pengguna,
                    'nuptk'       => isset($row['nuptk']) ? (string) $row['nuptk'] : null,
                    'jabatan'     => $row['jabatan'] ?? null,
                ]);
            }

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',  // ← sesuai kolom DB
            'username'  => 'required|string|max:255',
            'role'      => 'required|string',
            'password'  => 'required|string|min:6',
            'email'     => 'nullable|email',
            'no_telpon' => 'nullable',
            'hubungan'  => 'nullable|string',
            'nuptk'     => 'nullable|max:30',
            'jabatan'   => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required'     => 'Kolom name wajib diisi.',
            'username.required' => 'Kolom username wajib diisi.',
            'role.required'     => 'Kolom role wajib diisi.',
            'password.required' => 'Kolom password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'email.email'       => 'Format email tidak valid.',
            'nuptk.max'         => 'NUPTK terlalu panjang.',
        ];
    }
}