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

    // ── MODAL UPDATE STATUS ─────────────────────────────────
public $showModalStatus = false;
public $modalId         = null;
public $modalSiswa      = '';
public $modalStatus     = 'Belum Ditindak';
public $modalTanggal    = '';
public $modalJam        = '';
public $modalJamHour    = '';   // ← TAMBAH
public $modalJamMinute  = '';   // ← TAMBAH
public $modalCatatan    = '';

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
    public $showTrash = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'terbaru'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterJenis()
    {
        $this->resetPage();
    }
    public function updatingFilterTingkat()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterWaliKelas()
    {
        $this->resetPage();
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

    // ── HAPUS (SOFT DELETE) — hanya admin & guru_bk ─────────
    public function hapus($id): void
    {
        $this->cekAkses(['admin', 'guru_bk']);
        Pelanggaran::findOrFail($id)->delete();
        session()->flash('success', 'Data dipindahkan ke sampah.');
    }

    // ── RESTORE — hanya admin & guru_bk ─────────────────────
    public function restore($id): void
    {
        $this->cekAkses(['admin', 'guru_bk']);
        Pelanggaran::onlyTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Data berhasil dipulihkan.');
    }

    // ── FORCE DELETE — hanya admin & guru_bk ────────────────
    public function forceDelete($id): void
    {
        $this->cekAkses(['admin', 'guru_bk']);
        Pelanggaran::onlyTrashed()->findOrFail($id)->forceDelete();
        session()->flash('success', 'Data dihapus permanen.');
    }

    // ── EMPTY TRASH — hanya admin & guru_bk ─────────────────
    public function emptyTrash(): void
    {
        $this->cekAkses(['admin', 'guru_bk']);
        Pelanggaran::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    // ── BUKA MODAL STATUS ────────────────────────────────────
    public function bukaModalStatus($id): void
{
    $this->cekAkses(['guru_bk', 'wali_kelas']);

    $data = Pelanggaran::with('siswa')->findOrFail($id);

    $this->modalId      = $data->id_pelanggaran;
    $this->modalSiswa   = optional($data->siswa)->nama ?? '-';
    $this->modalStatus  = $data->status_pembinaan;
    $this->modalTanggal = $data->tanggal_pembinaan
        ? \Carbon\Carbon::parse($data->tanggal_pembinaan)->format('Y-m-d')
        : '';

    // Pecah jam ke hour & minute untuk dropdown
    $jamRaw = $data->getRawOriginal('jam_pembinaan');
    if ($jamRaw) {
        [$this->modalJamHour, $this->modalJamMinute] = explode(':', substr($jamRaw, 0, 5));
    } else {
        $this->modalJamHour   = '';
        $this->modalJamMinute = '';
    }

    $this->modalJam        = '';
    $this->modalCatatan    = $data->catatan_bk ?? '';
    $this->showModalStatus = true;
}

    // ── TUTUP MODAL ──────────────────────────────────────────
    public function tutupModalStatus(): void
{
    $this->showModalStatus = false;
    $this->modalId         = null;
    $this->modalSiswa      = '';
    $this->modalStatus     = 'Belum Ditindak';
    $this->modalTanggal    = '';
    $this->modalJam        = '';
    $this->modalJamHour    = '';   // ← TAMBAH
    $this->modalJamMinute  = '';   // ← TAMBAH
    $this->modalCatatan    = '';
    $this->resetErrorBag();
}

    // ── SIMPAN STATUS ────────────────────────────────────────
    public function simpanStatus(): void
{
    $this->cekAkses(['guru_bk', 'wali_kelas']);

    // Gabung dropdown hour + minute jadi "H:i"
    $this->modalJam = ($this->modalJamHour && $this->modalJamMinute)
        ? $this->modalJamHour . ':' . $this->modalJamMinute
        : null;

    $this->validate([
        'modalStatus'  => 'required|in:Belum Ditindak,Dalam Proses,Selesai',
        'modalTanggal' => 'nullable|date',
        'modalJam'     => 'nullable|date_format:H:i',
        'modalCatatan' => 'nullable|string|max:1000',
    ], [
        'modalStatus.required' => 'Status pembinaan wajib dipilih.',
        'modalStatus.in'       => 'Status tidak valid.',
        'modalTanggal.date'    => 'Format tanggal tidak valid.',
        'modalJam.date_format' => 'Format jam tidak valid.',
    ]);

    Pelanggaran::findOrFail($this->modalId)->update([
        'status_pembinaan'  => $this->modalStatus,
        'tanggal_pembinaan' => $this->modalTanggal ?: null,
        'jam_pembinaan'     => $this->modalJam ?: null,
        'catatan_bk'        => $this->modalCatatan ?: null,
    ]);

    session()->flash('success', 'Status pembinaan berhasil diperbarui.');
    $this->tutupModalStatus();
}

    // ── HELPER: cek akses role ───────────────────────────────
    private function cekAkses(array $roles): void
    {
        $role = optional(Auth::user()->role)->nama_role;
        if (! in_array($role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
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

        // Role-based scope
        if ($role === 'wali_kelas') {
            $query->where('id_walikelas', optional($user->waliKelas)->id_walikelas);
        } elseif ($role === 'orang_tua') {
            $query->whereHas('siswa', function ($q) use ($user) {
                $q->where('id_walimurid', optional($user->waliMurid)->id_walimurid);
            });
        }

        // Soft delete
        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        // Search
        if ($this->search) {
            $query->whereHas('siswa', function ($s) {
                $s->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        // Filter jenis
        if ($this->filterJenis) {
            $query->where('id_jenispelanggaran', $this->filterJenis);
        }

        // Filter tingkat
        if ($this->filterTingkat) {
            $query->whereHas('jenisPelanggaran', function ($q) {
                $q->where('tingkat_pelanggaran', $this->filterTingkat);
            });
        }

        // Filter status
        if ($this->filterStatus) {
            $query->where('status_pembinaan', $this->filterStatus);
        }

        // Filter wali kelas (hanya admin & guru_bk)
        if ($this->filterWaliKelas && in_array($role, ['admin', 'guru_bk'])) {
            $query->where('id_walikelas', $this->filterWaliKelas);
        }

        // Sort
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
            'waliKelasList' => WaliKelas::with('pengguna')->get(),
            'jenisList'     => JenisPelanggaran::orderBy('nama_pelanggaran')->get(),
            'trashCount'    => Pelanggaran::onlyTrashed()->count(),
            'role'          => $role,
        ]);
    }
}
