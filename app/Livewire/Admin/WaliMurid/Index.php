<?php

namespace App\Livewire\Admin\WaliMurid;

use Livewire\Component;
use App\Models\WaliMurid;
use App\Models\Pengguna;

class Index extends Component
{
    public $id_walimurid;
    public $id_pengguna;
    public $hubungan = '';
    public $isEdit = false;

    // Gunai updated() untuk mendeteksi perubahan
    public function updated($property)
    {
        // Jika id_pengguna berubah
        if ($property === 'id_pengguna') {
            $this->loadWaliMuridData();
        }
    }

    public function loadWaliMuridData()
    {
        if ($this->id_pengguna) {
            $waliMurid = WaliMurid::where('id_pengguna', $this->id_pengguna)->first();
            
            if ($waliMurid) {
                $this->id_walimurid = $waliMurid->id_walimurid;
                $this->hubungan = $waliMurid->hubungan;
                $this->isEdit = true;
            } else {
                $this->id_walimurid = null;
                $this->hubungan = '';
                $this->isEdit = false;
            }
        }
    }

    public function resetForm()
    {
        $this->id_walimurid = null;
        $this->id_pengguna = '';
        $this->hubungan = '';
        $this->isEdit = false;
    }

    public function simpan()
    {
        $this->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'hubungan' => 'required|string|max:50',
        ]);

        if ($this->isEdit) {
            WaliMurid::findOrFail($this->id_walimurid)->update([
                'hubungan' => $this->hubungan,
            ]);
            $message = 'Data berhasil diperbarui';
        } else {
            WaliMurid::create([
                'id_pengguna' => $this->id_pengguna,
                'hubungan' => $this->hubungan,
            ]);
            $message = 'Data berhasil disimpan';
        }

        $this->resetForm();
        session()->flash('success', $message);
    }

    public function edit($id)
    {
        $data = WaliMurid::findOrFail($id);

        $this->id_walimurid = $data->id_walimurid;
        $this->id_pengguna = $data->id_pengguna;
        $this->hubungan = $data->hubungan;
        $this->isEdit = true;
    }

    public function hapus($id)
    {
        WaliMurid::findOrFail($id)->delete();
        session()->flash('success', 'Data berhasil dihapus');
    }

    public function render()
    {
        $waliMuridPengguna = Pengguna::whereHas('role', function($query) {
            $query->where('nama_role', 'orang_tua');
        })->orderBy('name')->get();
    
        if ($this->isEdit && $this->id_pengguna) {
            $currentPengguna = Pengguna::where('id_pengguna', $this->id_pengguna)->first();
            $waliMuridPengguna = $waliMuridPengguna->push($currentPengguna)->unique('id_pengguna')->sortBy('name');
        }

        return view('livewire.admin.walimurid.index', [
            'data'      => WaliMurid::with('pengguna')->orderBy('id_walimurid','desc')->get(),
            'pengguna'  => $waliMuridPengguna,
        ]);
    }
}