<?php

namespace App\Imports;

use App\Models\Pengguna;
use App\Models\WaliKelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class WaliKelasImport implements
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

            $alreadyWaliKelas = WaliKelas::where('id_pengguna', $pengguna->id_pengguna)->exists();
            if ($alreadyWaliKelas) {
                $this->errors[] = "Baris " . ($index + 2) . ": Pengguna '{$row['username_pengguna']}' sudah terdaftar sebagai wali kelas.";
                continue;
            }

            WaliKelas::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'nuptk'       => $row['nuptk'] ?? null,
                'jabatan'     => $row['jabatan'] ?? null,
            ]);

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'username_pengguna' => 'required|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'username_pengguna.required' => 'Kolom username_pengguna wajib diisi.',
        ];
    }
}