<?php

namespace App\Imports;

use App\Models\Pengguna;
use App\Models\Role;
use App\Models\WaliSiswa;
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
            $rowNum   = $index + 2;
            $rowArray = $row->toArray();

            // ── Role ──
            $roleName = strtolower(trim($row['role'] ?? ''));
            $roleName = str_replace(' ', '_', $roleName);

            $role = Role::where('nama_role', $roleName)->first();
            if (! $role) {
                $this->errors[] = "Baris {$rowNum}: Role '{$row['role']}' tidak ditemukan.";
                continue;
            }

            // ── Username unik ──
            if (Pengguna::where('username', $row['username'])->exists()) {
                $this->errors[] = "Baris {$rowNum}: Username '{$row['username']}' sudah digunakan.";
                continue;
            }

            // ── Email unik (kalau diisi) ──
            if (! empty($row['email'])) {
                if (! filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = "Baris {$rowNum}: Format email tidak valid.";
                    continue;
                }
                if (Pengguna::where('email', $row['email'])->exists()) {
                    $this->errors[] = "Baris {$rowNum}: Email '{$row['email']}' sudah digunakan oleh akun lain.";
                    continue;
                }
            }

            // ── Nomor telepon ──
            $phone = isset($row['no_telpon'])
                ? preg_replace('/[^0-9]/', '', (string) $row['no_telpon'])
                : null;

            if ($phone) {
                if (substr($phone, 0, 1) === '0') {
                    $phone = '62' . substr($phone, 1);
                }
                if (! str_starts_with($phone, '62')) {
                    $this->errors[] = "Baris {$rowNum}: Nomor telepon harus diawali 62 atau 0.";
                    continue;
                }
                if (strlen($phone) < 10 || strlen($phone) > 15) {
                    $this->errors[] = "Baris {$rowNum}: Nomor telepon tidak valid.";
                    continue;
                }
            }

            // ── Validasi khusus wali_kelas ──
            if ($roleName === 'wali_kelas') {
                $nuptk = preg_replace('/[^0-9]/', '', (string) ($row['nuptk'] ?? ''));
                if (empty($nuptk)) {
                    $this->errors[] = "Baris {$rowNum}: Kolom NUPTK wajib diisi untuk role Wali Kelas.";
                    continue;
                }
                if (strlen($nuptk) !== 16) {
                    $this->errors[] = "Baris {$rowNum}: NUPTK harus berupa angka 16 digit.";
                    continue;
                }
                if (WaliKelas::where('nuptk', $nuptk)->exists()) {
                    $this->errors[] = "Baris {$rowNum}: NUPTK '{$nuptk}' sudah terdaftar pada pengguna lain.";
                    continue;
                }
                if (empty($row['jabatan'])) {
                    $this->errors[] = "Baris {$rowNum}: Kolom jabatan wajib diisi untuk role Wali Kelas.";
                    continue;
                }
            }

            // ── Validasi khusus wali_siswa ──
            if ($roleName === 'wali_siswa') {
                if (empty($row['hubungan'])) {
                    $this->errors[] = "Baris {$rowNum}: Kolom hubungan wajib diisi untuk role Wali Siswa.";
                    continue;
                }
            }

            // ── Buat pengguna ──
            $user = Pengguna::create([
                'id_role'   => $role->id_role,
                'name'      => $row['name'],
                'username'  => $row['username'],
                'email'     => ! empty($row['email']) ? $row['email'] : null,
                'no_telpon' => $phone,
                'password'  => Hash::make($row['password']),
            ]);

            // ── Buat entri wali_siswa ──
            if ($roleName === 'wali_siswa') {
                WaliSiswa::create([
                    'id_pengguna' => $user->id_pengguna,
                    'hubungan'    => trim($row['hubungan']),
                ]);
            }

            // ── Buat entri wali_kelas / guru_bk ──
            if (in_array($roleName, ['guru_bk', 'wali_kelas'])) {
                $nuptk = preg_replace('/[^0-9]/', '', (string) ($row['nuptk'] ?? ''));
                WaliKelas::create([
                    'id_pengguna' => $user->id_pengguna,
                    'nuptk'       => $nuptk ?: null,
                    'jabatan'     => $row['jabatan'] ?? null,
                ]);
            }

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255',
            'role'      => 'required|string',
            'password'  => 'required|string|min:6',
            'email'     => 'nullable|email|max:255',
            'no_telpon' => 'nullable',
            'hubungan'  => 'nullable|string|max:50',
            'nuptk'     => 'nullable',
            'jabatan'   => 'nullable|string|max:100',
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
        ];
    }
}