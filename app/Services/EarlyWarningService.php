<?php

namespace App\Services;

use App\Jobs\SendEarlyWarningNotification;
use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\Pengguna;

class EarlyWarningService
{
    /**
     * Grace period dalam menit sebelum notifikasi benar-benar dikirim.
     * Beri waktu BK untuk koreksi jika terjadi salah input.
     */
    const GRACE_PERIOD_MINUTES = 15;

    /**
     * Jalankan pengecekan EWS setelah pelanggaran baru disimpan.
     * Notifikasi tidak langsung dikirim — dijadwalkan setelah grace period.
     */
    public function check(Pelanggaran $pelanggaran): void
    {
        $siswa   = $pelanggaran->siswa;
        $tingkat = optional($pelanggaran->jenisPelanggaran)->tingkat_pelanggaran;

        if (! $siswa || ! $tingkat) {
            return;
        }

        $total = Pelanggaran::where('id_siswa', $siswa->id_siswa)
            ->whereHas('jenisPelanggaran', fn($q) => $q->where('tingkat_pelanggaran', $tingkat))
            ->count();

        $aksi = $this->tentukanAksi($tingkat, $total);

        if ($aksi === null) {
            return; // Belum mencapai threshold
        }

        $pesan = $this->buatPesan($siswa->nama, $tingkat, $total, $aksi);

        // ── Simpan notif dengan status 'pending' dulu ──
        // Notif pending ini bisa dibatalkan kalau BK edit/hapus
        // dalam waktu GRACE_PERIOD_MINUTES menit ke depan
        $penerimaIds = $this->kumpulkanPenerimaIds($pelanggaran, $siswa);

        foreach ($penerimaIds as $idPengguna) {
            Notifikasi::create([
                'id_pengguna'      => $idPengguna,
                'id_pelanggaran'   => $pelanggaran->id_pelanggaran,
                'isi_pesan'        => $pesan,
                'jenis_notifikasi' => 'sistem',
                'waktu_dikirim'    => now()->addMinutes(self::GRACE_PERIOD_MINUTES),
                'status'           => 'pending',
                'is_read'          => false,
            ]);
        }

        // ── Dispatch job dengan delay grace period ──
        SendEarlyWarningNotification::dispatch(
            $pelanggaran->id_pelanggaran,
            $pesan,
            $aksi,
        )->delay(now()->addMinutes(self::GRACE_PERIOD_MINUTES));
    }

    /**
     * Dipanggil saat pelanggaran diedit.
     * Batalkan notif pending lama, lalu evaluasi ulang dengan data baru.
     */
    public function recheck(Pelanggaran $pelanggaran): void
    {
        // Batalkan semua notif pending terkait pelanggaran ini
        // (job lama akan lihat status 'dibatalkan' dan tidak akan kirim)
        Notifikasi::where('id_pelanggaran', $pelanggaran->id_pelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);

        // Muat ulang relasi setelah update
        $pelanggaran->load(['siswa.waliMurid.pengguna', 'waliKelas.pengguna', 'jenisPelanggaran']);

        // Evaluasi ulang dengan data pelanggaran yang sudah diedit
        $this->check($pelanggaran);
    }

    /**
     * Dipanggil saat pelanggaran dihapus (soft delete).
     * Batalkan semua notif pending — tidak perlu kirim notif.
     */
    public function cancel(Pelanggaran $pelanggaran): void
    {
        Notifikasi::where('id_pelanggaran', $pelanggaran->id_pelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);
    }

    // ── Private helpers ─────────────────────────────────────────────────────

    public function tentukanAksi(string $tingkat, int $total): ?string
    {
        return match ($tingkat) {
            'ringan' => ($total % 3 !== 0) ? null : ($total > 5 ? 'panggil_ortu' : 'pembinaan'),
            'sedang' => match (true) {
                $total === 1                    => 'pembinaan',
                $total >= 2 && $total % 2 === 0 => 'panggil_ortu',
                default                         => null,
            },
            'berat'  => 'panggil_ortu',
            default  => null,
        };
    }

    private function buatPesan(string $namaSiswa, string $tingkat, int $total, string $aksi): string
    {
        $labelTingkat = ucfirst($tingkat);
        $labelAksi    = $aksi === 'pembinaan'
            ? 'perlu dilakukan PEMBINAAN'
            : 'perlu dilakukan PEMANGGILAN ORANG TUA';

        return "⚠️ Early Warning: Siswa {$namaSiswa} telah melakukan {$total}x pelanggaran "
             . "{$labelTingkat}. Siswa {$labelAksi}.";
    }

    private function kumpulkanPenerimaIds(Pelanggaran $pelanggaran, $siswa): array
    {
        $ids = [];

        // 1. Semua guru BK
        $guruBK = Pengguna::whereHas('role', fn($q) => $q->where('nama_role', 'guru_bk'))
                    ->pluck('id_pengguna')->toArray();
        $ids = array_merge($ids, $guruBK);

        // 2. Wali kelas
        $wkPengguna = optional($pelanggaran->waliKelas)->pengguna;
        if ($wkPengguna) {
            $ids[] = $wkPengguna->id_pengguna;
        }

        // 3. Orang tua
        $orangTua = optional($siswa->waliMurid)->pengguna ?? null;
        if ($orangTua) {
            $ids[] = $orangTua->id_pengguna;
        }

        return array_unique($ids);
    }
}