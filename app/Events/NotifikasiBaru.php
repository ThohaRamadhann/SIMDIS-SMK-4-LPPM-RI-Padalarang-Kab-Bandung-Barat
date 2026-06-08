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

    public function broadcastWith(): array
    {
        $notif = $this->notifikasi->load([
            'pelanggaran.siswa',
            'pelanggaran.jenisPelanggaran',
        ]);

        $statusPenerima = [];
        if ($notif->id_pelanggaran) {
            $statusPenerima = Notifikasi::where('id_pelanggaran', $notif->id_pelanggaran)
                ->where('status', 'terkirim')
                ->with(['pengguna.role'])
                ->get()
                ->unique('id_pengguna') // ✅ fix duplikat dari sumber
                ->map(function ($n) {
                    $namaPengguna = optional($n->pengguna)->name ?? '-';
                    $roleName     = optional(optional($n->pengguna)->role)->nama_role ?? '-';
                    $labelRole    = match ($roleName) {
                        'wali_siswa' => 'Wali Siswa',
                        'wali_kelas' => 'Wali Kelas',
                        'guru_bk'    => 'Guru BK',
                        default      => ucfirst(str_replace('_', ' ', $roleName)),
                    };
                    return [
                        'nama'    => $namaPengguna,
                        'display' => $namaPengguna,
                        'role'    => $labelRole,
                        'is_read' => (bool) $n->is_read,
                        'read_at' => optional($n->read_at)?->toDateTimeString(),
                    ];
                })
                ->values() // ✅ reset index setelah unique
                ->toArray();
        }

        return [
            'notifikasi' => [
                'id_notifikasi'    => $notif->id_notifikasi,
                'id_pelanggaran'   => $notif->id_pelanggaran,
                'isi_pesan'        => $notif->isi_pesan,
                'jenis_notifikasi' => $notif->jenis_notifikasi,
                'waktu_dikirim'    => $notif->waktu_dikirim?->toDateTimeString(),
                'status'           => $notif->status,
                'is_read'          => false,
                'read_at'          => null,
                'nama_siswa'       => optional(optional($notif->pelanggaran)->siswa)->nama ?? null,
                'nis_siswa'        => optional(optional($notif->pelanggaran)->siswa)->nis  ?? null,
                'nama_pelanggaran' => optional(optional($notif->pelanggaran)->jenisPelanggaran)->nama_pelanggaran ?? null,
                'tingkat'          => optional(optional($notif->pelanggaran)->jenisPelanggaran)->tingkat_pelanggaran ?? null,
                'waktu_kejadian'   => optional(optional($notif->pelanggaran)->waktu_kejadian)?->toDateTimeString() ?? null,
                'deskripsi'        => optional($notif->pelanggaran)->deskripsi ?? null,
                'status_pembinaan' => optional($notif->pelanggaran)->status_pembinaan ?? null,
                'status_penerima'  => $statusPenerima,
            ],
        ];
    }
}