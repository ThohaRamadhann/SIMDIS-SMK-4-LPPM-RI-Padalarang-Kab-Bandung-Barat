<?php

namespace App\Services;

use App\Jobs\SendEarlyWarningNotification;
use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\Pengguna;

class EarlyWarningService
{
    private function gracePeriod(): int
    {
        return (int) config('services.ews.grace_period_minutes', 10);
    }

    public function check(Pelanggaran $pelanggaran): void
    {
        // ✅ FIX: Pastikan semua relasi selalu ter-load,
        // regardless dari mana check() dipanggil
        $pelanggaran->loadMissing([
            'siswa.waliSiswa.pengguna',
            'siswa.kelas',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        $siswa   = $pelanggaran->siswa;
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

        SendEarlyWarningNotification::dispatch(
            $pelanggaran->id_pelanggaran,
            $pesan,
            $aksi,
        )->delay(now()->addMinutes($grace));
    }

    public function recheck(Pelanggaran $pelanggaran): void
    {
        Notifikasi::where('id_pelanggaran', $pelanggaran->id_pelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);

        // ✅ FIX: load semua relasi yang dibutuhkan sebelum check()
        $pelanggaran->load([
            'siswa.waliSiswa.pengguna',
            'siswa.kelas',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        $this->check($pelanggaran);
    }

    public function cancel(Pelanggaran $pelanggaran): void
    {
        Notifikasi::where('id_pelanggaran', $pelanggaran->id_pelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);
    }

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
        $labelAksi = $aksi === 'pembinaan'
            ? 'perlu dilakukan PEMBINAAN'
            : 'perlu dilakukan PEMANGGILAN ORANG TUA';

        return "⚠️ Early Warning: Siswa {$namaSiswa} telah melakukan {$total}x pelanggaran "
             . "{$tingkat}. Siswa {$labelAksi}.";
    }

    private function kumpulkanPenerimaIds(Pelanggaran $pelanggaran, $siswa): array
    {
        $ids = [];

        // Guru BK
        $guruBK = Pengguna::whereHas('role', fn($q) => $q->where('nama_role', 'guru_bk'))
                    ->pluck('id_pengguna')->toArray();
        $ids = array_merge($ids, $guruBK);

        // Wali Kelas
        $wkPengguna = optional($pelanggaran->waliKelas)->pengguna;
        if ($wkPengguna) {
            $ids[] = $wkPengguna->id_pengguna;
        }

        // ✅ FIX: Akses waliSiswa dari $siswa yang sudah pasti ter-load
        // karena loadMissing() dipanggil di awal check()
        $orangTua = optional($siswa->waliSiswa)->pengguna ?? null;
        if ($orangTua) {
            $ids[] = $orangTua->id_pengguna;
        }

        return array_unique($ids);
    }
}