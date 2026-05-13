<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggaran extends Model
{
    use SoftDeletes;
    protected $table      = 'pelanggaran';
    protected $primaryKey = 'id_pelanggaran';

    protected $fillable = [
        'id_siswa',
        'id_walikelas',
        'id_jenispelanggaran',
        'waktu_kejadian',
        'deskripsi',
        'status_pembinaan',
    ];

    protected $casts = [
        'waktu_kejadian' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function waliKelas()
    {
        return $this->belongsTo(WaliKelas::class, 'id_walikelas');
    }

    public function jenisPelanggaran()
    {
        return $this->belongsTo(JenisPelanggaran::class, 'id_jenispelanggaran');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_pelanggaran');
    }
}