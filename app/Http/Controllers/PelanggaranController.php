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

        $pelanggaran = Pelanggaran::create([
            'id_siswa'            => $request->id_siswa,
            'id_walikelas'        => $request->id_walikelas,
            'id_jenispelanggaran' => $request->id_jenispelanggaran,
            'waktu_kejadian'      => $request->waktu_kejadian,
            'deskripsi'           => $request->deskripsi,
            'status_pembinaan'    => 'Belum Ditindak',
        ]);

        // Load relasi untuk EWS
        $pelanggaran->load([
            'siswa.waliMurid.pengguna',
            'waliKelas.pengguna',
            'jenisPelanggaran',
        ]);

        // EWS: simpan notif pending + dispatch job dengan delay 10 menit
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
            'pelanggaran', 'siswa', 'waliKelas', 'jenisPelanggaran'
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