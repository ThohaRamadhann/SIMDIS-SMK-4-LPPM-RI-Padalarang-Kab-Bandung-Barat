<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Pengguna;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\WaliSiswa;
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

        // ── ADMIN ──
        if ($role === 'admin') {

            $stats = [
                'total_pengguna'  => Pengguna::count(),
                'total_siswa'     => Siswa::count(),
                'total_kelas'     => Kelas::count(),
                'total_waliswa'   => WaliSiswa::count(),   // sebelumnya: total_walimurid
                'total_walikelas' => WaliKelas::count(),
            ];

            $bulanLabels = [];
            $bulanData   = [];
            for ($i = 5; $i >= 0; $i--) {
                $bulan         = Carbon::now()->subMonths($i);
                $bulanLabels[] = $bulan->translatedFormat('M Y');
                $bulanData[]   = Pengguna::whereYear('created_at', $bulan->year)
                    ->whereMonth('created_at', $bulan->month)
                    ->count();
            }

            $kelasData = Kelas::withCount('siswa')->orderByDesc('siswa_count')->get();

            $siswaPerTingkat = [
                'X'   => Siswa::whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'X %')->where('nama_kelas', 'not like', 'XI %')->where('nama_kelas', 'not like', 'XII %'))->count(),
                'XI'  => Siswa::whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XI %')->where('nama_kelas', 'not like', 'XII %'))->count(),
                'XII' => Siswa::whereHas('kelas', fn($q) => $q->where('nama_kelas', 'like', 'XII %'))->count(),
            ];

            $charts = [
                'pengguna_bulanan' => [
                    'labels' => $bulanLabels,
                    'data'   => $bulanData,
                ],
                'siswa_per_kelas' => [
                    'labels' => $kelasData->map(
                        fn($k) => $k->nama_kelas . ($k->jurusan ? ' ' . $k->jurusan : '')
                    )->toArray(),
                    'data' => $kelasData->pluck('siswa_count')->toArray(),
                ],
                'siswa_per_tingkat' => [
                    'labels' => array_keys($siswaPerTingkat),
                    'data'   => array_values($siswaPerTingkat),
                ],
            ];

            $kelengkapan = [];

            // Siswa tanpa wali siswa
            $siswaTanpaWali = Siswa::whereNull('id_walisiswa')
                ->orWhere('id_walisiswa', 0)
                ->get(['id_siswa', 'nama', 'nis']);

            if ($siswaTanpaWali->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'warning',
                    'icon'     => '👨‍👩‍👧',
                    'judul'    => 'Siswa Belum Punya Wali Siswa',
                    'jumlah'   => $siswaTanpaWali->count(),
                    'detail'   => $siswaTanpaWali->take(5)
                        ->map(fn($s) => $s->nama . ($s->nis ? ' (NIS: ' . $s->nis . ')' : ''))
                        ->toArray(),
                    'ada_lagi' => max(0, $siswaTanpaWali->count() - 5),
                    'link'     => route('siswa'),
                ];
            }

            // Siswa tanpa kelas
            $siswaTanpaKelas = Siswa::whereNull('id_kelas')->orWhere('id_kelas', 0)->get(['id_siswa', 'nama', 'nis']);
            if ($siswaTanpaKelas->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'warning',
                    'icon'     => '🏫',
                    'judul'    => 'Siswa Belum Masuk Kelas',
                    'jumlah'   => $siswaTanpaKelas->count(),
                    'detail'   => $siswaTanpaKelas->take(5)->map(fn($s) => $s->nama . ($s->nis ? ' (NIS: ' . $s->nis . ')' : ''))->toArray(),
                    'ada_lagi' => max(0, $siswaTanpaKelas->count() - 5),
                    'link'     => route('siswa'),
                ];
            }

            // Akun wali_siswa belum terhubung ke data wali siswa
            $idSudahAdaWaliSiswa    = WaliSiswa::pluck('id_pengguna')->toArray();
            $waliSiswaBelumTerhubung = Pengguna::whereHas('role', fn($q) => $q->where('nama_role', 'wali_siswa'))
                ->whereNotIn('id_pengguna', $idSudahAdaWaliSiswa)
                ->get(['id_pengguna', 'name', 'username']);
            if ($waliSiswaBelumTerhubung->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'danger',
                    'icon'     => '🔗',
                    'judul'    => 'Akun Wali Siswa Belum Terhubung',
                    'jumlah'   => $waliSiswaBelumTerhubung->count(),
                    'detail'   => $waliSiswaBelumTerhubung->take(5)->map(fn($p) => $p->name . ' (@' . $p->username . ')')->toArray(),
                    'ada_lagi' => max(0, $waliSiswaBelumTerhubung->count() - 5),
                    'link'     => route('wali-siswa'),
                ];
            }

            // Nomor telepon kosong
            $tanpaNoTelpon = Pengguna::whereNull('no_telpon')->orWhere('no_telpon', '')->with('role')->get(['id_pengguna', 'name', 'username', 'id_role']);
            if ($tanpaNoTelpon->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'info',
                    'icon'     => '📱',
                    'judul'    => 'Nomor Telepon Kosong',
                    'jumlah'   => $tanpaNoTelpon->count(),
                    'detail'   => $tanpaNoTelpon->take(5)->map(fn($p) => $p->name . ' (' . (optional($p->role)->nama_role ?? '-') . ')')->toArray(),
                    'ada_lagi' => max(0, $tanpaNoTelpon->count() - 5),
                    'link'     => route('users'),
                ];
            }

            // Duplikat siswa
            $duplikat = Siswa::select('nama', 'id_kelas', DB::raw('COUNT(*) as total'))
                ->groupBy('nama', 'id_kelas')->having('total', '>', 1)->with('kelas')->get();
            if ($duplikat->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'danger',
                    'icon'     => '⚠️',
                    'judul'    => 'Potensi Data Duplikat Siswa',
                    'jumlah'   => $duplikat->count(),
                    'detail'   => $duplikat->take(5)->map(fn($d) => $d->nama . ' — ' . (optional($d->kelas)->nama_kelas ?? 'Tanpa Kelas') . ' (' . $d->total . 'x)')->toArray(),
                    'ada_lagi' => max(0, $duplikat->count() - 5),
                    'link'     => route('siswa'),
                ];
            }

            // Wali kelas belum mengampu kelas
            $idWaliKelasPunyaKelas = Kelas::whereNotNull('id_walikelas')->pluck('id_walikelas')->toArray();
            $waliKelasTanpaKelas   = WaliKelas::whereNotIn('id_walikelas', $idWaliKelasPunyaKelas)->with('pengguna')->get();
            if ($waliKelasTanpaKelas->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'warning',
                    'icon'     => '👨‍🏫',
                    'judul'    => 'Wali Kelas Belum Mengampu Kelas',
                    'jumlah'   => $waliKelasTanpaKelas->count(),
                    'detail'   => $waliKelasTanpaKelas->take(5)->map(fn($wk) => optional($wk->pengguna)->name ?? 'Tidak diketahui')->toArray(),
                    'ada_lagi' => max(0, $waliKelasTanpaKelas->count() - 5),
                    'link'     => route('wali-kelas'),
                ];
            }

            // NUPTK wali kelas kosong
            $waliKelasTanpaNuptk = WaliKelas::whereNull('nuptk')->orWhere('nuptk', '')->with('pengguna')->get();
            if ($waliKelasTanpaNuptk->isNotEmpty()) {
                $kelengkapan[] = [
                    'tipe'     => 'info',
                    'icon'     => '🪪',
                    'judul'    => 'NUPTK Wali Kelas Kosong',
                    'jumlah'   => $waliKelasTanpaNuptk->count(),
                    'detail'   => $waliKelasTanpaNuptk->take(5)->map(fn($wk) => optional($wk->pengguna)->name ?? 'Tidak diketahui')->toArray(),
                    'ada_lagi' => max(0, $waliKelasTanpaNuptk->count() - 5),
                    'link'     => route('wali-kelas'),
                ];
            }

            return view('dashboard.admin', compact('stats', 'charts', 'role', 'kelengkapan'));
        }

        // ── BASE QUERY per role ──
        if ($role === 'wali_kelas') {
            $idWaliKelas = optional($user->waliKelas)->id_walikelas;
            $baseQuery   = fn() => Pelanggaran::where('id_walikelas', $idWaliKelas);
            $kelas       = Kelas::where('id_walikelas', $idWaliKelas)->first();
            $jumlahSiswa = $kelas ? Siswa::where('id_kelas', $kelas->id_kelas)->count() : 0;
        } elseif ($role === 'wali_siswa') {

            $idWaliSiswa = optional($user->waliSiswa)->id_walisiswa;
        
            $baseQuery = fn() => Pelanggaran::whereHas(
                'siswa',
                fn($q) => $q->where('id_walisiswa', $idWaliSiswa)
            );
        
        } else {
            $baseQuery   = fn() => Pelanggaran::query();
            $jumlahSiswa = Siswa::count();
        }

        $totalPelanggaran = $baseQuery()->count();
        $sudahDitindak    = $baseQuery()->where('status_pembinaan', 'Selesai')->count();
        $belumDitindak    = $baseQuery()->where('status_pembinaan', 'Belum Ditindak')->count();

        if ($role === 'wali_siswa') {
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

        $bulanLabels = [];
        $bulanData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan         = Carbon::now()->subMonths($i);
            $bulanLabels[] = $bulan->translatedFormat('M Y');
            $bulanData[]   = $baseQuery()->whereYear('waktu_kejadian', $bulan->year)->whereMonth('waktu_kejadian', $bulan->month)->count();
        }
        $charts['bulanan'] = ['labels' => $bulanLabels, 'data' => $bulanData];

        $jenisPelanggaran = $baseQuery()->select('id_jenispelanggaran', DB::raw('count(*) as total'))
            ->groupBy('id_jenispelanggaran')->with('jenisPelanggaran')->orderByDesc('total')->get();
        $charts['jenis'] = [
            'labels'  => $jenisPelanggaran->map(fn($p) => optional($p->jenisPelanggaran)->nama_pelanggaran ?? 'Tidak diketahui')->toArray(),
            'data'    => $jenisPelanggaran->pluck('total')->toArray(),
            'tingkat' => $jenisPelanggaran->map(fn($p) => optional($p->jenisPelanggaran)->tingkat_pelanggaran ?? 'Ringan')->toArray(),
        ];

        $kelengkapan = [];

        $view = match ($role) {
            'wali_siswa' => 'dashboard.wali-siswa',   // sebelumnya: orang-tua
            'wali_kelas' => 'dashboard.wali-kelas',
            default      => 'dashboard.guru-bk',
        };

        return view($view, compact('stats', 'charts', 'role', 'kelengkapan'));
    }
}
