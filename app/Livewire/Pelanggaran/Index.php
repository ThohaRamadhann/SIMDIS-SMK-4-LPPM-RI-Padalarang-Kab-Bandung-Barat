<?php

namespace App\Livewire\Pelanggaran;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\JenisPelanggaran;
use App\Services\EarlyWarningService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // ── FORM ────────────────────────────────────────────────
    public $id_pelanggaran;
    public $id_siswa;
    public $id_walikelas;
    public $id_jenispelanggaran;
    public $waktu_kejadian;
    public $deskripsi;
    public $status_pembinaan = 'Belum Ditindak';

    // ── MODAL UPDATE STATUS ─────────────────────────────────
    public $showModalStatus   = false;
    public $modalId           = null;   // id_pelanggaran yang sedang diedit
    public $modalSiswa        = '';     // nama siswa (info display)
    public $modalStatus       = 'Belum Ditindak';
    public $modalTanggal      = '';
    public $modalCatatan      = '';

    // ── FILTER ──────────────────────────────────────────────
    public $search          = '';
    public $filterJenis     = '';
    public $filterTingkat   = '';
    public $filterStatus    = '';
    public $filterWaliKelas = '';

    // ── SORT & PAGINATION ───────────────────────────────────
    public $sortBy  = 'terbaru';
    public $perPage = 10;

    // ── MODE ────────────────────────────────────────────────
    public $isEdit    = false;
    public $showTrash = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'terbaru'],
    ];

    // ── RESET PAGE ON FILTER CHANGE ─────────────────────────
    public function updatingSearch()         { $this->resetPage(); }
    public function updatingFilterJenis()    { $this->resetPage(); }
    public function updatingFilterTingkat()  { $this->resetPage(); }
    public function updatingFilterStatus()   { $this->resetPage(); }
    public function updatingFilterWaliKelas(){ $this->resetPage(); }
    public function updatingSortBy()         { $this->resetPage(); }
    public function updatingPerPage()        { $this->resetPage(); }
    public function updatingShowTrash()      { $this->resetPage(); }

    // ── RESET FORM ──────────────────────────────────────────
    public function resetForm(): void
    {
        $this->reset([
            'id_pelanggaran',
            'id_siswa',
            'id_walikelas',
            'id_jenispelanggaran',
            'waktu_kejadian',
            'deskripsi',
            'status_pembinaan',
        ]);

        $this->status_pembinaan = 'Belum Ditindak';
        $this->isEdit           = false;
        $this->resetErrorBag();
    }

    // ── SIMPAN (CREATE) ─────────────────────────────────────
    public function simpan(): void
    {
        $this->validate([
            'id_siswa'            => 'required',
            'id_walikelas'        => 'required',
            'id_jenispelanggaran' => 'required',
            'waktu_kejadian'      => 'required|date',
            'deskripsi'           => 'required',
        ]);

        $pelanggaran = Pelanggaran::create([
            'id_siswa'            => $this->id_siswa,
            'id_walikelas'        => $this->id_walikelas,
            'id_jenispelanggaran' => $this->id_jenispelanggaran,
            'waktu_kejadian'      => $this->waktu_kejadian,
            'deskripsi'           => $this->deskripsi,
            'status_pembinaan'    => $this->status_pembinaan,
        ]);

        $pelanggaran->load([
            'siswa.waliMurid.pengguna',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        app(EarlyWarningService::class)->check($pelanggaran);

        session()->flash('success', 'Pelanggaran berhasil ditambahkan.');
        $this->resetForm();
    }

    // ── EDIT (LOAD DATA) ────────────────────────────────────
    public function edit($id): void
    {
        $data = Pelanggaran::findOrFail($id);

        $this->id_pelanggaran      = $data->id_pelanggaran;
        $this->id_siswa            = $data->id_siswa;
        $this->id_walikelas        = $data->id_walikelas;
        $this->id_jenispelanggaran = $data->id_jenispelanggaran;
        $this->waktu_kejadian      = $data->waktu_kejadian;
        $this->deskripsi           = $data->deskripsi;
        $this->status_pembinaan    = $data->status_pembinaan;
        $this->isEdit              = true;
    }

    // ── UPDATE ──────────────────────────────────────────────
    public function update(): void
    {
        $this->validate([
            'id_siswa'            => 'required',
            'id_walikelas'        => 'required',
            'id_jenispelanggaran' => 'required',
            'waktu_kejadian'      => 'required|date',
            'deskripsi'           => 'required',
        ]);

        Pelanggaran::findOrFail($this->id_pelanggaran)->update([
            'id_siswa'            => $this->id_siswa,
            'id_walikelas'        => $this->id_walikelas,
            'id_jenispelanggaran' => $this->id_jenispelanggaran,
            'waktu_kejadian'      => $this->waktu_kejadian,
            'deskripsi'           => $this->deskripsi,
            'status_pembinaan'    => $this->status_pembinaan,
        ]);

        session()->flash('success', 'Pelanggaran berhasil diperbarui.');
        $this->resetForm();
    }

    // ── HAPUS (SOFT DELETE) ─────────────────────────────────
    public function hapus($id): void
    {
        Pelanggaran::findOrFail($id)->delete();
        session()->flash('success', 'Data dipindahkan ke sampah.');
    }

    // ── RESTORE ─────────────────────────────────────────────
    public function restore($id): void
    {
        Pelanggaran::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Data berhasil dipulihkan.');
    }

    // ── FORCE DELETE ────────────────────────────────────────
    public function forceDelete($id): void
    {
        Pelanggaran::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Data dihapus permanen.');
    }

    // ── EMPTY TRASH ─────────────────────────────────────────
    public function emptyTrash(): void
    {
        Pelanggaran::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    // ── BUKA MODAL UPDATE STATUS PEMBINAAN ──────────────────
    public function bukaModalStatus($id): void
    {
        $data = Pelanggaran::with('siswa')->findOrFail($id);

        $this->modalId      = $data->id_pelanggaran;
        $this->modalSiswa   = optional($data->siswa)->nama ?? '-';
        $this->modalStatus  = $data->status_pembinaan;
        $this->modalTanggal = $data->tanggal_pembinaan
            ? \Carbon\Carbon::parse($data->tanggal_pembinaan)->format('Y-m-d')
            : '';
        $this->modalCatatan    = $data->catatan_bk ?? '';
        $this->showModalStatus = true;
    }

    // ── TUTUP MODAL ─────────────────────────────────────────
    public function tutupModalStatus(): void
    {
        $this->showModalStatus = false;
        $this->modalId         = null;
        $this->modalSiswa      = '';
        $this->modalStatus     = 'Belum Ditindak';
        $this->modalTanggal    = '';
        $this->modalCatatan    = '';
        $this->resetErrorBag();
    }

    // ── SIMPAN STATUS PEMBINAAN ─────────────────────────────
    public function simpanStatus(): void
    {
        $this->validate([
            'modalStatus'  => 'required|in:Belum Ditindak,Dalam Proses,Selesai',
            'modalTanggal' => 'nullable|date',
            'modalCatatan' => 'nullable|string|max:1000',
        ], [
            'modalStatus.required' => 'Status pembinaan wajib dipilih.',
            'modalStatus.in'       => 'Status tidak valid.',
            'modalTanggal.date'    => 'Format tanggal tidak valid.',
        ]);

        Pelanggaran::findOrFail($this->modalId)->update([
            'status_pembinaan' => $this->modalStatus,
            'tanggal_pembinaan' => $this->modalTanggal ?: null,
            'catatan_bk'        => $this->modalCatatan ?: null,
        ]);

        session()->flash('success', 'Status pembinaan berhasil diperbarui.');
        $this->tutupModalStatus();
    }

    // ── QUICK STATUS (klik langsung tanpa modal) ─────────────
    // Untuk toggle cepat: Belum Ditindak → Dalam Proses → Selesai
    public function quickStatus($id): void
    {
        $p = Pelanggaran::findOrFail($id);

        $urutan = ['Belum Ditindak', 'Dalam Proses', 'Selesai'];
        $index  = array_search($p->status_pembinaan, $urutan);
        $next   = $urutan[($index + 1) % count($urutan)];

        $p->update(['status_pembinaan' => $next]);

        session()->flash('success', "Status diubah ke: {$next}");
    }

    // ── RENDER ──────────────────────────────────────────────
    public function render()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $query = Pelanggaran::with([
            'siswa.kelas',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        // ROLE FILTER
        if ($role === 'wali_kelas') {
            $query->where('id_walikelas', optional($user->waliKelas)->id_walikelas);
        }

        if ($role === 'orang_tua') {
            $query->whereHas('siswa', function ($q) use ($user) {
                $q->where('id_walimurid', optional($user->waliMurid)->id_walimurid);
            });
        }

        // SOFT DELETE
        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        // SEARCH
        if ($this->search) {
            $query->whereHas('siswa', function ($s) {
                $s->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nis',  'like', '%' . $this->search . '%');
            });
        }

        // FILTER JENIS
        if ($this->filterJenis) {
            $query->where('id_jenispelanggaran', $this->filterJenis);
        }

        // FILTER TINGKAT — fix: pakai kolom tingkat_pelanggaran
        if ($this->filterTingkat) {
            $query->whereHas('jenisPelanggaran', function ($q) {
                $q->where('tingkat_pelanggaran', $this->filterTingkat);
            });
        }

        // FILTER STATUS — fix: pakai kolom status_pembinaan
        if ($this->filterStatus) {
            $query->where('status_pembinaan', $this->filterStatus);
        }

        // FILTER WALI KELAS
        if ($this->filterWaliKelas) {
            $query->where('id_walikelas', $this->filterWaliKelas);
        }

        // SORT
        match ($this->sortBy) {
            'terlama' => $query->orderBy('created_at', 'asc'),
            'az'      => $query->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id_siswa')
                               ->orderBy('siswa.nama', 'asc')
                               ->select('pelanggaran.*'),
            'za'      => $query->join('siswa', 'pelanggaran.id_siswa', '=', 'siswa.id_siswa')
                               ->orderBy('siswa.nama', 'desc')
                               ->select('pelanggaran.*'),
            default   => $query->orderBy('created_at', 'desc'),
        };

        return view('livewire.pelanggaran.index', [
            'pelanggarans'  => $query->paginate($this->perPage),
            'siswaList'     => Siswa::orderBy('nama')->get(),
            'waliKelasList' => WaliKelas::with('pengguna')->get(),
            'jenisList'     => JenisPelanggaran::orderBy('nama_pelanggaran')->get(),
            'trashCount'    => Pelanggaran::onlyTrashed()->count(),
            'role'          => $role,
        ]);
    }
}