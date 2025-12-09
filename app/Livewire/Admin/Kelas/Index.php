<?php

namespace App\Livewire\Admin\Kelas;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\WaliKelas;

class Index extends Component
{
    public $nama_kelas, $tingkat, $jurusan, $tahun_ajaran, $id_walikelas, $editingId = null;

    public function render()
    {
        return view('livewire.admin.kelas.index', [
            'kelas' => Kelas::with('waliKelas.pengguna')->get(),
            'waliKelasList' => WaliKelas::with('pengguna')->get()
        ]);
    }

    public function resetForm()
    {
        $this->nama_kelas = '';
        $this->tingkat = '';
        $this->jurusan = '';
        $this->tahun_ajaran = '';
        $this->id_walikelas = '';

        $this->editingId = null;
    }

    public function save()
    {
        $data = [
            'nama_kelas' => $this->nama_kelas,
            'tingkat' => $this->tingkat,
            'jurusan' => $this->jurusan,
            'tahun_ajaran' => $this->tahun_ajaran,
            'id_walikelas' => $this->id_walikelas ?: null,
        ];

        if ($this->editingId) {
            Kelas::where('id_kelas', $this->editingId)->update($data);
            session()->flash('success', 'Kelas berhasil diperbarui');
        } else {
            Kelas::create($data);
            session()->flash('success', 'Kelas berhasil ditambahkan');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $k = Kelas::findOrFail($id);

        $this->editingId = $id;
        $this->nama_kelas = $k->nama_kelas;
        $this->tingkat = $k->tingkat;
        $this->jurusan = $k->jurusan;
        $this->tahun_ajaran = $k->tahun_ajaran;
        $this->id_walikelas = $k->id_walikelas;
    }

    public function delete($id)
    {
        Kelas::where('id_kelas', $id)->delete();
        session()->flash('success', 'Kelas berhasil dihapus');
    }
}