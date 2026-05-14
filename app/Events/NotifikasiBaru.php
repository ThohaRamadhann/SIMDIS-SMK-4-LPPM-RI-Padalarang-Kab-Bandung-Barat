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
        return [
            'notifikasi' => [
                'id_notifikasi'    => $this->notifikasi->id_notifikasi,
                'isi_pesan'        => $this->notifikasi->isi_pesan,
                'jenis_notifikasi' => $this->notifikasi->jenis_notifikasi,
                'waktu_dikirim'    => $this->notifikasi->waktu_dikirim?->toISOString(),
                'status'           => $this->notifikasi->status,
                'is_read'          => false,
            ],
        ];
    }
}