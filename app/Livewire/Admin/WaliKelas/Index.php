<?php

namespace App\Livewire\Admin\WaliKelas;

use App\Models\Pengguna;
use App\Models\WaliKelas;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $id_walikelas;
    public $id_pengguna = '';
    public $nuptk       = '';
    public $jabatan     = '';
    public $isEdit      = false;

    // ── Search, sort, pagination, trash ──
    public $search   = '';
    public $sortBy   = 'terbaru';  // terbaru | az | za
    public $perPage  = 10;
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

    // ── Listener select pengguna ──
    protected $listeners = ['updatedIdPengguna'];

    public function updatedIdPengguna($value)
    {
        if ($value) {
            $wk = WaliKelas::where('id_pengguna', $value)->first();
            $this->nuptk   = $wk?->nuptk   ?? '';
            $this->jabatan = $wk?->jabatan  ?? '';
        } else {
            $this->nuptk = $this->jabatan = '';
        }
    }

    // ── Reset form ──
    public function resetForm()
    {
        $this->id_walikelas = null;
        $this->id_pengguna  = '';
        $this->nuptk        = '';
        $this->jabatan      = '';
        $this->isEdit       = false;
        $this->resetErrorBag();
    }

    // ── Simpan ──
    public function store()
    {
        $this->validate(['id_pengguna' => 'required']);

        $existing = WaliKelas::where('id_pengguna', $this->id_pengguna)->first();

        if ($existing) {
            $existing->update(['nuptk' => $this->nuptk, 'jabatan' => $this->jabatan]);
        } else {
            WaliKelas::create([
                'id_pengguna' => $this->id_pengguna,
                'nuptk'       => $this->nuptk,
                'jabatan'     => $this->jabatan,
            ]);
        }

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil disimpan.');
    }

    // ── Edit ──
    public function edit($id)
    {
        $data = WaliKelas::findOrFail($id);

        $this->id_walikelas = $data->id_walikelas;
        $this->id_pengguna  = $data->id_pengguna;
        $this->nuptk        = $data->nuptk;
        $this->jabatan      = $data->jabatan;
        $this->isEdit       = true;
    }

    // ── Update ──
    public function update()
    {
        $this->validate(['id_pengguna' => 'required']);

        WaliKelas::findOrFail($this->id_walikelas)->update([
            'id_pengguna' => $this->id_pengguna,
            'nuptk'       => $this->nuptk,
            'jabatan'     => $this->jabatan,
        ]);

        $this->resetForm();
        session()->flash('success', 'Wali Kelas berhasil diperbarui.');
    }

    // ── Soft Delete ──
    public function hapus($id)
    {
        WaliKelas::findOrFail($id)->delete();
        session()->flash('success', 'Wali Kelas dipindahkan ke tong sampah.');
    }

    // ── Restore ──
    public function restore($id)
    {
        WaliKelas::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Wali Kelas berhasil dipulihkan.');
    }

    // ── Force Delete ──
    public function forceDelete($id)
    {
        WaliKelas::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Wali Kelas dihapus permanen.');
    }

    // ── Kosongkan Trash ──
    public function emptyTrash()
    {
        WaliKelas::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    // ── Render ──
    public function render()
    {
        // Query utama
        $query = WaliKelas::with('pengguna');

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        // Search berdasarkan nama wali kelas atau NUPTK
        if ($this->search) {
            $keyword = $this->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nuptk', 'like', "%{$keyword}%")
                  ->orWhereHas('pengguna', fn($r) =>
                        $r->where('name', 'like', "%{$keyword}%")
                  );
            });
        }

        // Sorting
        match ($this->sortBy) {
            'az'    => $query->join('pengguna', 'wali_kelas.id_pengguna', '=', 'pengguna.id_pengguna')
                             ->orderBy('pengguna.name', 'asc')
                             ->select('wali_kelas.*'),
            'za'    => $query->join('pengguna', 'wali_kelas.id_pengguna', '=', 'pengguna.id_pengguna')
                             ->orderBy('pengguna.name', 'desc')
                             ->select('wali_kelas.*'),
            default => $query->orderBy('wali_kelas.id_walikelas', 'desc'),
        };

        // Data pengguna untuk dropdown (role wali_kelas)
        $waliKelasPengguna = Pengguna::whereHas('role', function ($q) {
            $q->where('nama_role', 'wali_kelas');
        })->orderBy('name')->get();

        // Saat edit, pastikan pengguna yang diedit tersedia di dropdown
        if ($this->isEdit && $this->id_pengguna) {
            $currentPengguna    = Pengguna::find($this->id_pengguna);
            $waliKelasPengguna  = $waliKelasPengguna
                ->push($currentPengguna)
                ->unique('id_pengguna')
                ->sortBy('name');
        }

        return view('livewire.admin.walikelas.index', [
            'dataWK'     => $query->paginate($this->perPage),
            'pengguna'   => $waliKelasPengguna,
            'trashCount' => WaliKelas::onlyTrashed()->count(),
        ]);
    }

    #[On('refresh')]
public function refreshData(): void {}
}