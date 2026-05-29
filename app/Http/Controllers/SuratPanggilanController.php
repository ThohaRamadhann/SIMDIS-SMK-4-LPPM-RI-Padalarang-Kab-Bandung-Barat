<?php
// app/Http/Controllers/SuratPanggilanController.php

namespace App\Http\Controllers;

use App\Models\SuratPanggilan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SuratPanggilanController extends Controller
{
    public function cetak(int $id)
    {
        $surat = SuratPanggilan::with([
            'pelanggaran.siswa.kelas',
            'pelanggaran.siswa.waliMurid.pengguna',
            'pelanggaran.jenisPelanggaran',
            'waliKelas.pengguna',
        ])->findOrFail($id);

        $pelanggaran   = $surat->pelanggaran;
        $siswa         = $pelanggaran->siswa;
        $kelas         = $siswa?->kelas;
        $namaOrtu      = $siswa?->waliMurid?->pengguna?->name ?? 'Orang Tua/Wali';
        $namaWaliKelas = $surat->waliKelas?->pengguna?->name ?? '-';

        Carbon::setLocale('id');
        $hariTanggal = Carbon::parse($surat->tanggal_panggilan)
            ->translatedFormat('l, d F Y');
        $tanggalSurat = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('surat-panggilan.pdf', compact(
            'surat', 'pelanggaran', 'siswa', 'kelas',
            'namaOrtu', 'namaWaliKelas', 'hariTanggal', 'tanggalSurat',
        ))->setPaper('a4', 'portrait');

        $namaFile = 'surat-panggilan-' . str_replace('/', '-', $surat->nomor_surat) . '.pdf';
        return $pdf->stream($namaFile);
    }
}