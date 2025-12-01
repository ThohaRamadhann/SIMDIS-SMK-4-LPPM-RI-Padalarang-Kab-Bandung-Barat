<?php

namespace App\Events;

use App\Models\Pelanggaran;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PelanggaranDibuat implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $pelanggaran;

    public function __construct(Pelanggaran $pelanggaran)
    {
        $this->pelanggaran = $pelanggaran;
    }

    public function broadcastOn()
    {
        // Bisa di-private channel untuk tiap pengguna
        return new Channel('notifikasi.' . $this->pelanggaran->id_walikelas);
    }
}

