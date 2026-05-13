<?php

namespace App\Livewire\Admin\Kelas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;
use App\Models\WaliKelas;

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

    // ── Search, filter, sort, pagination, trash ──
    public $search        = '';
    public $filterTingkat = '';
    public $filterJurusan = '';
    public $filterTahun   = '';
    public $filterWali    = '';   // 'ada' | 'kosong' | ''
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

    public function updatingSearch()        { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }
    public function updatingFilterJurusan() { $this->resetPage(); }
    public function updatingFilterTahun()   { $this->resetPage(); }
    public function updatingFilterWali()    { $this->resetPage(); }
    public function updatingSortBy()        { $this->resetPage(); }
    public function updatingPerPage()       { $this->resetPage(); }
    public function updatingShowTrash()     { $this->resetPage(); }

    // ── Reset form ──
    public function resetForm()
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
    public function resetFilters()
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
    public function save()
    {
        $this->validate([
            'nama_kelas'   => 'required|string|max:100',
            'tingkat'      => 'required|string|max:20',
            'jurusan'      => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:20',
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
    public function edit($id)
    {
        $k = Kelas::findOrFail($id);

        $this->editingId    = $id;
        $this->nama_kelas   = $k->nama_kelas;
        $this->tingkat      = $k->tingkat;
        $this->jurusan      = $k->jurusan;
        $this->tahun_ajaran = $k->tahun_ajaran;
        $this->id_walikelas = $k->id_walikelas;
    }

    // ── Soft Delete ──
    public function hapus($id)
    {
        Kelas::findOrFail($id)->delete();
        session()->flash('success', 'Kelas dipindahkan ke tong sampah.');
    }

    // ── Restore ──
    public function restore($id)
    {
        Kelas::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Kelas berhasil dipulihkan.');
    }

    // ── Force Delete ──
    public function forceDelete($id)
    {
        Kelas::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Kelas dihapus permanen.');
    }

    // ── Kosongkan Trash ──
    public function emptyTrash()
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

        // Search nama kelas
        if ($this->search) {
            $query->where('nama_kelas', 'like', "%{$this->search}%");
        }

        // Filter tingkat
        if ($this->filterTingkat) {
            $query->where('tingkat', $this->filterTingkat);
        }

        // Filter jurusan
        if ($this->filterJurusan) {
            $query->where('jurusan', 'like', "%{$this->filterJurusan}%");
        }

        // Filter tahun ajaran
        if ($this->filterTahun) {
            $query->where('tahun_ajaran', $this->filterTahun);
        }

        // Filter status wali kelas
        if ($this->filterWali === 'ada') {
            $query->whereNotNull('id_walikelas');
        } elseif ($this->filterWali === 'kosong') {
            $query->whereNull('id_walikelas');
        }

        // Sorting
        match ($this->sortBy) {
            'az'    => $query->orderBy('nama_kelas', 'asc'),
            'za'    => $query->orderBy('nama_kelas', 'desc'),
            default => $query->orderBy('id_kelas', 'desc'),
        };

        // Opsi dinamis untuk dropdown filter (ambil dari DB)
        $tingkatOptions   = Kelas::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');
        $jurusanOptions   = Kelas::select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        $tahunOptions     = Kelas::select('tahun_ajaran')->distinct()->orderByDesc('tahun_ajaran')->pluck('tahun_ajaran');

        return view('livewire.admin.kelas.index', [
            'kelas'            => $query->paginate($this->perPage),
            'waliKelasList'    => WaliKelas::with('pengguna')->get(),
            'trashCount'       => Kelas::onlyTrashed()->count(),
            'tingkatOptions'   => $tingkatOptions,
            'jurusanOptions'   => $jurusanOptions,
            'tahunOptions'     => $tahunOptions,
            'hasActiveFilters' => (bool) ($this->search || $this->filterTingkat
                                  || $this->filterJurusan || $this->filterTahun
                                  || $this->filterWali),
        ]);
    }
}