<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemplateExport;

class TemplateImportController extends Controller
{
    private array $templates = [
        'pengguna' => [
            'headers'  => ['nama', 'username', 'email', 'no_telpon', 'password', 'role'],
            'contoh'   => [['Budi Santoso', 'budi123', 'budi@email.com', '08123456789', 'password123', 'guru']],
            'filename' => 'template_pengguna.xlsx',
            'catatan'  => "CATATAN:\n- Kolom role diisi dengan nama_role yang ada di tabel role (contoh: admin, guru, siswa, wali)\n- Password minimal 6 karakter\n- Email & no_telpon boleh kosong",
        ],
        'wali_kelas' => [
            'headers'  => ['username_pengguna', 'nuptk', 'jabatan'],
            'contoh'   => [['budi123', '1234567890123456', 'walikelas']],
            'filename' => 'template_wali_kelas.xlsx',
            'catatan'  => "CATATAN:\n- username_pengguna harus sudah terdaftar di tabel pengguna\n- nuptk & jabatan boleh kosong\n- jabatan contoh: walikelas / guru bk",
        ],
        'wali_siswa' => [
            'headers'  => ['username_pengguna', 'hubungan'],
            'contoh'   => [['john_doe', 'ayah']],
            'filename' => 'template_wali_siswa.xlsx',
            'catatan'  => "CATATAN:\n- username_pengguna harus sudah terdaftar di tabel pengguna\n- hubungan hanya boleh: ayah / ibu / wali",
        ],
        'kelas' => [
            'headers'  => ['tingkat', 'nama_kelas', 'jurusan', 'tahun_ajaran', 'nuptk_walikelas'],
            'contoh'   => [['X', 'X TKJ 1', 'TKJ', '2024/2025', '1234567890123456']],
            'filename' => 'template_kelas.xlsx',
            'catatan'  => "CATATAN:\n- tingkat contoh: X, XI, XII\n- nuptk_walikelas boleh kosong jika belum ada wali kelas\n- jurusan boleh kosong",
        ],
        'siswa' => [
            'headers'  => ['nama', 'nis', 'status', 'username_walisiswa', 'nama_kelas', 'tahun_ajaran'],
            'contoh'   => [['Ahmad Fauzi', '2024001', 'aktif', 'john_doe', 'X TKJ 1', '2024/2025']],
            'filename' => 'template_siswa.xlsx',
            'catatan'  => "CATATAN:\n- NIS harus unik\n- status default: aktif (boleh kosong)\n- username_walisiswa harus sudah terdaftar sebagai wali siswa\n- nama_kelas & tahun_ajaran harus sesuai data kelas yang ada",
        ],
    ];

    public function download(string $type)
    {
        abort_unless(array_key_exists($type, $this->templates), 404);

        $data = $this->templates[$type];

        return Excel::download(
            new TemplateExport($data['headers'], $data['contoh'], $data['catatan']),
            $data['filename']
        );
    }
}