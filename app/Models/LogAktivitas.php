<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    public $timestamps = false;

    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_pengguna',
        'aksi',
        'modul',
        'keterangan',
        'id_referensi',
        'ip_address',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    // Helper static untuk catat log dengan mudah
    public static function catat(string $aksi, string $modul, string $keterangan, ?int $idReferensi = null): void
    {
        static::create([
            'id_pengguna' => auth()->user()->id_pengguna,
            'aksi'        => $aksi,
            'modul'       => $modul,
            'keterangan'  => $keterangan,
            'id_referensi' => $idReferensi,
            'ip_address'  => request()->ip(),
            'waktu'       => now(),
        ]);
    }
}