<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\JenisPelanggaran;
use App\Models\WaliKelas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportPelanggaranController extends Controller
{
    public function export(Request $request)
    {
        $search           = $request->get('search', '');
        $filterJenis      = $request->get('jenis', '');
        $filterTingkat    = $request->get('tingkat', '');
        $filterStatus     = $request->get('status', '');
        $filterWaliKelas  = $request->get('wali_kelas', '');
        $filterTahunAjaran = $request->get('tahun_ajaran', ''); // ← tambah ini
        $sortBy           = $request->get('sort', 'terbaru');

        $query = Pelanggaran::with([
            'siswa.kelas',
            'jenisPelanggaran',
            'waliKelas.pengguna',
        ]);

        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($filterJenis) {
            $query->where('id_jenispelanggaran', $filterJenis);
        }

        if ($filterTingkat) {
            $query->whereHas('jenisPelanggaran', function ($q) use ($filterTingkat) {
                $q->where('tingkat_pelanggaran', $filterTingkat);
            });
        }

        if ($filterStatus) {
            $query->where('status_pembinaan', $filterStatus);
        }

        if ($filterWaliKelas) {
            $query->where('id_walikelas', $filterWaliKelas);
        }

        // ← tambah filter tahun ajaran
        if ($filterTahunAjaran) {
            $query->whereHas('siswa.kelas', function ($q) use ($filterTahunAjaran) {
                $q->where('tahun_ajaran', $filterTahunAjaran);
            });
        }

        match ($sortBy) {
            'terlama' => $query->oldest('waktu_kejadian'),
            'az'      => $query->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id_siswa')
                               ->orderBy('siswa.nama', 'asc')
                               ->select('pelanggaran.*'),
            'za'      => $query->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id_siswa')
                               ->orderBy('siswa.nama', 'desc')
                               ->select('pelanggaran.*'),
            default   => $query->latest('waktu_kejadian'),
        };

        $pelanggarans = $query->get();

        // ── Info filter untuk header PDF ──
        $filterInfo = [];
        if ($search)             $filterInfo[] = 'Pencarian: "' . $search . '"';
        if ($filterTingkat)      $filterInfo[] = 'Tingkat: ' . $filterTingkat;
        if ($filterStatus)       $filterInfo[] = 'Status: ' . $filterStatus;
        if ($filterTahunAjaran)  $filterInfo[] = 'Tahun Ajaran: ' . $filterTahunAjaran; // ← tambah ini
        if ($filterJenis) {
            $jenis = JenisPelanggaran::find($filterJenis);
            if ($jenis) $filterInfo[] = 'Jenis: ' . $jenis->nama_pelanggaran;
        }
        if ($filterWaliKelas) {
            $wk = WaliKelas::with('pengguna')->find($filterWaliKelas);
            if ($wk) $filterInfo[] = 'Wali Kelas: ' . ($wk->pengguna->name ?? '-');
        }

        $tanggalCetak = now()->translatedFormat('l, d F Y');
        $jamCetak     = now()->format('H:i') . ' WIB';

        $pdf = Pdf::loadView('pdf.export-pelanggaran', compact(
            'pelanggarans',
            'filterInfo',
            'tanggalCetak',
            'jamCetak',
        ))->setPaper('a4', 'landscape');

        $filename = 'laporan-pelanggaran-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}