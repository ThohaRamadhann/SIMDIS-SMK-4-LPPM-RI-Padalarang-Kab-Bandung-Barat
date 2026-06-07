<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\WaliSiswa;
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
    public int   $updated  = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Cari wali siswa via username pengguna
            $waliSiswa = WaliSiswa::whereHas('pengguna', function ($q) use ($row) {
                $q->where('username', $row['username_walisiswa']);
            })->first();

            if (!$waliSiswa) {
                $this->errors[] = "Baris " . ($index + 2) . ": Wali siswa dengan username '{$row['username_walisiswa']}' tidak ditemukan.";
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

            // updateOrCreate: kalau NIS sudah ada → update, belum ada → insert
            $siswa = Siswa::withTrashed()
                ->where('nis', (string) $row['nis'])
                ->first();

            if ($siswa) {
                // Pulihkan dulu kalau ternyata di trash
                if ($siswa->trashed()) {
                    $siswa->restore();
                }

                $siswa->update([
                    'id_walisiswa' => $waliSiswa->id_walisiswa,
                    'id_kelas'     => $kelas->id_kelas,
                    'nama'         => $row['nama'],
                    'status'       => $row['status'] ?? 'aktif',
                ]);
                

                $this->updated++;
            } else {
                Siswa::create([
                    'id_walisiswa' => $waliSiswa->id_walisiswa,
                    'id_kelas'     => $kelas->id_kelas,
                    'nama'         => $row['nama'],
                    'nis'          => (string) $row['nis'],
                    'status'       => $row['status'] ?? 'aktif',
                ]);

                $this->imported++;
            }
        }
    }

    public function rules(): array
    {
        return [
            'nama'               => 'required|string|max:255',
            'nis'                => 'required|max:30',
            'username_walisiswa' => 'required|string',
            'nama_kelas'         => 'required|string',
            'tahun_ajaran'       => 'required|string',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required'               => 'Kolom nama wajib diisi.',
            'nis.required'                => 'Kolom NIS wajib diisi.',
            'username_walisiswa.required' => 'Kolom username_walisiswa wajib diisi.',
            'nama_kelas.required'         => 'Kolom nama_kelas wajib diisi.',
            'tahun_ajaran.required'       => 'Kolom tahun_ajaran wajib diisi.',
        ];
    }
}