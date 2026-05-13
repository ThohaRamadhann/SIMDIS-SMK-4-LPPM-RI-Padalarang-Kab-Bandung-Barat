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

        $pelanggarans = $query->paginate(10);

        return view('pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        $siswa            = Siswa::with('kelas.waliKelas.pengguna')->orderBy('nama')->get();
        $waliKelas        = WaliKelas::with('pengguna')->get();
        $jenisPelanggaran = JenisPelanggaran::orderBy('nama_pelanggaran')->get();

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

        $pelanggaran = Pelanggaran::create([
            'id_siswa'            => $request->id_siswa,
            'id_walikelas'        => $request->id_walikelas,
            'id_jenispelanggaran' => $request->id_jenispelanggaran,
            'waktu_kejadian'      => $request->waktu_kejadian,
            'deskripsi'           => $request->deskripsi,
            'status_pembinaan'    => 'Belum Ditindak',
        ]);

        $pelanggaran->load(['siswa.waliMurid.pengguna', 'waliKelas.pengguna', 'jenisPelanggaran']);

        $this->ews->check($pelanggaran);

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil ditambahkan.');
    }

    public function edit(Pelanggaran $pelanggaran)
    {
        $siswa            = Siswa::with('kelas.waliKelas.pengguna')->orderBy('nama')->get();
        $waliKelas        = WaliKelas::with('pengguna')->get();
        $jenisPelanggaran = JenisPelanggaran::orderBy('nama_pelanggaran')->get();

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
            'status_pembinaan'    => 'required|in:Belum Ditindak,Dalam Proses,Selesai',
            'tanggal_pembinaan'   => 'nullable|date',
            'catatan_bk'          => 'nullable|string|max:2000',
        ]);

        $pelanggaran->update([
            'id_siswa'            => $request->id_siswa,
            'id_walikelas'        => $request->id_walikelas,
            'id_jenispelanggaran' => $request->id_jenispelanggaran,
            'waktu_kejadian'      => $request->waktu_kejadian,
            'deskripsi'           => $request->deskripsi,
            'status_pembinaan'    => $request->status_pembinaan,
            'tanggal_pembinaan'   => $request->tanggal_pembinaan ?: null,
            'catatan_bk'          => $request->catatan_bk ?: null,
        ]);

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil diupdate.');
    }

    public function destroy(Pelanggaran $pelanggaran)
    {
        $pelanggaran->delete();

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dihapus.');
    }
}