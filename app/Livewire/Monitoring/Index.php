<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search       = '';
    public $filterKelas  = '';
    public $filterStatus = '';
    public $perPage      = 10;

    public $showModal    = false;
    public $modalSiswa   = null;
    public $modalRiwayat = [];

    public function updatingSearch()       { $this->resetPage(); }
    public function updatingFilterKelas()  { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingPerPage()      { $this->resetPage(); }

    /**
     * Bangun query dasar siswa dengan kolom agregat (total_pelanggaran, jumlah_sp, sp_terakhir_raw)
     * dihitung via subquery SQL — TIDAK ada query tambahan per-siswa (no N+1).
     */
    private function baseQuery()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $query = Siswa::query()
            ->select('siswa.*')
            // total pelanggaran (tidak terhapus)
            ->selectSub(function ($q) {
                $q->from('pelanggaran')
                    ->whereColumn('pelanggaran.id_siswa', 'siswa.id_siswa')
                    ->whereNull('pelanggaran.deleted_at')
                    ->selectRaw('COUNT(*)');
            }, 'total_pelanggaran')
            // jumlah surat panggilan (SP) milik siswa ini
            ->selectSub(function ($q) {
                $q->from('surat_panggilan')
                    ->join('pelanggaran', 'pelanggaran.id_pelanggaran', '=', 'surat_panggilan.id_pelanggaran')
                    ->whereColumn('pelanggaran.id_siswa', 'siswa.id_siswa')
                    ->selectRaw('COUNT(*)');
            }, 'jumlah_sp')
            // tanggal SP paling baru
            ->selectSub(function ($q) {
                $q->from('surat_panggilan')
                    ->join('pelanggaran', 'pelanggaran.id_pelanggaran', '=', 'surat_panggilan.id_pelanggaran')
                    ->whereColumn('pelanggaran.id_siswa', 'siswa.id_siswa')
                    ->selectRaw('MAX(surat_panggilan.created_at)');
            }, 'sp_terakhir_raw')
            ->with('kelas')
            ->whereNull('siswa.deleted_at');

        if ($role === 'wali_kelas') {
            $idKelasAmpu = optional($user->waliKelas)->kelas?->id_kelas;
            $query->where('siswa.id_kelas', $idKelasAmpu);
        } elseif ($role === 'wali_siswa') {
            $idWaliSiswa = optional($user->waliSiswa)->id_walisiswa;
            $query->where('siswa.id_walisiswa', $idWaliSiswa);
        }

        if ($this->search && $role !== 'wali_siswa') {
            $query->where(function ($q) {
                $q->where('siswa.nama', 'like', '%' . $this->search . '%')
                    ->orWhere('siswa.nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterKelas && $role === 'guru_bk') {
            $query->where('siswa.id_kelas', $this->filterKelas);
        }

        return $query;
    }

    /**
     * Terjemahkan total_pelanggaran & jumlah_sp (hasil query) menjadi info status,
     * TANPA query tambahan — murni kalkulasi PHP dari angka yang sudah ada.
     */
    public static function statusFromCounts(int $totalPelanggaran, int $jumlahSp, ?string $spTerakhirRaw = null): array
    {
        $spTerakhir = $spTerakhirRaw ? Carbon::parse($spTerakhirRaw) : null;

        if ($totalPelanggaran === 0) {
            return [
                'status' => 'aman', 'label' => 'Baik', 'color' => 'green',
                'total_pelanggaran' => 0, 'jumlah_sp' => 0, 'sp_terakhir' => null,
            ];
        }

        if ($jumlahSp === 0) {
            return [
                'status' => 'baik', 'label' => 'Baik', 'color' => 'green',
                'total_pelanggaran' => $totalPelanggaran, 'jumlah_sp' => 0, 'sp_terakhir' => null,
            ];
        } elseif ($jumlahSp === 1) {
            return [
                'status' => 'perhatian', 'label' => 'Perlu Perhatian (SP 1)', 'color' => 'yellow',
                'total_pelanggaran' => $totalPelanggaran, 'jumlah_sp' => 1, 'sp_terakhir' => $spTerakhir,
            ];
        } elseif ($jumlahSp === 2) {
            return [
                'status' => 'berisiko', 'label' => 'Berisiko (SP 2)', 'color' => 'red',
                'total_pelanggaran' => $totalPelanggaran, 'jumlah_sp' => 2, 'sp_terakhir' => $spTerakhir,
            ];
        }

        return [
            'status' => 'kritis', 'label' => 'Kritis — Ambang DO (SP ' . $jumlahSp . ')', 'color' => 'red',
            'total_pelanggaran' => $totalPelanggaran, 'jumlah_sp' => $jumlahSp, 'sp_terakhir' => $spTerakhir,
        ];
    }

    /**
     * Tetap disediakan untuk kompatibilitas dengan kode lain (mis. modal detail)
     * yang masih memanggil hitungStatus($siswaModel). Ini boleh tetap query langsung
     * karena hanya dipanggil SEKALI per buka modal, bukan per-baris listing.
     */
    public static function hitungStatus(Siswa $siswa): array
    {
        $totalPelanggaran = $siswa->pelanggaran()->whereNull('deleted_at')->count();

        $jumlahSp = \App\Models\SuratPanggilan::whereHas(
            'pelanggaran',
            fn($q) => $q->where('id_siswa', $siswa->id_siswa)
        )->count();

        $spTerakhir = \App\Models\SuratPanggilan::whereHas(
            'pelanggaran',
            fn($q) => $q->where('id_siswa', $siswa->id_siswa)
        )->latest('created_at')->first();

        return self::statusFromCounts(
            $totalPelanggaran,
            $jumlahSp,
            $spTerakhir?->created_at
        );
    }

    public function lihatDetail($id): void
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $siswa = Siswa::with([
            'kelas',
            'pelanggaran' => fn($q) => $q->with(['jenisPelanggaran'])
                ->whereNull('deleted_at')
                ->latest('waktu_kejadian'),
        ])->findOrFail($id);

        if ($role === 'wali_kelas') {
            $idKelasAmpu = optional($user->waliKelas)->kelas?->id_kelas;
            abort_if($siswa->id_kelas !== $idKelasAmpu, 403);
        } elseif ($role === 'wali_siswa') {
            $idWaliSiswa = optional($user->waliSiswa)->id_walisiswa;
            abort_if($siswa->id_walisiswa !== $idWaliSiswa, 403);
        }

        $this->modalSiswa   = $siswa;
        $this->modalRiwayat = $siswa->pelanggaran;
        $this->showModal    = true;
    }

    public function tutupModal(): void
    {
        $this->showModal    = false;
        $this->modalSiswa   = null;
        $this->modalRiwayat = [];
    }

    public function render()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        // ── Ringkasan (cards atas) ──
        // Pakai query agregat yang sama, tapi hanya tarik kolom angka yang dibutuhkan,
        // tanpa relasi/with(), supaya ringan walau menyentuh banyak baris.
        $ringkasanRows = $this->baseQuery()
            ->reorder()
            ->get(['siswa.id_siswa', 'total_pelanggaran', 'jumlah_sp']);

        $ringkasan = [
            'total'     => $ringkasanRows->count(),
            'aman'      => $ringkasanRows->where('total_pelanggaran', 0)->count(),
            'baik'      => $ringkasanRows->where('total_pelanggaran', '>', 0)->where('jumlah_sp', 0)->count(),
            'perhatian' => $ringkasanRows->where('jumlah_sp', 1)->count(),
            'berisiko'  => $ringkasanRows->where('jumlah_sp', 2)->count(),
            'kritis'    => $ringkasanRows->where('jumlah_sp', '>=', 3)->count(),
        ];

        // ── Query utama untuk tabel (dengan filter status di level SQL) ──
        $query = $this->baseQuery();

        // FIX: havingRaw harus dipanggil langsung pada $query,
        // BUKAN di dalam closure where(), karena closure where()
        // hanya menangkap klausa WHERE, bukan HAVING. Sebelumnya
        // havingRaw di dalam where() diam-diam tidak pernah
        // tergabung ke query final, sehingga filter status tidak
        // berpengaruh sama sekali ke hasil tabel.
        if ($this->filterStatus) {
            match ($this->filterStatus) {
                'aman'      => $query->havingRaw('total_pelanggaran = 0'),
                'baik'      => $query->havingRaw('total_pelanggaran > 0 AND jumlah_sp = 0'),
                'perhatian' => $query->havingRaw('jumlah_sp = 1'),
                'berisiko'  => $query->havingRaw('jumlah_sp = 2'),
                'kritis'    => $query->havingRaw('jumlah_sp >= 3'),
                default     => null,
            };
        }

        $query->orderBy('siswa.nama');

        $pelangganPaginator = $query->paginate($this->perPage);

        // Map hasil paginasi jadi struktur ['siswa' => ..., 'status' => ..., dst]
        // TANPA query tambahan — semua angka sudah ada dari kolom hasil select sub.
        $siswaData = collect($pelangganPaginator->items())->map(function ($siswa) {
            $statusInfo = self::statusFromCounts(
                (int) $siswa->total_pelanggaran,
                (int) $siswa->jumlah_sp,
                $siswa->sp_terakhir_raw
            );
            return array_merge(['siswa' => $siswa], $statusInfo);
        });

        $kelasList = $role === 'guru_bk'
            ? \App\Models\Kelas::orderBy('nama_kelas')->get()
            : collect();

        return view('livewire.monitoring.index', [
            'siswaData'  => $siswaData,
            'total'      => $pelangganPaginator->total(),
            'paginator'  => $pelangganPaginator,
            'ringkasan'  => $ringkasan,
            'kelasList'  => $kelasList,
            'role'       => $role,
        ]);
    }
}