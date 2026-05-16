<?php

namespace App\Events;

use App\Models\Notifikasi;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifikasiBaru implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Notifikasi $notifikasi
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifikasi.' . $this->notifikasi->id_pengguna),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NotifikasiBaru';
    }

    /**
     * Kirim data lengkap ke frontend — konsisten dengan loadNotifikasi()
     * di navigation.blade.php agar tampilan realtime sama dengan setelah refresh
     */
    public function broadcastWith(): array
    {
        // Load semua relasi yang dibutuhkan untuk tampilan lengkap
        $notif = $this->notifikasi->load([
            'pelanggaran.siswa',
            'pelanggaran.jenisPelanggaran',
        ]);

        return [
            'notifikasi' => [
                // ── Data notifikasi dasar ──
                'id_notifikasi'    => $notif->id_notifikasi,
                'isi_pesan'        => $notif->isi_pesan,
                'jenis_notifikasi' => $notif->jenis_notifikasi,
                'waktu_dikirim'    => $notif->waktu_dikirim?->toDateTimeString(),
                'status'           => $notif->status,
                'is_read'          => false,
                'read_at'          => null,

                // ── Detail siswa ──
                'nama_siswa'       => optional(optional($notif->pelanggaran)->siswa)->nama ?? null,
                'nis_siswa'        => optional(optional($notif->pelanggaran)->siswa)->nis  ?? null,

                // ── Detail pelanggaran ──
                'nama_pelanggaran' => optional(optional($notif->pelanggaran)->jenisPelanggaran)->nama_pelanggaran ?? null,
                'tingkat'          => optional(optional($notif->pelanggaran)->jenisPelanggaran)->tingkat_pelanggaran ?? null,
                'waktu_kejadian'   => optional(optional($notif->pelanggaran)->waktu_kejadian)?->toDateTimeString() ?? null,
                'deskripsi'        => optional($notif->pelanggaran)->deskripsi ?? null,
                'status_pembinaan' => optional($notif->pelanggaran)->status_pembinaan ?? null,
            ],
        ];
    }
}