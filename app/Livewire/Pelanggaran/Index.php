<?php

namespace App\Livewire\Pelanggaran;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\JenisPelanggaran;
use App\Models\LogAktivitas;
use App\Services\EarlyWarningService;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // ── MODAL UPDATE STATUS ──────────────────────────────────────────────
    public bool   $showModalStatus = false;
    public        $modalId         = null;
    public string $modalSiswa      = '';
    public string $modalStatus     = 'Belum Ditindak';
    public string $modalTanggal    = '';
    public string $modalJamHour    = '';
    public string $modalJamMinute  = '';
    public string $modalCatatan    = '';

    // ── FILTER ──────────────────────────────────────────────────────────
    public $search          = '';
    public $filterJenis     = '';
    public $filterTingkat   = '';
    public $filterStatus    = '';
    public $filterWaliKelas = '';

    // ── SORT & PAGINATION ────────────────────────────────────────────────
    public $sortBy  = 'terbaru';
    public $perPage = 10;

    // ── MODE ─────────────────────────────────────────────────────────────
    public $showTrash = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'terbaru'],
    ];

    public function updatingSearch()          { $this->resetPage(); }
    public function updatingFilterJenis()     { $this->resetPage(); }
    public function updatingFilterTingkat()   { $this->resetPage(); }
    public function updatingFilterStatus()    { $this->resetPage(); }
    public function updatingFilterWaliKelas() { $this->resetPage(); }
    public function updatingSortBy()          { $this->resetPage(); }
    public function updatingPerPage()         { $this->resetPage(); }
    public function updatingShowTrash()       { $this->resetPage(); }

    // ── HAPUS (SOFT DELETE) ──────────────────────────────────────────────
    public function hapus($id): void
    {
        $this->cekAkses(['admin', 'guru_bk']);

        $pelanggaran = Pelanggaran::with(['siswa', 'jenisPelanggaran'])->findOrFail($id);

        LogAktivitas::catat(
            aksi: 'hapus_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Memindahkan pelanggaran siswa ' . $pelanggaran->siswa->nama
                . ' - ' . $pelanggaran->jenisPelanggaran->nama_pelanggaran . ' ke tong sampah',
            idReferensi: $id
        );

        $pelanggaran->delete();
        session()->flash('success', 'Data dipindahkan ke sampah.');
    }

    // ── RESTORE ──────────────────────────────────────────────────────────
    public function restore($id): void
    {
        $this->cekAkses(['admin', 'guru_bk']);

        $pelanggaran = Pelanggaran::onlyTrashed()->with(['siswa', 'jenisPelanggaran'])->findOrFail($id);

        LogAktivitas::catat(
            aksi: 'restore_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Memulihkan pelanggaran siswa ' . $pelanggaran->siswa->nama
                . ' - ' . $pelanggaran->jenisPelanggaran->nama_pelanggaran . ' dari tong sampah',
            idReferensi: $id
        );

        $pelanggaran->restore();
        session()->flash('success', 'Data berhasil dipulihkan.');
    }

    // ── FORCE DELETE ─────────────────────────────────────────────────────
    public function forceDelete($id): void
    {
        $this->cekAkses(['admin', 'guru_bk']);

        $pelanggaran = Pelanggaran::onlyTrashed()->with(['siswa', 'jenisPelanggaran'])->findOrFail($id);

        LogAktivitas::catat(
            aksi: 'hapus_permanen_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Menghapus permanen pelanggaran siswa ' . $pelanggaran->siswa->nama
                . ' - ' . $pelanggaran->jenisPelanggaran->nama_pelanggaran,
            idReferensi: $id
        );

        $pelanggaran->forceDelete();
        session()->flash('success', 'Data dihapus permanen.');
    }

    // ── EMPTY TRASH ──────────────────────────────────────────────────────
    public function emptyTrash(): void
    {
        $this->cekAkses(['admin', 'guru_bk']);

        LogAktivitas::catat(
            aksi: 'kosongkan_sampah_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Mengosongkan seluruh tong sampah pelanggaran',
        );

        Pelanggaran::onlyTrashed()->forceDelete();
        session()->flash('success', 'Tong sampah dikosongkan.');
    }

    // ── BUKA MODAL STATUS ────────────────────────────────────────────────
    public function bukaModalStatus($id): void
    {
        $this->cekAkses(['guru_bk', 'wali_kelas']);

        $data = Pelanggaran::with('siswa')->findOrFail($id);

        $this->modalId     = $data->id_pelanggaran;
        $this->modalSiswa  = optional($data->siswa)->nama ?? '-';
        $this->modalStatus = $data->status_pembinaan ?? 'Belum Ditindak';

        // Tanggal
        $this->modalTanggal = $data->tanggal_pembinaan
            ? \Carbon\Carbon::parse($data->tanggal_pembinaan)->format('Y-m-d')
            : '';

        // Jam — baca dari kolom jam_pembinaan (format H:i atau H:i:s)
        $jamRaw = $data->getRawOriginal('jam_pembinaan');
        if ($jamRaw) {
            [$this->modalJamHour, $this->modalJamMinute] = explode(':', substr($jamRaw, 0, 5));
        } else {
            $this->modalJamHour   = '';
            $this->modalJamMinute = '';
        }

        $this->modalCatatan    = $data->catatan_bk ?? '';
        $this->showModalStatus = true;
        $this->resetErrorBag();
    }

    // ── TUTUP MODAL ──────────────────────────────────────────────────────
    public function tutupModalStatus(): void
    {
        $this->showModalStatus = false;
        $this->modalId         = null;
        $this->modalSiswa      = '';
        $this->modalStatus     = 'Belum Ditindak';
        $this->modalTanggal    = '';
        $this->modalJamHour    = '';
        $this->modalJamMinute  = '';
        $this->modalCatatan    = '';
        $this->resetErrorBag();
    }

    // ── SIMPAN STATUS ────────────────────────────────────────────────────
    public function simpanStatus(): void
    {
        $this->cekAkses(['guru_bk', 'wali_kelas']);

        // ── Validasi dasar (selalu dijalankan) ─────────────────────────
        $rules    = ['modalStatus' => 'required|in:Belum Ditindak,Dalam Proses,Selesai'];
        $messages = [
            'modalStatus.required' => 'Status pembinaan wajib dipilih.',
            'modalStatus.in'       => 'Status pembinaan tidak valid.',
        ];

        // ── Validasi kondisional: Dalam Proses ─────────────────────────
        // Tanggal wajib, jam & catatan opsional
        if ($this->modalStatus === 'Dalam Proses') {
            $rules['modalTanggal']   = 'required|date';
            $rules['modalJamHour']   = 'nullable';
            $rules['modalJamMinute'] = 'nullable';
            $rules['modalCatatan']   = 'nullable|string|max:1000';

            $messages['modalTanggal.required'] = 'Tanggal pembinaan wajib diisi saat status Dalam Proses.';
            $messages['modalTanggal.date']     = 'Format tanggal tidak valid.';
            $messages['modalCatatan.max']      = 'Catatan maksimal 1000 karakter.';
        }

        // ── Validasi kondisional: Selesai ──────────────────────────────
        // Tanggal, jam, dan catatan semuanya wajib
        if ($this->modalStatus === 'Selesai') {
            $rules['modalTanggal']   = 'required|date';
            $rules['modalJamHour']   = 'required';
            $rules['modalJamMinute'] = 'required';
            $rules['modalCatatan']   = 'required|string|min:10|max:1000';

            $messages['modalTanggal.required']   = 'Tanggal pembinaan wajib diisi untuk status Selesai.';
            $messages['modalTanggal.date']       = 'Format tanggal tidak valid.';
            $messages['modalJamHour.required']   = 'Jam pembinaan wajib dipilih untuk status Selesai.';
            $messages['modalJamMinute.required'] = 'Menit pembinaan wajib dipilih untuk status Selesai.';
            $messages['modalCatatan.required']   = 'Catatan BK wajib diisi untuk status Selesai.';
            $messages['modalCatatan.min']        = 'Catatan terlalu singkat, minimal 10 karakter.';
            $messages['modalCatatan.max']        = 'Catatan maksimal 1000 karakter.';
        }

        $this->validate($rules, $messages);

        // ── Gabungkan jam dari hour + minute ───────────────────────────
        $jamFinal = null;
        if ($this->modalJamHour !== '' && $this->modalJamMinute !== '') {
            $jamFinal = $this->modalJamHour . ':' . $this->modalJamMinute;
        }

        // ── Simpan ke database ─────────────────────────────────────────
        $pelanggaran = Pelanggaran::with('siswa')->findOrFail($this->modalId);

        $pelanggaran->update([
            'status_pembinaan'  => $this->modalStatus,
            'tanggal_pembinaan' => $this->modalTanggal ?: null,
            'jam_pembinaan'     => $jamFinal,
            'catatan_bk'        => $this->modalCatatan ?: null,
        ]);

        // ── Log aktivitas ──────────────────────────────────────────────
        LogAktivitas::catat(
            aksi: 'update_status_pembinaan',
            modul: 'pelanggaran',
            keterangan: 'Mengubah status pembinaan siswa ' . $pelanggaran->siswa->nama
                . ' menjadi "' . $this->modalStatus . '"'
                . ($this->modalCatatan ? ' | Catatan: ' . $this->modalCatatan : ''),
            idReferensi: $this->modalId
        );

        session()->flash('success', 'Status pembinaan berhasil diperbarui.');
        $this->tutupModalStatus();
    }

    // ── HELPER: cek akses role ────────────────────────────────────────────
    private function cekAkses(array $roles): void
    {
        $role = optional(Auth::user()->role)->nama_role;
        if (! in_array($role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }

    // ── RENDER ───────────────────────────────────────────────────────────
    public function render()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $query = Pelanggaran::with([
            'siswa.kelas',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        // Scope berdasarkan role
        if ($role === 'wali_kelas') {
            $query->where('id_walikelas', optional($user->waliKelas)->id_walikelas);
        } elseif ($role === 'orang_tua') {
            $query->whereHas('siswa', function ($q) use ($user) {
                $q->where('id_walimurid', optional($user->waliMurid)->id_walimurid);
            });
        }

        // Mode trash
        if ($this->showTrash) {
            $query->onlyTrashed();
        }

        // Filter pencarian
        if ($this->search) {
            $query->whereHas('siswa', function ($s) {
                $s->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nis',  'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterJenis) {
            $query->where('id_jenispelanggaran', $this->filterJenis);
        }

        if ($this->filterTingkat) {
            $query->whereHas('jenisPelanggaran', function ($q) {
                $q->where('tingkat_pelanggaran', $this->filterTingkat);
            });
        }

        if ($this->filterStatus) {
            $query->where('status_pembinaan', $this->filterStatus);
        }

        if ($this->filterWaliKelas && in_array($role, ['admin', 'guru_bk'])) {
            $query->where('id_walikelas', $this->filterWaliKelas);
        }

        // Sorting
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