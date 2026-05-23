<?php

namespace App\Livewire\Monitoring;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;
use App\Models\Pelanggaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Filter
    public $search       = '';
    public $filterKelas  = '';
    public $filterStatus = '';
    public $perPage      = 10;

    // Modal Detail
    public $showModal    = false;
    public $modalSiswa   = null;
    public $modalRiwayat = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterKelas()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public static function hitungStatus(Siswa $siswa): array
    {
        $semua = $siswa->pelanggaran()
            ->with('jenisPelanggaran')
            ->whereNull('deleted_at')
            ->get();

        $pernahDibina = $semua->where('status_pembinaan', 'Selesai')
            ->whereNotNull('tanggal_pembinaan')
            ->isNotEmpty();

        if (! $pernahDibina) {
            return ['status' => null, 'label' => '-', 'color' => 'gray'];
        }

        $belumSelesai = $semua->whereIn('status_pembinaan', ['Belum Ditindak', 'Dalam Proses']);
        $jumlah       = $belumSelesai->count();
        $adaBerat     = $belumSelesai->contains(
            fn($p) => strtolower($p->jenisPelanggaran?->tingkat_pelanggaran ?? '') === 'berat'
        );

        // ── Gabung tanggal + jam dengan benar ──
        $pembinaan = $semua->where('status_pembinaan', 'Selesai')
            ->whereNotNull('tanggal_pembinaan')
            ->sortByDesc('tanggal_pembinaan')
            ->first();

        $tglPembinaan = null;

        if ($pembinaan) {
            $tanggal      = $pembinaan->tanggal_pembinaan->format('Y-m-d');
            $jam          = $pembinaan->getRawOriginal('jam_pembinaan') ?? '00:00:00';
            $tglPembinaan = Carbon::parse($tanggal . ' ' . $jam);
        }
        // ───────────────────────────────────────

        if ($jumlah === 0) {
            return [
                'status'        => 'baik',
                'label'         => 'Baik',
                'color'         => 'green',
                'jumlah_baru'   => 0,
                'tgl_pembinaan' => $tglPembinaan,
            ];
        } elseif ($adaBerat || $jumlah >= 3) {
            return [
                'status'        => 'berisiko',
                'label'         => 'Berisiko',
                'color'         => 'red',
                'jumlah_baru'   => $jumlah,
                'tgl_pembinaan' => $tglPembinaan,
            ];
        } else {
            return [
                'status'        => 'perhatian',
                'label'         => 'Perlu Perhatian',
                'color'         => 'yellow',
                'jumlah_baru'   => $jumlah,
                'tgl_pembinaan' => $tglPembinaan,
            ];
        }
    }

    public function lihatDetail($id): void
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $siswa = Siswa::with([
            'kelas',
            'pelanggaran' => fn($q) => $q->with('jenisPelanggaran')
                ->whereNull('deleted_at')
                ->latest('waktu_kejadian'),
        ])->findOrFail($id);

        // Validasi akses per role sebelum tampilkan detail
        if ($role === 'wali_kelas') {
            $idKelasAmpu = optional($user->waliKelas)->kelas?->id_kelas;
            abort_if($siswa->id_kelas !== $idKelasAmpu, 403);
        } elseif ($role === 'orang_tua') {
            $idWaliMurid = optional($user->waliMurid)->id_walimurid;
            abort_if($siswa->id_walimurid !== $idWaliMurid, 403);
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

        $query = Siswa::with([
            'kelas',
            'pelanggaran.jenisPelanggaran',
        ])
            ->whereHas(
                'pelanggaran',
                fn($q) =>
                $q->where('status_pembinaan', 'Selesai')
                    ->whereNotNull('tanggal_pembinaan')
            )
            ->whereNull('deleted_at');

        // ── Scope berdasarkan role ──
        if ($role === 'wali_kelas') {
            // Hanya siswa di kelas yang diampu wali kelas ini
            $idKelasAmpu = optional($user->waliKelas)->kelas?->id_kelas;
            $query->where('id_kelas', $idKelasAmpu);
        } elseif ($role === 'orang_tua') {
            // Hanya anak sendiri
            $idWaliMurid = optional($user->waliMurid)->id_walimurid;
            $query->where('id_walimurid', $idWaliMurid);
        }
        // guru_bk → tidak ada filter tambahan, semua siswa

        // Search (tidak tersedia untuk orang_tua karena hanya lihat 1 anak)
        if ($this->search && $role !== 'orang_tua') {
            $query->where(
                fn($q) =>
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%')
            );
        }

        // Filter kelas (hanya guru_bk yang bisa filter lintas kelas)
        if ($this->filterKelas && $role === 'guru_bk') {
            $query->where('id_kelas', $this->filterKelas);
        }

        $siswaList = $query->get();

        // Hitung status disiplin tiap siswa
        $siswaData = $siswaList->map(function ($siswa) {
            $statusInfo = self::hitungStatus($siswa);
            return array_merge(['siswa' => $siswa], $statusInfo);
        });

        // Filter status disiplin
        if ($this->filterStatus) {
            $siswaData = $siswaData->filter(
                fn($d) => ($d['status'] ?? '') === $this->filterStatus
            );
        }

        // Ringkasan — guru_bk dan wali_kelas tampilkan semua card
        // orang_tua hanya tampilkan status anak sendiri
        $ringkasan = [
            'total'     => $siswaData->count(),
            'baik'      => $siswaData->where('status', 'baik')->count(),
            'perhatian' => $siswaData->where('status', 'perhatian')->count(),
            'berisiko'  => $siswaData->where('status', 'berisiko')->count(),
        ];

        // Paginasi manual
        $page   = $this->getPage();
        $offset = ($page - 1) * $this->perPage;
        $paged  = $siswaData->values()->slice($offset, $this->perPage);

        // Kelas untuk filter dropdown — hanya relevan untuk guru_bk
        $kelasList = $role === 'guru_bk'
            ? \App\Models\Kelas::orderBy('nama_kelas')->get()
            : collect();

        return view('livewire.monitoring.index', [
            'siswaData'  => $paged,
            'total'      => $siswaData->count(),
            'ringkasan'  => $ringkasan,
            'kelasList'  => $kelasList,
            'role'       => $role,
        ]);
    }
}
