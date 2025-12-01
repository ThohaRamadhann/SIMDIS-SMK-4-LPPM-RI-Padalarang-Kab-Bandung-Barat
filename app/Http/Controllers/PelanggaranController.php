<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\JenisPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelanggaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = optional($user->role)->nama_role;

        if ($role === 'admin' || $role === 'guru_bk') {
            $pelanggarans = Pelanggaran::all();
        } elseif ($role === 'wali_kelas') {
            $pelanggarans = Pelanggaran::where('id_walikelas', optional($user->waliKelas)->id_walikelas)->get();
        } elseif ($role === 'orang_tua') {
            $pelanggarans = Pelanggaran::whereHas('siswa', function($q) use ($user) {
                $q->where('id_walimurid', optional($user->waliMurid)->id_walimurid);
            })->get();
        } else {
            $pelanggarans = collect();
        }

        return view('pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        $siswa = Siswa::all();
        $waliKelas = WaliKelas::all();
        $jenisPelanggaran = JenisPelanggaran::all();

        return view('pelanggaran.create', compact('siswa', 'waliKelas', 'jenisPelanggaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'id_walikelas' => 'required|exists:wali_kelas,id_walikelas',
            'id_jenispelanggaran' => 'required|exists:jenis_pelanggaran,id_jenispelanggaran',
            'waktu_kejadian' => 'required|date',
            'deskripsi' => 'required|string',
        ]);

        Pelanggaran::create($request->all());

        return redirect()->route('pelanggaran.index')->with('success', 'Pelanggaran berhasil ditambahkan.');
    }

    public function edit(Pelanggaran $pelanggaran)
    {
        $siswa = Siswa::all();
        $waliKelas = WaliKelas::all();
        $jenisPelanggaran = JenisPelanggaran::all();

        return view('pelanggaran.edit', compact('pelanggaran', 'siswa', 'waliKelas', 'jenisPelanggaran'));
    }

    public function update(Request $request, Pelanggaran $pelanggaran)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'id_walikelas' => 'required|exists:wali_kelas,id_walikelas',
            'id_jenispelanggaran' => 'required|exists:jenis_pelanggaran,id_jenispelanggaran',
            'waktu_kejadian' => 'required|date',
            'deskripsi' => 'required|string',
        ]);

        $pelanggaran->update($request->all());

        return redirect()->route('pelanggaran.index')->with('success', 'Pelanggaran berhasil diupdate.');
    }

    public function destroy(Pelanggaran $pelanggaran)
    {
        $pelanggaran->delete();

        return redirect()->route('pelanggaran.index')->with('success', 'Pelanggaran berhasil dihapus.');
    }
}
