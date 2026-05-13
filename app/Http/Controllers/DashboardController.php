<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Pengguna;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\WaliMurid;
use App\Models\WaliKelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $stats  = [];
        $charts = [];

        // ════════════════════════════════════════════════════
        // ADMIN — statistik master data + kelengkapan data
        // ════════════════════════════════════════════════════
        if ($role === 'admin') {

            // ── Stats cards ──
            $stats = [
                'total_pengguna'  => Pengguna::count(),
                'total_siswa'     => Siswa::count(),
                'total_kelas'     => Kelas::count(),
                'total_walimurid' => WaliMurid::count(),
                'total_walikelas' => WaliKelas::count(),
            ];

            // ── Chart 1: Pertumbuhan pengguna per bulan (6 bulan terakhir) ──
            $bulanLabels = [];
            $bulanData   = [];
            for ($i = 5; $i >= 0; $i--) {
                $bulan         = Carbon::now()->subMonths($i);
                $bulanLabels[] = $bulan->translatedFormat('M Y');
                $bulanData[]   = Pengguna::whereYear('created_at', $bulan->year)
                    ->whereMonth('created_at', $bulan->month)
                    ->count();
            }

            // ── Chart 2: Distribusi siswa per kelas ──
            $kelasData = Kelas::withCount('siswa')
                ->orderByDesc('siswa_count')
                ->get();

            $charts = [
                'pengguna_bulanan' => [
                    'labels' => $bulanLabels,
                    'data'   => $bulanData,
                ],
                'siswa_per_kelas' => [
                    'labels' => $kelasData->map(fn ($k) =>
                        $k->nama_kelas . ($k->jurusan ? ' ' . $k->jurusan : '')
                    )->toArray(),
                    'data' => $kelasData->pluck('siswa_count')->toArray(),
                ],
            ];

            // ════════════════════════════════════════════════
            // STATUS KELENGKAPAN DATA
            // ════════════════════════════════════════════════
            $kelengkapan = [];

            // 1. Siswa belum punya wali murid
            $siswaTanpaWali = Siswa::whereNull('id_walimurid')
                ->orWhere('id_walimurid', 0)
                ->get(['id_siswa', 'nama', 'nis']);
            if ($siswaTanpaWali->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'warning',
                    'icon'    => '👨‍👩‍👧',
                    'judul'   => 'Siswa Belum Punya Wali Murid',
                    'jumlah'  => $siswaTanpaWali->count(),
                    'detail'  => $siswaTanpaWali->take(5)->map(fn ($s) =>
                        $s->nama . ($s->nis ? ' (NIS: ' . $s->nis . ')' : '')
                    )->toArray(),
                    'ada_lagi' => max(0, $siswaTanpaWali->count() - 5),
                    'link'    => route('siswa'),
                ];
            }

            // 2. Siswa belum masuk kelas
            $siswaTanpaKelas = Siswa::whereNull('id_kelas')
                ->orWhere('id_kelas', 0)
                ->get(['id_siswa', 'nama', 'nis']);
            if ($siswaTanpaKelas->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'warning',
                    'icon'    => '🏫',
                    'judul'   => 'Siswa Belum Masuk Kelas',
                    'jumlah'  => $siswaTanpaKelas->count(),
                    'detail'  => $siswaTanpaKelas->take(5)->map(fn ($s) =>
                        $s->nama . ($s->nis ? ' (NIS: ' . $s->nis . ')' : '')
                    )->toArray(),
                    'ada_lagi' => max(0, $siswaTanpaKelas->count() - 5),
                    'link'    => route('siswa'),
                ];
            }

            // 3. Akun orang tua belum terhubung ke data wali murid
            $idSudahAdaWaliMurid = WaliMurid::pluck('id_pengguna')->toArray();
            $orangTuaBelumTerhubung = Pengguna::whereHas('role', fn ($q) =>
                    $q->where('nama_role', 'orang_tua')
                )
                ->whereNotIn('id_pengguna', $idSudahAdaWaliMurid)
                ->get(['id_pengguna', 'name', 'username']);
            if ($orangTuaBelumTerhubung->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'danger',
                    'icon'    => '🔗',
                    'judul'   => 'Akun Orang Tua Belum Terhubung',
                    'jumlah'  => $orangTuaBelumTerhubung->count(),
                    'detail'  => $orangTuaBelumTerhubung->take(5)->map(fn ($p) =>
                        $p->name . ' (@' . $p->username . ')'
                    )->toArray(),
                    'ada_lagi' => max(0, $orangTuaBelumTerhubung->count() - 5),
                    'link'    => route('wali-murid'),
                ];
            }

            // 4. Nomor telepon kosong (semua role)
            $tanpaNoTelpon = Pengguna::whereNull('no_telpon')
                ->orWhere('no_telpon', '')
                ->with('role')
                ->get(['id_pengguna', 'name', 'username', 'id_role']);
            if ($tanpaNoTelpon->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'info',
                    'icon'    => '📱',
                    'judul'   => 'Nomor Telepon Kosong',
                    'jumlah'  => $tanpaNoTelpon->count(),
                    'detail'  => $tanpaNoTelpon->take(5)->map(fn ($p) =>
                        $p->name . ' (' . (optional($p->role)->nama_role ?? '-') . ')'
                    )->toArray(),
                    'ada_lagi' => max(0, $tanpaNoTelpon->count() - 5),
                    'link'    => route('users'),
                ];
            }

            // 5. Data duplikat siswa (nama + id_kelas sama)
            $duplikat = Siswa::select('nama', 'id_kelas', DB::raw('COUNT(*) as total'))
                ->groupBy('nama', 'id_kelas')
                ->having('total', '>', 1)
                ->with('kelas')
                ->get();
            if ($duplikat->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'danger',
                    'icon'    => '⚠️',
                    'judul'   => 'Potensi Data Duplikat Siswa',
                    'jumlah'  => $duplikat->count(),
                    'detail'  => $duplikat->take(5)->map(fn ($d) =>
                        $d->nama . ' — ' . (optional($d->kelas)->nama_kelas ?? 'Tanpa Kelas') .
                        ' (' . $d->total . 'x)'
                    )->toArray(),
                    'ada_lagi' => max(0, $duplikat->count() - 5),
                    'link'    => route('siswa'),
                ];
            }

            // 6. Wali kelas belum punya kelas yang diampu
            $idWaliKelasPunyaKelas = Kelas::whereNotNull('id_walikelas')
                ->pluck('id_walikelas')
                ->toArray();
            $waliKelasTanpaKelas = WaliKelas::whereNotIn('id_walikelas', $idWaliKelasPunyaKelas)
                ->with('pengguna')
                ->get();
            if ($waliKelasTanpaKelas->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'warning',
                    'icon'    => '👨‍🏫',
                    'judul'   => 'Wali Kelas Belum Mengampu Kelas',
                    'jumlah'  => $waliKelasTanpaKelas->count(),
                    'detail'  => $waliKelasTanpaKelas->take(5)->map(fn ($wk) =>
                        optional($wk->pengguna)->name ?? 'Tidak diketahui'
                    )->toArray(),
                    'ada_lagi' => max(0, $waliKelasTanpaKelas->count() - 5),
                    'link'    => route('wali-kelas'),
                ];
            }

            // 7. NUPTK kosong di wali kelas
            $waliKelasTanpaNuptk = WaliKelas::whereNull('nuptk')
                ->orWhere('nuptk', '')
                ->with('pengguna')
                ->get();
            if ($waliKelasTanpaNuptk->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'    => 'info',
                    'icon'    => '🪪',
                    'judul'   => 'NUPTK Wali Kelas Kosong',
                    'jumlah'  => $waliKelasTanpaNuptk->count(),
                    'detail'  => $waliKelasTanpaNuptk->take(5)->map(fn ($wk) =>
                        optional($wk->pengguna)->name ?? 'Tidak diketahui'
                    )->toArray(),
                    'ada_lagi' => max(0, $waliKelasTanpaNuptk->count() - 5),
                    'link'    => route('wali-kelas'),
                ];
            }

            return view('dashboard', compact('stats', 'charts', 'role', 'kelengkapan'));
        }

        // ════════════════════════════════════════════════════
        // BASE QUERY — filter sesuai role
        // ════════════════════════════════════════════════════

        if ($role === 'wali_kelas') {
            $idWaliKelas = optional($user->waliKelas)->id_walikelas;
            $baseQuery   = fn () => Pelanggaran::where('id_walikelas', $idWaliKelas);
            $kelas       = Kelas::where('id_walikelas', $idWaliKelas)->first();
            $jumlahSiswa = $kelas ? Siswa::where('id_kelas', $kelas->id_kelas)->count() : 0;

        } elseif ($role === 'orang_tua') {
            $idWaliMurid = optional($user->waliMurid)->id_walimurid;
            $baseQuery   = fn () => Pelanggaran::whereHas('siswa', fn ($q) =>
                $q->where('id_walimurid', $idWaliMurid)
            );

        } else {
            // guru_bk
            $baseQuery   = fn () => Pelanggaran::query();
            $jumlahSiswa = Siswa::count();
        }

        // ── Stats ──
        $totalPelanggaran = $baseQuery()->count();
        $sudahDitindak    = $baseQuery()->where('status_pembinaan', 'sudah ditindak')->count();
        $belumDitindak    = $baseQuery()->where('status_pembinaan', 'belum ditindak')->count();

        if ($role === 'orang_tua') {
            $stats = [
                'total_pelanggaran' => $totalPelanggaran,
                'sudah_ditindak'    => $sudahDitindak,
                'belum_ditindak'    => $belumDitindak,
            ];
        } else {
            $stats = [
                'jumlah_siswa'      => $jumlahSiswa,
                'total_pelanggaran' => $totalPelanggaran,
                'sudah_ditindak'    => $sudahDitindak,
                'belum_ditindak'    => $belumDitindak,
            ];
        }

        // ── Chart 1: Tren pelanggaran per bulan ──
        $bulanLabels = [];
        $bulanData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan         = Carbon::now()->subMonths($i);
            $bulanLabels[] = $bulan->translatedFormat('M Y');
            $bulanData[]   = $baseQuery()
                ->whereYear('waktu_kejadian', $bulan->year)
                ->whereMonth('waktu_kejadian', $bulan->month)
                ->count();
        }
        $charts['bulanan'] = ['labels' => $bulanLabels, 'data' => $bulanData];

        // ── Chart 2: Jenis pelanggaran terbanyak ──
        $jenisPelanggaran = $baseQuery()
            ->select('id_jenispelanggaran', DB::raw('count(*) as total'))
            ->groupBy('id_jenispelanggaran')
            ->with('jenisPelanggaran')
            ->orderByDesc('total')
            ->get();

        $charts['jenis'] = [
            'labels' => $jenisPelanggaran->map(fn ($p) =>
                optional($p->jenisPelanggaran)->nama_pelanggaran ?? 'Tidak diketahui'
            )->toArray(),
            'data' => $jenisPelanggaran->pluck('total')->toArray(),
        ];

        $kelengkapan = [];
        return view('dashboard', compact('stats', 'charts', 'role', 'kelengkapan'));
    }
}