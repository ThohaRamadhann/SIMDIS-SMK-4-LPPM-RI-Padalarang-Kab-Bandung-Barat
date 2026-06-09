<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /** Halaman daftar semua notifikasi user — hanya yang terkirim */
    public function index()
    {
        $notifikasis = Notifikasi::forUser(Auth::id())
            ->with('pelanggaran.siswa')
            ->where('status', 'terkirim')        // ← hanya notif yang sudah terkirim
            ->orderBy('waktu_dikirim', 'desc')   // ← urutkan by waktu dikirim
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasis'));
    }

    /** Tandai satu notifikasi sebagai sudah dibaca */
    public function markAsRead(Notifikasi $notifikasi)
    {
        if ($notifikasi->id_pengguna !== Auth::id()) {
            abort(403);
        }

        // Hanya bisa dibaca kalau statusnya terkirim
        if ($notifikasi->status !== 'terkirim') {
            return response()->json(['success' => false, 'message' => 'Notifikasi belum terkirim.'], 422);
        }

        $notifikasi->markAsRead();

        return response()->json(['success' => true]);
    }

    /** Tandai semua notifikasi terkirim milik user sebagai sudah dibaca */
    public function markAllRead()
    {
        Notifikasi::forUser(Auth::id())
            ->where('status', 'terkirim')   // ← hanya yang terkirim
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

public function statusPenerima(Notifikasi $notifikasi)
{
    if ($notifikasi->id_pengguna !== Auth::id()) {
        abort(403);
    }

    if (! $notifikasi->id_pelanggaran) {
        return response()->json([]);
    }

    $statusPenerima = Notifikasi::where('id_pelanggaran', $notifikasi->id_pelanggaran)
        ->where('status', 'terkirim')
        ->with(['pengguna.role'])
        ->get()
        ->unique('id_pengguna')
        ->map(function ($n) {
            $namaPengguna = optional($n->pengguna)->name ?? '-';
            $roleName     = optional(optional($n->pengguna)->role)->nama_role ?? '-';
            $labelRole    = match ($roleName) {
                'wali_siswa' => 'Orang Tua',
                'wali_kelas' => 'Wali Kelas',
                'guru_bk'    => 'Guru BK',
                default      => ucfirst(str_replace('_', ' ', $roleName)),
            };
            return [
                'nama'    => $namaPengguna,
                'display' => $namaPengguna,
                'role'    => $labelRole,
                'is_read' => (bool) $n->is_read,
                'read_at' => optional($n->read_at)?->toDateTimeString(),
            ];
        })
        ->values()
        ->toArray();

    return response()->json($statusPenerima);
}
}