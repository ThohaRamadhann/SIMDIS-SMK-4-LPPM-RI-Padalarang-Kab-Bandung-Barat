<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Notifikasi as NotifikasiModel;

class NotifikasiRealtime extends Component
{
    public $notifikasiList = [];

    protected $listeners = [
        'echo-private:notifikasi.{id_pengguna},NotifikasiBaru' => 'agregarNotifikasi'
    ];

    public function mount()
    {
        // Hanya muat notif yang sudah benar-benar terkirim
        // Notif pending (dalam grace period 15 menit) tidak dimuat
        $this->notifikasiList = NotifikasiModel::where('id_pengguna', auth()->id())
            ->where('status', 'terkirim')        // ← filter ini yang hilang
            ->orderBy('waktu_dikirim', 'desc')   // ← urut by waktu dikirim, bukan created_at
            ->get()
            ->toArray();
    }

    public function agregarNotifikasi($payload)
    {
        // Hanya tambahkan ke list kalau status terkirim
        // (real-time event dari Echo hanya dipush kalau job sudah selesai)
        if (isset($payload['notifikasi']['status']) 
            && $payload['notifikasi']['status'] === 'terkirim') {
            array_unshift($this->notifikasiList, $payload['notifikasi']);
        }
    }

    public function render()
    {
        return view('livewire.notifikasi-realtime');
    }
}