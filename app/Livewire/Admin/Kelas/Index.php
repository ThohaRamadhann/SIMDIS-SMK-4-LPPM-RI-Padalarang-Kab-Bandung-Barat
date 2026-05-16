<?php

namespace App\Livewire\Admin\Kelas;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
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

    public function updatingSearch()        { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }
    public function updatingFilterJurusan() { $this->resetPage(); }
    public function updatingFilterTahun()   { $this->resetPage(); }
    public function updatingFilterWali()    { $this->resetPage(); }
    public function updatingSortBy()        { $this->resetPage(); }
    public function updatingPerPage()       { $this->resetPage(); }
    public function updatingShowTrash()     { $this->resetPage(); }

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
                'Teknik Jaringan Akses',
                'Rekayasa Perangkat Lunak',
                'Perhotelan',
                'Otomotif',
            ])],
            'tahun_ajaran' => 'required|string|max:20',

            // Unique: 1 wali kelas hanya boleh mengampu 1 kelas
            // Saat edit, ignore kelas yang sedang diedit
            'id_walikelas' => [
                'nullable',
                Rule::unique('kelas', 'id_walikelas')
                    ->ignore($this->editingId, 'id_kelas')
                    ->whereNull('deleted_at'), // abaikan kelas yang sudah dihapus (soft delete)
            ],
        ], [
            'tingkat.in'      => 'Tingkat harus X, XI, atau XII.',
            'jurusan.in'      => 'Jurusan tidak valid.',
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

        // ── Wali kelas yang sudah mengampu kelas aktif lain (kecuali kelas yang sedang diedit) ──
        $sudahDipakai = Kelas::whereNotNull('id_walikelas')
            ->when($this->editingId, fn($q) => $q->where('id_kelas', '!=', $this->editingId))
            ->pluck('id_walikelas');

        // Hanya tampilkan wali yang belum mengampu kelas manapun
        // + wali dari kelas yang sedang diedit (agar tetap muncul di dropdown)
        $waliKelasList = WaliKelas::with('pengguna')
            ->whereNotIn('id_walikelas', $sudahDipakai)
            ->get();

        return view('livewire.admin.kelas.index', [
            'kelas'            => $query->paginate($this->perPage),
            'waliKelasList'    => $waliKelasList,
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