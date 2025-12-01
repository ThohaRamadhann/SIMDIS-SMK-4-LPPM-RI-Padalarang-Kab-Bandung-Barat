<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    protected $fillable = ['id_walikelas', 'tingkat', 'nama_kelas', 'jurusan', 'tahun_ajaran'];

    public function waliKelas()
    {
        return $this->belongsTo(WaliKelas::class, 'id_walikelas');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }
}
