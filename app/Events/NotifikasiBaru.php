<?php

namespace App\Events;

use App\Models\Notifikasi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NotifikasiBaru implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $notifikasi;

    public function __construct(Notifikasi $notifikasi)
    {
        $this->notifikasi = $notifikasi;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifikasi.' . $this->notifikasi->id_pengguna);
    }
}
