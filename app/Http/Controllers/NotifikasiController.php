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
}