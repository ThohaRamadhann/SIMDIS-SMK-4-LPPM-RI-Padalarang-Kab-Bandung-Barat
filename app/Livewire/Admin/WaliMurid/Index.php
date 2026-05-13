<?php

namespace App\Livewire\Admin\WaliMurid;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WaliMurid;
use App\Models\Pengguna;

class Index extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $id_walimurid;
    public $id_pengguna;
    public $hubungan = '';
    public $isEdit   = false;

    // ── Search, sort, pagination ──
    public $search  = '';
    public $sortBy  = 'terbaru';   // terbaru | az | za
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

    // ── Auto-load data saat id_pengguna berubah ──
    public function updated($property)
    {
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
                $this->hubungan     = $waliMurid->hubungan;
                $this->isEdit       = true;
            } else {
                $this->id_walimurid = null;
                $this->hubungan     = '';
                $this->isEdit       = false;
            }
        }
    }

    // ── Reset form ──
    public function resetForm()
    {
        $this->id_walimurid = null;
        $this->id_pengguna  = '';
        $this->hubungan     = '';
        $this->isEdit       = false;
        $this->resetErrorBag();
    }

    // ── Simpan / Update ──
    public function simpan()
    {
        $this->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'hubungan'    => 'required|string|max:50',
        ]);

        if ($this->isEdit) {
            WaliMurid::findOrFail($this->id_walimurid)->update([
                'hubungan' => $this->hubungan,
            ]);
            $message = 'Data berhasil diperbarui';
        } else {
            WaliMurid::create([
                'id_pengguna' => $this->id_pengguna,
                'hubungan'    => $this->hubungan,
            ]);
            $message = 'Data berhasil disimpan';
        }

        $this->resetForm();
        session()->flash('success', $message);
    }

    // ── Edit ──
    public function edit($id)
    {
        $data = WaliMurid::findOrFail($id);

        $this->id_walimurid = $data->id_walimurid;
        $this->id_pengguna  = $data->id_pengguna;
        $this->hubungan     = $data->hubungan;
        $this->isEdit       = true;
    }

    // ── Soft Delete ──
    public function hapus($id)
    {
        WaliMurid::findOrFail($id)->delete();
        $this->resetForm();
        session()->flash('success', 'Data dipindahkan ke tong sampah.');
    }

    // ── Restore ──
    public function restore($id)
    {
        WaliMurid::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Data berhasil dipulihkan.');
    }

    // ── Hapus permanen ──
    public function forceDelete($id)
    {
        WaliMurid::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Data dihapus permanen.');
    }

    // ── Kosongkan trash ──
    public function emptyTrash()
    {
        WaliMurid::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    // ── Render ──
    public function render()
    {
        $query = WaliMurid::with('pengguna');

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        // Search nama, username, atau hubungan
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('pengguna', function ($p) {
                    $p->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                })->orWhere('hubungan', 'like', '%' . $this->search . '%');
            });
        }

        // Sorting
        match ($this->sortBy) {
            'az'     => $query->join('pengguna', 'wali_murid.id_pengguna', '=', 'pengguna.id_pengguna')
                              ->orderBy('pengguna.name', 'asc')
                              ->select('wali_murid.*'),
            'za'     => $query->join('pengguna', 'wali_murid.id_pengguna', '=', 'pengguna.id_pengguna')
                              ->orderBy('pengguna.name', 'desc')
                              ->select('wali_murid.*'),
            default  => $query->orderBy('wali_murid.id_walimurid', 'desc'),
        };

        // Daftar pengguna untuk dropdown (role orang_tua)
        $penggunaList = Pengguna::whereHas('role', function ($q) {
            $q->where('nama_role', 'orang_tua');
        })->orderBy('name')->get();

        if ($this->isEdit && $this->id_pengguna) {
            $current      = Pengguna::find($this->id_pengguna);
            $penggunaList = $penggunaList->push($current)->unique('id_pengguna')->sortBy('name');
        }

        return view('livewire.admin.walimurid.index', [
            'data'       => $query->paginate($this->perPage),
            'pengguna'   => $penggunaList,
            'trashCount' => WaliMurid::onlyTrashed()->count(),
        ]);
    }
}