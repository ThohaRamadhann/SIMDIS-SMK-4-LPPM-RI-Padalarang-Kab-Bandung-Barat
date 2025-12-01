<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\Pelanggaran;
use Illuminate\Database\Eloquent\Model;

class WaliKelas extends Model
{
    protected $table = 'wali_kelas';
    protected $primaryKey = 'id_walikelas';

    protected $fillable = [
        'id_pengguna',
        'nuptk',
        'jabatan',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'id_walikelas');
    }
}
