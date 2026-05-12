<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\JenisPelanggaran;
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

        // ── Base query builder sesuai role ──────────────────────────────
        $baseQuery = fn () => Pelanggaran::query();

        if ($role === 'wali_kelas') {
            $idWaliKelas = optional($user->waliKelas)->id_walikelas;
            $baseQuery   = fn () => Pelanggaran::where('id_walikelas', $idWaliKelas);
        } elseif ($role === 'orang_tua') {
            $idWaliMurid = optional($user->waliMurid)->id_walimurid;
            $baseQuery   = fn () => Pelanggaran::whereHas('siswa', fn ($q) =>
                $q->where('id_walimurid', $idWaliMurid)
            );
        }
        // guru_bk & admin: semua data

        // ── STATS CARDS ─────────────────────────────────────────────────
        $totalPelanggaran   = $baseQuery()->count();
        $sudahDitindak      = $baseQuery()->where('status_pembinaan', 'sudah ditindak')->count();
        $belumDitindak      = $baseQuery()->where('status_pembinaan', 'belum ditindak')->count();

        if ($role === 'orang_tua') {
            // Orang tua: hanya 3 card (tidak perlu jumlah siswa)
            $stats = [
                'total_pelanggaran' => $totalPelanggaran,
                'sudah_ditindak'    => $sudahDitindak,
                'belum_ditindak'    => $belumDitindak,
            ];
        } else {
            // guru_bk, wali_kelas: 4 card
            if ($role === 'wali_kelas') {
                $idWaliKelas    = optional($user->waliKelas)->id_walikelas;
                $jumlahSiswa    = Siswa::whereHas('pelanggaran', fn ($q) =>
                    $q->where('id_walikelas', $idWaliKelas)
                )->distinct()->count('id_siswa');
                // fallback: ambil dari kelas yg diampu
                if ($jumlahSiswa === 0) {
                    $idKelas     = optional($user->waliKelas)->id_kelas;
                    $jumlahSiswa = Siswa::where('id_kelas', $idKelas)->count();
                }
            } else {
                $jumlahSiswa = Siswa::count();
            }

            $stats = [
                'jumlah_siswa'      => $jumlahSiswa,
                'total_pelanggaran' => $totalPelanggaran,
                'sudah_ditindak'    => $sudahDitindak,
                'belum_ditindak'    => $belumDitindak,
            ];
        }

        // ── CHART 1: Pelanggaran per bulan (12 bulan terakhir) ──────────
        $bulanLabels = [];
        $bulanData   = [];

        for ($i = 11; $i >= 0; $i--) {
            $bulan         = Carbon::now()->subMonths($i);
            $bulanLabels[] = $bulan->translatedFormat('M Y'); // Jan 2025, dst
            $bulanData[]   = $baseQuery()
                ->whereYear('waktu_kejadian', $bulan->year)
                ->whereMonth('waktu_kejadian', $bulan->month)
                ->count();
        }

        $charts['bulanan'] = [
            'labels' => $bulanLabels,
            'data'   => $bulanData,
        ];

        // ── CHART 2: Jenis pelanggaran terbanyak ────────────────────────
        $jenisPelanggaran = $baseQuery()
            ->select('id_jenispelanggaran', DB::raw('count(*) as total'))
            ->groupBy('id_jenispelanggaran')
            ->orderByDesc('total')
            ->with('jenisPelanggaran')
            ->get();

        $charts['jenis'] = [
            'labels' => $jenisPelanggaran->map(fn ($p) =>
                optional($p->jenisPelanggaran)->nama_pelanggaran ?? 'Tidak diketahui'
            )->toArray(),
            'data' => $jenisPelanggaran->pluck('total')->toArray(),
        ];

        return view('dashboard', compact('stats', 'charts', 'role'));
    }
}