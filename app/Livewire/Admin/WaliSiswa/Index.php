<?php

namespace App\Livewire\Admin\WaliSiswa;

use App\Models\Pengguna;
use App\Models\WaliSiswa;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $id_walisiswa;
    public $id_pengguna;
    public $hubungan = '';
    public $isEdit   = false;

    // ── Search, sort, pagination ──
    public $search  = '';
    public $sortBy  = 'terbaru';
    public $perPage = 10;

    // ── Soft delete ──
    public $showTrash = false;

    protected $queryString = [
        'search'  => ['except' => ''],
        'sortBy'  => ['except' => 'terbaru'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()    { $this->resetPage(); }
    public function updatingSortBy()    { $this->resetPage(); }
    public function updatingPerPage()   { $this->resetPage(); }
    public function updatingShowTrash() { $this->resetPage(); }

    public function updated($property)
    {
        if ($property === 'id_pengguna') {
            $this->loadWaliSiswaData();
        }
    }

    public function loadWaliSiswaData()
    {
        if ($this->id_pengguna) {
            $waliSiswa = WaliSiswa::where('id_pengguna', $this->id_pengguna)->first();

            if ($waliSiswa) {
                $this->id_walisiswa = $waliSiswa->id_walisiswa;
                $this->hubungan     = $waliSiswa->hubungan;
                $this->isEdit       = true;
            } else {
                $this->id_walisiswa = null;
                $this->hubungan     = '';
                $this->isEdit       = false;
            }
        }
    }

    public function resetForm()
    {
        $this->id_walisiswa = null;
        $this->id_pengguna  = '';
        $this->hubungan     = '';
        $this->isEdit       = false;
        $this->resetErrorBag();
    }

    public function simpan()
    {
        $this->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'hubungan'    => 'required|string|max:50',
        ]);

        if ($this->isEdit) {
            WaliSiswa::findOrFail($this->id_walisiswa)->update([
                'hubungan' => $this->hubungan,
            ]);
            $message = 'Data berhasil diperbarui';
        } else {
            WaliSiswa::create([
                'id_pengguna' => $this->id_pengguna,
                'hubungan'    => $this->hubungan,
            ]);
            $message = 'Data berhasil disimpan';
        }

        $this->resetForm();
        session()->flash('success', $message);
    }

    public function edit($id)
    {
        $data = WaliSiswa::findOrFail($id);

        $this->id_walisiswa = $data->id_walisiswa;
        $this->id_pengguna  = $data->id_pengguna;
        $this->hubungan     = $data->hubungan;
        $this->isEdit       = true;
    }

    public function hapus($id)
    {
        WaliSiswa::findOrFail($id)->delete();
        $this->resetForm();
        session()->flash('success', 'Data dipindahkan ke tong sampah.');
    }

    public function restore($id)
    {
        WaliSiswa::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Data berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        WaliSiswa::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Data dihapus permanen.');
    }

    public function emptyTrash()
    {
        WaliSiswa::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    public function render()
    {
        $query = WaliSiswa::with('pengguna');

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('pengguna', function ($p) {
                    $p->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                })->orWhere('hubungan', 'like', '%' . $this->search . '%');
            });
        }

        match ($this->sortBy) {
            'az'    => $query->join('pengguna', 'wali_siswa.id_pengguna', '=', 'pengguna.id_pengguna')
                             ->orderBy('pengguna.name', 'asc')
                             ->select('wali_siswa.*'),
            'za'    => $query->join('pengguna', 'wali_siswa.id_pengguna', '=', 'pengguna.id_pengguna')
                             ->orderBy('pengguna.name', 'desc')
                             ->select('wali_siswa.*'),
            default => $query->orderBy('wali_siswa.id_walisiswa', 'desc'),
        };

        $penggunaList = Pengguna::whereHas('role', function ($q) {
            $q->where('nama_role', 'wali_siswa');
        })->orderBy('name')->get();

        if ($this->isEdit && $this->id_pengguna) {
            $current      = Pengguna::find($this->id_pengguna);
            $penggunaList = $penggunaList->push($current)->unique('id_pengguna')->sortBy('name');
        }

        return view('livewire.admin.walisiswa.index', [
            'data'       => $query->paginate($this->perPage),
            'pengguna'   => $penggunaList,
            'trashCount' => WaliSiswa::onlyTrashed()->count(),
        ]);
    }

    #[On('refresh')]
    public function refreshData(): void {}
}