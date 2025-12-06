<?php
namespace App\Events;

use App\Models\Pengguna;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserChanged implements ShouldBroadcast
{
    use SerializesModels;

    public $user;

    public function __construct(Pengguna $user)
    {
        $this->user = $user->only(['id_pengguna','name','username','id_role','email']);
    }

    public function broadcastOn()
    {
        // broadcast ke channel admin agar semua admin dapat mendengarkan update
        return new PrivateChannel('admin.users');
    }

    public function broadcastAs()
    {
        return 'UserChanged';
    }
}
