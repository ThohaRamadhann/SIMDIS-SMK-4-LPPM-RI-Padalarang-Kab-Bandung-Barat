<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\WaliMurid;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class SiswaImport implements
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
            // Cari wali murid via username pengguna
            $waliMurid = WaliMurid::whereHas('pengguna', function ($q) use ($row) {
                $q->where('username', $row['username_walimurid']);
            })->first();

            if (!$waliMurid) {
                $this->errors[] = "Baris " . ($index + 2) . ": Wali murid dengan username '{$row['username_walimurid']}' tidak ditemukan.";
                continue;
            }

            // Cari kelas via nama_kelas + tahun_ajaran
            $kelas = Kelas::where('nama_kelas', $row['nama_kelas'])
                ->where('tahun_ajaran', $row['tahun_ajaran'])
                ->first();

            if (!$kelas) {
                $this->errors[] = "Baris " . ($index + 2) . ": Kelas '{$row['nama_kelas']}' tahun '{$row['tahun_ajaran']}' tidak ditemukan.";
                continue;
            }

            // Cek NIS duplikat
            if (Siswa::where('nis', (string) $row['nis'])->exists()) {
                $this->errors[] = "Baris " . ($index + 2) . ": NIS '{$row['nis']}' sudah terdaftar.";
                continue;
            }

            Siswa::create([
                'id_walimurid' => $waliMurid->id_walimurid,
                'id_kelas'     => $kelas->id_kelas,
                'nama'         => $row['nama'],
                'nis'          => (string) $row['nis'], // ✅ Cast ke string
                'status'       => $row['status'] ?? 'aktif',
            ]);

            $this->imported++;
        }
    }

    public function rules(): array
    {
        return [
            'nama'               => 'required|string|max:255',
            'nis'                => 'required|max:30', // ✅ Hapus 'string'
            'username_walimurid' => 'required|string',
            'nama_kelas'         => 'required|string',
            'tahun_ajaran'       => 'required|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required'               => 'Kolom nama wajib diisi.',
            'nis.required'                => 'Kolom NIS wajib diisi.',
            'username_walimurid.required' => 'Kolom username_walimurid wajib diisi.',
            'nama_kelas.required'         => 'Kolom nama_kelas wajib diisi.',
            'tahun_ajaran.required'       => 'Kolom tahun_ajaran wajib diisi.',
        ];
    }
}
