<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\JenisPelanggaran;
use App\Services\EarlyWarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelanggaranController extends Controller
{
    protected EarlyWarningService $ews;

    public function __construct(EarlyWarningService $ews)
    {
        $this->ews = $ews;
    }

    public function index()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        $query = Pelanggaran::with(['siswa', 'waliKelas.pengguna', 'jenisPelanggaran'])
            ->orderBy('created_at', 'desc');

        if ($role === 'wali_kelas') {
            $query->where('id_walikelas', optional($user->waliKelas)->id_walikelas);
        } elseif ($role === 'orang_tua') {
            $query->whereHas('siswa', function ($q) use ($user) {
                $q->where('id_walimurid', optional($user->waliMurid)->id_walimurid);
            });
        }
        // admin & guru_bk: tampil semua

        $pelanggarans = $query->paginate(10);

        return view('pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        $siswa            = Siswa::all();
        $waliKelas        = WaliKelas::all();
        $jenisPelanggaran = JenisPelanggaran::all();

        return view('pelanggaran.create', compact('siswa', 'waliKelas', 'jenisPelanggaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_siswa'            => 'required|exists:siswa,id_siswa',
            'id_walikelas'        => 'required|exists:wali_kelas,id_walikelas',
            'id_jenispelanggaran' => 'required|exists:jenis_pelanggaran,id_jenispelanggaran',
            'waktu_kejadian'      => 'required|date',
            'deskripsi'           => 'required|string',
        ]);

        // Simpan pelanggaran
        $pelanggaran = Pelanggaran::create($request->all());

        // Load semua relasi yang dibutuhkan EWS
        $pelanggaran->load(['siswa.waliMurid.pengguna', 'waliKelas.pengguna', 'jenisPelanggaran']);

        // Jalankan Early Warning System
        $this->ews->check($pelanggaran);

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil ditambahkan.');
    }

    public function edit(Pelanggaran $pelanggaran)
    {
        $siswa            = Siswa::all();
        $waliKelas        = WaliKelas::all();
        $jenisPelanggaran = JenisPelanggaran::all();

        return view('pelanggaran.edit', compact('pelanggaran', 'siswa', 'waliKelas', 'jenisPelanggaran'));
    }

    public function update(Request $request, Pelanggaran $pelanggaran)
    {
        $request->validate([
            'id_siswa'            => 'required|exists:siswa,id_siswa',
            'id_walikelas'        => 'required|exists:wali_kelas,id_walikelas',
            'id_jenispelanggaran' => 'required|exists:jenis_pelanggaran,id_jenispelanggaran',
            'waktu_kejadian'      => 'required|date',
            'deskripsi'           => 'required|string',
        ]);

        $pelanggaran->update($request->all());

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil diupdate.');
    }

    public function destroy(Pelanggaran $pelanggaran)
    {
        // Notifikasi terkait terhapus otomatis via cascadeOnDelete di migration notifikasi
        $pelanggaran->delete();

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dihapus.');
    }
}