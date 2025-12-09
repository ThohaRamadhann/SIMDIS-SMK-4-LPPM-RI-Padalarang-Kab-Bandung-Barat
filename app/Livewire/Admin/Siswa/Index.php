<?php

namespace App\Livewire\Admin\Siswa;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\WaliMurid;   

class Index extends Component
{
    public $search = '';

    public $id_siswa;
    public $nama;
    public $nis;
    public $status = 'aktif';
    public $id_kelas;
    public $id_walimurid;
    public $isEdit = false;

    public function resetForm()
    {
        $this->id_siswa = null;
        $this->nama = '';
        $this->nis = '';
        $this->status = 'aktif';
        $this->id_kelas = '';
        $this->id_walimurid = '';
        $this->isEdit = false;

        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'nis' => 'required|unique:siswa,nis',
            'id_kelas' => 'required',
            'id_walimurid' => 'required',
        ]);

        Siswa::create([
            'nama' => $this->nama,
            'nis' => $this->nis,
            'status' => $this->status,
            'id_kelas' => $this->id_kelas,
            'id_walimurid' => $this->id_walimurid,
        ]);

        $this->resetForm();
        session()->flash('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $s = Siswa::findOrFail($id);

        $this->id_siswa = $s->id_siswa;
        $this->nama = $s->nama;
        $this->nis = $s->nis;
        $this->status = $s->status;
        $this->id_kelas = $s->id_kelas;
        $this->id_walimurid = $s->id_walimurid;

        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'nis'  => 'required|unique:siswa,nis,' . $this->id_siswa . ',id_siswa',
            'id_kelas' => 'required',
            'id_walimurid' => 'required',
        ]);

        Siswa::findOrFail($this->id_siswa)->update([
            'nama' => $this->nama,
            'nis' => $this->nis,
            'status' => $this->status,
            'id_kelas' => $this->id_kelas,
            'id_walimurid' => $this->id_walimurid,
        ]);

        $this->resetForm();
        session()->flash('success', 'Siswa berhasil diperbarui.');
    }

    public function delete($id)
    {
        Siswa::findOrFail($id)->delete();
        session()->flash('success', 'Siswa berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.siswa.index', [
            'dataSiswa' => Siswa::with(['kelas', 'waliMurid.pengguna'])
                ->where('nama', 'like', "%{$this->search}%")
                ->orderBy('nama')
                ->get(),

            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'wali' => WaliMurid::with('pengguna')->orderBy('id_walimurid')->get(),
        ]);
    }
}