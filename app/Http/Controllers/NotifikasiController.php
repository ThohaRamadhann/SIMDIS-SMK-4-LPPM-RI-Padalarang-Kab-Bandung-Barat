<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Events\NotifikasiBaru;

class NotifikasiController extends Controller
{
    public function create(Request $request)
    {
        $notifikasi = Notifikasi::create([
            'id_pengguna' => $request->id_pengguna,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'status' => 'baru',
        ]);

        // Broadcast event
        event(new NotifikasiBaru($notifikasi));

        return response()->json(['success' => true]);
    }
}
