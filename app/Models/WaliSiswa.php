<?php

namespace App\Models;

use App\Models\Siswa;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaliSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'wali_siswa';
    protected $primaryKey = 'id_walisiswa';

    protected $fillable = [
        'id_pengguna',
        'hubungan',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_walisiswa');
    }
}