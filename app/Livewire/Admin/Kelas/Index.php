<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Kelas;
use App\Models\WaliKelas;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;


class Index extends Component
{
    use WithPagination;

    // ── Form fields ──
    public $nama_kelas   = '';
    public $tingkat      = '';
    public $jurusan      = '';
    public $tahun_ajaran = '';
    public $id_walikelas = '';
    public $editingId    = null;

    // ── Wali kelas options (reaktif ke Alpine) ──
    public $waliKelasOptions = [];

    // ── Search, filter, sort, pagination, trash ──
    public $search        = '';
    public $filterTingkat = '';
    public $filterJurusan = '';
    public $filterTahun   = '';
    public $filterWali    = '';
    public $sortBy        = 'terbaru';
    public $perPage       = 10;
    public $showTrash     = false;

    protected $queryString = [
        'search'        => ['except' => ''],
        'filterTingkat' => ['except' => ''],
        'filterJurusan' => ['except' => ''],
        'filterTahun'   => ['except' => ''],
        'filterWali'    => ['except' => ''],
        'sortBy'        => ['except' => 'terbaru'],
        'perPage'       => ['except' => 10],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }
    public function updatedFilterTingkat(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }
    public function updatedFilterJurusan(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }
    public function updatedFilterTahun(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }
    public function updatedFilterWali(): void
    {
        $this->resetPage();
        $this->dispatchFilterChanged();
    }

    private function dispatchFilterChanged(): void
    {
        $this->dispatch('filter-kelas-changed', [
            'filterTingkat' => $this->filterTingkat,
            'filterJurusan' => $this->filterJurusan,
            'filterTahun'   => $this->filterTahun,
            'filterWali'    => $this->filterWali,
            'search'        => $this->search,
        ]);
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function updatingShowTrash()
    {
        $this->resetPage();
    }

    // ── Helper: build waliKelasOptions berdasarkan editingId saat ini ──
    private function buildWaliKelasOptions(): void
    {
        $currentWaliId = $this->editingId
            ? Kelas::where('id_kelas', $this->editingId)->value('id_walikelas')
            : null;

        $sudahDipakai = Kelas::whereNotNull('id_walikelas')
            ->when($this->editingId, fn($q) => $q->where('id_kelas', '!=', $this->editingId))
            ->pluck('id_walikelas');

        $this->waliKelasOptions = WaliKelas::with('pengguna')
            ->where(function ($q) use ($sudahDipakai, $currentWaliId) {
                $q->whereNotIn('id_walikelas', $sudahDipakai);
                if ($currentWaliId) {
                    $q->orWhere('id_walikelas', $currentWaliId);
                }
            })
            ->get()
            ->map(fn($w) => [
                'id'   => $w->id_walikelas,
                'name' => optional($w->pengguna)->name ?? '-',
            ])
            ->values()
            ->toArray();
    }

    // ── Reset form ──
    public function resetForm(): void
    {
        $this->nama_kelas   = '';
        $this->tingkat      = '';
        $this->jurusan      = '';
        $this->tahun_ajaran = '';
        $this->id_walikelas = '';
        $this->editingId    = null;
        $this->resetErrorBag();
    }

    // ── Reset semua filter ──
    public function resetFilters(): void
    {
        $this->search        = '';
        $this->filterTingkat = '';
        $this->filterJurusan = '';
        $this->filterTahun   = '';
        $this->filterWali    = '';
        $this->sortBy        = 'terbaru';
        $this->resetPage();
    }

    // ── Simpan / Update ──
    public function save(): void
    {
        $this->validate([
            'nama_kelas'   => 'required|string|max:100',
            'tingkat'      => ['required', 'string', Rule::in(['X', 'XI', 'XII'])],
            'jurusan'      => ['required', 'string', Rule::in([
                'Akomodasi Perhotelan',
                'Rekayasa Perangkat Lunak',
                'Teknik Komputer Jaringan',
                'Teknik Bisnis Sepeda Motor',
            ])],
            'tahun_ajaran' => 'required|string|max:20',
            'id_walikelas' => [
                'nullable',
                Rule::unique('kelas', 'id_walikelas')
                    ->ignore($this->editingId, 'id_kelas')
                    ->whereNull('deleted_at'),
            ],
        ], [
            'tingkat.in'          => 'Tingkat harus X, XI, atau XII.',
            'jurusan.in'          => 'Jurusan tidak valid.',
            'id_walikelas.unique' => 'Wali kelas ini sudah mengampu kelas lain. Pilih wali kelas yang berbeda.',
        ]);

        $data = [
            'nama_kelas'   => $this->nama_kelas,
            'tingkat'      => $this->tingkat,
            'jurusan'      => $this->jurusan,
            'tahun_ajaran' => $this->tahun_ajaran,
            'id_walikelas' => $this->id_walikelas ?: null,
        ];

        if ($this->editingId) {
            Kelas::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Kelas berhasil diperbarui.');
        } else {
            Kelas::create($data);
            session()->flash('success', 'Kelas berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    // ── Edit ──
    public function edit($id): void
    {
        $k = Kelas::findOrFail($id);

        $this->editingId    = $id;
        $this->nama_kelas   = $k->nama_kelas;
        $this->tingkat      = $k->tingkat;
        $this->jurusan      = $k->jurusan;
        $this->tahun_ajaran = $k->tahun_ajaran;
        $this->id_walikelas = $k->id_walikelas;

        // Rebuild options agar wali yang mengampu kelas ini ikut muncul
        $this->buildWaliKelasOptions();
    }

    // ── Soft Delete ──
    public function hapus($id): void
    {
        Kelas::findOrFail($id)->delete();
        session()->flash('success', 'Kelas dipindahkan ke tong sampah.');
    }

    // ── Restore ──
    public function restore($id): void
    {
        Kelas::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Kelas berhasil dipulihkan.');
    }

    // ── Force Delete ──
    public function forceDelete($id): void
    {
        Kelas::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Kelas dihapus permanen.');
    }

    // ── Kosongkan Trash ──
    public function emptyTrash(): void
    {
        Kelas::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    // ── Render ──
    public function render()
    {
        $query = Kelas::with('waliKelas.pengguna');

        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $query->where('nama_kelas', 'like', "%{$this->search}%");
        }

        if ($this->filterTingkat) {
            $query->where('tingkat', $this->filterTingkat);
        }

        if ($this->filterJurusan) {
            $query->where('jurusan', 'like', "%{$this->filterJurusan}%");
        }

        if ($this->filterTahun) {
            $query->where('tahun_ajaran', $this->filterTahun);
        }

        if ($this->filterWali === 'ada') {
            $query->whereNotNull('id_walikelas');
        } elseif ($this->filterWali === 'kosong') {
            $query->whereNull('id_walikelas');
        }

        match ($this->sortBy) {
            'az'    => $query->orderBy('nama_kelas', 'asc'),
            'za'    => $query->orderBy('nama_kelas', 'desc'),
            default => $query->orderBy('id_kelas', 'desc'),
        };

        $tingkatOptions = Kelas::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');
        $jurusanOptions = Kelas::select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        $tahunOptions   = Kelas::select('tahun_ajaran')->distinct()->orderByDesc('tahun_ajaran')->pluck('tahun_ajaran');

        // Build waliKelasOptions kalau belum ada (misal saat pertama load / tambah baru)
        if (empty($this->waliKelasOptions)) {
            $this->buildWaliKelasOptions();
        }

        return view('livewire.admin.kelas.index', [
            'kelas'            => $query->paginate($this->perPage),
            'trashCount'       => Kelas::onlyTrashed()->count(),
            'tingkatOptions'   => $tingkatOptions,
            'jurusanOptions'   => $jurusanOptions,
            'tahunOptions'     => $tahunOptions,
            'hasActiveFilters' => (bool) ($this->search || $this->filterTingkat
                || $this->filterJurusan || $this->filterTahun
                || $this->filterWali),
        ]);
    }

    #[On('refresh')]
    public function refreshData(): void {}
}