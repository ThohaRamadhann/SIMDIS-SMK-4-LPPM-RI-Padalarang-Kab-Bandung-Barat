<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran';
    protected $primaryKey = 'id_jenispelanggaran';

    protected $fillable = [
        'nama_pelanggaran',
        'tingkat_pelanggaran',
    ];

    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'id_jenispelanggaran');
    }
}
