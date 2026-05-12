<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /** Halaman daftar semua notifikasi user */
    public function index()
    {
        $notifikasis = Notifikasi::forUser(Auth::id())
            ->with('pelanggaran.siswa')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasis'));
    }

    /** Tandai satu notifikasi sebagai sudah dibaca */
    public function markAsRead(Notifikasi $notifikasi)
    {
        // Pastikan notifikasi milik user yang login
        if ($notifikasi->id_pengguna !== Auth::id()) {
            abort(403);
        }

        $notifikasi->markAsRead();

        return response()->json(['success' => true]);
    }

    /** Tandai semua notifikasi user sebagai sudah dibaca */
    public function markAllRead()
    {
        Notifikasi::forUser(Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}