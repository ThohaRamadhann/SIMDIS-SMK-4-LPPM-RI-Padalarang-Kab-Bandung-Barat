<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class EarlyWarningService
{
    /**
     * Jalankan pengecekan EWS setelah pelanggaran baru disimpan.
     */
    
    public function check(Pelanggaran $pelanggaran): void

    {
        $siswa    = $pelanggaran->siswa;
        $tingkat  = optional($pelanggaran->jenisPelanggaran)->tingkat_pelanggaran;

        if (! $siswa || ! $tingkat) {
            return;
        }

        // Hitung total pelanggaran siswa berdasarkan tingkat (akumulatif)
        $total = Pelanggaran::where('id_siswa', $siswa->id_siswa)
            ->whereHas('jenisPelanggaran', fn ($q) => $q->where('tingkat_pelanggaran', $tingkat))
            ->count();

        $aksi = $this->tentukanAksi($tingkat, $total);

        if ($aksi === null) {
            return; // Belum memenuhi threshold, tidak ada notif
        }

        $this->kirimNotifikasi($pelanggaran, $siswa, $tingkat, $total, $aksi);
    }

    /**
     * Tentukan aksi berdasarkan tingkat dan total pelanggaran.
     * Return: 'pembinaan' | 'panggil_ortu' | null
     */
    private function tentukanAksi(string $tingkat, int $total): ?string
    {
        switch ($tingkat) {

            case 'ringan':
                // Kelipatan 3
                if ($total % 3 !== 0) return null;
                // Lebih dari 5 → panggil ortu, sebelumnya → pembinaan
                return $total > 5 ? 'panggil_ortu' : 'pembinaan';

            case 'sedang':
                // ke-1 → pembinaan
                if ($total === 1) return 'pembinaan';
                // Kelipatan 2 setelah ke-2 → panggil ortu
                if ($total >= 2 && $total % 2 === 0) return 'panggil_ortu';
                return null;

            case 'berat':
                // ke-1 → panggil ortu, setelahnya setiap penambahan 1 → panggil ortu
                return 'panggil_ortu';

            default:
                return null;
        }
    }

    /**
     * Kirim notifikasi ke guru BK, wali kelas siswa, dan orang tua siswa.
     */
    private function kirimNotifikasi(
        Pelanggaran $pelanggaran,
        $siswa,
        string $tingkat,
        int $total,
        string $aksi
    ): void {
        $namaSiswa   = $siswa->nama;
        $labelTingkat = ucfirst($tingkat);
        $labelAksi    = $aksi === 'pembinaan'
            ? 'perlu dilakukan PEMBINAAN'
            : 'perlu dilakukan PEMANGGILAN ORANG TUA';

        $pesan = "⚠️ Early Warning: Siswa {$namaSiswa} telah melakukan {$total}x pelanggaran {$labelTingkat}. "
               . "Siswa {$labelAksi}.";

        // Kumpulkan penerima notifikasi
        $penerima = collect();

        // 1. Semua guru BK
        $guruBK = Pengguna::whereHas('role', fn ($q) => $q->where('nama_role', 'guru_bk'))->get();
        $penerima = $penerima->merge($guruBK);

        // 2. Wali kelas siswa (via relasi pelanggaran → waliKelas → pengguna)
        $waliKelas = optional($pelanggaran->waliKelas)->pengguna;
        if ($waliKelas) {
            $penerima->push($waliKelas);
        }

        // 3. Orang tua siswa (via siswa → waliMurid → pengguna)
        $orangTua = optional(optional($siswa->waliMurid)->pengguna ?? null);
        $orangTuaPengguna = optional($siswa->waliMurid)->pengguna ?? null;
        if ($orangTuaPengguna) {
            $penerima->push($orangTuaPengguna);
        }

        // Deduplikasi berdasarkan id_pengguna
        $penerima = $penerima->unique('id_pengguna');

        // Simpan notifikasi ke DB untuk setiap penerima
        foreach ($penerima as $pengguna) {
            Notifikasi::create([
                'id_pengguna'      => $pengguna->id_pengguna,
                'id_pelanggaran'   => $pelanggaran->id_pelanggaran,
                'isi_pesan'        => $pesan,
                'jenis_notifikasi' => 'sistem',
                'waktu_dikirim'    => now(),
                'status'           => 'terkirim',
            ]);
        }
    }
}