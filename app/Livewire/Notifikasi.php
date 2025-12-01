<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Notifikasi as NotifikasiModel; // alias agar tidak bentrok
use App\Events\NotifikasiBaru;

class NotifikasiRealtime extends Component
{
    public $notifikasiList = []; // ganti nama properti juga

    protected $listeners = [
        'echo-private:notifikasi.{id_pengguna},NotifikasiBaru' => 'agregarNotifikasi'
    ];

    public function mount()
    {
        $this->notifikasiList = NotifikasiModel::where('id_pengguna', auth()->id())
                                       ->orderBy('created_at', 'desc')
                                       ->get()
                                       ->toArray();
    }

    public function agregarNotifikasi($payload)
    {
        array_unshift($this->notifikasiList, $payload['notifikasi']); // akses array dengan tanda []
    }

    public function render()
    {
        return view('livewire.notifikasi-realtime');
    }
}

