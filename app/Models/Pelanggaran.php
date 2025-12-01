<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $table = 'pelanggaran';
    protected $primaryKey = 'id_pelanggaran';

    protected $fillable = [
        'id_siswa',
        'id_walikelas',
        'id_jenispelanggaran',
        'waktu_kejadian',
        'deskripsi',
        'status_pembinaan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function walikelas()
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
