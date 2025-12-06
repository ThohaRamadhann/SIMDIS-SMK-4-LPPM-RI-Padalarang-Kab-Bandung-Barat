<?php

namespace App\Livewire\Admin\WaliKelas;

use Livewire\Component;
use App\Models\Pengguna;
use App\Models\WaliKelas;

class Index extends Component
{
    public $search = '';

    public $id_walikelas;
    public $id_pengguna;
    public $nuptk;
    public $jabatan;
    public $isEdit = false;

    public function resetForm()
    {
        $this->id_walikelas = null;
        $this->id_pengguna = '';
        $this->nuptk = '';
        $this->jabatan = '';
        $this->isEdit = false;

        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'id_pengguna' => 'required',
            'nuptk' => 'required',
            'jabatan' => 'required',
        ]);

        WaliKelas::create([
            'id_pengguna' => $this->id_pengguna,
            'nuptk' => $this->nuptk,
            'jabatan' => $this->jabatan,
        ]);

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = WaliKelas::findOrFail($id);

        $this->id_walikelas = $data->id_walikelas;
        $this->id_pengguna = $data->id_pengguna;
        $this->nuptk = $data->nuptk;
        $this->jabatan = $data->jabatan;

        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'id_pengguna' => 'required',
            'nuptk' => 'required',
            'jabatan' => 'required',
        ]);

        WaliKelas::findOrFail($this->id_walikelas)->update([
            'id_pengguna' => $this->id_pengguna,
            'nuptk' => $this->nuptk,
            'jabatan' => $this->jabatan,
        ]);

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil diperbarui.');
    }

    public function delete($id)
    {
        WaliKelas::findOrFail($id)->delete();
        session()->flash('success', 'Wali Kelas berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.walikelas.index', [
            'dataWK' => WaliKelas::with('pengguna')
                ->whereHas('pengguna', fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                )
                ->orderBy('id_walikelas', 'DESC')
                ->get(),
        
            'pengguna' => Pengguna::orderBy('name')->get(),
        ]);        
    }
}
