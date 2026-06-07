<?php

namespace App\Livewire\Admin\Siswa;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $id_siswa;
    public $nama;
    public $nis;
    public $status     = 'aktif';
    public $id_kelas;
    public $id_walisiswa;
    public $isEdit     = false;

    // ── Search, filter, sort, pagination ──
    public $search            = '';
    public $filterKelas       = '';
    public $filterStatus      = '';
    public $filterTahunAjaran = '';
    public $sortBy            = 'az';
    public $perPage           = 10;

    // ── Soft delete ──
    public $showTrash = false;

    protected $queryString = [
        'search'            => ['except' => ''],
        'filterKelas'       => ['except' => ''],
        'filterStatus'      => ['except' => ''],
        'filterTahunAjaran' => ['except' => ''],
        'sortBy'            => ['except' => 'az'],
        'perPage'           => ['except' => 10],
    ];
    
    public function updatedFilterKelas(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }

    public function updatedFilterTahunAjaran(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }

    private function dispatchFilterChanged(): void
    {
        $this->dispatch('filter-changed', [
            'filterTahunAjaran' => $this->filterTahunAjaran,
            'filterKelas'       => $this->filterKelas,
            'filterStatus'      => $this->filterStatus,
            'search'            => $this->search,
        ]);
    }


    public function resetForm()
    {
        $this->id_siswa     = null;
        $this->nama         = '';
        $this->nis          = '';
        $this->status       = 'aktif';
        $this->id_kelas     = '';
        $this->id_walisiswa = '';
        $this->isEdit       = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate([
            'nama'         => 'required',
            'nis'          => 'required|unique:siswa,nis',
            'id_kelas'     => 'required',
            'id_walisiswa' => 'required',
        ]);

        Siswa::create([
            'nama'         => $this->nama,
            'nis'          => $this->nis,
            'status'       => $this->status,
            'id_kelas'     => $this->id_kelas,
            'id_walisiswa' => $this->id_walisiswa,
        ]);

        $this->resetForm();
        session()->flash('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $s = Siswa::findOrFail($id);

        $this->id_siswa     = $s->id_siswa;
        $this->nama         = $s->nama;
        $this->nis          = $s->nis;
        $this->status       = $s->status;
        $this->id_kelas     = $s->id_kelas;
        $this->id_walisiswa = $s->id_walisiswa;
        $this->isEdit       = true;
    }

    public function update()
    {
        $this->validate([
            'nama'         => 'required',
            'nis'          => 'required|unique:siswa,nis,' . $this->id_siswa . ',id_siswa',
            'id_kelas'     => 'required',
            'id_walisiswa' => 'required',
        ]);

        Siswa::findOrFail($this->id_siswa)->update([
            'nama'         => $this->nama,
            'nis'          => $this->nis,
            'status'       => $this->status,
            'id_kelas'     => $this->id_kelas,
            'id_walisiswa' => $this->id_walisiswa,
        ]);

        $this->resetForm();
        session()->flash('success', 'Siswa berhasil diperbarui.');
    }

    public function delete($id)
    {
        Siswa::findOrFail($id)->delete();
        $this->resetForm();
        session()->flash('success', 'Siswa dipindahkan ke tong sampah.');
    }

    public function restore($id)
    {
        Siswa::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Siswa berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        Siswa::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Siswa dihapus permanen.');
    }

    public function emptyTrash()
    {
        Siswa::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    public function render()
    {
        $query = Siswa::with(['kelas', 'waliSiswa.pengguna']);

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterKelas) {
            $query->where('id_kelas', $this->filterKelas);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterTahunAjaran) {
            $query->whereHas('kelas', function ($q) {
                $q->where('tahun_ajaran', $this->filterTahunAjaran);
            });
        }

        match ($this->sortBy) {
            'za'      => $query->orderBy('nama', 'desc'),
            'terbaru' => $query->orderBy('id_siswa', 'desc'),
            default   => $query->orderBy('nama', 'asc'),
        };

        return view('livewire.admin.siswa.index', [
            'dataSiswa'       => $query->paginate($this->perPage),
            'kelas'           => Kelas::orderBy('nama_kelas')->get(),
            'wali'            => WaliSiswa::with('pengguna')->orderBy('id_walisiswa')->get(),
            'allKelas'        => Kelas::orderBy('nama_kelas')->get(),
            'trashCount'      => Siswa::onlyTrashed()->count(),
            'tahunAjaranList' => Kelas::select('tahun_ajaran')
                ->distinct()
                ->orderByDesc('tahun_ajaran')
                ->pluck('tahun_ajaran'),
        ]);
    }

    #[On('refresh')]
    public function refreshData(): void {}
}
