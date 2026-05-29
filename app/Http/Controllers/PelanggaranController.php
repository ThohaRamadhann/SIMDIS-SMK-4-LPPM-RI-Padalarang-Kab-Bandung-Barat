<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
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

    // ── Index — dialihkan ke Livewire ─────────────────────────────────────
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

        // Anti-duplikat
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

        LogAktivitas::catat(
            aksi: 'tambah_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Mencatat pelanggaran siswa ' . $pelanggaran->siswa->nama
                . ' - ' . $pelanggaran->jenisPelanggaran->nama_pelanggaran
                . ' (Tingkat: ' . $pelanggaran->jenisPelanggaran->tingkat_pelanggaran . ')',
            idReferensi: $pelanggaran->id_pelanggaran
        );

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
            'id_siswa'              => 'required|exists:siswa,id_siswa',
            'id_walikelas'          => 'required|exists:wali_kelas,id_walikelas',
            'id_jenispelanggaran'   => 'required|exists:jenis_pelanggaran,id_jenispelanggaran',
            'waktu_kejadian_date'   => 'required|date',
            'waktu_kejadian_hour'   => 'required|string',
            'waktu_kejadian_minute' => 'required|string',
            'deskripsi'             => 'required|string',
            'status_pembinaan'      => 'required|in:Belum Ditindak,Dalam Proses,Selesai',
            'tanggal_pembinaan'     => 'nullable|date',
            'jam_pembinaan_hour'    => 'nullable|string',
            'jam_pembinaan_minute'  => 'nullable|string',
            'catatan_bk'            => 'nullable|string|max:2000',
        ]);

        $waktuKejadian = $request->waktu_kejadian_date
            . ' ' . $request->waktu_kejadian_hour
            . ':' . $request->waktu_kejadian_minute
            . ':00';

        $jamPembinaan = ($request->jam_pembinaan_hour && $request->jam_pembinaan_minute)
            ? $request->jam_pembinaan_hour . ':' . $request->jam_pembinaan_minute . ':00'
            : null;

        $perluRecheck =
            $pelanggaran->id_siswa != $request->id_siswa ||
            $pelanggaran->id_walikelas != $request->id_walikelas ||
            $pelanggaran->id_jenispelanggaran != $request->id_jenispelanggaran ||
            $pelanggaran->waktu_kejadian != $waktuKejadian ||
            $pelanggaran->deskripsi != $request->deskripsi;

        $pelanggaran->update([
            'id_siswa'            => $request->id_siswa,
            'id_walikelas'        => $request->id_walikelas,
            'id_jenispelanggaran' => $request->id_jenispelanggaran,
            'waktu_kejadian'      => $waktuKejadian,
            'deskripsi'           => $request->deskripsi,
            'status_pembinaan'    => $request->status_pembinaan,
            'tanggal_pembinaan'   => $request->tanggal_pembinaan ?: null,
            'jam_pembinaan'       => $jamPembinaan,
            'catatan_bk'          => $request->catatan_bk ?: null,
        ]);

        $pelanggaran->load(['siswa', 'jenisPelanggaran']);

        LogAktivitas::catat(
            aksi: 'edit_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Mengubah data pelanggaran siswa ' . $pelanggaran->siswa->nama
                . ' - ' . $pelanggaran->jenisPelanggaran->nama_pelanggaran
                . ' | Status: ' . $request->status_pembinaan,
            idReferensi: $pelanggaran->id_pelanggaran
        );

        if ($perluRecheck) {
            $this->ews->recheck($pelanggaran);

            return redirect()->route('pelanggaran.index')
                ->with('success', 'Pelanggaran berhasil diperbarui. Notifikasi pending telah dievaluasi ulang.');
        }

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Data pembinaan berhasil diperbarui.');
    }

    // ── Destroy (soft delete) ─────────────────────────────────────────────
    public function destroy(Pelanggaran $pelanggaran)
    {
        $pelanggaran->load(['siswa', 'jenisPelanggaran']);

        LogAktivitas::catat(
            aksi: 'hapus_pelanggaran',
            modul: 'pelanggaran',
            keterangan: 'Menghapus pelanggaran siswa ' . $pelanggaran->siswa->nama
                . ' - ' . $pelanggaran->jenisPelanggaran->nama_pelanggaran,
            idReferensi: $pelanggaran->id_pelanggaran
        );

        $this->ews->cancel($pelanggaran);
        $pelanggaran->delete();

        return redirect()->route('pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dihapus. Notifikasi pending telah dibatalkan.');
    }
}