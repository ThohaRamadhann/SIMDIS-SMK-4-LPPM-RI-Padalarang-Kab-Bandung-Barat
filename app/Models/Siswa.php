<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'id_walimurid',
        'id_kelas',
        'nama',
        'nis',
        'status',
    ];

    public function waliMurid()
    {
        return $this->belongsTo(WaliMurid::class, 'id_walimurid');
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
