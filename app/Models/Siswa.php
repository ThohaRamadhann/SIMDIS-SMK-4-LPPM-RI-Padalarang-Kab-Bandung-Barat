<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'id_walisiswa',
        'id_kelas',
        'nama',
        'nis',
        'status',
    ];

    public function waliSiswa()
    {
        return $this->belongsTo(WaliSiswa::class, 'id_walisiswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'id_siswa');
    }
}