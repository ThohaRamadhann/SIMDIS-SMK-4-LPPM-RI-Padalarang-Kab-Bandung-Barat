<?php

namespace App\Imports;

use App\Models\Pengguna;
use App\Models\WaliSiswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class WaliSiswaImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    public array $errors = [];
    public int   $imported = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $pengguna = Pengguna::where('username', $row['username_pengguna'])->first();

            if (!$pengguna) {
                $this->errors[] = "Baris " . ($index + 2) . ": Pengguna dengan username '{$row['username_pengguna']}' tidak ditemukan.";
                continue;
            }

            $alreadyWaliSiswa = WaliSiswa::where('id_pengguna', $pengguna->id_pengguna)->exists();
            if ($alreadyWaliSiswa) {
                $this->errors[] = "Baris " . ($index + 2) . ": Pengguna '{$row['username_pengguna']}' sudah terdaftar sebagai wali siswa.";
                continue;
            }

            WaliSiswa::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'hubungan'    => $row['hubungan'],
            ]);

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'username_pengguna' => 'required|string',
            'hubungan'          => 'required|in:ayah,ibu,wali',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'username_pengguna.required' => 'Kolom username_pengguna wajib diisi.',
            'hubungan.required'          => 'Kolom hubungan wajib diisi.',
            'hubungan.in'                => 'Hubungan harus salah satu dari: ayah, ibu, wali.',
        ];
    }
}