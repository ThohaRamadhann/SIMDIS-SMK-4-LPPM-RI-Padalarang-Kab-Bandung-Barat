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

    // ── Index — dialihkan ke Livewire ──────────────────────────────────────
    public function index()
    {
        return view('pelanggaran.index');
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create()
    {
        $siswa            = Siswa::with('kelas')->orderBy('nama')->get();
        $waliKelas        = WaliKelas::with('pengguna')->get();
        $jenisPelanggaran = JenisPelanggaran::orderBy('nama_pelanggaran')->get();

        return view('pelanggaran.create', compact('siswa', 'waliKelas', 'jenisPelanggaran'));
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_siswa'            => 'required|exists:siswa,id_siswa',
            'id_walikelas'        => 'required|exists:wali_kelas,id_walikelas',
            'id_jenispelanggaran' => 'required|exists:jenis_pelanggaran,id_jenispelanggaran',
            'waktu_kejadian'      => 'required|date',
            'deskripsi'           => 'required|string',
        ]);

        // Anti-duplikat: siswa + jenis pelanggaran yang sama tidak boleh diinput di hari yang sama
        $waktu    = \Carbon\Carbon::parse($request->waktu_kejadian);
        $duplikat = Pelanggaran::where('id_siswa', $request->id_siswa)
            ->where('id_jenispelanggaran', $request->id_jenispelanggaran)
            ->whereDate('waktu_kejadian', $waktu->toDateString())
            ->exists();

        if ($duplikat) {
            return back()
                ->withInput()
                ->withErrors([
                    'id_jenispelanggaran' => 'Pelanggaran ini sudah pernah dicatat untuk siswa yang sama pada hari ini.',
                ]);
        }

        $pelanggaran = Pelanggaran::create([
            'id_siswa'            => $request->id_siswa,
            'id_walikelas'        => $request->id_walikelas,
            'id_jenispelanggaran' => $request->id_jenispelanggaran,
            'waktu_kejadian'      => $request->waktu_kejadian,
            'deskripsi'           => $request->deskripsi,
            'status_pembinaan'    => 'Belum Ditindak',
        ]);

        $pelanggaran->load([
            'siswa.waliMurid.pengguna',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        $this->ews->check($pelanggaran);

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil ditambahkan. Notifikasi akan dikirim dalam 10 menit jika tidak ada koreksi.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(Pelanggaran $pelanggaran)
    {
        $siswa            = Siswa::with('kelas')->orderBy('nama')->get();
        $waliKelas        = WaliKelas::with('pengguna')->get();
        $jenisPelanggaran = JenisPelanggaran::orderBy('nama_pelanggaran')->get();

        return view('pelanggaran.edit', compact(
            'pelanggaran',
            'siswa',
            'waliKelas',
            'jenisPelanggaran'
        ));
    }

    // ── Update ────────────────────────────────────────────────────────────
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
            'jam_pembinaan'       => 'nullable|date_format:H:i',  // ← TAMBAH
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
            'jam_pembinaan'       => $request->jam_pembinaan ?: null,  // ← TAMBAH
            'catatan_bk'          => $request->catatan_bk ?: null,
        ]);

        // EWS recheck:
        // 1. Batalkan notif pending lama
        // 2. Evaluasi ulang dengan data baru → dispatch job baru jika perlu
        $this->ews->recheck($pelanggaran);

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil diperbarui. Notifikasi pending telah dievaluasi ulang.');
    }

    // ── Destroy (soft delete) ─────────────────────────────────────────────
    public function destroy(Pelanggaran $pelanggaran)
    {
        // Batalkan notif pending sebelum hapus
        // (job yang sudah di-queue akan lihat status 'dibatalkan' dan skip)
        $this->ews->cancel($pelanggaran);

        $pelanggaran->delete();

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dihapus. Notifikasi pending telah dibatalkan.');
    }
}
