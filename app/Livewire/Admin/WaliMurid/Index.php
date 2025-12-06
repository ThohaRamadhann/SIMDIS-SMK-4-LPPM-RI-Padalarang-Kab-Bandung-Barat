<?php

namespace App\Livewire\Admin\WaliMurid;

use Livewire\Component;
use App\Models\WaliMurid;
use App\Models\Pengguna;

class Index extends Component
{
    public $id_pengguna;
    public $hubungan;

    public function simpan()
    {
        $this->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'hubungan' => 'required|string|max:50',
        ]);

        WaliMurid::create([
            'id_pengguna' => $this->id_pengguna,
            'hubungan' => $this->hubungan,
        ]);

        $this->reset(['id_pengguna', 'hubungan']);
        session()->flash('success', 'Data berhasil disimpan');
    }

    public function hapus($id)
    {
        WaliMurid::findOrFail($id)->delete();
        session()->flash('success', 'Data berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.admin.walimurid.index', [
            'data'      => WaliMurid::with('pengguna')->orderBy('id_walimurid','desc')->get(),
            'pengguna'  => Pengguna::orderBy('name')->get(),
        ]);
    }
}
