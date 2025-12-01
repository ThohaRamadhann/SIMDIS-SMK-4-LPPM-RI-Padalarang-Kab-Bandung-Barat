<?php

namespace App\Models;

use App\Models\Siswa;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Model;

class WaliMurid extends Model
{
    protected $table = 'wali_murid';
    protected $primaryKey = 'id_walimurid';

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
        return $this->hasMany(Siswa::class, 'id_walimurid');
    }
}
