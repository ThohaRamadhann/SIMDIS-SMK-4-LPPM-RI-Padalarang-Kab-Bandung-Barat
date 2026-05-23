<?php

namespace App\Services;

use App\Jobs\SendEarlyWarningNotification;
use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\Pengguna;

class EarlyWarningService
{
    /**
     * Grace period diambil dari config — ubah via .env saja
     * Development : EWS_GRACE_PERIOD_MINUTES=1
     * Production  : EWS_GRACE_PERIOD_MINUTES=10
     */
    private function gracePeriod(): int
    {
        return (int) config('services.ews.grace_period_minutes', 10);
    }

    /**
     * Jalankan pengecekan EWS setelah pelanggaran baru disimpan.
     * Notifikasi tidak langsung dikirim — dijadwalkan setelah grace period.
     */
    public function check(Pelanggaran $pelanggaran): void
    {
        $siswa   = $pelanggaran->siswa;

        // Langsung pakai nilai DB (Ringan / Sedang / Berat) — sudah konsisten kapital
        $tingkat = optional($pelanggaran->jenisPelanggaran)->tingkat_pelanggaran ?? '';

        if (! $siswa || ! $tingkat) {
            return;
        }

        $total = Pelanggaran::where('id_siswa', $siswa->id_siswa)
            ->whereHas('jenisPelanggaran', fn($q) =>
                $q->where('tingkat_pelanggaran', $tingkat)
            )
            ->count();

        $aksi = $this->tentukanAksi($tingkat, $total);

        if ($aksi === null) {
            return;
        }

        $pesan       = $this->buatPesan($siswa->nama, $tingkat, $total, $aksi);
        $penerimaIds = $this->kumpulkanPenerimaIds($pelanggaran, $siswa);
        $grace       = $this->gracePeriod();

        // Simpan notif pending — belum tampil di bell sampai grace period habis
        foreach ($penerimaIds as $idPengguna) {
            Notifikasi::create([
                'id_pengguna'      => $idPengguna,
                'id_pelanggaran'   => $pelanggaran->id_pelanggaran,
                'isi_pesan'        => $pesan,
                'jenis_notifikasi' => 'sistem',
                'waktu_dikirim'    => now()->addMinutes($grace),
                'status'           => 'pending',
                'is_read'          => false,
            ]);
        }

        // Dispatch job dengan delay sesuai grace period dari config
        SendEarlyWarningNotification::dispatch(
            $pelanggaran->id_pelanggaran,
            $pesan,
            $aksi,
        )->delay(now()->addMinutes($grace));
    }

    /**
     * Dipanggil saat pelanggaran diedit.
     * Batalkan notif pending lama, evaluasi ulang dengan data baru.
     */
    public function recheck(Pelanggaran $pelanggaran): void
    {
        Notifikasi::where('id_pelanggaran', $pelanggaran->id_pelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);

        $pelanggaran->load(['siswa.waliMurid.pengguna', 'waliKelas.pengguna', 'jenisPelanggaran']);

        $this->check($pelanggaran);
    }

    /**
     * Dipanggil saat pelanggaran dihapus (soft delete).
     * Batalkan semua notif pending.
     */
    public function cancel(Pelanggaran $pelanggaran): void
    {
        Notifikasi::where('id_pelanggaran', $pelanggaran->id_pelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Tentukan aksi berdasarkan tingkat dan total pelanggaran.
     * Nilai tingkat: 'Ringan' | 'Sedang' | 'Berat' (kapital, sesuai ENUM DB)
     */
    public function tentukanAksi(string $tingkat, int $total): ?string
    {
        return match ($tingkat) {
            'Ringan' => ($total % 3 !== 0) ? null : ($total > 5 ? 'panggil_ortu' : 'pembinaan'),
            'Sedang' => match (true) {
                $total === 1                    => 'pembinaan',
                $total >= 2 && $total % 2 === 0 => 'panggil_ortu',
                default                         => null,
            },
            'Berat'  => 'panggil_ortu',
            default  => null,
        };
    }

    private function buatPesan(string $namaSiswa, string $tingkat, int $total, string $aksi): string
    {
        // $tingkat sudah kapital dari DB (Ringan/Sedang/Berat), tidak perlu ucfirst
        $labelAksi = $aksi === 'pembinaan'
            ? 'perlu dilakukan PEMBINAAN'
            : 'perlu dilakukan PEMANGGILAN ORANG TUA';

        return "⚠️ Early Warning: Siswa {$namaSiswa} telah melakukan {$total}x pelanggaran "
             . "{$tingkat}. Siswa {$labelAksi}.";
    }

    private function kumpulkanPenerimaIds(Pelanggaran $pelanggaran, $siswa): array
    {
        $ids = [];

        $guruBK = Pengguna::whereHas('role', fn($q) => $q->where('nama_role', 'guru_bk'))
                    ->pluck('id_pengguna')->toArray();
        $ids = array_merge($ids, $guruBK);

        $wkPengguna = optional($pelanggaran->waliKelas)->pengguna;
        if ($wkPengguna) {
            $ids[] = $wkPengguna->id_pengguna;
        }

        $orangTua = optional($siswa->waliMurid)->pengguna ?? null;
        if ($orangTua) {
            $ids[] = $orangTua->id_pengguna;
        }

        return array_unique($ids);
    }
}