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
    public $status       = 'aktif';
    public $id_kelas;
    public $id_walisiswa;
    public $isEdit       = false;

    // ── AJAX search wali ──
    public $waliSearch        = '';   // keyword yang diketik admin
    public $waliSearchResults = [];   // hasil query (maks 15)
    public $waliSelectedName  = '';   // label yang ditampilkan di tombol

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

    // ── AJAX: dipanggil wire:model.live saat admin mengetik di kotak cari wali ──
    public function updatedWaliSearch(): void
    {
        $q = trim($this->waliSearch);

        if (strlen($q) < 2) {
            $this->waliSearchResults = [];
            return;
        }

        $this->waliSearchResults = WaliSiswa::with('pengguna')
            ->whereHas('pengguna', fn($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->orderBy('id_walisiswa')
            ->limit(15)
            ->get()
            ->map(fn($w) => [
                'id'   => $w->id_walisiswa,
                'name' => optional($w->pengguna)->name . ($w->hubungan ? ' (' . $w->hubungan . ')' : ''),
            ])
            ->toArray();
    }

    // ── Dipanggil saat admin klik salah satu hasil search wali ──
    public function selectWali(int $id, string $name): void
    {
        $this->id_walisiswa      = $id;
        $this->waliSelectedName  = $name;
        $this->waliSearch        = '';
        $this->waliSearchResults = [];
    }

    // ── Reset wali field ──
    public function clearWali(): void
    {
        $this->id_walisiswa      = '';
        $this->waliSelectedName  = '';
        $this->waliSearch        = '';
        $this->waliSearchResults = [];
    }

    public function updatedFilterKelas(): void { $this->resetPage(); $this->dispatchFilterChanged(); }
    public function updatedFilterStatus(): void { $this->resetPage(); $this->dispatchFilterChanged(); }
    public function updatedFilterTahunAjaran(): void { $this->resetPage(); $this->dispatchFilterChanged(); }
    public function updatedSearch(): void { $this->resetPage(); $this->dispatchFilterChanged(); }

    private function dispatchFilterChanged(): void
    {
        $this->dispatch('filter-changed', [
            'filterTahunAjaran' => $this->filterTahunAjaran,
            'filterKelas'       => $this->filterKelas,
            'filterStatus'      => $this->filterStatus,
            'search'            => $this->search,
        ]);
    }

    public function resetForm(): void
    {
        $this->id_siswa          = null;
        $this->nama              = '';
        $this->nis               = '';
        $this->status            = 'aktif';
        $this->id_kelas          = '';
        $this->id_walisiswa      = '';
        $this->waliSelectedName  = '';
        $this->waliSearch        = '';
        $this->waliSearchResults = [];
        $this->isEdit            = false;
        $this->resetErrorBag();
    }

    public function store(): void
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

    public function edit(int $id): void
    {
        $s = Siswa::with('waliSiswa.pengguna')->findOrFail($id);

        $this->id_siswa         = $s->id_siswa;
        $this->nama             = $s->nama;
        $this->nis              = $s->nis;
        $this->status           = $s->status;
        $this->id_kelas         = $s->id_kelas;
        $this->id_walisiswa     = $s->id_walisiswa;
        // Pre-populate label wali yang sudah terpilih
        $this->waliSelectedName = optional(optional($s->waliSiswa)->pengguna)->name
            . ($s->waliSiswa?->hubungan ? ' (' . $s->waliSiswa->hubungan . ')' : '');
        $this->isEdit           = true;
    }

    public function update(): void
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

    public function delete(int $id): void
    {
        Siswa::findOrFail($id)->delete();
        $this->resetForm();
        session()->flash('success', 'Siswa dipindahkan ke tong sampah.');
    }

    public function restore(int $id): void
    {
        Siswa::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Siswa berhasil dipulihkan.');
    }

    public function forceDelete(int $id): void
    {
        Siswa::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Siswa dihapus permanen.');
    }

    public function emptyTrash(): void
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
            // Kelas tetap dimuat semua (biasanya puluhan, aman)
            'kelas'           => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(),
            'allKelas'        => Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(),
            'trashCount'      => Siswa::onlyTrashed()->count(),
            'tahunAjaranList' => Kelas::select('tahun_ajaran')
                ->distinct()
                ->orderByDesc('tahun_ajaran')
                ->pluck('tahun_ajaran'),
            // 'wali' DIHAPUS dari sini — tidak lagi load semua data wali
        ]);
    }

    #[On('refresh')]
    public function refreshData(): void {}
}