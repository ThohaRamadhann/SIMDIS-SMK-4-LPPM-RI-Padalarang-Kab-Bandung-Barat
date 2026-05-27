<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\WaliKelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class KelasImport implements
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
            $idWaliKelas = null;

            if (!empty($row['nuptk_walikelas'])) {
                $waliKelas = WaliKelas::where('nuptk', $row['nuptk_walikelas'])->first();
                if (!$waliKelas) {
                    $this->errors[] = "Baris " . ($index + 2) . ": Wali kelas dengan NUPTK '{$row['nuptk_walikelas']}' tidak ditemukan. Kelas dibuat tanpa wali kelas.";
                } else {
                    $idWaliKelas = $waliKelas->id_walikelas;
                }
            }

            $exists = Kelas::where('nama_kelas', $row['nama_kelas'])
                ->where('tahun_ajaran', $row['tahun_ajaran'])
                ->exists();

            if ($exists) {
                $this->errors[] = "Baris " . ($index + 2) . ": Kelas '{$row['nama_kelas']}' untuk tahun ajaran '{$row['tahun_ajaran']}' sudah ada.";
                continue;
            }

            Kelas::create([
                'id_walikelas' => $idWaliKelas,
                'tingkat'      => $row['tingkat'],
                'nama_kelas'   => $row['nama_kelas'],
                'jurusan'      => $row['jurusan'] ?? null,
                'tahun_ajaran' => $row['tahun_ajaran'],
            ]);

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'tingkat'      => 'required|string',
            'nama_kelas'   => 'required|string',
            'jurusan' => 'required|in:Perhotelan,Rekayasa Perangkat Lunak,Teknik Komputer Jaringan,Teknik Bisnis Sepeda Motor',
            'tahun_ajaran' => 'required|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'tingkat.required'      => 'Kolom tingkat wajib diisi.',
            'nama_kelas.required'   => 'Kolom nama_kelas wajib diisi.',
            'jurusan.required'      => 'Kolom jurusan wajib diisi.',
            'tahun_ajaran.required' => 'Kolom tahun_ajaran wajib diisi.',
        ];
    }
}
