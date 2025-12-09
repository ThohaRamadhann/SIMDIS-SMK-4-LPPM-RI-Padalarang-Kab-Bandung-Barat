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
    public $nuptk = '';
    public $jabatan = '';
    public $isEdit = false;

    // Listener untuk ketika id_pengguna berubah
    protected $listeners = ['updatedIdPengguna'];

    public function updatedIdPengguna($value)
    {
        // Jika ada pengguna yang dipilih, cek apakah sudah ada data wali kelas
        if ($value) {
            $waliKelas = WaliKelas::where('id_pengguna', $value)->first();
            
            if ($waliKelas) {
                // Jika sudah ada data, isi form dengan data yang ada
                $this->nuptk = $waliKelas->nuptk;
                $this->jabatan = $waliKelas->jabatan;
            } else {
                // Jika belum ada data, kosongkan form
                $this->nuptk = '';
                $this->jabatan = '';
            }
        }
    }

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
            // NUPTK dan jabatan tidak required karena opsional
        ]);

        // Cek apakah sudah ada data untuk pengguna ini
        $existing = WaliKelas::where('id_pengguna', $this->id_pengguna)->first();
        
        if ($existing) {
            // Update data yang sudah ada
            $existing->update([
                'nuptk' => $this->nuptk,
                'jabatan' => $this->jabatan,
            ]);
        } else {
            // Buat data baru
            WaliKelas::create([
                'id_pengguna' => $this->id_pengguna,
                'nuptk' => $this->nuptk,
                'jabatan' => $this->jabatan,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil disimpan.');
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
        // Ambil pengguna yang memiliki role wali_kelas
        $waliKelasPengguna = Pengguna::whereHas('role', function($query) {
            $query->where('nama_role', 'wali_kelas');
        })->orderBy('name')->get();
    
        // Jika sedang edit mode, tambahkan pengguna yang sedang diedit (jika belum ada)
        if ($this->isEdit && $this->id_pengguna) {
            $currentPengguna = Pengguna::where('id_pengguna', $this->id_pengguna)->first();
            $waliKelasPengguna = $waliKelasPengguna->push($currentPengguna)->unique('id_pengguna')->sortBy('name');
        }

        return view('livewire.admin.walikelas.index', [
            'dataWK' => WaliKelas::with('pengguna')
                ->whereHas('pengguna', fn($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                )
                ->orderBy('id_walikelas', 'DESC')
                ->get(),
        
            'pengguna' => $waliKelasPengguna,
        ]);        
    }
}