<?php

namespace App\Jobs;

use App\Events\NotifikasiBaru;
use App\Models\Notifikasi;
use App\Models\Pelanggaran;
use App\Models\Pengguna;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEarlyWarningNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int    $idPelanggaran,
        public readonly string $pesan,
        public readonly string $aksi,
    ) {}

    public function handle(): void
    {
        $pelanggaran = Pelanggaran::with([
            'siswa.waliSiswa.pengguna',
            'siswa.kelas',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ])->find($this->idPelanggaran);

        if (! $pelanggaran) {
            $this->batalkanNotifPending();
            return;
        }

        $siswa   = $pelanggaran->siswa;
        $tingkat = optional($pelanggaran->jenisPelanggaran)->tingkat_pelanggaran ?? '';

        if (! $siswa || ! $tingkat) {
            $this->batalkanNotifPending();
            return;
        }

        $total = Pelanggaran::where('id_siswa', $siswa->id_siswa)
            ->whereHas('jenisPelanggaran', fn($q) =>
                $q->where('tingkat_pelanggaran', $tingkat)
            )
            ->count();

        $aksiSekarang = $this->tentukanAksi($tingkat, $total);

        if ($aksiSekarang !== $this->aksi) {
            $this->batalkanNotifPending();
            return;
        }

        // ── Kumpulkan penerima ──
        $penerima = collect();

        $guruBK = Pengguna::whereHas('role', fn($q) => $q->where('nama_role', 'guru_bk'))->get();
        $penerima = $penerima->merge($guruBK);

        $waliKelas = optional($pelanggaran->waliKelas)->pengguna;
        if ($waliKelas) {
            $penerima->push($waliKelas);
        }

        $orangTua = optional($siswa->waliSiswa)->pengguna ?? null;
        if ($orangTua) {
            $penerima->push($orangTua);
        }

        $penerima = $penerima->unique('id_pengguna');

        // ── STEP 1: Update semua notif pending → terkirim dulu ──
        $notifList = collect();

        foreach ($penerima as $pengguna) {
            $notif = Notifikasi::where('id_pelanggaran', $this->idPelanggaran)
                ->where('id_pengguna', $pengguna->id_pengguna)
                ->where('status', 'pending')
                ->first();

            if ($notif) {
                $notif->update([
                    'status'        => 'terkirim',
                    'waktu_dikirim' => now(),
                    'isi_pesan'     => $this->pesan,
                ]);
            } else {
                $notif = Notifikasi::create([
                    'id_pengguna'      => $pengguna->id_pengguna,
                    'id_pelanggaran'   => $this->idPelanggaran,
                    'isi_pesan'        => $this->pesan,
                    'jenis_notifikasi' => 'sistem',
                    'waktu_dikirim'    => now(),
                    'status'           => 'terkirim',
                    'is_read'          => false,
                ]);
            }

            $notifList->push($notif);
        }

        // ── STEP 2: Baru broadcast semua setelah semua sudah terkirim ──
        foreach ($notifList as $notif) {
            event(new NotifikasiBaru($notif));
        }

        if ($this->aksi === 'panggil_ortu') {
            $this->kirimWhatsApp($pelanggaran, $siswa, $total, $tingkat);
        }
    }

    private function kirimWhatsApp($pelanggaran, $siswa, int $total, string $tingkat): void
    {
        $siswa->loadMissing(['waliSiswa.pengguna', 'kelas']);

        $orangTuaPengguna = optional($siswa->waliSiswa)->pengguna ?? null;

        if (! $orangTuaPengguna) {
            Log::warning('EWS WA: orang tua tidak ditemukan', [
                'id_siswa'     => $siswa->id_siswa,
                'id_walisiswa' => $siswa->id_walisiswa,
            ]);
            return;
        }

        $noTelpon = $orangTuaPengguna->no_telpon;

        if (! $noTelpon) {
            Log::warning('EWS WA: nomor orang tua kosong', [
                'id_pengguna' => $orangTuaPengguna->id_pengguna,
            ]);
            return;
        }

        // Gunakan data pelanggaran yang di-trigger langsung
        $namaOrtu        = $orangTuaPengguna->name;
        $namaSiswa       = $siswa->nama;
        $kelasSiswa      = optional($siswa->kelas)->nama_kelas ?? '-';
        $namaPelanggaran = optional($pelanggaran->jenisPelanggaran)->nama_pelanggaran ?? '-';
        $waktuKejadian   = $pelanggaran->waktu_kejadian
                            ? $pelanggaran->waktu_kejadian->format('d M Y, H:i') . ' WIB'
                            : '-';

        $pesan = "🚨 *PEMBERITAHUAN DARI SEKOLAH*\n\n"
               . "Yth. Bapak/Ibu *{$namaOrtu}*,\n\n"
               . "Kami memberitahukan bahwa putra/putri Anda:\n"
               . "👤 *{$namaSiswa}* ({$kelasSiswa})\n\n"
               . "telah melakukan *{$total}x pelanggaran {$tingkat}* "
               . "dan memerlukan *PEMANGGILAN ORANG TUA* ke sekolah.\n\n"
               . "📋 *Detail Pelanggaran Terakhir:*\n"
               . "• Jenis   : {$namaPelanggaran}\n"
               . "• Tingkat : {$tingkat}\n"
               . "• Waktu   : {$waktuKejadian}\n\n"
               . "Mohon segera menghubungi pihak sekolah atau Guru BK untuk "
               . "informasi lebih lanjut.\n\n"
               . "Terima kasih atas perhatian dan kerjasamanya.\n\n"
               . "_Sistem Informasi Disiplin Siswa (SIMDIS)_";

        $fonnte   = app(FonnteService::class);
        $berhasil = $fonnte->kirim($noTelpon, $pesan);

        Log::info('EWS WA: hasil pengiriman', [
            'id_siswa'       => $siswa->id_siswa,
            'nama_siswa'     => $namaSiswa,
            'no_telpon'      => $noTelpon,
            'pelanggaran'    => $namaPelanggaran,
            'waktu_kejadian' => $waktuKejadian,
            'berhasil'       => $berhasil,
        ]);
    }

    private function batalkanNotifPending(): void
    {
        Notifikasi::where('id_pelanggaran', $this->idPelanggaran)
            ->where('status', 'pending')
            ->update(['status' => 'dibatalkan']);
    }

    private function tentukanAksi(string $tingkat, int $total): ?string
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

    public function failed(\Throwable $exception): void
    {
        Notifikasi::where('id_pelanggaran', $this->idPelanggaran)
            ->where('status', 'pending')
            ->update([
                'status'      => 'gagal',
                'pesan_error' => $exception->getMessage(),
            ]);

        Log::error('SendEarlyWarningNotification failed', [
            'id_pelanggaran' => $this->idPelanggaran,
            'error'          => $exception->getMessage(),
        ]);
    }
}